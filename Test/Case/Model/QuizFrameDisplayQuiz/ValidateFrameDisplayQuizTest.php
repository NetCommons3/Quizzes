<?php
/**
 * QuizFrameDisplayQuiz::validateFrameDisplayQuiz()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsModelTestCase', 'NetCommons.TestSuite');
App::uses('QuizzesComponent', 'Quizzes.Controller/Component');

/**
 * QuizFrameDisplayQuiz::validateFrameDisplayQuiz()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\Model\QuizFrameDisplayQuiz
 */
class QuizFrameDisplayQuizValidateFrameDisplayQuizTest extends NetCommonsModelTestCase {

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
	protected $_modelName = 'QuizFrameDisplayQuiz';

/**
 * Method name
 *
 * @var string
 */
	protected $_methodName = 'validateFrameDisplayQuiz';

/**
 * テストDataの取得
 *
 * @param int $displayType display type
 * @return array
 */
	protected function _getData($displayType = QuizzesComponent::DISPLAY_TYPE_SINGLE) {
		$data = array(
			'QuizFrameSetting' => array(
				'display_type' => $displayType,
				'display_num_per_page' => 10,
				'sort_type' => 'Quiz.modified DESC',
			),
			'List' => array(
				'QuizFrameDisplayQuiz' => array(
					array('is_display' => '0', 'quiz_key' => '5fdb4f0049f3bddeabc49cd2b72c6ac9'),
					array('is_display' => '1', 'quiz_key' => 'a2cf0e48f281be7c3cc35f0920f047ca'),
					array('is_display' => '1', 'quiz_key' => 'a916437af184b4a185f685a93099adca')
				)
			),
			'Single' => array(
				'QuizFrameDisplayQuiz' => array(
					'quiz_key' => '5fdb4f0049f3bddeabc49cd2b72c6ac9',
				)
			)
		);
		return $data;
	}

/**
 * validateFrameDisplayQuiz()のテスト
 *
 * @return void
 */
	public function testValidateFrameDisplayQuiz() {
		$model = $this->_modelName;
		$methodName = $this->_methodName;

		//データ生成
		$data = $this->_getData();
		$data['Single'] = array();

		//テスト実施
		$result = $this->$model->$methodName($data);

		//チェック
		$this->assertFalse($result);
	}

}
