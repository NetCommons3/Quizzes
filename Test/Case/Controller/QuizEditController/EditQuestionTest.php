<?php
/**
 * QuizEditController::edit_question()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('WorkflowControllerEditTest', 'Workflow.TestSuite');

/**
 * QuizEditController::edit_question()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\Controller\QuizEditController
 */
class QuizEditControllerEditQuestionTest extends WorkflowControllerEditTest {

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
	protected $_controller = 'quiz_edit';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Quiz = ClassRegistry::init('Quizzes.Quiz');
		$this->Quiz->Behaviors->unload('AuthorizationKey');

		$this->controller->Session->expects($this->any())
			->method('check')
			->will($this->returnValueMap([
				['Quizzes.quizEdit.' . 'testSession', true],
				['Quizzes.quizEdit.' . 'testGeneralSession', true]
			]));
		$this->controller->Session->expects($this->any())
			->method('read')
			->will($this->returnValueMap([
				['Quizzes.quizEdit.' . 'testSession', $this->__data()],
				['Quizzes.quizEdit.' . 'testGeneralSession', $this->__data(Role::ROOM_ROLE_KEY_GENERAL_USER)]
			]));

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
 * @param string $role ロール
 * @param string $key 小テストキー
 * @return array
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 */
	private function __data($role = null, $key = null) {
		$frameId = '6';
		$blockId = '2';
		$blockKey = 'block_1';
		if ($role === Role::ROOM_ROLE_KEY_GENERAL_USER) {
			if (! $key) {
				// 一般作成未公開
				$contentKey = 'e3eee47e033eccc3f42c02d75678235b';
				$contentId = '38';
			} else {
				// 一般作成未公開
				$contentKey = $key;
				$contentId = '';
			}
			$userId = 4;
		} else {
			// 編集者がかいた一時保存
			$contentKey = '257b711744f8fb6ba8313a688a9de52f';
			$contentId = '37';
			$userId = 3;
		}
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
			'Quiz' => array(
				'id' => $contentId,
				'key' => $contentKey,
				'language_id' => '2',
				'status' => null,
				'title' => 'EditTestTitle',
				'sub_title' => 'EditTestSubTitle',
				'passing_grade' => '0',
				'estimated_time' => '0',
				'answer_timing' => '0',
				'is_no_member_allow' => 1,
				'is_key_pass_use' => 0,
				'is_image_authentication' => 0,
				'is_repeat_allow' => 0,
				'is_repeat_until_passing' => 0,
				'is_page_random' => 0,
				'perfect_score' => '0',
				'is_correct_show' => 0,
				'is_total_show' => 0,
				'created_user' => $userId,
			),
			'QuizPage' => array(
				array(
					'page_title' => null,
					'page_sequence' => '0',
					'is_page_description' => 1,
					'page_description' => 'ページ先頭の問題文章',
					'QuizQuestion' => array(
						array(
							'question_sequence' => '0',
							'question_value' => '<p>新規問題1</p>',
							'question_type' => '1',
							'is_choice_random' => 0,
							'is_choice_horizon' => 0,
							'is_order_fixed' => 0,
							'allotment' => '10',
							'commentary' => '解説文章ですよ',
							'QuizChoice' => array(
								array(
									'choice_sequence' => '0',
									'choice_label' => '新規選択肢1',
								),
								array(
									'choice_sequence' => '1',
									'choice_label' => '新規選択肢2',
								),
								array(
									'choice_sequence' => '2',
									'choice_label' => '新規選択肢3',
								),
							),
							'QuizCorrect' => array(
								array(
									'correct_sequence' => '0',
									'correct' => '新規選択肢1',
								)
							),
						)
					)
				)
			),
			'WorkflowComment' => array(
				'comment' => 'WorkflowComment save test',
			),
		);
		return $data;
	}

/**
 * editアクションのGETテスト(ログインなし)用DataProvider
 *
 * ### 戻り値
 *  - urlOptions: URLオプション
 *  - assert: テストの期待値
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderEditGet() {
		$data = $this->__data();

		//テストデータ
		$results = array();
		// * ログインなし
		$results[0] = array(
			'urlOptions' => array(
				'frame_id' => $data['Frame']['id'],
				'block_id' => $data['Block']['id'],
				'key' => '257b711744f8fb6ba8313a688a9de52f',
				'action' => 'edit_question',
			),
			'assert' => null, 'exception' => 'ForbiddenException'
		);

		return $results;
	}

/**
 * editアクションのGETテスト(作成権限のみ)用DataProvider
 *
 * ### 戻り値
 *  - urlOptions: URLオプション
 *  - assert: テストの期待値
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderEditGetByCreatable() {
		$data = $this->__data();

		//テストデータ
		// * 作成権限のみ
		$results = array();
		// ** 他人の記事の編集
		$results[0] = array(
			'urlOptions' => array(
				'frame_id' => $data['Frame']['id'],
				'block_id' => $data['Block']['id'],
				'action' => 'edit_question',
				'key' => '257b711744f8fb6ba8313a688a9de52f'
			),
			'assert' => null, 'exception' => 'BadRequestException'
		);
		// ** 自分の記事（一度も公開してない
		$results[1] = array(
			'urlOptions' => array(
				'frame_id' => $data['Frame']['id'],
				'block_id' => $data['Block']['id'],
				'action' => 'edit_question',
				'key' => 'e3eee47e033eccc3f42c02d75678235b'
			),
			'assert' => array('method' => 'assertContains', 'expected' => __d('quizzes', 'Add Question')),
		);
		// 存在してない小テストを指定
		$results[2] = array(
			'urlOptions' => array(
				'frame_id' => $data['Frame']['id'],
				'block_id' => $data['Block']['id'],
				'action' => 'edit_question',
				'key' => 'quiz_99999'),
			'assert' => null, 'exception' => 'BadRequestException', 'return' => 'json'
		);
		//新規作成
		$results[3] = array(
			'urlOptions' => array(
				'frame_id' => $data['Frame']['id'],
				'block_id' => $data['Block']['id'],
				'action' => 'edit_question',
				's_id' => 'testSession'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		//--自分の記事の編集(公開すみ)＝追加ボタンがない
		$results[4] = array(
			'urlOptions' => array(
				'frame_id' => $data['Frame']['id'],
				'block_id' => $data['Block']['id'],
				'action' => 'edit_question',
				'key' => 'cc38fc4c532f2252c3d0861df0c8866c'),
			'assert' => array('method' => 'assertNotContains', 'expected' => __d('quizzes', 'Add Question')),
		);

		return $results;
	}

/**
 * editアクションのGETテスト(編集権限あり、公開権限なし)用DataProvider
 *
 * ### 戻り値
 *  - urlOptions: URLオプション
 *  - assert: テストの期待値
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderEditGetByEditable() {
		$data = $this->__data();

		//テストデータ
		// * 編集権限あり
		$results = array();
		// ** コンテンツあり
		$results[0] = array(
			'urlOptions' => array(
				'frame_id' => $data['Frame']['id'],
				'block_id' => $data['Block']['id'],
				'action' => 'edit_question',
				'key' => '257b711744f8fb6ba8313a688a9de52f'
			),
			'assert' => array('method' => 'assertNotEmpty'),
		);

		// ** コンテンツなし
		$results[count($results)] = array(
			'urlOptions' => array(
				'frame_id' => '14',
				'block_id' => null,
				'action' => 'edit_question',
				'key' => null
			),
			'assert' => null, 'exception' => 'BadRequestException',
			'return' => 'viewFile'
		);

		return $results;
	}

/**
 * editアクションのGETテスト(公開権限あり)用DataProvider
 *
 * ### 戻り値
 *  - urlOptions: URLオプション
 *  - assert: テストの期待値
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderEditGetByPublishable() {
		$data = $this->__data();

		//テストデータ
		// * フレームID指定なしテスト
		$results = array();
		$results[0] = array(
			'urlOptions' => array(
				'frame_id' => null,
				'block_id' => $data['Block']['id'],
				'action' => 'edit_question',
				'key' => '257b711744f8fb6ba8313a688a9de52f'
			),
			'assert' => array('method' => 'assertNotEmpty'),
		);

		return $results;
	}

/**
 * editアクションのPOSTテスト用DataProvider
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
	public function dataProviderEditPost() {
		$data = $this->__data();

		//テストデータ
		$results = array();
		// * ログインなし
		$contentKey = '257b711744f8fb6ba8313a688a9de52f';
		array_push($results, array(
			'data' => $data,
			'role' => null,
			'urlOptions' => array(
				'frame_id' => $data['Frame']['id'],
				'block_id' => $data['Block']['id'],
				'action' => 'edit_question',
				'key' => $contentKey,
			),
			'exception' => 'ForbiddenException'
		));
		// * 作成権限のみ
		// ** 他人の記事
		$contentKey = '257b711744f8fb6ba8313a688a9de52f';
		array_push($results, array(
			'data' => $data,
			'role' => Role::ROOM_ROLE_KEY_GENERAL_USER,
			'urlOptions' => array(
				'frame_id' => $data['Frame']['id'],
				'block_id' => $data['Block']['id'],
				'action' => 'edit_question',
				'key' => $contentKey,
			),
			'exception' => 'BadRequestException'
		));
		// ** 自分の記事
		$contentKey = 'e3eee47e033eccc3f42c02d75678235b';
		array_push($results, array(
			'data' => $this->__data(Role::ROOM_ROLE_KEY_GENERAL_USER),
			'role' => Role::ROOM_ROLE_KEY_GENERAL_USER,
			'urlOptions' => array(
				'frame_id' => $data['Frame']['id'],
				'block_id' => $data['Block']['id'],
				'action' => 'edit_question',
				'key' => $contentKey,
				's_id' => 'testGeneralSession'
			),
		));
		// ** 自分の記事発行済み
		$contentKey = '41e2b809108edead2f30adc37f51e979';
		array_push($results, array(
			'data' => $this->__data(Role::ROOM_ROLE_KEY_GENERAL_USER, '41e2b809108edead2f30adc37f51e979'),
			'role' => Role::ROOM_ROLE_KEY_GENERAL_USER,
			'urlOptions' => array(
				'frame_id' => $data['Frame']['id'],
				'block_id' => $data['Block']['id'],
				'action' => 'edit_question',
				'key' => $contentKey,
				's_id' => 'testGeneralSession'
			),
		));
		// * 編集権限あり
		// ** コンテンツあり
		$contentKey = '257b711744f8fb6ba8313a688a9de52f';
		array_push($results, array(
			'data' => $data,
			'role' => Role::ROOM_ROLE_KEY_EDITOR,
			'urlOptions' => array(
				'frame_id' => $data['Frame']['id'],
				'block_id' => $data['Block']['id'],
				'action' => 'edit_question',
				'key' => $contentKey,
				's_id' => 'testSession'
			),
		));
		// ** フレームID指定なしテスト
		$contentKey = '257b711744f8fb6ba8313a688a9de52f';
		array_push($results, array(
			'data' => $data,
			'role' => Role::ROOM_ROLE_KEY_ROOM_ADMINISTRATOR,
			'urlOptions' => array(
				'frame_id' => null,
				'block_id' => $data['Block']['id'],
				'action' => 'edit_question',
				'key' => $contentKey,
				's_id' => 'testSession'
			),
		));

		return $results;
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
		$result = array(
			'data' => $data,
			'urlOptions' => array(
				'frame_id' => $data['Frame']['id'],
				'block_id' => $data['Block']['id'],
				'action' => 'edit_question',
				'key' => '257b711744f8fb6ba8313a688a9de52f',
				's_id' => 'testSession'
			),
			'validationError' => array(),
		);

		//テストデータ
		$results = array();
		// そうでした。Angularでエラーメッセージを展開しているから、直接的な文字列ではなく
		// Angular変数名を書くしかないんだった
		array_push($results, Hash::merge($result, array(
			'validationError' => array(
				'field' => 'QuizPage.0.QuizQuestion.0.allotment',
				'value' => '',
				'message' => 'question.errorMessages.allotment'
			)
		)));
		array_push($results, Hash::merge($result, array(
			'validationError' => array(
				'field' => 'QuizPage.0.QuizQuestion.0.question_value',
				'value' => '',
				'message' => 'question.errorMessages.questionValue'
			)
		)));
		array_push($results, Hash::merge($result, array(
			'validationError' => array(
				'field' => 'QuizPage.0.QuizQuestion.0.question_type',
				'value' => 'aaaaaa',
				'message' => 'question.errorMessages.questionType'
			)
		)));
		array_push($results, Hash::merge($result, array(
			'validationError' => array(
				'field' => 'QuizPage.0.QuizQuestion.0.question_type',
				'value' => 'aaaaaa',
				'message' => 'question.errorMessages.questionType'
			)
		)));
		array_push($results, Hash::merge($result, array(
			'validationError' => array(
				'field' => 'QuizPage.0.QuizQuestion.0.QuizChoice.0.choice_label',
				'value' => '',
				'message' => 'choice.errorMessages.choiceLabel',
			)
		)));
		array_push($results, Hash::merge($result, array(
			'validationError' => array(
				'field' => 'QuizPage.0.QuizQuestion.0.QuizCorrect.0.correct',
				'value' => '',
				'message' => 'question.errorMessages.questionPickupError',
			)
		)));
		return $results;
	}

/**
 * Viewのアサーション
 *
 * @param array $data テストデータ
 * @return void
 */
	private function __assertEditGet($data) {
		$this->assertInput(
			'input', 'data[Frame][id]', $data['Frame']['id'], $this->view);
		$this->assertInput(
			'input', 'data[Block][id]', $data['Block']['id'], $this->view);
		// ページタブ,ページ追加リンク,質問追加ボタン,質問LI、質問種別選択,質問削除ボタン,
		// 選択肢追加ボタン, 選択肢削除ボタン、キャンセルボタン、次へボタンの存在の確認
		$this->assertInput(
			'input', 'data[QuizPage][{{pageIndex}}][page_sequence]', null, $this->view);
		$this->assertInput(
			'input', 'data[QuizPage][{{pageIndex}}][key]', null, $this->view);
		$this->assertInput('input',
			'data[QuizPage][{{pageIndex}}][QuizQuestion][{{qIndex}}][question_sequence]',
			null, $this->view);
		$this->assertInput('input',
			'data[QuizPage][{{pageIndex}}][QuizQuestion][{{qIndex}}][key]',
			null, $this->view);
		$this->assertInput('input',
			'data[QuizPage][{{pageIndex}}][QuizQuestion][{{qIndex}}][key]',
			null, $this->view);
		$this->assertInput('input',
			'data[QuizPage][{{pageIndex}}][QuizQuestion][{{qIndex}}][allotment]',
			null, $this->view);
		$this->assertInput('input',
			'data[QuizPage][{{pageIndex}}][QuizQuestion][{{qIndex}}][question_value]',
			null, $this->view);
		$this->assertInput('input',
			'data[QuizPage][{{pageIndex}}][QuizQuestion][{{qIndex}}][question_type]',
			null, $this->view);
		$this->assertInput('input',
			'data[QuizPage][{{pageIndex}}][QuizQuestion][{{qIndex}}][is_choice_horizon]',
			null, $this->view);
		$this->assertInput('input',
			'data[QuizPage][{{pageIndex}}][QuizQuestion][{{qIndex}}][is_choice_random]',
			null, $this->view);
		$this->assertInput('input',
			'data[QuizPage][{{pageIndex}}][QuizQuestion][{{qIndex}}][QuizChoice]' .
			'[{{choice.choiceSequence}}][choice_label]',
			null, $this->view);
		$this->assertInput('input',
			'data[QuizPage][{{pageIndex}}][QuizQuestion][{{qIndex}}][QuizChoice]' .
			'[{{choice.choiceSequence}}][key]',
			null, $this->view);

		$this->assertInput('input',
			'data[QuizPage][{{pageIndex}}][QuizQuestion][{{qIndex}}][QuizCorrect][0][correct]',
			null, $this->view);
	}

/**
 * view(ctp)ファイルのテスト(公開権限なし)
 *
 * @return void
 */
	public function testViewFileByEditable() {
		TestAuthGeneral::login($this, Role::ROOM_ROLE_KEY_EDITOR);

		//テスト実行
		$data = $this->__data();
		$this->_testGetAction(
			array(
				'frame_id' => $data['Frame']['id'],
				'block_id' => $data['Block']['id'],
				'action' => 'edit_question',
				'key' => '257b711744f8fb6ba8313a688a9de52f',
			),
			array('method' => 'assertNotEmpty')
		);

		//チェック
		$this->__assertEditGet($data);

		TestAuthGeneral::logout($this);
	}

/**
 * view(ctp)ファイルのテスト(公開権限あり)
 *
 * @return void
 */
	public function testViewFileByPublishable() {
		//ログイン
		TestAuthGeneral::login($this);

		//テスト実行
		$data = $this->__data();
		$urlOptions = array(
			'block_id' => $data['Block']['id'],
			'frame_id' => $data['Frame']['id'],
			'action' => 'edit_question',
			'key' => '257b711744f8fb6ba8313a688a9de52f',
		);
		$this->_testGetAction($urlOptions, array('method' => 'assertNotEmpty'));

		//チェック
		$this->__assertEditGet($data);
		// この画面ではまだ「一時保存」「決定」「削除」ボタンは現れません
		//$this->assertInput('button', 'save_' . WorkflowComponent::STATUS_IN_DRAFT, null, $this->view);
		//$this->assertInput('button', 'save_' . WorkflowComponent::STATUS_PUBLISHED, null, $this->view);
		//$this->assertInput('input', '_method', 'DELETE', $this->view);
		// 次へボタン
		$this->assertInput('button', 'save', null, $this->view);

		// セッティングモード画面からの編集画面のときのキャンセル戻り先
		$urlOptions = array(
			'block_id' => $data['Block']['id'],
			'frame_id' => $data['Frame']['id'],
			'action' => 'edit_question',
			'q_mode' => 'setting',
			'key' => '257b711744f8fb6ba8313a688a9de52f',
		);
		$result = $this->_testGetAction($urlOptions, array('method' => 'assertNotEmpty'));
		$this->assertActionLink('index', array('controller' => 'quiz_blocks'), true, $result);

		//ログアウト
		TestAuthGeneral::logout($this);
	}

}
