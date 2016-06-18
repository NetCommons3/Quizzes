<?php
/**
 * QuizAnswer Model
 *
 * @property QuizAnswerSummary $QuizAnswerSummary
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('QuizzesAppModel', 'Quizzes.Model');

/**
 * Summary for QuizAnswer Model
 */
class QuizAnswer extends QuizzesAppModel {

/**
 * answer max length
 *
 * @var int
 */
	const	QUIZ_MAX_ANSWER_LENGTH = 60000;

/**
 * use behaviors
 *
 * @var array
 */
	public $actsAs = array(
		'Quizzes.QuizAnswerValidate',
		'Quizzes.QuizAnswerScore',
	);

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
			'QuizCorrect' => 'Quizzes.QuizCorrect',
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
 */
	public function beforeValidate($options = array()) {
		// Choiceモデルは繰り返し判定が行われる可能性高いのでvalidateルールは最初に初期化
		// mergeはしません
		$this->validate = array(
			// scoreはここではなくAnswerGradeModelでチェックする

			'quiz_answer_summary_id' => array(
				'numeric' => array(
					'rule' => array('numeric'),
					'message' => __d('net_commons', 'Invalid request.'),
					'allowEmpty' => false,
					'required' => true,
				),
			),
			'quiz_question_key' => array(
				'notBlank' => array(
					'rule' => array('notBlank'),
					'message' => __d('net_commons', 'Invalid request.'),
					'allowEmpty' => false,
					'required' => true,
				),
			),
		);

		return parent::beforeValidate($options);
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
			if (! isset($val[$this->alias])) {
				continue;
			}
			if (! isset($val[$this->alias]['answer_value'])) {
				continue;
			}
			$val[$this->alias]['answer_value'] =
				explode(QuizzesComponent::ANSWER_DELIMITER, $val[$this->alias]['answer_value']);
			$val[$this->alias]['answer_correct_status'] =
				explode(QuizzesComponent::ANSWER_DELIMITER, $val[$this->alias]['answer_correct_status']);
		}
		return $results;
	}

/**
 * 回答したデータのうち、未採点の回答がある小テストキーを返す
 * 指定されるsummaryIdの配列は、回答が完了しているものが既に精査されていることが前提です
 *
 * @param array $summaryIds サマリID配列
 * @return array
 */
	public function getNotScoringQuizKey($summaryIds) {
		$ret = array();
		if (empty($summaryIds)) {
			return $ret;
		}
		$ret = $this->find('all', array(
			'fields' => array(
				'DISTINCT QuizAnswerSummary.quiz_key'
			),
			'conditions' => array(
				'QuizAnswer.correct_status' => QuizzesComponent::STATUS_GRADE_YET,
				'QuizAnswer.quiz_answer_summary_id' => $summaryIds,
				'NOT' => array('QuizAnswerSummary.quiz_key' => null)
			),
		));
		return $ret;
	}

/**
 * 指定された回答データの未採点数と採点済み点数を返す
 *
 * @param array $quiz 小テストデータ
 * @param int $summaryId サマリID
 * @return array 未採点の点数と採点済みの得点数
 */
	public function getScore($quiz, $summaryId) {
		$ret = array('ungraded' => 0, 'graded' => 0);
		$questionIds = Hash::extract($quiz, 'QuizPage.{n}.QuizQuestion.{n}.id');

		$ungrade = $this->find('first', array(
			'fields' => array('SUM(QuizQuestion.allotment) AS total_score'),
			'conditions' => array(
				'QuizAnswer.correct_status' => QuizzesComponent::STATUS_GRADE_YET,
				'quiz_answer_summary_id' => $summaryId
			),
			'joins' => array(
				array(
					'table' => 'quiz_questions',
					'alias' => 'QuizQuestion',
					'type' => 'LEFT',
					'conditions' => array(
						'QuizAnswer.quiz_question_key = QuizQuestion.key',
						'QuizQuestion.id' => $questionIds,
					),
				),
			),
			'group' => array('quiz_answer_summary_id'),
			'recursive' => -1,
		));
		if ($ungrade) {
			$ret['ungraded'] = $ungrade[0]['total_score'];
		}

		$grade = $this->find('first', array(
			'fields' => array('SUM(score) AS total_score'),
			'conditions' => array(
				'NOT' => array(
					'correct_status' => QuizzesComponent::STATUS_GRADE_YET,
				),
				'quiz_answer_summary_id' => $summaryId
			),
			'group' => array('quiz_answer_summary_id'),
			'recursive' => -1,
		));
		if ($grade) {
			$ret['graded'] = $grade[0]['total_score'];
		}
		return $ret;
	}

/**
 * 回答データの保存
 *
 * @param array $data 回答データ
 * @param array $quiz 小テストデータ
 * @param array $summary サマリデータ
 * @return bool
 * @throws InternalErrorException
 */
	public function saveAnswer($data, $quiz, $summary) {
		$this->log($data, 'debug');
		// 回答データを保存する
		//トランザクションBegin
		$this->begin();
		try {
			$summaryId = $summary['QuizAnswerSummary']['id'];
			// 繰り返しValidationを行うときは、こうやってエラーメッセージを蓄積するところ作らねばならない
			// 仕方ないCakeでModelObjectを使う限りは
			$validationErrors = array();
			foreach ($data['QuizAnswer'] as $answer) {
				// 対象の問題情報
				$targetQuestionKey = $answer[0]['quiz_question_key'];
				$targetQuestion = Hash::extract(
					$quiz['QuizPage'],
					'{n}.QuizQuestion.{n}[key=' . $targetQuestionKey . ']'
				);
				// 対象の問題が見つからないのはエラー
				if (! $targetQuestion) {
					throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
				}
				// データまとめ
				$saveAnswer = $answer[0];
				if (is_array($saveAnswer['answer_value'])) {
					$saveAnswer['answer_value'] = implode(
						QuizzesComponent::ANSWER_DELIMITER,
						$saveAnswer['answer_value']
					);
				}
				// サマリIDはここで固定的に設定
				$saveAnswer['quiz_answer_summary_id'] = $summaryId;
				$this->create();
				$this->set($saveAnswer);
				// データチェックと保存
				if (! $this->validates(array('question' => $targetQuestion[0]))) {
					$validationErrors[$targetQuestionKey] = Hash::filter($this->validationErrors);
				} elseif (! $this->save($saveAnswer, false)) {
					throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
				}
			}
			if (! empty($validationErrors)) {
				$this->validationErrors = Hash::filter($validationErrors);
				$this->rollback();
				return false;
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
 * 回答データの確定保存
 *
 * @param array $quiz 小テストデータ
 * @param array $summary サマリデータ
 * @return bool
 * @throws InternalErrorException
 */
	public function saveConfirmAnswer($quiz, $summary) {
		//トランザクションBegin
		$this->begin();
		try {
			$answers = $this->find('all', array(
				'conditions' => array('quiz_answer_summary_id' => $summary['QuizAnswerSummary']['id'])
			));
			foreach ($answers as $answer) {
				// 回答に対応する問題キー
				$qKey = $answer['QuizAnswer']['quiz_question_key'];
				$question = Hash::extract($quiz, 'QuizPage.{n}.QuizQuestion.{n}[key=' . $qKey . ']');
				if (! $question) {
					continue;
				}
				$question = $question[0];
				if ($question['question_type'] == QuizzesComponent::TYPE_TEXT_AREA) {
					// 長文は採点不可
					continue;
				}
				// その回答データが正解か判定し
				$answer = $this->scoreAnswer(
					$question['question_type'],
					$question['is_order_fixed'],
					$answer, $question['QuizCorrect']
				);
				// 回答データを更新する
				$isCorrect = $answer['QuizAnswer']['correct_status'];
				if ($isCorrect == QuizzesComponent::STATUS_GRADE_PASS) {
					$answer['QuizAnswer']['score'] = $question['allotment'];
				}
				if (is_array($answer['QuizAnswer']['answer_correct_status'])) {
					$answer['QuizAnswer']['answer_correct_status'] = implode(
						QuizzesComponent::ANSWER_DELIMITER,
						$answer['QuizAnswer']['answer_correct_status']
					);
				}

				$this->clear();
				if (! $this->save($answer, false, array('answer_correct_status', 'correct_status', 'score'))) {
					throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
				}
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
 * getProgressiveAnswerOfThisSummary
 *
 * @param array $summary quiz summary ( one record )
 * @return array
 */
	public function getProgressiveAnswerOfThisSummary($summary) {
		$answers = array();
		if (empty($summary)) {
			return $answers;
		}
		$answers = $this->find('all', array(
			'conditions' => array(
				'quiz_answer_summary_id' => $summary['QuizAnswerSummary']['id']
			),
			'recursive' => 0
		));
		if (! $answers) {
			return false;
		}
		return $answers;
	}
}
