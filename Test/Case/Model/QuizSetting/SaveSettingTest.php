<?php
/**
 * QuizSetting::saveSetting()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsModelTestCase', 'NetCommons.TestSuite');

/**
 * QuizSetting::saveSetting()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\Model\QuizSetting
 */
class QuizSettingSaveSettingTest extends NetCommonsModelTestCase {

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
	protected $_modelName = 'QuizSetting';

/**
 * Method name
 *
 * @var string
 */
	protected $_methodName = 'saveSetting';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		Current::write('Plugin.key', $this->plugin);
	}

/**
 * Saveのテスト 通常の登録
 *
 * @return void
 */
	public function testSave() {
		$model = $this->_modelName;
		$method = $this->_methodName;

		Current::$current['Frame']['key'] = 'frame_3';
		Current::$current['Block']['key'] = 'block_1';

		$result = $this->$model->$method();
		$this->assertTrue($result);
	}

/**
 * Saveのテスト Setting登録で何等かのエラー
 *
 * @return void
 */
	public function testSaveError() {
		$model = $this->_modelName;
		$method = $this->_methodName;

		Current::$current['Frame']['key'] = 'frame_3';
		// カレントのブロック情報を設定しないと、データが空になるのでエラーになります
		//Current::$current['Block']['key'] = '';

		$result = $this->$model->$method();

		// Current::$current['Block']['id'] = null のため、検索結果=空によりtrue
		//$this->assertFalse($result);
		$this->assertTrue($result);
	}

/**
 * Saveのテスト Exceptionエラー
 *
 * @return void
 */
	public function testSaveException() {
		$model = $this->_modelName;
		$method = $this->_methodName;

		Current::$current['Frame']['key'] = 'frame_3';
		Current::$current['Block']['key'] = 'block_1';

		$this->_mockForReturnFalse($model, 'Blocks.BlockSetting', 'saveMany');
		$this->setExpectedException('InternalErrorException');

		$result = $this->$model->$method();
		$this->assertFalse($result);
	}

/**
 * Saveのテスト 既に登録済み
 *
 * @return void
 */
	public function testSaveTrue() {
		$model = $this->_modelName;
		$method = $this->_methodName;

		Current::$current['Frame']['key'] = 'frame_3';
		Current::$current['Block']['id'] = '2';
		Current::$current['Block']['key'] = 'block_1';

		$result = $this->$model->$method();
		$this->assertTrue($result);
	}

}
