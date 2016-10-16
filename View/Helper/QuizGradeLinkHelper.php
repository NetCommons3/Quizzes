<?php
/**
 * Quizzes App Helper
 *
 * @author Allcreator Co., Ltd. <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('AppHelper', 'View/Helper');

/**
 * Quizzes Status Label Helper
 *
 * @author Allcreator Co., Ltd. <info@allcreator.net>
 * @package NetCommons\Quizzes\View\Helper
 */
class QuizGradeLinkHelper extends AppHelper {

/**
 * Other helpers used by FormHelper
 *
 * @var array
 */
	public $helpers = array(
		'Workflow.Workflow',
		'NetCommons.NetCommonsForm',
		'NetCommons.NetCommonsHtml',
		'Form'
	);

/**
 * Goto Grade Page Link
 *
 * @param array $quiz quiz
 * @return string
 */
	public function getGradePageLink($quiz) {
		if ($this->canGrade($quiz)) {
			$gradingUrl = NetCommonsUrl::actionUrl(array(
				'controller' => 'quiz_result',
				'action' => 'index',
				Current::read('Block.id'),
				$quiz['Quiz']['key'],
				'frame_id' => Current::read('Frame.id'),
			));
		} else {
			$ownAnswere = $this->_View->viewVars['ownAnswerdSummaryMap'];
			$gradingUrl = NetCommonsUrl::actionUrl(array(
				'controller' => 'quiz_result',
				'action' => 'view',
				Current::read('Block.id'),
				$quiz['Quiz']['key'],
				$ownAnswere[$quiz['Quiz']['key']],
				'frame_id' => Current::read('Frame.id'),
			));
		}
		// ※未採点のデータがあります
		$text = '<span class="text-danger">' .
			__d('quizzes', '! There is a non-scoring of data') .
			'</span>';

		$ret = $this->NetCommonsHtml->link($text, $gradingUrl, array('escape' => false));
		return $ret;
	}
/**
 * canGrade
 *
 * 採点できるかどうか
 *
 * @param array $quiz 対象となる小テスト
 * @return bool
 */
	public function canGrade($quiz) {
		return Current::permission('block_editable');
	}
}
