<?php
/**
 * QuizQuestion::validate()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsValidateTest', 'NetCommons.TestSuite');
App::uses('QuizQuestionFixture', 'Quizzes.Test/Fixture');
App::uses('QuizzesComponent', 'Quizzes.Controller/Component');

/**
 * QuizQuestion::validate()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\Model\QuizQuestion
 */
class QuizQuestionValidateTest extends NetCommonsValidateTest {

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
	protected $_modelName = 'QuizQuestion';

/**
 * Method name
 *
 * @var string
 */
	protected $_methodName = 'validates';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		Current::$current['Language']['id'] = '2';
	}

/**
 * Validatesのテスト
 *
 * @param array $data 登録データ
 * @param string $field フィールド名
 * @param string $value セットする値
 * @param string $message エラーメッセージ
 * @param array $overwrite 上書きするデータ
 * @dataProvider dataProviderValidationError
 * @return void
 */
	public function testValidationError($data, $field, $value, $message, $overwrite = array()) {
		$model = $this->_modelName;

		if (is_null($value)) {
			unset($data[$model][$field]);
		} else {
			$data[$model][$field] = $value;
		}
		$options = Hash::get($overwrite, 'options');
		$overwrite = Hash::remove($overwrite, 'options');
		$data = Hash::merge($data, $overwrite);

		//validate処理実行
		$this->$model->set($data);
		$result = $this->$model->validates($options);
		$this->assertFalse($result);

		if ($message) {
			$this->assertEquals($message, $this->$model->validationErrors[$field][0]);
		}
	}

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
		$data['QuizQuestion'] = (new QuizQuestionFixture())->records[0];

		return array(
			array(
				'data' => $data,
				'field' => 'question_type',
				'value' => '12',
				'message' => __d('net_commons', 'Invalid request.'),
				'overwrite' => array('options' => array('questionIndex' => 0))
			),
			array(
				'data' => $data,
				'field' => 'question_sequence',
				'value' => 'aa',
				'message' => __d('net_commons', 'Invalid request.'),
				'overwrite' => array('options' => array('questionIndex' => 0))
			),
			array(
				'data' => $data,
				'field' => 'question_sequence',
				'value' => '12',
				'message' => __d('quizzes', 'question sequence is illegal.'),
				'overwrite' => array('options' => array('questionIndex' => 0))
			),
			array(
				'data' => $data,
				'field' => 'question_value',
				'value' => '',
				'message' => __d('quizzes', 'Please input question text.'),
				'overwrite' => array('options' => array('questionIndex' => 0))
			),
			array(
				'data' => $data,
				'field' => 'is_choice_random',
				'value' => 'aa',
				'message' => __d('net_commons', 'Invalid request.'),
				'overwrite' => array('options' => array('questionIndex' => 0))
			),
			array(
				'data' => $data,
				'field' => 'is_choice_horizon',
				'value' => 'aa',
				'message' => __d('net_commons', 'Invalid request.'),
				'overwrite' => array('options' => array('questionIndex' => 0))
			),
			array(
				'data' => $data,
				'field' => 'is_order_fixed',
				'value' => 'aa',
				'message' => __d('net_commons', 'Invalid request.'),
				'overwrite' => array('options' => array('questionIndex' => 0))
			),
			array(
				'data' => $data,
				'field' => 'allotment',
				'value' => 'aa',
				'message' => __d('quizzes', 'Please enter a number greater than 0 .'),
				'overwrite' => array('options' => array('questionIndex' => 0))
			),
			array(
				'data' => $data,
				'field' => 'allotment',
				'value' => null,
				'message' => __d('quizzes', 'Please enter a number greater than 0 .'),
				'overwrite' => array('options' => array('questionIndex' => 0))
			),
			array(
				'data' => $data,
				'field' => 'allotment',
				'value' => '0.3',
				'message' => __d('quizzes', 'Please enter a number greater than 0 .'),
				'overwrite' => array('options' => array('questionIndex' => 0))
			),
			array(
				'data' => $data,
				'field' => 'allotment',
				'value' => '-10',
				'message' => __d('quizzes', 'Please enter a number greater than 0 .'),
				'overwrite' => array('options' => array('questionIndex' => 0))
			),
		);
	}
}
