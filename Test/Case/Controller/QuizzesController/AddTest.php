<?php
/**
 * QuizzesController::add()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('WorkflowControllerAddTest', 'Workflow.TestSuite');

/**
 * QuizzesController::add()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\Controller\QuizzesController
 */
class QuizzesControllerAddTest extends WorkflowControllerAddTest {

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
 * Controller name
 *
 * @var string
 */
	protected $_controller = 'quizzes';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Quiz = ClassRegistry::init('Quizzes.Quiz');
		$this->Quiz->Behaviors->unload('AuthorizationKey');
		$this->ActionQuizAdd = ClassRegistry::init('Quizzes.ActionQuizAdd');
		$this->Plugin = ClassRegistry::init('PluginManager.Plugin');
		$db = $this->Plugin->getDataSource();
		$value = 'quiz_blocks/index';
		$value = $db->value($value, 'string');
		$this->Plugin->updateAll(
			array('Plugin.default_setting_action' => $value),
			array('Plugin.key' => 'quizzes')
		);
	}

/**
 * テストDataの取得
 *
 * @return array
 */
	private function __data() {
		$frameId = '6';
		$blockId = '2';
		$blockKey = 'block_1';

		$data = array(
			'save_' . WorkflowComponent::STATUS_IN_DRAFT => null,
			'Frame' => array(
				'id' => $frameId,
			),
			'Block' => array(
				'id' => $blockId,
				'key' => $blockKey,
				'language_id' => '2',
				'room_id' => '1',
				'plugin_key' => $this->plugin,
			),
			'ActionQuizAdd' => array(
				'create_option' => 'create',
				'title' => 'New Quiz Title',
			),
		);

		return $data;
	}
/**
 * テストDataの取得
 *
 * @return array
 */
	private function __getDataPastReuse() {
		$frameId = '6';
		$blockId = '2';
		$blockKey = 'block_1';

		$data = array(
			//'save_' . WorkflowComponent::STATUS_IN_DRAFT => null,
			'Frame' => array(
				'id' => $frameId
			),
			'Block' => array(
				'id' => $blockId,
				'key' => $blockKey,
				'language_id' => '2',
				'room_id' => '1',
				'plugin_key' => $this->plugin,
			),
			'ActionQuizAdd' => array(
				'create_option' => 'reuse',
				'past_quiz_id' => '15',
			),
		);

		return $data;
	}
/**
 * addアクションのGETテスト(ログインなし)用DataProvider
 *
 * ### 戻り値
 *  - urlOptions: URLオプション
 *  - assert: テストの期待値
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderAddGet() {
		$data = $this->__data();

		//テストデータ
		$results = array();
		// * ログインなし
		$results[0] = array(
			'urlOptions' => array(
				'frame_id' => $data['Frame']['id'],
				'block_id' => $data['Block']['id']
			),
			'assert' => null, 'exception' => 'ForbiddenException',
		);

		return $results;
	}

/**
 * addアクションのGETテスト(作成権限あり)用DataProvider
 *
 * ### 戻り値
 *  - urlOptions: URLオプション
 *  - assert: テストの期待値
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderAddGetByCreatable() {
		$data = $this->__data();

		//テストデータ
		$results = array();

		// 正しいフレームIDとブロックID
		$results[0] = array(
			'urlOptions' => array(
				'frame_id' => $data['Frame']['id'],
				'block_id' => $data['Block']['id'],
			),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		// セッティングモードから来た振りをしてもCreatableなだけだと設定が効かないことを確認
		array_push($results, Hash::merge($results[0], array(
			'urlOptions' => array('q_mode' => 'setting'),
			'assert' => array(
				'method' => 'assertActionLink', 'action' => 'index', 'linkExist' => false,
				'url' => array('controller' => 'quiz_blocks')),
		)));
		// フレームIDのhidden-inputがあるか
		array_push($results, Hash::merge($results[0], array('assert' => array(
			'method' => 'assertInput', 'type' => 'input',
			'name' => 'data[Frame][id]', 'value' => $data['Frame']['id']),
		)));
		// ブロックIDのhidden-inputがあるか
		array_push($results, Hash::merge($results[0], array('assert' => array(
			'method' => 'assertInput', 'type' => 'input',
			'name' => 'data[Block][id]', 'value' => $data['Block']['id']),
		)));
		// 作成方法選択肢オプションがあるか
		array_push($results, Hash::merge($results[0], array('assert' => array(
			'method' => 'assertInput', 'type' => 'input',
			'name' => 'data[ActionQuizAdd][create_option]', 'value' => null),
		)));
		// タイトル入力テキストがあるか
		array_push($results, Hash::merge($results[0], array('assert' => array(
			'method' => 'assertInput', 'type' => 'text',
			'name' => 'data[ActionQuizAdd][title]', 'value' => null),
		)));
		// 過去再利用の絞込テキスト入力とhiddenがあることを確認する
		// 本当は過去のアンケート一覧が表示されることも確認せねばならないが、
		//それはAngularで展開しているのでphpunitでは確認できないため省略
		array_push($results, Hash::merge($results[0], array('assert' => array(
			'method' => 'assertInput', 'type' => 'text',
			'name' => 'data[ActionQuizAdd][past_search]', 'value' => null),
		)));
		array_push($results, Hash::merge($results[0], array('assert' => array(
			'method' => 'assertInput', 'type' => 'input',
			'name' => 'data[ActionQuizAdd][past_quiz_id]', 'value' => null),
		)));
		// テンプレートファイル読み込みがあるか
		array_push($results, Hash::merge($results[0], array('assert' => array(
			'method' => 'assertInput', 'type' => 'input',
			'name' => 'data[ActionQuizAdd][template_file]', 'value' => null),
		)));

		// * フレームID指定なしテスト
		array_push($results, Hash::merge($results[0], array(
			'urlOptions' => array('frame_id' => null, 'block_id' => $data['Block']['id']),
			'assert' => array('method' => 'assertNotEmpty'),
		)));
		array_push($results, Hash::merge($results[0], array(
			'urlOptions' => array('frame_id' => null, 'block_id' => $data['Block']['id']),
			'assert' => array('method' => 'assertInput', 'type' => 'input',
				'name' => 'data[Frame][id]', 'value' => null),
		)));

		return $results;
	}
/**
 * addアクションのGETテスト(管理者)用DataProvider
 *
 * ### 戻り値
 *  - urlOptions: URLオプション
 *  - assert: テストの期待値
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderAddGetByAdmin() {
		$data = $this->__data();

		//テストデータ
		$results = array();
		//テストデータ
		$results = array();

		// 正しいフレームIDとブロックID
		$results[0] = array(
			'urlOptions' => array(
				'frame_id' => $data['Frame']['id'],
				'block_id' => $data['Block']['id'],
			),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		// セッティングモードから来た振りをしてもCreatableなだけだと設定が効かないことを確認
		array_push($results, Hash::merge($results[0], array(
			'urlOptions' => array('q_mode' => 'setting'),
			'assert' => array(
				'method' => 'assertActionLink', 'action' => 'index', 'linkExist' => true,
				'url' => array('controller' => 'quiz_blocks', 'q_mode' => null)),
		)));
		return $results;
	}

/**
 * addアクションのGETテスト(ブロック編集権限)
 *
 * @param array $urlOptions URLオプション
 * @param array $assert テストの期待値
 * @param string|null $exception Exception
 * @param string $return testActionの実行後の結果
 * @dataProvider dataProviderAddGetByAdmin
 * @return void
 */
	public function testAddGetByAdmin($urlOptions, $assert, $exception = null, $return = 'view') {
		//ログイン
		TestAuthGeneral::login($this, Role::ROOM_ROLE_KEY_ROOM_ADMINISTRATOR);

		//テスト実施
		$url = Hash::merge(array(
			'plugin' => $this->plugin,
			'controller' => $this->_controller,
			'action' => 'add',
		), $urlOptions);

		$this->_testGetAction($url, $assert, $exception, $return);

		//ログアウト
		TestAuthGeneral::logout($this);
	}
/**
 * addアクションのPOSTテスト用DataProvider
 *
 * ### 戻り値
 *  - data: 登録データ
 *  - role: ロール
 *  - urlOptions: URLオプション
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderAddPost() {
		$data = $this->__data();

		//テストデータ
		$results = array();
		// * ログインなし
		$results[0] = array(
			'data' => $data, 'role' => null,
			'urlOptions' => array(
				'frame_id' => $data['Frame']['id'],
				'block_id' => $data['Block']['id']
			),
			'exception' => 'ForbiddenException'
		);
		// * 作成権限あり
		$results[] = array(
			'data' => $data, 'role' => Role::ROOM_ROLE_KEY_GENERAL_USER,
			'urlOptions' => array(
				'frame_id' => $data['Frame']['id'],
				'block_id' => $data['Block']['id']
			),
		);
		$results[] = array(
			'data' => $this->__getDataPastReuse(), 'role' => Role::ROOM_ROLE_KEY_GENERAL_USER,
			'urlOptions' => array(
				'frame_id' => $data['Frame']['id'],
				'block_id' => $data['Block']['id']),
		);
		// * フレームID指定なしテスト
		$results[] = array(
			'data' => $data, 'role' => Role::ROOM_ROLE_KEY_ROOM_ADMINISTRATOR,
			'urlOptions' => array(
				'frame_id' => null,
				'block_id' => $data['Block']['id']),
		);
		$results[] = array(
			'data' => $data, 'role' => Role::ROOM_ROLE_KEY_ROOM_ADMINISTRATOR,
			'urlOptions' => array(
				'frame_id' => $data['Frame']['id'],
				'block_id' => $data['Block']['id'],
				'q_mode' => 'setting'),
		);

		return $results;
	}

/**
 * addアクションのValidationErrorテスト用DataProvider
 *
 * ### 戻り値
 *  - data: 登録データ
 *  - urlOptions: URLオプション
 *  - validationError: バリデーションエラー
 *
 * @return array
 */
	public function dataProviderAddValidationError() {
		$data = $this->__data();
		$dataPastReuse = $this->__getDataPastReuse();

		$result = array(
			'data' => $data,
			'urlOptions' => array(
				'frame_id' => $data['Frame']['id'],
				'block_id' => $data['Block']['id']
			),
			'validationError' => array(),
		);
		$resultPastReuse = array(
			'data' => $dataPastReuse,
			'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id']),
		);

		//テストデータ
		//$dataTemplate = $this->__getData();
		//$dataTemplate['ActionQuestionnaireAdd']['create_option'] = 'template';
		//$resultTemplate = array(
		//	'data' => $dataTemplate,
		//	'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id']),
		//);

		return array(
			Hash::merge($result, array(
				'validationError' => array(
					'field' => 'ActionQuizAdd.create_option',
					'value' => null,
					'message' => sprintf(__d('quizzes', 'Please choose create option.'))
				)
			)),
			Hash::merge($result, array(
				'validationError' => array(
					'field' => 'ActionQuizAdd.title',
					'value' => '',
					'message' => sprintf(__d('net_commons', 'Please input %s.'), __d('quizzes', 'Title'))
				)
			)),
			Hash::merge($resultPastReuse, array(
				'validationError' => array(
					'field' => 'ActionQuizAdd.past_quiz_id',
					'value' => '',
					'message' => sprintf(__d('quizzes', 'Please select past quiz.'))
				)
			)),
			Hash::merge($resultPastReuse, array(
				'validationError' => array(
					'field' => 'ActionQuizAdd.past_quiz_id',
					'value' => '9999999',
					'message' => sprintf(__d('quizzes', 'Please select past quiz.'))
				)
			)),
			//Hash::merge($resultTemplate, array(
			//	'validationError' => array(
			//		'field' => 'ActionQuestionnaireAdd.template_file',
			//		'value' => null,
			//		'message' => sprintf(__d('questionnaires', 'file upload error.'))
			//	)
			//)),
		);
	}

}
