<?php
/**
 * QuizzesShuffleComponent::shufflePage()のテスト
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
 * QuizzesShuffleComponent::shufflePage()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\Controller\Component\QuizzesShuffleComponent
 */
class QuizzesShuffleComponentShufflePageTest extends NetCommonsControllerTestCase {

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
 * shufflePage()のテスト
 *
 * @return void
 */
	public function testShufflePage() {
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

		//テスト実行
		$this->controller->QuizzesShuffle->shufflePage($quiz);
		// shuffleがランダム任せのため、必ずしもオリジナルと違う順番になるという保証がない
		// もしかしたらもともとの順番と同じかもしれない
		// なのでassertは行わないことにします
		/*
		$indexes = array();
		$pageSeq = array();
		foreach ($quiz['QuizPage'] as $index => $page) {
			$indexes[] = $index;
			$pageSeq[] = $page['page_sequence'];
		}
		$this->assertNotEqual($pageSeq, $indexes);
		*/
	}
/**
 * shufflePage()のテスト
 *
 * @return void
 */
	public function testShufflePageReadSession() {
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
		shuffle($quiz['QuizPage']);

		$pages = $quiz['QuizPage'];
		$this->_mockForReturn('Session', 'check', true, 1);
		$this->_mockForReturn('Session', 'read', $pages, 1);

		//テスト実行
		$this->controller->QuizzesShuffle->shufflePage($quiz);
		$this->assertEqual($quiz['QuizPage'], $pages);
	}
/**
 * shufflePage()のテスト
 *
 * @return void
 */
	public function testShufflePageNoShuffle() {
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
		$quiz['Quiz']['is_page_random'] = QuizzesComponent::USES_NOT_USE;

		$pages = $quiz['QuizPage'];

		//テスト実行
		$this->controller->QuizzesShuffle->shufflePage($quiz);
		$qNumber = 0;
		foreach ($quiz['QuizPage'] as $page) {
			foreach ($page['QuizQuestion'] as $q) {
				$this->assertEqual($q['serial_number'], $qNumber);
				$qNumber++;
			}
		}
		$quiz['QuizPage'] = Hash::remove($quiz['QuizPage'], '{n}.QuizQuestion.{n}.serial_number');
		$this->assertEqual($quiz['QuizPage'], $pages);
	}

}
