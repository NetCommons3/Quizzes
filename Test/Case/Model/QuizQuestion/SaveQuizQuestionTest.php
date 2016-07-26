<?php
/**
 * QuizQuestion::saveQuizQuestion()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('QuizSaveTest', 'Quizzes.TestSuite');
App::uses('QuizQuestionFixture', 'Quizzes.Test/Fixture');
App::uses('QuizDataGetTest', 'Quizzes.TestSuite');

/**
 * QuizQuestion::saveQuizQuestion()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\Model\QuizQuestion
 */
class QuizQuestionSaveQuizQuestionTest extends QuizSaveTest {

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
	);

/**
 * Plugin name
 *
 * @var string
 */
	public $plugin = 'quizzes';

/**
 * Model name
 *
 * @var string
 */
	protected $_modelName = 'QuizQuestion';

/**
 * Method name
 *
 * @var string
 */
	protected $_methodName = 'saveQuizQuestion';

/**
 * テストDataの取得
 *
 * @param string $id questionId
 * @return array
 */
	private function __getData($id = null) {
		$data = (new QuizQuestionFixture())->records[0];
		foreach ($data as $key => &$value) {
			if (preg_match('/^is_/', $key)) {
				if ($value == 0) {
					$value = false;
				} else {
					$value = true;
				}
			}
		}
		$data = Hash::remove($data, 'created');
		return $data;
	}
/**
 * テストDataの取得
 *
 * @param string $quizId quizId
 * @return array
 */
	private function __getDataWithChoiceAndCorrect($quizId) {
		$dataGet = new QuizDataGetTest();
		//データ生成
		$quiz = $dataGet->getData($quizId);
		$data = $quiz['QuizPage'][0]['QuizQuestion'][0];
		return $data;
	}

/**
 * Save用DataProvider
 *
 * ### 戻り値
 *  - data 登録データ
 *
 * @return array テストデータ
 */
	public function dataProviderSave() {
		$data['QuizQuestion'][0] = $this->__getData(18);
		$dataWithChoice['QuizQuestion'][0] = $this->__getDataWithChoiceAndCorrect(6);

		$results = array();
		// * 編集の登録処理
		$results[0] = array($data);
		// * 新規の登録処理
		$results[1] = array($data);
		$results[1] = Hash::insert($results[1], 'QuizQuestion.0.id', null);
		$results[1] = Hash::insert($results[1], 'QuizQuestion.0.key', null);
		$results[1] = Hash::remove($results[1], 'QuizQuestion.0.created_user');

		$results[2] = array($dataWithChoice);

		return $results;
	}

/**
 * SaveのExceptionError用DataProvider
 *
 * ### 戻り値
 *  - data 登録データ
 *  - mockModel Mockのモデル
 *  - mockMethod Mockのメソッド
 *
 * @return array テストデータ
 */
	public function dataProviderSaveOnExceptionError() {
		$data['QuizQuestion'][0] = $this->__getData(18);

		return array(
			array($data, 'Quizzes.QuizQuestion', 'save'),
		);
	}
}
