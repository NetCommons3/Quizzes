<?php
/**
 * QuizAnswerCorrectHelper::getCorrect()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsHelperTestCase', 'NetCommons.TestSuite');
App::uses('QuizzesComponent', 'Quizzes.Controller/Component');

/**
 * QuizAnswerCorrectHelper::getCorrect()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\View\Helper\QuizAnswerCorrectHelper
 */
class QuizAnswerCorrectHelperGetCorrectTest extends NetCommonsHelperTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array();

/**
 * Plugin name
 *
 * @var string
 */
	public $plugin = 'quizzes';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		//テストデータ生成
		//必要に応じてセットする
		$viewVars = array();
		$requestData = array();
		$params = array();

		//Helperロード
		$this->loadHelper('Quizzes.QuizAnswerCorrect', $viewVars, $requestData, $params);
	}

/**
 * getCorrect()のテスト
 *
 * @return void
 */
	public function testGetCorrect() {
		//データ生成
		$questions[QuizzesComponent::TYPE_TEXT_AREA] = array(
			'question_type' => QuizzesComponent::TYPE_TEXT_AREA
		);
		$questions[QuizzesComponent::TYPE_SELECTION] = array(
			'question_type' => QuizzesComponent::TYPE_SELECTION,
			'commentary' => '解説です',
			'QuizCorrect' => array(
				array(
					'correct' => array('正解ですよ')
				)
			),
		);
		$questions[QuizzesComponent::TYPE_MULTIPLE_SELECTION] = array(
			'question_type' => QuizzesComponent::TYPE_MULTIPLE_SELECTION,
			'commentary' => '',
			'QuizCorrect' => array(
				array(
					'correct' => array('正解1', '正解2')
				)
			),
		);
		$questions[QuizzesComponent::TYPE_WORD] = array(
			'question_type' => QuizzesComponent::TYPE_WORD,
			'commentary' => '',
			'QuizCorrect' => array(
				array(
					'correct' => array('正解1', '正解2')
				)
			),
		);
		$questions[QuizzesComponent::TYPE_MULTIPLE_WORD] = array(
			'question_type' => QuizzesComponent::TYPE_MULTIPLE_WORD,
			'commentary' => '',
			'QuizCorrect' => array(
				array(
					'correct' => array('正解1', '正解2'),
					'correct_label' => '(1)'
				),
				array(
					'correct' => array('他の1', '他の2'),
					'correct_label' => '(2)'
				)
			),
		);
		$answers[QuizzesComponent::TYPE_TEXT_AREA] = null;
		$answers[QuizzesComponent::TYPE_SELECTION] = null;
		$answers[QuizzesComponent::TYPE_MULTIPLE_SELECTION] = null;
		$answers[QuizzesComponent::TYPE_WORD] = null;
		$answers[QuizzesComponent::TYPE_MULTIPLE_WORD] = null;

		//テスト実施
		// テキストエリアの場合
		$result = $this->_testGetCorrect(QuizzesComponent::TYPE_TEXT_AREA, $questions, $answers);
		$this->assertEmpty($result);

		// 択一選択の場合
		$result = $this->_testGetCorrect(QuizzesComponent::TYPE_SELECTION, $questions, $answers);
		$this->assertTextEquals($result,
			'<dt class="quiz-grading-correct bg-success">正解</dt>' .
			'<dd class="quiz-grading-correct bg-success">正解ですよ</dd>' .
			'<dt class="quiz-grading-correct bg-success">解説</dt>' .
			'<dd class="quiz-grading-correct bg-success">解説です</dd>');

		$result = $this->_testGetCorrect(QuizzesComponent::TYPE_MULTIPLE_SELECTION, $questions, $answers);
		$this->assertTextEquals($result,
			'<dt class="quiz-grading-correct bg-success">正解</dt>' .
			'<dd class="quiz-grading-correct bg-success">正解1,正解2</dd>');

		$result = $this->_testGetCorrect(QuizzesComponent::TYPE_WORD, $questions, $answers);
		$this->assertTextEquals($result,
			'<dt class="quiz-grading-correct bg-success">正解</dt>' .
			'<dd class="quiz-grading-correct bg-success">正解1 ' .
			'<button type="button" class="btn btn-default btn-sm" ' .
			'popover-placement="right" uib-popover="正解2,">他に認められる解答</button></dd>');

		$result = $this->_testGetCorrect(QuizzesComponent::TYPE_MULTIPLE_WORD, $questions, $answers);
		$this->assertTextEquals($result,
			'<dt class="quiz-grading-correct bg-success">正解</dt>' .
			'<dd class="quiz-grading-correct bg-success">(1) : 正解1 ' .
			'<button type="button" class="btn btn-default btn-sm" ' .
			'popover-placement="right" uib-popover="正解2,">他に認められる解答</button><br />' .
			'(2) : 他の1 <button type="button" class="btn btn-default btn-sm" ' .
			'popover-placement="right" uib-popover="他の2,">他に認められる解答</button><br /></dd>');
	}

/**
 * _testGetCorrect
 *
 * @param int $type 質問タイプ
 * @param array $questions 質問データ
 * @param array $answers 回答データ
 * @return mixed
 */
	protected function _testGetCorrect($type, $questions, $answers) {
		$result = $this->QuizAnswerCorrect->getCorrect($questions[$type], $answers[$type]);
		return $result;
	}
}
