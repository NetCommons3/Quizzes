<?php
/**
 * Quiz::saveExportKey()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsModelTestCase', 'NetCommons.TestSuite');
App::uses('QuizzesComponent', 'Quizzes.Controller/Component');

/**
 * Quiz::saveExportKey()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\Model\Quiz
 */
class QuizSaveExportKeyTest extends NetCommonsModelTestCase {

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
	protected $_methodName = 'saveExportKey';

/**
 * saveExportKey()のテスト
 *
 * @return void
 */
	public function testSaveExportKey() {
		$model = $this->_modelName;
		$methodName = $this->_methodName;

		//データ生成
		$quizId = 6;
		$exportKey = 'testExportKey';
		//登録データ取得
		$before = $this->$model->find('first', array(
			'recursive' => -1,
			'conditions' => array('id' => $quizId),
		));

		//テスト実施
		$result = $this->$model->$methodName($quizId, $exportKey);

		//チェック
		$this->assertNotEmpty($result);

		//登録データ取得
		$actual = $this->$model->find('first', array(
			'recursive' => -1,
			'conditions' => array('id' => $quizId),
		));

		$this->assertNotEquals($before[$model]['export_key'], $actual[$model]['export_key']);
		$this->assertEqual('testExportKey', $actual[$model]['export_key']);
	}
/**
 * SaveのExceptionErrorテスト
 *
 * @return void
 */
	public function testSaveOnExceptionError() {
		$model = $this->_modelName;
		$method = $this->_methodName;

		$this->_mockForReturnFalse($model, $model, 'saveField');
		$this->setExpectedException('InternalErrorException');
		//テスト実行
		$quizId = 6;
		$this->$model->$method($quizId, 'testExportKey');
	}

}
