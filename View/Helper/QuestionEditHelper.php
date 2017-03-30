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
		'NetCommons.Token',
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

		$ret .= '<label class="col-xs-2 control-label">' . $title;
		if (isset($options['required']) && $options['required'] == true) {
			$ret .= $this->_View->element('NetCommons.required');
		}
		$ret .= '</label><div class="col-xs-10">';

		$type = $options['type'];
		if ($this->_View->viewVars['isPublished']) {
			$options = Hash::merge($options, array('disabled' => true));
		}

		$options = Hash::merge(array('div' => false, 'label' => false), $options);
		if ($type == 'wysiwyg') {
			if ($this->_View->viewVars['isPublished']) {
				$ret .= '<div class="well well-sm quiz-edit-disabled-well" ng-bind-html="';
				$ret .= $options['ng-model'] . ' | ncHtmlContent"></div>';
			} else {
				$ret .= $this->NetCommonsForm->wysiwyg($fieldName, $options);
			}
		} elseif ($type == 'checkbox') {
			$options = Hash::merge($options, array('label' => $label));
			$ret .= $this->NetCommonsForm->checkbox($fieldName, $options);
		} else {
			$ret .= $this->NetCommonsForm->input($fieldName, $options);
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
 * 小テスト属性設定作成
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
			// この記述でないと チェックON,OFFが正常に動作しない。
			'ng-false-value' => '"0"',
			'ng-true-value' => '"1"'
			),
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
 * 小テスト期間設定作成
 *
 * @param string $fieldName フィールド名
 * @param array $options オプション
 * @param string $help 追加説明文
 * @return string HTML
 */
	public function quizAttributeDatetime($fieldName, $options, $help = '') {
		//$ngModel = 'quiz.quiz.' . Inflector::variable($fieldName);

		$defaultOptions = array(
			'type' => 'datetime',
			'id' => $fieldName,
			//'ng-model' => $ngModel,
		);
		$options = Hash::merge($defaultOptions, $options);
		if (isset($options['min']) && isset($options['max'])) {
			$min = 'NetCommonsFormDatetimePickerModel_Quiz_' . $options['min'];
			$max = 'NetCommonsFormDatetimePickerModel_Quiz_' . $options['max'];
			$options = Hash::merge($options, array(
				'ng-focus' => 'setMinMaxDate($event, \'' . $min . '\', \'' . $max . '\')',
			));
		}

		$ret = $this->NetCommonsForm->input($fieldName, $options);
		if (!empty($help)) {
			$ret .= '<span class="help-block">' . $help . '</span>';
		}
		return $ret;
	}
/**
 * quizGetFinallySubmit
 *
 * 小テストは質問編集画面では分割送信をする
 * 分割送信後、最終POSTをする時に使用するFormを作成する
 *
 * @param array $postUrl POST先URL情報
 * @return string HTML
 */
	public function quizGetFinallySubmit($postUrl) {
		$html = '';
		$html .= $this->NetCommonsForm->create('QuizQuestion',
			Hash::merge(array('id' => 'finallySubmitForm'), $postUrl)
		);
		$html .= $this->NetCommonsForm->hidden('Frame.id');
		$html .= $this->NetCommonsForm->hidden('Block.id');
		$html .= $this->NetCommonsForm->hidden('Quiz.key');
		$this->NetCommonsForm->unlockField('QuizPage');
		$html .= $this->NetCommonsForm->end();
		return $html;
	}

/**
 * getJsPostData
 *
 * 小テストは分割送信をAjaxで行う
 * AjaxでPostを行うときにtoken含みの配列を取得する
 *
 * @param string $quizKey 小テストキー（Postデータの一つとして必要）
 * @param string $ajaxPostUrl Post先URL（セッション保存キーが含まれるためコントローラーからもらわないとわからない）
 * @return array
 */
	public function getJsPostData($quizKey, $ajaxPostUrl) {
		$postData = array(
			'Frame' => array('id' => Current::read('Frame.id')),
			'Block' => array('id' => Current::read('Block.id')),
			'Quiz' => array('key' => $quizKey),
		);
		$tokenFields = Hash::flatten($postData);
		$hiddenFields = $tokenFields;
		$hiddenFields = array_keys($hiddenFields);
		$this->Token->unlockField('QuizPage');

		$tokens = $this->Token->getToken('Quiz', $ajaxPostUrl, $tokenFields, $hiddenFields);
		$postData += $tokens;
		return $postData;
	}
}
