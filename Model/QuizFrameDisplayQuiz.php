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
 * Validation rules
 *
 * @var array
 */
	public $validate = array();

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Frame' => array(
			'className' => 'Frames.Frame',
			'foreignKey' => 'frame_key',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Quiz' => array(
			'className' => 'Quizzes.Quiz',
			'foreignKey' => 'quiz_key',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

/**
 * Quiz list for check
 *
 * @var array
 */
	public $chkQuizList = array();

/**
 * Called during validation operations, before validation. Please note that custom
 * validation rules can be defined in $validate.
 *
 * @param array $options Options passed from Model::save().
 * @return bool True if validate operation should continue, false to abort
 * @link http://book.cakephp.org/2.0/en/models/callback-methods.html#beforevalidate
 * @see Model::save()
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 */
	public function beforeValidate($options = array()) {
		// チェック用のリストを確保しておく
		$Quiz = ClassRegistry::init('Quizzes.Quiz');
		$quizzes = $Quiz->find('all', array(
			'conditions' => $Quiz->getBaseCondition(),
			'recursive' => -1
		));
		$this->chkQuizList = Hash::combine($quizzes, '{n}.Quiz.id', '{n}.Quiz.key');

		$this->validate = ValidateMerge::merge($this->validate, array(
			'quiz_key' => array(
				'notBlank' => array(
					'rule' => array('notBlank'),
					'message' => __d('net_commons', 'Invalid request.'),
					'allowEmpty' => false,
					'required' => true,
					//'last' => false, // Stop validation after this rule
					//'on' => 'create', // Limit validation to 'create' or 'update' operations
				),
				'inList' => array(
					'rule' => array('inList', $this->chkQuizList),
					'message' => __d('net_commons', 'Invalid request.'),
				),
			),
		));
		parent::beforeValidate($options);

		return true;
	}

/**
 * validateFrameDisplayQuiz
 *
 * @param mix $data PostData
 * @return bool
 */
	public function validateFrameDisplayQuiz($data) {
		$frameSetting = $data['QuizFrameSetting'];

		if ($frameSetting['display_type'] == QuizzesComponent::DISPLAY_TYPE_SINGLE) {
			$saveData = Hash::extract($data, 'Single.QuizFrameDisplayQuiz');
			if (! $saveData) {
				return false;
			}
			$this->set($saveData);
			$ret = $this->validates();
		} else {
			$saveData = Hash::extract($data, 'List.QuizFrameDisplayQuiz');
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
 * @throws InternalErrorException
 */
	public function saveFrameDisplayQuiz($data) {
		if (! $this->validateFrameDisplayQuiz($data)) {
			return false;
		}

		//トランザクションBegin
		$this->begin();
		try {
			if ($data['QuizFrameSetting']['display_type'] == QuizzesComponent::DISPLAY_TYPE_SINGLE) {
				// このフレームに設定されている全てのレコードを消す
				// POSTされたアンケートのレコードのみ作成する
				$ret = $this->saveDisplayQuizForSingle($data);
			} else {
				// hiddenでPOSTされたレコードについて全て処理する
				// POSTのis_displayが０，１によってdeleteかinsertで処理する
				$ret = $this->saveDisplayQuizForList($data);
			}
			//トランザクションCommit
			$this->commit();
		} catch (Exception $ex) {
			//トランザクションRollback
			$this->rollback();
			CakeLog::error($ex);
			throw $ex;
		}
		return $ret;
	}

/**
 * saveDisplayQuizForList
 *
 * @param mix $data PostData
 * @return bool
 * @throws InternalErrorException
 */
	public function saveDisplayQuizForList($data) {
		$frameKey = Current::read('Frame.key');

		foreach ($data['List']['QuizFrameDisplayQuiz'] as $value) {
			$quizKey = $value['quiz_key'];
			// 何かinputの実現時にどのメソッド呼ぶかで配列で来たり値で来たりするんだ..
			// 仕方ないのでくる値のタイプによって見るところを変更する
			if (is_array($value['is_display'])) {
				$isDisplay = $value['is_display'][0];
			} else {
				$isDisplay = $value['is_display'];
			}
			$saveQs = array(
				'frame_key' => $frameKey,
				'quiz_key' => $quizKey
			);
			if ($isDisplay != 0) {
				// この関数内部でエラーがあった時は、Exceptionなので戻りは見ない
				$this->saveDisplayQuiz($saveQs);
			} else {
				if (! $this->deleteAll($saveQs, false)) {
					throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
				}
			}
		}
		// この関数内部でエラーがあった時は、Exceptionなので戻りは見ない
		$this->updateFrameDefaultAction("''");

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

		$saveData = Hash::extract($data, 'Single.QuizFrameDisplayQuiz');
		$saveData['frame_key'] = $frameKey;
		// この関数内部でエラーがあった時は、Exceptionなので戻りは見ない
		$this->saveDisplayQuiz($saveData);

		$action = '\'quiz_answers/start/' . Current::read('Block.id') . '/' . $saveData['quiz_key'] . "'";
		// この関数内部でエラーがあった時は、Exceptionなので戻りは見ない
		$this->updateFrameDefaultAction($action);

		return true;
	}
/**
 * updateFrameDefaultAction
 * update Frame default_action
 *
 * @param string $action default_action
 * @return bool
 * @throws InternalErrorException
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
			throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
		}
		return true;
	}
/**
 * saveDisplayQuiz
 * saveQuizFrameDisplayQuiz
 *
 * @param array $data save data
 * @return bool
 * @throws InternalErrorException
 */
	public function saveDisplayQuiz($data) {
		$displayQuiz = $this->find('first', array(
			'conditions' => $data
		));
		if (! empty($displayQuiz)) {
			return true;
		}
		$this->create();
		if (! $this->save($data)) {
			throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
		}
		return true;
	}
}
