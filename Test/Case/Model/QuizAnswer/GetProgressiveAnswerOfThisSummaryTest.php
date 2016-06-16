<?php
/**
 * QuizAnswer::getProgressiveAnswerOfThisSummary()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsGetTest', 'NetCommons.TestSuite');

/**
 * QuizAnswer::getProgressiveAnswerOfThisSummary()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\Model\QuizAnswer
 */
class QuizAnswerGetProgressiveAnswerOfThisSummaryTest extends NetCommonsGetTest {

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
	protected $_modelName = 'QuizAnswer';

/**
 * Method name
 *
 * @var string
 */
	protected $_methodName = 'getProgressiveAnswerOfThisSummary';

/**
 * getProgressiveAnswerOfThisSummary()のテスト
 *
 * @return void
 */
	public function testGetProgressiveAnswerOfThisSummary() {
		$model = $this->_modelName;
		$methodName = $this->_methodName;

		//データ生成
		$summary = null;
		//テスト実施
		$result = $this->$model->$methodName($summary);
		$this->assertEqual($result, array());

		$summary = array('QuizAnswerSummary' => array('id' => '99999'));
		$result = $this->$model->$methodName($summary);
		$this->assertEqual($result, false);

		$summary = array('QuizAnswerSummary' => array('id' => '11'));
		$result = $this->$model->$methodName($summary);
		$this->assertNotEmpty($result);
	}

}
