<?php
/**
 * QuizFrameDisplayQuiz Model
 *
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Your Name <yourname@domain.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('QuizzesAppModel', 'Quizzes.Model');

/**
 * Summary for QuizFrameDisplayQuiz Model
 */
class QuizFrameDisplayQuiz extends QuizzesAppModel {

/**
 * Use database config
 *
 * @var string
 */
	public $useDbConfig = 'master';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'frame_key' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'quiz_key' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);

/**
 * validateFrameDisplayQuiz
 *
 * @param mix $data PostData
 * @return bool
 */
	public function validateFrameDisplayQuiz($data) {
		if ($data['QuizFrameSetting']['display_type'] == QuizzesComponent::DISPLAY_TYPE_SINGLE) {
			$saveData = Hash::extract($data, 'Single.QuizFrameDisplayQuizzes');
			$this->set($saveData);
			$ret = $this->validates();
		} else {
			$saveData = $data['QuizFrameDisplayQuizzes'];
			$ret = $this->saveAll($saveData, array('validate' => 'only'));
		}
		return $ret;
	}

/**
 * saveFrameDisplayQuiz
 * this function is called when save quiz
 *
 * @param mix $data PostData
 * @return bool
 */
	public function saveFrameDisplayQuiz($data) {
		//トランザクションは親元のQuizFrameSettingでやっているので不要
		if ($data['QuizFrameSetting']['display_type'] == QuizzesComponent::DISPLAY_TYPE_SINGLE) {
			// このフレームに設定されている全てのレコードを消す
			// POSTされたアンケートのレコードのみ作成する
			$ret = $this->saveDisplayQuizForSingle($data);
		} else {
			// hiddenでPOSTされたレコードについて全て処理する
			// POSTのis_displayが０，１によってdeleteかinsertで処理する
			$ret = $this->saveDisplayQuizForList($data);
		}
		return $ret;
	}

/**
 * saveDisplayQuizForList
 *
 * @param mix $data PostData
 * @return bool
 */
	public function saveDisplayQuizForList($data) {
		$frameKey = Current::read('Frame.key');

		foreach ($data['QuizFrameDisplayQuizzes'] as $index => $value) {
			$quizKey = $value['quiz_key'];
			$isDisplay = $data['List']['QuizFrameDisplayQuizzes'][$index]['is_display'];
			$saveQs = array(
				'frame_key' => $frameKey,
				'quiz_key' => $quizKey
			);
			if ($isDisplay != 0) {
				if (!$this->saveDisplayQuiz($saveQs)) {
					return false;
				}
			} else {
				if (!$this->deleteAll($saveQs, false)) {
					return false;
				}
			}
		}
		if (!$this->updateFrameDefaultAction("''")) {
			return false;
		}
		return true;
	}

/**
 * saveDisplayQuizForSingle
 *
 * @param mix $data PostData
 * @return bool
 */
	public function saveDisplayQuizForSingle($data) {
		$frameKey = Current::read('Frame.key');
		$deleteQs = array(
			'frame_key' => $frameKey,
		);
		$this->deleteAll($deleteQs, false);

		$saveData = Hash::extract($data, 'Single.QuizFrameDisplayQuizzes');
		$saveData['frame_key'] = $frameKey;
		if (!$this->saveDisplayQuiz($saveData)) {
			return false;
		}
		$action = '\'quiz_answers/view/' . Current::read('Block.id') . '/' . $saveData['quiz_key'] . "'";
		if (!$this->updateFrameDefaultAction($action)) {
			return false;
		}
		return true;
	}
/**
 * updateFrameDefaultAction
 * update Frame default_action
 *
 * @param string $action default_action
 * @return bool
 */
	public function updateFrameDefaultAction($action) {
		// frameのdefault_actionを変更しておく
		$this->loadModels([
			'Frame' => 'Frames.Frame',
		]);
		$conditions = array(
			'Frame.key' => Current::read('Frame.key')
		);
		$frameData = array(
			'default_action' => $action
		);
		if (! $this->Frame->updateAll($frameData, $conditions)) {
			return false;
		}
		return true;
	}
/**
 * saveDisplayQuiz
 * saveQuizFrameDisplayQuiz
 *
 * @param array $data save data
 * @return bool
 */
	public function saveDisplayQuiz($data) {
		$displayQuiz = $this->find('first', array(
			'conditions' => $data
		));
		if (! empty($displayQuiz)) {
			return true;
		}
		$this->create();
		if (!$this->save($data)) {
			return false;
		}
		return true;
	}
}
