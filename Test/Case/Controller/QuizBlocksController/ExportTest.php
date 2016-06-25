<?php
/**
 * QuizBlocksController::export()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsControllerTestCase', 'NetCommons.TestSuite');

/**
 * QuizBlocksController::export()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\Controller\QuizBlocksController
 */
class QuizBlocksControllerExportTest extends NetCommonsControllerTestCase {

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

		$this->controller->Quiz->Behaviors->unload('Workflow.Workflow');
		$this->controller->Quiz->Behaviors->unload('Workflow.WorkflowComment');
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
 * export()アクションのGetリクエストテスト
 *
 * @return void
 */
	public function testExportGet() {
		//テスト実施
		$frameId = '6';
		$blockId = '2';
		$url = array(
			'plugin' => 'test_quizzes',
			'controller' => 'test_quiz_blocks',
			'action' => 'export',
			'block_id' => $blockId,
			'key' => 'acc5e94c9617ed332cc2ef4d013ae686',
			'frame_id' => $frameId
		);
		$this->_testNcAction($url);
		//チェック
		$this->assertTextEquals(rawurlencode('テストパターン９.zip'), $this->controller->returnValue);
	}
/**
 * export()のgetテスト
 *
 * @return void
 */
	public function testIndexNoneFrameBlock() {
		//テスト実施
		// フレーム、ブロック指定なし
		$url = array(
			'plugin' => 'test_quizzes',
			'controller' => 'test_quiz_blocks',
			'action' => 'export',
			'key' => 'acc5e94c9617ed332cc2ef4d013ae686',
		);

		$this->_testNcAction($url, array(), 'NotFoundException');
	}
/**
 * export()の不正アンケート指定テスト
 *
 * 一度も発行されたことのない小テストはテンプレートを入手できない
 * 存在しないテンプレート
 *
 * @return void
 */
	public function testNoPublish() {
		$frameId = '6';
		$blockId = '2';
		$url = array(
			'plugin' => 'test_quizzes',
			'controller' => 'test_quiz_blocks',
			'action' => 'export',
			'block_id' => $blockId,
			'key' => '4f02540a2a10aeffbcc079e73961d4ad',
			'frame_id' => $frameId
		);
		$this->controller->Session->expects($this->once())
			->method('setFlash')
			->with(__d('quizzes', 'Designation of the quiz does not exist.'));
		$result = $this->_testNcAction($url);
		$this->assertEmpty($result);
	}
/**
 * export()のファイル作成異常テスト
 *
 * @return void
 */
	public function testException() {
		$mock = $this->getMockForModel('Quizzes.QuizExport', array('getExportData'));
		$mock->expects($this->once())
			->method('getExportData')
			->will($this->throwException(new Exception));
		$frameId = '6';
		$blockId = '2';
		$url = array(
			'plugin' => 'test_quizzes',
			'controller' => 'test_quiz_blocks',
			'action' => 'export',
			'block_id' => $blockId,
			'key' => 'acc5e94c9617ed332cc2ef4d013ae686',
			'frame_id' => $frameId
		);
		$this->controller->Session->expects($this->once())
			->method('setFlash')
			->with(__d('quizzes', 'export error'));
		$this->_testNcAction($url);
	}
}
