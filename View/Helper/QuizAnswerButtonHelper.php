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
 * Quizzes Answer Button Helper
 *
 * @author Allcreator Co., Ltd. <info@allcreator.net>
 * @package NetCommons\Quizzes\View\Helper
 */
class QuizAnswerButtonHelper extends AppHelper {

/**
 * Other helpers used by FormHelper
 *
 * @var array
 */
	public $helpers = array(
		'NetCommonsHtml',
		'Html'
	);

/**
 * getAnswerButtons 回答済み 回答する テストのボタン表示
 *
 * @param array $quiz 小テスト
 * @return string
 */
	public function getAnswerButtons($quiz) {
		//
		//回答ボタンの(回答済み|回答する|テスト)の決定
		//
		// satus != 公開状態 つまり編集者が見ている場合は「テスト」
		//
		// 公開状態の場合が枝分かれする
		// 公開時期にマッチしていない = 回答前＝回答する（disabled） 回答後＝回答済み（disabled）
		//
		// 公開期間中
		// 繰り返しの回答を許さない = 回答前＝回答する　回答後＝回答済み（Disabled）
		// 合格まで繰り返しを許す = 合格前＝回答する　合格後＝回答済み（Disabled）
		// 繰り返しの回答を許す = いずれの状態でも「回答する」

		$key = $quiz['Quiz']['key'];

		// 編集権限がない人が閲覧しているとき、未公開アンケートはFindされていないので対策する必要はない
		// ボタン表示ができるかできないか
		// 編集権限がないのに公開状態じゃないアンケートの場合はボタンを表示しない
		//
		//if ($questionnaire['Questionnaire']['status'] != WorkflowComponent::STATUS_PUBLISHED && !$editable) {
		//	return '';
		//}

		$buttonStr = '<a class="btn btn-%s quiz-listbtn %s" %s href="%s">%s</a>';

		// ボタンの色
		// ボタンのラベル
		if ($quiz['Quiz']['status'] != WorkflowComponent::STATUS_PUBLISHED) {
			$answerButtonClass = 'info';
			$answerButtonLabel = __d('quizzes', 'Test');
			$url = NetCommonsUrl::actionUrl(array(
				'controller' => 'quiz_answers',
				'action' => 'test_mode',
				Current::read('Block.id'),
				$key,
				'frame_id' => Current::read('Frame.id'),
			));
			return sprintf($buttonStr, $answerButtonClass, '', '', $url, $answerButtonLabel);
		} else {
			$url = NetCommonsUrl::actionUrl(array(
				'controller' => 'quiz_answers',
				'action' => 'view',
				Current::read('Block.id'),
				$key,
				'frame_id' => Current::read('Frame.id'),
			));
		}

		// 何事もなければ回答可能のボタン
		$answerButtonLabel = __d('quizzes', 'Answer');
		$answerButtonClass = 'success';
		$answerButtonDisabled = '';

		// 操作できるかできないかの決定
		// 期間外だったら操作不可能
		if ($quiz['Quiz']['period_range_stat'] != QuizzesComponent::QUIZ_PERIOD_STAT_IN) {
			$answerButtonClass = 'default';
			$answerButtonDisabled = 'disabled';
		}
		// 繰り返し回答不可で回答済なら操作不可能
		if (in_array($key, $this->_View->viewVars['ownAnsweredKeys'])
				&& $quiz['Quiz']['is_repeat_allow'] == QuizzesComponent::PERMISSION_NOT_PERMIT) {
			$answerButtonClass = 'default';
			$answerButtonDisabled = 'disabled';
		}
		// 合格まで繰り返し可能で合格後なら操作不可能
		if (in_array($key, $this->_View->viewVars['passQuizKeys'])
			&& $quiz['Quiz']['is_repeat_until_passing'] == QuizzesComponent::USES_USE) {
			$answerButtonClass = 'default';
			$answerButtonDisabled = 'disabled';
		}

		// ラベル名の決定
		if ($quiz['Quiz']['period_range_stat'] == QuizzesComponent::QUIZ_PERIOD_STAT_BEFORE) {
			// 未公開
			$answerButtonLabel = __d('quizzes', 'Unpublished');
		}
		if (in_array($key, $this->_View->viewVars['ownAnsweredKeys'])) {
			// 回答済み
			$answerButtonLabel = __d('quizzes', 'Finished');
		}

		return sprintf($buttonStr, $answerButtonClass, '', $answerButtonDisabled, $url, $answerButtonLabel);
	}

}
