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
class QuizStatusLabelHelper extends AppHelper {

/**
 * Status label
 *
 * @param array $quiz quiz
 * @return string
 */
	public function statusLabel($quiz) {
		$status = $quiz['Quiz']['status'];
		//初期値セット
		$lblColor = 'danger';
		$lblMsg = __d('quizzes', 'Undefined');

		if ($status == WorkflowComponent::STATUS_IN_DRAFT) {
			//一時保存中
			$lblColor = 'info';
			$lblMsg = __d('net_commons', 'Temporary');
		} elseif ($status == WorkflowComponent::STATUS_APPROVED) {
			//承認待ち
			$lblColor = 'warning';
			$lblMsg = __d('net_commons', 'Approving');
		} elseif ($status == WorkflowComponent::STATUS_DISAPPROVED) {
			//差し戻し
			$lblColor = 'danger';
			$lblMsg = __d('net_commons', 'Disapproving');
		} else {
			if ($quiz['Quiz']['period_range_stat'] == QuizzesComponent::QUIZ_PERIOD_STAT_BEFORE) {
				//未実施
				$lblColor = 'default';
				$lblMsg = __d('quizzes', 'Before public');
			} elseif ($quiz['Quiz']['period_range_stat'] == QuizzesComponent::QUIZ_PERIOD_STAT_END) {
				//終了
				$lblColor = 'default';
				$lblMsg = __d('quizzes', 'End');
			} else {
				$lblMsg = '';
			}
		}
		if ($lblMsg) {
			return '<span  class="label label-' . $lblColor . '">' . $lblMsg . '</span>';
		}
		return '';
	}

/**
 * Status label for management widget
 *
 * @param array $quiz quiz data
 * @return string
 */
	public function statusLabelManagementWidget($quiz) {
		$label = $this->statusLabel($quiz);
		if ($label == '') {
			$label = __d('net_commons', 'Published');
		}
		return $label;
	}
}
