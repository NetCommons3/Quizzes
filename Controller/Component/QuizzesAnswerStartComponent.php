<?php
/**
 * QuizzesAnswerStart Component
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('Component', 'Controller');

/**
 * QuizzesAnswerStartComponent
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Questionnaires\Controller
 */
class QuizzesAnswerStartComponent extends Component {

/**
 * 指定された小テストのIDを回答スタートIDとしてセッションに記録
 *
 * @param string $quizKey 回答中の小テストキー
 * @return void
 */
	public function saveStartQuizOfThisUser($quizKey) {
		// セッションに始まったことを記載
		$session = $this->_Collection->load('Session');
		$session->write('Quizzes.startQuiz.' . $quizKey, true);
	}

/**
 * セッションから指定された小テストのスタートIDを削除
 *
 * @param string $quizKey 回答中の小テストキー
 * @return void
 */
	public function deleteStartQuizOfThisUser($quizKey = null) {
		$session = $this->_Collection->load('Session');
		if (! $quizKey) {
			$session->delete('Quizzes.startQuiz');
		} else {
			$session->delete('Quizzes.startQuiz.' . $quizKey);
		}
	}

/**
 * 小テスト回答開始済みかどうかを返す
 *
 * @param string $quizKey 小テストキー
 * @return bool
 */
	public function checkStartedQuizKeys($quizKey) {
		$session = $this->_Collection->load('Session');
		return $session->check('Quizzes.startQuiz.' . $quizKey);
	}
}
