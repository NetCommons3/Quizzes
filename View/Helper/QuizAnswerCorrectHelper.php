<?php
/**
 * Quiz Answer Correct Helper
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator Co., Ltd. <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
App::uses('AppHelper', 'View/Helper');
/**
 * Quiz answer correct Helper
 *
 * @author Allcreator Co., Ltd. <info@allcreator.net>
 * @package NetCommons\Quizzes\View\Helper
 */
class QuizAnswerCorrectHelper extends AppHelper {

/**
 * Other helpers used by FormHelper
 *
 * @var array
 */
	public $helpers = array(
		'NetCommonsForm',
		'Form'
	);

/**
 * 正解表示
 *
 * @param array $question 問題
 * @param array $answer 回答
 * @return string 正解の文字列
 */
	public function getCorrect($question, $answer) {
		// 長文記述に正解はない
		if ($question['question_type'] == QuizzesComponent::TYPE_TEXT_AREA) {
			return '';
		}
		$ret = '<dt class="quiz-grading-correct bg-success">';
		$ret .= __d('quizzes', 'Correct answer') . '</dt>';
		$ret .= '<dd class="quiz-grading-correct bg-success">';
		$ret .= $this->_getCorrect($question['question_type'], $question['QuizCorrect']);
		$ret .= '</dd>';
		if (! empty($question['commentary'])) {
			$ret .= '<dt class="quiz-grading-correct bg-success">' . __d('quizzes', 'Commentary') . '</dt>';
			$ret .= '<dd class="quiz-grading-correct bg-success">' . $question['commentary'] . '</dd>';
		}
		return $ret;
	}
/**
 * 正解表示
 *
 * @param int $type 問題種別
 * @param array $corrects 正解
 * @return string 正解の文字列
 */
	protected function _getCorrect($type, $corrects) {
		if ($type == QuizzesComponent::TYPE_SELECTION) {
			$ret = $this->_getSingleSelectCorrect($corrects[0]);
		} elseif ($type == QuizzesComponent::TYPE_MULTIPLE_SELECTION) {
			$ret = $this->_getMultipleSelectCorrect($corrects[0]);
		} elseif ($type == QuizzesComponent::TYPE_WORD) {
			$ret = $this->_getWordCorrect($corrects[0]);
		} elseif ($type == QuizzesComponent::TYPE_MULTIPLE_WORD) {
			$ret = $this->_getMultipleWordCorrect($corrects);
		}
		return $ret;
	}
/**
 * 択一選択正解表示
 *
 * @param array $correct 正解
 * @return string 正解の文字列
 */
	protected function _getSingleSelectCorrect($correct) {
		return $correct['correct'][0];
	}
/**
 * 複数選択正解表示
 *
 * @param array $correct 正解
 * @return string 正解の文字列
 */
	protected function _getMultipleSelectCorrect($correct) {
		return implode(',', $correct['correct']);
	}
/**
 * 単語正解表示
 *
 * @param array $correct 正解
 * @return string 正解の文字列
 */
	protected function _getWordCorrect($correct) {
		$words = $correct['correct'];

		$ret = array_shift($words);
		if (! empty($words) && count($words) > 0) {
			$ret .= ' <button type="button" class="btn btn-default btn-sm" ';
			$ret .= 'popover-placement="right" uib-popover="';
			foreach ($words as $word) {
				$ret .= $word . ',';
			}
			//他に認められる解答
			$ret .= '">' . __d('quizzes', 'Answer found in other') . '</button>';
		}
		return $ret;
	}
/**
 * 単語複数正解表示
 *
 * @param array $corrects 正解
 * @return string 正解の文字列
 */
	protected function _getMultipleWordCorrect($corrects) {
		$ret = '';
		foreach ($corrects as $index => $correct) {
			$ret .= sprintf('(%d) ', $index + 1);
			$ret .= $this->_getWordCorrect($correct);
			$ret .= '<br />';
		}
		return $ret;
	}

}
