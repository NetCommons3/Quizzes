<?php
/**
 * QuizAnswerHelper::singleChoice()のテスト
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
 * QuizAnswerHelper::singleChoice()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\View\Helper\QuizAnswerHelper
 */
class QuizAnswerHelperSingleChoiceTest extends NetCommonsHelperTestCase {

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
		$this->loadHelper('Quizzes.QuizAnswer', $viewVars, $requestData, $params);
	}

/**
 * singleChoice()のテスト
 *
 * @return void
 */
	public function testSingleChoice() {
		//データ生成
		$index = 1;
		$fieldName = 'QuizAnswer.' . 1 . '.0.answer_value';
		$question = array(
			'is_choice_horizon' => 1,
			'QuizChoice' => array(
				array(
					'choice_label' => 'test1'
				),
				array(
					'choice_label' => 'test2'
				),
				array(
					'choice_label' => 'test3'
				),
			)
		);
		$readonly = false;

		$expected = '<input type="hidden" name="data[QuizAnswer][1][0][answer_value]" ' .
			'id="QuizAnswer10AnswerValue_" value=""/>' .
			'<div class="radio radio-inline"><label class="control-label">' .
		'<input type="radio" name="data[QuizAnswer][1][0][answer_value]" ' .
			'id="QuizAnswer10AnswerValueTest1" value="test1" />test1</label></div>' .
		'<div class="radio radio-inline"><label class="control-label">' .
		'<input type="radio" name="data[QuizAnswer][1][0][answer_value]" ' .
			'id="QuizAnswer10AnswerValueTest2" value="test2" />test2</label></div>' .
		'<div class="radio radio-inline"><label class="control-label">' .
		'<input type="radio" name="data[QuizAnswer][1][0][answer_value]" ' .
			'id="QuizAnswer10AnswerValueTest3" value="test3" />test3</label></div>';

		//テスト実施
		$result = $this->QuizAnswer->singleChoice($index, $fieldName, $question, $readonly);
		//チェック
		$this->assertTextEquals($expected, $result);
	}

}
