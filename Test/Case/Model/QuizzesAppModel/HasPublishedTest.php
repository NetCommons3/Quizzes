<?php
/**
 * QuizzesAppModel::hasPublished()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsGetTest', 'NetCommons.TestSuite');
App::uses('QuizDataGetTest', 'Quizzes.TestSuite');

/**
 * QuizzesAppModel::hasPublished()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\Model\QuizzesAppModel
 */
class QuizzesAppModelHasPublishedTest extends NetCommonsGetTest {

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
	protected $_modelName = 'Quiz';

/**
 * Method name
 *
 * @var string
 */
	protected $_methodName = 'hasPublished';

/**
 * hasPublished()のテスト
 *
 * @return void
 */
	public function testHasPublished() {
		$model = $this->_modelName;
		$methodName = $this->_methodName;

		$dataGet = new QuizDataGetTest();

		//データ生成
		$quiz = $dataGet->getData(6);
		//テスト実施
		$result = $this->$model->$methodName($quiz);
		//チェック
		$this->assertNotEqual($result, 0);

		//データ生成
		$quiz = $dataGet->getData(44);
		//テスト実施
		$result = $this->$model->$methodName($quiz);
		//チェック
		$this->assertEqual($result, 0);

		//テスト実施
		$result = $this->$model->$methodName(array());
		//チェック
		$this->assertEqual($result, 0);
	}

}
