<?php
/**
 * QuizAnswer::getNotScoringQuizKey()のテスト
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
 * QuizAnswer::getNotScoringQuizKey()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\Model\QuizAnswer
 */
class QuizAnswerGetNotScoringQuizKeyTest extends NetCommonsGetTest {

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
	protected $_modelName = 'QuizAnswer';

/**
 * Method name
 *
 * @var string
 */
	protected $_methodName = 'getNotScoringQuizKey';

/**
 * getNotScoringQuizKey()のテスト
 *
 * @return void
 */
	public function testGetNotScoringQuizKey() {
		$model = $this->_modelName;
		$methodName = $this->_methodName;

		//データ生成
		$summaryIds = array();
		for ($i = 1; $i < 50; $i++) {
			$summaryIds[] = $i;
		}

		//テスト実施
		$result = $this->$model->$methodName($summaryIds);

		//チェック
		$this->assertEqual(count($result), 1);
		$this->assertEqual($result[0]['QuizAnswerSummary']['quiz_key'], '83b294e176a8c8026d4fbdb07ad2ed7f');
	}

}
