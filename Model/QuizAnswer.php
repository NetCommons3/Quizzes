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
	public $validate = array(
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
 * 指定された回答データの未採点または合計点数を返す
 *
 * @param int $summaryId サマリID
 * @return int
 */
	public function getScore($summaryId) {
		$ret = $this->find('count', array(
			'conditions' => array(
				'QuizAnswer.correct_status' => QuizzesComponent::STATUS_GRADE_YET,
				'quiz_answer_summary_id' => $summaryId
			),
			'recursive' => -1,
		));
		$this->log($ret, 'debug');
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
		$this->log($ret, 'debug');
		return $ret[0]['total_score'];
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
			if ($question[0]['question_type'] == QuizzesComponent::TYPE_TEXT_AREA) {
				// 長文は採点不可
				continue;
			}
			// その回答データが正解か判定し
			$isCorrect = $this->_scoreAnswer(
				$question[0]['question_type'],
				$answer,
				$question[0]['QuizCorrect']
			);
			// 回答データを更新する
			$answer['QuizAnswer']['correct_status'] = $isCorrect;
			if ($isCorrect == QuizzesComponent::STATUS_GRADE_PASS) {
				$answer['QuizAnswer']['score'] = $question[0]['allotment'];
			}
			$this->clear();
			$this->save($answer);
		}
		// 最後にサマリ情報を更新する
		return true;
	}

/**
 * 回答採点
 *
 * @param int $type 質問タイプ
 * @param array $answer 回答データ
 * @param array $correct 正解データ
 * @return int
 */
	protected function _scoreAnswer($type, $answer, $correct) {
		if ($type == QuizzesComponent::TYPE_SELECTION) {
			$ret = $this->__scoreSingleChoice($answer, $correct);
		} elseif ($type == QuizzesComponent::TYPE_MULTIPLE_SELECTION) {
			$ret = $this->__scoreMultipleChoice($answer, $correct);
		} elseif ($type == QuizzesComponent::TYPE_WORD) {
			$ret = $this->__scoreWord($answer, $correct);
		} else {
			$ret = $this->__scoreMultipleWord($answer, $correct);
		}
		return $ret;
	}

/**
 * 択一回答採点
 *
 * @param array $answer 回答データ
 * @param array $correct 正解データ
 * @return int
 */
	private function __scoreSingleChoice($answer, $correct) {
		if ($answer['QuizAnswer']['answer_value'] == $correct[0]['correct']) {
			return QuizzesComponent::STATUS_GRADE_PASS;
		}
		return QuizzesComponent::STATUS_GRADE_FAIL;
	}

/**
 * 複数選択回答採点
 *
 * @param array $answer 回答データ
 * @param array $correct 正解データ
 * @return int
 */
	private function __scoreMultipleChoice($answer, $correct) {
		if ($answer['QuizAnswer']['answer_value'] == $correct[0]['correct']) {
			return QuizzesComponent::STATUS_GRADE_PASS;
		}
		return QuizzesComponent::STATUS_GRADE_FAIL;
	}

/**
 * 単語回答採点
 *
 * @param array $answer 回答データ
 * @param array $correct 正解データ
 * @return int
 */
	private function __scoreWord($answer, $correct) {
		$corrects = explode(QuizzesComponent::ANSWER_DELIMITER, $correct[0]['correct']);
		if (in_array($answer['QuizAnswer']['answer_value'], $corrects)) {
			return QuizzesComponent::STATUS_GRADE_PASS;
		}
		return QuizzesComponent::STATUS_GRADE_FAIL;
	}

/**
 * 単語複数回答採点
 *
 * @param array $answer 回答データ
 * @param array $correct 正解データ
 * @return int
 */
	private function __scoreMultipleWord($answer, $correct) {
		$answers = explode(QuizzesComponent::ANSWER_DELIMITER, $answer['QuizAnswer']['answer_value']);
		foreach ($answers as $index => $ans) {
			$corrects = explode(QuizzesComponent::ANSWER_DELIMITER, $correct[$index]['correct']);
			if (! in_array($ans, $corrects)) {
				return QuizzesComponent::STATUS_GRADE_FAIL;
			}
		}
		return QuizzesComponent::STATUS_GRADE_PASS;
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
		if (!empty($answers)) {
			foreach ($answers as $ans) {
				if (strpos($ans['QuizAnswer']['answer_value'], QuizzesComponent::ANSWER_DELIMITER)) {
					$ans['QuizAnswer']['answer_value'] = explode(
						QuizzesComponent::ANSWER_DELIMITER,
						$ans['QuizAnswer']['answer_value']
					);
				}
				$answers[$ans['QuizAnswer']['quiz_question_key']][] = $ans['QuizAnswer'];
			}
		}
		return $answers;
	}
}
