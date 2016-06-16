<?php
/**
 * ActionQuizAdd::checkPastQuiz()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsGetTest', 'NetCommons.TestSuite');
App::uses('QuizzesComponent', 'Quizzes.Controller/Component');
App::uses('QuizDataGetTest', 'Quizzes.TestSuite');

/**
 * ActionQuizAdd::checkPastQuiz()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\Model\ActionQuizAdd
 */
class ActionQuizAddCheckPastQuizTest extends NetCommonsGetTest {

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
	protected $_modelName = 'ActionQuizAdd';

/**
 * Method name
 *
 * @var string
 */
	protected $_methodName = 'checkPastQuiz';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Quiz = ClassRegistry::init('Quizzes.Quiz');
		$this->Quiz->Behaviors->unload('AuthorizationKey');
		Current::$current['Block']['id'] = 2;
	}

/**
 * testCheckPastQuiz
 *
 * @param array $data POSTデータ
 * @param array $check チェックデータ
 * @param array $expected 期待値（取得したキー情報）
 * @dataProvider dataProviderValidationError
 *
 * @return void
 */
	public function testCheckPastQuestionnaire($data, $check, $expected) {
		$model = $this->_modelName;
		$method = $this->_methodName;

		$this->$model->create();
		$this->$model->set($data);
		//テスト実行
		$result = $this->$model->$method($check);
		//チェック
		$this->assertEquals($result, $expected);
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
		return array(
			// optionが違う
			array(
				array('ActionQuizAdd' => array(
					'create_option' => 'aaaa'
				)),
				array('past_quiz_id' => 'aaa'),
				true
			),
			// 過去の小テストIDがおかしい
			array(
				array('ActionQuizAdd' => array(
					'create_option' => 'reuse',
					'past_quiz_id' => 'aaa'
				)),
				array('past_quiz_id' => 'aaa'),
				false
			),
			// 言語違い
			array(
				array('ActionQuizAdd' => array(
					'create_option' => 'reuse',
					'past_quiz_id' => '56'
				)),
				array('past_quiz_id' => '56'),
				false
			),
			// 一時保存中は使えない
			array(
				array('ActionQuizAdd' => array(
					'create_option' => 'reuse',
					'past_quiz_id' => '36'
				)),
				array('past_quiz_id' => '36'),
				false
			),
			// 使える
			array(
				array('ActionQuizAdd' => array(
					'create_option' => 'reuse',
					'past_quiz_id' => '6'
				)),
				array('past_quiz_id' => '6'),
				true
			),
		);
	}

}
