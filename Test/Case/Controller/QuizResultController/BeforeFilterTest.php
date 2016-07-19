<?php
/**
 * QuizResultController::beforeFilter()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsControllerTestCase', 'NetCommons.TestSuite');

/**
 * QuizResultController::beforeFilter()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\Controller\QuizResultController
 */
class QuizResultControllerBeforeFilterTest extends NetCommonsControllerTestCase {

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
		'plugin.quizzes.quiz_setting',
		'plugin.authorization_keys.authorization_keys',
	);

/**
 * Plugin name
 *
 * @var string
 */
	public $plugin = 'quizzes';

/**
 * Controller name
 *
 * @var string
 */
	protected $_controller = 'quiz_result';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		//ログイン
		TestAuthGeneral::login($this);
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
 * index()アクションのGetリクエストテスト
 *
 * @return void
 */
	public function testBeforeFilterGet() {
		//テスト実行
		$this->_testGetAction(
			array(
				'action' => 'index',
				'frame_id' => 6,
				'block_id' => 2,
				'key' => '83b294e176a8c8026d4fbdb07ad2ed7f'
			),
			array('method' => 'assertNotEmpty'), null, 'view'
		);
	}
/**
 * index()アクションのGetリクエストテスト
 *
 * @return void
 */
	public function testBeforeFilterGetNG() {
		//テスト実行
		$this->setExpectedException('BadRequestException');
		$this->_testGetAction(
			array(
				'action' => 'index',
				'frame_id' => 6,
				'block_id' => 2,
				'key' => 'no_exist_key'
			),
			array('method' => 'assertNotEmpty'), null, 'view'
		);
	}
}
