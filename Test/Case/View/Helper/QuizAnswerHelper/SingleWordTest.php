<?php
/**
 * QuizAnswerHelper::singleWord()のテスト
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
 * QuizAnswerHelper::singleWord()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\View\Helper\QuizAnswerHelper
 */
class QuizAnswerHelperSingleWordTest extends NetCommonsHelperTestCase {

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
 * singleWord()のテスト
 *
 * @return void
 */
	public function testSingleWord() {
		//データ生成
		$index = 0;
		$fieldName = 'QuizAnswer.' . 0 . '.0.answer_value';
		$question = array(
		);
		$readonly = false;

		//テスト実施
		$result = $this->QuizAnswer->singleWord($index, $fieldName, $question, $readonly);

		//チェック
		$expected = '<div class="form-inline">' .
			'<input name="data[QuizAnswer][0][0][answer_value]" class="form-control" type="text"' .
			' value="TTTTTT" id="QuizAnswer00AnswerValue"/></div>';
		$this->assertTextEquals($result, $expected);
	}

/**
 * singleWord()のテスト
 *
 * @return void
 */
	public function testSingleWordReadOnly() {
		//データ生成
		$index = 0;
		$fieldName = 'QuizAnswer.' . 0 . '.0.answer_value';
		$question = array(
		);
		$readonly = true;

		//テスト実施
		$result = $this->QuizAnswer->singleWord($index, $fieldName, $question, $readonly);

		//チェック
		$this->assertTextEquals($result, 'TTTTTT');
	}

}
