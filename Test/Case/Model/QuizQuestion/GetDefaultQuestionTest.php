<?php
/**
 * QuizQuestion::getDefaultQuestion()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsGetTest', 'NetCommons.TestSuite');
App::uses('QuizzesComponent', 'Quizzes.Controller/Component');
App::uses('QuizQuestion', 'Quizzes.Model');

/**
 * QuizQuestion::getDefaultQuestion()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\Model\QuizQuestion
 */
class QuizQuestionGetDefaultQuestionTest extends NetCommonsGetTest {

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
	protected $_modelName = 'QuizQuestion';

/**
 * Method name
 *
 * @var string
 */
	protected $_methodName = 'getDefaultQuestion';

/**
 * getDefaultQuestionのテスト
 *
 * @param array $expected 期待値（取得したキー情報）
 * @dataProvider dataProviderGet
 *
 * @return void
 */
	public function testGetDefaultQuestion($expected) {
		$model = $this->_modelName;
		$method = $this->_methodName;

		//テスト実行
		$result = $this->$model->$method();
		// Choice, Correctは省く
		$result = Hash::remove($result, 'QuizChoice');
		$result = Hash::remove($result, 'QuizCorrect');

		//チェック
		$this->assertEquals($result, $expected);
	}

/**
 * getDefaultQuestionのDataProvider
 *
 * #### 戻り値
 *  - array 取得するキー情報
 *  - array 期待値 （取得したキー情報）
 *
 * @return array
 */
	public function dataProviderGet() {
		$expect = array(
			'question_sequence' => 0,
			'question_value' => __d('quizzes', 'New Question') . '1',
			'question_type' => QuizzesComponent::TYPE_SELECTION,
			'is_choice_random' => QuizzesComponent::USES_NOT_USE,
			'is_choice_horizon' => QuizzesComponent::USES_NOT_USE,
			'is_order_fixed' => QuizzesComponent::USES_NOT_USE,
			'allotment' => QuizQuestion::QUIZ_QUESTION_DEFAULT_ALLOTMENT,
			'commentary' => '',
		);
		return array(
			array($expect),
		);
	}
}
