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
 * Answered quiz keys
 *
 * 回答済み小テスト回答数
 * 小テストキーを配列のキーにして、それぞれの値はこれまでの回答数
 *
 * @var array
 */
	private $__ownAnsweredCounts = null;

/**
 * 指定された小テストに該当する回答中小テストのサマリを取得する
 *
 * @param string $quizKey 回答済に追加する小テストキー
 * @return progressive Answer Summary data
 */
	public function getProgressiveSummaryOfThisUser($quizKey) {
		// 戻り値初期化
		$summary = false;
		$answerSummary = ClassRegistry::init('Quizzes.QuizAnswerSummary');
		// ログインユーザーといえどもセッションにあることを大前提とすべき
		// そうしないと、時間を計るテストだというのに、DBに残っている古いデータを持ってきて続行を
		// 許してしまう。そうすると時間計測がとんでもない時間になる
		//$userId = Current::read('User.id');
		//if (empty($userId)) {
		$session = $this->_Collection->load('Session');
		$summaryId = $session->read('Quizzes.progressiveSummary.' . $quizKey);
		if ($summaryId) {
			$summary = $answerSummary->findById($summaryId);
		}
		return $summary;
		//}
		////////////////////////////////上記理由により下記ロジックはカット
		// ログインユーザーはDBから探す
		//$conditions = array(
		//	'answer_status != ' => QuizzesComponent::ACTION_ACT,
		//	'quiz_key' => $quizKey,
		//	'user_id' => Current::read('User.id'),
		//);
		//$summary = $answerSummary->find('first', array(
		//	'conditions' => $conditions,
		//	'order' => 'QuizAnswerSummary.created ASC'	// 最も古いものを一つ選ぶ
		//));
		//return $summary;
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
 * 指定された小テストに対応する回答中サマリを作成
 *
 * @param array $quiz 小テスト
 * @return progressive Answer Summary data
 */
	public function forceGetProgressiveSummaryOfThisUser($quiz) {
		// とりあえず現在　回答中のデータがないか調べて
		$summary = $this->getProgressiveSummaryOfThisUser($quiz['Quiz']['key']);
		// 無いようだったら新たに作成する
		if (! $summary) {
			$answerSummary = ClassRegistry::init('Quizzes.QuizAnswerSummary');
			// スタート
			$summaryId = $answerSummary->saveStartSummary($quiz);
			if ($summaryId) {
				$this->saveProgressiveSummaryOfThisUser(
					$quiz['Quiz']['key'],
					$summaryId
				);
				$summary = $answerSummary->findById($summaryId);
			}
		}

		return $summary;
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
		$this->__ownAnsweredCounts = array();

		$userId = Current::read('User.id');
		if (empty($userId)) {
			$session = $this->_Collection->load('Session');
			$blockId = Current::read('Block.id');
			$ownAnsweredKeys = $session->read('Quizzes.ownAnsweredQuizKeys.' . $blockId);
			if (isset($ownAnsweredKeys)) {
				$this->__ownAnsweredKeys = array_keys($ownAnsweredKeys);
				$this->__ownAnsweredCounts = $ownAnsweredKeys;
			}

			return $this->__ownAnsweredKeys;
		}

		$answerSummary = ClassRegistry::init('Quizzes.QuizAnswerSummary');
		$conditions = array(
			'user_id' => Current::read('User.id'),
			'answer_status' => QuizzesComponent::ACTION_ACT,
			'test_status' => QuizzesComponent::TEST_ANSWER_STATUS_PEFORM,
		);
		$ownAnsweredKeys = $answerSummary->find(
			'all',
			array(
				'fields' => array(
					'QuizAnswerSummary.quiz_key',
					'COUNT(QuizAnswerSummary.id) AS cnt'
				),
				'conditions' => $conditions,
				//'fields' => array('QuizAnswerSummary.quiz_key'),
				'recursive' => -1,
				'group' => 'quiz_key'
			)
		);
		if ($ownAnsweredKeys) {
			$ownAnsweredKeys = Hash::combine(
				$ownAnsweredKeys,
				'{n}.QuizAnswerSummary.quiz_key',
				'{n}.{n}.cnt'
			);
			$this->__ownAnsweredKeys = array_keys($ownAnsweredKeys);
			$this->__ownAnsweredCounts = $ownAnsweredKeys;
		}
		return $this->__ownAnsweredKeys;
	}
/**
 * 回答済み小テスト回数リストを取得する
 *
 * @return Answered Quiz keys list
 */
	public function getOwnAnsweredCounts() {
		if (is_null($this->__ownAnsweredCounts)) {
			$this->getOwnAnsweredKeys();
		}
		return $this->__ownAnsweredCounts;
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
		if (isset($this->__ownAnsweredCounts[$quizKey])) {
			$this->__ownAnsweredCounts[$quizKey]++;
		} else {
			$this->__ownAnsweredCounts[$quizKey] = 1;
		}
		// 回答中アンケートからは削除しておく
		$this->deleteProgressiveSummaryOfThisUser($quizKey);

		// ログイン状態の人の場合はこれ以上の処理は不要
		$userId = Current::read('User.id');
		if (! empty($userId)) {
			return;
		}
		// 未ログイン：セッションに書いておく
		$session = $this->_Collection->load('Session');
		$blockId = Current::read('Block.id');
		$session->write(
			'Quizzes.ownAnsweredQuizKeys.' . $blockId,
			$this->__ownAnsweredCounts
		);
	}
}
