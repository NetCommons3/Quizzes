<?php
/**
 * QuizQuestionValidateBehavior::validates()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsModelTestCase', 'NetCommons.TestSuite');
App::uses('QuizzesComponent', 'Quizzes.Controller/Component');
App::uses('QuizDataGetTest', 'Quizzes.TestSuite');

/**
 * QuizQuestionValidateBehavior::validates()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\Model\Behavior\QuizQuestionValidateBehavior
 */
class QuizQuestionValidateBehaviorValidatesTest extends NetCommonsModelTestCase {

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
		'plugin.authorization_keys.authorization_keys',
	);

/**
 * Plugin name
 *
 * @var string
 */
	public $plugin = 'quizzes';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		//テストプラグインのロード
		NetCommonsCakeTestCase::loadTestPlugin($this, 'Quizzes', 'TestQuizzes');
		$this->TestModel = ClassRegistry::init('TestQuizzes.TestQuizQuestionValidateBehaviorValidatesModel');
	}

/**
 * __getData
 *
 * テストデータ取得
 * @return array
 */
	private function __getData() {
		$data = array(
			'QuizQuestion' => array(
				'question_sequence' => '0',
				'question_value' => '<p>新規問題1</p>',
				'question_type' => '1',
				'is_choice_random' => 0,
				'is_choice_horizon' => 0,
				'is_order_fixed' => 0,
				'allotment' => '10',
				'commentary' => '',
				'quiz_page_id' => '1',
			),
			'QuizChoice' => array(
				array(
					'choice_sequence' => '0',
					'choice_label' => '新規選択肢1',
				),
				array(
					'choice_sequence' => '1',
					'choice_label' => '新規選択肢2',
				),
				array(
					'choice_sequence' => '2',
					'choice_label' => '新規選択肢3',
				),
			),
			'QuizCorrect' => array(
				array(
					'correct_sequence' => '0',
					'correct' => '新規選択肢1',
				)
			)
		);
		return $data;
	}
/**
 * Validatesのテスト
 *
 * @param array $data 登録データ
 * @param string $field フィールド名
 * @param string $value セットする値
 * @param string $message エラーメッセージ
 * @param array $overwrite 上書きするデータ
 * @param string $errorField エラーメッセージが入っているフィールド名
 * @dataProvider dataProviderValidationError
 * @return void
 */
	public function testValidates($data, $field, $value, $message, $overwrite, $errorField) {
		$model = 'QuizQuestion';

		if (strpos($field, 'Quiz') === 0) {
			if (is_null($value)) {
				unset($data[$field]);
			} else {
				$data[$field] = $value;
			}
		} else {
			if (is_null($value)) {
				unset($data[$model][$field]);
			} else {
				$data[$model][$field] = $value;
			}
		}
		$data = Hash::merge($data, $overwrite);

		//テスト実施
		$this->TestModel->set($data);
		$result = $this->TestModel->validates();
		$this->assertFalse($result);

		if ($message) {
			$this->assertEquals($message, $this->TestModel->validationErrors[$errorField][0]);
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
		//テストデータ
		$data = $this->__getData();
		return array(
			array(
				'data' => $data,
				'field' => 'QuizChoice',
				'value' => null,
				'message' => __d('quizzes', 'please set at least one choice.'),
				'overwrite' => array(),
				'errorField' => 'question_pickup_error'
			),
			array(
				'data' => $data,
				'field' => 'QuizCorrect',
				'value' => null,
				'message' => __d('quizzes', 'please set at least one correct.'),
				'overwrite' => array(),
				'errorField' => 'question_pickup_error'
			),
		);
	}

}
