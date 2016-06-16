<?php
/**
 * QuizChoice::saveQuizChoice()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('QuizSaveTest', 'Quizzes.TestSuite');
App::uses('QuizChoiceFixture', 'Quizzes.Test/Fixture');
App::uses('QuizDataGetTest', 'Quizzes.TestSuite');

/**
 * QuizChoice::saveQuizChoice()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\Model\QuizChoice
 */
class QuizChoiceSaveQuizChoiceTest extends QuizSaveTest {

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
	protected $_modelName = 'QuizChoice';

/**
 * Method name
 *
 * @var string
 */
	protected $_methodName = 'saveQuizChoice';

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
		$data = $quiz['QuizPage'][0]['QuizQuestion'][0]['QuizChoice'];
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
		$data['QuizChoice'] = $this->__getData(6);

		$results = array();
		$results[0] = array($data);
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
		$data = $this->dataProviderSave()[0][0];

		return array(
			array($data, 'Quizzes.QuizChoice', 'save'),
		);
	}

}
