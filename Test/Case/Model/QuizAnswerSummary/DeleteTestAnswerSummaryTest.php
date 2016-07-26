<?php
/**
 * QuizAnswerSummary::deleteTestAnswerSummary()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsModelTestCase', 'NetCommons.TestSuite');
App::uses('QuizAnswerSummaryFixture', 'Quizzes.Test/Fixture');
App::uses('QuizzesComponent', 'Quizzes.Controller/Component');

/**
 * QuizAnswerSummary::deleteTestAnswerSummary()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\Model\QuizAnswerSummary
 */
class QuizAnswerSummaryDeleteTestAnswerSummaryTest extends NetCommonsModelTestCase {

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
 * Plugin name
 *
 * @var string
 */
	public $plugin = 'quizzes';

/**
 * Model name
 *
 * @var string
 */
	protected $_modelName = 'QuizAnswerSummary';

/**
 * Method name
 *
 * @var string
 */
	protected $_methodName = 'deleteTestAnswerSummary';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		// 試験のため、現在存在する回答データをテスト状態回答とします
		$this->QuizAnswerSummary = ClassRegistry::init('Quizzes.QuizAnswerSummary');
		$this->QuizAnswerSummary->Behaviors->unload('Mails.MailQueue');
		$this->QuizAnswerSummary->id = 26;
		$this->QuizAnswerSummary->saveField('test_status', QuizzesComponent::TEST_ANSWER_STATUS_TEST);
	}
/**
 * Deleteのテスト
 *
 * @param array|string $data 削除データ
 * @param array $associationModels 削除確認の関連モデル array(model => conditions)
 * @dataProvider dataProviderDelete
 * @return void
 */
	public function testDelete($data, $associationModels = null) {
		$model = $this->_modelName;
		$method = $this->_methodName;
		if (! $associationModels) {
			$associationModels = array();
		}

		//テスト実行前のチェック
		$keyConditions = array(
			'quiz_key' => $data,
			'test_status' => QuizzesComponent::TEST_ANSWER_STATUS_TEST
		);
		$count = $this->$model->find('count', array(
			'recursive' => -1,
			'conditions' => $keyConditions,
		));
		$this->assertNotEquals(0, $count);

		foreach ($associationModels as $assocModel => $conditions) {
			if (! is_object($this->$model->$assocModel)) {
				debug('Not defined association model "' . $assocModel . '".');
				continue;
			}
			$count = $this->$model->$assocModel->find('count', array(
				'recursive' => -1,
				'conditions' => $conditions,
			));
			$this->assertNotEquals(0, $count);
		}

		//テスト実行
		$result = $this->$model->$method($data, WorkflowComponent::STATUS_PUBLISHED);
		$this->assertTrue($result);

		//チェック
		$count = $this->$model->find('count', array(
			'recursive' => -1,
			'conditions' => $keyConditions,
		));
		$this->assertEquals(0, $count);

		foreach ($associationModels as $assocModel => $conditions) {
			$count = $this->$model->$assocModel->find('count', array(
				'recursive' => -1,
				'conditions' => $conditions,
			));
			$this->assertEquals(0, $count);
		}
	}

/**
 * Delete用DataProvider
 *
 * ### 戻り値
 *  - data: 削除データ
 *  - associationModels: 削除確認の関連モデル array(model => conditions)
 *
 * @return array テストデータ
 */
	public function dataProviderDelete() {
		$association = array(
			'QuizAnswer' => array(
				'quiz_answer_summary_id' => 26,
			),
		);
		$results = array();
		$results[0] = array('83b294e176a8c8026d4fbdb07ad2ed7f', $association);

		return $results;
	}

/**
 * DeleteのExceptionErrorテスト
 *
 * @param array $data 登録データ
 * @param string $mockModel Mockのモデル
 * @param string $mockMethod Mockのメソッド
 * @dataProvider dataProviderDeleteOnExceptionError
 * @return void
 */
	public function testDeleteOnExceptionError($data, $mockModel, $mockMethod) {
		$model = $this->_modelName;
		$method = $this->_methodName;

		$this->_mockForReturnFalse($model, $mockModel, $mockMethod);

		$this->setExpectedException('InternalErrorException');
		$this->$model->$method($data, WorkflowComponent::STATUS_PUBLISHED);
	}

/**
 * ExceptionError用DataProvider
 *
 * ### 戻り値
 *  - data 登録データ
 *  - mockModel Mockのモデル
 *  - mockMethod Mockのメソッド
 *
 * @return array テストデータ
 */
	public function dataProviderDeleteOnExceptionError() {
		$data = $this->dataProviderDelete()[0][0];

		return array(
			array($data, 'Quizzes.QuizAnswerSummary', 'deleteAll'),
		);
	}

}
