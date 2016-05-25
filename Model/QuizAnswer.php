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
			'score' => array(
				'numeric' => array(
					'rule' => array('numeric'),
					//'message' => 'Your custom message here',
					//'allowEmpty' => false,
					//'required' => false,
					//'last' => false, // Stop validation after this rule
					//'on' => 'create', // Limit validation to 'create' or 'update' operations
				),
			),
			'quiz_answer_summary_id' => array(
				'numeric' => array(
					'rule' => array('numeric'),
					//'message' => 'Your custom message here',
					//'allowEmpty' => false,
					//'required' => false,
					//'last' => false, // Stop validation after this rule
					//'on' => 'create', // Limit validation to 'create' or 'update' operations
				),
			),
			'quiz_question_key' => array(
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
			$val[$this->alias]['answer_value'] =
				explode(QuizzesComponent::ANSWER_DELIMITER, $val[$this->alias]['answer_value']);
			$val[$this->alias]['answer_correct_status'] =
				explode(QuizzesComponent::ANSWER_DELIMITER, $val[$this->alias]['answer_correct_status']);
		}
		return $results;
	}

/**
 * 回答したデータのうち、未採点の回答がある小テストキーを返す
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
				'QuizAnswer.quiz_answer_summary_id' => $summaryIds
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
		/*
		$ret = $this->find('count', array(
			'conditions' => array(
				'QuizAnswer.correct_status' => QuizzesComponent::STATUS_GRADE_YET,
				'quiz_answer_summary_id' => $summaryId
			),
			'recursive' => -1,
		));
		// 未採点があるうちは
		if ($ret > 0) {
			return null;
		}
		// 未採点状態じゃ無いデータの点を合計
		// ※長文問題とか不正解で部分点上げたりするものね
		$ret = $this->find('first', array(
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
		// 正解が一つもないときは「見つからない」のでfalseが返される
		if (! $ret) {
			return 0;
		}
		return $ret[0]['total_score'];
		*/
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
		$this->log($ungrade, 'debug');
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
		$this->log($ret, 'debug');
		return $ret;
	}

/**
 * 回答データの保存
 *
 * @param array $data 回答データ
 * @param array $quiz 小テストデータ
 * @param array $summary サマリデータ
 * @return bool
 * @throws $ex
 */
	public function saveAnswer($data, $quiz, $summary) {
		// 回答データを保存する
		//トランザクションBegin
		$this->begin();
		try {
			$summaryId = $summary['QuizAnswerSummary']['id'];
			// 繰り返しValidationを行うときは、こうやってエラーメッセージを蓄積するところ作らねばならない
			// 仕方ないCakeでModelObjectを使う限りは
			$validationErrors = array();
			foreach ($data['QuizAnswer'] as $answer) {
				$targetQuestionKey = $answer[0]['quiz_question_key'];
				// データ保存
				$saveAnswer = $answer[0];
				if (is_array($saveAnswer['answer_value'])) {
					$saveAnswer['answer_value'] = implode(
						QuizzesComponent::ANSWER_DELIMITER,
						$saveAnswer['answer_value']
					);
				}
				$saveAnswer['quiz_answer_summary_id'] = $summaryId;
				$this->create();
				if (! $this->save($saveAnswer)) {
					$validationErrors[$targetQuestionKey] = Hash::filter($this->validationErrors);
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
 */
	public function saveConfirmAnswer($quiz, $summary) {
		$this->loadModels([
			'QuizQuestion' => 'Quizzes.QuizQuestion',
			'QuizCorrect' => 'Quizzes.QuizCorrect',
		]);
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
			$isCorrect = $this->_scoreAnswer(
				$question['question_type'],
				$question['is_order_fixed'],
				$answer, $question['QuizCorrect']
			);
			// 回答データを更新する
			$answer['QuizAnswer']['correct_status'] = $isCorrect;
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
			$this->save($answer, false, array(
				'answer_correct_status',
				'correct_status',
				'score')
			);
		}
		// 最後にサマリ情報を更新する
		return true;
	}

/**
 * 回答採点
 *
 * @param int $type 質問タイプ
 * @param bool $isOrderFixed 順番固定か否か
 * @param array &$answer 回答データ
 * @param array $correct 正解データ
 * @return int
 */
	protected function _scoreAnswer($type, $isOrderFixed, &$answer, $correct) {
		if ($type == QuizzesComponent::TYPE_SELECTION) {
			$ret = $this->__scoreSingleChoice($answer, $correct);
		} elseif ($type == QuizzesComponent::TYPE_MULTIPLE_SELECTION) {
			$ret = $this->__scoreMultipleChoice($answer, $correct);
		} elseif ($type == QuizzesComponent::TYPE_WORD) {
			$ret = $this->__scoreWord($answer, $correct);
		} else {
			if ($isOrderFixed) {
				$ret = $this->__scoreMultipleWord($answer, $correct);
			} else {
				$ret = $this->__scoreMultipleWordWithoutOrder($answer, $correct);
			}
		}
		return $ret;
	}
/**
 * 解答ごとの正答状態設定
 *
 * @param array &$answer 回答データ
 * @param array $correct 正解データ
 * @return int
 */
	private function __setAnswerCorrectStatus(&$answer, $correct) {
		foreach ($answer['answer_value'] as $index => $ans) {
			if (in_array($ans, $correct['correct'])) {
				$answer['answer_correct_status'][$index] = QuizzesComponent::STATUS_GRADE_PASS;
			} else {
				$answer['answer_correct_status'][$index] = QuizzesComponent::STATUS_GRADE_FAIL;
			}
		}
	}

/**
 * 択一回答採点
 *
 * @param array &$answer 回答データ
 * @param array $correct 正解データ
 * @return int
 */
	private function __scoreSingleChoice(&$answer, $correct) {
		// answerも配列で来る
		// correctも配列でくる

		// 解答それぞれの正答状態設定
		$this->__setAnswerCorrectStatus($answer['QuizAnswer'], $correct[0]);

		// この問題に対しての正答状態
		if ($answer['QuizAnswer']['answer_value'][0] == $correct[0]['correct'][0]) {
			return QuizzesComponent::STATUS_GRADE_PASS;
		}
		return QuizzesComponent::STATUS_GRADE_FAIL;
	}

/**
 * 複数選択回答採点
 *
 * @param array &$answer 回答データ
 * @param array $correct 正解データ
 * @return int
 */
	private function __scoreMultipleChoice(&$answer, $correct) {
		// answerも配列で来る
		// correctも配列でくる
		// 解答それぞれの正答状態設定
		$this->__setAnswerCorrectStatus($answer['QuizAnswer'], $correct[0]);

		// この問題に対しての正答状態
		$correctArr = $correct[0]['correct'];
		$answerArr = $answer['QuizAnswer']['answer_value'];
		sort($answerArr);
		sort($correctArr);
		if (Hash::contains($correctArr, $answerArr)) {
			return QuizzesComponent::STATUS_GRADE_PASS;
		}
		return QuizzesComponent::STATUS_GRADE_FAIL;
	}

/**
 * 単語回答採点
 *
 * @param array &$answer 回答データ
 * @param array $correct 正解データ
 * @return int
 */
	private function __scoreWord(&$answer, $correct) {
		// 解答それぞれの正答状態設定
		$this->__setAnswerCorrectStatus($answer['QuizAnswer'], $correct[0]);

		$corrects = $correct[0]['correct'];
		if (in_array($answer['QuizAnswer']['answer_value'][0], $corrects)) {
			return QuizzesComponent::STATUS_GRADE_PASS;
		}
		return QuizzesComponent::STATUS_GRADE_FAIL;
	}

/**
 * 単語複数回答採点
 *
 * @param array &$answer 回答データ
 * @param array $correct 正解データ
 * @return int
 */
	private function __scoreMultipleWord(&$answer, $correct) {
		$ret = QuizzesComponent::STATUS_GRADE_PASS;
		$answerArr = $answer['QuizAnswer']['answer_value'];
		foreach ($answerArr as $index => $ans) {

			$corrects = $correct[$index]['correct'];

			if (! in_array($ans, $corrects)) {

				$ret = QuizzesComponent::STATUS_GRADE_FAIL;

				$answer['QuizAnswer']['answer_correct_status'][$index] =
					QuizzesComponent::STATUS_GRADE_FAIL;
			} else {
				$answer['QuizAnswer']['answer_correct_status'][$index] =
					QuizzesComponent::STATUS_GRADE_PASS;
			}
		}
		return $ret;
	}

/**
 * 順番を問わないときの単語複数回答採点
 *
 * @param array &$answer 回答データ
 * @param array $corrects 正解データ
 * @return int
 */
	private function __scoreMultipleWordWithoutOrder(&$answer, $corrects) {
		$answer['QuizAnswer']['answer_correct_status'] = array_fill(
			0,
			count($answer['QuizAnswer']['answer_value']),
			QuizzesComponent::STATUS_GRADE_FAIL
		);
		foreach ($answer['QuizAnswer']['answer_value'] as $aIdx => $ans) {
			foreach ($corrects as $cIdx => $correct) {
				if (in_array($ans, $correct['correct'])) {

					$answer['QuizAnswer']['answer_correct_status'][$aIdx] =
						QuizzesComponent::STATUS_GRADE_PASS;

					array_splice($corrects, $cIdx, 1);

					break;
				}
			}
		}
		if (count($corrects) == 0) {
			return QuizzesComponent::STATUS_GRADE_PASS;
		}
		return QuizzesComponent::STATUS_GRADE_FAIL;
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
