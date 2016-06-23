<?php
/**
 * QuizFrameDisplayQuiz::validate()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsValidateTest', 'NetCommons.TestSuite');
App::uses('QuizFrameDisplayQuizFixture', 'Quizzes.Test/Fixture');

/**
 * QuizFrameDisplayQuiz::validate()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\Model\QuizFrameDisplayQuiz
 */
class QuizFrameDisplayQuizValidateTest extends NetCommonsValidateTest {

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
	protected $_modelName = 'QuizFrameDisplayQuiz';

/**
 * Method name
 *
 * @var string
 */
	protected $_methodName = 'validates';

/**
 * ValidationErrorのDataProvider
 *
 * ### 戻り値
 *  - data 登録データ
 *  - field フィールド名
 *  - value セットする値
 *  - message エラーメッセージ
 *  - overwrite 上書きするデータ(省略可)
 *
 * @return array テストデータ
 */
	public function dataProviderValidationError() {
		$data['QuizFrameDisplayQuiz'] = (new QuizFrameDisplayQuizFixture())->records[0];

		return array(
			array('data' => $data, 'field' => 'quiz_key', 'value' => '',
				'message' => __d('net_commons', 'Invalid request.')),
			//array('data' => $data, 'field' => 'quiz_key', 'value' => null,
			//	'message' => __d('net_commons', 'Invalid request.')),
			array('data' => $data, 'field' => 'quiz_key', 'value' => 'error_ni_naru_code',
				'message' => __d('net_commons', 'Invalid request.')),
		);
	}

}
