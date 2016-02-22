<?php
/**
 * QuizzesOwnAnswerQuiz Component
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('Component', 'Controller');

/**
 * QuizzesOwnAnswerComponent
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Questionnaires\Controller
 */
class QuizzesOwnAnswerQuizComponent extends Component {

/**
 * Answered quiz keys
 *
 * 回答済み小テストキー配列
 *
 * @var array
 */
	private $__ownAnsweredKeys = null;

/**
 * 指定された小テストに該当する回答中小テストのサマリを取得する
 *
 * @param string $quizKey 回答済に追加する小テストキー
 * @return progressive Answer Summary id list
 */
	public function getProgressiveSummaryOfThisUser($quizKey) {
		// 戻り値初期化
		$summary = false;
		$answerSummary = ClassRegistry::init('Quizzes.QuizAnswerSummary');
		// 未ログインの人の場合はセッションにある回答中データを参照する
		if (empty(Current::read('User.id'))) {
			$session = $this->_Collection->load('Session');
			$summaryId = $session->read('Quizzes.progressiveSummary.' . $quizKey);
			if ($summaryId) {
				$summary = $answerSummary->findById($summaryId);
			}
			return $summary;
		}
		// ログインユーザーはDBから探す
		$conditions = array(
			'answer_status != ' => QuizzesComponent::ACTION_ACT,
			'quiz_key' => $quizKey,
			'user_id' => Current::read('User.id'),
		);
		$summary = $answerSummary->find('first', array(
			'conditions' => $conditions,
			'order' => 'QuizAnswerSummary.created ASC'	// 最も古いものを一つ選ぶ
		));
		return $summary;
	}
/**
 * 指定された小テストに対応する回答中サマリを作成
 *
 * @param array $quiz 小テスト
 * @return progressive Answer Summary data
 */
	public function forceGetProgressiveAnswerSummary($quiz) {
		$summary = $this->getProgressiveSummaryOfThisUser($quiz['Quiz']['key']);
		if (! $summary) {
			$answerSummary = ClassRegistry::init('Quizzes.QuizAnswerSummary');
			$session = $this->_Collection->load('Session');
			$summary = $answerSummary->forceGetProgressiveAnswerSummary($quiz, Current::read('User.id'), $session->id());
			if ($summary) {
				$this->saveProgressiveSummaryOfThisUser($quiz['Quiz']['key'], $summary['QuizAnswerSummary']['id']);
			}
		}

		return $summary;
	}

/**
 * 指定された小テストのサマリIDを回答中サマリIDとしてセッションに記録
 *
 * @param string $quizKey 回答中の小テストキー
 * @param int $summaryId 回答中のサマリのID
 * @return void
 */
	public function saveProgressiveSummaryOfThisUser($quizKey, $summaryId) {
		$session = $this->_Collection->load('Session');
		$session->write('Quizzes.progressiveSummary.' . $quizKey, $summaryId);
	}
/**
 * セッションから指定された小テストの回答中サマリIDを削除
 *
 * @param string $quizKey 回答中の小テストキー
 * @return void
 */
	public function deleteProgressiveSummaryOfThisUser($quizKey) {
		$session = $this->_Collection->load('Session');
		$session->delete('Quizzes.progressiveSummary.' . $quizKey);
	}

/**
 * 回答済み小テストリストを取得する
 *
 * @return Answered Quiz keys list
 */
	public function getOwnAnsweredKeys() {
		if (isset($this->__ownAnsweredKeys)) {
			return $this->__ownAnsweredKeys;
		}

		$this->__ownAnsweredKeys = array();

		if (empty(Current::read('User.id'))) {
			$session = $this->_Collection->load('Session');
			$blockId = Current::read('Block.id');
			$ownAnsweredKeys = $session->read('Quizzes.ownAnsweredQuizKeys.' . $blockId);
			if (isset($ownAnsweredKeys)) {
				$this->__ownAnsweredKeys = explode(',', $ownAnsweredKeys);
			}

			return $this->__ownAnsweredKeys;
		}

		$answerSummary = ClassRegistry::init('Quizzes.QuizAnswerSummary');
		$conditions = array(
			'user_id' => Current::read('User.id'),
			'answer_status' => QuizzesComponent::ACTION_ACT,
			'test_status' => QuizzesComponent::TEST_ANSWER_STATUS_PEFORM,
			'answer_number' => 1
		);
		$ownAnsweredKeys = $answerSummary->find(
			'list',
			array(
				'conditions' => $conditions,
				'fields' => array('QuizAnswerSummary.quiz_key'),
				'recursive' => -1
			)
		);
		$this->__ownAnsweredKeys = array_values($ownAnsweredKeys);	// idの使用を防ぐ（いらない？）

		return $this->__ownAnsweredKeys;
	}
/**
 * 小テスト回答済みかどうかを返す
 *
 * @param string $quizKey 回答済に追加する小テストキー
 * @return bool
 */
	public function checkOwnAnsweredKeys($quizKey) {
		// まだ回答済データが初期状態のときはまずは確保
		if ($this->__ownAnsweredKeys === null) {
			$this->getOwnAnsweredKeys();
		}
		if (in_array($quizKey, $this->__ownAnsweredKeys)) {
			return true;
		}
		return false;
	}
/**
 * セッションの回答済み小テストリストに新しい小テストを追加する
 *
 * @param string $quizKey 回答済に追加する小テストキー
 * @return void
 */
	public function saveOwnAnsweredKeys($quizKey) {
		// まだ回答済データが初期状態のときはまずは確保
		if ($this->__ownAnsweredKeys === null) {
			$this->getOwnAnsweredKeys();
		}
		// 回答済み小テスト配列に追加
		$this->__ownAnsweredKeys[] = $quizKey;
		// ログイン状態の人の場合はこれ以上の処理は不要
		if (! empty(Current::read('User.id'))) {
			return;
		}
		// 未ログインの人の場合はセッションに書いておく
		$session = $this->_Collection->load('Session');
		$blockId = Current::read('Block.id');
		$session->write('Quizzes.ownAnsweredQuizKeys.' . $blockId, implode(',', $this->__ownAnsweredKeys));

		// 回答中アンケートからは削除しておく
		$this->deleteProgressiveSummaryOfThisUser($quizKey);
	}
}
