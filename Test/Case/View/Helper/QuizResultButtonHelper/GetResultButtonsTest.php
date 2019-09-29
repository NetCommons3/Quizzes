<?php
/**
 * QuizResultButtonHelper::getResultButtons()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsHelperTestCase', 'NetCommons.TestSuite');
App::uses('QuizzesComponent', 'Quizzes.Controller/Component');
App::uses('WorkflowComponent', 'Workflow.Controller/Component');

/**
 * QuizResultButtonHelper::getResultButtons()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\View\Helper\QuizResultButtonHelper
 */
class QuizResultButtonHelperGetResultButtonsTest extends NetCommonsHelperTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.quizzes.quiz',
		'plugin.quizzes.quiz_page',
		'plugin.quizzes.quiz_question',
		'plugin.quizzes.quiz_choice',
		'plugin.quizzes.quiz_correct',
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

		//テストデータ生成
		//必要に応じてセットする
		$viewVars = array();
		$requestData = array();
		$params = array();

		//Helperロード
		$this->loadHelper('Quizzes.QuizResultButton', $viewVars, $requestData, $params);
	}

/**
 * getResultButtons()のテスト
 *
 * @return void
 */
	public function testGetResultButtons() {
		//データ生成
		$quiz = array(
			'Quiz' => array(
				'key' => 'test_quiz',
				'status' => WorkflowComponent::STATUS_PUBLISHED,
			)
		);
		Current::write('Room.id', '2');
		Current::writePermission('2', 'content_editable', true);
		Current::writePermission('2', 'block_editable', true);
		//Current::$current['Permission']['content_editable']['value'] = true;
		//Current::$current['Permission']['block_editable']['value'] = true;
		//テスト実施
		$result = $this->QuizResultButton->getResultButtons(
			$quiz,
			array(
				'title' => '&nbsp;' . __d('quizzes', 'Results confirmed'),
				'icon' => 'stats',
				'class' => 'success',
				'size' => 'lg'
			));
		//チェック
		$this->assertTextContains(
			'<a href="/quiz_result/index/test_quiz" class="btn btn-success btn-lg">' .
			'<span class="glyphicon glyphicon-stats" aria-hidden="true"></span>',
			$result);
	}

}