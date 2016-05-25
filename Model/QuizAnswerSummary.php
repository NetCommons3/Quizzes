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
		// ログインしている人は自分の数がわかるけど、未ログインの人は指定アンケート
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
		return $ret;
	}

/**
 * getPassedQuizKeys
 * 合格した小テストのキーリストを返す
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
		), $addConditions);
		$passQuizKeys = $this->find(
			'list',
			array(
				'conditions' => $conditions,
				'fields' => array('QuizAnswerSummary.quiz_key'),
				'recursive' => -1
			)
		);
		return $passQuizKeys;
	}
/**
 * saveStartSummary
 * 回答スタート時に呼ばれる。新しいレコードを作成する。
 *
 * @param array $quiz quiz
 * @param array $ids 回答したサマリID配列
 * @return int 作成したサマリID
 * @throws InternalErrorException
 */
	public function saveStartSummary($quiz, $ids) {
		// 完了時以外はメールBehaviorを外す
		$this->Behaviors->unload('Mails.MailQueue');

		$this->begin();

		try {
			$netCommonsTime = new NetCommonsTime();
			$nowTime = $netCommonsTime->getNowDatetime();
			$count = 0;
			if (Current::read('User.id')) {
				$count = $this->getCountMyAnswerSummary($quiz['Quiz']['key'], $ids);
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
			if (Current::read('User.id')) {
				$data['user_id'] = Current::read('User.id');
			}
			$this->create();
			$this->set($data);
			$this->save();
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
				return false;
			}
			// メールのembed のURL設定を行っておく
			$url = NetCommonsUrl::actionUrl(array(
				'controller' => 'quiz_answers',
				'action' => 'grading',
				Current::read('Block.id'),
				'key' => $quiz['Quiz']['key'],
				'frame_id' => Current::read('Frame.id'),
				$summary['QuizAnswerSummary']['answer_number'],
			));
			$this->setAddEmbedTagValue('X-URL', $url);

			$score = $this->QuizAnswer->getScore($quiz, $summaryId);

			$data['id'] = $summaryId;
			$data['answer_status'] = QuizzesComponent::ACTION_ACT;
			$data['answer_finish_time'] = $nowTime;
			$data['elapsed_second'] =
				strtotime($nowTime) - strtotime($summary[$this->alias]['answer_start_time']);
			$data['summary_score'] = $score['graded'];

			if ($score['ungraded'] == 0) {
				$data['is_grade_finished'] = true;
				if ($quiz['Quiz']['passing_grade'] > 0 && $score['graded'] >= $quiz['Quiz']['passing_grade']) {
					$data['passing_status'] = QuizzesComponent::STATUS_GRADE_PASS;
				} else {
					$data['passing_status'] = QuizzesComponent::STATUS_GRADE_FAIL;
				}
			} else {
				$data['is_grade_finished'] = false;
				$data['passing_status'] = QuizzesComponent::STATUS_GRADE_YET;
			}
			if ($quiz['Quiz']['estimated_time'] > 0 &&
				$data['elapsed_second'] <= $quiz['Quiz']['estimated_time'] * 60) {
				$data['within_time_status'] = QuizzesComponent::STATUS_GRADE_PASS;
			} else {
				$data['within_time_status'] = QuizzesComponent::STATUS_GRADE_FAIL;
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
		return true;
	}

/**
 * deleteTestAnswerSummary
 * when quiz is published, delete test answer summary
 *
 * @param int $key quiz key
 * @param int $status publish status
 * @return bool
 */
	public function deleteTestAnswerSummary($key, $status) {
		if ($status != WorkflowComponent::STATUS_PUBLISHED) {
			return true;
		}
		$this->deleteAll(array(
			'quiz_key' => $key,
			'test_status' => QuizzesComponent::TEST_ANSWER_STATUS_TEST), true);
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
		$this->loadModels([
			'QuizAnswer' => 'Quizzes.QuizAnswer',
		]);
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
