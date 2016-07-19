<?php
/**
 * QuizAnswerHelper::multipleWord()のテスト
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
 * QuizAnswerHelper::multipleWord()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\View\Helper\QuizAnswerHelper
 */
class QuizAnswerHelperMultipleWordTest extends NetCommonsHelperTestCase {

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
						'answer_value' => array('T1', 'T2', 'T3')
					)
				)
			)

		);
		$params = array();

		//Helperロード
		$this->loadHelper('Quizzes.QuizAnswer', $viewVars, $requestData, $params);
	}

/**
 * multipleWord()のテスト
 *
 * @return void
 */
	public function testMultipleWord() {
		//データ生成
		$index = 0;
		$fieldName = 'QuizAnswer.' . 0 . '.0.answer_value';
		$question = array(
			'QuizCorrect' => array(
				array('correct' => array('test1')),
				array('correct' => array('test2')),
				array('correct' => array('test3')),
			)
		);
		$readonly = false;

		//テスト実施
		$result = $this->QuizAnswer->multipleWord($index, $fieldName, $question, $readonly);

		//チェック
		$expected = '<div class="form-inline"><label for="QuizAnswer00AnswerValue0" ' .
			'class="control-label">(1) </label>' .
			'<input name="data[QuizAnswer][0][0][answer_value][0]" class="form-control" ' .
			'type="text" value="T1" id="QuizAnswer00AnswerValue0"/></div><div class="form-inline">' .
			'<label for="QuizAnswer00AnswerValue1" class="control-label">(2) </label>' .
			'<input name="data[QuizAnswer][0][0][answer_value][1]" class="form-control" ' .
			'type="text" value="T2" id="QuizAnswer00AnswerValue1"/></div><div class="form-inline">' .
			'<label for="QuizAnswer00AnswerValue2" class="control-label">(3) </label>' .
			'<input name="data[QuizAnswer][0][0][answer_value][2]" class="form-control" ' .
			'type="text" value="T3" id="QuizAnswer00AnswerValue2"/></div>';
		$this->assertTextEquals($result, $expected);
	}

/**
 * multipleWord()のテスト
 *
 * @return void
 */
	public function testMultipleWordReadOnly() {
		//データ生成
		$index = 0;
		$fieldName = 'QuizAnswer.' . 0 . '.0.answer_value';
		$question = array(
			'QuizCorrect' => array(
				array('correct' => array('test1')),
				array('correct' => array('test2')),
				array('correct' => array('test3')),
			)
		);
		$readonly = true;

		//テスト実施
		$result = $this->QuizAnswer->multipleWord($index, $fieldName, $question, $readonly);

		//チェック
		$expected = '(1) T1<br />(2) T2<br />(3) T3<br />';
		$this->assertTextEquals($result, $expected);
	}
}
