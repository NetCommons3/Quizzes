<?php
/**
 * QuizGradingHelper::getToGrade()のテスト
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
 * QuizGradingHelper::getToGrade()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\View\Helper\QuizGradingHelper
 */
class QuizGradingHelperGetToGradeTest extends NetCommonsHelperTestCase {

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
 * getToGrade()のテスト
 *
 * @return void
 */
	public function testGetToGrade() {
		//データ生成
		$quiz = null;
		$summary = null;
		$pageIndex = null;
		$questionIndex = null;
		$question = array(
			'question_type' => QuizzesComponent::TYPE_SELECTION
		);
		$answer = null;
		Current::$current['Permission']['content_editable']['value'] = true;

		//テスト実施
		$result = $this->QuizGrading->getToGrade($quiz, $summary, $pageIndex, $questionIndex, $question, $answer);

		//チェック
		$this->assertEmpty($result);
	}

}
