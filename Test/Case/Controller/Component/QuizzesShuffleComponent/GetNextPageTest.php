<?php
/**
 * QuizzesShuffleComponent::getNextPage()のテスト
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
 * QuizzesShuffleComponent::getNextPage()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\Controller\Component\QuizzesShuffleComponent
 */
class QuizzesShuffleComponentGetNextPageTest extends NetCommonsControllerTestCase {

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
 * getNextPage()のテスト
 *
 * @return void
 */
	public function testGetNextPage() {
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
		$indexes = array();
		foreach ($quiz['QuizPage'] as $index => $page) {
			$indexes[] = $page['page_sequence'];
		}
		$pageCount = count($quiz['QuizPage']);
		$indexes[] = 999;
		foreach ($indexes as $index => $seq) {
			$result = $this->controller->QuizzesShuffle->getNextPage($quiz, $seq);
			if ($index < $pageCount - 1) {
				$this->assertEqual($result, $index + 1);
			} else {
				$this->assertFalse($result);
			}
		}
	}

}
