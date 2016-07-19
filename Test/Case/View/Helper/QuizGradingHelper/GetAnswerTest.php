<?php
/**
 * QuizGradingHelper::getAnswer()のテスト
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
 * QuizGradingHelper::getAnswer()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\View\Helper\QuizGradingHelper
 */
class QuizGradingHelperGetAnswerTest extends NetCommonsHelperTestCase {

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
 * getAnswer()のテスト
 *
 * @return void
 */
	public function testGetAnswer() {
		//データ生成
		$question = array(
			'question_type' => QuizzesComponent::TYPE_MULTIPLE_WORD
		);
		$answer = array(
			'answer_value' => array(
				0 => 'answer1',
				1 => 'answer2',
				2 => 'answer3',
				3 => 'answer4',
			),
			'answer_correct_status' => array(
				0 => 0,
				1 => 1,
				2 => 2,
			)
		);

		//テスト実施
		$result = $this->QuizGrading->getAnswer($question, $answer);

		$expected = '<dt>' . __d('quizzes', 'your score') . '</dt>';
		$expected .= '<dd> (1) answer1 <br /><span class="label label-warning">Ｘ</span>' .
			' (2) answer2 <br /><span class="label label-success">◯</span>' .
			' (3) answer3 <br /><span class="label label-warning">Ｘ</span>' .
			' (4) answer4 <br /></dd>';
		//チェック
		$this->assertTextEquals($result, $expected);
	}
}
