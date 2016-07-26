<?php
/**
 * QuizAnswerSummary::saveEndSummary()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsModelTestCase', 'NetCommons.TestSuite');
App::uses('NetCommonsTime', 'NetCommons.Utility');
App::uses('QuizzesComponent', 'Quizzes.Controller/Component');
App::uses('QuizDataGetTest', 'Quizzes.TestSuite');

/**
 * QuizAnswerSummary::saveEndSummary()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\Model\QuizAnswerSummary
 */
class QuizAnswerSummarySaveEndSummaryTest extends NetCommonsModelTestCase {

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
	protected $_methodName = 'saveEndSummary';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		Current::$current['Frame']['key'] = 'frame_3';

		$model = $this->_modelName;

		$mailQueueMock = $this->getMock('MailQueueBehavior',
			['setAddEmbedTagValue', 'afterSave']);
		$mailQueueMock->expects($this->any())
			->method('setAddEmbedTagValue')
			->will($this->returnValue(true));
		$mailQueueMock->expects($this->any())
			->method('afterSave')
			->will($this->returnValue(true));

		// ClassRegistoryを使ってモックを登録。
		// まずremoveObjectしないとaddObjectできないのでremoveObjectする
		ClassRegistry::removeObject('MailQueueBehavior');
		// addObjectでUploadBehaviorでMockが使われる
		ClassRegistry::addObject('MailQueueBehavior', $mailQueueMock);

		// このloadではモックがロードされる
		$this->$model->Behaviors->load('MailQueue');

		$netCommonsTime = new NetCommonsTime();
		$nowTime = $netCommonsTime->getNowDatetime();
		$this->$model->id = 11;
		$this->$model->saveField('answer_start_time', $nowTime);
	}
/**
 * Saveのテスト
 *
 * @param array $quiz 小テストデータ
 * @param int $summaryId サマリID
 * @param array $expected 期待値
 * @dataProvider dataProviderSave
 * @return void
 */
	public function testSave($quiz, $summaryId, $expected) {
		$model = $this->_modelName;
		$method = $this->_methodName;

		$id = $this->$model->$method($quiz, $summaryId);
		$summary = $this->$model->findById($id);
		if (is_array($expected)) {
			foreach ($expected as $key => $val) {
				$this->assertEqual($summary['QuizAnswerSummary'][$key], $val);
			}
		} else {
			$this->assertEqual($summary, $expected);
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
		$dataGet = new QuizDataGetTest();
		$quiz = $dataGet->getData(51);
		$quiz['Quiz']['passing_grade'] = 5;
		$quiz2 = $dataGet->getData(46);
		$quiz2['Quiz']['passing_grade'] = 5;
		$quiz2['Quiz']['estimated_time'] = 50;
		$quiz3 = $dataGet->getData(47);

		$results = array();
		$results[0] = array(
			$quiz, 31, array(
				'answer_status' => QuizzesComponent::ACTION_ACT,
				'is_grade_finished' => false,
				'passing_status' => QuizzesComponent::STATUS_GRADE_YET,
				'within_time_status' => QuizzesComponent::STATUS_GRADE_PASS
			)
		);
		$results[1] = array(
			$quiz, 99, array()
		);
		$results[2] = array(
			$quiz2, 11, array(
				'answer_status' => QuizzesComponent::ACTION_ACT,
				'is_grade_finished' => true,
				'passing_status' => QuizzesComponent::STATUS_GRADE_PASS,
				'within_time_status' => QuizzesComponent::STATUS_GRADE_PASS
			)
		);
		$results[3] = array(
			$quiz2, 12, array(
				'answer_status' => QuizzesComponent::ACTION_ACT,
				'is_grade_finished' => true,
				'passing_status' => QuizzesComponent::STATUS_GRADE_FAIL,
				'within_time_status' => QuizzesComponent::STATUS_GRADE_FAIL
			)
		);
		$results[4] = array(
			$quiz3, 18, array(
				'answer_status' => QuizzesComponent::ACTION_ACT,
				'is_grade_finished' => true,
				'passing_status' => QuizzesComponent::STATUS_GRADE_PASS,
				'within_time_status' => QuizzesComponent::STATUS_GRADE_PASS
			)
		);

		return $results;
	}

/**
 * SaveのExceptionErrorテスト
 *
 * @param array $quiz 小テストデータ
 * @param int $summaryId サマリID
 * @param string $mockModel Mockのモデル
 * @param string $mockMethod Mockのメソッド
 * @dataProvider dataProviderSaveOnExceptionError
 * @return void
 */
	public function testSaveOnExceptionError($quiz, $summaryId, $mockModel, $mockMethod) {
		$model = $this->_modelName;
		$method = $this->_methodName;

		$this->_mockForReturnFalse($model, $mockModel, $mockMethod);

		$this->setExpectedException('InternalErrorException');
		$this->$model->$method($quiz, $summaryId);
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
			array($quiz, 27, 'Quizzes.QuizAnswerSummary', 'save'),
		);
	}

}
