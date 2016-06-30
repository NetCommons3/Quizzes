<?php
/**
 * QuizEditController::delete()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('WorkflowControllerDeleteTest', 'Workflow.TestSuite');

/**
 * QuizEditController::delete()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\Controller\QuizEditController
 */
class QuizEditControllerDeleteTest extends WorkflowControllerDeleteTest {

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
			->will($this->returnValueMap([['Quizzes.quizEdit.' . 'testSession', true]]));
		$this->controller->Session->expects($this->any())
			->method('read')
			->will($this->returnValueMap([['Quizzes.quizEdit.' . 'testSession', $this->__data()]]));
	}
/**
 * テストDataの取得
 *
 * @param string $contentKey キー
 * @return array
 */
	private function __data($contentKey = null) {
		$frameId = '6';
		$blockId = '2';
		$blockKey = 'block_1';
		if ($contentKey === 'chief_write') {
			$contentKey = '257b711744f8fb6ba8313a688a9de52f';
			$contentId = '37';
		} elseif ($contentKey === 'general_user_write_published') {
			// 一般作成公開済み
			$contentKey = '41e2b809108edead2f30adc37f51e979';
			$contentId = '35';
		} else {
			// 一般作成未公開
			$contentKey = 'e3eee47e033eccc3f42c02d75678235b';
			$contentId = '38';
		}

		$data = array(
			'delete' => null,
			'Frame' => array(
				'id' => $frameId,
			),
			'Block' => array(
				'id' => $blockId,
				'key' => $blockKey,
			),

			'Quiz' => array(
				'id' => $contentId,
				'key' => $contentKey,
			),
		);

		return $data;
	}

/**
 * deleteアクションのGETテスト用DataProvider
 *
 * ### 戻り値
 *  - role: ロール
 *  - urlOptions: URLオプション
 *  - assert: テストの期待値
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderDeleteGet() {
		$data = $this->__data();

		//テストデータ
		$results = array();
		// * ログインなし
		$results[0] = array('role' => null,
			'urlOptions' => array(
				'frame_id' => $data['Frame']['id'],
				'block_id' => $data['Block']['id'],
				'key' => 'general_user_write_un_published',
			),
			'assert' => null, 'exception' => 'ForbiddenException'
		);
		// * 作成権限のみ(自分自身)
		array_push($results, Hash::merge($results[0], array(
			'role' => Role::ROOM_ROLE_KEY_GENERAL_USER,
			'urlOptions' => array(
				'frame_id' => $data['Frame']['id'],
				'block_id' => $data['Block']['id'],
				'key' => 'general_user_write_un_published',
			),
			'assert' => null, 'exception' => 'BadRequestException'
		)));
		// * 編集権限、公開権限なし
		array_push($results, Hash::merge($results[0], array(
			'role' => Role::ROOM_ROLE_KEY_EDITOR,
			'assert' => null, 'exception' => 'BadRequestException'
		)));
		// * 公開権限あり
		array_push($results, Hash::merge($results[0], array(
			'role' => Role::ROOM_ROLE_KEY_ROOM_ADMINISTRATOR,
			'assert' => null, 'exception' => 'BadRequestException',
			'return' => 'json'
		)));

		return $results;
	}

/**
 * deleteアクションのPOSTテスト用DataProvider
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
	public function dataProviderDeletePost() {
		//テストデータ
		$results = array();
		// * ログインなし
		$data0 = $this->__data('chief_write');
		array_push($results, array(
			'data' => $data0, 'role' => null,
			'urlOptions' => array(
				'frame_id' => $data0['Frame']['id'],
				'block_id' => $data0['Block']['id'],
				'key' => $data0['Quiz']['key']
			),
			'exception' => 'ForbiddenException'
		));
		// * 作成権限のみ
		// ** 他人の記事
		$data1 = $this->__data('chief_write');
		array_push($results, array(
			'data' => $data1, 'role' => Role::ROOM_ROLE_KEY_GENERAL_USER,
			'urlOptions' => array(
				'frame_id' => $data1['Frame']['id'],
				'block_id' => $data1['Block']['id'],
				'key' => $data1['Quiz']['key']
			),
			'exception' => 'BadRequestException',
			'return' => 'json'
		));
		// ** 自分の記事＆一度も公開されていない
		$data2 = $this->__data('general_user_write_un_published');
		array_push($results, array(
			'data' => $data2, 'role' => Role::ROOM_ROLE_KEY_GENERAL_USER,
			'urlOptions' => array(
				'frame_id' => $data2['Frame']['id'],
				'block_id' => $data2['Block']['id'],
				'key' => $data2['Quiz']['key']
			),
		));
		// ** 自分の記事＆一度公開している
		$data3 = $this->__data('general_user_write_published');
		array_push($results, array(
			'data' => $data3, 'role' => Role::ROOM_ROLE_KEY_GENERAL_USER,
			'urlOptions' => array(
				'frame_id' => $data3['Frame']['id'],
				'block_id' => $data3['Block']['id'],
				'key' => $data3['Quiz']['key']
			),
			'exception' => 'BadRequestException',
			'return' => 'json'
		));
		// * 編集権限あり
		// ** 公開していない
		$data4 = $this->__data('general_user_write_un_published');
		array_push($results, array(
			'data' => $data4, 'role' => Role::ROOM_ROLE_KEY_EDITOR,
			'urlOptions' => array(
				'frame_id' => $data4['Frame']['id'],
				'block_id' => $data4['Block']['id'],
				'key' => $data4['Quiz']['key']
			),
		));
		// ** 公開している
		$data5 = $this->__data('general_user_write_published');
		array_push($results, array(
			'data' => $data5, 'role' => Role::ROOM_ROLE_KEY_EDITOR,
			'urlOptions' => array(
				'frame_id' => $data5['Frame']['id'],
				'block_id' => $data5['Block']['id'],
				'key' => $data5['Quiz']['key']
			),
			'exception' => 'BadRequestException',
			'return' => 'json'
		));
		// * 公開権限あり
		// ** フレームID指定なしテスト
		$data6 = $this->__data('chief_write');
		array_push($results, array(
			'data' => $data6, 'role' => Role::ROOM_ROLE_KEY_ROOM_ADMINISTRATOR,
			'urlOptions' => array(
				'frame_id' => null,
				'block_id' => $data6['Block']['id'],
				'key' => $data6['Quiz']['key']
			),
		));
		array_push($results, array(
			'data' => $data6, 'role' => Role::ROOM_ROLE_KEY_ROOM_ADMINISTRATOR,
			'urlOptions' => array(
				'frame_id' => $data6['Frame']['id'],
				'block_id' => $data5['Block']['id'],
				'q_mode' => 'setting',
				'key' => $data6['Quiz']['key']
			),
		));

		return $results;
	}

/**
 * deleteアクションのExceptionErrorテスト用DataProvider
 *
 * ### 戻り値
 *  - mockModel: Mockのモデル
 *  - mockMethod: Mockのメソッド
 *  - data: 登録データ
 *  - urlOptions: URLオプション
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderDeleteExceptionError() {
		$data = $this->__data();

		//テストデータ
		$results = array();
		$results[0] = array(
			'mockModel' => 'Quizzes.Quiz',
			'mockMethod' => 'deleteQuiz',
			'data' => $data,
			'urlOptions' => array(
				'frame_id' => $data['Frame']['id'],
				'block_id' => $data['Block']['id'],
				'key' => '257b711744f8fb6ba8313a688a9de52f',
			),
			'exception' => 'BadRequestException',
			'return' => 'json'
		);

		return $results;
	}

}
