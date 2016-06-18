<?php
/**
 * QuizAnswerSummary::getCanGradingSummary()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsGetTest', 'NetCommons.TestSuite');
App::uses('QuizzesComponent', 'Quizzes.Controller/Component');

/**
 * QuizAnswerSummary::getCanGradingSummary()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\Model\QuizAnswerSummary
 */
class QuizAnswerSummaryGetCanGradingSummaryTest extends NetCommonsGetTest {

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
		'plugin.authorization_keys.authorization_keys',
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
	protected $_modelName = 'QuizAnswerSummary';

/**
 * Method name
 *
 * @var string
 */
	protected $_methodName = 'getCanGradingSummary';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		// 試験のため、現在存在する回答データをテスト状態回答とします
		$this->QuizAnswerSummary = ClassRegistry::init('Quizzes.QuizAnswerSummary');
		$this->QuizAnswerSummary->Behaviors->unload('Mails.MailQueue');
		//データ生成
		$data = array(
			'test_status' => QuizzesComponent::TEST_ANSWER_STATUS_PEFORM,
			'answer_status' => QuizzesComponent::ACTION_ACT,
			'is_grade_finished' => false,
			'quiz_key' => 'cc38fc4c532f2252c3d0861df0c8866c'
		);
		$this->QuizAnswerSummary->save($data, false);
	}

/**
 * getCanGradingSummary()のテスト
 * 一般の人対象：
 * 管理者の人が作ったテストに回答がある（Fixtureで作成済み）
 * 一般の人が作成したテストに回答がある（ここでinsert）
 * 返ってくるのは一般の作ったテストのものだけ
 *
 * @return void
 */
	public function testGetCanGradingSummary() {
		$model = $this->_modelName;
		$methodName = $this->_methodName;

		Current::$current['Permission']['content_creatable']['value'] = true;
		Current::$current['Language']['id'] = 2;
		Current::$current['User']['id'] = 5;
		Current::$current['Block']['id'] = 2;

		//テスト実施
		$result = $this->$model->$methodName();

		//チェック
		$this->assertEqual($result, array(34 => 34));
	}
/**
 * getCanGradingSummary()のテスト
 * 編集長の人対象：
 * 管理者の人が作ったテストに回答がある（Fixtureで作成済み）
 * 管理者の人が作ったテストに完了してない回答がある（ここでinsert）
 * 返ってくるのは回答完了しているものだけ
 *
 * @return void
 */
	public function testGetCanGradingSummary2() {
		$model = $this->_modelName;
		$methodName = $this->_methodName;

		//データ生成
		$data = array(
			'test_answer' => QuizzesComponent::TEST_ANSWER_STATUS_PEFORM,
			'answer_status' => QuizzesComponent::ACTION_NOT_ACT,
			'is_grade_finished' => false,
			'quiz_key' => '5fdb4f0049f3bddeabc49cd2b72c6ac9'
			// このテストのIDは１です
		);
		$this->QuizAnswerSummary->save($data, false);
		$insertId = $this->QuizAnswerSummary->getLastInsertId();

		Current::$current['Permission']['content_editable']['value'] = true;
		Current::$current['Language']['id'] = 2;
		Current::$current['User']['id'] = 1;
		Current::$current['Block']['id'] = 2;

		//テスト実施
		$result = $this->$model->$methodName();

		//チェック
		// 返ってきたテストには１がないことを確認
		$ret = isset($result[$insertId]);
		$this->assertFalse($ret);
	}

}
