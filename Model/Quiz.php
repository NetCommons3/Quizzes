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
 * バリデートタイプ
 * ウィザード画面で形成中の判定をしてほしいときに使う
 */
	const	QUIZ_VALIDATE_TYPE = 'duringSetup';

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
		'Quizzes.QuizValidate',
		// 自動でメールキューの登録, 削除。ワークフロー利用時はWorkflow.Workflowより下に記述する
		'Mails.MailQueue' => array(
			'embedTags' => array(
				'X-SUBJECT' => 'Quiz.title',
				'X-URL' => array(
					'controller' => 'quiz_answers'
				)
			),
		),
		'Mails.MailQueueDelete',
		//新着情報
		'Topics.Topics' => array(
			'fields' => array(
				//※小テストの場合、'title'は$this->dataの値をセットしないので、
				//　ここではセットせずに、save直前で新着タイトルをセットする
				'publish_start' => 'answer_start_period',
				'answer_period_start' => 'answer_start_period',
				'answer_period_end' => 'answer_end_period',
				'path' => '/:plugin_key/quiz_answers/view/:block_id/:content_key',
			),
			'search_contents' => array(
				'title', 'sub_title'
			),
		),
		//多言語
		'M17n.M17n' => array(
			'commonFields' => array(
				'status',
				'is_active',
				'is_latest',
				'passing_grade',
				'estimated_time',
				'answer_timing',
				'answer_start_period',
				'answer_end_period',
				'is_no_member_allow',
				'is_key_pass_use',
				'is_image_authentication',
				'is_repeat_allow',
				'is_repeat_until_passing',
				'is_page_random',
				'perfect_score',
				'is_correct_show',
				'is_total_show',
				'is_answer_mail_send',
				'import_key',
				'export_key',
			),
			'associations' => array(
				'QuizPage' => array(
					'class' => 'Quizzes.QuizPage',
					'foreignKey' => 'quiz_id',
					'associations' => array(
						'QuizQuestion' => array(
							'class' => 'Quizzes.QuizQuestion',
							'foreignKey' => 'quiz_page_id',
							'associations' => array(
								'QuizChoice' => array(
									'class' => 'Quizzes.QuizChoice',
									'foreignKey' => 'quiz_question_id',
									'isM17n' => true,
								),
								'QuizCorrect' => array(
									'class' => 'Quizzes.QuizCorrect',
									'foreignKey' => 'quiz_question_id',
									'isM17n' => true,
								),
							),
							'isM17n' => true,
						),
					),
					'isM17n' => true,
				),
				'AuthorizationKey' => array(
					'class' => 'AuthorizationKeys.AuthorizationKey',
					'foreignKey' => 'content_id',
					'fieldForIdentifyPlugin' => array('field' => 'model', 'value' => 'Quiz'),
					'isM17n' => false
				),
			),
			'afterCallback' => false,
		),
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
			'QuizFrameSetting' => 'Quizzes.QuizFrameSetting',
			'QuizPage' => 'Quizzes.QuizPage',
			'QuizQuestion' => 'Quizzes.QuizQuestion',
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
		if (Hash::check($options, 'validate') == self::QUIZ_VALIDATE_TYPE) {
			$this->validate = Hash::remove($this->validate, 'status');
		}
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
				'message' => __d('net_commons', 'Please input %s.', __d('quizzes', 'Title')),
				'required' => true,
				'allowEmpty' => false,
				'required' => true,
			),
			'passing_grade' => array( // 合格点
				'naturalNumber' => array(
					'rule' => array('naturalNumber', true),
					'allowEmpty' => true,
					'message' => __d('quizzes', 'Please input natural number.'),
				)
			),
			'estimated_time' => array( // 時間の目安（分）
				'naturalNumber' => array(
					'rule' => array('naturalNumber', true),
					'allowEmpty' => true,
					'message' => __d('quizzes', 'Please input natural number.'),
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
				'requireOtherFields' => array(
					'rule' => array(
						'requireOtherFields',
						QuizzesComponent::USES_USE,
						array('Quiz.answer_start_period', 'Quiz.answer_end_period'),
						'OR'
					),
					'message' => __d('quizzes', 'if you set the period, please set time.')
				)
			),
			'answer_start_period' => array(
				'checkDateTime' => array(
					'rule' => 'checkDateTime',
					'message' => __d('net_commons',
						'Unauthorized pattern for %s. Please input the data in %s format.',
						__d('quizzes', 'Start period'), 'YYYY-MM-DD hh:mm:ss')
				)
			),
			'answer_end_period' => array(
				'checkDateTime' => array(
					'rule' => 'checkDateTime',
					'message' => __d('net_commons',
						'Unauthorized pattern for %s. Please input the data in %s format.',
						__d('quizzes', 'End period'), 'YYYY-MM-DD hh:mm:ss')
				),
				'checkDateComp' => array(
					'rule' => array('checkDateComp', '>=', 'answer_start_period'),
					'message' => __d('quizzes', 'start period must be smaller than end period.')
				)
			),
			'is_page_random' => array(
				'boolean' => array(
					'rule' => array('boolean'),
					'message' => __d('net_commons', 'Invalid request.'),
				),
			),
			'is_correct_show' => array(
				'boolean' => array(
					'rule' => array('boolean'),
					'message' => __d('net_commons', 'Invalid request.'),
				),
			),
			'is_total_show' => array(
				'boolean' => array(
					'rule' => array('boolean'),
					'message' => __d('net_commons', 'Invalid request.'),
				),
			),
			'is_answer_mail_send' => array(
				'boolean' => array(
					'rule' => array('boolean'),
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
			'is_repeat_until_passing' => array(
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
		$this->validate['is_image_authentication']['inList'] = array(
			'rule' => array('inList', array(QuizzesComponent::USES_NOT_USE, false)),
			'message' =>
				__d('quizzes',
					'Authentication key setting , image authentication , either only one can not be selected.')
		);
		if (! isset($this->data['AuthorizationKey']) ||
			! Validation::notBlank($this->data['AuthorizationKey']['authorization_key'])) {
			$this->validationErrors['is_key_pass_use'][] =
				__d('quizzes', 'Please input key phrase.');
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
		$this->validate['is_key_pass_use']['inList'] = array(
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
		$this->data['Quiz']['perfect_score'] = array_sum($allotments);
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
		$quizPages = array();
		$quizQuestions = array();
		$quizAnswerCts = array();
		if ($this->recursive >= 0) {
			$quizIds = Hash::extract($results, '{n}.Quiz.id');
			// Quiz.idの配列から対応するQuizPageの配列を取得
			$quizPages = $this->QuizPage->find('all', array(
				'conditions' => array(
					'quiz_id' => $quizIds,
				),
				'order' => array('quiz_id ASC', 'page_sequence ASC'),
				'recursive' => -1
			));
			// QuizPage.idの配列から対応するQuizQuestionの配列を取得
			$quizPageIds = Hash::extract($quizPages, '{n}.QuizPage.id');
			$quizQuestions = $this->QuizQuestion->find('all', array(
				'conditions' => array(
					'quiz_page_id' => $quizPageIds,
				),
				'order' => array('quiz_page_id ASC', 'question_sequence ASC'),
			));
			// Quiz.idの配列から対応するQuizAnswerCountの配列を取得
			$this->QuizAnswerSummary->virtualFields = array('all_answer_count' => 'COUNT(quiz_key)');
			$quizAnswerCts = $this->QuizAnswerSummary->find('all', array(
				'fields' => array(
					'quiz_key',
					'all_answer_count'
				),
				'conditions' => array(
					'quiz_key' => Hash::extract($results, '{n}.Quiz.key'),
					'answer_status' => QuizzesComponent::ACTION_ACT,
					'test_status' => QuizzesComponent::TEST_ANSWER_STATUS_PEFORM
				),
				'group' => 'quiz_key',
				'recursive' => -1
			));
			$this->QuizAnswerSummary->virtualFields = array();
		}

		foreach ($results as &$val) {
			// この場合はcount
			if (! isset($val['Quiz']['id'])) {
				continue;
			}
			// この場合はdelete
			if (! isset($val['Quiz']['key'])) {
				continue;
			}
			// この場合はlist取得
			if (! isset($val['Quiz']['answer_timing'])) {
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
			$quizAnswer = Hash::extract(
				$quizAnswerCts,
				'{n}.QuizAnswerSummary[quiz_key=' . $val['Quiz']['key'] . ']'
			);
			$val['Quiz']['all_answer_count'] = Hash::get($quizAnswer, '0.all_answer_count', 0);
			$this->QuizPage->getPageForQuiz($val, $quizPages, $quizQuestions);
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
			$this->QuizFrameSetting->saveDefaultFrameSetting();
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
 * get result view condition method
 *
 * @param array $addConditions 追加条件
 * @return array
 */
	public function getResultViewCondition($addConditions = array()) {
		// ベースとなる権限のほかに,期間、会員専用
		$conditions = $this->getBaseCondition($addConditions);
		$periodCondition = $this->_getPeriodConditions();
		$conditions[] = $periodCondition;
		if (! Current::read('User')) {
			$conditions['is_no_member_allow'] = QuizzesComponent::PERMISSION_PERMIT;
		}
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

		// 小テストは始まってないやつは見せないけど
		// 終わったやつは見せていてよいと思う
		$limitedConditions[] = array('OR' => array(
			'Quiz.answer_start_period <=' => $nowTime,
			'Quiz.answer_start_period' => null,
		));
		//$limitedConditions[] = array(
		//	'OR' => array(
		//		'Quiz.answer_end_period >=' => $nowTime,
		//		'Quiz.answer_end_period' => null,
		//	));

		$timingConditions = array(
			'OR' => array(
				'Quiz.answer_timing' => QuizzesComponent::USES_NOT_USE,
				'AND' => array(
					'Quiz.answer_timing' => QuizzesComponent::USES_USE,
					$limitedConditions,
				)
			));

		if (Current::permission('content_creatable')) {
			$timingConditions['OR']['Quiz.created_user'] = Current::read('User.id');
		}

		return $timingConditions;
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
			$quiz['Block'] = Current::read('Block');
			$quiz['Quiz']['block_id'] = Current::read('Frame.block_id');
			$status = $quiz['Quiz']['status'];
			$this->create();
			// 小テストは履歴を取っていくタイプのコンテンツデータなのでSave前にはID項目はカット
			// （そうしないと既存レコードのUPDATEになってしまうから）
			// （ちなみにこのカット処理をbeforeSaveで共通でやってしまおうとしたが、
			//   beforeSaveでIDをカットしてもUPDATE動作になってしまっていたのでここに置くことにした)
			$quiz = Hash::remove($quiz, 'Quiz.id');

			$this->set($quiz);
			if (!$this->validates()) {
				return false;
			}

			//新着データセット
			$this->setTopicValue(
				'title', __d('quizzes', '%s started', $quiz['Quiz']['title'])
			);
			if (! $quiz['Quiz']['answer_timing']) {
				$this->setTopicValue('publish_start', null);
				$this->setTopicValue('answer_period_start', null);
				$this->setTopicValue('answer_period_end', null);
			}

			$saveQuiz = $this->save($quiz, false);
			if (! $saveQuiz) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
			$quizId = $this->id;

			// ページ以降のデータを登録
			$quiz = Hash::insert($quiz, 'QuizPage.{n}.quiz_id', $quizId);
			if (! $this->QuizPage->saveQuizPage($quiz['QuizPage'])) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
			// フレーム内表示対象アンケートに登録する
			if (! $this->QuizFrameDisplayQuiz->saveDisplayQuiz(array(
				'quiz_key' => $saveQuiz['Quiz']['key'],
				'frame_key' => Current::read('Frame.key')
			))) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
			// これまでのテスト回答データを消す
			$this->QuizAnswerSummary->deleteTestAnswerSummary($saveQuiz['Quiz']['key'], $status);

			//多言語化の処理
			$this->set($saveQuiz);
			$this->saveM17nData();

			$this->commit();
		} catch (Exception $ex) {
			$this->rollback($ex);
		}
		return $saveQuiz;
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
			$options = array(
				'validate' => false,
				'callbacks' => false
			);
			if (! $this->saveField('export_key', $exportKey, $options)) {
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
			$deleteQuizzes = $this->find('all', array(
				'conditions' => array('Quiz.key' => $data['Quiz']['key']),
				'recursive' => -1
			));
			foreach ($deleteQuizzes as $quiz) {
				if (! $this->QuizPage->deleteQuizPage($quiz['Quiz']['id'])) {
					throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
				}
				if (! $this->delete($quiz['Quiz']['id'], false)) {
					throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
				}
			}

			//コメントの削除
			$this->deleteCommentsByContentKey($data['Quiz']['key']);

			// 表示設定削除
			if (! $this->QuizFrameDisplayQuiz->deleteAll(array(
				'quiz_key' => $data['Quiz']['key']), true, false)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
			// 小テスト回答削除
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
