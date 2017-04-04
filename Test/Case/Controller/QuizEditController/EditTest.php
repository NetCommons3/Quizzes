<?php
/**
 * QuizEditController::edit()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('WorkflowControllerEditTest', 'Workflow.TestSuite');

/**
 * QuizEditController::edit()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\Controller\QuizEditController
 */
class QuizEditControllerEditTest extends WorkflowControllerEditTest {

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
				['Quizzes.quizEdit.testSession', true],
				['Quizzes.quizEdit.testGeneralSession', true]
			]));
		$this->controller->Session->expects($this->any())
			->method('read')
			->will($this->returnValueMap([
				['Quizzes.quizEdit.testSession', $this->__data()],
				['Quizzes.quizEdit.testGeneralSession',	$this->__data(Role::ROOM_ROLE_KEY_GENERAL_USER)]
			]));
	}
/**
 * テストDataの取得
 *
 * @param string $role ロール
 * @param string $key 小テストキー
 * @return array
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
				'room_id' => '2',
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
				'answer_start_period' => '2016-08-01 00:00:00',
				'answer_end_period' => '2016-08-31 23:59:59',
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
			'QuizPage' => array(array(
				'page_title' => null,
				'page_sequence' => '0',
				'is_page_description' => 1,
				'page_description' => 'ページ先頭の問題文章',
				'QuizQuestion' => array(array(
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
					'QuizCorrect' => array(array(
						'correct_sequence' => '0',
						'correct' => '新規選択肢1',
					)),
				))
			)),
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
				'key' => '257b711744f8fb6ba8313a688a9de52f'
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
		// ** 他人の記事
		$results[0] = array(
			'urlOptions' => array(
				'frame_id' => $data['Frame']['id'],
				'block_id' => $data['Block']['id'],
				'key' => '257b711744f8fb6ba8313a688a9de52f'
			),
			'assert' => array('method' => 'assertContains', 'expected' => __d('quizzes', 'not found this quiz.')),
			//'assert' => null, 'exception' => 'BadRequestException'
		);
		// ** 自分の記事（一度も公開してない
		$results[1] = array(
			'urlOptions' => array(
				'frame_id' => $data['Frame']['id'],
				'block_id' => $data['Block']['id'],
				'key' => 'e3eee47e033eccc3f42c02d75678235b'
			),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		// 存在してない小テストを指定
		$results[2] = array(
			'urlOptions' => array(
				'frame_id' => $data['Frame']['id'],
				'block_id' => $data['Block']['id'],
				'key' => 'quiz_99999'),
			'assert' => array('method' => 'assertContains', 'expected' => __d('quizzes', 'not found this quiz.')),
			//'assert' => null, 'exception' => 'BadRequestException', 'return' => 'json'
		);
		//新規作成
		$results[3] = array(
			'urlOptions' => array(
				'frame_id' => $data['Frame']['id'],
				'block_id' => $data['Block']['id'],
				's_id' => 'testSession'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		//--自分の記事の編集(公開すみ)＝合格点が変更できない
		$results[4] = array(
			'urlOptions' => array(
				'frame_id' => $data['Frame']['id'],
				'block_id' => $data['Block']['id'],
				'key' => 'cc38fc4c532f2252c3d0861df0c8866c'),
			'assert' => array('method' => 'assertRegExp', 'expected' => '<input.*?disabled="disabled".*?\/>'),
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
				'key' => '257b711744f8fb6ba8313a688a9de52f'
			),
			'assert' => array('method' => 'assertNotEmpty'),
		);

		// ** コンテンツなし
		$results[count($results)] = array(
			'urlOptions' => array(
				'frame_id' => '14',
				'block_id' => null,
				'key' => null
			),
			'assert' => array('method' => 'assertContains', 'expected' => __d('quizzes', 'not found this quiz.')),
			//'assert' => null, 'exception' => 'BadRequestException',
			//'return' => 'viewFile'
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
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
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
				'key' => $contentKey,
				's_id' => 'testSession'
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
				'key' => $contentKey,
				's_id' => 'testSession'
			),
			'exception' => 'BadRequestException'
		));
		// 一般が書いた一時保存データ 編集者はかけるよね
		$contentKey = 'e3eee47e033eccc3f42c02d75678235b';
		array_push($results, array(
			'data' => $this->__data(Role::ROOM_ROLE_KEY_GENERAL_USER, $contentKey),
			'role' => Role::ROOM_ROLE_KEY_EDITOR,
			'urlOptions' => array(
				'frame_id' => $data['Frame']['id'],
				'block_id' => $data['Block']['id'],
				'key' => $contentKey,
				's_id' => 'testGeneralSession'),
		));
		// ** 自分の記事 一度も公開してない
		$contentKey = 'e3eee47e033eccc3f42c02d75678235b';
		array_push($results, array(
			'data' => $this->__data(Role::ROOM_ROLE_KEY_GENERAL_USER, $contentKey),
			'role' => Role::ROOM_ROLE_KEY_GENERAL_USER,
			'urlOptions' => array(
				'frame_id' => $data['Frame']['id'],
				'block_id' => $data['Block']['id'],
				'key' => $contentKey,
				's_id' => 'testGeneralSession'
			),
		));
		// ** 自分の記事 公開済
		$contentKey = 'cc38fc4c532f2252c3d0861df0c8866c';
		array_push($results, array(
			'data' => $this->__data(Role::ROOM_ROLE_KEY_GENERAL_USER, $contentKey),
			'role' => Role::ROOM_ROLE_KEY_GENERAL_USER,
			'urlOptions' => array(
				'frame_id' => $data['Frame']['id'],
				'block_id' => $data['Block']['id'],
				'key' => $contentKey,
				's_id' => 'testGeneralSession',
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
				'key' => $contentKey,
				's_id' => 'testSession'
			),
		));
		// 新規作成
		array_push($results, array(
			'data' => $data,
			'role' => Role::ROOM_ROLE_KEY_EDITOR,
			'urlOptions' => array(
				'frame_id' => $data['Frame']['id'],
				'block_id' => $data['Block']['id'],
				's_id' => 'testSession'),
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
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 */
	public function dataProviderEditValidationError() {
		$data = $this->__data();
		$result = array(
			'data' => $data,
			'urlOptions' => array(
				'frame_id' => $data['Frame']['id'],
				'block_id' => $data['Block']['id'],
				'key' => '257b711744f8fb6ba8313a688a9de52f',
				's_id' => 'testSession'
			),
			'validationError' => array(),
		);
		$resultPeriodOn = Hash::merge($result, array(
			'data' => array('Quiz' => array('answer_timing' => '1'))));

		//テストデータ
		$results = array();
		array_push($results, Hash::merge($result, array(
			'validationError' => array(
				'field' => 'Quiz.title',
				'value' => '',
				'message' => __d('net_commons', 'Please input %s.', __d('quizzes', 'Title')),
			)
		)));
		array_push($results, Hash::merge($result, array(
			'validationError' => array(
				'field' => 'Quiz.estimated_time',
				'value' => 'aaa',
				'message' => __d('quizzes', 'Please input natural number.'),
			)
		)));
		array_push($results, Hash::merge($result, array(
			'validationError' => array(
				'field' => 'Quiz.passing_grade',
				'value' => 'aaa',
				'message' => __d('quizzes', 'Please input natural number.'),
			)
		)));
		array_push($results, Hash::merge($result, array(
			'validationError' => array(
				'field' => 'Quiz.is_repeat_allow',
				'value' => 'aaa',
				'message' => __d('net_commons', 'Invalid request.'),
			)
		)));
		array_push($results, Hash::merge($result, array(
			'validationError' => array(
				'field' => 'Quiz.is_repeat_until_passing',
				'value' => 'aaa',
				'message' => __d('net_commons', 'Invalid request.'),
			)
		)));
		array_push($results, Hash::merge($result, array(
			'validationError' => array(
				'field' => 'Quiz.is_page_random',
				'value' => 'aa',
				'message' => __d('net_commons', 'Invalid request.'),
			)
		)));
		array_push($results, Hash::merge($result, array(
			'validationError' => array(
				'field' => 'Quiz.is_correct_show',
				'value' => 'aa',
				'message' => __d('net_commons', 'Invalid request.'),
			)
		)));
		array_push($results, Hash::merge($result, array(
			'validationError' => array(
				'field' => 'Quiz.is_total_show',
				'value' => 'aa',
				'message' => __d('net_commons', 'Invalid request.'),
			)
		)));
		array_push($results, Hash::merge($result, array(
			'validationError' => array(
				'field' => 'Quiz.answer_timing',
				'value' => 'aa',
				'message' => __d('net_commons', 'Invalid request.'),
			)
		)));
		array_push($results, Hash::merge($resultPeriodOn,
			array(
				'data' => array('Quiz' => array('answer_end_period' => null))
			),
			array(
				'validationError' => array(
					'field' => 'Quiz.answer_start_period',
					'value' => null,
					'message' => __d('quizzes', 'if you set the period, please set time.')
				)
			)
		));
		/*
		array_push($results, Hash::merge($resultPeriodOn, array(
			'validationError' => array(
				'field' => 'Quiz.answer_end_period',
				'value' => '',
				'message' => __d('net_commons',
					'Unauthorized pattern for %s. Please input the data in %s format.',
					__d('quizzes', 'Start period'), 'YYYY-MM-DD hh:mm:ss'),
			)
		)));
		*/
		array_push($results, Hash::merge($resultPeriodOn, array(
			'validationError' => array(
				'field' => 'Quiz.answer_end_period',
				'value' => '1999-01-01 00:00:00',
				'message' => __d('quizzes', 'start period must be smaller than end period.')
			)
		)));
		array_push($results, Hash::merge($result, array(
			'validationError' => array(
				'field' => 'Quiz.is_no_member_allow',
				'value' => 'aa',
				'message' => __d('net_commons', 'Invalid request.'),
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
			'input', 'data[Frame][id]', $data['Frame']['id'], $this->view
		);
		$this->assertInput(
			'input', 'data[Block][id]', $data['Block']['id'], $this->view
		);
		$this->assertInput('input', 'data[Quiz][title]', null, $this->view);
		$this->assertInput('input', 'data[Quiz][estimated_time]', null, $this->view);
		$this->assertInput('input', 'data[Quiz][passing_grade]', null, $this->view);
		$this->assertInput('input', 'data[Quiz][is_repeat_allow]', null, $this->view);
		$this->assertInput('input', 'data[Quiz][is_repeat_until_passing]', null, $this->view);
		$this->assertInput('input', 'data[Quiz][is_page_random]', null, $this->view);
		$this->assertInput('input', 'data[Quiz][is_correct_show]', null, $this->view);
		$this->assertInput('input', 'data[Quiz][is_total_show]', null, $this->view);

		$this->assertInput('input', 'data[Quiz][answer_timing]', null, $this->view);
		$this->assertInput('input', 'data[Quiz][is_no_member_allow]', null, $this->view);
		$this->assertInput('input', 'data[Quiz][is_key_pass_use]', null, $this->view);
		$this->assertInput('input', 'data[Quiz][is_image_authentication]', null, $this->view);
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
		//本人のデータだが公開済みで削除できない
		$this->_testGetAction(
			array(
				'action' => 'edit',
				'frame_id' => $data['Frame']['id'],
				'block_id' => $data['Block']['id'],
				'key' => '468e3c55607b0c1d5cf55ddad51f836a',
			),
			array('method' => 'assertNotEmpty')
		);

		//チェック
		$this->__assertEditGet($data);
		$this->assertInput('button', 'save_' . WorkflowComponent::STATUS_IN_DRAFT, null, $this->view);
		$this->assertInput('button', 'save_' . WorkflowComponent::STATUS_APPROVAL_WAITING, null, $this->view);
		$this->assertNotRegExp('/<input.*?name="_method" value="DELETE".*?>/', $this->view);

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
			'action' => 'edit',
			'block_id' => $data['Block']['id'],
			'frame_id' => $data['Frame']['id'],
			'key' => '257b711744f8fb6ba8313a688a9de52f',
		);
		$this->_testGetAction($urlOptions, array('method' => 'assertNotEmpty'));

		//チェック
		$this->__assertEditGet($data);
		$this->assertInput('button', 'save_' . WorkflowComponent::STATUS_IN_DRAFT, null, $this->view);
		$this->assertInput('button', 'save_' . WorkflowComponent::STATUS_PUBLISHED, null, $this->view);
		$this->assertInput('input', '_method', 'DELETE', $this->view);

		//ログアウト
		TestAuthGeneral::logout($this);
	}

}
