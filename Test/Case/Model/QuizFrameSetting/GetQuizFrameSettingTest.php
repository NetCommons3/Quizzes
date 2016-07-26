<?php
/**
 * QuizFrameSetting::getQuizFrameSetting()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsGetTest', 'NetCommons.TestSuite');
App::uses('QuizFrameSettingFixture', 'Quizzes.Test/Fixture');

/**
 * QuizFrameSetting::getQuizFrameSetting()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\Model\QuizFrameSetting
 */
class QuizFrameSettingGetQuizFrameSettingTest extends NetCommonsGetTest {

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
	protected $_modelName = 'QuizFrameSetting';

/**
 * Method name
 *
 * @var string
 */
	protected $_methodName = 'getQuizFrameSetting';

/**
 * getQuizFrameSetting()のテスト
 *
 * @return void
 */
	public function testGetQuizFrameSetting() {
		$model = $this->_modelName;
		$methodName = $this->_methodName;

		//データ生成
		// まだデータが存在しない場合
		$frameKey = 'frame_99';
		$expected = array(
			QuizzesComponent::DISPLAY_TYPE_LIST,
			QuizFrameSetting::QUIZ_DEFAULT_DISPLAY_NUM_PER_PAGE,
			'Quiz.modified',
			'DESC',
		);

		//テスト実施
		$result = $this->$model->$methodName($frameKey);
		//チェック
		$this->assertEqual($result, $expected);

		// データが存在する場合
		$frameKey = 'frame_3';
		$record = (new QuizFrameSettingFixture())->records[0];
		$expected = array(
			$record['display_type'],
			$record['display_num_per_page'],
			'Quiz.modified',
			'DESC',
		);
		//テスト実施
		$result = $this->$model->$methodName($frameKey);
		//チェック
		$this->assertEqual($result, $expected);
	}

}
