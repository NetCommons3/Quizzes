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
		'NetCommonsForm',
		'NetCommonsHtml',
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
		$ret = '<div class="row form-group"><label	class="col-sm-2 control-label">' . $title;
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
		if ($type == 'wysiswyg') {
			$ret .= $this->NetCommonsForm->wysiwyg($fieldName, $options);
		} else {
			$ret .= $this->NetCommonsForm->input($fieldName, $options);
		}

		if ($type == 'checkbox') {
			$ret .= $label . '</label></div>';
		}
		$ret .= '</div></div>';
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
		$ret .= '<div class="has-error">' . $this->NetCommonsForm->error($fieldName, null, array('class' => 'help-block')) . '</div>';
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
			'div' => false);
		if (! empty($minMax)) {
			$min = $minMax['min'];
			$max = $minMax['max'];
			$options = Hash::merge($options, array(
				'min' => $min,
				'max' => $max,
				'ng-focus' => 'setMinMaxDate($event, \'' . $min . '\', \'' . $max . '\')',
			));
		}

		$ret = '<div class="form-group "><div class="input-group">';
		$ret .= $this->NetCommonsForm->input($fieldName, $options);
		if (!empty($help)) {
			$ret .= '<span class="help-block">' . $help . '</span>';
		}
		$ret .= '<div class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></div></div></div>';
		return $ret;
	}

/**
 * フロー見出し作成
 *
 * @param int $current 現在の手順位置
 * @return string HTML
 */
	public function getEditFlowChart($current) {
		$steps = array(
			1 => array('label' => __d('quizzes', 'Set questions'), 'action' => 'edit_question'),
			2 => array('label' => __d('quizzes', 'Set quiz'), 'action' => 'edit')
		);
		$stepCount = count($steps);
		$stepWidth = 'style="width: ' . 100 / $stepCount . '%;"';
		$check = $steps;

		$ret = '<div class="progress quiz-steps">';
		foreach ($steps as $index => $stepContent) {
			$badge = '<span class="badge">' . $index . '</span>';
			if ($index == $current) {
				$currentClass = 'progress-bar';
				$badge = '<span class="btn-primary">' . $badge . '</span>';
			} else {
				$currentClass = '';
			}
			$ret .= '<div class="' . $currentClass . ' quiz-step-item"' . $stepWidth . '>';
			$ret .= '<span class="quiz-step-item-title">' . $badge;
			if ($index != $current) {
				//FUJI
				$routes = explode('/', str_replace('http://', '', Router::reverse($this->request, true)));
				$routes[3] = $stepContent['action'];
				array_shift($routes);
				$url = $this->NetCommonsHtml->url('/' . implode('/', $routes));
				$ret .= '<a href="' . $url . '">' . $stepContent['label'] . '</a>';
			} else {
				$ret .= $stepContent['label'];
			}
			$ret .= '</span>';
			if (next($check)) {
				$ret .= '<span class="glyphicon glyphicon-chevron-right"></span>';
			}
			$ret .= '</div>';
		}
		$ret .= '</div>';
		return $ret;
	}
}
