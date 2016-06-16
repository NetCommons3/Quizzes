<?php
/**
 * Quiz::beforeAfterFrameSave()とafterAfterFrameSave()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsModelTestCase', 'NetCommons.TestSuite');
App::uses('QuizFixture', 'Quizzes.Test/Fixture');
App::uses('QuizzesComponent', 'Quizzes.Controller/Component');
App::uses('QuizDataGetTest', 'Quizzes.TestSuite');

/**
 * Quiz::beforeAfterFrameSave()とafterAfterFrameSave()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\Model\Quiz
 */
class QuizAfterFrameSaveTest extends NetCommonsModelTestCase {

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
	protected $_methodName = 'afterFrameSave';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		$this->Frame = ClassRegistry::init('Frames' . '.' . 'Frame');
		$this->Block = ClassRegistry::init('Blocks' . '.' . 'Block');
		$this->QuizSetting = ClassRegistry::init('Quizzes' . '.' . 'QuizSetting');
		$this->QuizFrameSetting = ClassRegistry::init('Quizzes' . '.' . 'QuizFrameSetting');
	}

/**
 * テストDataの取得
 *
 * @param string $frameId frame id
 * @param string $blockId block id
 * @param string $roomId room id
 * @return array
 */
	private function __getData($frameId, $blockId, $roomId) {
		$data = array();
		$data['Frame']['id'] = $frameId;
		$data['Frame']['block_id'] = $blockId;
		$data['Frame']['language_id'] = 2;
		$data['Frame']['room_id'] = $roomId;
		$data['Frame']['plugin_key'] = 'quizzes';

		return $data;
	}

/**
 * afterFrameSaveのテスト
 *
 * @param array $data 登録データ
 * @dataProvider dataProviderSave
 * @return void
 */
	public function testSave($data) {
		$model = $this->_modelName;
		$method = $this->_methodName;

		//テスト実行
		$result = $this->$model->$method($data);
		$this->assertNotEmpty($result);

		//登録データ取得
		$actual = $this->Frame->find('first', array(
			'recursive' => -1,
			'conditions' => array('id' => $data['Frame']['id']),
		));
		$actualBlockId = $actual['Frame']['block_id'];
		// block_idが設定されていて
		$this->assertNotEmpty($actualBlockId);

		$block = $this->Block->find('first', array(
			'recursive' => -1,
			'conditions' => array('id' => $actualBlockId),
		));
		$this->assertNotEmpty($block);

		//そのブロックは小テストのもので
		$this->assertTextEquals($block['Block']['plugin_key'], 'quizzes');

		$actualBlockKey = $block['Block']['key'];

		// 小テストのフレーム設定情報もできていること
		$setting = $this->QuizSetting->find('first', array(
			'recursive' => -1,
			'conditions' => array('block_key' => $actualBlockKey),
		));
		$this->assertNotEmpty($setting);
	}

/**
 * SaveのDataProvider
 *
 * ### 戻り値
 *  - data 登録データ
 *
 * @return void
 */
	public function dataProviderSave() {
		return array(
			array($this->__getData(6, 2, 1)), //
			array($this->__getData(14, null, 1)), //
			array($this->__getData(16, null, 4)), //
		);
	}
}
