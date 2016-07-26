<?php
/**
 * QuizPage::validate()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsValidateTest', 'NetCommons.TestSuite');
App::uses('QuizPageFixture', 'Quizzes.Test/Fixture');
App::uses('QuizQuestionFixture', 'Quizzes.Test/Fixture');
App::uses('QuizzesComponent', 'Quizzes.Controller/Component');

/**
 * QuizPage::validate()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\Model\QuizPage
 */
class QuizPageValidateTest extends NetCommonsValidateTest {

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
	protected $_modelName = 'QuizPage';

/**
 * Method name
 *
 * @var string
 */
	protected $_methodName = 'validates';

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
			$errors = Hash::flatten($this->$model->validationErrors);
			//$this->assertEquals($message, $this->$model->validationErrors[$field][0]);
			$this->assertEquals($message, $errors[$field . '.0']);
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
		$data['QuizPage'] = (new QuizPageFixture())->records[0];
		$question = (new QuizQuestionFixture())->records[0];
		$data2 = Hash::merge($data, array('QuizQuestion' => array()));
		$data3 = Hash::merge($data, array('QuizQuestion' => array($question)));
		return array(
			array(
				'data' => $data,
				'field' => 'page_sequence',
				'value' => 'aa',
				'message' => __d('quizzes', 'question sequence is illegal.'),
				'overwrite' => array('options' => array('pageIndex' => '0'))
			),
			array(
				'data' => $data,
				'field' => 'page_sequence',
				'value' => '12',
				'message' => __d('quizzes', 'question sequence is illegal.'),
				'overwrite' => array('options' => array('pageIndex' => '0'))
			),
			array(
				'data' => $data2,
				'field' => 'page_pickup_error',
				'value' => '0',
				'message' => __d('quizzes', 'please set at least one question.'),
				'overwrite' => array('options' => array('pageIndex' => '0'))
			),
			array(
				'data' => $data3,
				'field' => 'QuizQuestion.0.question_pickup_error',
				'value' => '0',
				'message' => __d('quizzes', 'please set at least one choice.'),
				'overwrite' => array('options' => array('pageIndex' => '0'))
			),
		);
	}

}
