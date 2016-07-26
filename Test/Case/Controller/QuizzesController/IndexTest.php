<?php
/**
 * QuizzesController::index()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('WorkflowControllerIndexTest', 'Workflow.TestSuite');

/**
 * QuizzesController::index()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\Controller\QuizzesController
 */
class QuizzesControllerIndexTest extends WorkflowControllerIndexTest {

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
	protected $_controller = 'quizzes';

/**
 * テストDataの取得
 *
 * @return array
 */
	private function __data() {
		$frameId = '6';
		$blockId = '2';

		$data = array(
			'action' => 'index',
			'frame_id' => $frameId,
			'block_id' => $blockId,
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
		$data = $this->__data();

		//テストデータ
		$results = array();
		$results[0] = array(
			'urlOptions' => $data,
			'assert' => array('method' => 'assertNotEmpty'),
		);

		//ソート、表示件数指定なし
		$results[] = array(
			'urlOptions' => array('frame_id' => $data['frame_id']),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		// ログイン無しでテストモードは０になる　　画面には"no quiz"のテキストが現れていること
		$results[] = array(
			'urlOptions' => array('frame_id' => $data['frame_id'], 'answer_status' => 'test'),
			'assert' => array('method' => 'assertContains', 'expected' => __d('quizzes', 'no quiz')),
		);
		// ログイン無しで未受験を絞り込んだら出てくる　画面には何らかの表示が現れていること
		$results[] = array(
			'urlOptions' => array('frame_id' => $data['frame_id'], 'answer_status' => 'unanswered'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		// ログイン無しで受験済絞込は０になる　画面には"no quiz"のテキストが現れていること
		$results[] = array(
			'urlOptions' => array('frame_id' => $data['frame_id'], 'answer_status' => 'answered'),
			'assert' => array('method' => 'assertContains', 'expected' => __d('quizzes', 'no quiz')),
		);
		// 表示件数を増やすことでFixtureデータをすべて表示させる
		// そのうえで試験を行う
		// 未ログインでも公開中なら見える
		$results[] = array(
			'urlOptions' => array(
				'frame_id' => $data['frame_id'],
				'block_id' => $data['block_id'], 'action' => 'index', 'limit' => 50),
			'assert' => array(
				'method' => 'assertActionLink',
				'action' => 'view',
				'linkExist' => true,
				'url' => array(
					'controller' => 'quiz_answers',
					'key' => '5fdb4f0049f3bddeabc49cd2b72c6ac9', 'limit' => null)),
		);
		// 未ログインの場合は一時保存が見えない
		$results[] = array(
			'urlOptions' => array(
				'frame_id' => $data['frame_id'],
				'block_id' => $data['block_id'], 'action' => 'index', 'limit' => 50),
			'assert' => array(
				'method' => 'assertActionLink',
				'action' => 'view',
				'linkExist' => false,
				'url' => array(
					'controller' => 'quiz_answers',
					'key' => 'e3eee47e033eccc3f42c02d75678235b', 'limit' => null)),
		);
		// 未ログインの場合は未来公開が見えない
		$results[] = array(
			'urlOptions' => array(
				'frame_id' => $data['frame_id'],
				'block_id' => $data['block_id'], 'action' => 'index', 'limit' => 50),
			'assert' => array(
				'method' => 'assertActionLink',
				'action' => 'view',
				'linkExist' => false,
				'url' => array(
					'controller' => 'quiz_answers',
					'key' => '468e3c55607b0c1d5cf55ddad51f836a', 'limit' => null)),
		);
		// 未ログインの場合でも過去公開は見える
		$results[] = array(
			'urlOptions' => array(
				'frame_id' => $data['frame_id'],
				'block_id' => $data['block_id'], 'action' => 'index', 'limit' => 50),
			'assert' => array(
				'method' => 'assertActionLink',
				'action' => 'view',
				'linkExist' => true,
				'url' => array(
					'controller' => 'quiz_answers',
					'key' => '13001359fc46bd17c03451906eee7e4e', 'limit' => null)),
		);
		//チェック
		//--追加ボタンチェック(なし)
		$results[] = array(
			'urlOptions' => array(
				'frame_id' => $data['frame_id'], 'block_id' => $data['block_id']),
			'assert' => array(
				'method' => 'assertActionLink', 'action' => 'add', 'linkExist' => false,
				'url' => array('controller' => 'quizzes')),
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
		$data = $this->__data();
		$base = 0;
		$results[0] = array(
			'urlOptions' => array('frame_id' => $data['frame_id'], 'block_id' => $data['block_id']),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		//チェック
		//--追加ボタンチェック
		array_push($results, Hash::merge($results[$base], array(
			'urlOptions' => array('frame_id' => null, 'controller' => 'quizzes'),
			'assert' => array('method' => 'assertActionLink', 'action' => 'add', 'linkExist' => true, 'url' => array()),
		)));
		//フレームID指定なしテスト
		array_push($results, Hash::merge($results[$base], array(
			'urlOptions' => array('frame_id' => null, 'block_id' => $data['block_id']),
			'assert' => array('method' => 'assertNotEmpty'),
		)));
		// 公開中なら見える ※かつ会員のみのもの
		array_push($results, Hash::merge($results[$base], array(
			'urlOptions' => array('action' => 'index', 'limit' => 50),
			'assert' => array(
				'method' => 'assertActionLink', 'action' => 'view', 'linkExist' => true,
				'url' => array('controller' => 'quiz_answers',
					'key' => '52fc8a15a76b4f315db20e319de5c6d0', 'limit' => null))
		)));
		// 自分のなら承認待ちも見える
		array_push($results, Hash::merge($results[$base], array(
			'urlOptions' => array('action' => 'index', 'limit' => 50),
			'assert' => array(
				'method' => 'assertActionLink', 'action' => 'test_mode', 'linkExist' => true,
				'url' => array('controller' => 'quiz_answers',
					'key' => '4f02540a2a10aeffbcc079e73961d4ad', 'limit' => null))
		)));
		// 自分のなら差し戻しも見える #5
		array_push($results, Hash::merge($results[$base], array(
			'urlOptions' => array('action' => 'index', 'limit' => 50),
			'assert' => array(
				'method' => 'assertActionLink', 'action' => 'test_mode', 'linkExist' => true,
				'url' => array('controller' => 'quiz_answers',
					'key' => '5f687070b3dbecb005cf000d95048a44', 'limit' => null))
		)));
		// 自分のなら一時保存も見える
		array_push($results, Hash::merge($results[$base], array(
			'urlOptions' => array('action' => 'index', 'limit' => 50),
			'assert' => array(
				'method' => 'assertActionLink', 'action' => 'test_mode', 'linkExist' => true,
				'url' => array('controller' => 'quiz_answers',
					'key' => 'e3eee47e033eccc3f42c02d75678235b', 'limit' => null))
		)));
		// 自分のなら未来も見える
		array_push($results, Hash::merge($results[$base], array(
			'urlOptions' => array('action' => 'index', 'limit' => 50),
			'assert' => array(
				'method' => 'assertActionLink', 'action' => 'view', 'linkExist' => true,
				'url' => array('controller' => 'quiz_answers',
					'key' => '39e6aa5d38fff3230276e0abf408c9a6', 'limit' => null))
		)));
		// 他人の承認待ちも見えない
		array_push($results, Hash::merge($results[$base], array(
			'urlOptions' => array('action' => 'index', 'limit' => 50),
			'assert' => array(
				'method' => 'assertActionLink', 'action' => 'test_mode', 'linkExist' => false,
				'url' => array('controller' => 'quiz_answers',
					'key' => '9e003ee9cd538f7a4cf9d73b0b3470c4', 'limit' => null))
		)));
		// 他人の差し戻しも見えない #10
		array_push($results, Hash::merge($results[$base], array(
			'urlOptions' => array('action' => 'index', 'limit' => 50),
			'assert' => array(
				'method' => 'assertActionLink', 'action' => 'test_mode', 'linkExist' => false,
				'url' => array('controller' => 'quiz_answers',
					'key' => 'ac3198521c927f4c25f7a14e64e286ea', 'limit' => null))
		)));
		// 他人の一時保存も見えない
		array_push($results, Hash::merge($results[$base], array(
			'urlOptions' => array('action' => 'index', 'limit' => 50),
			'assert' => array(
				'method' => 'assertActionLink', 'action' => 'test_mode', 'linkExist' => false,
				'url' => array('controller' => 'quiz_answers',
					'key' => '257b711744f8fb6ba8313a688a9de52f', 'limit' => null))
		)));
		// 他人の未来 見えない
		array_push($results, Hash::merge($results[$base], array(
			'urlOptions' => array('action' => 'index', 'limit' => 50),
			'assert' => array(
				'method' => 'assertActionLink', 'action' => 'view', 'linkExist' => false,
				'url' => array('controller' => 'quiz_answers',
					'key' => '468e3c55607b0c1d5cf55ddad51f836a', 'limit' => null))
		)));

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
		$data = $this->__data();
		$results = array();

		//編集権限あり
		$base = 0;
		$results[0] = array(
			'urlOptions' => array('frame_id' => $data['frame_id'], 'block_id' => $data['block_id']),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		//チェック
		//--追加ボタンチェック
		array_push($results, Hash::merge($results[$base], array(
			'urlOptions' => array('controller' => 'quizzes'),
			'assert' => array(
				'method' => 'assertActionLink', 'action' => 'add', 'linkExist' => true,
				'url' => array('controller' => 'quizzes')),
		)));
		//フレームあり(コンテンツなし)テスト
		array_push($results, Hash::merge($results[$base], array(
			'urlOptions' => array('frame_id' => '14', 'block_id' => null),
			'assert' => array('method' => 'assertContains', 'expected' => __d('quizzes', 'no quiz'))
		)));
		//記事なしテスト
		array_push($results, Hash::merge($results[$base], array(
			'urlOptions' => array('frame_id' => null, 'block_id' => '6'),
			'assert' => array('method' => 'assertContains', 'expected' => __d('quizzes', 'no quiz'))
		)));
		//フレームID指定なしテスト
		array_push($results, Hash::merge($results[$base], array(
			'urlOptions' => array('frame_id' => null, 'block_id' => $data['block_id']),
		)));
		// 他人の承認待ちも見える
		array_push($results, Hash::merge($results[$base], array(
			'urlOptions' => array('action' => 'index', 'limit' => 50),
			'assert' => array(
				'method' => 'assertActionLink', 'action' => 'test_mode', 'linkExist' => true,
				'url' => array(
					'controller' => 'quiz_answers',
					'key' => '9e003ee9cd538f7a4cf9d73b0b3470c4', 'limit' => null))
		)));
		// 他人の差し戻しも
		array_push($results, Hash::merge($results[$base], array(
			'urlOptions' => array('action' => 'index', 'limit' => 50),
			'assert' => array(
				'method' => 'assertActionLink', 'action' => 'test_mode', 'linkExist' => true,
				'url' => array(
					'controller' => 'quiz_answers',
					'key' => 'ac3198521c927f4c25f7a14e64e286ea', 'limit' => null))
		)));
		// 他人の一時保存も
		array_push($results, Hash::merge($results[$base], array(
			'urlOptions' => array('action' => 'index', 'limit' => 50),
			'assert' => array(
				'method' => 'assertActionLink', 'action' => 'test_mode', 'linkExist' => true,
				'url' => array(
					'controller' => 'quiz_answers',
					'key' => '257b711744f8fb6ba8313a688a9de52f', 'limit' => null))
		)));
		// 他人の未来 見え
		array_push($results, Hash::merge($results[$base], array(
			'urlOptions' => array('action' => 'index', 'limit' => 50),
			'assert' => array(
				'method' => 'assertActionLink', 'action' => 'view', 'linkExist' => true,
				'url' => array(
					'controller' => 'quiz_answers',
					'key' => '468e3c55607b0c1d5cf55ddad51f836a', 'limit' => null))
		)));

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
	}

}
