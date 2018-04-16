<?php
/**
 * QuizQuestionEditHelper::quizAttributeDatetime()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsHelperTestCase', 'NetCommons.TestSuite');

/**
 * QuizQuestionEditHelper::quizAttributeDatetime()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\View\Helper\QuizQuestionEditHelper
 */
class QuizQuestionEditHelperQuizAttributeDatetimeTest extends NetCommonsHelperTestCase {

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
		$this->loadHelper('Quizzes.QuizQuestionEdit', $viewVars, $requestData, $params);
	}

/**
 * quizAttributeDatetime()のテスト
 *
 * @return void
 */
	public function testQuizAttributeDatetime() {
		//データ生成
		$fieldName = 'test';
		$options = array(
			'min' => '2016-01-01 00:00:00',
			'max' => '2016-12-31 23:59:59',
		);
		$help = 'help text';

		//テスト実施
		$result = $this->QuizQuestionEdit->quizAttributeDatetime($fieldName, $options, $help);
		//チェック
		$this->assertContains('<span class="help-block">help text</span>', $result);
	}

}
