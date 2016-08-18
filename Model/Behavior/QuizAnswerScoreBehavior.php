<?php
/**
 * QuizAnswerScoreValidate Behavior
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('ModelBehavior', 'Model');

/**
 * QuizQuestionChoice Behavior
 *
 * @package  Quizzes\Quizzes\Model\Befavior\QuizAnswerScore
 * @author Allcreator <info@allcreator.net>
 */
class QuizAnswerScoreBehavior extends ModelBehavior {

/**
 * 回答採点
 *
 * @param Model &$model モデル
 * @param int $type 質問タイプ
 * @param bool $isOrderFixed 順番固定か否か
 * @param array $answer 回答データ
 * @param array $correct 正解データ
 * @return array answer
 */
	public function scoreAnswer(Model &$model, $type, $isOrderFixed, $answer, $correct) {
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
		$answer['QuizAnswer']['correct_status'] = $ret;
		return $answer;
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
		if ($answerArr == $correctArr) {
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
}
