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
 * 正解表示作成
 *
 * @param string $question 設問
 * @return string HTML
 */
	public function questionCorrect($question) {
		$ret = '<blockquote><div class="row form-group">';

		// DEBUG CODE FUJI とりあえず書いてみてるだけ
		$options = array('1' => '正解１', '2' => '正解２', '3' => '正解３');
		$ret = $this->Form->input('QuizzesAnswer.value', array(
			'type' => 'radio',
			'div' => 'radio well',
			'options' => $options,
			'value' => '2',
			'legend' => false,
			'label' => false,
			'before' => '<div class="radio"><label>',
			'separator' => '</label></div><div class="radio"><label>',
			'after' => '</label></div>',
			'disabled' => true,
			'error' => false,
		));
		$ret .= $this->NetCommonsForm->wysiwyg('commentary', array('label' => __d('quizzes', 'commentary')));
		$ret .= '</div></blockquote>';
		return $ret;
	}
}
