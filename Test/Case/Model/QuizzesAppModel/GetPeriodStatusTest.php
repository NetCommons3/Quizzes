<?php
/**
 * QuizzesAppModel::getPeriodStatus()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsGetTest', 'NetCommons.TestSuite');
App::uses('QuizzesComponent', 'Quizzes.Controller/Component');

/**
 * QuizzesAppModel::getPeriodStatus()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\Model\QuizzesAppModel
 */
class QuizzesAppModelGetPeriodStatusTest extends NetCommonsGetTest {

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
	protected $_modelName = 'Quiz';

/**
 * Method name
 *
 * @var string
 */
	protected $_methodName = 'getPeriodStatus';

/**
 * getPeriodStatus()のテスト
 *
 * @param array $check チェック
 * @param string $startTime 開始時刻
 * @param string $endTime 終了時刻
 * @param mix $expected 期待値
 * @dataProvider dataProvider
 * @return void
 */
	public function testGetPeriodStatus($check, $startTime, $endTime, $expected) {
		$model = $this->_modelName;
		$methodName = $this->_methodName;

		//テスト実施
		$result = $this->$model->$methodName($check, $startTime, $endTime, $expected);

		//チェック
		$this->assertEqual($result, $expected);
	}
/**
 * getPeriodStatus()のテストのデータプロバイダー
 *
 * ### 戻り値
 *  - check: 制限日時チェックをするかしないか
 *  - startTime : 開始時刻
 *  - endTime : 終了時刻
 *  - assert: テストの期待値
 * @return array
 */
	public function dataProvider() {
		return array(
			array(QuizzesComponent::USES_NOT_USE,
				'1900-01-01 00:00:00',
				'1950-01-01 00:00:00',
				QuizzesComponent::QUIZ_PERIOD_STAT_IN),
			array(QuizzesComponent::USES_USE,
				'1900-01-01 00:00:00',
				'1950-01-01 00:00:00',
				QuizzesComponent::QUIZ_PERIOD_STAT_END),
			array(QuizzesComponent::USES_USE,
				'2900-01-01 00:00:00',
				'2950-01-01 00:00:00',
				QuizzesComponent::QUIZ_PERIOD_STAT_BEFORE),
			array(QuizzesComponent::USES_USE,
				'1900-01-01 00:00:00',
				'2950-01-01 00:00:00',
				QuizzesComponent::QUIZ_PERIOD_STAT_IN),
		);
	}

}
