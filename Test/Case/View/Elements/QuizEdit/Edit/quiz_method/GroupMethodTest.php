<?php
/**
 * View/Elements/QuizEdit/Edit/quiz_method/group_methodのテスト
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
 * View/Elements/QuizEdit/Edit/quiz_method/group_methodのテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\View\Elements\QuizEdit\Edit\quiz_method\GroupMethod
 */
class QuizzesViewElementsQuizEditEditQuizMethodGroupMethodTest extends NetCommonsControllerTestCase {

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
		//テストコントローラ生成
		$this->generateNc('TestQuizzes.TestViewElementsQuizEditEditQuizMethodGroupMethod');
	}

/**
 * View/Elements/QuizEdit/Edit/quiz_method/group_methodのテスト
 *
 * @return void
 */
	public function testGroupMethod() {
		//テスト実行
		$this->_testGetAction('/test_quizzes/test_view_elements_quiz_edit_edit_quiz_method_group_method/group_method',
				array('method' => 'assertNotEmpty'), null, 'view');

		//チェック
		$pattern = '/' . preg_quote('View/Elements/QuizEdit/Edit/quiz_method/group_method', '/') . '/';
		$this->assertRegExp($pattern, $this->view);

		$this->assertInput('hidden', 'data[is_no_member_allow]', '0', $this->view);
		$this->assertInput('hidden', 'data[is_key_pass_use]', '0', $this->view);
		$this->assertInput('hidden', 'data[is_image_authentication]', '0', $this->view);
	}

}
