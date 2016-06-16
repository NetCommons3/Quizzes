<?php
/**
 * ActionQuizAdd::_createFromTemplate()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('TemporaryFolder', 'Files.Utility');
App::uses('NetCommonsGetTest', 'NetCommons.TestSuite');
App::uses('QuizzesComponent', 'Quizzes.Controller/Component');

/**
 * ActionQuizAdd::_createFromTemplate()のテスト
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\Model\ActionQuizAdd
 */
class ActionQuizAddCreateFromTemplateTest extends NetCommonsGetTest {

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
		'plugin.quizzes.quiz_setting',
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
		NetCommonsCakeTestCase::loadTestPlugin($this, 'Quizzes', 'TestQuizzes');
		NetCommonsCakeTestCase::loadTestPlugin($this, 'Quizzes', 'TestFiles');

		$this->TestActionQuizAdd = ClassRegistry::init('TestQuizzes.TestActionQuizAdd');
		$this->TestActionQuizAddSuccess = ClassRegistry::init('TestQuizzes.TestActionQuizAddSuccess');
		$this->Quiz = ClassRegistry::init('Quizzes.Quiz');
		$this->Quiz->Behaviors->unload('AuthorizationKey');

		Current::$current['Block']['id'] = 2;
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->TestActionQuizAdd);
		unset($this->TestActionQuizAddSuccess);
		parent::tearDown();
	}

/**
 * _createFromTemplate()のテスト
 * Successパターン
 *
 * @param array $data POSTデータ
 * @return void
 */
	public function testCreateFromTemplate() {
		$tmpFolder = new TemporaryFolder();
		copy(APP . 'Plugin/Quizzes/Test/Fixture/TemplateTest.zip', $tmpFolder->path . DS . 'TemplateTest.zip');
		$data = array('ActionQuizAdd' => array(
			'create_option' => 'template',
			'template_file' => array(
				'name' => 'TemplateTest.zip',
				'type' => 'application/x-zip-compressed',
				'tmp_name' => $tmpFolder->path . DS . 'TemplateTest.zip',
				'error' => 0,
				'size' => 1481
			)
		));
		$this->TestActionQuizAddSuccess->create();
		$this->TestActionQuizAddSuccess->set($data);
		// getNewQuizを呼ぶことで_createFromTemplateが呼ばれる仕組み
		$result = $this->TestActionQuizAddSuccess->getNewQuiz();
		if (isset($this->TestActionQuizAddSuccess->validationErrors['template_file'])) {
			$this->assertTextEquals($this->TestActionQuizAddSuccess->validationErrors['template_file'][0], '');
		}
		$this->assertNotNull($result);
		$this->assertTrue(Hash::check($result, 'Quiz[import_key=2c6abb3e7083720935a4b8065f7db766642c87ca]'));
		for ($i = 0; $i < 3; $i++) {
			$this->assertTrue(Hash::check($result, 'QuizPage.' . $i));
			$this->assertTrue(Hash::check($result, 'QuizPage.' . $i . '.QuizQuestion.0'));
			if ($i < 2) {
				$this->assertTrue(Hash::check($result, 'QuizPage.' . $i . '.QuizQuestion.0.QuizChoice.0'));
				$this->assertTrue(Hash::check($result, 'QuizPage.' . $i . '.QuizQuestion.0.QuizCorrect.0'));
			}
		}
	}
/**
 * _createFromTemplate()のテスト
 * ファイルアップロードなしできたNGパターン
 *
 * @param array $data POSTデータ
 * @return void
 */
	public function testCreateFromTemplateNG1() {
		$data = array('ActionQuizAdd' => array(
			'create_option' => 'template',
			'template_file' => '',
			'tmp_name' => '',
		));
		$this->TestActionQuizAdd->create();
		$this->TestActionQuizAdd->set($data);
		// getNewQuizを呼ぶことで_createFromTemplateが呼ばれる仕組み
		$result = $this->TestActionQuizAdd->getNewQuiz();
		$this->assertNull($result);
	}
/**
 * _createFromTemplate()のテスト
 * ファイルアップロードエラーが発生したNGパターン
 * 実際には存在しないファイルを指定している
 *
 * @param array $data POSTデータ
 * @return void
 */
	public function testCreateFromTemplateNG2() {
		$data = array('ActionQuizAdd' => array(
			'create_option' => 'template',
			'template_file' => array(
				'name' => 'no_TemplateTest.zip',
				'type' => 'application/x-zip-compressed',
				'tmp_name' => 'no_TemplateTest.zip',
				'error' => 0,
				'size' => 1481
			)
		));
		$this->TestActionQuizAdd->create();
		$this->TestActionQuizAdd->set($data);
		// getNewQuizを呼ぶことで_createFromTemplateが呼ばれる仕組み
		$result = $this->TestActionQuizAdd->getNewQuiz();
		$this->assertNull($result);
	}
/**
 * _createFromTemplate()のテスト
 * Zip形式じゃないZIPファイルが指定されたNGパターン
 * emptyErrorTemplateTest.zipの実態はただのテキストファイル
 *
 * @param array $data POSTデータ
 * @return void
 */
	public function testCreateFromTemplateNG3() {
		$tmpFolder = new TemporaryFolder();
		copy(APP . 'Plugin/Quizzes/Test/Fixture/emptyErrorTemplateTest.zip', $tmpFolder->path . DS . 'emptyErrorTemplateTest.zip');
		$data = array('ActionQuizAdd' => array(
			'create_option' => 'template',
			'template_file' => array(
				'name' => 'TemplateTest.zip',
				'type' => 'application/x-zip-compressed',
				'tmp_name' => $tmpFolder->path . DS . 'emptyErrorTemplateTest.zip',
				'error' => 0,
				'size' => 2218
			)
		));
		$this->TestActionQuizAdd->create();
		$this->TestActionQuizAdd->set($data);
		// getNewQuizを呼ぶことで_createFromTemplateが呼ばれる仕組み
		$result = $this->TestActionQuizAdd->getNewQuiz();
		$this->assertNull($result);
	}

/**
 * _createFromTemplate()のテスト
 * fingrPrintが違うNGパターン
 *
 * @param array $data POSTデータ
 * @return void
 */
	public function testCreateFromTemplateNG4() {
		$tmpFolder = new TemporaryFolder();
		copy(APP . 'Plugin/Quizzes/Test/Fixture/fingerPrintErrorTest.zip', $tmpFolder->path . DS . 'fingerPrintErrorTest.zip');
		$data = array('ActionQuizAdd' => array(
			'create_option' => 'template',
			'template_file' => array(
				'name' => 'TemplateTest.zip',
				'type' => 'application/x-zip-compressed',
				'tmp_name' => $tmpFolder->path . DS . 'fingerPrintErrorTest.zip',
				'error' => 0,
				'size' => 1806
			)
		));
		$this->TestActionQuizAdd->create();
		$this->TestActionQuizAdd->set($data);
		// getNewQuizを呼ぶことで_createFromTemplateが呼ばれる仕組み
		$result = $this->TestActionQuizAdd->getNewQuiz();
		$this->assertNull($result);
	}
/**
 * _createFromTemplate()のテスト
 * versionが違うNGパターン
 *
 * @param array $data POSTデータ
 * @return void
 */
	public function testCreateFromTemplateNG5() {
		$tmpFolder = new TemporaryFolder();
		copy(APP . 'Plugin/Quizzes/Test/Fixture/versionErrorTest.zip', $tmpFolder->path . DS . 'versionErrorTest.zip');
		$data = array('ActionQuizAdd' => array(
			'create_option' => 'template',
			'template_file' => array(
				'name' => 'TemplateTest.zip',
				'type' => 'application/x-zip-compressed',
				'tmp_name' => $tmpFolder->path . DS . 'versionErrorTest.zip',
				'error' => 0,
				'size' => 2218
			)
		));
		$this->TestActionQuizAdd->create();
		$this->TestActionQuizAdd->set($data);
		// getNewQuizを呼ぶことで_createFromTemplateが呼ばれる仕組み
		$result = $this->TestActionQuizAdd->getNewQuiz();
		$this->assertNull($result);
	}
}
