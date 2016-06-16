<?php
/**
 * QuizFrameSetting::getDefaultFrameSetting()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsGetTest', 'NetCommons.TestSuite');

/**
 * QuizFrameSetting::getDefaultFrameSetting()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\Model\QuizFrameSetting
 */
class QuizFrameSettingGetDefaultFrameSettingTest extends NetCommonsGetTest {

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
	protected $_modelName = 'QuizFrameSetting';

/**
 * Method name
 *
 * @var string
 */
	protected $_methodName = 'getDefaultFrameSetting';

/**
 * getDefaultFrameSetting()のテスト
 *
 * @return void
 */
	public function testGetDefaultFrameSetting() {
		$model = $this->_modelName;
		$methodName = $this->_methodName;

		//データ生成
		$expected = array(
			'QuizFrameSetting' => array(
				'display_type' => QuizzesComponent::DISPLAY_TYPE_LIST,
				'display_num_per_page' => QuizFrameSetting::QUIZ_DEFAULT_DISPLAY_NUM_PER_PAGE,
				'sort_type' => 'Questionnaire.modified DESC',
			)
		);

		//テスト実施
		$result = $this->$model->$methodName();

		//チェック
		$this->assertEqual($result, $expected);
	}

}
