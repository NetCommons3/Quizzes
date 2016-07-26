<?php
/**
 * QuizAnswersController::grading()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsControllerTestCase', 'NetCommons.TestSuite');

/**
 * QuizAnswersController::grading()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\Controller\QuizAnswersController
 */
class QuizAnswersControllerGradingTest extends NetCommonsControllerTestCase {

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
		'plugin.authorization_keys.authorization_keys',
	);

/**
 * Plugin name
 *
 * @var string
 */
	public $plugin = 'quizzes';

/**
 * Controller name
 *
 * @var string
 */
	protected $_controller = 'quiz_answers';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->generateNc(Inflector::camelize($this->_controller));
		$this->_targetController = $this->generate('Quizzes.QuizAnswers', array(
			'components' => array(
				'Auth' => array('user'),
				'Session',
				'Security',
				'NetCommons.Permission',
				'AuthorizationKeys.AuthorizationKey',
				'VisualCaptcha.VisualCaptcha',
				'Quizzes.QuizzesOwnAnswerQuiz',	// 回答済み小テスト管理
				'Quizzes.QuizzesOwnAnswer',		// 回答ID管理
				'Quizzes.QuizzesPassQuiz',		// 合格小テスト管理
				'Quizzes.QuizzesAnswerStart',
				//'Quizzes.QuizzesShuffle',
			)
		));
	}
/**
 * テストDataの取得
 *
 * @return array
 */
	private function __urlOption() {
		$frameId = '6';
		$blockId = '2';
		$contentKey = 'content_key_1';

		$data = array(
			'action' => 'grading',
			'frame_id' => $frameId,
			'block_id' => $blockId,
			'key' => $contentKey,
		);

		return $data;
	}
/**
 * テストDataの取得
 *
 * @param string $role ロール
 * @return array
 */
	private function __data($role = null) {
		$data = array(
			'data' => array(
				'Frame' => array('id' => 6),
				'Block' => array('id' => 2),
				'QuizAnswerGrade' => array(
					'34' => array(
						'id' => 34,
						'quiz_question_key' => '9cc4e8ba1f575fb349e74c5f958c4a69',
						'correct_status' => 2,
						'score' => 10,
					)
				)
			)
		);

		return $data;
	}
/**
 * gradingアクションのテスト
 * 未ログイン：自分の回答ではない：BadRequest
 *
 * @return void
 */
	public function testViewNoLoginNoAnswer() {
		$data = $this->__urlOption();
		$urlOptions = Hash::insert($data, 'key', '64f129efa1cc9f1f21feaa7052f3b86c');
		//テスト実行
		$url = Hash::merge(array(
			'plugin' => $this->plugin,
			'controller' => $this->_controller,
			'action' => 'grading',
		), $urlOptions);
		$url[] = '11';
		$this->setExpectedException('BadRequestException');
		$this->_testGetAction($url, null);
	}
/**
 * gradingアクションのテスト
 * 未ログイン：存在しないsummaryId：Forbidden
 *
 * @return void
 */
	public function testViewNoLoginNotExsitSummary() {
		$data = $this->__urlOption();
		$urlOptions = Hash::insert($data, 'key', '64f129efa1cc9f1f21feaa7052f3b86c');
		//テスト実行
		$url = Hash::merge(array(
			'plugin' => $this->plugin,
			'controller' => $this->_controller,
			'action' => 'grading',
		), $urlOptions);
		$url[] = '99999';
		$this->setExpectedException('ForbiddenException');
		$this->_testGetAction($url, null);
	}
/**
 * gradingアクションのテスト
 * 未ログイン：サマリID未指定：Forbidden
 *
 * @return void
 */
	public function testViewNoLoginNoSummaryId() {
		$data = $this->__urlOption();
		$urlOptions = Hash::insert($data, 'key', '64f129efa1cc9f1f21feaa7052f3b86c');
		//テスト実行
		$url = Hash::merge(array(
			'plugin' => $this->plugin,
			'controller' => $this->_controller,
			'action' => 'grading',
		), $urlOptions);
		$this->setExpectedException('ForbiddenException');
		$this->_testGetAction($url, null);
	}
/**
 * gradingアクションのテスト
 * 未ログイン：正しく表示される
 *
 * @return void
 */
	public function testViewNoLogin() {
		$data = $this->__urlOption();
		$urlOptions = Hash::insert($data, 'key', '64f129efa1cc9f1f21feaa7052f3b86c');
		//テスト実行
		$url = Hash::merge(array(
			'plugin' => $this->plugin,
			'controller' => $this->_controller,
			'action' => 'grading',
			0 => '11',
		), $urlOptions);
		$this->_targetController->QuizzesOwnAnswer->expects($this->any())
			->method('checkOwnAnsweredSummaryId')
			->will($this->returnValue(true));
		$result = $this->_testGetAction($url, null);
		$this->assertTextContains(sprintf(__d('quizzes', 'Question %2d:'), 1), $result);
		$this->assertTextNotContains(sprintf(__d('quizzes', 'Question %2d:'), 2), $result);
	}
/**
 * gradingアクションのテスト
 * 一般ログイン：自分が作成者じゃない小テスト：サマリID未指定：Forbidden
 *
 * @return void
 */
	public function testViewGeneralLoginViewNoCreator() {
		//ログイン
		TestAuthGeneral::login($this, Role::ROOM_ROLE_KEY_GENERAL_USER);

		$data = $this->__urlOption();
		$urlOptions = Hash::insert($data, 'key', '64f129efa1cc9f1f21feaa7052f3b86c');
		//テスト実行
		$url = Hash::merge(array(
			'plugin' => $this->plugin,
			'controller' => $this->_controller,
			'action' => 'grading',
			0 => '11',
		), $urlOptions);
		$this->setExpectedException('BadRequestException');
		$this->_testGetAction($url, null);

		//ログアウト
		TestAuthGeneral::logout($this);
	}
/**
 * gradingアクションのテスト
 * 一般ログイン：自分が作成者小テスト：サマリID未指定：Forbidden
 *
 * @return void
 */
	public function testViewGeneralLoginCreator() {
		//ログイン
		TestAuthGeneral::login($this, Role::ROOM_ROLE_KEY_GENERAL_USER);

		// 一般がつくったことにしておく
		// なおかつ、正解や統計表示にもしておく
		$this->Quiz = ClassRegistry::init('Quizzes.Quiz');
		$this->Quiz->updateAll(
			['Quiz.created_user' => 4,
				'Quiz.is_correct_show' => true,
				'Quiz.is_total_show' => true
			],
			['Quiz.key' => '64f129efa1cc9f1f21feaa7052f3b86c']);

		$data = $this->__urlOption();
		$urlOptions = Hash::insert($data, 'key', '64f129efa1cc9f1f21feaa7052f3b86c');
		//テスト実行
		$url = Hash::merge(array(
			'plugin' => $this->plugin,
			'controller' => $this->_controller,
			'action' => 'grading',
			0 => '11',
		), $urlOptions);
		$result = $this->_testGetAction($url, null);
		$this->assertTextContains('<form ', $result);

		//ログアウト
		TestAuthGeneral::logout($this);
	}
/**
 * アクションのPOSTテスト
 * 採点POST
 *
 * @return void
 */
	public function testGradingPost() {
		$data = $this->__data();

		TestAuthGeneral::login($this, Role::ROOM_ROLE_KEY_EDITOR);

		$this->_testPostAction('post', $data, array(
			'action' => 'grading',
			'frame_id' => 6,
			'block_id' => 2,
			'key' => '83b294e176a8c8026d4fbdb07ad2ed7f',
			0 => 31));

		TestAuthGeneral::logout($this);
	}
/**
 * アクションのPOSTテスト
 * 採点POST
 * 一般会員が自分の回答に対して採点をPOST
 * でもテスト作成者は管理者だから編集（＝採点）は不可能
 *
 * @return void
 */
	public function testGradingPostNG() {
		$data = $this->__data();

		$this->_targetController->QuizzesOwnAnswer->expects($this->any())
			->method('checkOwnAnsweredSummaryId')
			->will($this->returnValue(true));

		TestAuthGeneral::login($this, Role::ROOM_ROLE_KEY_GENERAL_USER);

		$this->setExpectedException('BadRequestException');

		$this->_testPostAction('post', $data, array(
			'action' => 'grading',
			'frame_id' => 6,
			'block_id' => 2,
			'key' => '83b294e176a8c8026d4fbdb07ad2ed7f',
			0 => 31));

		TestAuthGeneral::logout($this);
	}
/**
 * editアクションのValidateionErrorテスト
 *
 * @param array $data post データ
 * @param array $urlOptions urlパラメータ配列データ
 * @param array $validationError バリデーションエラー情報
 * @dataProvider dataProviderEditValidationError
 * @return mixed テスト結果
 */
	public function testEditValidationError($data, $urlOptions, $validationError = null) {
		//ログイン
		TestAuthGeneral::login($this, Role::ROOM_ROLE_KEY_ROOM_ADMINISTRATOR);

		//テスト実施
		$this->_testActionOnValidationError(
			'put', $data, $urlOptions, $validationError
		);

		//ログアウト
		TestAuthGeneral::logout($this);
	}
/**
 * editアクションのValidationErrorテスト用DataProvider
 *
 * ### 戻り値
 *  - data: 登録データ
 *  - urlOptions: URLオプション
 *  - validationError: バリデーションエラー
 *
 * @return array
 */
	public function dataProviderEditValidationError() {
		$data = $this->__data();

		$urlOptions = array(
			'action' => 'grading',
			'frame_id' => 6,
			'block_id' => 2,
			'key' => '83b294e176a8c8026d4fbdb07ad2ed7f',
			0 => 31
		);

		//テストデータ
		$results = array();
		array_push($results, array(
			'data' => Hash::merge($data, array(
				'data' => array('QuizAnswerGrade' => array('34' => array('id' => 'aaaa'))))),
			'urlOptions' => $urlOptions,
			'validationError' => array(
				'field' => 'id',
				'value' => '',
				'message' => __d('net_commons', 'Invalid request.')
			)
		));
		array_push($results, array(
			'data' => Hash::merge($data, array(
				'data' => array('QuizAnswerGrade' => array('34' => array('correct_status' => 'aaaa'))))),
			'urlOptions' => $urlOptions,
			'validationError' => array(
				'field' => 'id',
				'value' => '',
				'message' => __d('net_commons', 'Invalid request.')
			)
		));
		array_push($results, array(
			'data' => Hash::merge($data, array(
				'data' => array('QuizAnswerGrade' => array('34' => array('score' => 'aaaa'))))),
			'urlOptions' => $urlOptions,
			'validationError' => array(
				'field' => 'score',
				'value' => 'aaa',
				'message' => __d('quizzes', 'Please input natural number.')
			)
		));
		return $results;
	}

}
