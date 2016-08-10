<?php
/**
 * QuizResultHelper::getMaxScore()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsHelperTestCase', 'NetCommons.TestSuite');
App::uses('QuizzesComponent', 'Quizzes.Controller/Component');
App::uses('WorkflowComponent', 'Workflow.Controller/Component');

/**
 * QuizResultHelper::getMaxScore()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\View\Helper\QuizResultHelper
 */
class QuizResultHelperGetMaxScoreTest extends NetCommonsHelperTestCase {

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
		$this->loadHelper('Quizzes.QuizResult', $viewVars, $requestData, $params);
	}

/**
 * getMaxScore()のテスト
 *
 * @return void
 */
	public function testGetMaxScore() {
		//データ生成
		$quiz = array();
		$summary = array(
			'Statistics' => array('max_score' => null)
		);
		$result = $this->QuizResult->getMaxScore($quiz, $summary);
		$this->assertTextEquals('-', $result);
	}
}