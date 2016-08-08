<?php
/**
 * QuizAnswersController::start()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('WorkflowControllerViewTest', 'Workflow.TestSuite');

/**
 * QuizAnswersController::start()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\Controller\QuizAnswersController
 */
class QuizAnswersControllerStartTest extends WorkflowControllerViewTest {

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
 * テストDataの取得
 *
 * @return array
 */
	private function __data() {
		$frameId = '6';
		$blockId = '2';
		$contentKey = 'content_key_1';

		$data = array(
			'action' => 'start',
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
		$data = $this->__data();

		//テストデータ
		// 未ログイン
		//   会員の専用のもの、
		//   非会員もOKのもの、
		//   回答済みのもの（繰り返し不可 --> これはログイン環境で確認
		//   回答済みのもの（繰り返しOK --> これはログイン環境で確認
		//   画像認証付き
		//   認証キーつき
		//   未来
		//   過去
		//   現在
		$results = array();
		// 非会員もOK 現在
		$results[0] = array(
			'urlOptions' => Hash::insert($data, 'key', '5fdb4f0049f3bddeabc49cd2b72c6ac9'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		// 非会員もOK 未来
		$results[1] = array(
			'urlOptions' => Hash::insert($data, 'key', '468e3c55607b0c1d5cf55ddad51f836a'),
			'assert' => array('method' => 'assertTextContains',
			'expected' => __d('quizzes', 'you will not be able to answer this quiz.'))
		);
		// 非会員もOK 過去
		$results[2] = array(
			'urlOptions' => Hash::insert($data, 'key', '13001359fc46bd17c03451906eee7e4e'),
			'assert' => array('method' => 'assertTextContains',
			'expected' => __d('quizzes', 'you will not be able to answer this quiz.'))
		);
		// 会員専用
		$results[3] = array(
			'urlOptions' => Hash::insert($data, 'key', '52fc8a15a76b4f315db20e319de5c6d0'),
			'assert' => array('method' => 'assertTextContains',
			'expected' => __d('quizzes', 'you will not be able to answer this quiz.'))
		);
		// 画像認証
		$results[4] = array(
			'urlOptions' => Hash::insert($data, 'key', 'a2cf0e48f281be7c3cc35f0920f047ca'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		// 認証キー
		$results[5] = array(
			'urlOptions' => Hash::insert($data, 'key', 'a916437af184b4a185f685a93099adca'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		// 存在しない
		$results[6] = array(
			'urlOptions' => Hash::insert($data, 'key', 'content_key_999'),
			'assert' => array('method' => 'assertTextContains',
				'expected' => __d('quizzes', 'not found this quiz.'))
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
		//テスト実行
		parent::testView($urlOptions, $assert, $exception, $return);
		if ($exception) {
			return;
		}

		//チェック
		if ($urlOptions['key'] === '5fdb4f0049f3bddeabc49cd2b72c6ac9' ||
			$urlOptions['key'] === 'a2cf0e48f281be7c3cc35f0920f047ca' ||
			$urlOptions['key'] === 'a916437af184b4a185f685a93099adca') {
			$this->__assertView($urlOptions['key'], false);
		} else {
			$this->__assertView($urlOptions['key'], true);
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
		$data = $this->__data();

		//テストデータ
		// 一般
		//   会員の専用のもの、
		//   非会員もOKのもの、
		//   回答済みのもの（繰り返し不可
		//   回答済みのもの（繰り返しOK
		//   自分の一時保存
		//   他人の一時保存
		//   自分の承認待ち
		//   自分の差し戻し
		$results = array();
		// 会員専用のもの
		$results[0] = array(
			'urlOptions' => Hash::insert($data, 'key', '52fc8a15a76b4f315db20e319de5c6d0'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		// 非会員もOK
		$results[1] = array(
			'urlOptions' => Hash::insert($data, 'key', '5fdb4f0049f3bddeabc49cd2b72c6ac9'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		// 回答済みのもの（繰り返し不可
		$results[2] = array(
			'urlOptions' => Hash::insert($data, 'key', 'c389a74ef01516f9b3e477afcf3dfa02'),
			'assert' => array('method' => 'assertTextContains',
			'expected' => __d('quizzes', 'you will not be able to answer this quiz.'))
		);
		// 回答済みのもの（繰り返しOK
		$results[3] = array(
			'urlOptions' => Hash::insert($data, 'key', '83b294e176a8c8026d4fbdb07ad2ed7f'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		//   自分の一時保存
		$results[4] = array(
			'urlOptions' => Hash::insert($data, 'key', 'e3eee47e033eccc3f42c02d75678235b'),
			'assert' => array('method' => 'assertTextContains',
			'expected' => __d('quizzes',
				'This quiz is being temporarily stored . ' .
				'You can quiz test before performed in this page . ' .
				'If you want to modify or change the quiz , ' .
				'you will be able to edit by pressing the [ Edit question ] ' .
				'button in the upper-right corner .'))
		);
		//   他人の一時保存
		$results[5] = array(
			'urlOptions' => Hash::insert($data, 'key', 'e9329d3567b76c1b880e1a80a74c12f5'),
			'assert' => array('method' => 'assertTextContains',
				'expected' => __d('quizzes', 'not found this quiz.'))
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
		// 解答は全部一般が答えたことにする
		$this->QuizAnswerSummary = ClassRegistry::init('Quizzes.QuizAnswerSummary');
		$this->QuizAnswerSummary->updateAll(['user_id' => 4]);

		//テスト実行
		parent::testViewByCreatable($urlOptions, $assert, $exception, $return);
		if ($exception) {
			return;
		}

		//チェック
		if ($urlOptions['key'] === 'c389a74ef01516f9b3e477afcf3dfa02' ||
		$urlOptions['key'] == 'e9329d3567b76c1b880e1a80a74c12f5') {
			$this->__assertView($urlOptions['key'], true);

		} else {
			$this->__assertView($urlOptions['key'], false);
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
		$data = $this->__data();

		$tstmsg = 'This quiz is being temporarily stored . ' .
			'You can quiz test before performed in this page . ' .
			'If you want to modify or change the quiz , ' .
			'you will be able to edit by pressing the [ Edit question ] ' .
			'button in the upper-right corner .';
		//テストデータ
		$results = array();
		// 自分の一時保存
		// 一般が書いた差し戻し
		// 自分より上の人が書いた一時保存

		// 自分の一時保存
		$results[0] = array(
			'urlOptions' => Hash::insert($data, 'key', '257b711744f8fb6ba8313a688a9de52f'),
			'assert' => array('method' => 'assertTextContains', 'expected' => __d('quizzes', $tstmsg))
		);
		// 一般が書いた差し戻し
		$results[1] = array(
			'urlOptions' => Hash::insert($data, 'key', '5f687070b3dbecb005cf000d95048a44'),
			'assert' => array('method' => 'assertTextContains', 'expected' => __d('quizzes', $tstmsg))
		);
		// 自分より上の人が書いた一時保存
		$results[2] = array(
			'urlOptions' => Hash::insert($data, 'key', 'e9329d3567b76c1b880e1a80a74c12f5'),
			'assert' => array('method' => 'assertTextContains', 'expected' => __d('quizzes', $tstmsg))
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
		parent::testViewByEditable($urlOptions, $assert, $exception, $return);
		if ($exception) {
			return;
		}

		//チェック
		$this->__assertView($urlOptions['key'], false);
	}

/**
 * view()のassert
 *
 * @param string $contentKey コンテンツキー
 * @param bool $isNoMoreAnswer 解答不可能か
 * @return void
 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
 */
	private function __assertView($contentKey, $isNoMoreAnswer = false) {
		// 解答不可
		if ($isNoMoreAnswer) {
			$this->assertTextContains(__d('quizzes', 'Back to Top'), $this->view);
		} else {
			// 認証キーのinputが出ているか
			if ($contentKey === 'a916437af184b4a185f685a93099adca') {
				$this->assertInput('input', 'data[AuthorizationKey][authorization_key]', null, $this->view);
			}
			// 画像認証
			if ($contentKey === 'a2cf0e48f281be7c3cc35f0920f047ca') {
				$this->assertTextContains('visualCaptcha', $this->view);
			}
			// 必ずある
			$this->assertTextContains(__d('quizzes', 'Start the quiz'), $this->view);
		}
	}

}
