<?php
/**
 * QuizGrading Helper
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator Co., Ltd. <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
App::uses('AppHelper', 'View/Helper');
/**
 * Quiz grading Helper
 *
 * @author Allcreator Co., Ltd. <info@allcreator.net>
 * @package NetCommons\Quizzes\View\Helper
 */
class QuizGradingHelper extends AppHelper {

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
 * 採点時のの回答、正解表示
 *
 * @param array $quiz 小テストデータ
 * @param int $pageIndex ページインデックス
 * @param int $questionIndex 質問インデックス
 * @param array $question 問題
 * @param array $answers 回答
 * @return string 回答のHTML
 */
	public function grading($quiz, $pageIndex, $questionIndex, $question, $answers) {
		$answer = Hash::extract($answers, 'QuizAnswer.{n}[quiz_question_key=' . $question['key'] . ']');
		if ($answer) {
			$answer = $answer[0];
		}
		$ret = $this->getScoreLabel($question, $answer);
		$ret .= $this->getQuestionLabel($quiz, $pageIndex, $questionIndex, $question, $answer);
		$ret .= $question['question_value'];
		$ret .= '<dl class="dl-horizontal">';
		$ret .= $this->getAnswer($question, $answer);
		if ($quiz['Quiz']['is_correct_show'] == true) {
			$ret .= $this->getCorrect($question, $answer);
		}
		$ret .= $this->getToGrade($quiz, $pageIndex, $questionIndex, $question, $answer);
		if ($quiz['Quiz']['is_total_show'] == true) {
			$ret .= $this->getCorrectTotal($question);
		}
		$ret .= '</dl>';
		return $ret;
	}
/**
 * 配点・得点表示
 *
 * @param array $question 問題
 * @param array $answer 回答
 * @return string 配点得点の文字列
 */
	public function getScoreLabel($question, $answer) {
		$ret = '<label class="pull-right text-muted">';
		if ($answer['correct_status'] != QuizzesComponent::STATUS_GRADE_YET) {
			$ret .= sprintf('(%3d点', $answer['score']);
		} else {
			$ret .= '(未採点';
		}
		$ret .= sprintf(' / 配点%3d点)', $question['allotment']);
		$ret .= '</label>';
		return $ret;
	}

/**
 * 解答表示
 *
 * @param array $question 問題
 * @param array $answer 回答
 * @return string 解答の文字列
 */
	public function getAnswer($question, $answer) {
		$ret = '<dt>' . 'あなたの解答' . '</dt>';
		$ret .= '<dd>';
		$this->_setupAnswer($question, $answer);
		if ($question['question_type'] == QuizzesComponent::TYPE_MULTIPLE_SELECTION) {
			$ret .= implode(',', $answer['answer_value']);
		} elseif ($question['question_type'] == QuizzesComponent::TYPE_MULTIPLE_WORD) {
			foreach ($answer['answer_value'] as $index => $ans) {
				$ret .= sprintf('(%d) %s <br />', $index + 1, $ans);
			}
		} else {
			$ret .= $answer['answer_value'];
		}
		$ret .= '</dd>';
		return $ret;
	}
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
		$ret = '<dt>' . '正解' . '</dt>';
		$ret .= '<dd>';
		$ret .= $this->_getCorrect($question['question_type'], $question['QuizCorrect']);
		$ret .= '</dd>';
		if (! empty($question['commentary'])) {
			$ret .= '<dt>' . '解説' . '</dt>';
			$ret .= '<dd>' . $question['commentary'] . '</dd>';
		}
		return $ret;
	}
/**
 * 正解表示
 *
 * @param int $type 問題タイプ
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
		return $correct['correct'];
	}
/**
 * 複数選択正解表示
 *
 * @param array $correct 正解
 * @return string 正解の文字列
 */
	protected function _getMultipleSelectCorrect($correct) {
		return implode(',', explode(QuizzesComponent::ANSWER_DELIMITER, $correct['correct']));
	}
/**
 * 単語正解表示
 *
 * @param array $correct 正解
 * @return string 正解の文字列
 */
	protected function _getWordCorrect($correct) {
		$words = explode(QuizzesComponent::ANSWER_DELIMITER, $correct['correct']);

		$ret = array_shift($words);
		if (! empty($words)) {
			$ret .= ' <buttontype="button" class="btn btn-default btn-sm" popover-placement="right" popover="';
			foreach ($words as $word) {
				$ret .= $word . ',';
			}
			$ret .= '">他に認められる解答</button>';
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

/**
 * 回答データのセットアップ
 *
 * @param array $question 問題
 * @param array &$answer 回答
 * @return array 複数回答の場合、回答データを配列にして返す
 */
	protected function _setupAnswer($question, &$answer) {
		if ($question['question_type'] == QuizzesComponent::TYPE_MULTIPLE_SELECTION ||
			$question['question_type'] == QuizzesComponent::TYPE_MULTIPLE_WORD) {
			$answer['answer_value'] = explode(QuizzesComponent::ANSWER_DELIMITER, $answer['answer_value']);
		}
	}
/**
 * 採点用
 *
 * @param array $quiz 小テスト
 * @param int $pageIndex ページインデックス
 * @param int $questionIndex 質問インデックス
 * @param array $question 問題
 * @param array $answer 回答
 * @return string 採点用input群文字列
 */
	public function getToGrade($quiz, $pageIndex, $questionIndex, $question, $answer) {
		if ($question['question_type'] != QuizzesComponent::TYPE_TEXT_AREA) {
			return '';
		}
		if (! $this->_View->Workflow->canEdit('Quiz', $quiz)) {
			return '';
		}
		$fieldNameBase = 'QuizAnswer.' . $questionIndex . '.';
		$ret = '<dt>採点</dt><dd><div class="form-inline"><div class="form-group">';
		$ret .= $this->Form->input($fieldNameBase . 'correct_status', array(
			'type' => 'radio',
			'options' => array(
				QuizzesComponent::STATUS_GRADE_YET => '未採点',
				QuizzesComponent::STATUS_GRADE_PASS => '正解',
				QuizzesComponent::STATUS_GRADE_FAIL => '不正解'),
			'div' => false,
			'legend' => false,
			'label' => false,
			'before' => '<label class="radio-inline">',
			'separator' => '</label><label class="radio-inline">',
			'after' => '</label>',
			'error' => false,
		));
		$ret .= '&nbsp;&nbsp;';
		$ret .= $this->Form->input($fieldNameBase . 'score', array(
			'div' => 'form-group',
			'label' => __d('quizzes', '点数'),
			'class' => 'form-control',
			'type' => 'number',
			'max' => $question['allotment'],
			'min' => 0
		));
		$ret .= $this->Form->hidden($fieldNameBase . 'id', array('value' => $answer['id']));
		$ret .= sprintf(' / %d 点', $question['allotment']);
		$ret .= '</div></div></dd>';
		return $ret;
	}
/**
 * 正答比率グラフ表示
 *
 * @param array $question 問題
 * @return string グラフ表示用AngularDirectionタグ
 */
	public function getCorrectTotal($question) {
		$questionId = $question['id'];
		$ret = '<dt>' . '正答比率' . '</dt>';
		$ret .= '<dd>';
		$ret .= '<nvd3 options="config"';
		$ret .= ' data=' . "'" . 'data["' . $questionId . '"]' . "'></nvd3>";
		$ret .= '</dd>';
		return $ret;
	}
/**
 * 問題ラベル（正解・不正解ラベル付き）
 *
 * @param array $quiz 小テストデータ
 * @param int $pageIndex ページインデックス
 * @param int $questionIndex 質問インデックス
 * @param array $question 問題
 * @param array $answer 回答
 * @return string 回答のHTML
 */
	public function getQuestionLabel($quiz, $pageIndex, $questionIndex, $question, $answer) {
		$ret = '<label class="control-label">';
		if ($quiz['Quiz']['page_count'] > 1) {
			$ret .= sprintf('ページ%2d - 問題%2d：', $pageIndex + 1, $questionIndex + 1);
		} else {
			$ret .= sprintf('問題%2d：', $questionIndex + 1);
		}
		$ret .= $this->getGradingLabel($answer);
		$ret .= '</label>';
		return $ret;
	}
/**
 * 正解・不正解ラベル
 *
 * @param array $answer 回答
 * @return string 回答のHTML
 */
	public function getGradingLabel($answer) {
		if (! isset($answer['correct_status'])) {
			$class = 'default';
			$label = '未回答';
		}
		if ($answer['correct_status'] == QuizzesComponent::STATUS_GRADE_YET) {
			$class = 'danger';
			$label = '未採点';
		} elseif ($answer['correct_status'] == QuizzesComponent::STATUS_GRADE_FAIL) {
			$class = 'warning';
			$label = '不正解';
		} else {
			$class = 'success';
			$label = '正解';
		}
		$ret = sprintf('<span class="label label-%s">%s</span>', $class, $label);
		return $ret;
	}
/**
 * グラフ用正答比率配列
 *
 * @param array $quiz 小テストデータ
 * @return array グラフ用正答比率データ配列
 */
	public function correctRate($quiz) {
		$correctRate = array();
		foreach ($quiz['QuizPage'] as $page) {
			foreach ($page['QuizQuestion'] as $question) {
				$correctRate[$question['id']] = [
					['key' => ' 未採点・未回答　　 ', 'color' => '#777777', 'values' => [['value' => $question['rest_percentage']]]],
					['key' => ' 正解 ', 'color' => '#5cb85c', 'values' => [['value' => $question['correct_percentage']]]],
					['key' => ' 不正解 ', 'color' => '#f0ad4e', 'values' => [['value' => $question['wrong_percentage']]]],
				];
			}
		}
		return $correctRate;
	}
}