<?php
/**
 * QuizAnswerSummaryCsv::getAnswerSummaryCsv()のテスト
 *
 * @property QuizAnswerSummaryCsv $QuizAnswerSummaryCsv
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsGetTest', 'NetCommons.TestSuite');
App::uses('QuizzesComponent', 'Quizzes.Controller/Component');
App::uses('QuizDataGetTest', 'Quizzes.TestSuite');

/**
 * QuizAnswerSummaryCsv::getAnswerSummaryCsv()のテスト
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\Model\QuizAnswerSummaryCsv
 */
class GetAnswerSummaryCsvTest extends NetCommonsGetTest {

/**
 * Plugin name
 *
 * @var array
 */
	public $plugin = 'quizzes';

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
 * Model name
 *
 * @var array
 */
	protected $_modelName = 'QuizAnswerSummaryCsv';

/**
 * Method name
 *
 * @var array
 */
	protected $_methodName = 'getAnswerSummaryCsv';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->QuizAnswerSummary = ClassRegistry::init('Quizzes.QuizAnswerSummary');
		$this->QuizAnswerSummary->Behaviors->unload('Mails.MailQueue');
	}

/**
 * getAnswerSummaryCsv
 *
 * @param int $quizId quiz id
 * @param array $expected 期待値（取得したキー情報）
 * @dataProvider dataProviderGet
 *
 * @return void
 */
	public function testGetAnswerSummaryCsv($quizId, $expected) {
		$model = $this->_modelName;
		$method = $this->_methodName;
		$dataGet = new QuizDataGetTest();
		$quiz = $dataGet->getData($quizId);
		$dataCount = 0;

		//テスト実行
		$result = $this->$model->$method($quiz, 1000, 0, $dataCount);
		//チェック
		$this->assertEquals($expected, $result);
	}
/**
 * getDefaultChoiceのDataProvider
 *
 * #### 戻り値
 *  - array 取得するキー情報
 *  - array 期待値 （取得したキー情報）
 *
 * @return array
 */
	public function dataProviderGet() {
		$expect = array(
			array('#####問題 1：<p>新規問題1</p>', '', '', '', '', '', '', ''),
			array(
				__d('quizzes', 'Answer\'s'),
				__d('quizzes', 'Date'),
				__d('quizzes', 'Elapsed'),
				__d('quizzes', 'Number'),
				__d('quizzes', 'Score'),
				__d('quizzes', 'Deviation'),
				'問題 1：',
				__d('quizzes', 'Score')
			),	// header
			array(
				'非会員', '2016-06-10 06:35:02', '10', '1', '0', '40', '', __d('quizzes', 'Ungraded'),
			),	// data1
			array(
				'非会員', '2016-06-10 06:36:46', '77', '1', '8', '59', '正解で部分点でお願いします', 8,
			),	// data2
			array(
				'非会員', '2016-06-10 06:37:26', '24', '1', '10', '64', '正解で満点でお願いします', 10,
			),	// data3
			array(
				'非会員', '2016-06-10 06:38:17', '30', '1', '2', '45', '間違い部分点でお願いします', 2,
			),	// data4
			array(
				'非会員', '2016-06-10 06:39:14', '35', '1', '0', '40', '間違いで０点でお願いします', 0,
			),	// data5
			array(
				'Editor', '2016-06-10 06:47:11', '27', '1', '0', '40', '未採点のままにしてください', __d('quizzes', 'Ungraded'),
			),	// data6
			array(
				'Editor', '2016-06-10 06:47:56', '26', '2', '10', '64', '正解満点でお願いします', 10,
			),	// data6
			array(
				'Editor', '2016-06-10 06:48:55', '27', '3', '4', '49', '間違い部分点でお願いします', 4,
			),	// data6
		);
		$expect2 = array(
			array('#####ページ先頭の問題文章', '', '', '', '', '', '', ''),
			array('#####問題 1：<p>新規問題1</p>', '', '', '', '', '', '', ''),
			array(
				__d('quizzes', 'Answer\'s'),
				__d('quizzes', 'Date'),
				__d('quizzes', 'Elapsed'),
				__d('quizzes', 'Number'),
				__d('quizzes', 'Score'),
				__d('quizzes', 'Deviation'),
				'問題 1：',
				__d('quizzes', 'Score')
			),	// header
		);
		$expect3 = array(
			array('#####問題 1：<p>新規問題1</p>', '', '', '', '', '', '', ''),
			array(
				__d('quizzes', 'Answer\'s'),
				__d('quizzes', 'Date'),
				__d('quizzes', 'Elapsed'),
				__d('quizzes', 'Number'),
				__d('quizzes', 'Score'),
				__d('quizzes', 'Deviation'),
				'問題 1：',
				__d('quizzes', 'Score')
			),	// header
			array(
				'非会員', '2016-06-10 06:20:53', '14', '1', '10', '50.0', '新規選択肢1', 10,
			),	// data2
		);
		return array(
			array('51', $expect),
			array('6', $expect2),
			array('12', $expect3),
		);
	}

}