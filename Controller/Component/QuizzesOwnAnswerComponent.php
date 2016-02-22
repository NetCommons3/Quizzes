<?php
/**
 * QuizzesOwnAnswer Component
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
class QuizzesOwnAnswerComponent extends Component {

/**
 * Answered summary id list
 *
 * 回答ID配列
 *
 * @var array
 */
	private $__answeredSummaryIds = null;

/**
 * 回答IDリストを取得する
 *
 * @return Answered id list
 */
	public function getAnsweredSummaryIds() {
		if (isset($this->__answeredSummaryIds)) {
			return $this->__answeredSummaryIds;
		}

		$this->__answeredSummaryIds = array();

		if (empty(Current::read('User.id'))) {
			$session = $this->_Collection->load('Session');
			$blockId = Current::read('Block.id');
			$answerSummaryIds = $session->read('Quizzes.answeredSummaryIds.' . $blockId);
			if (isset($answerSummaryIds)) {
				$this->__answeredSummaryIds = explode(',', $answerSummaryIds);
			}

			return $this->__answeredSummaryIds;
		}

		$answerSummary = ClassRegistry::init('Quizzes.QuizAnswerSummary');
		$conditions = array(
			'user_id' => Current::read('User.id'),
			'answer_status' => QuizzesComponent::ACTION_ACT,
			'test_status' => QuizzesComponent::TEST_ANSWER_STATUS_PEFORM,
		);
		$answerSummaryIds = $answerSummary->find(
			'list',
			array(
				'conditions' => $conditions,
				'fields' => array('QuizAnswerSummary.id'),
				'recursive' => -1
			)
		);
		$this->__answeredSummaryIds = array_values($answerSummaryIds);	// idの使用を防ぐ（いらない？）

		return $this->__answeredSummaryIds;
	}

/**
 * 指定された回答サマリが自分の回答かどうかを返す
 *
 * @param int $summaryId 回答サマリＩＤ
 * @return bool
 */
	public function checkOwnAnsweredSummaryId($summaryId) {
		// まだ回答済データが初期状態のときはまずは確保
		if ($this->__answeredSummaryIds === null) {
			$this->getAnsweredSummaryIds();
		}
		if (in_array($summaryId, $this->__answeredSummaryIds)) {
			return true;
		}
		return false;
	}

/**
 * セッションの回答リストに新しい回答IDを追加する
 *
 * @param string $id 追加する回答ID
 * @return void
 */
	public function saveAnsweredSummaryIds($id) {
		// まだデータが初期状態のときはまずは確保
		if ($this->__answeredSummaryIds === null) {
			$this->getAnsweredSummaryIds();
		}
		// 回答済み小テスト配列に追加
		$this->__answeredSummaryIds[] = $id;
		// ログイン状態の人の場合はこれ以上の処理は不要
		if (! empty(Current::read('User.id'))) {
			return;
		}
		// 未ログインの人の場合はセッションに書いておく
		$session = $this->_Collection->load('Session');
		$blockId = Current::read('Block.id');
		$session->write('Quizzes.answeredSummaryIds.' . $blockId, implode(',', $this->getAnsweredSummaryIds()));
	}
}
