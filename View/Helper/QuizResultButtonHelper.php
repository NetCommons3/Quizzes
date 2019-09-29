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
 * Quizzes ResultButton Helper
 *
 * @author Allcreator Co., Ltd. <info@allcreator.net>
 * @package NetCommons\Quizzes\View\Helper
 */
class QuizResultButtonHelper extends AppHelper {

/**
 * Other helpers used by FormHelper
 *
 * @var array
 */
	public $helpers = array(
		'NetCommons.NetCommonsHtml',
		'Workflow.Workflow',
		'Quizzes.QuizGradeLink',
		'Html'
	);

/**
 * getResultButtons 成績ボタン表示
 * 基本的に常に表示される
 * 回答期間じゃなくても成績はいつでも見られるようにする
 * まだ回答してなくて見に行っても、データがないだけだから
 *
 * @param array $quiz 小テスト
 * @param array $options option
 * @return string
 */
	public function getResultButtons($quiz, $options = array()) {
		//
		// 成績ボタン
		// (not editor)でかつ
		// 小テスト自体が公開状態にないまたはまだ回答日がきてないときは表示しない
		// これでいくと一般が作成したときの小テスト、承認待ちのあいだは見に行けなくなってしまう
		// なので、やはりEditableかどうかに判断を変更することにした
		$canGrade = $this->QuizGradeLink->canGrade($quiz);
		$canEdit = $this->Workflow->canEdit('Quiz', $quiz);
		if (! $canEdit &&
			($quiz['Quiz']['status'] != WorkflowComponent::STATUS_PUBLISHED ||
			$quiz['Quiz']['period_range_stat'] == QuizzesComponent::QUIZ_PERIOD_STAT_BEFORE)) {
			return '';
		}

		$key = $quiz['Quiz']['key'];

		//　編集できる人かどうかで見に行くアクションが異なる
		// 総合情報（index）は公開権限の人しか見ることができません
		if ($canGrade) {
			$action = 'index';
		} else {
			$action = 'view';
		}
		list($title, $icon, $btnClass) = $this->_getBtnAttributes($options);
		$url = NetCommonsUrl::actionUrl(array(
			'controller' => 'quiz_result',
			'action' => $action,
			Current::read('Block.id'),
			$key,
			'frame_id' => Current::read('Frame.id'),
		));
		$html = $this->NetCommonsHtml->link($icon . $title,
			$url, array(
			'class' => $btnClass,
			'escape' => false
		));

		return $html;
	}
/**
 * _getBtnAttributes ボタン属性整理作成
 *
 * @param array $options option
 * @return array
 */
	protected function _getBtnAttributes($options) {
		$btnClass = 'btn btn-default quiz-listbtn';
		if (isset($options['class'])) {
			$btnClass = 'btn btn-' . $options['class'];
		}
		if (isset($options['size'])) {
			$btnClass .= ' btn-' . $options['size'];
		}

		$title = '';
		if (isset($options['title'])) {
			$title = $options['title'];
		}
		$icon = '';
		if (isset($options['icon'])) {
			$icon = '<span class="glyphicon glyphicon-' . $options['icon'] . '" aria-hidden="true"></span>';
		}
		return array($title, $icon, $btnClass);
	}
}
