<?php
/**
 * QuizAnswer::validate()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsModelTestCase', 'NetCommons.TestSuite');
App::uses('QuizAnswerFixture', 'Quizzes.Test/Fixture');
App::uses('QuizQuestionFixture', 'Quizzes.Test/Fixture');
App::uses('QuizzesComponent', 'Quizzes.Controller/Component');
App::uses('QuizDataGetTest', 'Quizzes.TestSuite');

/**
 * QuizAnswer::validate()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\Model\QuizAnswer
 */
class QuizAnswerValidateTest extends NetCommonsModelTestCase {

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
	protected $_modelName = 'QuizAnswer';

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
 * @param array $options オプション情報
 * @dataProvider dataProviderValidationError
 * @return void
 */
	public function testValidationError(
		$data, $field, $value, $message, $overwrite = array(), $options = array()) {
		$model = $this->_modelName;

		if (is_null($value)) {
			unset($data[$model][$field]);
		} else {
			$data[$model][$field] = $value;
		}
		$data = Hash::merge($data, $overwrite);

		//validate処理実行
		$this->$model->set($data);
		$result = $this->$model->validates($options);
		$this->assertFalse($result);
		if ($message) {
			$this->assertEquals($this->$model->validationErrors[$field][0], $message);
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
		// 選択肢タイプ
		$data['QuizAnswer'] = (new QuizAnswerFixture())->records[15];
		$dataGet = new QuizDataGetTest();
		$quiz = $dataGet->getData(46);
		$question = $quiz['QuizPage'][0]['QuizQuestion'][0];

		return array(
			array('data' => $data, 'field' => 'answer_value', 'value' => 'aaaa',
				'message' => __d('net_commons', 'Invalid request.'),
				'overwrite' => array(),
				'options' => array('question' => $question)),
		);
	}

}
