<?php
/**
 * QuestionEditHelper::questionInput()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsHelperTestCase', 'NetCommons.TestSuite');

/**
 * QuestionEditHelper::questionInput()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\View\Helper\QuestionEditHelper
 */
class QuestionEditHelperQuestionInputTest extends NetCommonsHelperTestCase {

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
		$viewVars = array('isPublished' => true);
		$requestData = array();
		$params = array();

		//Helperロード
		$this->loadHelper('Quizzes.QuestionEdit', $viewVars, $requestData, $params);
	}

/**
 * questionInput()のテスト
 *
 * @return void
 */
	public function testQuestionInput() {
		//データ生成
		$fieldName = 'test';
		$title = 'testTitle';
		$options = array(
			'type' => 'checkbox'
		);
		$label = 'testLabel';

		//テスト実施
		$result = $this->QuestionEdit->questionInput($fieldName, $title, $options, $label);

		//チェック
		$expected = '<div class="row form-group">' .
		'<label class="col-xs-2 control-label">testTitle</label>' .
		'<div class="col-xs-10"><div class="checkbox"><label class="control-label" for="test">' .
		'<input type="hidden" name="data[test]" id="test_" value="0" disabled="disabled"/>' .
		'<input type="checkbox" name="data[test]" disabled="disabled" value="1" id="test"/>' .
			'testLabel</label></div></div></div>';
		$this->assertTextEquals($expected, $result);
	}

}
