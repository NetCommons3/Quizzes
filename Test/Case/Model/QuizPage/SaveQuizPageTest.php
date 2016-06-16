<?php
/**
 * QuizPage::saveQuizPage()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('QuizSaveTest', 'Quizzes.TestSuite');
App::uses('QuizPageFixture', 'Quizzes.Test/Fixture');
App::uses('QuizDataGetTest', 'Quizzes.TestSuite');

/**
 * QuizPage::saveQuizPage()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\Model\QuizPage
 */
class QuizPageSaveQuizPageTest extends QuizSaveTest {

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
 * Model name
 *
 * @var string
 */
	protected $_modelName = 'QuizPage';

/**
 * Method name
 *
 * @var string
 */
	protected $_methodName = 'saveQuizPage';

/**
 * テストDataの取得
 *
 * @param string $quizId quizId
 * @return array
 */
	private function __getData($quizId) {
		$dataGet = new QuizDataGetTest();
		//データ生成
		$quiz = $dataGet->getData($quizId);
		$data = $quiz['QuizPage'][0];
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
		$data['QuizPage'][0] = $this->__getData(6);

		$results = array();
		// * 編集の登録処理
		$results[0] = array($data);
		// * 新規の登録処理
		$results[1] = array($data);
		$results[1] = Hash::insert($results[1], 'QuizPage.0.id', null);
		$results[1] = Hash::insert($results[1], 'QuizPage.0.key', null);
		$results[1] = Hash::remove($results[1], 'QuizPage.0.created_user');

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
		$data['QuizPage'][0] = $this->__getData(6);

		return array(
			array($data, 'Quizzes.QuizPage', 'save'),
		);
	}
}
