<?php
/**
 * QuizAnswerHelper::textArea()のテスト
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
 * QuizAnswerHelper::textArea()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\View\Helper\QuizAnswerHelper
 */
class QuizAnswerHelperTextAreaTest extends NetCommonsHelperTestCase {

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
		$requestData = array(
			'QuizAnswer' => array(
				array(
					array(
						'answer_value' => 'TTTTTT'
					)
				)
			)
		);
		$params = array();

		//Helperロード
		$this->loadHelper('Quizzes.QuizAnswer', $viewVars, $requestData, $params);
	}

/**
 * textArea()のテスト
 *
 * @return void
 */
	public function testTextArea() {
		//データ生成
		$index = 0;
		$fieldName = 'QuizAnswer.' . 0 . '.0.answer_value';
		$question = null;
		$readonly = false;

		//テスト実施
		$result = $this->QuizAnswer->textArea($index, $fieldName, $question, $readonly);

		//チェック
		$expected = '<textarea name="data[QuizAnswer][0][0][answer_value]" div="form-inline"' .
			' class="form-control" rows="5" id="QuizAnswer00AnswerValue">TTTTTT</textarea>';
		$this->assertTextEquals($result, $expected);
	}
/**
 * textArea()のテスト
 *
 * @return void
 */
	public function testTextAreaReadOnly() {
		//データ生成
		$index = 0;
		$fieldName = 'QuizAnswer.' . 0 . '.0.answer_value';
		$question = null;
		$readonly = true;

		//テスト実施
		$result = $this->QuizAnswer->textArea($index, $fieldName, $question, $readonly);

		//チェック
		$expected = 'TTTTTT';
		$this->assertTextEquals($result, $expected);
	}

}
