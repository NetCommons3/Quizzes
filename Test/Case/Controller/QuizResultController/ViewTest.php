<?php
/**
 * QuizResultController::view()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('WorkflowControllerViewTest', 'Workflow.TestSuite');

/**
 * QuizResultController::view()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\Controller\QuizResultController
 */
class QuizResultControllerViewTest extends WorkflowControllerViewTest {

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
	protected $_controller = 'quiz_result';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->generateNc(Inflector::camelize($this->_controller));
		$this->_targetController = $this->generate('Quizzes.QuizResult', array(
			'components' => array(
				'Auth' => array('login'),
				'Session',
				'Security',
				//'NetCommons.Permission',
				//'Quizzes.QuizzesOwnAnswerQuiz',   // 回答済み小テスト管理
				'Quizzes.QuizzesOwnAnswer', // 回答ID管理
				//'Quizzes.QuizzesPassQuiz',        // 合格小テスト管理
				//'Quizzes.QuizzesAnswerStart',
			)
		));
	}
/**
 * テストDataの取得
 *
 * @param string $contentKey 小テストキー
 * @return array
 */
	private function __data($contentKey) {
		$frameId = '6';
		$blockId = '2';

		$data = array(
			'action' => 'view',
			'frame_id' => $frameId,
			'block_id' => $blockId,
			'key' => $contentKey,
		);

		return $data;
	}

/**
 * viewアクションのテスト用DataProvider
 *
 * ### 戻り値
 *  - urlOptions: URLオプション
 *  - assert: テストの期待値
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderView() {
		//テストデータ
		$results = array();
		// サマリIDを指定していないときは
		//「自分の回答したデータ」を見ることになるので 特に問題なく画面は表示される
		//
		// サマリIDを指定したときは
		// 「解答した」情報がセッションに残ってないのは見れない
		// 「解答した」情報がセッションに残っているのは見られる

		$myAnswer = $this->__data('64f129efa1cc9f1f21feaa7052f3b86c');
		$othersAnswer = $this->__data('7ac353d879f3ec845f2333d405793afe');

		$results[0] = array(
			'urlOptions' => $myAnswer,
			'assert' => array('method' => 'assertNotEmpty'),
		);
		$results[1] = array(
			'urlOptions' => $othersAnswer,
			'assert' => array('method' => 'assertNotEmpty'),
		);

		// サマリID指定あり
		$results[2] = array(
			'urlOptions' => Hash::insert($myAnswer, 0, '11'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		$results[3] = array(
			'urlOptions' => Hash::insert($othersAnswer, 0, '15'),
			'assert' => null, 'exception' => 'BadRequestException'
		);
		// 存在しないサマリID
		$results[4] = array(
			'urlOptions' => Hash::insert($othersAnswer, 0, '99'),
			'assert' => null, 'exception' => 'BadRequestException'
		);
		// 非会員には公開してない
		$results[5] = array(
			'urlOptions' => $this->__data('52fc8a15a76b4f315db20e319de5c6d0'),
			'assert' => null, 'exception' => 'BadRequestException'
		);
		return $results;
	}

/**
 * viewアクションのテスト
 *
 * @param array $urlOptions URLオプション
 * @param array $assert テストの期待値
 * @param string|null $exception Exception
 * @param string $return testActionの実行後の結果
 * @dataProvider dataProviderView
 * @return void
 */
	public function testView($urlOptions, $assert, $exception = null, $return = 'view') {
		$this->_targetController->QuizzesOwnAnswer->expects($this->any())
			->method('checkOwnAnsweredSummaryId')
			->will($this->returnValueMap([
				['11', true],
				['15', false],
				['99', false],
			]));
		//テスト実行
		parent::testView($urlOptions, $assert, $exception, $return);
		if ($exception) {
			return;
		}
	}

/**
 * viewアクションのテスト(作成権限のみ)用DataProvider
 *
 * ### 戻り値
 *  - urlOptions: URLオプション
 *  - assert: テストの期待値
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderViewByCreatable() {
		//テストデータ
		// 自分が作ってない小テストで「解答した」情報がDBに残ってないのは見れない
		// 自分が作ってない小テストで「解答した」情報がDBに残っているのは見られる
		// 自分がつくった小テストなら誰のものでも見られる

		// 自分がつくって公開中
		$results[0] = array(
			'urlOptions' => $this->__data('cc38fc4c532f2252c3d0861df0c8866c'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		// 他人が作って公開中小テスト
		$results[1] = array(
			'urlOptions' => $this->__data('a916437af184b4a185f685a93099adca'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		// 自分がつくって一時保存
		$results[2] = array(
			'urlOptions' => $this->__data('e3eee47e033eccc3f42c02d75678235b'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		// 他人がつくって承認待ち
		$results[3] = array(
			'urlOptions' => $this->__data('9e003ee9cd538f7a4cf9d73b0b3470c4'),
			'assert' => null, 'exception' => 'BadRequestException'
		);
		// 自分がつくって未来
		$results[4] = array(
			'urlOptions' => $this->__data('39e6aa5d38fff3230276e0abf408c9a6'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		// 他人が作って未来
		$results[5] = array(
			'urlOptions' => $this->__data('468e3c55607b0c1d5cf55ddad51f836a'),
			'assert' => null, 'exception' => 'BadRequestException'
		);
		// 自分がつくって公開はされているが回答期間が過去の小テスト
		$results[6] = array(
			'urlOptions' => $this->__data('41e2b809108edead2f30adc37f51e979'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		// 他人がつくって公開はされているが回答期間が過去の小テスト
		$results[7] = array(
			'urlOptions' => $this->__data('13001359fc46bd17c03451906eee7e4e'),
			'assert' => array('method' => 'assertNotEmpty'),
		);

		// サマリID指定あり
		// 自分がつくったテストで解答者他人
		// 一般では見ることができなくなりました
		$results[8] = array(
			'urlOptions' => Hash::insert($this->__data('83b294e176a8c8026d4fbdb07ad2ed7f'), 0, '26'),
			'assert' => null, 'exception' => 'BadRequestException'
		);
		// 他人がつくったテストで解答者自分
		$results[9] = array(
			'urlOptions' => Hash::insert($this->__data('c389a74ef01516f9b3e477afcf3dfa02'), 0, '35'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		// 他人がつくったテストで解答者他人
		$results[10] = array(
			'urlOptions' => Hash::insert($this->__data('64f129efa1cc9f1f21feaa7052f3b86c'), 0, '11'),
			'assert' => null, 'exception' => 'BadRequestException'
		);
		// 非会員には公開してない
		$results[11] = array(
			'urlOptions' => $this->__data('52fc8a15a76b4f315db20e319de5c6d0'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		return $results;
	}

/**
 * viewアクションのテスト(作成権限のみ)
 *
 * @param array $urlOptions URLオプション
 * @param array $assert テストの期待値
 * @param string|null $exception Exception
 * @param string $return testActionの実行後の結果
 * @dataProvider dataProviderViewByCreatable
 * @return void
 */
	public function testViewByCreatable($urlOptions, $assert, $exception = null, $return = 'view') {
		// 83b294e176a8c8026d4fbdb07ad2ed7fは一般が作成したことにしておく
		$this->Quiz = ClassRegistry::init('Quizzes.Quiz');
		$this->Quiz->updateAll(
			array('created_user' => '4'),
			array('Quiz.key' => '83b294e176a8c8026d4fbdb07ad2ed7f')
		);
		$this->_targetController->QuizzesOwnAnswer->expects($this->any())
			->method('checkOwnAnsweredSummaryId')
			->will($this->returnValueMap([
				['35', true],
			]));
		//テスト実行
		parent::testViewByCreatable($urlOptions, $assert, $exception, $return);
		if ($exception) {
			return;
		}
	}

/**
 * viewアクションのテスト用DataProvider
 *
 * ### 戻り値
 *  - urlOptions: URLオプション
 *  - assert: テストの期待値
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderViewByEditable() {
		//テストデータ
		// 編集者では見ることができなくなりました
		$results = array();
		$results[0] = array(
			'urlOptions' => Hash::insert($this->__data('83b294e176a8c8026d4fbdb07ad2ed7f'), 0, '33'),
			'assert' => null, 'exception' => 'BadRequestException'
		);

		return $results;
	}

/**
 * viewアクションのテスト(編集権限あり)
 *
 * @param array $urlOptions URLオプション
 * @param array $assert テストの期待値
 * @param string|null $exception Exception
 * @param string $return testActionの実行後の結果
 * @dataProvider dataProviderViewByEditable
 * @return void
 */
	public function testViewByEditable($urlOptions, $assert, $exception = null, $return = 'view') {
		//テスト実行
		$this->Quiz = ClassRegistry::init('Quizzes.Quiz');
		$this->Quiz->updateAll(
			array('passing_grade' => '5', 'estimated_time' => 1),
			array('Quiz.key' => '83b294e176a8c8026d4fbdb07ad2ed7f')
		);
		parent::testViewByEditable($urlOptions, $assert, $exception, $return);
		if ($exception) {
			return;
		}

		//チェック
		$this->__assertView($urlOptions['key'], true);
	}

/**
 * viewアクションのテスト用DataProvider
 *
 * ### 戻り値
 *  - urlOptions: URLオプション
 *  - assert: テストの期待値
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderViewByPublishable() {
		//テストデータ
		$results = array();
		$results[0] = array(
			'urlOptions' => Hash::insert($this->__data('83b294e176a8c8026d4fbdb07ad2ed7f'), 0, '33'),
			'assert' => array('method' => 'assertNotEmpty')
		);

		return $results;
	}
/**
 * viewアクションのテスト(公開権限あり)
 *
 * @param array $urlOptions URLオプション
 * @param array $assert テストの期待値
 * @param string|null $exception Exception
 * @param string $return testActionの実行後の結果
 * @dataProvider dataProviderViewByPublishable
 * @return void
 */
	public function testViewByPublishable($urlOptions, $assert, $exception = null, $return = 'view') {
		//ログイン
		TestAuthGeneral::login($this, Role::ROOM_ROLE_KEY_CHIEF_EDITOR);

		$this->Quiz = ClassRegistry::init('Quizzes.Quiz');
		$this->Quiz->updateAll(
			array('passing_grade' => '5', 'estimated_time' => 1),
			array('Quiz.key' => '83b294e176a8c8026d4fbdb07ad2ed7f')
		);

		//テスト実施
		$url = Hash::merge(array(
			'plugin' => $this->plugin,
			'controller' => $this->_controller,
			'action' => 'view',
		), $urlOptions);

		$this->_testGetAction($url, $assert, $exception, $return);

		//チェック
		$this->__assertView($urlOptions['key'], true);

		//ログアウト
		TestAuthGeneral::logout($this);
	}

/**
 * view()のassert
 *
 * @param string $contentKey コンテンツキー
 * @param bool $isLatest 最終コンテンツかどうか
 * @return void
 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
 */
	private function __assertView($contentKey, $isLatest = false) {
		$this->assertTextContains('テストパターン３４', $this->view);
		$this->assertTextContains(
			__d('quizzes', 'If you allow repeat examination , data is based on the last answer .'),
			$this->view);
		$this->assertTextContains(__d('quizzes', 'Overall performance'), $this->view);
		$this->assertTextContains(
			__d('quizzes', '%s \'s grade', 'Editor'),
			$this->view);
		$this->assertTextContains(__d('quizzes', 'Score history'), $this->view);
		$this->assertTextContains('<nvd3 options="opt" data="data"></nvd3>', $this->view);
		$this->assertTextContains(__d('quizzes', 'Answer history'), $this->view);
	}

}
