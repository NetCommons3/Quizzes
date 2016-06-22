<?php
/**
 * QuizzesPassQuizComponent::getPassQuizKeys()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsControllerTestCase', 'NetCommons.TestSuite');
App::uses('QuizzesComponent', 'Quizzes.Controller/Component');

/**
 * QuizzesPassQuizComponent::getPassQuizKeys()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\Controller\Component\QuizzesPassQuizComponent
 */
class QuizzesPassQuizComponentGetPassQuizKeysTest extends NetCommonsControllerTestCase {

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
		'plugin.quizzes.quiz_page',
		'plugin.quizzes.quiz_question',
	);

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
 * getPassQuizKeys()のテスト
 *
 * @return void
 */
	public function testGetPassQuizKeys() {
		//テストコントローラ生成
		$this->generateNc('TestQuizzes.TestQuizzesPassQuizComponent');

		//ログイン
		TestAuthGeneral::login($this);

		//テストアクション実行
		$this->_testGetAction('/test_quizzes/test_quizzes_pass_quiz_component/index',
				array('method' => 'assertNotEmpty'), null, 'view');
		$pattern = '/' . preg_quote('Controller/Component/TestQuizzesPassQuizComponent', '/') . '/';
		$this->assertRegExp($pattern, $this->view);

		//テスト実行
		$result = $this->controller->QuizzesPassQuiz->getPassQuizKeys();

		$this->assertEqual($result, array());
	}
/**
 * getPassQuizKeys()のテスト
 *
 * @return void
 */
	public function testGetPassQuizKeysNoLogin() {
		//テストコントローラ生成
		$this->generateNc('TestQuizzes.TestQuizzesPassQuizComponent');

		//テストアクション実行
		$this->_testGetAction('/test_quizzes/test_quizzes_pass_quiz_component/index',
			array('method' => 'assertNotEmpty'), null, 'view');
		$pattern = '/' . preg_quote('Controller/Component/TestQuizzesPassQuizComponent', '/') . '/';
		$this->assertRegExp($pattern, $this->view);

		//テスト実行
		$result = $this->controller->QuizzesPassQuiz->getPassQuizKeys();

		$this->assertEqual($result, array());
	}
/**
 * getPassQuizKeys()のテスト
 *
 * @return void
 */
	public function testGetPassQuizKeysNoLogin2() {
		//テストコントローラ生成
		$this->generateNc('TestQuizzes.TestQuizzesPassQuizComponent');

		//テストアクション実行
		$this->_testGetAction('/test_quizzes/test_quizzes_pass_quiz_component/index',
			array('method' => 'assertNotEmpty'), null, 'view');
		$pattern = '/' . preg_quote('Controller/Component/TestQuizzesPassQuizComponent', '/') . '/';
		$this->assertRegExp($pattern, $this->view);

		$this->_mockForReturn('Session', 'read', '11,12,13', 1);

		//テスト実行
		$result = $this->controller->QuizzesPassQuiz->getPassQuizKeys();
		$this->assertEqual($result, array(11, 12, 13));
		//もう一回呼ぶ
		$result = $this->controller->QuizzesPassQuiz->getPassQuizKeys();
		$this->assertEqual($result, array(11, 12, 13));
	}

}
