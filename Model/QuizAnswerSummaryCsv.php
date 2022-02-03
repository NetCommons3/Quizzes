<?php
/**
 * QuizAnswerSummary Model
 *
 * @property Quiz $Quiz
 * @property User $User
 * @property QuizAnswerSummary $QuizAnswerSummary
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */

App::uses('QuizzesAppModel', 'Quizzes.Model');
App::uses('QuizAnswerSummary', 'Quizzes.Model');
App::uses('NetCommonsTime', 'NetCommons.Utility');

/**
 * Summary for QuizAnswerSummary Model
 */
class QuizAnswerSummaryCsv extends QuizAnswerSummary {

/**
 * use table
 *
 * @var array
 */
	public $useTable = 'quiz_answer_summaries';

/**
 * use behaviors
 *
 * @var array
 */
	public $actsAs = array(
		'NetCommons.Trackable',
	);

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
	);

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
	);

/**
 * 日時を変換するクラス
 *
 * @var NetCommonsTime
 */
	private $__NetCommonsTime;

/**
 * Constructor. Binds the model's database table to the object.
 *
 * @param bool|int|string|array $id Set this ID for this model on startup,
 * can also be an array of options, see above.
 * @param string $table Name of database table to use.
 * @param string $ds DataSource connection name.
 * @see Model::__construct()
 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
 */
	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);

		$this->loadModels([
			'Quiz' => 'Quizzes.Quiz',
			'QuizAnswer' => 'Quizzes.QuizAnswer',
		]);
		$this->__NetCommonsTime = new NetCommonsTime();
	}

/**
 * getQuizForAnswerCsv
 *
 * @param int $quizKey quiz key
 * @return array quiz data
 */
	public function getQuizForAnswerCsv($quizKey) {
		// 指定の小テストデータを取得
		// CSVの取得は公開してちゃんとした回答を得ている小テストに限定である

		// CSVをダウンロードする対象の小テストは「発行状態」にあるものに限定をしている
		// ブロック一覧に表示するCSVダウンロードボタン自体がその意味で表示させているので
		// ここでも同じロジックを用いるものとする
		//$conditions = $this->Quiz->getBaseCondition(array('Quiz.key' => $quizKey));
		$conditions = array(
			'Quiz.block_id' => Current::read('Block.id'),
			'Quiz.key' => $quizKey,
			'Quiz.is_active' => true,
			'Quiz.language_id' => Current::read('Language.id'),
		);

		$quiz = $this->Quiz->find('first', array(
			'conditions' => $conditions,
			'recursive' => -1
		));
		return $quiz;
	}

/**
 * getAnswerSummaryCsv
 *
 * @param array $quiz quiz data
 * @param int $limit record limit
 * @param int $offset offset
 * @param int &$dataCount データ数
 * @return array
 */
	public function getAnswerSummaryCsv($quiz, $limit, $offset, &$dataCount) {
		// 指定された小テストの回答データをＣｓｖに出力しやすい行形式で返す
		$retArray = array();
		$headerLineCount = 0;
		$dataCount = 0;

		// $offset == 0 のときのみヘッダ行を出す
		if ($offset == 0) {
			$retArray = $this->_putHeader($quiz);
			$headerLineCount = count($retArray);
		}
		// $quizにはページデータ、質問データが入っていることを前提とする

		// 小テストのkeyを取得
		$quizKey = $quiz['Quiz']['key'];

		// keyに一致するsummaryを取得（テストじゃない、完了している）
		$summaries = $this->_getSummary($quizKey, $limit, $offset);
		if (empty($summaries)) {
			return $retArray;
		}
		// 偏差値計算の元になる分散値を取り出す
		$sampScore = $this->_getSampScore($quizKey);

		// 質問のIDを取得
		$questionIds = Hash::extract($quiz['QuizPage'], '{n}.QuizQuestion.{n}.id');

		// summary loop
		foreach ($summaries as $summary) {
			//$answers = $summary['QuizAnswer'];
			// 何回もSQLを発行するのは無駄かなと思いつつも
			// QuizAnswerに回答データの取り扱いしやすい形への整備機能を組み込んであるので、それを利用したかった
			// このクラスからでも利用できないかと試みたが
			// AnswerとQuestionがJOINされた形でFindしないと整備機能が発動しない
			// そうするためにはrecursive=2でないといけないわけだが、recursive=2にするとRoleのFindでSQLエラーになる
			// 仕方ないのでこの形式で処理を行う
			$answers = $this->QuizAnswer->find('all', array(
				'fields' => array('QuizAnswer.*', 'QuizQuestion.*'),
				'conditions' => array(
					'quiz_answer_summary_id' => $summary[$this->alias]['id'],
					'QuizQuestion.id' => $questionIds
				),
				'recursive' => -1,
				'joins' => array(
					array(
						'table' => 'quiz_questions',
						'alias' => 'QuizQuestion',
						'type' => 'LEFT',
						'conditions' => array(
							'QuizAnswer.quiz_question_key = QuizQuestion.key',
						)
					)
				)
			));
			$retArray[] = $this->_getRows($quiz, $sampScore, $summary, $answers);
		}

		$dataCount = count($retArray) - $headerLineCount;
		return $retArray;
	}
/**
 * _getSummary
 *
 * @param string $quizKey quiz key
 * @param int $limit record limit
 * @param int $offset offset
 * @return array
 */
	protected function _getSummary($quizKey, $limit, $offset) {
		$summaries = $this->find('all', array(
			'fields' => array('QuizAnswerSummaryCsv.*', 'User.handlename'),
			'conditions' => array(
				'answer_status' => QuizzesComponent::ACTION_ACT,
				'test_status' => QuizzesComponent::TEST_ANSWER_STATUS_PEFORM,
				'quiz_key' => $quizKey,
			),
			'recursive' => -1,
			'joins' => array(
				array(
					'table' => 'users',
					'alias' => 'User',
					'type' => 'LEFT',
					'conditions' => array(
						'QuizAnswerSummaryCsv.user_id = User.id',
					)
				),
			),
			'limit' => $limit,
			'offset' => $offset,
			'order' => array('QuizAnswerSummaryCsv.created ASC'),
		));
		return $summaries;
	}

/**
 * _getSampScore
 *
 * @param string $quizKey quiz key
 * @return float
 */
	protected function _getSampScore($quizKey) {
		$general = $this->find('all', array(
			'fields' => array(
				'AVG(summary_score) AS avg_score',
				'VAR_POP(summary_score) AS samp_score',
			),
			'conditions' => array(
				'answer_status' => QuizzesComponent::ACTION_ACT,
				'test_status' => QuizzesComponent::TEST_ANSWER_STATUS_PEFORM,
				'quiz_key' => $quizKey,
			),
			'group' => array('quiz_key'),
			'recursive' => -1,
		));
		$general = $general[0][0];
		$general['avg_score'] = round(floatval($general['avg_score']), 1);
		$general['samp_score'] = round(sqrt(floatval($general['samp_score'])), 1);
		return $general;
	}
/**
 * _putHeader
 *
 * 小テストの問題文、配点をコメント行として全て出力する
 * カラムのヘッダ行を出力する
 * 解答者名,
 * 解答日,
 * 時間（秒）,
 * 受験回数,
 * 偏差値,
 * 総合得点,
 * 問題番号（解答のため）,得点,問題番号（解答のため）,得点,...問題数分繰り返し
 *
 * @param array $quiz quiz data
 * @return array
 */
	protected function _putHeader($quiz) {
		// ヘッダ行
		$cols = array();
		// 問題文のコメント行用
		$questions = array();
		//
		$cols[] = __d('quizzes', 'Answer\'s');
		$cols[] = __d('quizzes', 'Date');
		$cols[] = __d('quizzes', 'Elapsed');
		$cols[] = __d('quizzes', 'Number');
		$cols[] = __d('quizzes', 'Score');
		$cols[] = __d('quizzes', 'Deviation');

		$questionNumber = 0;
		foreach ($quiz['QuizPage'] as $page) {
			if ($page['is_page_description'] == QuizzesComponent::USES_USE) {
				$questions[] = array('#####' . $page['page_description']);
			}
			foreach ($page['QuizQuestion'] as $question) {
				$questionNumber = $questionNumber + 1;
				$qNumberStr = __d('quizzes', 'Question %2d :', $questionNumber);
				$cols[] = $qNumberStr;
				$cols[] = __d('quizzes', 'Score');

				$questions[] = array('#####' . $qNumberStr . $question['question_value']);
			}
		}
		$colCount = count($cols);
		foreach ($questions as &$q) {
			$q = array_pad($q, $colCount, '');
		}
		return array_merge($questions, array($cols));
	}

/**
 * _getRow
 *
 * @param array $quiz quiz data
 * @param array $sampScore 分散値
 * @param array $summary answer summary
 * @param array $answers answer data
 * @return array
 */
	protected function _getRows($quiz, $sampScore, $summary, $answers) {
		// ページ、質問のループから、取り出すべき質問のIDを順番に取り出す
		// question loop
		// 返却用配列にquestionのIDにマッチするAnswerを配列要素として追加、Answerがないときは空文字

		$cols = array();
		$cols[] = $this->_getUserName($quiz, $summary);
		$cols[] = $this->__NetCommonsTime->dateFormat(
			$summary['QuizAnswerSummaryCsv']['answer_finish_time'],
			'Y-m-d H:i:s'
		);
		$cols[] = $summary['QuizAnswerSummaryCsv']['elapsed_second'];
		$cols[] = $summary['QuizAnswerSummaryCsv']['answer_number'];
		$cols[] = $summary['QuizAnswerSummaryCsv']['summary_score'];
		$cols[] = $this->_getStdScore($sampScore, $summary);

		foreach ($quiz['QuizPage'] as $page) {
			foreach ($page['QuizQuestion'] as $question) {
				list($ans, $score) = $this->_getAns($question, $answers);
				$cols[] = $ans;
				$cols[] = $score;
			}
		}
		return $cols;
	}
/**
 * _getStdScore
 *
 * @param array $sampScore 偏差値算出のための分散値と平均点
 * @param array $summary answer summary
 * @return float
 */
	protected function _getStdScore($sampScore, $summary) {
		$std = $sampScore['samp_score'];
		$avg = $sampScore['avg_score'];
		if ($std == 0) {
			$stdScore = 50;
		} else {
			$stdDiv = ($summary['QuizAnswerSummaryCsv']['summary_score'] - $avg) * 10 / $std;
			$stdScore = 50 + $stdDiv;
		}
		return round($stdScore);
	}
/**
 * _getUserName
 *
 * @param array $quiz quiz data
 * @param array $summary answer summary
 * @return string
 */
	protected function _getUserName($quiz, $summary) {
		if (empty($summary['User']['handlename'])) {
			return __d('quizzes', 'Guest');
		}
		return $summary['User']['handlename'];
	}

/**
 * _getAns
 *
 * @param array $question question data
 * @param array $answers answer data
 * @return array
 */
	protected function _getAns($question, $answers) {
		$retAns = '';
		// 回答配列データの中から、現在指定された質問に該当するものを取り出す
		$ans = Hash::extract(
			$answers,
			'{n}.QuizAnswer[quiz_question_key=' . $question['key'] . ']');
		// 回答が存在するとき処理
		if (! $ans) {
			// 通常の処理ではこのような場面はありえない
			// 小テストは空回答であっても回答レコードを作成するからです
			// データレコード異常があった場合のみです
			// ただ、この回答を異常データだからといってオミットすると、サマリの合計数と
			// 合わなくなって集計データが狂ってしまうので空回答だったように装って処理します
			return array($retAns, 0);
		}
		$ans = $ans[0];
		$retAns = implode(',', $ans['answer_value']);
		if ($ans['correct_status'] == QuizzesComponent::STATUS_GRADE_YET) {
			$score = __d('quizzes', 'Ungraded');
		} else {
			$score = $ans['score'];
		}
		return array($retAns, $score);
	}
}
