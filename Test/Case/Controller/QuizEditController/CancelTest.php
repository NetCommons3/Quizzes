<?php
/**
 * QuizEditController::cancel()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsControllerTestCase', 'NetCommons.TestSuite');

/**
 * QuizEditController::cancel()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\Controller\QuizEditController
 */
class QuizEditControllerCancelTest extends NetCommonsControllerTestCase {

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
	protected $_controller = 'quiz_edit';

/**
 * testCancel
 *
 * @return void
 */
	public function testCancel() {
		//テスト実施
		$urlOptions = array(
			'action' => 'cancel', 'block_id' => 2, 'frame_id' => 6
		);
		$url = Hash::merge(array(
			'plugin' => $this->plugin,
			'controller' => $this->_controller,
		), $urlOptions);
		$this->_testNcAction($url, array('method' => 'get'), null, 'view');
		$result = $this->headers['Location'];
		$this->assertNotEmpty($result);
	}
}
