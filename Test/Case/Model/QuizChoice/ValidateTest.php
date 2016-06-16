<?php
/**
 * QuizChoice::validate()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsValidateTest', 'NetCommons.TestSuite');
App::uses('QuizChoiceFixture', 'Quizzes.Test/Fixture');
App::uses('QuizzesComponent', 'Quizzes.Controller/Component');

/**
 * QuizChoice::validate()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\Model\QuizChoice
 */
class QuizChoiceValidateTest extends NetCommonsValidateTest {

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
	protected $_modelName = 'QuizChoice';

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
		$data['QuizChoice'] = (new QuizChoiceFixture())->records[0];

		return array(
			array(
				'data' => $data,
				'field' => 'choice_sequence',
				'value' => 'aa',
				'message' => __d('quizzes', 'choice sequence is illegal.'),
				'overwrite' => array('options' => array('choiceIndex' => 0))
			),
			array(
				'data' => $data,
				'field' => 'choice_sequence',
				'value' => '12',
				'message' => __d('quizzes', 'choice sequence is illegal.'),
				'overwrite' => array('options' => array('choiceIndex' => 0))
			),
			array(
				'data' => $data,
				'field' => 'choice_label',
				'value' => '',
				'message' => __d('quizzes', 'Please input choice text.'),
				'overwrite' => array('options' => array('choiceIndex' => 0))
			),
			array(
				'data' => $data,
				'field' => 'choice_label',
				'value' => 'aiueo#||||||#kakiku',
				'message' => __d('quizzes', 'You can not use the string of #||||||# for choice text.'),
				'overwrite' => array('options' => array('choiceIndex' => 0))
			),
		);
	}

}
