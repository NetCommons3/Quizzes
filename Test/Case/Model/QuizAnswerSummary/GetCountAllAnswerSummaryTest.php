<?php
/**
 * QuizAnswerSummary::getCountAllAnswerSummary()のテスト
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
 * QuizAnswerSummary::getCountAllAnswerSummary()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\Model\QuizAnswerSummary
 */
class QuizAnswerSummaryGetCountAllAnswerSummaryTest extends NetCommonsGetTest {

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
	protected $_methodName = 'getCountAllAnswerSummary';

/**
 * getCountAllAnswerSummary()のテスト
 *
 * @return void
 */
	public function testGetCountAllAnswerSummary() {
		$model = $this->_modelName;
		$methodName = $this->_methodName;

		//データ生成
		$quizKey = '83b294e176a8c8026d4fbdb07ad2ed7f ';

		//テスト実施
		$result = $this->$model->$methodName($quizKey);
		//チェック
		$this->assertEqual($result, 8);

		$data = array();
		for ($i = 26; $i <= 33; $i++) {
			$data[$i][$model]['id'] = $i;
			$data[$i][$model]['answer_status'] = QuizzesComponent::ACTION_NOT_ACT;
		}
		$this->$model->saveMany($data);
		//テスト実施
		$result = $this->$model->$methodName($quizKey);
		//チェック
		$this->assertEqual($result, 0);

		for ($i = 26; $i <= 33; $i++) {
			$data[$i][$model]['id'] = $i;
			$data[$i][$model]['answer_status'] = QuizzesComponent::ACTION_ACT;
			$data[$i][$model]['test_status'] = QuizzesComponent::TEST_ANSWER_STATUS_TEST;
		}
		$this->$model->saveMany($data);
		//テスト実施
		$result = $this->$model->$methodName($quizKey);
		//チェック
		$this->assertEqual($result, 0);
	}

}
