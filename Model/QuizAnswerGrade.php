<?php
/**
 * QuizAnswerGrade Model
 *
 * @property QuizAnswerGrade $QuizAnswerGrade
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('QuizzesAppModel', 'Quizzes.Model');

/**
 * QuizAnswerGrade Model
 */
class QuizAnswerGrade extends QuizzesAppModel {

/**
 * Use table config
 *
 * @var string
 */
	public $useTable = 'quiz_answers';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array();

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'QuizAnswerSummary' => array(
			'className' => 'Quizzes.QuizAnswerSummary',
			'foreignKey' => 'quiz_answer_summary_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
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
			'QuizQuestion' => 'Quizzes.QuizQuestion',
			'QuizAnswer' => 'Quizzes.QuizAnswer',
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
		$quiz = $options['quiz'];
		$qKey = $this->data['QuizAnswerGrade']['quiz_question_key'];
		$question = Hash::extract($quiz['QuizPage'], '{n}.QuizQuestion.{n}[key=' . $qKey . ']');
		if (! $question) {
			$this->validationErrors['score'][] =
				__d('net_commons', 'Invalid request.');
			return false;
		}
		$question = $question[0];
		$answerIds = Hash::extract($options['answerSummary'], 'QuizAnswer.{n}.id');
		// 自由記述以外は採点対象じゃないです
		if ($question['question_type'] != QuizzesComponent::TYPE_TEXT_AREA) {
			$this->validationErrors['score'][] =
				__d('quizzes', 'The scoring is available only descriptive of the problem.');
			return false;
		}

		$allotment = $question['allotment'];

		$this->validate = Hash::merge($this->validate, array(
			'id' => array(
				'numeric' => array(
					'rule' => array('numeric'),
					'message' => __d('net_commons', 'Invalid request.'),
					'allowEmpty' => false,
					'required' => true,
				),
				'inList' => array(
					'rule' => array('inList', $answerIds),
					'message' => __d('net_commons', 'Invalid request.'),
				)
			),
			'correct_status' => array(
				'isCorrect' => array(
					'rule' => array('inList', array(
						QuizzesComponent::STATUS_GRADE_YET,
						QuizzesComponent::STATUS_GRADE_FAIL,
						QuizzesComponent::STATUS_GRADE_PASS)),
					'message' => __d('net_commons', 'Invalid request.'),
				)
			),
			'score' => array(
				'naturalNumber' => array(
					'rule' => array('naturalNumber', true),
					'message' => __d('quizzes', 'Please input natural number.'),
				),
				'maxCheck' => array(
					'rule' => array('comparison', '<=', $allotment),
					'message' =>
						__d('quizzes',
							'It is not possible to give the Scoring value or more of the points.'),
				),
			),
		));
		parent::beforeValidate($options);
		return true;
	}

/**
 * 採点の保存
 *
 * @param array $quiz 小テストデータ
 * @param int $summaryId サマリID
 * @param array $datas 採点データ配列
 * @return bool
 * @throws InternalErrorException
 */
	public function saveGrade($quiz, $summaryId, $datas) {
		$this->begin();
		try {
			foreach ($datas as $data) {
				$answer = $data['QuizAnswerGrade'];
				// validateは終わっていることが前提
				// see:QuizAnswerController-grade
				if (! $this->save($answer, false)) {
					throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
				}
			}
			// 未採点があるときはnullが返ってきます
			$score = $this->QuizAnswer->getScore($quiz, $summaryId);

			$summaryData = array();
			$summaryData['id'] = $summaryId;
			$summaryData['summary_score'] = $score['graded'];
			// 採点完了
			if ($score['ungraded'] == 0) {
				// 未採点はもうない場合

				// 採点完了状態にする
				$summaryData['is_grade_finished'] = true;
				// 点数による合格判定不要とされている小テストの場合は
				if ($quiz['Quiz']['passing_grade'] == 0) {
					// 無条件に合格状態にする
					$summaryData['passing_status'] = QuizzesComponent::STATUS_GRADE_PASS;
				} elseif ($quiz['Quiz']['passing_grade'] > 0 &&
					$score['graded'] >= $quiz['Quiz']['passing_grade']) {
					// 点数による合格判定アリの場合は
					// 点数が合格点を超えていたら合格です
					$summaryData['passing_status'] = QuizzesComponent::STATUS_GRADE_PASS;
				} else {
					// そうじゃないときは不合格
					$summaryData['passing_status'] = QuizzesComponent::STATUS_GRADE_FAIL;
				}
			} else {
				$summaryData['is_grade_finished'] = false;
				$summaryData['passing_status'] = QuizzesComponent::STATUS_GRADE_YET;
			}
			$this->QuizAnswerSummary->Behaviors->unload('Mails.MailQueue');
			if (! $this->QuizAnswerSummary->save($summaryData, false, array(
				'summary_score',
				'is_grade_finished',
				'passing_status'))) {
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
}
