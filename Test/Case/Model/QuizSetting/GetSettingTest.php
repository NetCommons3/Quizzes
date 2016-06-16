<?php
/**
 * QuizSetting::getSetting()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsGetTest', 'NetCommons.TestSuite');

/**
 * QuizSetting::getSetting()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\Model\QuizSetting
 */
class QuizSettingGetSettingTest extends NetCommonsGetTest {

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
	protected $_modelName = 'QuizSetting';

/**
 * Method name
 *
 * @var string
 */
	protected $_methodName = 'getSetting';

/**
 * getSetting()のテスト
 *
 * @param int $blockId ブロックID
 * @param array $expected 期待値（取得したキー情報）
 * @dataProvider dataProviderGet
 * @return void
 */
	public function testGetSetting($blockId, $expected) {
		$model = $this->_modelName;
		$methodName = $this->_methodName;

		//データ生成
		Current::$current['Block']['id'] = $blockId;

		//テスト実施
		$result = $this->$model->$methodName();
		$result = Hash::flatten($result);

		$expected = Hash::flatten($expected);
		//チェック
		foreach ($expected as $key => $value) {
			$this->assertEqual($result[$key], $value);
		}
	}
/**
 * testGetSettingのDataProvider
 *
 * #### 戻り値
 *  - array 取得するキー情報
 *  - array 期待値 （取得したキー情報）
 *
 * @return array
 */
	public function dataProviderGet() {
		return array(
			array(2, array(
				'Block' => array(
					'id' => 2
				),
				'QuizSetting' => array(
					'use_workflow' => true
				)
			)),
			array(999, array(
			)),
		);
	}

}
