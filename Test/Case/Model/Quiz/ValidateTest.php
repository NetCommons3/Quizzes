<?php
/**
 * Quiz::validate()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsValidateTest', 'NetCommons.TestSuite');
App::uses('QuizFixture', 'Quizzes.Test/Fixture');
App::uses('QuizzesComponent', 'Quizzes.Controller/Component');

/**
 * Quiz::validate()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\Model\Quiz
 */
class QuizValidateTest extends NetCommonsValidateTest {

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
		$data['Quiz'] = (new QuizFixture())->records[0];
		$data['Quiz']['answer_timing'] = QuizzesComponent::USES_USE;
		$data['Quiz']['answer_start_period'] = '2016-01-01 00:00:00';
		$data['Quiz']['answer_end_period'] = '2017-01-01 00:00:00';

		return array(
			array('data' => $data, 'field' => 'block_id', 'value' => 'aa',
				'message' => __d('net_commons', 'Invalid request.')),
			array('data' => $data, 'field' => 'title', 'value' => '',
				'message' => sprintf(__d('net_commons', 'Please input %s.'), __d('quizzes', 'Title'))),
			array('data' => $data, 'field' => 'passing_grade', 'value' => '-1',
				'message' => __d('quizzes', 'Please input natural number.')),
			array('data' => $data, 'field' => 'passing_grade', 'value' => '0.4',
				'message' => __d('quizzes', 'Please input natural number.')),
			array('data' => $data, 'field' => 'passing_grade', 'value' => 'aa',
				'message' => __d('quizzes', 'Please input natural number.')),
			array('data' => $data, 'field' => 'estimated_time', 'value' => '-1',
				'message' => __d('quizzes', 'Please input natural number.')),
			array('data' => $data, 'field' => 'estimated_time', 'value' => '0.4',
				'message' => __d('quizzes', 'Please input natural number.')),
			array('data' => $data, 'field' => 'estimated_time', 'value' => 'aa',
				'message' => __d('quizzes', 'Please input natural number.')),
			array('data' => $data, 'field' => 'answer_timing', 'value' => '2',
				'message' => __d('net_commons', 'Invalid request.')),
			array('data' => $data, 'field' => 'is_no_member_allow', 'value' => '2',
				'message' => __d('net_commons', 'Invalid request.')),
			//#10
			array('data' => $data, 'field' => 'is_key_pass_use', 'value' => '2',
				'message' => __d('net_commons', 'Invalid request.'),
				'overwrite' => array(
					'AuthorizationKey' => array('authorization_key' => 'aaa'))
			),
			array('data' => $data, 'field' => 'is_key_pass_use', 'value' => '1',
				'message' => __d('quizzes', 'Please input key phrase.'),
			),
			array('data' => $data, 'field' => 'is_image_authentication', 'value' => '1',
				'message' =>
					__d('quizzes',
						'Authentication key setting , image authentication , either only one can not be selected.'),
				'overwrite' => array(
					'Quiz' => array('is_key_pass_use' => '1'),
					'AuthorizationKey' => array('authorization_key' => 'aaa')
				)
			),
			array('data' => $data, 'field' => 'is_repeat_allow', 'value' => '2',
				'message' => __d('net_commons', 'Invalid request.')),
			array(
				'data' => $data,
				'field' => 'is_image_authentication',
				'value' => '2',
				'message' => __d('net_commons', 'Invalid request.'),
			),
			array('data' => $data, 'field' => 'answer_start_period', 'value' => 'aaaaa',
				'message' => sprintf(
				__d('net_commons', 'Unauthorized pattern for %s. Please input the data in %s format.'),
				__d('quizzes', 'Start period'), 'YYYY-MM-DD hh:mm:ss')
			),
			array('data' => $data, 'field' => 'answer_end_period', 'value' => '2015-01-01 00:00:00',
				'message' => __d('quizzes', 'start period must be smaller than end period.')),
		);
	}

}
