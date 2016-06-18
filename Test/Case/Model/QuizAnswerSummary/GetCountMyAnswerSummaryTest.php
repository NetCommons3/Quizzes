<?php
/**
 * QuizAnswerSummary::getCountMyAnswerSummary()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsGetTest', 'NetCommons.TestSuite');
App::uses('QuizzesComponent', 'Quizzes.Controller/Component');

/**
 * QuizAnswerSummary::getCountMyAnswerSummary()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\Model\QuizAnswerSummary
 */
class QuizAnswerSummaryGetCountMyAnswerSummaryTest extends NetCommonsGetTest {

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
	protected $_methodName = 'getCountMyAnswerSummary';

/**
 * getCountMyAnswerSummary()のテスト
 *
 * @return void
 */
	public function testGetCountMyAnswerSummary() {
		$model = $this->_modelName;
		$methodName = $this->_methodName;

		//データ生成
		$quizKey = '83b294e176a8c8026d4fbdb07ad2ed7f';
		$ids = array(26,28);

		//テスト実施
		$result = $this->$model->$methodName($quizKey, $ids);

		//チェック
		$this->assertEqual($result, count($ids));

		Current::$current['User']['id'] = 4;
		$result = $this->$model->$methodName($quizKey, $ids);
		$this->assertEqual($result, 3);
	}

}
