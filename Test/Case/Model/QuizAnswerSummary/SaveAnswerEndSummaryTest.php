<?php
/**
 * QuizAnswerSummary::saveAnswerEndSummary()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsModelTestCase', 'NetCommons.TestSuite');
App::uses('QuizzesComponent', 'Quizzes.Controller/Component');
App::uses('QuizDataGetTest', 'Quizzes.TestSuite');

/**
 * QuizAnswerSummary::saveAnswerEndSummary()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\Model\QuizAnswerSummary
 */
class QuizAnswerSummarySaveAnswerEndSummaryTest extends NetCommonsModelTestCase {

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
	protected $_methodName = 'saveAnswerEndSummary';

/**
 * Saveのテスト
 *
 * @param int $summaryId サマリID
 * @dataProvider dataProviderSave
 * @return void
 */
	public function testSave($summaryId) {
		$model = $this->_modelName;
		$method = $this->_methodName;

		$before = $this->$model->find('first', array(
			'conditions' => array(
				'id' => $summaryId
			),
			'recursive' => -1
		));
		$result = $this->$model->$method($summaryId);

		foreach ($result['QuizAnswerSummary'] as $key => $val) {
			if ($key == 'modified') {
				continue;
			}
			if ($key == 'answer_status') {
				$this->assertNotEqual($val, $before['QuizAnswerSummary'][$key]);
				$this->assertEqual($val, QuizzesComponent::ACTION_BEFORE_ACT);
			} else {
				$this->assertEqual($val, $before['QuizAnswerSummary'][$key]);
			}
		}
	}

/**
 * Save用DataProvider
 *
 * ### 戻り値
 *  - data 登録データ
 *
 * @return array テストデータ
 */
	public function dataProviderSave() {
		$results = array();
		// * 編集の登録処理
		$results[0] = array(26);

		return $results;
	}
/**
 * SaveのExceptionErrorテスト
 *
 * @param int $summaryId サマリID
 * @param string $mockModel Mockのモデル
 * @param string $mockMethod Mockのメソッド
 * @dataProvider dataProviderSaveOnExceptionError
 * @return void
 */
	public function testSaveOnExceptionError($summaryId, $mockModel, $mockMethod) {
		$model = $this->_modelName;
		$method = $this->_methodName;

		$this->_mockForReturnFalse($model, $mockModel, $mockMethod);

		$this->setExpectedException('InternalErrorException');
		$this->$model->$method($summaryId);
	}

/**
 * SaveのExceptionError用DataProvider
 *
 * ### 戻り値
 *  - data 登録データ
 *  - mockModel Mockのモデル
 *  - mockMethod Mockのメソッド
 *
 * @return array テストデータ
 */
	public function dataProviderSaveOnExceptionError() {
		$data = $this->dataProviderSave()[0][0];

		return array(
			array($data, 'Quizzes.QuizAnswerSummary', 'save'),
		);
	}

/**
 * SaveのValidationError用DataProvider
 *
 * ### 戻り値
 *  - data 登録データ
 *  - mockModel Mockのモデル
 *  - mockMethod Mockのメソッド(省略可：デフォルト validates)
 *
 * @return array テストデータ
 */
	public function dataProviderSaveOnValidationError() {
		$data = $this->dataProviderSave()[0][0];

		//TODO:テストパタンを書く
		return array(
			array($data, 'Quizzes.QuizAnswerSummary'),
		);
	}

}
