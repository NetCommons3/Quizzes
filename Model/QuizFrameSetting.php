<?php
/**
 * QuizFrameSetting Model
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
 * Summary for QuizFrameSetting Model
 */
class QuizFrameSetting extends QuizzesAppModel {

/**
 * default display quiz item count
 *
 * @var int
 */
	const	QUIZ_DEFAULT_DISPLAY_NUM_PER_PAGE = 10;

/**
 * display_sort_type. new arrivals
 *
 * @var string
 */
	const DISPLAY_SORT_TYPE_NEW_ARRIVALS = '0';
	const DISPLAY_SORT_TYPE_RESPONSE_TIME_DESC = '1';
	const DISPLAY_SORT_TYPE_SURVEY_STATUS_ORDER_ASC = '2';
	const DISPLAY_SORT_TYPE_BY_TITLE_ASC = '3';

/**
 * display_sort_types list
 *
 * @var array
 */
	static public $displaySortTypesList = array(
		self::DISPLAY_SORT_TYPE_NEW_ARRIVALS,
		self::DISPLAY_SORT_TYPE_RESPONSE_TIME_DESC,
		self::DISPLAY_SORT_TYPE_SURVEY_STATUS_ORDER_ASC,
		self::DISPLAY_SORT_TYPE_BY_TITLE_ASC
	);

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
	);

/**
 * getQuizFrameSettingConditions 指定されたframe_keyの設定要件をSQL検索用の配列で取り出す
 *
 * @param string $frameKey frame key
 * @return array ... displayNum sortField sortDirection
 */
	public function getQuizFrameSettingConditions($frameKey) {
		list(, $limit, $sort, $dir) = $this->getQuizFrameSetting($frameKey);
		return array(
			'offset' => 0,
			'limit' => $limit,
			'order' => 'Quiz.' . $sort . ' ' . $dir);
	}
/**
 * getQuizFrameSetting 指定されたframe_keyの設定要件を取り出す
 *
 * @param string $frameKey frame key
 * @return array ... displayNum sortField sortDirection
 */
	public function getQuizFrameSetting($frameKey) {
		$frameSetting = $this->find('first', array(
			'conditions' => array(
				'frame_key' => $frameKey
			),
			'recursive' => -1
		));

		if (!$frameSetting) {
			$frameSetting = $this->prepareFrameSetting($frameKey);
		}

		$setting = $frameSetting['QuizFrameSetting'];
		$displayType = $setting['display_type'];
		$displayNum = $setting['display_num_per_page'];
		if ($setting['sort_type'] == QuizzesComponent::QUIZ_SORT_MODIFIED) {
			$sort = 'modified';
			$dir = 'DESC';
		} elseif ($setting['sort_type'] == QuizzesComponent::QUIZ_SORT_CREATED) {
			$sort = 'created';
			$dir = 'ASC';
		} elseif ($setting['sort_type'] == QuizzesComponent::QUIZ_SORT_TITLE) {
			$sort = 'title';
			$dir = 'ASC';
		} elseif ($setting['sort_type'] == QuizzesComponent::QUIZ_SORT_END) {
			$sort = 'publish_end';
			$dir = 'ASC';
		}
		return array($displayType, $displayNum, $sort, $dir);
	}
/**
 * getDefaultFrameSetting
 * return default frame setting
 *
 * @return array
 */
	public function getDefaultFrameSetting() {
		$frame = array(
			'QuizFrameSetting' => array(
				'id' => '',
				'display_type' => QuizzesComponent::DISPLAY_TYPE_LIST,
				'display_num_per_page' => self::QUIZ_DEFAULT_DISPLAY_NUM_PER_PAGE,
				'sort_type' => self::DISPLAY_SORT_TYPE_NEW_ARRIVALS,
			)
		);
		return $frame;
	}

/**
 * prepareFrameSetting
 *
 * @param string $frameKey frame key
 * @return mix
 * @throws Exception
 * @throws InternalErrorException
 */
	public function prepareFrameSetting($frameKey) {
		$frameSetting = $this->getDefaultFrameSetting();
		$this->saveFrameSettings($frameSetting);
		$ret = $this->find('first', array(
			'conditions' => array(
				'frame_key' => $frameKey
			),
			'recursive' => -1
		));
		return $ret;
	}
/**
 * saveFrameSettings
 *
 * @param array $data save data
 * @return bool
 * @throws InternalErrorException
 */
	public function saveFrameSettings($data) {
		$this->loadModels([
			'QuizFrameDisplayQuiz' => 'Quizzes.QuizFrameDisplayQuiz',
		]);

		//トランザクションBegin
		$this->begin();
		try {
			// フレーム設定のバリデート
			$this->create();
			$this->set($data);
			if (! $this->validates()) {
				$this->log($this->validationErrors, 'debug');
				return false;
			}

			// フレームに表示するアンケート一覧設定のバリデート
			// 一覧表示タイプと単独表示タイプ
			if (isset($data['QuizFrameDisplayQuizzes'])) {
				$ret = $this->QuizFrameDisplayQuiz->validateFrameDisplayQuiz($data);
				if ($ret === false) {
					$this->log($this->QuizFrameDisplayQuiz->validationErrors, 'debug');
					return false;
				}
			}
			// フレーム設定の登録
			if (! $this->save($data, false)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}

			// フレームに表示するアンケート一覧設定の登録
			// 一覧表示タイプと単独表示タイプ
			if (isset($data['QuizFrameDisplayQuizzes'])) {
				$ret = $this->QuizFrameDisplayQuiz->saveFrameDisplayQuiz($data);
				if ($ret === false) {
					throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
				}
			}
			//トランザクションCommit
			$this->commit();
		} catch (Exception $ex) {
			//トランザクションRollback
			$this->rollback();
			CakeLog::error($ex);
			throw $ex;
		}

		return true;
	}

}
