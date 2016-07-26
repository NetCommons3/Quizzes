<?php
/**
 * QuizFrameSetting::saveFrameSettings()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsSaveTest', 'NetCommons.TestSuite');
App::uses('QuizFrameSettingFixture', 'Quizzes.Test/Fixture');

/**
 * QuizFrameSetting::saveFrameSettings()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\Model\QuizFrameSetting
 */
class QuizFrameSettingSaveFrameSettingsTest extends NetCommonsSaveTest {

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
	);

/**
 * Plugin name
 *
 * @var string
 */
	public $plugin = 'quizzes';

/**
 * Model name
 *
 * @var string
 */
	protected $_modelName = 'QuizFrameSetting';

/**
 * Method name
 *
 * @var string
 */
	protected $_methodName = 'saveFrameSettings';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		Current::$current['Block']['id'] = 2;
		$this->_setMockMethod(
			'QuizFrameDisplayQuiz',
			array('validateFrameDisplayQuiz', 'saveFrameDisplayQuiz'),
			true
		);
	}

/**
 * _setMockMethod
 * モック設定
 *
 * @param $mockModel string モックにするモデル名
 * @param $mockMethod array モックにするメソッド
 * @param $return mix 期待値
 * @return void
 */
	protected function _setMockMethod($mockModel, $mockMethod, $return) {
		$model = $this->_modelName;
		$mockModel = 'QuizFrameDisplayQuiz';
		$mockClassName = get_class($this->$model->$mockModel);
		if (substr($mockClassName, 0, strlen('Mock_')) !== 'Mock_') {
			$this->$model->$mockModel = $this->getMockForModel(
				'Quizzes.' . $mockModel, $mockMethod, array('plugin' => 'Quizzes')
			);
		}
		foreach ($mockMethod as $method) {
			$this->$model->$mockModel->expects($this->any())
				->method($method)
				->will($this->returnValue($return));
		}
	}
/**
 * Save用DataProvider
 *
 * ### 戻り値
 *  - data 登録データ
 *
 * @return array テストデータ
 */
	public function dataProviderSave() {
		$data['QuizFrameSetting'] = (new QuizFrameSettingFixture())->records[0];

		$results = array();
		// * 編集の登録処理
		$results[0] = array($data);
		// * 新規の登録処理
		$results[1] = array($data);
		$results[1] = Hash::insert($results[1], '0.QuizFrameSetting.id', null);
		$results[1] = Hash::insert($results[1], '0.QuizFrameSetting.frame_key', 'frame_99');
		$results[1] = Hash::remove($results[1], '0.QuizFrameSetting.created_user');
		$results[1] = Hash::remove($results[1], '0.QuizFrameSetting.created');

		return $results;
	}

/**
 * SaveのExceptionError用DataProvider
 *
 * ### 戻り値
 *  - data 登録データ
 *  - mockModel Mockのモデル
 *  - mockMethod Mockのメソッド
 *
 * @return array テストデータ
 */
	public function dataProviderSaveOnExceptionError() {
		$data = $this->dataProviderSave()[0][0];

		return array(
			array($data, 'Quizzes.QuizFrameSetting', 'save'),
		);
	}

/**
 * SaveのValidationError用DataProvider
 *
 * ### 戻り値
 *  - data 登録データ
 *  - mockModel Mockのモデル
 *  - mockMethod Mockのメソッド(省略可：デフォルト validates)
 *
 * @return array テストデータ
 */
	public function dataProviderSaveOnValidationError() {
		$data = $this->dataProviderSave()[0][0];

		return array(
			array($data, 'Quizzes.QuizFrameSetting'),
		);
	}
}
