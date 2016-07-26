<?php
/**
 * QuizBlocksController::download()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsControllerTestCase', 'NetCommons.TestSuite');

/**
 * QuizBlocksController::download()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\Controller\QuizBlocksController
 */
class QuizBlocksControllerDownloadTest extends NetCommonsControllerTestCase {

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
		'plugin.workflow.workflow_comment',
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
	protected $_controller = 'quiz_blocks';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		//ログイン
		TestAuthGeneral::login($this);

		//テストプラグインのロード
		NetCommonsCakeTestCase::loadTestPlugin($this, 'Quizzes', 'TestQuizzes');
		NetCommonsCakeTestCase::loadTestPlugin($this, 'Quizzes', 'TestFiles');

		//テストコントローラ生成
		$this->generateNc('TestQuizzes.TestQuizBlocks');
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
 * download()のテスト
 *
 * @return void
 */
	public function testDownload() {
		//テスト実施
		$frameId = '6';
		$blockId = '2';
		$url = array(
			'plugin' => 'test_quizzes',
			'controller' => 'test_quiz_blocks',
			'action' => 'download',
			'block_id' => $blockId,
			'key' => '83b294e176a8c8026d4fbdb07ad2ed7f',
			'frame_id' => $frameId
		);
		$this->_testPostAction('post', array(
			'AuthorizationKey' => array(
				'authorization_key' => 'ABC'
			)
		),
			$url);
		//チェック
		$this->assertTextEquals(rawurlencode('テストパターン３４.zip'), $this->controller->returnValue[0]);
		$this->assertTextEquals('テストパターン３４.csv', $this->controller->returnValue[1]);
		$this->assertTextEquals('ABC', $this->controller->returnValue[2]);
		$this->assertEqual(count($this->controller->returnValue[3]), 10);	// header line + 8 records
	}
/**
 * download()のgetテスト
 *
 * @return void
 */
	public function testIndexNoneFrameBlock() {
		//テスト実施
		// フレーム、ブロック指定なし
		$url = array(
			'plugin' => 'test_quizzes',
			'controller' => 'test_quiz_blocks',
			'action' => 'download',
			'key' => '83b294e176a8c8026d4fbdb07ad2ed7f',
		);

		$this->_testPostAction('post', array(
			'AuthorizationKey' => array(
				'authorization_key' => 'ABC'
			)
		), $url, 'NotFoundException');
	}
/**
 * download()の不正小テスト指定テスト
 *
 * 一度も発行されたことのない小テストはCSVを入手できない
 * 存在しない小テスト
 *
 * @return void
 */
	public function testNoPublish() {
		$frameId = '6';
		$blockId = '2';
		$url = array(
			'plugin' => 'test_quizzes',
			'controller' => 'test_quiz_blocks',
			'action' => 'download',
			'block_id' => $blockId,
			'key' => '4f02540a2a10aeffbcc079e73961d4ad',
			'frame_id' => $frameId
		);
		$this->controller->Session->expects($this->once())
			->method('setFlash')
			->with(__d('quizzes', 'Designation of the quiz does not exist.'));
		$result = $this->_testPostAction('post', array(
			'AuthorizationKey' => array(
				'authorization_key' => 'ABC'
			)
		), $url);
		//$flash = CakeSession::read('Message.flash');
		$this->assertEmpty($result);
	}
/**
 * download()の圧縮パスワードなし指定テスト
 *
 * @return void
 */
	public function testNoPassword() {
		$frameId = '6';
		$blockId = '2';
		$url = array(
			'plugin' => 'test_quizzes',
			'controller' => 'test_quiz_blocks',
			'action' => 'download',
			'block_id' => $blockId,
			'key' => '83b294e176a8c8026d4fbdb07ad2ed7f',
			'frame_id' => $frameId
		);
		$this->controller->Session->expects($this->once())
			->method('setFlash')
			->with(__d('quizzes', 'Setting of password is required always to download answers.'));
		$result = $this->_testPostAction('post', array(
			'AuthorizationKey' => array(
				'authorization_key' => ''
			)
		), $url);
		$this->assertEmpty($result);
	}
/**
 * download()のファイル作成異常テスト
 *
 * @return void
 */
	public function testException() {
		$mock = $this->getMockForModel('Quizzes.QuizAnswerSummaryCsv', array('getAnswerSummaryCsv'));
		$mock->expects($this->once())
			->method('getAnswerSummaryCsv')
			->will($this->throwException(new Exception));
		$frameId = '6';
		$blockId = '2';
		$url = array(
			'plugin' => 'test_quizzes',
			'controller' => 'test_quiz_blocks',
			'action' => 'download',
			'block_id' => $blockId,
			'key' => '83b294e176a8c8026d4fbdb07ad2ed7f',
			'frame_id' => $frameId
		);
		$this->controller->Session->expects($this->once())
			->method('setFlash')
			->with(__d('quizzes', 'download error'));
		$this->_testPostAction('post', array(
			'AuthorizationKey' => array(
				'authorization_key' => 'ABC'
			)
		), $url);
	}

}
