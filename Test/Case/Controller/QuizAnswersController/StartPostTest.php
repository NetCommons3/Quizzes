<?php
/**
 * QuizAnswersController::start()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsControllerTestCase', 'NetCommons.TestSuite');

/**
 * QuizAnswersController::start()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\Controller\QuizAnswersController
 */
class QuizAnswersControllerStartPostTest extends NetCommonsControllerTestCase {

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
	protected $_controller = 'quiz_answers';

/**
 * Controller
 *
 * @var mixed
 */
	protected $_targetController;

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->generateNc(Inflector::camelize($this->_controller));
		$this->_targetController = $this->generate('Quizzes.QuizAnswers', array(
			'components' => array(
				'Auth' => array('user'),
				'Session',
				'Security',
				'NetCommons.Permission',
				'AuthorizationKeys.AuthorizationKey',
				'VisualCaptcha.VisualCaptcha',
				'Quizzes.QuizzesOwnAnswerQuiz',	// 回答済み小テスト管理
				'Quizzes.QuizzesOwnAnswer',		// 回答ID管理
				'Quizzes.QuizzesPassQuiz',		// 合格小テスト管理
				'Quizzes.QuizzesAnswerStart',
				'Quizzes.QuizzesShuffle',
			)
		));
	}

/**
 * アクションのPOSTテスト
 * KeyAuthへPost
 *
 * @return void
 */
	public function testKeyAuthPost() {
		$data = array(
			'data' => array(
				'Frame' => array('id' => 6),
				'Block' => array('id' => 2),
				'AuthorizationKeys' => array('key' => 'test')
			)
		);
		$this->_targetController->AuthorizationKey->expects($this->any())
			->method('check')
			->will($this->returnValue(true));

		TestAuthGeneral::login($this, Role::ROOM_ROLE_KEY_GENERAL_USER);

		$this->_testPostAction('post', $data, array(
			'action' => 'start',
			'frame_id' => 6,
			'block_id' => 2,
			'key' => 'a916437af184b4a185f685a93099adca'));
		$result = $this->headers['Location'];

		$this->assertTextContains('a916437af184b4a185f685a93099adca', $result);

		TestAuthGeneral::logout($this);
	}
/**
 * アクションのPOSTテスト
 * KeyAuthへPost
 *
 * @return void
 */
	public function testKeyAuthPostNG() {
		$data = array(
			'data' => array(
				'Frame' => array('id' => 6),
				'Block' => array('id' => 2),
				'AuthorizationKeys' => array('key' => 'test')
			)
		);
		$this->_targetController->AuthorizationKey->expects($this->any())
			->method('check')
			->will($this->returnValue(false));

		TestAuthGeneral::login($this, Role::ROOM_ROLE_KEY_GENERAL_USER);

		$result = $this->_testPostAction('post', $data, array(
			'action' => 'start',
			'frame_id' => 6,
			'block_id' => 2,
			'key' => 'a916437af184b4a185f685a93099adca'));
		// 認証キーcomponentをMockにしてるからエラーメッセージが入らない
		// 同じ画面を表示していることでエラー画面になっていると判断する
		$this->assertTextContains('/quizzes/quiz_answers/start/', $result);

		TestAuthGeneral::logout($this);
	}
/**
 * アクションのPOSTテスト
 * ImgAuthへPost
 *
 * @return void
 */
	public function testImgAuthPost() {
		$data = array(
			'data' => array(
				'Frame' => array('id' => 6),
				'Block' => array('id' => 2),
				'VisualCaptcha' => array('test' => 'test')	// Mock使うんでなんでもよい
			)
		);
		$this->_targetController->VisualCaptcha->expects($this->any())
			->method('check')
			->will($this->returnValue(true));

		TestAuthGeneral::login($this, Role::ROOM_ROLE_KEY_GENERAL_USER);

		$this->_testPostAction('post', $data, array(
			'action' => 'start',
			'frame_id' => 6,
			'block_id' => 2,
			'key' => 'a2cf0e48f281be7c3cc35f0920f047ca'));
		$result = $this->headers['Location'];

		$this->assertTextContains('a2cf0e48f281be7c3cc35f0920f047ca', $result);

		TestAuthGeneral::logout($this);
	}
/**
 * アクションのPOSTテスト
 * ImgAuthへPost
 *
 * @return void
 */
	public function testImgAuthPostNG() {
		$data = array(
			'data' => array(
				'Frame' => array('id' => 6),
				'Block' => array('id' => 2),
				'VisualCaptcha' => array('test' => 'test')	// Mock使うんでなんでもよい
			)
		);
		$this->_targetController->VisualCaptcha->expects($this->any())
			->method('check')
			->will($this->returnValue(false));

		TestAuthGeneral::login($this, Role::ROOM_ROLE_KEY_GENERAL_USER);

		$result = $this->_testPostAction('post', $data, array(
			'action' => 'start',
			'frame_id' => 6,
			'block_id' => 2,
			'key' => 'a2cf0e48f281be7c3cc35f0920f047ca'));

		$this->assertTextContains('/quizzes/quiz_answers/start/', $result);

		TestAuthGeneral::logout($this);
	}

}
