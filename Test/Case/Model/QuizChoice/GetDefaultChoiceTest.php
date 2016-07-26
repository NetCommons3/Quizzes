<?php
/**
 * QuizChoice::getDefaultChoice()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsGetTest', 'NetCommons.TestSuite');
App::uses('QuizzesComponent', 'Quizzes.Controller/Component');
App::uses('QuizChoice', 'Quizzes.Model');

/**
 * QuizChoice::getDefaultChoice()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\Model\QuizChoice
 */
class QuizChoiceGetDefaultChoiceTest extends NetCommonsGetTest {

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
	protected $_modelName = 'QuizChoice';

/**
 * Method name
 *
 * @var string
 */
	protected $_methodName = 'getDefaultChoice';

/**
 * getDefaultChoice()のテスト
 *
 * @return void
 */
	public function testGetDefaultChoice() {
		$model = $this->_modelName;
		$methodName = $this->_methodName;

		//データ生成
		$expect = array(
			array(
				'choice_sequence' => 0,
				'choice_label' => __d('quizzes', 'New Choice') . '1',
			),
			array(
				'choice_sequence' => 1,
				'choice_label' => __d('quizzes', 'New Choice') . '2',
			),
			array(
				'choice_sequence' => 2,
				'choice_label' => __d('quizzes', 'New Choice') . '3',
			),
		);
		//テスト実施
		$result = $this->$model->$methodName();
		//チェック
		$this->assertEquals($result, $expect);
	}

}
