<?php
/**
 * QuizAnswerSummary::getPassedQuizKeys()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsGetTest', 'NetCommons.TestSuite');
App::uses('QuizzesComponent', 'Quizzes.Controller/Component');
App::uses('QuizDataGetTest', 'Quizzes.TestSuite');

/**
 * QuizAnswerSummary::getPassedQuizKeys()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\Model\QuizAnswerSummary
 */
class QuizAnswerSummaryGetPassedQuizKeysTest extends NetCommonsGetTest {

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
	protected $_methodName = 'getPassedQuizKeys';

/**
 * getPassedQuizKeys()のテスト
 *
 * @param int $pointPass 点数合格状態
 * @param int $timePass 時間合格状態
 * @param array $expected 期待値
 * @dataProvider dataProviderGet
 * @return void
 */
	public function testGetPassedQuizKeys($pointPass, $timePass, $expected) {
		$model = $this->_modelName;
		$dataGet = new QuizDataGetTest();

		$quizIds = array(18, 19, 21);
		foreach ($quizIds as $quizId) {
			$quiz = $dataGet->getData($quizId);
			$data = array(
				'answer_status' => QuizzesComponent::ACTION_ACT,
				'test_status' => QuizzesComponent::TEST_ANSWER_STATUS_PEFORM,
				'answer_number' => 1,
				'is_grade_finished' => true,
				'summary_score' => 10,
				'passing_status' => $pointPass,
				'within_time_status' => $timePass,
				'quiz_key' => $quiz['Quiz']['key'],
				'user_id' => 4,
			);
			$this->$model->create();
			$this->$model->set($data);
			$this->$model->save();
		}

		$methodName = $this->_methodName;

		$addConditions = array('user_id' => 4);
		//テスト実施
		$result = $this->$model->$methodName($addConditions);

		$resultValue = array_values($result);
		//チェック
		$this->assertEqual($resultValue, $expected);
	}
/**
 * DataProvider
 *
 * ### 戻り値
 *  - data 登録データ
 *
 * @return array テストデータ
 */
	public function dataProviderGet() {
		return array(
			array(
				QuizzesComponent::STATUS_GRADE_PASS,
				QuizzesComponent::STATUS_GRADE_PASS,
				array(
					'7a32c4f0c47d05fa43953b06cf23e0f2',
					'58688715449e27e5af9ded1f90dd2bc8',
					'b1fc3e74d1fdf47e06d76d41fad41067',
				),
			),
			array(
				QuizzesComponent::STATUS_GRADE_FAIL,
				QuizzesComponent::STATUS_GRADE_FAIL,
				array(
				),
			),
		);
	}

}
