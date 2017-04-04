<?php
/**
 * Quiz::deleteQuiz()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('WorkflowDeleteTest', 'Workflow.TestSuite');
App::uses('QuizFixture', 'Quizzes.Test/Fixture');
App::uses('QuizzesComponent', 'Quizzes.Controller/Component');
App::uses('QuizDataGetTest', 'Quizzes.TestSuite');

/**
 * Quiz::deleteQuiz()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\Model\Quiz
 */
class QuizDeleteQuizTest extends WorkflowDeleteTest {

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
 * Model name
 *
 * @var string
 */
	protected $_modelName = 'Quiz';

/**
 * Method name
 *
 * @var string
 */
	protected $_methodName = 'deleteQuiz';

/**
 * テストAssociationDataの取得
 *
 * @param string $quizKey quiz key
 * @return array
 */
	private function __getAssociation($quizKey) {
		$association = array(
			'QuizFrameDisplayQuiz' => array(
				'quiz_key' => $quizKey,
			),
			'QuizAnswerSummary' => array(
				'quiz_key' => $quizKey,
			),
		);
		return $association;
	}

/**
 * Delete用DataProvider
 *
 * ### 戻り値
 *  - data: 削除データ
 *  - associationModels: 削除確認の関連モデル array(model => conditions)
 *
 * @return array テストデータ
 */
	public function dataProviderDelete() {
		$dataGet = new QuizDataGetTest();
		$data = $dataGet->getData(51);
		$association = $this->__getAssociation('83b294e176a8c8026d4fbdb07ad2ed7f');

		$results = array();
		$results[0] = array($data, $association);

		return $results;
	}

/**
 * ExceptionError用DataProvider
 *
 * ### 戻り値
 *  - data 登録データ
 *  - mockModel Mockのモデル
 *  - mockMethod Mockのメソッド
 *
 * @return array テストデータ
 */
	public function dataProviderDeleteOnExceptionError() {
		$data = $this->dataProviderDelete()[0][0];

		return array(
			array($data, 'Quizzes.Quiz', 'delete'),
			array($data, 'Quizzes.QuizFrameDisplayQuiz', 'deleteAll'),
			array($data, 'Quizzes.QuizAnswerSummary', 'deleteAll'),
		);
	}

}
