<?php
/**
 * Quiz Model
 *
 * @property Language $Language
 * @property Block $Block
 * @property QuizPage $QuizPage
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Your Name <yourname@domain.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('QuizzesAppModel', 'Quizzes.Model');

/**
 * Summary for Quiz Model
 */
class Quiz extends QuizzesAppModel {

/**
 * use behaviors
 *
 * @var array
 */
	public $actsAs = array(
		'NetCommons.OriginalKey',
		'Workflow.Workflow',
		'Workflow.WorkflowComment',
		'AuthorizationKeys.AuthorizationKey',
		//FUJI		'Questionnaires.QuestionnaireValidate',
	);

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Language' => array(
			'className' => 'Language',
			'foreignKey' => 'language_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Block' => array(
			'className' => 'Block',
			'foreignKey' => 'block_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'QuizPage' => array(
			'className' => 'Quizzes.QuizPage',
			'foreignKey' => 'quiz_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);

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
			'Frame' => 'Frames.Frame',
			'QuizSetting' => 'Quizzes.QuizSetting',
			'QuizPage' => 'Quizzes.QuizPage',
			'QuizFrameDisplayQuiz' => 'Quizzes.QuizFrameDisplayQuiz',
			'QuizAnswerSummary' => 'Quizzes.QuizAnswerSummary',
		]);
	}

/**
 * Called during validation operations, before validation. Please note that custom
 * validation rules can be defined in $validate.
 *
 * @param array $options Options passed from Model::save().
 * @return bool True if validate operation should continue, false to abort
 * @link http://book.cakephp.org/2.0/en/models/callback-methods.html#beforevalidate
 * @see Model::save()
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 */
	public function beforeValidate($options = array()) {
		$this->validate = Hash::merge($this->validate, array(
			'language_id' => array(
				'numeric' => array(
					'rule' => array('numeric'),
					//'message' => 'Your custom message here',
					//'allowEmpty' => false,
					//'required' => false,
					//'last' => false, // Stop validation after this rule
					//'on' => 'create', // Limit validation to 'create' or 'update' operations
				),
			),
		));
	}
/**
 * Called before each save operation, after validation. Return a non-true result
 * to halt the save.
 *
 * @param array $options Options passed from Model::save().
 * @return bool True if the operation should continue, false if it should abort
 * @link http://book.cakephp.org/2.0/en/models/callback-methods.html#beforesave
 * @see Model::save()
 */
	public function beforeSave($options = array()) {
		$this->data['perfect_score'] = 0;
		foreach ($this->data['QuizPage'] as $page) {
			foreach ($page['QuizQuestion'] as $question) {
				$this->data['perfect_score'] = $question['allotment'];
			}
		}
		return true;
	}
/**
 * AfterFind Callback function
 *
 * @param array $results found data records
 * @param bool $primary indicates whether or not the current model was the model that the query originated on or whether or not this model was queried as an association
 * @return mixed
 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
 */
	public function afterFind($results, $primary = false) {
		$this->QuizPage = ClassRegistry::init('Quizzes.QuizPage', true);
		$this->QuizAnswerSummary = ClassRegistry::init('Quizzes.QuizAnswerSummary', true);

		foreach ($results as &$val) {
			// この場合はcount
			if (! isset($val['Quiz']['id'])) {
				continue;
			}
			// この場合はdelete
			if (! isset($val['Quiz']['key'])) {
				continue;
			}

			$val['Quiz']['period_range_stat'] = $this->getPeriodStatus(
				$val['Quiz']['answer_timing'],
				$val['Quiz']['answer_start_period'],
				$val['Quiz']['answer_end_period']);

			//
			// ページ配下の質問データも取り出す
			// かつ、ページ数、質問数もカウントする
			$val['Quiz']['page_count'] = 0;
			$val['Quiz']['question_count'] = 0;

			if ($this->recursive >= 0) {
				// ページ情報取り出し
				$this->QuizPage->setPageToQuiz($val);
				// 回答数取り出し
				$val['Quiz']['all_answer_count'] = $this->QuizAnswerSummary->find('count', array(
					'conditions' => array(
						'quiz_key' => $val['Quiz']['key'],
						'answer_status' => QuizzesComponent::ACTION_ACT,
						'test_status' => QuizzesComponent::TEST_ANSWER_STATUS_PEFORM
					),
					'recursive' => -1
				));
			}
		}
		return $results;
	}
/**
 * getPeriodStatus
 * get period status now and specified time
 *
 * @param bool $check flag data
 * @param string $startTime start time
 * @param string $endTime end time
 * @return int
 */
	public function getPeriodStatus($check, $startTime, $endTime) {
		$ret = QuizzesComponent::QUIZ_PERIOD_STAT_IN;
		if ($check == QuizzesComponent::USES_USE) {
			$nowTime = strtotime((new NetCommonsTime())->getNowDatetime());
			if ($nowTime < strtotime($startTime)) {
				$ret = QuizzesComponent::QUIZ_PERIOD_STAT_BEFORE;
			}
			if ($nowTime > strtotime($endTime)) {
				$ret = QuizzesComponent::QUIZ_PERIOD_STAT_END;
			}
		}
		return $ret;
	}

/**
 * After frame save hook
 *
 * このルームにすでに小テストブロックが存在した場合で、かつ、現在フレームにまだブロックが結びついてない場合、
 * すでに存在するブロックと現在フレームを結びつける
 *
 * @param array $data received post data
 * @return mixed On success Model::$data if its not empty or true, false on failure
 * @throws InternalErrorException
 */
	public function afterFrameSave($data) {
		// すでに結びついている場合は何もしないでよい
		if (!empty($data['Frame']['block_id'])) {
			return $data;
		}
		$frame = $data['Frame'];

		$this->begin();

		try{
			$this->_saveBlock($frame);
			$this->commit();
		} catch (Exception $ex) {
			//トランザクションRollback
			$this->rollback();
			//エラー出力
			CakeLog::error($ex);
			throw $ex;
		}
		// ルームに存在するブロックを探す
		$block = $this->Block->find('first', array(
			'conditions' => array(
				'Block.room_id' => $frame['room_id'],
				'Block.plugin_key' => $frame['plugin_key'],
			)
		));
		if (! $block) {
			throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
		}
		return $data;
	}

/**
 * getQuizzesList
 * get quizzes by specified block id and specified user id limited number
 *
 * @param array $conditions find condition
 * @param array $options 検索オプション
 * @return array
 */
	public function getQuizzesList($conditions, $options = array()) {
		// 絞込条件
		$baseConditions = $this->getBaseCondition();
		$conditions = Hash::merge($baseConditions, $conditions);

		// 取得オプション
		$this->QuizFrameSetting = ClassRegistry::init('Quizzes.QuizFrameSetting', true);
		$defaultOptions = $this->QuizFrameSetting->getQuizFrameSettingConditions(Current::read('Frame.key'));
		$options = Hash::merge($defaultOptions, $options);
		$this->recursive = -1;
		$list = $this->find('all',
			Hash::merge(
				array('conditions' => $conditions),
				$options
			)
		);
		return $list;
	}

/**
 * get index sql condition method
 *
 * @param array $addConditions 追加条件
 * @return array
 */
	public function getBaseCondition($addConditions = array()) {
		$conditions = $this->getWorkflowConditions(array(
			'block_id' => Current::read('Block.id'),
		));

		if ($addConditions) {
			$conditions = array_merge($conditions, $addConditions);
		}
		return $conditions;
	}

/**
 * get index sql condition method
 *
 * @param array $addConditions 追加条件
 * @return array
 */
	public function getCondition($addConditions = array()) {
		// ベースとなる権限のほかに現在フレームに表示設定されている小テストか見ている
		$conditions = $this->getBaseCondition($addConditions);

		// 表示対象になっている小テストだけにするための条件
		$keys = $this->QuizFrameDisplayQuiz->find('list', array(
			'conditions' => array('QuizFrameDisplayQuiz.frame_key' => Current::read('Frame.key')),
			'fields' => array('QuizFrameDisplayQuiz.quiz_key'),
			'recursive' => -1
		));
		$conditions['Quiz.key'] = $keys;

		$periodCondition = $this->_getPeriodConditions();
		$conditions[] = $periodCondition;

		if (! Current::read('User')) {
			$conditions['is_no_member_allow'] = QuizzesComponent::PERMISSION_PERMIT;
		}

		$conditions = array_merge($conditions, $addConditions);
		return $conditions;
	}

/**
 * 時限公開のconditionsを返す
 *
 * @return array
 */
	protected function _getPeriodConditions() {
		if (Current::permission('content_editable')) {
			return array();
		}
		$netCommonsTime = new NetCommonsTime();
		$nowTime = $netCommonsTime->getNowDatetime();

		$limitedConditions[] = array('OR' => array(
			'Quiz.answer_start_period <=' => $nowTime,
			'Quiz.answer_start_period' => null,
		));
		$limitedConditions[] = array(
			'OR' => array(
				'Quiz.answer_end_period >=' => $nowTime,
				'Quiz.answer_end_period' => null,
			));

		$timingConditions = array(
			'OR' => array(
				'Quiz.answer_timing' => QuizzesComponent::USES_NOT_USE,
				$limitedConditions,
			));

		if (Current::permission('content_creatable')) {
			$timingConditions['OR']['Quiz.created_user'] = Current::read('User.id');
		}

		return $timingConditions;
	}

/**
 * hasPublished method
 *
 * @param array $quiz quiz data
 * @return int
 */
	public function hasPublished($quiz) {
		if (isset($quiz['Quiz']['key'])) {
			$isPublished = $this->find('count', array(
				'recursive' => -1,
				'conditions' => array(
					'is_active' => true,
					'key' => $quiz['Quiz']['key']
				)
			));
		} else {
			$isPublished = 0;
		}
		return $isPublished;
	}

/**
 * saveQuiz
 * save Quiz data
 *
 * @param array &$quiz quiz
 * @throws InternalErrorException
 * @return bool
 */
	public function saveQuiz(&$quiz) {
		$this->loadModels([
			'QuizPage' => 'Quizzes.QuizPage',
			'QuizFrameDisplayQuiz' => 'Quizzes.QuizFrameDisplayQuiz',
			'QuizAnswerSummary' => 'Quizzes.QuizAnswerSummary',
		]);

		//トランザクションBegin
		$this->begin();

		try {
			$this->_saveBlock(Current::read('Frame'));
			$quiz['Quiz']['block_id'] = Current::read('Frame.block_id');
			$status = $quiz['Quiz']['status'];
			$this->create();
			// 小テストは履歴を取っていくタイプのコンテンツデータなのでSave前にはID項目はカット
			// （そうしないと既存レコードのUPDATEになってしまうから）
			// （ちなみにこのカット処理をbeforeSaveで共通でやってしまおうとしたが、
			//   beforeSaveでIDをカットしてもUPDATE動作になってしまっていたのでここに置くことにした)
			$quiz = Hash::remove($quiz, 'Quiz.id');

			$this->set($quiz);

			$saveQuiz = $this->save($quiz);
			if (! $saveQuiz) {
				$this->rollback();
				return false;
			}
			$quizId = $this->id;

			// ページ以降のデータを登録
			$quiz = Hash::insert($quiz, 'QuizPage.{n}.quiz_id', $quizId);
			if (! $this->QuizPage->saveQuizPage($quiz['QuizPage'])) {
				$this->rollback();
				return false;
			}
			// フレーム内表示対象アンケートに登録する
			if (! $this->QuizFrameDisplayQuiz->saveDisplayQuiz(array(
				'quiz_key' => $saveQuiz['Quiz']['key'],
				'frame_key' => Current::read('Frame.key')
			))) {
				$this->rollback();
				return false;
			}
			// これまでのテスト回答データを消す
			$this->QuizAnswerSummary->deleteTestAnswerSummary($saveQuiz['Quiz']['key'], $status);

			$this->commit();
		} catch (Exception $ex) {
			$this->rollback();
			CakeLog::error($ex);
			throw $ex;
		}
		return $quiz;
	}

/**
 * save block
 *
 * afterFrameSaveやsaveQuestionnaireから呼び出される
 *
 * @param array $frame frame data
 * @return bool
 * @throws InternalErrorException
 */
	protected function _saveBlock($frame) {
		// ルームに存在するブロックを探す
		$block = $this->Block->find('first', array(
			'conditions' => array(
				'Block.room_id' => $frame['room_id'],
				'Block.plugin_key' => $frame['plugin_key'],
				'Block.language_id' => $frame['language_id'],
			)
		));
		// まだない場合
		if (empty($block)) {
			// 作成する
			$block = $this->Block->save(array(
				'room_id' => $frame['room_id'],
				'language_id' => $frame['language_id'],
				'plugin_key' => $frame['plugin_key'],
			));
			if (! $block) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
			Current::$current['Block'] = $block['Block'];
		}

		$frame['block_id'] = $block['Block']['id'];
		if (! $this->Frame->save($frame)) {
			throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
		}
		Current::$current['Frame']['block_id'] = $block['Block']['id'];

		$blockSetting = $this->QuizSetting->create();
		$blockSetting['QuizSetting']['block_key'] = $block['Block']['key'];
		$this->QuizSetting->saveQuizSetting($blockSetting);
		return true;
	}
/**
 * clearQuizId 小テストデータからＩＤのみをクリアする
 *
 * @param array &$quiz アンケートデータ
 * @return void
 */
	public function clearQuizId(&$quiz) {
		foreach ($quiz as $qKey => $q) {
			if (is_array($q)) {
				$this->clearQuizId($quiz[$qKey]);
			} elseif (preg_match('/^id$/', $qKey) ||
				preg_match('/^key$/', $qKey) ||
				preg_match('/^created(.*?)/', $qKey) ||
				preg_match('/^modified(.*?)/', $qKey)) {
				unset($quiz[$qKey]);
			}
		}
	}
}
