<?php
/**
 * QuizAnswerSummary::isPassAnswer()のテスト
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
 * QuizAnswerSummary::isPassAnswer()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\Model\QuizAnswerSummary
 */
class QuizAnswerSummaryIsPassAnswerTest extends NetCommonsGetTest {

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
	protected $_methodName = 'isPassAnswer';

/**
 * isPassAnswer()のテスト
 *
 * @param array $quiz quiz
 * @param array $summary summary
 * @dataProvider dataProviderGet
 * @return void
 */
	public function testIsPassAnswer($quiz, $summary, $expected) {
		$model = $this->_modelName;
		$methodName = $this->_methodName;

		//テスト実施
		$result = $this->$model->$methodName($quiz, $summary);

		//チェック
		$this->assertEqual($result, $expected);
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
				array(
					'Quiz' => array('passing_grade' => 0, 'estimated_time' => 0)
				),
				array(
					'QuizAnswerSummary' => array(
						'is_grade_finished' => false,
						'passing_status' => QuizzesComponent::STATUS_GRADE_YET,
						'within_time_status' => QuizzesComponent::STATUS_GRADE_YET
					)
				),
				QuizzesComponent::STATUS_GRADE_YET
			),
			array(
				array(
					'Quiz' => array('passing_grade' => 0, 'estimated_time' => 0)
				),
				array(
					'QuizAnswerSummary' => array(
						'is_grade_finished' => true,
						'passing_status' => QuizzesComponent::STATUS_GRADE_PASS,
						'within_time_status' => QuizzesComponent::STATUS_GRADE_PASS
					)
				),
				QuizzesComponent::STATUS_GRADE_NONE
			),
			array(
				array(
					'Quiz' => array('passing_grade' => 10, 'estimated_time' => 0)
				),
				array(
					'QuizAnswerSummary' => array(
						'is_grade_finished' => true,
						'passing_status' => QuizzesComponent::STATUS_GRADE_PASS,
						'within_time_status' => QuizzesComponent::STATUS_GRADE_PASS
					)
				),
				QuizzesComponent::STATUS_GRADE_PASS
			),
			array(
				array(
					'Quiz' => array('passing_grade' => 10, 'estimated_time' => 0)
				),
				array(
					'QuizAnswerSummary' => array(
						'is_grade_finished' => true,
						'passing_status' => QuizzesComponent::STATUS_GRADE_FAIL,
						'within_time_status' => QuizzesComponent::STATUS_GRADE_PASS
					)
				),
				QuizzesComponent::STATUS_GRADE_FAIL
			),
			array(
				array(
					'Quiz' => array('passing_grade' => 0, 'estimated_time' => 2)
				),
				array(
					'QuizAnswerSummary' => array(
						'is_grade_finished' => true,
						'passing_status' => QuizzesComponent::STATUS_GRADE_PASS,
						'within_time_status' => QuizzesComponent::STATUS_GRADE_PASS
					)
				),
				QuizzesComponent::STATUS_GRADE_PASS
			),
			array(
				array(
					'Quiz' => array('passing_grade' => 0, 'estimated_time' => 2)
				),
				array(
					'QuizAnswerSummary' => array(
						'is_grade_finished' => true,
						'passing_status' => QuizzesComponent::STATUS_GRADE_PASS,
						'within_time_status' => QuizzesComponent::STATUS_GRADE_FAIL
					)
				),
				QuizzesComponent::STATUS_GRADE_FAIL
			),
		);
	}

}
