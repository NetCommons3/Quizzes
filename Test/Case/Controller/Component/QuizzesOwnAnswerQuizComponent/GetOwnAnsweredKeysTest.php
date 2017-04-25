<?php
/**
 * QuizzesOwnAnswerQuizComponent::getOwnAnsweredKeys()のテスト
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
 * QuizzesOwnAnswerQuizComponent::getOwnAnsweredKeys()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\Controller\Component\QuizzesOwnAnswerQuizComponent
 */
class QuizzesOwnAnswerQuizComponentGetOwnAnsweredKeysTest extends NetCommonsControllerTestCase {

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
 * getOwnAnsweredKeys()のテスト
 *
 * @return void
 */
	public function testGetOwnAnsweredKeys() {
		//テストコントローラ生成
		$this->generateNc('TestQuizzes.TestQuizzesOwnAnswerQuizComponent');

		//ログイン
		TestAuthGeneral::login($this);

		//テストアクション実行
		$this->_testGetAction('/test_quizzes/test_quizzes_own_answer_quiz_component/index',
				array('method' => 'assertNotEmpty'), null, 'view');
		$pattern = '/' . preg_quote('Controller/Component/TestQuizzesOwnAnswerQuizComponent', '/') . '/';
		$this->assertRegExp($pattern, $this->view);

		//テスト実行
		$result = $this->controller->QuizzesOwnAnswerQuiz->getOwnAnsweredKeys();
		$this->assertEmpty($result);
		// もう一回呼ぶと作成済みのリストを返す
		$result = $this->controller->QuizzesOwnAnswerQuiz->getOwnAnsweredKeys();
		$this->assertEmpty($result);
	}
/**
 * getOwnAnsweredKeys()のテスト
 *
 * @return void
 */
	public function testGetOwnAnsweredKeys2() {
		//テストコントローラ生成
		$this->generateNc('TestQuizzes.TestQuizzesOwnAnswerQuizComponent');

		//ログイン
		TestAuthGeneral::login($this, Role::ROOM_ROLE_KEY_GENERAL_USER);

		//テストアクション実行
		$this->_testGetAction('/test_quizzes/test_quizzes_own_answer_quiz_component/index_with_login',
			array('method' => 'assertNotEmpty'), null, 'view');
		$pattern = '/' . preg_quote('Controller/Component/TestQuizzesOwnAnswerQuizComponent', '/') . '/';
		$this->assertRegExp($pattern, $this->view);

		//テスト実行
		$result = $this->controller->QuizzesOwnAnswerQuiz->getOwnAnsweredKeys();
		$this->assertEqual($result, array(
			'83b294e176a8c8026d4fbdb07ad2ed7f',
			'c389a74ef01516f9b3e477afcf3dfa02'));
	}
/**
 * getOwnAnsweredKeys()のテスト
 * 未ログイン
 *
 * @return void
 */
	public function testGetOwnAnsweredKeysNoLogin() {
		//テストコントローラ生成
		$this->generateNc('TestQuizzes.TestQuizzesOwnAnswerQuizComponent');

		//テストアクション実行
		$this->_testGetAction('/test_quizzes/test_quizzes_own_answer_quiz_component/index',
			array('method' => 'assertNotEmpty'), null, 'view');
		$pattern = '/' . preg_quote('Controller/Component/TestQuizzesOwnAnswerQuizComponent', '/') . '/';
		$this->assertRegExp($pattern, $this->view);

		//テスト実行
		$result = $this->controller->QuizzesOwnAnswerQuiz->getOwnAnsweredKeys();
		$this->assertEmpty($result);
	}
/**
 * getOwnAnsweredKeys()のテスト
 * 未ログイン
 *
 * @return void
 */
	public function testGetOwnAnsweredKeysNoLogin2() {
		//テストコントローラ生成
		$this->generateNc('TestQuizzes.TestQuizzesOwnAnswerQuizComponent');

		//テストアクション実行
		$this->_testGetAction('/test_quizzes/test_quizzes_own_answer_quiz_component/index',
			array('method' => 'assertNotEmpty'), null, 'view');
		$pattern = '/' . preg_quote('Controller/Component/TestQuizzesOwnAnswerQuizComponent', '/') . '/';
		$this->assertRegExp($pattern, $this->view);

		//テスト実行
		// sessionがarray(quiz_1)を返すように
		$this->_mockForReturn('Session', 'read', array('quiz_1' => 3), 1);
		$result = $this->controller->QuizzesOwnAnswerQuiz->getOwnAnsweredKeys();
		$this->assertEqual($result, array('quiz_1'));
	}

}
