<?php
/**
 * QuizAnswersController::test_mode()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('WorkflowControllerViewTest', 'Workflow.TestSuite');

/**
 * QuizAnswersController::test_mode()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\Controller\QuizAnswersController
 */
class QuizAnswersControllerTestModeTest extends WorkflowControllerViewTest {

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
		$contentKey = '5fdb4f0049f3bddeabc49cd2b72c6ac9';

		$data = array(
			'action' => 'test_mode',
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
		$results = array();
		// 公開済みのデータのとき
		$results[0] = array(
			'urlOptions' => Hash::insert($data, 'key', '5fdb4f0049f3bddeabc49cd2b72c6ac9'),
			'assert' => null
		);
		// 一時保存のデータのとき
		$results[0] = array(
			'urlOptions' => Hash::insert($data, 'key', 'cc38fc4c532f2252c3d0861df0c8866c'),
			'assert' => null
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
		$result = $this->headers['Location'];
		$this->assertTextContains('/quizzes/quiz_answers/view/2/' . $urlOptions['key'], $result);
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
		$results = array();
		// 公開済みのデータのとき
		$results[0] = array(
			'urlOptions' => Hash::insert($data, 'key', '5fdb4f0049f3bddeabc49cd2b72c6ac9'),
			'assert' => null
		);
		// 一時保存のデータのとき(自分
		$results[1] = array(
			'urlOptions' => Hash::insert($data, 'key', 'e3eee47e033eccc3f42c02d75678235b'),
			'assert' => array(
				'method' => 'assertTextContains',
				'expected' => __d('quizzes', 'Start the test answers of this quiz'))
		);
		// 一時保存のデータのとき(偉い人
		$results[2] = array(
			'urlOptions' => Hash::insert($data, 'key', '257b711744f8fb6ba8313a688a9de52f'),
			'assert' => null, 'exception' => 'BadRequestException'
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
		//テスト実行
		parent::testViewByCreatable($urlOptions, $assert, $exception, $return);
		if ($exception) {
			return;
		}
		if ($assert === null && $exception === null) {
			$result = $this->headers['Location'];
			$this->assertTextContains('/quizzes/quiz_answers/view/2/' . $urlOptions['key'], $result);
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

		//テストデータ
		$results = array();
		// 一時保存のデータのとき(偉い人
		$results[0] = array(
			'urlOptions' => Hash::insert($data, 'key', 'e9329d3567b76c1b880e1a80a74c12f5'),
			'assert' => array(
				'method' => 'assertTextContains',
				'expected' => __d('quizzes', 'Start the test answers of this quiz'))
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
	}
}
