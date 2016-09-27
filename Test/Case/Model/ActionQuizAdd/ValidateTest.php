<?php
/**
 * ActionQuizAdd::validate()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsValidateTest', 'NetCommons.TestSuite');
App::uses('QuizzesComponent', 'Quizzes.Controller/Component');
App::uses('QuizDataGetTest', 'Quizzes.TestSuite');

/**
 * ActionQuizAdd::validate()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\Model\ActionQuizAdd
 */
class ActionQuizAddValidateTest extends NetCommonsValidateTest {

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
	protected $_modelName = 'ActionQuizAdd';

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
		$data['ActionQuizAdd'] = array(
			'create_option' => QuizzesComponent::QUIZ_CREATE_OPT_NEW,
			'title' => '',
			'past_quiz_id' => ''
		);

		return array(
			array('data' => $data, 'field' => 'create_option', 'value' => 'aaa',
				'message' => __d('quizzes', 'Please choose create option.')),
			array('data' => $data, 'field' => 'title', 'value' => '',
				'message' => __d('net_commons', 'Please input %s.', __d('quizzes', 'Title'))),
			array('data' => $data, 'field' => 'past_quiz_id', 'value' => '',
				'message' => __d('quizzes', 'Please select past quiz.'),
				'overwrite' => array(
					'ActionQuizAdd' => array(
						'create_option' => QuizzesComponent::QUIZ_CREATE_OPT_REUSE))),
		);
	}

}
