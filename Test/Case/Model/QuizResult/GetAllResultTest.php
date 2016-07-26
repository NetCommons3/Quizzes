<?php
/**
 * QuizResult::getAllResult()のテスト
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
 * QuizResult::getAllResult()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\Model\QuizResult
 */
class QuizResultGetAllResultTest extends NetCommonsGetTest {

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
	protected $_modelName = 'QuizResult';

/**
 * Method name
 *
 * @var string
 */
	protected $_methodName = 'getAllResult';

/**
 * getAllResult()のテスト
 *
 * @return void
 */
	public function testGetAllResult() {
		$model = $this->_modelName;
		$methodName = $this->_methodName;

		//データ生成
		$dataGet = new QuizDataGetTest();
		$quiz = $dataGet->getData(51);

		//テスト実施
		$this->$model->initResult($quiz);
		$result = $this->$model->$methodName();

		//チェック
		$general = $result['general'];
		$this->assertEqual($general['number_pepole'], 5);
		$this->assertEqual($general['max_score'], 10);
		$this->assertEqual($general['min_score'], 0);
		// 8 + 10 + 2 + 0 + 4 / 5
		$this->assertEqual($general['avg_score'], 4.8);
		// (8-4.8)^2 + (10-4.8)^2 + (2-4.8)^2 + (0-4.8)^2 + (4-2.8)^2 /5 13.76
		$this->assertEqual($general['samp_score'], 13.8);
		// 77 +24+30+35+27 /5
		$this->assertEqual($general['avg_time'], 38.6);
		$numCheck = array(1, 0, 1, 0, 1, 0, 0, 0, 1, 1);
		foreach ($result['score_distribution'] as $index => $num) {
			$this->assertEqual($num['value'], $numCheck[$index]);
		}
	}
/**
 * getAllResult()のテスト no data
 *
 * @return void
 */
	public function testGetAllResultNoAnswerData() {
		$model = $this->_modelName;
		$methodName = $this->_methodName;

		//データ生成
		$dataGet = new QuizDataGetTest();
		$quiz = $dataGet->getData(6);

		//テスト実施
		$this->$model->initResult($quiz);
		$result = $this->$model->$methodName();
		//チェック
		$this->assertEqual($result['general'], false);
		foreach ($result['score_distribution'] as $num) {
			$this->assertEqual($num['value'], 0);
		}
	}
}
