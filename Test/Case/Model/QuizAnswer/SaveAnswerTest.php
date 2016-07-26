<?php
/**
 * QuizAnswer::saveAnswer()のテスト
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
 * QuizAnswer::saveAnswer()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\Model\QuizAnswer
 */
class QuizAnswerSaveAnswerTest extends NetCommonsModelTestCase {

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
	protected $_modelName = 'QuizAnswer';

/**
 * Method name
 *
 * @var string
 */
	protected $_methodName = 'saveAnswer';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		Current::$current['Frame']['key'] = 'frame_3';
		$this->QuestionnaireAnswerSummary = ClassRegistry::init(Inflector::camelize($this->plugin) . '.QuestionnaireAnswerSummary');
	}
/**
 * テストDataの取得
 *
 * @param int $pageSeq page sequence
 * @param string $qKey question key
 * @param int $summaryId summary id
 * @param mix $value answer values
 * @param int $answerId answer id
 * @return array
 */
	private function __getData($pageSeq, $qKey, $summaryId, $value, $answerId = '') {
		$data = array(
			'Frame' => array('id' => 6),
			'Block' => array('id' => 2),
			'QuizPage' => array('page_sequence' => $pageSeq),
			'QuizAnswerSummary' => $summaryId,
			'QuizAnswer' => array(
				$qKey => array(
					array(
						'answer_value' => $value,
						'quiz_question_key' => $qKey,
						'id' => $answerId
					)
				),
			),
		);
		return $data;
	}
/**
 * Saveのテスト
 *
 * @param int $answerId answer id
 * @param array $data 回答データ
 * @param array $quiz 小テストデータ
 * @param array $summary サマリデータ
 * @param array $expected 期待値
 * @dataProvider dataProviderSave
 * @return void
 */
	public function testSave($answerId, $data, $quiz, $summary, $expected) {
		$model = $this->_modelName;
		$method = $this->_methodName;

		//テスト実行
		$result = $this->$model->$method($data, $quiz, $summary);
		if (! $answerId) {
			$answerId = $this->$model->getLastInsertId();
		}
		$this->assertTrue($result);
		$answers = $this->$model->findById($answerId);

		$this->assertEqual($answers['QuizAnswer']['answer_value'], $expected);
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
		$dataGet = new QuizDataGetTest();

		$results = array();
		// 択一選択
		$results[] = array(
			null,
			$this->__getData(0, '0984f470eb7a6453b8ed8f9602fa8744', 11, array('新規選択肢1')),
			$dataGet->getData(46),
			array('QuizAnswerSummary' => array('id' => 11)),
			array('新規選択肢1'));
		// 択一選択
		$results[] = array(
			16,
			$this->__getData(0, '0984f470eb7a6453b8ed8f9602fa8744', 11, array('新規選択肢1'), 16),
			$dataGet->getData(46),
			array('QuizAnswerSummary' => array('id' => 11)),
			array('新規選択肢1'));
		// 複数選択
		$results[] = array(
			null,
			$this->__getData(0, '6594db1a6175375e8c64db3288ca4bdb', 18, array('新規選択肢1', '新規選択肢2')),
			$dataGet->getData(47),
			array('QuizAnswerSummary' => array('id' => 18)),
			array('新規選択肢1', '新規選択肢2'));
		// 複数選択
		$results[] = array(
			21,
			$this->__getData(0, '6594db1a6175375e8c64db3288ca4bdb', 18, array('新規選択肢1', '新規選択肢2'), 21),
			$dataGet->getData(47),
			array('QuizAnswerSummary' => array('id' => 18)),
			array('新規選択肢1', '新規選択肢2'));
		// 単語
		$results[] = array(
			null,
			$this->__getData(0, 'd57779bc6eec5710d711881050d825b5', 20, array('aaa')),
			$dataGet->getData(48),
			array('QuizAnswerSummary' => array('id' => 20)),
			array('aaa'));
		// 単語
		$results[] = array(
			23,
			$this->__getData(0, 'd57779bc6eec5710d711881050d825b5', 20, array('aaa'), 23),
			$dataGet->getData(48),
			array('QuizAnswerSummary' => array('id' => 20)),
			array('aaa'));
		// 記述
		$results[] = array(
			null,
			$this->__getData(0, '9cc4e8ba1f575fb349e74c5f958c4a69', 27, array('kijutu')),
			$dataGet->getData(51),
			array('QuizAnswerSummary' => array('id' => 27)),
			array('kijutu'));
		// 単語
		$results[] = array(
			30,
			$this->__getData(0, '9cc4e8ba1f575fb349e74c5f958c4a69', 27, array('kijutu2'), 30),
			$dataGet->getData(51),
			array('QuizAnswerSummary' => array('id' => 27)),
			array('kijutu2'));
		// 複数単語
		$results[] = array(
			null,
			$this->__getData(0, 'ca5816303caf3a27bad5a4754c75c40e', 24, array('eee', 'rrr')),
			$dataGet->getData(50),
			array('QuizAnswerSummary' => array('id' => 24)),
			array('eee', 'rrr'));
		// 複数選択
		$results[] = array(
			27,
			$this->__getData(0, 'ca5816303caf3a27bad5a4754c75c40e', 24, array('yyy', 'uuu'), 27),
			$dataGet->getData(50),
			array('QuizAnswerSummary' => array('id' => 24)),
			array('yyy', 'uuu'));

		return $results;
	}
/**
 * SaveのValidationErrorテスト
 *
 * @param array $data 登録データ
 * @param int $quizId quiz id
 * @param int $summaryId summary id
 * @param string $mockModel Mockのモデル
 * @param string $mockMethod Mockのメソッド
 * @dataProvider dataProviderSaveOnValidationError
 * @return void
 */
	public function testSaveOnValidationError($data, $quizId, $summaryId, $mockModel, $mockMethod = 'validates') {
		$model = $this->_modelName;
		$method = $this->_methodName;
		$dataGet = new QuizDataGetTest();
		$quiz = $dataGet->getData($quizId);
		$summary = array('QuizAnswerSummary' => array('id' => $summaryId));

		$this->_mockForReturnFalse($model, $mockModel, $mockMethod);
		$result = $this->$model->$method($data, $quiz, $summary);
		$this->assertFalse($result);
	}

/**
 * SaveのValidationError用DataProvider
 *
 * ### 戻り値
 *  - data 登録データ
 *  - mockModel Mockのモデル
 *  - mockMethod Mockのメソッド(省略可：デフォルト validates)
 *
 * @return array テストデータ
 */
	public function dataProviderSaveOnValidationError() {
		return array(
			array(
				$this->__getData(0, 'ca5816303caf3a27bad5a4754c75c40e', 24, array('yyy', 'uuu')),
				50,
				24,
				'Quizzes.QuizAnswer'),
		);
	}
/**
 * SaveのExceptionErrorテスト
 *
 * @param array $data 登録データ
 * @param int $quizId quiz id
 * @param int $summaryId summary id
 * @param string $mockModel Mockのモデル
 * @param string $mockMethod Mockのメソッド
 * @dataProvider dataProviderSaveOnExceptionError
 * @return void
 */
	public function testSaveOnExceptionError($data, $quizId, $summaryId, $mockModel, $mockMethod) {
		$model = $this->_modelName;
		$method = $this->_methodName;

		$dataGet = new QuizDataGetTest();
		$quiz = $dataGet->getData($quizId);
		$summary = array('QuizAnswerSummary' => array('id' => $summaryId));

		$this->_mockForReturnFalse($model, $mockModel, $mockMethod);

		$this->setExpectedException('InternalErrorException');
		$this->$model->$method($data, $quiz, $summary);
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
		return array(
			array(
				$this->__getData(0, 'ca5816303caf3a27bad5a4754c75c40e', 24, array('yyy', 'uuu')),
				50,
				24,
				'Quizzes.QuizAnswer', 'save'),
		);
	}
}
