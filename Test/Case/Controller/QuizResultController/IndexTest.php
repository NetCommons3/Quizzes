<?php
/**
 * QuizResultController::index()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('WorkflowControllerIndexTest', 'Workflow.TestSuite');

/**
 * QuizResultController::index()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\Controller\QuizResultController
 */
class QuizResultControllerIndexTest extends WorkflowControllerIndexTest {

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
 * テストDataの取得
 *
 * @param string $key 小テストキー
 * @return array
 */
	private function __data($key) {
		$frameId = '6';
		$blockId = '2';

		$data = array(
			'action' => 'index',
			'frame_id' => $frameId,
			'block_id' => $blockId,
			'key' => $key
		);

		return $data;
	}

/**
 * indexアクションのテスト(ログインなし)用DataProvider
 *
 * ### 戻り値
 *  - urlOptions: URLオプション
 *  - assert: テストの期待値
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderIndex() {
		$data = $this->__data('83b294e176a8c8026d4fbdb07ad2ed7f');

		//テストデータ
		$results = array();
		// 未ログインの人は編集権限がないので一律[NetCommons.Permission]ではじかれる
		$results[0] = array(
			'urlOptions' => $data,
			'assert' => null,
			'exception' => 'ForbiddenException'
		);
		return $results;
	}

/**
 * indexアクションのテスト
 *
 * @param array $urlOptions URLオプション
 * @param array $assert テストの期待値
 * @param string|null $exception Exception
 * @param string $return testActionの実行後の結果
 * @dataProvider dataProviderIndex
 * @return void
 */
	public function testIndex($urlOptions, $assert, $exception = null, $return = 'view') {
		//テスト実行
		parent::testIndex($urlOptions, $assert, $exception, $return);
	}

/**
 * indexアクションのテスト(作成権限あり)用DataProvider
 *
 * ### 戻り値
 *  - urlOptions: URLオプション
 *  - assert: テストの期待値
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderIndexByCreatable() {
		// 公開されているけど自分が作ったんじゃないやつ
		$data = $this->__data('83b294e176a8c8026d4fbdb07ad2ed7f');
		// 自分が作ったもの（一時保存 回答なし
		$myData1 = $this->__data('e3eee47e033eccc3f42c02d75678235b');
		// 自分がつくったもの（公開中 回答期間は過ぎている 回答なし
		$myData2 = $this->__data('41e2b809108edead2f30adc37f51e979');

		$results = array();
		$results[0] = array(
			'urlOptions' => $data,
			'assert' => null,
			'exception' => 'BadRequestException'
		);
		$results[1] = array(
			'urlOptions' => $myData1,
			'assert' => array('method' => 'assertNotEmpty'),
		);
		$results[2] = array(
			'urlOptions' => $myData2,
			'assert' => array('method' => 'assertNotEmpty'),
		);
		return $results;
	}

/**
 * indexアクションのテスト(作成権限のみ)
 *
 * @param array $urlOptions URLオプション
 * @param array $assert テストの期待値
 * @param string|null $exception Exception
 * @param string $return testActionの実行後の結果
 * @dataProvider dataProviderIndexByCreatable
 * @return void
 */
	public function testIndexByCreatable($urlOptions, $assert, $exception = null, $return = 'view') {
		//テスト実行
		parent::testIndexByCreatable($urlOptions, $assert, $exception, $return);

		//チェック
		// ここの試験では対象の小テストは回答のない小テストだけなので
		// 「採点の終わった解答がまだないため表示できません。 」の確認
		$this->assertTextContains(__d('quizzes', 'Answer that ended the scoring is not yet.'), $this->view);
	}
/**
 * indexアクションのテスト(編集権限あり)用DataProvider
 *
 * ### 戻り値
 *  - urlOptions: URLオプション
 *  - assert: テストの期待値
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderIndexByEditable() {
		// 公開されている 回答あり
		$data = $this->__data('83b294e176a8c8026d4fbdb07ad2ed7f');

		$results = array();
		$results[0] = array(
			'urlOptions' => $data,
			'assert' => array('method' => 'assertNotEmpty'),
		);
		return $results;
	}

/**
 * indexアクションのテスト(編集権限あり)
 *
 * @param array $urlOptions URLオプション
 * @param array $assert テストの期待値
 * @param string|null $exception Exception
 * @param string $return testActionの実行後の結果
 * @dataProvider dataProviderIndexByEditable
 * @return void
 */
	public function testIndexByEditable($urlOptions, $assert, $exception = null, $return = 'view') {
		//テスト実行
		parent::testIndexByEditable($urlOptions, $assert, $exception, $return);

		//チェック
		$this->assertTextNotContains(
			__d('quizzes', 'Answer that ended the scoring is not yet.'), $this->view);
		$this->assertTextContains(
			'<nvd3 options="opt" data="data"></nvd3>', $this->view
		);
		// 下記回答リンクは同一人物の回答が複数あるときは最新の回答のサマリIDが使用されるので
		// 33となる（このひとの解答　31, 32, 33 とあるのです
		$this->assertTextContains(
			'/quizzes/quiz_result/view/2/83b294e176a8c8026d4fbdb07ad2ed7f/33', $this->view
		);
	}

/**
 * testIndexByEditableWithPassLine
 *
 * 合格判定付きの結果画面の場合
 * @return void
 */
	public function testIndexByEditableWithPassLine() {
		// 公開されている 回答あり
		$urlOptions = $this->__data('83b294e176a8c8026d4fbdb07ad2ed7f');
		$assert = array('method' => 'assertNotEmpty');
		// テストを合格判定付きにしておく
		// 合格ラインつきにしておく
		$Quiz = ClassRegistry::init('Quizzes.Quiz');
		$Quiz->updateAll(
			['Quiz.passing_grade' => 5, 'Quiz.estimated_time' => 1],
			['Quiz.key' => '83b294e176a8c8026d4fbdb07ad2ed7f']
		);
		//テスト実行
		parent::testIndexByEditable($urlOptions, $assert);

		//チェック
		$this->assertRegExp(
			'/<a href=".*?\/quizzes\/quiz_result\/index\/2\/83b294e176a8c8026d4fbdb07ad2ed7f' .
			'\/passing_status:2\?frame_id=6">/',
			$this->view);
		$this->assertRegExp(
			'/<a href=".*?\/quizzes\/quiz_result\/index\/2\/83b294e176a8c8026d4fbdb07ad2ed7f' .
			'\/within_time_status:2\?frame_id=6">/',
			$this->view);
	}
}
