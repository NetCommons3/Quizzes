<?php
/**
 * View/Elements/QuizAnswers/grade_buttonのテスト
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
 * View/Elements/QuizAnswers/grade_buttonのテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\View\Elements\QuizAnswers\GradeButton
 */
class QuizzesViewElementsQuizAnswersGradeButtonTest extends NetCommonsControllerTestCase {

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
		$this->generateNc('TestQuizzes.TestViewElementsQuizAnswersGradeButton');
	}

/**
 * View/Elements/QuizAnswers/grade_buttonのテスト
 *
 * @return void
 */
	public function testGradeButton() {
		//テスト実行
		$this->_testGetAction('/test_quizzes/test_view_elements_quiz_answers_grade_button/grade_button',
				array('method' => 'assertNotEmpty'), null, 'view');

		//チェック
		$pattern = '/' . preg_quote('View/Elements/QuizAnswers/grade_button', '/') . '/';
		$this->assertRegExp($pattern, $this->view);

		//必要に応じてassert追加する
		$this->assertTextContains(__d('quizzes', 'Challenge once again'), $this->view);
	}

}
