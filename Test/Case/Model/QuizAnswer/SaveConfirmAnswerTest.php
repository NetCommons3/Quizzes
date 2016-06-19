<?php
/**
 * QuizAnswer::saveConfirmAnswer()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsModelTestCase', 'NetCommons.TestSuite');
App::uses('QuizAnswerFixture', 'Quizzes.Test/Fixture');
App::uses('QuizzesComponent', 'Quizzes.Controller/Component');
App::uses('QuizDataGetTest', 'Quizzes.TestSuite');

/**
 * QuizAnswer::saveConfirmAnswer()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\Model\QuizAnswer
 */
class QuizAnswerSaveConfirmAnswerTest extends NetCommonsModelTestCase {

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
	protected $_methodName = 'saveConfirmAnswer';

/**
 * Saveのテスト
 *
 * @param array $quiz 小テストデータ
 * @param array $summary サマリデータ
 * @param array $expected 期待値
 * @dataProvider dataProviderSave
 * @return void
 */
	public function testSave($quiz, $summary, $expected) {
		$model = $this->_modelName;
		$method = $this->_methodName;

		//テスト実行
		$result = $this->$model->$method($quiz, $summary);
		$this->assertNotEmpty($result);

		$answers = $this->$model->find('all', array(
			'conditions' => array('quiz_answer_summary_id' => $summary['QuizAnswerSummary']['id'])
		));
		$answer = $answers[0];
		// summaryの
		foreach ($expected as $key => $value) {
			if ($key == 'answer_correct_status') {
				$this->assertEquals(
					$value,
					implode(QuizzesComponent::ANSWER_DELIMITER, $answer[$model][$key]));
			} else {
				$this->assertEquals($value, $answer[$model][$key]);
			}
		}
	}

/**
 * Save用DataProvider
 *
 * ### 戻り値
 *  - data 登録データ
 *
 * @return array テストデータ
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 */
	public function dataProviderSave() {
		$dataGet = new QuizDataGetTest();

		$orderFixQuiz = $dataGet->getData(50);
		$orderFixQuiz['QuizPage'][0]['QuizQuestion'][0]['is_order_fixed'] = true;
		$results = array();
		// 択一選択：正解 #0
		$results[] = array(
			$dataGet->getData(46),
			array('QuizAnswerSummary' => array('id' => 11)),
			array('correct_status' => QuizzesComponent::STATUS_GRADE_PASS,
				'score' => 10, 'answer_correct_status' => QuizzesComponent::STATUS_GRADE_PASS));
		// 択一選択：間違い #1
		$results[] = array(
			$dataGet->getData(46),
			array('QuizAnswerSummary' => array('id' => 12)),
			array('correct_status' => QuizzesComponent::STATUS_GRADE_FAIL,
				'score' => 0, 'answer_correct_status' => QuizzesComponent::STATUS_GRADE_FAIL));
		// 択一選択：解答記入なし #2
		$results[] = array(
			$dataGet->getData(46),
			array('QuizAnswerSummary' => array('id' => 13)),
			array('correct_status' => QuizzesComponent::STATUS_GRADE_FAIL,
				'score' => 0, 'answer_correct_status' => QuizzesComponent::STATUS_GRADE_FAIL));
		// 複数選択：正解 #3
		$results[] = array(
			$dataGet->getData(47),
			array('QuizAnswerSummary' => array('id' => 18)),
			array('correct_status' => QuizzesComponent::STATUS_GRADE_PASS,
				'score' => 10, 'answer_correct_status' => QuizzesComponent::STATUS_GRADE_PASS .
					QuizzesComponent::ANSWER_DELIMITER . QuizzesComponent::STATUS_GRADE_PASS));
		// 複数選択：間違い #4
		$results[] = array(
			$dataGet->getData(47),
			array('QuizAnswerSummary' => array('id' => 17)),
			array('correct_status' => QuizzesComponent::STATUS_GRADE_FAIL,
				'score' => 0, 'answer_correct_status' => QuizzesComponent::STATUS_GRADE_PASS .
					QuizzesComponent::ANSWER_DELIMITER . QuizzesComponent::STATUS_GRADE_FAIL));
		// 複数選択：未記入 #5
		$results[] = array(
			$dataGet->getData(47),
			array('QuizAnswerSummary' => array('id' => 15)),
			array('correct_status' => QuizzesComponent::STATUS_GRADE_FAIL,
				'score' => 0, 'answer_correct_status' => QuizzesComponent::STATUS_GRADE_FAIL));

		// 単語：正解 #6
		$results[] = array(
			$dataGet->getData(48),
			array('QuizAnswerSummary' => array('id' => 20)),
			array('correct_status' => QuizzesComponent::STATUS_GRADE_PASS,
				'score' => 10, 'answer_correct_status' => QuizzesComponent::STATUS_GRADE_PASS));
		// 単語：間違い #7
		$results[] = array(
			$dataGet->getData(48),
			array('QuizAnswerSummary' => array('id' => 21)),
			array('correct_status' => QuizzesComponent::STATUS_GRADE_FAIL,
				'score' => 0, 'answer_correct_status' => QuizzesComponent::STATUS_GRADE_FAIL));
		// 単語：解答記入なし #8
		$results[] = array(
			$dataGet->getData(48),
			array('QuizAnswerSummary' => array('id' => 19)),
			array('correct_status' => QuizzesComponent::STATUS_GRADE_FAIL,
				'score' => 0, 'answer_correct_status' => QuizzesComponent::STATUS_GRADE_FAIL));

		// 複数単語：正解 #9
		$results[] = array(
			$dataGet->getData(50),
			array('QuizAnswerSummary' => array('id' => 24)),
			array('correct_status' => QuizzesComponent::STATUS_GRADE_PASS,
				'score' => 10, 'answer_correct_status' =>
					QuizzesComponent::STATUS_GRADE_PASS . QuizzesComponent::ANSWER_DELIMITER .
					QuizzesComponent::STATUS_GRADE_PASS . QuizzesComponent::ANSWER_DELIMITER .
					QuizzesComponent::STATUS_GRADE_PASS));
		// 複数単語：間違い #10
		$results[] = array(
			$dataGet->getData(50),
			array('QuizAnswerSummary' => array('id' => 23)),
			array('correct_status' => QuizzesComponent::STATUS_GRADE_FAIL,
				'score' => 0, 'answer_correct_status' =>
					QuizzesComponent::STATUS_GRADE_PASS . QuizzesComponent::ANSWER_DELIMITER .
					QuizzesComponent::STATUS_GRADE_FAIL . QuizzesComponent::ANSWER_DELIMITER .
					QuizzesComponent::STATUS_GRADE_FAIL));
		// 複数単語：解答記入なし #11
		$results[] = array(
			$dataGet->getData(50),
			array('QuizAnswerSummary' => array('id' => 25)),
			array('correct_status' => QuizzesComponent::STATUS_GRADE_FAIL,
				'score' => 0, 'answer_correct_status' =>
					QuizzesComponent::STATUS_GRADE_FAIL . QuizzesComponent::ANSWER_DELIMITER .
					QuizzesComponent::STATUS_GRADE_FAIL . QuizzesComponent::ANSWER_DELIMITER .
					QuizzesComponent::STATUS_GRADE_FAIL));
		// 複数単語：順番固定：間違い #12
		$results[] = array(
			$orderFixQuiz,
			array('QuizAnswerSummary' => array('id' => 24)),
			array('correct_status' => QuizzesComponent::STATUS_GRADE_FAIL,
				'score' => 10, 'answer_correct_status' =>
				QuizzesComponent::STATUS_GRADE_FAIL . QuizzesComponent::ANSWER_DELIMITER .
				QuizzesComponent::STATUS_GRADE_PASS . QuizzesComponent::ANSWER_DELIMITER .
				QuizzesComponent::STATUS_GRADE_FAIL));
		// 質問データが取れないエラー
		$results[] = array(
			$dataGet->getData(49),
			array('QuizAnswerSummary' => array('id' => 20)),
			array('correct_status' => QuizzesComponent::STATUS_GRADE_PASS,
				'score' => 10, 'answer_correct_status' => QuizzesComponent::STATUS_GRADE_PASS));
		// 記述
		$results[] = array(
			$dataGet->getData(51),
			array('QuizAnswerSummary' => array('id' => 31)),
			array('correct_status' => QuizzesComponent::STATUS_GRADE_YET,
				'score' => 0, 'answer_correct_status' => ''));//記述式は個別正答状態もたない
		return $results;
	}

/**
 * SaveのExceptionErrorテスト
 *
 * @param array $quiz 小テストデータ
 * @param array $summary サマリデータ
 * @param string $mockModel Mockのモデル
 * @param string $mockMethod Mockのメソッド
 * @dataProvider dataProviderSaveOnExceptionError
 * @return void
 */
	public function testSaveOnExceptionError($quiz, $summary, $mockModel, $mockMethod) {
		$model = $this->_modelName;
		$method = $this->_methodName;

		$this->_mockForReturnFalse($model, $mockModel, $mockMethod);

		$this->setExpectedException('InternalErrorException');
		$this->$model->$method($quiz, $summary);
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
		$dataGet = new QuizDataGetTest();

		return array(
			array(
				$dataGet->getData(46),
				array('QuizAnswerSummary' => array('id' => 11)),
				'Quizzes.QuizAnswer', 'save'),
		);
	}

}
