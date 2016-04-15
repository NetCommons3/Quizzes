<?php
/**
 * QuizResult Model
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */

App::uses('QuizzesAppModel', 'Quizzes.Model');

/**
 * Summary for ActionQuizAdd Model
 */
class QuizResult extends QuizzesAppModel {

/**
 * Use table config
 *
 * @var bool
 */
	public $useTable = 'quiz_answer_summaries';

/**
 * Use table alias
 *
 * @var bool
 */
	public $alias = 'QuizAnswerSummary';

/**
 * 総合統計用サマリID配列
 *
 * @var array
 */
	protected $_userSummaryIds = null;

/**
 * 一覧用サマリID配列
 *
 * @var array
 */
	protected $_latestSummaryIds = null;

/**
 * 統計用小テスト情報
 *
 * @var array
 */
	protected $_quiz = null;

/**
 * ページネーション用ソート上家
 *
 * @var array
 */
	protected $_orderForPaginate = null;

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array();

/**
 * initResult
 * 統計情報取得前の初期処理
 *
 * @param array $quiz 小テストデータ
 * @return void
 */
	public function initResult($quiz) {
		$this->_quiz = $quiz;

		$this->loadModels([
			'QuizAnswerSummary' => 'Quizzes.QuizAnswerSummary',
		]);

		$userSummaryIds = $this->QuizAnswerSummary->find('all', array(
			'fields' => array('MAX(id) AS summary_id'),
			'conditions' => array(
				'answer_status' => QuizzesComponent::ACTION_ACT,
				//FUJI 'test_status' => QuizzesComponent::TEST_ANSWER_STATUS_PEFORM,
				'quiz_key' => $quiz['Quiz']['key'],
				'is_grade_finished' => true,
				'NOT' => array(
					'user_id' => null,
				)
			),
			'group' => array('user_id'),
			'recursive' => -1,
		));
		$this->_userSummaryIds = Hash::extract($userSummaryIds, '{n}.0.summary_id');

		// 受験者一覧取得
		$latestSummaryIds = $this->QuizAnswerSummary->find('all', array(
			'fields' => array('MAX(id) AS summary_id'),
			'conditions' => array(
				'quiz_key' => $quiz['Quiz']['key'],
				'NOT' => array(
					'user_id' => null,
				)
			),
			'group' => array('user_id'),
			'recursive' => -1,
		));
		if ($latestSummaryIds) {
			$this->_latestSummaryIds = Hash::extract($latestSummaryIds, '{n}.0.summary_id');
		}
	}
/**
 * getAllResult
 * 総合情報を返す
 *
 * @return array
 */
	public function getAllResult() {
		$ret = array();
		$ret['general'] = $this->getGeneralInformation($this->_quiz['Quiz']['key'], $this->_userSummaryIds);
		$ret['score_distribution'] = $this->getScoreDistribution($this->_quiz, $this->_userSummaryIds);
		return $ret;
	}
/**
 * getGeneralInformation
 * 総合情報を返す
 *
 * @return array
 */
	public function getGeneralInformation() {
		$general = $this->QuizAnswerSummary->find('all', array(
			'fields' => array(
				'COUNT(*) AS number_pepole',
				'MAX(summary_score) AS max_score',
				'MIN(summary_score) AS min_score',
				'AVG(summary_score) AS avg_score',
				'VAR_POP(summary_score) AS samp_score',
				'AVG(elapsed_second) AS avg_time',
			),
			'conditions' => $this->getCondition(),
			'group' => array('quiz_key'),
			'recursive' => -1,
		));
		$general = $general[0][0];
		$general['avg_score'] = round(floatval($general['avg_score']), 1);
		$general['samp_score'] = round(floatval($general['samp_score']), 1);
		return $general;
	}
/**
 * getScoreDistribution
 * 得点分布情報を返す
 *
 * @param array $quiz 小テスト
 * @param array $userSummaryIds 会員のサマリID
 * @return array
 */
	public function getScoreDistribution($quiz, $userSummaryIds) {
		$dispersion = array();
		// 合計点計算
		$allotments = array_sum(Hash::extract($quiz, 'QuizPage.{n}.QuizQuestion.{n}.allotment'));
		// 10等分
		$baseScore = $allotments / 10;
		// 検索条件
		$baseCondition = $this->getCondition();
		for ($i = 0; $i < 10; $i++) {
			$rangeLow = $baseScore * ($i);
			$rangeHigh = $baseScore * ($i + 1);
			$dispersion[$i]['label'] = sprintf('%d - %d', round($rangeLow), round($rangeHigh));
			// それぞれの範囲の人数を取得
			$condition = Hash::merge($baseCondition, array('summary_score BETWEEN ? AND ?' => array($rangeLow, $rangeHigh)));
			$number = $this->QuizAnswerSummary->find('all', array(
				'fields' => array(
					'COUNT(*) AS number',
				),
				'conditions' => $condition,
				'group' => array('quiz_key'),
				'recursive' => -1,
			));
			$dispersion[$i]['number'] = 0;
			if ($number) {
				$dispersion[$i]['value'] = $number[0][0]['number'];
			}
		}
		return $dispersion;
	}
/**
 * getCondition
 * 成績情報検索のための基本条件
 *
 * @return array
 */
	public function getCondition() {
		return array(
			'quiz_key' => $this->_quiz['Quiz']['key'],
			'is_grade_finished' => true,
			'OR' => array(
				'QuizAnswerSummary.id' => $this->_userSummaryIds,
				'QuizAnswerSummary.user_id' => null,
			)
		);
	}
/**
 * getSummaryListCondition
 * 成績一覧情報検索のための基本条件
 *
 * @return array
 */
	public function getSummaryListCondition() {
		return array(
			'quiz_key' => $this->_quiz['Quiz']['key'],
			'OR' => array(
				'QuizAnswerSummary.id' => $this->_userSummaryIds,
			)
		);
	}
/**
 * setPaginateOrder
 * ソート条件設定
 *
 * @param array $order ソート条件
 * @return void
 */
	public function setPaginateOrder($order) {
		$this->_orderForPaginate = $order;
	}
/**
 * オーバーライドされた paginate メソッド
 *
 * @param array $conditions 条件配列
 * @param array $fields 検索フィールド
 * @param array $order ソート指定
 * @param int $limit 取得数
 * @param int $page ページ数
 * @param int $recursive 再帰ネスト数
 * @param array $extra 追加情報
 * @return array レコード
 */
	public function paginate($conditions, $fields, $order, $limit, $page = 1, $recursive = null, $extra = array()) {
		$recursive = 0;
		$fields = array('QuizAnswerSummary.*', 'User.*', 'Statistics.*');
		$joins = $this->_getJoins();
		$conditions = Hash::merge(array(
			'QuizAnswerSummary.quiz_key' => $this->_quiz['Quiz']['key'],
			'OR' => array(
				'QuizAnswerSummary.id' => $this->_latestSummaryIds,
				'QuizAnswerSummary.user_id' => null,
			),
		), $conditions);
		$order = $this->_orderForPaginate;
		return $this->find(
			'all',
			compact('conditions', 'fields', 'joins', 'order', 'limit', 'page', 'recursive')
		);
	}
/**
 * オーバーライドされた paginateCount メソッド
 *
 * @param array $conditions 条件配列
 * @param int $recursive 再帰ネスト数
 * @param array $extra 追加情報
 * @return int レコード数
 */
	public function paginateCount($conditions = null, $recursive = 0, $extra = array()) {
		$this->recursive = $recursive;
		$conditions = array(
			'QuizAnswerSummary.quiz_key' => $this->_quiz['Quiz']['key'],
			'OR' => array(
				'QuizAnswerSummary.id' => $this->_latestSummaryIds,
				'QuizAnswerSummary.user_id' => null,
			),
		);
		$results = $this->find('count', array(
			'fields' => array('QuizAnswerSummary.*', 'User.*', 'Statistics.*'),
			'joins' => $this->_getJoins(),
			'conditions' => $conditions,
			'recursive' => $recursive,
		));
		return count($results);
	}
/**
 * サブクエリJoinテーブル配列
 *
 * @return array Join条件
 */
	protected function _getJoins() {
		$subQuery = $this->_getSubQuery($this->_quiz['Quiz']['key']);
		$joins = array(
			array(
				'table' => "({$subQuery})",
				'alias' => 'Statistics',
				'type' => 'LEFT',
				'conditions' => array(
					'QuizAnswerSummary.id = Statistics.id',
				),
			),
		);
		return $joins;
	}
/**
 * サブクエリ
 *
 * @param array $quizKey 小テストキー
 * @return string サブクエリ文字列
 */
	protected function _getSubQuery($quizKey) {
		$db = $this->QuizAnswerSummary->getDataSource();
		$subQuery = $db->buildStatement(array(
			'fields' => array(
				'MAX(id) AS id',
				'MAX(passing_status) as passing_status',
				'MAX(within_time_status) as within_time_status',
				'AVG(elapsed_second) as avg_elapsed_second',
				'MAX(summary_score) as max_score',
				'MIN(summary_score) as min_score',
				'MIN(passing_status) as not_scoring',
			),
			'table' => $db->fullTableName($this->QuizAnswerSummary),
			'alias' => 'Statistics',
			'group' => array('user_id'),
			'conditions' => array(
				'Statistics.quiz_key' => $quizKey,
				'NOT' => array(
					'Statistics.user_id' => null,
				),
			),
		),
			$this->QuizAnswerSummary
		);
		return $subQuery;
	}
}