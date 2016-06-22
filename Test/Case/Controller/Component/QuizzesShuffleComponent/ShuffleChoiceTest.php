<?php
/**
 * QuizzesShuffleComponent::shuffleChoice()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsControllerTestCase', 'NetCommons.TestSuite');
App::uses('QuizzesComponent', 'Quizzes.Controller/Component');
App::uses('QuizDataGetTest', 'Quizzes.TestSuite');

/**
 * QuizzesShuffleComponent::shuffleChoice()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\Controller\Component\QuizzesShuffleComponent
 */
class QuizzesShuffleComponentShuffleChoiceTest extends NetCommonsControllerTestCase {

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
 * shuffleChoice()のテスト
 *
 * @return void
 */
	public function testShuffleChoice() {
		//テストコントローラ生成
		$this->generateNc('TestQuizzes.TestQuizzesShuffleComponent');

		//ログイン
		TestAuthGeneral::login($this);

		//テストアクション実行
		$this->_testGetAction('/test_quizzes/test_quizzes_shuffle_component/index',
				array('method' => 'assertNotEmpty'), null, 'view');
		$pattern = '/' . preg_quote('Controller/Component/TestQuizzesShuffleComponent', '/') . '/';
		$this->assertRegExp($pattern, $this->view);

		$dataGet = new QuizDataGetTest();
		$orgQuiz = $quiz = $dataGet->getData(15);

		//テスト実行
		// 変更なしパターン
		$this->controller->QuizzesShuffle->shuffleChoice($quiz);
		$this->assertEqual($orgQuiz, $quiz);
	}
/**
 * shuffleChoice()のテスト
 *
 * @return void
 */
	public function testShuffleChoiceRandom() {
		//テストコントローラ生成
		$this->generateNc('TestQuizzes.TestQuizzesShuffleComponent');

		//ログイン
		TestAuthGeneral::login($this);

		//テストアクション実行
		$this->_testGetAction('/test_quizzes/test_quizzes_shuffle_component/index',
			array('method' => 'assertNotEmpty'), null, 'view');
		$pattern = '/' . preg_quote('Controller/Component/TestQuizzesShuffleComponent', '/') . '/';
		$this->assertRegExp($pattern, $this->view);

		$dataGet = new QuizDataGetTest();
		$quiz = $dataGet->getData(15);
		$quiz['QuizPage'][0]['QuizQuestion'][0]['is_choice_random'] = QuizzesComponent::USES_USE;
		//テスト実行
		// 変更なしパターン
		$this->controller->QuizzesShuffle->shuffleChoice($quiz);
	}
/**
 * shuffleChoice()のテスト
 *
 * @return void
 */
	public function testShuffleChoiceRandomFromSession() {
		//テストコントローラ生成
		$this->generateNc('TestQuizzes.TestQuizzesShuffleComponent');

		//ログイン
		TestAuthGeneral::login($this);

		//テストアクション実行
		$this->_testGetAction('/test_quizzes/test_quizzes_shuffle_component/index',
			array('method' => 'assertNotEmpty'), null, 'view');
		$pattern = '/' . preg_quote('Controller/Component/TestQuizzesShuffleComponent', '/') . '/';
		$this->assertRegExp($pattern, $this->view);

		$dataGet = new QuizDataGetTest();
		$quiz = $dataGet->getData(15);
		$quiz['QuizPage'][0]['QuizQuestion'][0]['is_choice_random'] = QuizzesComponent::USES_USE;
		$this->_mockForReturn('Session', 'check', true, 1);
		$this->_mockForReturn('Session', 'read', array(1, 2, 3), 1);
		//テスト実行
		// 変更なしパターン
		$this->controller->QuizzesShuffle->shuffleChoice($quiz);
		$this->assertEqual($quiz['QuizPage'][0]['QuizQuestion'][0]['QuizChoice'], array(1, 2, 3));
	}

}
