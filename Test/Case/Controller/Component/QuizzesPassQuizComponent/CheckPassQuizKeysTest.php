<?php
/**
 * QuizzesPassQuizComponent::checkPassQuizKeys()のテスト
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
 * QuizzesPassQuizComponent::checkPassQuizKeys()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\Controller\Component\QuizzesPassQuizComponent
 */
class QuizzesPassQuizComponentCheckPassQuizKeysTest extends NetCommonsControllerTestCase {

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
 * checkPassQuizKeys()のテスト
 *
 * @return void
 */
	public function testCheckPassQuizKeys() {
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
		$result = $this->controller->QuizzesPassQuiz->checkPassQuizKeys('quiz_1');
		$this->assertFalse($result);
		//テスト実行
		$result = $this->controller->QuizzesPassQuiz->savePassQuizKeys('quiz_1');
		$result = $this->controller->QuizzesPassQuiz->checkPassQuizKeys('quiz_1');
		$this->assertTrue($result);
	}

}
