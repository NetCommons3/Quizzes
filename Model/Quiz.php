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
		// 自動でメールキューの登録, 削除。ワークフロー利用時はWorkflow.Workflowより下に記述する
		'Mails.MailQueue' => array(
			'embedTags' => array(
				'X-SUBJECT' => 'Questionnaire.title',
			),
		),
		'Mails.MailQueueDelete',
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
			'dependent' => true,
			'conditions' => '',
			'fields' => '',
			'order' => array('page_sequence ASC'),
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
		// ウィザード画面中はstatusチェックをしないでほしいので
		// ここに来る前にWorkflowBehaviorでつけられたstatus-validateを削除しておく
		$this->validate = Hash::merge($this->validate, array(
			'block_id' => array(
				'numeric' => array(
					'rule' => array('numeric'),
					'message' => __d('net_commons', 'Invalid request.'),
					// Limit validation to 'create' or 'update' operations 新規の時はブロックIDがなかったりするから
					'on' => 'update',
				)
			),
			'title' => array( // タイトル
				'rule' => 'notBlank',
				'message' => sprintf(__d('net_commons', 'Please input %s.'), __d('quizzes', 'Title')),
				'required' => true,
				'allowEmpty' => false,
				'required' => true,
			),
			'passing_grade' => array( // 合格点
				'numeric' => array(
					'rule' => array('numeric'),
					'allowEmpty' => true,
					//'message' => __d('net_commons', 'Invalid request.'),
				)
			),
			'estimated_time' => array( // 時間の目安（分）
				'numeric' => array(
					'rule' => array('numeric'),
					'allowEmpty' => true,
					//'message' => __d('net_commons', 'Invalid request.'),
				)
			),
			'answer_timing' => array(
				'answerTimingCheck' => array(
					'rule' => array(
						'inList', array(
							QuizzesComponent::USES_USE, QuizzesComponent::USES_NOT_USE
						)),
					'message' => __d('net_commons', 'Invalid request.'),
				),
			),
			'is_no_member_allow' => array(
				'boolean' => array(
					'rule' => array('boolean'),
					'message' => __d('net_commons', 'Invalid request.'),
				),
			),
			'is_key_pass_use' => array(
				'boolean' => array(
					'rule' => array('boolean'),
					'message' => __d('net_commons', 'Invalid request.'),
				),
			),
			'is_repeat_allow' => array(
				'boolean' => array(
					'rule' => array('boolean'),
					'message' => __d('net_commons', 'Invalid request.'),
				),
			),
			'is_image_authentication' => array(
				'boolean' => array(
					'rule' => array('boolean'),
					'message' => __d('net_commons', 'Invalid request.'),
				),
			),
		));
		$this->_setAnswerTimingValidation();
		$this->_setKeyPhraseValidation();
		$this->_setImageAuthValidation();

		parent::beforeValidate($options);

		// 最低でも１ページは存在しないとエラー
		if (! isset($this->data['QuizPage'][0])) {
			$this->validationErrors['pickup_error'] = __d('quizzes', 'please set at least one page.');
		} else {
			// ページデータが存在する場合
			// 配下のページについてバリデート
			$validationErrors = array();
			$maxPageIndex = count($this->data['QuizPage']);
			$options['maxPageIndex'] = $maxPageIndex;
			foreach ($this->data['QuizPage'] as $pageIndex => $page) {
				// それぞれのページのフィールド確認
				$this->QuizPage->create();
				$this->QuizPage->set($page);
				// ページシーケンス番号の正当性を確認するため、現在の配列インデックスを渡す
				$options['pageIndex'] = $pageIndex;
				if (!$this->QuizPage->validates($options)) {
					$validationErrors['QuizPage'][$pageIndex] = $this->QuizPage->validationErrors;
				}
			}
			$this->validationErrors += $validationErrors;
		}
		return true;
	}

/**
 * _setAnswerTimingValidation
 *
 * 回答期間に制限を与える設定の場合、
 * 回答期間にまつわるバリデーションを設定する
 *
 * @return void
 */
	protected function _setAnswerTimingValidation() {
		if ($this->data['Quiz']['answer_timing'] != QuizzesComponent::USES_USE) {
			return;
		}
		$this->validate['answer_start_period'] = array(
			'rule' => array('datetime', 'ymd'),
			'required' => true,
		);
		$this->validate['answer_end_period'] = array(
			'checkDateTime' => array(
				'rule' => array('datetime', 'ymd'),
				'required' => true,
			),
			'checkDateComp' => array(
				'rule' => array('comparison', '>=', $this->data['Quiz']['answer_start_period']),
				'message' => __d('quizzes', 'start period must be smaller than end period')
			)
		);
	}
/**
 * _setKeyPhraseValidation
 *
 * 認証キーを与える設定の場合、
 * 認証キーにまつわるバリデーションを設定する
 *
 * @return void
 */
	protected function _setKeyPhraseValidation() {
		if (! $this->data['Quiz']['is_key_pass_use']) {
			return;
		}
		$this->validate['is_image_authentication'] = array(
			'rule' => array('inList', array(QuizzesComponent::USES_NOT_USE, false)),
			'message' =>
				__d('quizzes',
					'Authentication key setting , image authentication , either only one can not be selected.')
		);
		if (! Validation::notBlank($this->data['AuthorizationKey']['authorization_key'])) {
			$this->validationErrors['is_key_pass_use'][] =
				__d('quizzes',
					'Please input key phrase.');
		}
	}
/**
 * _setImageAuthValidation
 *
 * 画像認証を与える設定の場合、
 * 画像認証にまつわるバリデーションを設定する
 *
 * @return void
 */
	protected function _setImageAuthValidation() {
		if (! $this->data['Quiz']['is_image_authentication']) {
			return;
		}
		$this->validate['is_key_pass_use'] = array(
			'rule' => array('inList', array(QuizzesComponent::USES_NOT_USE, false)),
			'message' =>
				__d('quizzes',
					'Authentication key setting , image authentication , either only one can not be selected.')
		);
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
		$allotments = Hash::extract($this->data['QuizPage'], '{n}.QuizQuestion.{n}.allotment');
		$this->data['perfect_score'] = array_sum($allotments);
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
		$frame['Frame'] = $data['Frame'];

		$this->begin();

		try {
			$this->QuizSetting->saveBlock($frame);
			// 設定情報も
			$this->QuizSetting->saveSetting();
			$this->commit();
		} catch (Exception $ex) {
			//トランザクションRollback
			$this->rollback();
			//エラー出力
			CakeLog::error($ex);
			throw $ex;
		}
		return $data;
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
		$conditions = array_merge($conditions, $addConditions);
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

/**
 * saveQuiz
 * save Quiz data
 *
 * @param array &$quiz quiz
 * @throws InternalErrorException
 * @return bool
 */
	public function saveQuiz(&$quiz) {
		// 設定画面を表示する前にこのルームのブロックがあるか確認
		// 万が一、まだ存在しない場合には作成しておく
		// afterFrameSaveが呼ばれず、また最初に設定画面が開かれもしなかったような状況の想定
		$frame['Frame'] = Current::read('Frame');
		$this->afterFrameSave($frame);

		//トランザクションBegin
		$this->begin();

		try {
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
 * saveExportKey
 * update export key
 *
 * @param int $quizId id of quiz
 * @param string $exportKey exported key ( finger print)
 * @throws InternalErrorException
 * @return bool
 */
	public function saveExportKey($quizId, $exportKey) {
		$this->begin();
		try {
			$this->id = $quizId;
			$this->Behaviors->unload('Mails.MailQueue');
			if (! $this->saveField('export_key', $exportKey)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
			$this->commit();
		} catch (Exception $ex) {
			//トランザクションRollback
			$this->rollback();
			//エラー出力
			CakeLog::error($ex);
			throw $ex;
		}
		return true;
	}
/**
 * deleteQuiz
 * Delete the quiz data set of specified ID
 *
 * @param array $data post data
 * @throws InternalErrorException
 * @return bool
 */
	public function deleteQuiz($data) {
		$this->begin();
		try {
			// 小テスト削除
			if (! $this->deleteAll(array(
				'Quiz.key' => $data['Quiz']['key']), true, true)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}

			//コメントの削除
			$this->deleteCommentsByContentKey($data['Quiz']['key']);

			// 表示設定削除
			if (! $this->QuizFrameDisplayQuiz->deleteAll(array(
				'quiz_key' => $data['Quiz']['key']), true, false)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
			// アンケート回答削除
			if (! $this->QuizAnswerSummary->deleteAll(array(
				'quiz_key' => $data['Quiz']['key']), true, false)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
			$this->commit();
		} catch (Exception $ex) {
			//トランザクションRollback
			$this->rollback();
			//エラー出力
			CakeLog::error($ex);
			throw $ex;
		}

		return true;
	}
}
