<?php
/**
 * QuizAnswerSummary::getCorrectRate()のテスト
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
 * QuizAnswerSummary::getCorrectRate()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\Model\QuizAnswerSummary
 */
class QuizAnswerSummaryGetCorrectRateTest extends NetCommonsGetTest {

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
		'plugin.authorization_keys.authorization_keys',
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
	protected $_methodName = 'getCorrectRate';

/**
 * getCorrectRate()のテスト
 *
 * @return void
 */
	public function testGetCorrectRate() {
		$model = $this->_modelName;
		$methodName = $this->_methodName;

		$dataGet = new QuizDataGetTest();
		//データ生成
		$quiz = $dataGet->getData(51);
		//テスト実施
		// 表示しないで戻るパターン
		$this->$model->$methodName($quiz);
		// 表示するようにして
		$quiz['Quiz']['is_total_show'] = true;
		$this->$model->$methodName($quiz);

		//チェック
		$question = $quiz['QuizPage'][0]['QuizQuestion'][0];
		$this->assertEquals($question['correct_percentage'], '37.5');
		$this->assertEquals($question['wrong_percentage'], '37.5');
		$this->assertEquals($question['rest_percentage'], '25');
	}

}
