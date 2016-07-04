<?php
/**
 * QuizAnswersController::confirm()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsControllerTestCase', 'NetCommons.TestSuite');

/**
 * QuizAnswersController::confirm()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\Controller\QuizAnswersController
 */
class QuizAnswersControllerConfirmTest extends NetCommonsControllerTestCase {

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
				//'Quizzes.QuizzesOwnAnswerQuiz',	// 回答済み小テスト管理
				//'Quizzes.QuizzesOwnAnswer',		// 回答ID管理
				//'Quizzes.QuizzesPassQuiz',		// 合格小テスト管理
				'Quizzes.QuizzesAnswerStart',
			)
		));
	}

/**
 * テストDataの取得
 *
 * @return array
 */
	private function __data() {
		$frameId = '6';
		$blockId = '2';
		$contentKey = 'content_key_1';

		$data = array(
			'action' => 'confirm',
			'frame_id' => $frameId,
			'block_id' => $blockId,
			'key' => $contentKey,
		);

		return $data;
	}

/**
 * confirmアクションのテスト
 * 一般ログイン：スタート画面を終えてない：スタート画面へリダイレクトする
 *
 * @return void
 */
	public function testConfirmGard() {
		$data = $this->__data();
		$urlOptions = Hash::insert($data, 'key', '5cd22110e513bf7e3964b223212c329e');
		//テスト実行
		$url = Hash::merge(array(
			'plugin' => $this->plugin,
			'controller' => $this->_controller,
			'action' => 'confirm',
		), $urlOptions);

		TestAuthGeneral::login($this, Role::ROOM_ROLE_KEY_GENERAL_USER);

		$this->_testGetAction($url, null);
		$location = $this->headers['Location'];
		$this->assertTextContains('/quizzes/quiz_answers/start/2/5cd22110e513bf7e3964b223212c329e', $location);

		//ログアウト
		TestAuthGeneral::logout($this);
	}
/**
 * confirmアクションのテスト
 * 一般ログイン：スタート画面を終えている：スタート画面をクリアしていたらあるはずのサマリがない
 *
 * @return void
 */
	public function testConfirmNoSummary() {
		$data = $this->__data();
		$urlOptions = Hash::insert($data, 'key', '5cd22110e513bf7e3964b223212c329e');
		//テスト実行
		$url = Hash::merge(array(
			'plugin' => $this->plugin,
			'controller' => $this->_controller,
			'action' => 'confirm',
		), $urlOptions);

		TestAuthGeneral::login($this, Role::ROOM_ROLE_KEY_GENERAL_USER);

		$this->_targetController->QuizzesAnswerStart->expects($this->any())
			->method('checkStartedQuizKeys')
			->will($this->returnValue(true));

		$this->setExpectedException('BadRequestException');

		$this->_testGetAction($url, null);

		//ログアウト
		TestAuthGeneral::logout($this);
	}
/**
 * confirmアクションのテスト
 * 一般ログイン：スタート画面を終えている：確認画面が表示される
 *
 * @return void
 */
	public function testConfirm() {
		$data = $this->__data();
		$urlOptions = Hash::insert($data, 'key', '5cd22110e513bf7e3964b223212c329e');
		//テスト実行
		$url = Hash::merge(array(
			'plugin' => $this->plugin,
			'controller' => $this->_controller,
			'action' => 'confirm',
		), $urlOptions);

		TestAuthGeneral::login($this, Role::ROOM_ROLE_KEY_GENERAL_USER);

		$this->__saveTestAnswer();

		$this->_targetController->QuizzesAnswerStart->expects($this->any())
			->method('checkStartedQuizKeys')
			->will($this->returnValue(true));
		$this->_targetController->Session->expects($this->any())
			->method('read')
			->will($this->returnValueMap(
				[['Quizzes.progressiveSummary.5cd22110e513bf7e3964b223212c329e', 36]]));

		$this->_testGetAction($url, null);
		$this->assertInput('input',
			'data[QuizAnswer][29e1400259c473b2d87ad4a33b01c9fa][0][answer_value][]',
			null, $this->view);
		$this->assertInput('button', 'confirm_quiz', null, $this->view);

		//ログアウト
		TestAuthGeneral::logout($this);
	}
/**
 * Confirmアクションのテスト
 * 一般ログイン：解答をPOSTする
 *
 * @return void
 */
	public function testConfirmPost() {
		$data = array(
			'data' => array(
				'Frame' => array('id' => 6),
				'Block' => array('id' => 2),
				'QuizAnswerSummary' => array('id' => 36),
			)
		);

		TestAuthGeneral::login($this, Role::ROOM_ROLE_KEY_GENERAL_USER);

		$this->__saveTestAnswer();

		$this->_targetController->QuizzesAnswerStart->expects($this->any())
			->method('checkStartedQuizKeys')
			->will($this->returnValue(true));
		$this->_targetController->Session->expects($this->any())
			->method('read')
			->will($this->returnValueMap(
				[['Quizzes.progressiveSummary.5cd22110e513bf7e3964b223212c329e', 36]]));

		$this->_testPostAction('post', $data, array(
			'action' => 'confirm',
			'frame_id' => 6,
			'block_id' => 2,
			'key' => '5cd22110e513bf7e3964b223212c329e'));

		$result = $this->headers['Location'];

		$this->assertTextContains('grading', $result);

		//ログアウト
		TestAuthGeneral::logout($this);
	}
/**
 * Confirmアクションのテスト
 * 一般ログイン：違う解答をPOSTする
 *
 * @return void
 */
	public function testConfirmPostNG() {
		$data = array(
			'data' => array(
				'Frame' => array('id' => 6),
				'Block' => array('id' => 2),
				'QuizAnswerSummary' => array('id' => 35),
			)
		);

		TestAuthGeneral::login($this, Role::ROOM_ROLE_KEY_GENERAL_USER);

		$this->__saveTestAnswer();

		$this->_targetController->QuizzesAnswerStart->expects($this->any())
			->method('checkStartedQuizKeys')
			->will($this->returnValue(true));
		$this->_targetController->Session->expects($this->any())
			->method('read')
			->will($this->returnValueMap(
				[['Quizzes.progressiveSummary.5cd22110e513bf7e3964b223212c329e', 36]]));

		$this->setExpectedException('BadRequestException');

		$this->_testPostAction('post', $data, array(
			'action' => 'confirm',
			'frame_id' => 6,
			'block_id' => 2,
			'key' => '5cd22110e513bf7e3964b223212c329e'));

		//ログアウト
		TestAuthGeneral::logout($this);
	}
/**
 * Confirmアクションのテスト
 * 一般ログイン：POSTでSummaryの保存に失敗する
 *
 * @return void
 */
	public function testConfirmPostSaveSummaryNG() {
		$data = array(
			'data' => array(
				'Frame' => array('id' => 6),
				'Block' => array('id' => 2),
				'QuizAnswerSummary' => array('id' => 36),
			)
		);

		TestAuthGeneral::login($this, Role::ROOM_ROLE_KEY_GENERAL_USER);

		$this->__saveTestAnswer();

		$this->_targetController->QuizzesAnswerStart->expects($this->any())
			->method('checkStartedQuizKeys')
			->will($this->returnValue(true));
		$this->_targetController->Session->expects($this->any())
			->method('read')
			->will($this->returnValueMap(
				[['Quizzes.progressiveSummary.5cd22110e513bf7e3964b223212c329e', 36]]));
		$this->_mockForReturn('QuizAnswerSummary', 'saveEndSummary', false, 1);

		$this->setExpectedException('BadRequestException');

		$this->_testPostAction('post', $data, array(
			'action' => 'confirm',
			'frame_id' => 6,
			'block_id' => 2,
			'key' => '5cd22110e513bf7e3964b223212c329e'));

		//ログアウト
		TestAuthGeneral::logout($this);
	}

/**
 * テストAnswerDataの取得
 *
 * @return void
 */
	private function __saveTestAnswer() {
		$this->QuizAnswerSummary = ClassRegistry::init('Quizzes.QuizAnswerSummary');
		$summary = array(
			'QuizAnswerSummary' => array(
				'answer_status' => 1,
				'test_status' => 0,
				'is_grade_finished' => false,
				'summary_score' => 0,
				'passing_status' => 0,
				'quiz_key' => '5cd22110e513bf7e3964b223212c329e'
		));
		$summary = $this->QuizAnswerSummary->save($summary);
		$this->QuizAnswer = ClassRegistry::init('Quizzes.QuizAnswer');
		$this->QuizAnswer->Behaviors->unload('QuizAnswerValidate');
		//$this->QuizAnswer->Behaviors->unload('QuizAnswerScore');
		$answer = array(
			'QuizAnswer' => array(
				'answer_value' => '新規選択肢1',
				'answer_correct_status' => '2',
				'correct_status' => '1',
				'score' => 0,
				'quiz_answer_summary_id' => $summary['QuizAnswerSummary']['id'],
				'quiz_question_key' => '29e1400259c473b2d87ad4a33b01c9fa'
			));
		$this->QuizAnswer->save($answer);
	}

}
