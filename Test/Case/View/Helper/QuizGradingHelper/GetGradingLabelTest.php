<?php
/**
 * QuizGradingHelper::getGradingLabel()のテスト
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
 * QuizGradingHelper::getGradingLabel()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\View\Helper\QuizGradingHelper
 */
class QuizGradingHelperGetGradingLabelTest extends NetCommonsHelperTestCase {

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
 * getGradingLabel()のテスト
 *
 * @return void
 */
	public function testGetGradingLabelNoCorrectStatus() {
		//データ生成
		$answer = array(
		);

		//テスト実施
		$result = $this->QuizGrading->getGradingLabel($answer);

		//チェック
		$expected = '<span class="label label-default">未受験</span>';
		$this->assertTextContains($expected, $result);
	}
/**
 * getGradingLabel()のテスト
 *
 * @return void
 */
	public function testGetGradingLabelFailStatus() {
		//データ生成
		$answer = array(
			'correct_status' => QuizzesComponent::STATUS_GRADE_FAIL
		);

		//テスト実施
		$result = $this->QuizGrading->getGradingLabel($answer);

		//チェック
		$expected = '<span class="label label-warning">不正解</span>';
		$this->assertTextContains($expected, $result);
	}

}
