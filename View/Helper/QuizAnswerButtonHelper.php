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
		'NetCommons.NetCommonsHtml',
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
		// status != 公開状態 つまり編集者が見ている場合は「テスト」
		//
		// まだ回答できる状態だったら
		// 回答してもしてなくても「回答」
		//
		// 回答期間外はdisabled
		//
		// 公開期間中
		// 繰り返しの回答を許さない = Disabled
		// 合格まで繰り返しを許す = 合格前＝able　合格後＝Disabled
		//
		// 状態がdisableのもので、かつ、解答していないとき[未回答]
		// 状態がdisableで、解答をしているときは[終了]

		$key = $quiz['Quiz']['key'];

		// 編集権限がない人が閲覧しているとき、未公開小テストはFindされていないので対策する必要はない

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
		}

		$url = NetCommonsUrl::actionUrl(array(
			'controller' => 'quiz_answers',
			'action' => 'view',
			Current::read('Block.id'),
			$key,
			'frame_id' => Current::read('Frame.id'),
		));

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

		$answerButtonLabel = $this->_getLabel($quiz, $answerButtonDisabled);

		return sprintf($buttonStr, $answerButtonClass, '', $answerButtonDisabled, $url, $answerButtonLabel);
	}
/**
 * _getLabel 回答済み 回答 終了 ラベルの取得
 *
 * @param array $quiz 小テスト
 * @param string $answerButtonDisabled ボタン操作可能状態
 * @return string
 */
	protected function _getLabel($quiz, $answerButtonDisabled) {
		// 操作可能状態だったら無条件に「回答」だけのボタン
		if ($answerButtonDisabled == '') {
			return __d('quizzes', 'Answer');
		}
		// 操作不可能のときだけ分岐
		$key = $quiz['Quiz']['key'];
		// 未回答
		if (! in_array($key, $this->_View->viewVars['ownAnsweredKeys'])) {
			return __d('quizzes', 'Unanswered');
		}
		return __d('quizzes', 'Finished');
	}

}
