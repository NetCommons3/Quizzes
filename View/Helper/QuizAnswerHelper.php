<?php
/**
 * QuizAnswer Helper
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator Co., Ltd. <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
App::uses('AppHelper', 'View/Helper');
/**
 * Quiz answer Helper
 *
 * @author Allcreator Co., Ltd. <info@allcreator.net>
 * @package NetCommons\Quizzes\View\Helper
 */
class QuizAnswerHelper extends AppHelper {

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
 * Answer html create by question type
 *
 * @var array
 */
	protected $_answerFunc = array(
		QuizzesComponent::TYPE_SELECTION => 'singleChoice',
		QuizzesComponent::TYPE_MULTIPLE_SELECTION => 'multipleChoice',
		QuizzesComponent::TYPE_WORD => 'singleWord',
		QuizzesComponent::TYPE_TEXT_AREA => 'textArea',
		QuizzesComponent::TYPE_MULTIPLE_WORD => 'multipleWord',
	);

/**
 * 回答作成
 *
 * @param array $question 質問データ
 * @param bool $readonly 読み取り専用
 * @return string 回答HTML
 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
 */
	public function answer($question, $readonly = false) {
		// 質問セットをもらう
		// 種別に応じて質問＆回答の要素を作成し返す
		$index = $question['key'];
		$fieldName = 'QuizAnswer.' . $index . '.0.answer_value';

		$ret = call_user_func_array(
			array($this, $this->_answerFunc[$question['question_type']]),
			array($index, $fieldName, $question, $readonly));

		$ret .= $this->_error($fieldName);
		$ret .= $this->NetCommonsForm->hidden(
			'QuizAnswer.' . $index . '.0.quiz_question_key',
			array('value' => $index)
		);
		$ret .= $this->NetCommonsForm->hidden('QuizAnswer.' . $index . '.0.id');
		return $ret;
	}

/**
 * 択一選択回答作成
 *
 * @param string $index 回答データのPOST用dataのインデックス値
 * @param string $fieldName フィールド名
 * @param array $question 質問データ
 * @param bool $readonly 読み取り専用
 * @return string 択一選択肢回答のHTML
 */
	public function singleChoice($index, $fieldName, $question, $readonly) {
		if (isset($question['QuizChoice'])) {
			$choices = $question['QuizChoice'];
			$options = $this->_getChoiceOptionElement($choices);
			$setting = array(
				'type' => 'radio',
				'options' => $options,
				'div' => false,
				'legend' => false,
				'label' => false,
				'disabled' => $readonly,
				'error' => false,
				'hiddenField' => false,
			);
			if ($question['is_choice_horizon'] == QuizzesComponent::USES_USE) {
				$setting = Hash::merge($setting, array(
					'inline' => true
				));
			}
			$ret = $this->NetCommonsForm->hidden($fieldName, array('value' => ''));
			$ret .= $this->NetCommonsForm->input($fieldName, $setting);
		}
		return $ret;
	}

/**
 * 複数選択回答作成
 *
 * @param string $index 回答データのPOST用dataのインデックス値
 * @param string $fieldName フィールド名
 * @param array $question 質問データ
 * @param bool $readonly 読み取り専用
 * @return string 複数選択肢回答のHTML
 */
	public function multipleChoice($index, $fieldName, $question, $readonly) {
		$ret = '';
		if (isset($question['QuizChoice'])) {
			$options = $this->_getChoiceOptionElement($question['QuizChoice']);

			$checkboxClass = 'checkbox';
			if ($question['is_choice_horizon'] == QuizzesComponent::USES_USE) {
				$checkboxClass = 'checkbox-inline';
			}

			$ret .= $this->NetCommonsForm->input($fieldName, array(
				'type' => 'select',
				'multiple' => 'checkbox',
				'options' => $options,
				'label' => false,
				'div' => false,
				'class' => $checkboxClass . ' nc-checkbox',
				'disabled' => $readonly,
				'hiddenField' => !$readonly,
				'error' => false,
				//'escape' => false,
			));
		}
		return $ret;
	}

/**
 * 単語回答作成
 *
 * @param string $index  回答データのPOST用dataのインデックス値
 * @param string $fieldName フィールド名
 * @param array $question  質問データ
 * @param bool $readonly 読み取り専用
 * @return string 複数選択肢回答のHTML
 */
	public function singleWord($index, $fieldName, $question, $readonly) {
		$ret = '';
		if ($readonly) {
			$ret = h($this->value($fieldName));
			return $ret;
		}
		$ret = $this->NetCommonsForm->input($fieldName, array(
			'div' => 'form-inline',
			'type' => 'text',
			'label' => false,
			'error' => false,
		));
		return $ret;
	}

/**
 * 長文テキスト回答作成
 *
 * @param string $index 回答データのPOST用dataのインデックス値
 * @param string $fieldName フィールド名
 * @param array $question 質問データ
 * @param bool $readonly 読み取り専用
 * @return string 複数選択肢回答のHTML
 */
	public function textArea($index, $fieldName, $question, $readonly) {
		if ($readonly) {
			$ret = h(nl2br($this->value($fieldName)));
			return $ret;
		}
		$ret = $this->NetCommonsForm->textarea($fieldName, array(
			'div' => 'form-inline',
			'label' => false,
			'class' => 'form-control',
			'rows' => 5,
			'error' => false,
		));
		return $ret;
	}

/**
 * 複数単語回答作成
 *
 * @param string $index 回答データのPOST用dataのインデックス値
 * @param string $fieldName フィールド名
 * @param array $question 質問データ
 * @param bool $readonly 読み取り専用
 * @return string 複数選択肢回答のHTML
 */
	public function multipleWord($index, $fieldName, $question, $readonly) {
		$ret = '';
		$correctCnt = count($question['QuizCorrect']);
		for ($iCnt = 0; $iCnt < $correctCnt; $iCnt++) {
			if ($readonly) {
				$ret .= sprintf('%s : ',
					h($question['QuizCorrect'][$iCnt]['correct_label'])) .
					h($this->value($fieldName . '.' . $iCnt)) . '<br />';
			} else {
				$ret .= $this->NetCommonsForm->input($fieldName . '.' . $iCnt, array(
					'div' => 'form-inline',
					'type' => 'text',
					'label' => sprintf('%s : ', h($question['QuizCorrect'][$iCnt]['correct_label'])),
					'error' => false,
				));
			}
		}
		return $ret;
	}

/**
 * エラーメッセージ表示要素作成
 *
 * @param string $fieldName フィールド名
 * @return string エラーメッセージ表示要素のHTML
 */
	protected function _error($fieldName) {
		$output = '<div class="has-error">';
		$output .= $this->NetCommonsForm->error($fieldName, null, array('class' => 'help-block'));
		$output .= '</div>';
		return $output;
	}

/**
 * 選択肢要素作成
 *
 * @param array $choices 選択肢データ
 * @return string 選択肢要素のHTML
 */
	protected function _getChoiceOptionElement($choices) {
		$ret = array();
		foreach ($choices as $choice) {
			$ret[$choice['choice_label']] = $choice['choice_label'];
		}
		return $ret;
	}
}
