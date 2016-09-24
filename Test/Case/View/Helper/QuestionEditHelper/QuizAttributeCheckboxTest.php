<?php
/**
 * QuestionEditHelper::quizAttributeCheckbox()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsHelperTestCase', 'NetCommons.TestSuite');

/**
 * QuestionEditHelper::quizAttributeCheckbox()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\View\Helper\QuestionEditHelper
 */
class QuestionEditHelperQuizAttributeCheckboxTest extends NetCommonsHelperTestCase {

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
		$viewVars = array();
		$requestData = array();
		$params = array();

		//Helperロード
		$this->loadHelper('Quizzes.QuestionEdit', $viewVars, $requestData, $params);
	}

/**
 * quizAttributeCheckbox()のテスト
 *
 * @return void
 */
	public function testQuizAttributeCheckbox() {
		//データ生成
		$fieldName = 'test';
		$label = 'label';
		$options = array();
		$help = 'help text';

		//テスト実施
		$result = $this->QuestionEdit->quizAttributeCheckbox($fieldName, $label, $options, $help);

		//チェック
		$this->assertContains('<span class="help-block">help text</span></label>', $result);
	}

}
