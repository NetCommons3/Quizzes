<?php
/**
 * QuizAnswerSummary::saveStartSummary()のテスト
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
App::uses('QuizDataGetTest', 'Quizzes.TestSuite');

/**
 * QuizAnswerSummary::saveStartSummary()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\Model\QuizAnswerSummary
 */
class QuizAnswerSummarySaveStartSummaryTest extends NetCommonsModelTestCase {

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
	protected $_methodName = 'saveStartSummary';

/**
 * Saveのテスト
 *
 * @param array $quiz 小テストデータ
 * @param string $userId ユーザーID
 * @param array $expected 期待値
 * @dataProvider dataProviderSave
 * @return void
 */
	public function testSave($quiz, $userId, $expected) {
		$model = $this->_modelName;
		$method = $this->_methodName;

		if ($userId) {
			Current::$current['User']['id'] = $userId;
		}
		$id = $this->$model->$method($quiz);
		$summary = $this->$model->findById($id);

		$this->assertEqual($summary['QuizAnswerSummary']['answer_number'], $expected);
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
		$dataGet = new QuizDataGetTest();
		$quiz = $dataGet->getData(51);
		$quiz2 = $dataGet->getData(43);

		$results = array();
		// * 編集の登録処理
		$results[0] = array($quiz, null, 1);
		$results[1] = array($quiz, 4, 4);
		$results[2] = array($quiz2, 4, 1);

		return $results;
	}
/**
 * SaveのExceptionErrorテスト
 *
 * @param array $quiz 小テストデータ
 * @param string $mockModel Mockのモデル
 * @param string $mockMethod Mockのメソッド
 * @dataProvider dataProviderSaveOnExceptionError
 * @return void
 */
	public function testSaveOnExceptionError($quiz, $mockModel, $mockMethod) {
		$model = $this->_modelName;
		$method = $this->_methodName;

		$this->_mockForReturnFalse($model, $mockModel, $mockMethod);

		$this->setExpectedException('InternalErrorException');
		$this->$model->$method($quiz);
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
		$dataGet = new QuizDataGetTest();
		$quiz = $dataGet->getData(51);

		return array(
			array($quiz, 'Quizzes.QuizAnswerSummary', 'save'),
		);
	}
}
