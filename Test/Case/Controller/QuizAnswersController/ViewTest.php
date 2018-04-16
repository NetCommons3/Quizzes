<?php
/**
 * QuizAnswersController::view()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsControllerTestCase', 'NetCommons.TestSuite');

/**
 * QuizAnswersController::view()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\Controller\QuizAnswersController
 */
class QuizAnswersControllerViewTest extends NetCommonsControllerTestCase {

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
		'plugin.quizzes.block_setting_for_quiz',
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
				'Auth' => array('login'),
				'Session',
				'Security',
				'NetCommons.Permission',
				'AuthorizationKeys.AuthorizationKey',
				'VisualCaptcha.VisualCaptcha',
				'Quizzes.QuizzesOwnAnswerQuiz',	// 回答済み小テスト管理
				'Quizzes.QuizzesOwnAnswer',		// 回答ID管理
				'Quizzes.QuizzesPassQuiz',		// 合格小テスト管理
				'Quizzes.QuizzesAnswerStart',
				//'Quizzes.QuizzesShuffle',
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
			'action' => 'view',
			'frame_id' => $frameId,
			'block_id' => $blockId,
			'key' => $contentKey,
		);

		return $data;
	}

/**
 * viewアクションのテスト
 * 未ログイン：スタート画面を終えてない：スタート画面へリダイレクトする
 *
 * @return void
 */
	public function testViewGard() {
		$data = $this->__data();
		$urlOptions = Hash::insert($data, 'key', '64f129efa1cc9f1f21feaa7052f3b86c');
		//テスト実行
		$url = Hash::merge(array(
			'plugin' => $this->plugin,
			'controller' => $this->_controller,
			'action' => 'view',
		), $urlOptions);

		$this->_testGetAction($url, null);
		$location = $this->headers['Location'];
		$this->assertTextContains('/quizzes/quiz_answers/start/2/64f129efa1cc9f1f21feaa7052f3b86c', $location);
	}
/**
 * viewアクションのテスト
 * 未ログイン：スタート画面を終えている：スタート画面をクリアしていたらあるはずのサマリがない
 *
 * @return void
 */
	public function testViewNoSummary() {
		$data = $this->__data();
		$urlOptions = Hash::insert($data, 'key', '64f129efa1cc9f1f21feaa7052f3b86c');
		//テスト実行
		$url = Hash::merge(array(
			'plugin' => $this->plugin,
			'controller' => $this->_controller,
			'action' => 'view',
		), $urlOptions);

		$this->_targetController->QuizzesAnswerStart->expects($this->any())
			->method('checkStartedQuizKeys')
			->will($this->returnValue(true));

		$this->setExpectedException('BadRequestException');

		$this->_testGetAction($url, null);
	}
/**
 * viewアクションのテスト
 * 未ログイン：スタート画面を終えている：回答画面が表示される
 *
 * @return void
 */
	public function testView() {
		$data = $this->__data();
		$urlOptions = Hash::insert($data, 'key', '64f129efa1cc9f1f21feaa7052f3b86c');
		//テスト実行
		$url = Hash::merge(array(
			'plugin' => $this->plugin,
			'controller' => $this->_controller,
			'action' => 'view',
		), $urlOptions);

		$this->_targetController->QuizzesAnswerStart->expects($this->any())
			->method('checkStartedQuizKeys')
			->will($this->returnValue(true));
		$this->_targetController->QuizzesOwnAnswerQuiz->expects($this->any())
			->method('getProgressiveSummaryOfThisUser')
			->will($this->returnValue(array('QuizAnswerSummary' => array('id' => 11))));

		$this->_testGetAction($url, null);
		$this->assertInput('input',
			'data[QuizAnswer][0984f470eb7a6453b8ed8f9602fa8744][0][answer_value]',
			null, $this->view);
	}
/**
 * viewアクションのテスト
 * 未ログイン：解答をPOSTする
 *
 * @return void
 */
	public function testViewPost() {
		$data = array(
			'data' => array(
				'Frame' => array('id' => 6),
				'Block' => array('id' => 2),
				'QuizPage' => array('page_sequence' => 0),
				'QuizAnswer' => array(
					'0984f470eb7a6453b8ed8f9602fa8744' => array(
						array(
							'answer_value' => '新規選択肢1',
							'quiz_question_key' => '0984f470eb7a6453b8ed8f9602fa8744'
						),
					)
				)
			)
		);

		$this->_targetController->QuizzesAnswerStart->expects($this->any())
			->method('checkStartedQuizKeys')
			->will($this->returnValue(true));
		$this->_targetController->QuizzesOwnAnswerQuiz->expects($this->any())
			->method('getProgressiveSummaryOfThisUser')
			->will($this->returnValue(array('QuizAnswerSummary' => array('id' => 11))));

		$this->_testPostAction('post', $data, array(
			'action' => 'view',
			'frame_id' => 6,
			'block_id' => 2,
			'key' => '64f129efa1cc9f1f21feaa7052f3b86c'));

		$result = $this->headers['Location'];

		$this->assertTextContains('confirm', $result);
	}
}
