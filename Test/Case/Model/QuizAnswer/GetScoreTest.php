<?php
/**
 * QuizAnswer::getScore()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsGetTest', 'NetCommons.TestSuite');
App::uses('QuizzesComponent', 'Quizzes.Controller/Component');
App::uses('QuizDataGetTest', 'Quizzes.TestSuite');

/**
 * QuizAnswer::getScore()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\Model\QuizAnswer
 */
class QuizAnswerGetScoreTest extends NetCommonsGetTest {

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
	protected $_modelName = 'QuizAnswer';

/**
 * Method name
 *
 * @var string
 */
	protected $_methodName = 'getScore';

/**
 * getScore()のテスト
 *
 * @param array $quiz 小テストデータ
 * @param int $summaryId サマリID
 * @param int $ungraded 期待値1
 * @param int $graded 期待値2
 * @dataProvider dataProviderGet
 * @return void
 */
	public function testGetScore($quiz, $summaryId, $ungraded, $graded) {
		$model = $this->_modelName;
		$methodName = $this->_methodName;

		//テスト実施
		$result = $this->$model->$methodName($quiz, $summaryId);
		$this->assertEqual($result['ungraded'], $ungraded);
		$this->assertEqual($result['graded'], $graded);
	}
/**
 * Save用DataProvider
 *
 * ### 戻り値
 *  - data 登録データ
 *
 * @return array テストデータ
 */
	public function dataProviderGet() {
		//データ生成
		$dataGet = new QuizDataGetTest();
		return array(
			// 正解
			array(
				'quiz' => $dataGet->getData(46),
				'summaryId' => 11,
				'ungraded' => 0,
				'graded' => 10
			),
			// 正解部分点
			array(
				'quiz' => $dataGet->getData(51),
				'summaryId' => 27,
				'ungraded' => 0,
				'graded' => 8
			),
			// 不正解部分点
			array(
				'quiz' => $dataGet->getData(51),
				'summaryId' => 29,
				'ungraded' => 0,
				'graded' => 2
			),
			// 未採点
			array(
				'quiz' => $dataGet->getData(51),
				'summaryId' => 31,
				'ungraded' => 10,
				'graded' => 0
			),
		);
	}

}
