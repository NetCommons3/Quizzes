<?php
/**
 * Question Edit Helper
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator Co., Ltd. <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
App::uses('AppHelper', 'View/Helper');
/**
 * Question edit Helper
 *
 * @author Allcreator Co., Ltd. <info@allcreator.net>
 * @package NetCommons\Quizzes\View\Helper
 */
class QuestionEditHelper extends AppHelper {

/**
 * Other helpers used by FormHelper
 *
 * @var array
 */
	public $helpers = array(
		'NetCommons.NetCommonsForm',
		'NetCommons.NetCommonsHtml',
		'Form'
	);

/**
 * 質問属性設定作成
 *
 * @param string $fieldName フィールド名
 * @param string $title 見出しラベル
 * @param array $options INPUT要素に与えるオプション属性
 * @param string $label checkboxの時のラベル
 * @return string HTML
 */
	public function questionInput($fieldName, $title, $options, $label = '') {
		if (isset($options['ng-model'])) {
			$errorMsgModelName = $this->quizGetNgErrorModelName($options['ng-model']);
			$ret = '<div class="row form-group" ng-class="{\'has-error\':' . $errorMsgModelName . '}">';
		} else {
			$ret = '<div class="row form-group">';
		}

		$ret .= '<label class="col-sm-2 control-label">' . $title;
		if (isset($options['required']) && $options['required'] == true) {
			$ret .= $this->_View->element('NetCommons.required');
		}
		$ret .= '</label><div class="col-sm-10">';

		$type = $options['type'];
		if ($this->_View->viewVars['isPublished']) {
			$disabled = 'disabled';
			$options = Hash::remove($options, 'ui-tinymce');
		} else {
			$disabled = '';
		}

		if ($type == 'checkbox') {
			$ret .= '<div class="checkbox ' . $disabled . '"><label>';
		}

		$options = Hash::merge(array('div' => false, 'label' => false), $options);
		if ($type == 'wysiwyg') {
			$ret .= $this->NetCommonsForm->wysiwyg($fieldName, $options);
		} else {
			$ret .= $this->NetCommonsForm->input($fieldName, $options);
		}

		if ($type == 'checkbox') {
			$ret .= $label . '</label></div>';
		}

		if (isset($options['ng-model'])) {
			$ret .= $this->quizNgError($options['ng-model']);
		}

		$ret .= '</div></div>';
		return $ret;
	}
/**
 * Angularモデルに対するエラーメッセージモデル名取得
 *
 * @param string $ngModelName Angularモデル名
 * @return string エラーメッセージ保持するモデル名
 */
	public function quizGetNgErrorModelName($ngModelName) {
		$modelNames = explode('.', $ngModelName);
		$errorMsgModelName = $modelNames[0] . '.errorMessages.' . $modelNames[1];
		return $errorMsgModelName;
	}
/**
 * Angularモデルに対するエラーメッセージ表示
 *
 * @param string $ngModelName Angularモデル名
 * @return string エラーメッセージ表示HTML
 */
	public function quizNgError($ngModelName) {
		$errorMsgModelName = $this->quizGetNgErrorModelName($ngModelName);
		$ret = '<div class="has-error" ng-if="' . $errorMsgModelName . '">';
		$ret .= '<div class="help-block" ng-repeat="errorMessage in ' . $errorMsgModelName . '">';
		$ret .= '{{errorMessage}}</div></div>';
		return $ret;
	}

/**
 * ラジオボタン属性設定作成
 *
 * @param string $fieldName フィールド名
 * @param string $title 見出しラベル
 * @param array $options INPUT要素に与えるオプション属性
 * @param string $attributes 属性
 * @return string HTML
 */
	public function quizRadio($fieldName, $title, $options, $attributes = array()) {
		$ngModel = 'quiz.' . Inflector::variable($fieldName);
		$ret = '<div class="row form-group quiz-group"><label>';
		$ret .= $title;
		$ret .= '</label>';
		$ret .= $this->NetCommonsForm->input($fieldName,
			array('type' => 'radio',
				'options' => $options,
				'legend' => false,
				'div' => false,
				'class' => '',
				'label' => false,
				'before' => '<div class="radio"><label>',
				'separator' => '</label></div><div class="radio"><label>',
				'after' => '</label></div>',
				'ng-model' => $ngModel
		));
		$ret .= '</div>';
		return $ret;
	}

/**
 * アンケート属性設定作成
 *
 * @param string $fieldName フィールド名
 * @param string $label checkboxの時のラベル
 * @param array $options INPUT要素に与えるオプション属性
 * @param string $help 追加説明文
 * @return string HTML
 */
	public function quizAttributeCheckbox($fieldName, $label, $options = array(), $help = '') {
		$ngModel = 'quiz.quiz.' . Inflector::variable($fieldName);
		$ret = '<div class=" checkbox"><label>';
		$options = Hash::merge(array(
			'type' => 'checkbox',
			'div' => false,
			'label' => false,
			'class' => '',
			'error' => false,
			'ng-model' => $ngModel,
			'ng-checked' => $ngModel . '==' . QuizzesComponent::USES_USE),
			$options
		);

		$ret .= $this->NetCommonsForm->input($fieldName, $options);
		$ret .= $label;
		if (!empty($help)) {
			$ret .= '<span class="help-block">' . $help . '</span>';
		}
		$ret .= '</label>';
		$ret .= $this->NetCommonsForm->error($fieldName, null, array('class' => 'help-block'));
		$ret .= '</div>';
		return $ret;
	}

/**
 * アンケート期間設定作成
 *
 * @param string $fieldName フィールド名
 * @param string $label checkboxの時のラベル
 * @param array $minMax 日時指定の範囲がある場合のmin, max
 * @param string $help 追加説明文
 * @return string HTML
 */
	public function quizAttributeDatetime($fieldName, $label, $minMax = array(), $help = '') {
		$ngModel = 'quiz.quiz.' . Inflector::variable($fieldName);

		$options = array('type' => 'text',
			'id' => $fieldName,
			'class' => 'form-control',
			'placeholder' => 'yyyy-mm-dd',
			'show-weeks' => 'false',
			'ng-model' => $ngModel,
			'datetimepicker',
			'datetimepicker-options' => "{format:'YYYY-MM-DD HH:mm'}",
			'show-meridian' => 'false',
			'label' => $label,
		);
		if (! empty($minMax)) {
			$min = $minMax['min'];
			$max = $minMax['max'];
			$options = Hash::merge($options, array(
				'min' => $min,
				'max' => $max,
				'ng-focus' => 'setMinMaxDate($event, \'' . $min . '\', \'' . $max . '\')',
			));
		}

		$ret = $this->NetCommonsForm->input($fieldName, $options);
		if (!empty($help)) {
			$ret .= '<span class="help-block">' . $help . '</span>';
		}
		return $ret;
	}
}
