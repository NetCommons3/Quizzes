<?php
/**
 * QuizFrameDisplayQuiz::saveFrameDisplayQuiz()のテスト
 *
 * @property QuizFrameDisplayQuiz $QuizFrameDisplayQuiz
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsSaveTest', 'NetCommons.TestSuite');
App::uses('QuizzesComponent', 'Quizzes.Controller/Component');

/**
 * QuizFrameDisplayQuiz::saveFrameDisplayQuiz()のテスト
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\Model\QuizFrameDisplayQuiz
 */
class QuizSaveFrameDisplayQuizTest extends NetCommonsSaveTest {

/**
 * Plugin name
 *
 * @var array
 */
	public $plugin = 'quizzes';

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.quizzes.quiz',
		'plugin.quizzes.quiz_answer',
		'plugin.quizzes.quiz_answer_summary',
		'plugin.quizzes.quiz_choice',
		'plugin.quizzes.quiz_correct',
		'plugin.quizzes.quiz_frame_display_quiz',
		'plugin.quizzes.quiz_frame_setting',
		'plugin.quizzes.quiz_page',
		'plugin.quizzes.quiz_question',
		'plugin.quizzes.block_setting_for_quiz',
		'plugin.workflow.workflow_comment',
		'plugin.authorization_keys.authorization_keys'
	);

/**
 * Model name
 *
 * @var array
 */
	protected $_modelName = 'QuizFrameDisplayQuiz';

/**
 * Method name
 *
 * @var array
 */
	protected $_methodName = 'saveFrameDisplayQuiz';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		Current::$current['Frame']['key'] = 'frame_3';
		$this->_mockForAny(
			$this->_modelName,
			'Quizzes.Quiz',
			'getBaseCondition', array());
	}
/**
 * Mockセット
 *
 * @param string $model モデル名
 * @param string $mockModel Mockのモデル
 * @param string $mockMethod Mockのメソッド
 * @param mixed $return 戻り値
 * @return void
 */
	protected function _mockForAny($model, $mockModel, $mockMethod, $return) {
		list($mockPlugin, $mockModel) = pluginSplit($mockModel);

		if (is_string($mockMethod)) {
			$mockMethod = array($mockMethod);
		}
		$mockClassName = get_class($this->$model->$mockModel);
		if (substr($mockClassName, 0, strlen('Mock_')) !== 'Mock_') {
			$this->$model->$mockModel = $this->getMockForModel(
				$mockPlugin . '.' . $mockModel, $mockMethod, array('plugin' => $mockPlugin)
			);
		}
		foreach ($mockMethod as $method) {
			$this->$model->$mockModel->expects($this->any())
				->method($method)
				->will($this->returnValue($return));
		}
	}
/**
 * テストDataの取得
 *
 * @param int $displayType display type
 * @return array
 */
	protected function _getData($displayType = QuizzesComponent::DISPLAY_TYPE_SINGLE) {
		$data = array(
			'QuizFrameSetting' => array(
				'display_type' => $displayType,
				'display_num_per_page' => 10,
				'sort_type' => 'Quiz.modified DESC',
			),
			'List' => array(
				'QuizFrameDisplayQuiz' => array(
					array('is_display' => '0', 'quiz_key' => '5fdb4f0049f3bddeabc49cd2b72c6ac9'),
					array('is_display' => '1', 'quiz_key' => 'a2cf0e48f281be7c3cc35f0920f047ca'),
					array('is_display' => '1', 'quiz_key' => 'a916437af184b4a185f685a93099adca')
				)
			),
			'Single' => array(
				'QuizFrameDisplayQuiz' => array(
					'quiz_key' => '5fdb4f0049f3bddeabc49cd2b72c6ac9',
				)
			)
		);
		return $data;
	}

/**
 * Saveのテスト
 *
 * @param array $data 登録データ
 * @dataProvider dataProviderSave
 * @return void
 */
	public function testSave($data) {
		$model = $this->_modelName;
		$method = $this->_methodName;

		//チェック用データ取得
		$before = $this->$model->find('all', array(
			'recursive' => -1,
			'conditions' => array('frame_key' => Current::read('Frame.key')),
		));

		//テスト実行
		$result = $this->$model->$method($data);
		$this->assertNotEmpty($result);

		//登録データ取得
		$actual = $this->$model->find('all', array(
			'recursive' => -1,
			'conditions' => array('frame_key' => Current::read('Frame.key')),
			'order' => array('quiz_key asc'),
		));
		$actual = Hash::remove($actual, '{n}.' . $this->$model->alias . '.created');
		$actual = Hash::remove($actual, '{n}.' . $this->$model->alias . '.created_user');
		$actual = Hash::remove($actual, '{n}.' . $this->$model->alias . '.modified');
		$actual = Hash::remove($actual, '{n}.' . $this->$model->alias . '.modified_user');
		$actual = Hash::remove($actual, '{n}.' . $this->$model->alias . '.id');
		$actual = Hash::remove($actual, '{n}.' . $this->$model->alias . '.frame_key');

		if ($data['QuizFrameSetting']['display_type'] == QuizzesComponent::DISPLAY_TYPE_SINGLE) {
			$expected[0] = Hash::extract($data, 'Single');
		} else {
			$expected = $before;
			foreach ($data['List']['QuizFrameDisplayQuiz'] as $value) {
				if ($value['is_display']) {
					$quiz = Hash::extract($expected, '{n}.' . $this->$model->alias . '[quiz_key=' . $value['quiz_key'] . ']');
					if (! $quiz) {
						$expected[] = array('QuizFrameDisplayQuiz' => array('quiz_key' => $value['quiz_key']));
					}
				} else {
					$expected = Hash::remove($expected, '{n}.' . $this->$model->alias . '[quiz_key=' . $value['quiz_key'] . ']');
				}
			}
			$expected = Hash::remove($expected, '{n}.' . $this->$model->alias . '.created');
			$expected = Hash::remove($expected, '{n}.' . $this->$model->alias . '.created_user');
			$expected = Hash::remove($expected, '{n}.' . $this->$model->alias . '.modified');
			$expected = Hash::remove($expected, '{n}.' . $this->$model->alias . '.modified_user');
			$expected = Hash::remove($expected, '{n}.' . $this->$model->alias . '.id');
			$expected = Hash::remove($expected, '{n}.' . $this->$model->alias . '.frame_key');
			$expected = Hash::sort($expected, '{n}.' . $this->$model->alias . '.quiz_key');
		}

		$this->assertEquals($expected, $actual);
	}

/**
 * SaveのDataProvider
 *
 * ### 戻り値
 *  - data 登録データ
 *
 * @return void
 */
	public function dataProviderSave() {
		return array(
			array($this->_getData(QuizzesComponent::DISPLAY_TYPE_SINGLE)),
			array($this->_getData(QuizzesComponent::DISPLAY_TYPE_LIST)),
		);
	}

/**
 * SaveのExceptionErrorのDataProvider
 *
 * ### 戻り値
 *  - data 登録データ
 *  - mockModel Mockのモデル
 *  - mockMethod Mockのメソッド
 *
 * @return void
 */
	public function dataProviderSaveOnExceptionError() {
		return array(
			array(
				$this->_getData(QuizzesComponent::DISPLAY_TYPE_SINGLE),
				'Quizzes.QuizFrameDisplayQuiz',
				'save'),
			array(
				$this->_getData(QuizzesComponent::DISPLAY_TYPE_LIST),
				'Quizzes.QuizFrameDisplayQuiz',
				'deleteAll'),
			array(
				$this->_getData(QuizzesComponent::DISPLAY_TYPE_LIST),
				'Frames.Frame',
				'updateAll'),
		);
	}
/**
 * SaveのValidationErrorのDataProvider
 *
 * ### 戻り値
 *  - data 登録データ
 *  - mockModel Mockのモデル
 *
 * @return void
 */
	public function dataProviderSaveOnValidationError() {
		$data = $this->_getData(QuizzesComponent::DISPLAY_TYPE_SINGLE);
		return array(
			array($data, 'Quizzes.QuizFrameDisplayQuiz'),
		);
	}
/**
 * ValidationErrorのDataProvider
 *
 * ### 戻り値
 *  - field フィールド名
 *  - value セットする値
 *  - message エラーメッセージ
 *  - overwrite 上書きするデータ
 *
 * @return void
 */
	public function dataProviderValidationError() {
		return array(
			array($this->_getData(), 'Single', null,
				__d('net_commons', 'Invalid request.')),
		);
	}

}
