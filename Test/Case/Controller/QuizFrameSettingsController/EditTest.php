<?php
/**
 * QuizFrameSettingsController::edit()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('FrameSettingsControllerTest', 'Frames.TestSuite');
App::uses('QuizFixture', 'Quizzes.Test/Fixture');
App::uses('QuizzesComponent', 'Quizzes.Controller/Component');

/**
 * QuizFrameSettingsController::edit()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\Controller\QuizFrameSettingsController
 */
class QuizFrameSettingsControllerEditTest extends FrameSettingsControllerTest {

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
	protected $_controller = 'quiz_frame_settings';

/**
 * テストDataの取得
 *
 * @return array
 */
	private function __data() {
		$frameId = '6';
		$frameKey = 'frame_3';
		$quizFrameId = '1';
		$blockId = '2';

		$data = array(
			'Frame' => array(
				'id' => $frameId,
				'key' => $frameKey,
			),
			'Block' => array(
				'id' => $blockId,
			),
			'QuizFrameSetting' => array(
				'id' => $quizFrameId,
				'frame_key' => $frameKey,
				'display_type' => QuizzesComponent::DISPLAY_TYPE_LIST,
				'display_num_per_page' => '10',
				'sort_type' => 'Quiz.modified DESC',
			),
		);
		$quizRec = (new QuizFixture())->records;
		$break = 1;
		$displayQ = array();
		foreach ($quizRec as $index => $rec) {
			$displayQ['List']['QuizFrameDisplayQuiz'][$rec['key']] = array(
				'is_display' => '1', 'quiz_key' => $rec['key']);
			if ($index == $break) {
				break;
			}
		}
		$displayQ['Single']['QuizFrameDisplayQuiz']['quiz_key'] = $quizRec[0]['key'];

		$data = Hash::merge($data, $displayQ);

		return $data;
	}

/**
 * edit()アクションDataProvider
 *
 * ### 戻り値
 *  - method: リクエストメソッド（get or post or put）
 *  - data: 登録データ
 *  - validationError: バリデーションエラー(省略可)
 *  - exception: Exception Error(省略可)
 *
 * @return array
 */
	public function dataProviderEdit() {
		$data = $this->__data();

		//テストデータ
		$results = array();
		$results[0] = array('method' => 'get');
		$results[1] = array('method' => 'post', 'data' => $data, 'validationError' => false);
		$results[2] = array('method' => 'put', 'data' => $data, 'validationError' => false);
		$results[3] = array('method' => 'put', 'data' => $data,
			'validationError' => array(
				'field' => 'QuizFrameSetting.display_type',
				'value' => null, 'message' => __d('net_commons', 'Invalid request.')
			),
		);

		return $results;
	}

}
