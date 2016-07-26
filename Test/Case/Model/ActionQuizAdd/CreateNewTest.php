<?php
/**
 * ActionQuizAdd::_createNew()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsGetTest', 'NetCommons.TestSuite');
App::uses('QuizzesComponent', 'Quizzes.Controller/Component');

/**
 * ActionQuizAdd::_createNew()のテスト
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\Model\ActionQuizAdd
 */
class ActionQuizAddCreateNewTest extends NetCommonsGetTest {

/**
 * Plugin name
 *
 * @var array
 */
	public $plugin = 'quizzes';

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.quizzes.quiz',
		'plugin.quizzes.quiz_answer',
		'plugin.quizzes.quiz_answer_summary',
		'plugin.quizzes.quiz_choice',
		'plugin.quizzes.quiz_correct',
		'plugin.quizzes.quiz_frame_display_quiz',
		'plugin.quizzes.quiz_frame_setting',
		'plugin.quizzes.quiz_page',
		'plugin.quizzes.quiz_question',
		'plugin.quizzes.block_setting_for_quiz',
		'plugin.workflow.workflow_comment',
	);

/**
 * Model name
 *
 * @var array
 */
	protected $_modelName = 'ActionQuizAdd';

/**
 * Method name
 *
 * @var array
 */
	protected $_methodName = 'getNewQuiz';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Quiz = ClassRegistry::init('Quizzes.Quiz');
		$this->Quiz->Behaviors->unload('AuthorizationKey');
		Current::$current['Block']['id'] = 2;
	}

/**
 * _createNew()のテスト
 *
 * @param array $data POSTデータ
 * @dataProvider dataProviderGet
 *
 * @return void
 */
	public function testCreateNew($data) {
		$this->ActionQuizAdd->create();
		$this->ActionQuizAdd->set($data);
		// getNewQuizを呼ぶことで_createNewが呼ばれる仕組み
		$result = $this->ActionQuizAdd->getNewQuiz();
		$this->assertTrue(Hash::check($result, 'Quiz[title=test title]'));
		$this->assertTrue(Hash::check($result, 'QuizPage.0'));
		$this->assertTrue(Hash::check($result, 'QuizPage.0.QuizQuestion.0'));
		$this->assertTrue(Hash::check($result, 'QuizPage.0.QuizQuestion.0.QuizChoice.0'));
	}

/**
 * testCreateNewのDataProvider
 *
 * #### 戻り値
 *  - array 取得するキー情報
 *  - array 期待値 （取得したキー情報）
 *
 * @return array
 */
	public function dataProviderGet() {
		return array(
			array(
				array('ActionQuizAdd' => array(
					'create_option' => 'create',
					'title' => 'test title'
				)),
			),
		);
	}
}
