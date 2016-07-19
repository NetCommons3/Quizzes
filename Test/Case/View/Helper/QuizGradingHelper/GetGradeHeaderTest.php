<?php
/**
 * QuizGradingHelper::getGradeHeader()のテスト
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
 * QuizGradingHelper::getGradeHeader()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\View\Helper\QuizGradingHelper
 */
class QuizGradingHelperGetGradeHeaderTest extends NetCommonsHelperTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array();

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
		$this->loadHelper('Quizzes.QuizGrading', $viewVars, $requestData, $params);
	}

/**
 * getGradeHeader()のテスト
 *
 * @return void
 */
	public function testGetGradeHeaderPass() {
		//データ生成
		$quiz = array(
			'Quiz' => array(
				'estimated_time' => 100
			)
		);
		$gradePass = QuizzesComponent::STATUS_GRADE_PASS;
		$summary = array(
			'QuizAnswerSummary' => array(
				'summary_score' => 10,
				'is_grade_finished' => true,
				'elapsed_second' => 9999,
			)
		);

		//テスト実施
		$result = $this->QuizGrading->getGradeHeader($quiz, $gradePass, $summary);

		//チェック
		$expected = '<div class="alert alert-success h1"><small><span class="text-success">';
		$this->assertTextContains($expected, $result);
	}
/**
 * getGradeHeader()のテスト
 *
 * @return void
 */
	public function testGetGradeHeaderFail() {
		//データ生成
		$quiz = array(
			'Quiz' => array(
				'estimated_time' => 100
			)
		);
		$gradePass = QuizzesComponent::STATUS_GRADE_FAIL;
		$summary = array(
			'QuizAnswerSummary' => array(
				'summary_score' => 10,
				'is_grade_finished' => true,
				'elapsed_second' => 9999,
			)
		);

		//テスト実施
		$result = $this->QuizGrading->getGradeHeader($quiz, $gradePass, $summary);

		//チェック
		$expected = '<div class="alert alert-danger h1"><small><span class="text-danger">';
		$this->assertTextContains($expected, $result);
	}

}
