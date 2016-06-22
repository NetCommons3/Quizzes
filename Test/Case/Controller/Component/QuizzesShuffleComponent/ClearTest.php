<?php
/**
 * QuizzesShuffleComponent::clear()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsControllerTestCase', 'NetCommons.TestSuite');

/**
 * QuizzesShuffleComponent::clear()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\Controller\Component\QuizzesShuffleComponent
 */
class QuizzesShuffleComponentClearTest extends NetCommonsControllerTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array();

/**
 * Plugin name
 *
 * @var string
 */
	public $plugin = 'quizzes';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		//テストプラグインのロード
		NetCommonsCakeTestCase::loadTestPlugin($this, 'Quizzes', 'TestQuizzes');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		//ログアウト
		TestAuthGeneral::logout($this);

		parent::tearDown();
	}

/**
 * clear()のテスト
 *
 * @return void
 */
	public function testClear() {
		//テストコントローラ生成
		$this->generateNc('TestQuizzes.TestQuizzesShuffleComponent');

		//ログイン
		TestAuthGeneral::login($this);

		//テストアクション実行
		$this->_testGetAction('/test_quizzes/test_quizzes_shuffle_component/index',
				array('method' => 'assertNotEmpty'), null, 'view');
		$pattern = '/' . preg_quote('Controller/Component/TestQuizzesShuffleComponent', '/') . '/';
		$this->assertRegExp($pattern, $this->view);

		//テスト実行
		$this->controller->QuizzesShuffle->clear();
		$this->controller->QuizzesShuffle->clear('quiz_1');
	}

}
