<?php
/**
 * QuizAnswerSummary Model
 *
 * @property User $User
 * @property QuizAnswer $QuizAnswer
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Your Name <yourname@domain.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('QuizzesAppModel', 'Quizzes.Model');
App::uses('NetCommonsUrl', 'NetCommons.Utility');
App::uses('MailSettingFixedPhrase', 'Mails.Model');

/**
 * Summary for QuizAnswerSummary Model
 */
class QuizAnswerSummary extends QuizzesAppModel {

/**
 * use behaviors
 *
 * @var array
 */
	public $actsAs = array(
		// 自動でメールキューの登録, 削除。ワークフロー利用時はWorkflow.Workflowより下に記述する
		'Mails.MailQueue' => array(
			'embedTags' => array(
				'X-SUBJECT' => 'Quiz.title',
			),
			'keyField' => 'id',
			'typeKey' => MailSettingFixedPhrase::ANSWER_TYPE,
		),
		'Mails.MailQueueDelete',
	);

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'summary_score' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				'allowEmpty' => true,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'quiz_key' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

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
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'QuizAnswer' => array(
			'className' => 'Quizzes.QuizAnswer',
			'foreignKey' => 'quiz_answer_summary_id',
			'dependent' => true,
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
			'Quiz' => 'Quizzes.Quiz',
			'QuizAnswer' => 'Quizzes.QuizAnswer',
		]);
	}

/**
 * getCountAllAnswerSummary
 * 全回答数取得
 *
 * @param string $quizKey quiz key
 * @return int
 */
	public function getCountAllAnswerSummary($quizKey) {
		$condition = array(
			'quiz_key' => $quizKey,
		);
		$ret = $this->_getCountAnswerSummary($condition);
		return $ret;
	}

/**
 * getCountMyAnswerSummary
 * 自分の回答数取得
 *
 * @param string $quizKey quiz key
 * @param array $ids 回答したサマリID配列
 * @return int
 */
	public function getCountMyAnswerSummary($quizKey, $ids) {
		$condition = array(
			'quiz_key' => $quizKey,
		);
		// ログインしている人は自分の数がわかるけど、未ログインの人は指定小テスト
		if (Current::read('User.id')) {
			$condition['user_id'] = Current::read('User.id');
		} else {
			$condition['id'] = $ids;
		}
		$ret = $this->_getCountAnswerSummary($condition);
		return $ret;
	}

/**
 * _getCountAnswerSummary
 * 回答数取得
 *
 * @param array $condition 基本の他に追加したい条件
 * @return int
 */
	protected function _getCountAnswerSummary($condition) {
		$condition = Hash::merge(array(
			'answer_status' => QuizzesComponent::ACTION_ACT,
			'test_status' => QuizzesComponent::TEST_ANSWER_STATUS_PEFORM
		), $condition);
		$ret = $this->find('count', array(
			'conditions' => $condition,
			'recursive' => -1
		));
		$this->log( $this->getDataSource()->getLog(), LOG_DEBUG);
		return $ret;
	}

/**
 * getPassedQuizKeys
 * 合格した小テストのキーリストを返す
 * 汎用的にと考えたが、今のところ追加条件でUser.idをもらうようなシーンしか想定できていない
 *
 * @param array $addConditions 追加条件（必須）
 * @return array
 */
	public function getPassedQuizKeys($addConditions) {
		$conditions = Hash::merge(array(
			//'user_id' => Current::read('User.id'),
			'answer_status' => QuizzesComponent::ACTION_ACT,
			//'test_status' => QuizzesComponent::TEST_ANSWER_STATUS_PEFORM,
			'passing_status' => QuizzesComponent::STATUS_GRADE_PASS,
			'within_time_status' => QuizzesComponent::STATUS_GRADE_PASS,
			'OR' => array(
				'Quiz.passing_grade >' => 0,
				'Quiz.estimated_time >' => 0,
			)
		), $addConditions);
		$passQuizKeys = $this->find('all', array(
				'conditions' => $conditions,
				'fields' => array(
					'QuizAnswerSummary.id',
					'QuizAnswerSummary.quiz_key',
				),
				'joins' => array(
					array(
						'table' => 'quizzes',
						'alias' => 'Quiz',
						'type' => 'LEFT',
						'conditions' => array(
							'QuizAnswerSummary.quiz_key = Quiz.key',
						),
					),
				),
				'recursive' => -1,
			)
		);
		$passQuizKeys = Hash::combine(
			$passQuizKeys, '{n}.QuizAnswerSummary.id', '{n}.QuizAnswerSummary.quiz_key');
		return $passQuizKeys;
	}
/**
 * getCanGradingSummary
 * 現在のブロックで自分が採点権限を持つ回答サマリを取得する
 *
 * @return array 採点権限を持つ採点可能な回答サマリを返す
 */
	public function getCanGradingSummary() {
		$conditions = $this->Quiz->getBaseCondition();
		$conditions = Hash::merge($conditions, array('created_user' => Current::read('User.id')));
		$quizKeys = $this->Quiz->find('list', array(
			'conditions' => $conditions,
			'fields' => array('key'),
			'recursive' => -1
		));
		$summaryIds = $this->find('list', array(
			'conditions' => array(
				'quiz_key' => $quizKeys,
				'answer_status' => QuizzesComponent::ACTION_ACT,
			),
		));
		return $summaryIds;
	}
/**
 * saveStartSummary
 * 回答スタート時に呼ばれる。新しいレコードを作成する。
 *
 * @param array $quiz quiz
 * @return int 作成したサマリID
 * @throws InternalErrorException
 */
	public function saveStartSummary($quiz) {
		// 完了時以外はメールBehaviorを外す
		$this->Behaviors->unload('Mails.MailQueue');

		$this->begin();

		try {
			$netCommonsTime = new NetCommonsTime();
			$nowTime = $netCommonsTime->getNowDatetime();
			$userId = Current::read('User.id');
			// ログインしているとは回数を重ねることができますが
			if ($userId) {
				// 回数は純粋にその人の回答サマリ数を数えるのみ
				// 完答したかどうかとか見ない
				$count = $this->find('count', array(
					'conditions' => array(
						'quiz_key' => $quiz['Quiz']['key'],
						'user_id' => $userId
					)));
			} else {
				// ログインしてない人は回数履歴を重ねられません
				$count = 0;
			}
			$data = array(
				'answer_status' => QuizzesComponent::ACTION_NOT_ACT,
				'answer_number' => $count + 1,
				'test_status' => QuizzesComponent::TEST_ANSWER_STATUS_PEFORM,
				'answer_start_time' => $nowTime,
				'quiz_key' => $quiz['Quiz']['key'],
			);
			if ($quiz['Quiz']['status'] != WorkflowComponent::STATUS_PUBLISHED) {
				$data['test_status'] = QuizzesComponent::TEST_ANSWER_STATUS_TEST;
			}
			if ($userId) {
				$data['user_id'] = $userId;
			}
			$this->create();
			$this->set($data);
			if (! $this->save()) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
			$id = $this->getLastInsertID();

			$this->commit();
		} catch (Exception $ex) {
			$this->rollback();
			CakeLog::error($ex);
			throw $ex;
		}
		return $id;
	}

/**
 * saveAnswerEndSummary
 * 回答完了時に呼ばれる。確認待ち状態に変更。
 *
 * @param int $summaryId サマリID
 * @return bool
 * @throws InternalErrorException
 */
	public function saveAnswerEndSummary($summaryId) {
		// 完了時以外はメールBehaviorを外す
		$this->Behaviors->unload('Mails.MailQueue');

		$data['id'] = $summaryId;
		$data['answer_status'] = QuizzesComponent::ACTION_BEFORE_ACT;
		$this->begin();
		try {
			$ret = $this->save($data, false, array('answer_status'));
			if (! $ret) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
			$this->commit();
		} catch (Exception $ex) {
			$this->rollback();
			CakeLog::error($ex);
			throw $ex;
		}
		return $ret;
	}

/**
 * saveStartSummary
 * 確認完了時に呼ばれる。指定のサマリレコードを更新する。
 *
 * @param array $quiz quiz
 * @param int $summaryId サマリID
 * @return array 保存したデータ
 * @throws InternalErrorException
 */
	public function saveEndSummary($quiz, $summaryId) {
		$netCommonsTime = new NetCommonsTime();
		$nowTime = $netCommonsTime->getNowDatetime();
		$this->loadModels([
			'QuizAnswer' => 'Quizzes.QuizAnswer',
		]);

		$this->begin();
		try {
			$summary = $this->findById($summaryId);
			if (! $summary) {
				return $summary;
			}
			// メールのembed のURL設定を行っておく
			$url = NetCommonsUrl::actionUrl(array(
				'controller' => 'quiz_answers',
				'action' => 'grading',
				Current::read('Block.id'),
				'key' => $quiz['Quiz']['key'],
				'frame_id' => Current::read('Frame.id'),
				$summary['QuizAnswerSummary']['answer_number'],
			), true);
			$this->setAddEmbedTagValue('X-URL', $url);

			$score = $this->QuizAnswer->getScore($quiz, $summaryId);

			$data['id'] = $summaryId;
			$data['answer_status'] = QuizzesComponent::ACTION_ACT;
			$data['answer_finish_time'] = $nowTime;
			$data['elapsed_second'] =
				strtotime($nowTime) - strtotime($summary[$this->alias]['answer_start_time']);
			$data['summary_score'] = $score['graded'];

			//
			// 得点から判定
			//
			// 未採点が残っていないなら
			// 得点からの合格不合格判定を行う
			if ($score['ungraded'] == 0) {
				$data['is_grade_finished'] = true;
				$data['passing_status'] = QuizzesComponent::STATUS_GRADE_PASS;
				if ($quiz['Quiz']['passing_grade'] > 0) {
					if ($score['graded'] < $quiz['Quiz']['passing_grade']) {
						$data['passing_status'] = QuizzesComponent::STATUS_GRADE_FAIL;
					}
				}
			} else {
				// 未採点が残っているときは
				$data['is_grade_finished'] = false;
				$data['passing_status'] = QuizzesComponent::STATUS_GRADE_YET;
			}
			//
			// 経過時間から判定
			//
			$data['within_time_status'] = QuizzesComponent::STATUS_GRADE_PASS;
			if ($quiz['Quiz']['estimated_time'] > 0) {
				if ($data['elapsed_second'] > $quiz['Quiz']['estimated_time'] * 60) {
					$data['within_time_status'] = QuizzesComponent::STATUS_GRADE_FAIL;
				}
			}
			if (! $this->save($data, false, array(
				'answer_status',
				'summary_score',
				'is_grade_finished',
				'passing_status',
				'answer_finish_time',
				'elapsed_second',
				'within_time_status'))) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
			$this->commit();
		} catch (Exception $ex) {
			$this->rollback();
			CakeLog::error($ex);
			throw $ex;
		}
		return $data;
	}

/**
 * isPassAnswer
 * 合格回答なのか
 *
 * @param array $quiz quiz
 * @param array $summary summary
 * @return int
 */
	public function isPassAnswer($quiz, $summary) {
		if (isset($summary['QuizAnswerSummary'])) {
			$summary = $summary['QuizAnswerSummary'];
		}
		// 未採点状況
		if ($summary['is_grade_finished'] == false) {
			return QuizzesComponent::STATUS_GRADE_YET;
		}
		if ($quiz['Quiz']['passing_grade'] == 0 && $quiz['Quiz']['estimated_time'] == 0) {
			return QuizzesComponent::STATUS_GRADE_NONE;
		}
		// 不合格判定１
		if ($quiz['Quiz']['passing_grade'] != 0) {
			if ($summary['passing_status'] == QuizzesComponent::STATUS_GRADE_FAIL) {
				return QuizzesComponent::STATUS_GRADE_FAIL;
			}
		}
		// 不合格判定２
		if ($quiz['Quiz']['estimated_time'] != 0) {
			if ($summary['within_time_status'] == QuizzesComponent::STATUS_GRADE_FAIL) {
				return QuizzesComponent::STATUS_GRADE_FAIL;
			}
		}
		// 合格
		return QuizzesComponent::STATUS_GRADE_PASS;
	}

/**
 * deleteTestAnswerSummary
 * when quiz is published, delete test answer summary
 *
 * @param int $key quiz key
 * @param int $status publish status
 * @return bool
 * @throws InternalErrorException
 */
	public function deleteTestAnswerSummary($key, $status) {
		if ($status != WorkflowComponent::STATUS_PUBLISHED) {
			return true;
		}
		$this->begin();
		try {
			if (! $this->deleteAll(array(
				'quiz_key' => $key,
				'test_status' => QuizzesComponent::TEST_ANSWER_STATUS_TEST), true)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
			$this->commit();
		} catch (Exception $ex) {
			$this->rollback();
			CakeLog::error($ex);
			throw $ex;
		}
		return true;
	}

/**
 * getCorrectRate
 * 正答率取得
 *
 * @param array &$quiz quiz
 * @return void
 */
	public function getCorrectRate(&$quiz) {
		if (! $quiz['Quiz']['is_total_show']) {
			return;
		}
		$allCount = $this->find('count', array(
			'conditions' => array(
				'answer_status' => QuizzesComponent::ACTION_ACT,
				//FUJI 'test_status' => QuizzesComponent::TEST_ANSWER_STATUS_PEFORM,
				'quiz_key' => $quiz['Quiz']['key']
			)
		));
		foreach ($quiz['QuizPage'] as &$page) {
			foreach ($page['QuizQuestion'] as &$q) {
				$correct = $this->QuizAnswer->find('count', array(
					'conditions' => array(
						'quiz_question_key' => $q['key'],
						'correct_status' => QuizzesComponent::STATUS_GRADE_PASS,
					)
				));
				$wrong = $this->QuizAnswer->find('count', array(
					'conditions' => array(
						'quiz_question_key' => $q['key'],
						'correct_status' => QuizzesComponent::STATUS_GRADE_FAIL,
					)
				));
				$q['correct_percentage'] = round($correct / $allCount * 100, 2);
				$q['wrong_percentage'] = round($wrong / $allCount * 100, 2);
				$q['rest_percentage'] = ($allCount - $correct - $wrong) / $allCount * 100;
			}
		}
	}
}
