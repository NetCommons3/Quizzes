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
		// 小テスト自体が公開状態にない(not editor)
		// 未回答＆回答期間内　　　　　　　集計ボタン（disabled）
		$key = $quiz['Quiz']['key'];

		$disabled = '';

		//　編集できる人かどうかで見に行くアクションが異なる
		// 総合情報（index）は一般の人は見ることができません
		if ($this->_View->Workflow->canEdit('Quiz', $quiz)) {
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
			'class' => $btnClass . ' ' . $disabled,
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
		$btnClass = 'btn btn-default questionnaire-listbtn';
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
