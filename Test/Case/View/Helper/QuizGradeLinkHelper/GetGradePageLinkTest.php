<?php
/**
 * QuizGradeLinkHelper::getGradePageLink()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsHelperTestCase', 'NetCommons.TestSuite');
App::uses('QuizzesComponent', 'Quizzes.Controller/Component');

/**
 * QuizGradeLinkHelper::getGradePageLink()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\View\Helper\QuizGradeLinkHelper
 */
class QuizGradeLinkHelperGetGradePageLinkTest extends NetCommonsHelperTestCase {

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
		$this->loadHelper('Quizzes.QuizGradeLink', $viewVars, $requestData, $params);
	}
/**
 * getGradePageLink()のテスト
 *
 * @return void
 */
	public function testGetGradePageLink() {
		//データ生成
		$quiz = array(
			'Quiz' => array(
				'key' => 'test_data_key',
			)
		);
		Current::$current['Permission']['block_editable']['value'] = true;
		//Current::$current['Permission']['content_publishable']['value'] = true;
		//Current::$current['Permission']['content_editable']['value'] = true;
		//テスト実施
		$result = $this->QuizGradeLink->getGradePageLink($quiz);

		//チェック
		$expected = '<a href="/quiz_result/index/test_data_key"><span class="text-danger">' .
			__d('quizzes', '! There is a non-scoring of data') . '</span></a>';
		$this->assertTextEquals($result, $expected);
	}

}