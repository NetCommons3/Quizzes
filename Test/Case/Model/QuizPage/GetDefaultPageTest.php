<?php
/**
 * QuizPage::getDefaultPage()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsGetTest', 'NetCommons.TestSuite');
App::uses('QuizzesComponent', 'Quizzes.Controller/Component');
App::uses('QuizPage', 'Quizzes.Model');

/**
 * QuizPage::getDefaultPage()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\Model\QuizPage
 */
class QuizPageGetDefaultPageTest extends NetCommonsGetTest {

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
	protected $_modelName = 'QuizPage';

/**
 * Method name
 *
 * @var string
 */
	protected $_methodName = 'getDefaultPage';

/**
 * getDefaultPage()のテスト
 *
 * @param array $expected 期待値（取得したキー情報）
 * @dataProvider dataProviderGet
 * @return void
 */
	public function testGetDefaultPage($expected) {
		$model = $this->_modelName;
		$methodName = $this->_methodName;

		//データ生成

		//テスト実施
		$result = $this->$model->$methodName();
		// Choice, Correctは省く
		$result = Hash::remove($result, 'QuizQuestion');

		//チェック
		$this->assertEquals($result, $expected);
	}
/**
 * getDefaultPageのDataProvider
 *
 * #### 戻り値
 *  - array 取得するキー情報
 *  - array 期待値 （取得したキー情報）
 *
 * @return array
 */
	public function dataProviderGet() {
		$expect = array(
			'page_title' => __d('quizzes', 'First Page'),
			'page_sequence' => 0,
			'key' => '',
			'is_page_description' => QuizzesComponent::USES_NOT_USE,
			'page_description' => '',
		);
		return array(
			array($expect),
		);
	}

}
