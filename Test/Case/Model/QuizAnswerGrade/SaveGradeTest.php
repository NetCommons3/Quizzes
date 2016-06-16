<?php
/**
 * QuizAnswerGrade::saveGrade()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsModelTestCase', 'NetCommons.TestSuite');
App::uses('QuizAnswerFixture', 'Quizzes.Test/Fixture');
App::uses('QuizzesComponent', 'Quizzes.Controller/Component');
App::uses('QuizDataGetTest', 'Quizzes.TestSuite');

/**
 * QuizAnswerGrade::saveGrade()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\Model\QuizAnswerGrade
 */
class QuizAnswerGradeSaveGradeTest extends NetCommonsModelTestCase {

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
	protected $_modelName = 'QuizAnswerGrade';

/**
 * Method name
 *
 * @var string
 */
	protected $_methodName = 'saveGrade';

/**
 * Saveのテスト
 *
 * @param array $quiz
 * @param int $summaryId
 * @param array $data 登録データ
 * @dataProvider dataProviderSave
 * @return void
 */
	public function testSave($quiz, $summaryId, $data) {
		$model = $this->_modelName;
		$method = $this->_methodName;

		//テスト実行
		$result = $this->$model->$method($quiz, $summaryId, $data);
		$this->assertNotEmpty($result);

		//登録データ取得
		foreach ($data as $d) {
			$d = $d['QuizAnswerGrade'];
			$actual = $this->$model->find('first', array(
				'recursive' => -1,
				'conditions' => array('id' => $d['id']),
			));
			$this->assertEqual($actual['QuizAnswerGrade']['score'], $d['score']);
			$this->assertEqual($actual['QuizAnswerGrade']['answer_correct_status'], $d['answer_correct_status']);
			$this->assertEqual($actual['QuizAnswerGrade']['correct_status'], $d['answer_correct_status']);
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
		//データ生成
		$data['QuizAnswerGrade'] = (new QuizAnswerFixture())->records[33];
		$data['QuizAnswerGrade']['correct_status'] = QuizzesComponent::STATUS_GRADE_PASS;
		$data['QuizAnswerGrade']['answer_correct_status'] = QuizzesComponent::STATUS_GRADE_PASS;
		$data['QuizAnswerGrade']['score'] = 10;

		$data2 = $data;
		$data2['QuizAnswerGrade']['correct_status'] = QuizzesComponent::STATUS_GRADE_FAIL;
		$data2['QuizAnswerGrade']['answer_correct_status'] = QuizzesComponent::STATUS_GRADE_FAIL;
		$data2['QuizAnswerGrade']['score'] = 0;

		$data3 = $data;
		$data3['QuizAnswerGrade']['correct_status'] = QuizzesComponent::STATUS_GRADE_YET;
		$data3['QuizAnswerGrade']['answer_correct_status'] = QuizzesComponent::STATUS_GRADE_YET;
		$data3['QuizAnswerGrade']['score'] = 3;

		$dataGet = new QuizDataGetTest();
		$quiz = $dataGet->getData(51);
		$quiz2 = $quiz;
		$quiz2['Quiz']['passing_grade'] = 5;

		$results = array();
		// * 編集の登録処理
		$results[0] = array($quiz, 31, array($data));
		$results[1] = array($quiz2, 31, array($data));
		$results[2] = array($quiz2, 31, array($data2));
		$results[3] = array($quiz, 31, array($data3));

		return $results;
	}
/**
 * SaveのExceptionErrorテスト
 *
 * @param array $quiz
 * @param int $summaryId
 * @param array $data 登録データ
 * @param string $mockModel Mockのモデル
 * @param string $mockMethod Mockのメソッド
 * @dataProvider dataProviderSaveOnExceptionError
 * @return void
 */
	public function testSaveOnExceptionError($quiz, $summaryId, $data, $mockModel, $mockMethod) {
		$model = $this->_modelName;
		$method = $this->_methodName;

		$this->_mockForReturnFalse($model, $mockModel, $mockMethod);

		$this->setExpectedException('InternalErrorException');
		$this->$model->$method($quiz, $summaryId, $data);
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
		$datas = $this->dataProviderSave()[0];
		return array(
			array($datas[0], $datas[1], $datas[2], 'Quizzes.QuizAnswerGrade', 'save'),
			array($datas[0], $datas[1], $datas[2], 'Quizzes.QuizAnswerSummary', 'save'),
		);
	}

/**
 * SaveのValidationErrorテスト
 *
 * @param array $quiz
 * @param int $summaryId
 * @param array $data 登録データ
 * @param string $mockModel Mockのモデル
 * @param string $mockMethod Mockのメソッド
 * @dataProvider dataProviderSaveOnValidationError
 * @return void
 */
	public function testSaveOnValidationError($quiz, $summaryId, $data, $mockModel, $mockMethod = 'validates') {
		$model = $this->_modelName;
		$method = $this->_methodName;

		$this->_mockForReturnFalse($model, $mockModel, $mockMethod);
		$this->setExpectedException('InternalErrorException');
		$this->$model->$method($quiz, $summaryId, $data);
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
		$data = $this->dataProviderSave()[0];

		return array(
			array($data[0], $data[1], $data[2], 'Quizzes.QuizAnswerGrade'),
		);
	}

}
