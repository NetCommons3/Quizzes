<?php
/**
 * QuizzesComponent::isMultipleAnswerType()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsControllerTestCase', 'NetCommons.TestSuite');
App::uses('QuizzesComponent', 'Quizzes.Controller/Component');

/**
 * QuizzesComponent::isMultipleAnswerType()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\Controller\Component\QuizzesComponent
 */
class QuizzesComponentIsMultipleAnswerTypeTest extends NetCommonsControllerTestCase {

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

		//テストプラグインのロード
		NetCommonsCakeTestCase::loadTestPlugin($this, 'Quizzes', 'TestQuizzes');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		//ログアウト
		TestAuthGeneral::logout($this);

		parent::tearDown();
	}

/**
 * isMultipleAnswerType()のテスト
 *
 * @return void
 */
	public function testIsMultipleAnswerType() {
		//テストコントローラ生成
		$this->generateNc('TestQuizzes.TestQuizzesComponent');

		//ログイン
		TestAuthGeneral::login($this);

		//テストアクション実行
		$this->_testGetAction('/test_quizzes/test_quizzes_component/index',
				array('method' => 'assertNotEmpty'), null, 'view');
		$pattern = '/' . preg_quote('Controller/Component/TestQuizzesComponent', '/') . '/';
		$this->assertRegExp($pattern, $this->view);

		//テスト実行
		$result = $this->controller->Quizzes->isMultipleAnswerType(QuizzesComponent::TYPE_MULTIPLE_SELECTION);
		$this->assertTrue($result);
		$result = $this->controller->Quizzes->isMultipleAnswerType(QuizzesComponent::TYPE_MULTIPLE_WORD);
		$this->assertTrue($result);
		$result = $this->controller->Quizzes->isMultipleAnswerType(QuizzesComponent::TYPE_TEXT_AREA);
		$this->assertFalse($result);
	}

}
