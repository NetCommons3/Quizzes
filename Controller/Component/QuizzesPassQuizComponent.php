<?php
/**
 * QuizzesPassQuiz Component
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('Component', 'Controller');

/**
 * QuizzesPassQuizComponent
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Questionnaires\Controller
 */
class QuizzesPassQuizComponent extends Component {

/**
 * Passed quiz keys
 *
 * 合格済み小テストキー配列
 *
 * @var array
 */
	private $__passQuizKeys = null;

/**
 * 合格済み小テストリストを取得する
 *
 * @return Answered Quiz keys list
 */
	public function getPassQuizKeys() {
		if (isset($this->__passQuizKeys)) {
			return $this->__passQuizKeys;
		}

		$this->__passQuizKeys = array();

		$userId = Current::read('User.id');
		if (empty($userId)) {
			$session = $this->_Collection->load('Session');
			$blockId = Current::read('Block.id');
			$passQuizKeys = $session->read('Quizzes.passQuizKeys.' . $blockId);
			if (isset($passQuizKeys)) {
				$this->__passQuizKeys = explode(',', $passQuizKeys);
			}

			return $this->__passQuizKeys;
		}

		$answerSummary = ClassRegistry::init('Quizzes.QuizAnswerSummary');
		$conditions = array(
			'user_id' => Current::read('User.id'),
			//'test_status' => QuizzesComponent::TEST_ANSWER_STATUS_PEFORM,
		);
		$passQuizKeys = $answerSummary->getPassedQuizKeys($conditions);
		$this->__passQuizKeys = array_values($passQuizKeys);	// idの使用を防ぐ（いらない？）

		return $this->__passQuizKeys;
	}

/**
 * 小テスト合格済みかどうかを返す
 *
 * @param string $quizKey 合格済かどうかを判定する小テストキー
 * @return bool
 */
	public function checkPassQuizKeys($quizKey) {
		// まだ回答済データが初期状態のときはまずは確保
		if ($this->__passQuizKeys === null) {
			$this->getPassQuizKeys();
		}
		if (in_array($quizKey, $this->__passQuizKeys)) {
			return true;
		}
		return false;
	}

/**
 * セッションの合格済み小テストリストに新しい小テストを追加する
 *
 * @param string $quizKey 合格済に追加する小テストキー
 * @return void
 */
	public function savePassQuizKeys($quizKey) {
		// まだ回答済データが初期状態のときはまずは確保
		if ($this->__passQuizKeys === null) {
			$this->getPassQuizKeys();
		}
		// 回答済み小テスト配列に追加
		$this->__passQuizKeys[] = $quizKey;
		// ログイン状態の人の場合はこれ以上の処理は不要
		$userId = Current::read('User.id');
		if (! empty($userId)) {
			return;
		}
		// 未ログインの人の場合はセッションに書いておく
		$session = $this->_Collection->load('Session');
		$blockId = Current::read('Block.id');
		$session->write('Quizzes.passQuizKeys.' . $blockId, implode(',', $this->getPassQuizKeys()));
	}

}
