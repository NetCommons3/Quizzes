<?php
/**
 * Quiz::saveQuiz()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('WorkflowSaveTest', 'Workflow.TestSuite');
App::uses('QuizzesComponent', 'Quizzes.Controller/Component');
App::uses('QuizDataGetTest', 'Quizzes.TestSuite');

/**
 * Quiz::saveQuiz()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\Model\Quiz
 */
class QuizSaveQuizTest extends WorkflowSaveTest {

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
	protected $_methodName = 'saveQuiz';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$model = $this->_modelName;
		$this->$model->Behaviors->unload('AuthorizationKey');
		Current::$current['Frame']['id'] = '6';
		Current::$current['Frame']['key'] = 'frame_3';
		Current::$current['Frame']['room_id'] = '2';
		Current::$current['Frame']['plugin_key'] = 'quizzes';
		Current::$current['Frame']['language_id'] = '2';
		Current::$current['Plugin']['key'] = 'quizzes';
		$mailQueueMock = $this->getMock('MailQueueBehavior',
			['setAddEmbedTagValue', 'afterSave']);
		$mailQueueMock->expects($this->any())
			->method('setAddEmbedTagValue')
			->will($this->returnValue(true));
		$mailQueueMock->expects($this->any())
			->method('afterSave')
			->will($this->returnValue(true));

		// ClassRegistoryを使ってモックを登録。
		// まずremoveObjectしないとaddObjectできないのでremoveObjectする
		ClassRegistry::removeObject('MailQueueBehavior');
		// addObjectでUploadBehaviorでMockが使われる
		ClassRegistry::addObject('MailQueueBehavior', $mailQueueMock);

		// このloadではモックがロードされる
		$this->$model->Behaviors->load('MailQueue');

		//新着のビヘイビアをモックに差し替え
		$this->$model->Behaviors->unload('Topics');
		$topicsMock = $this->getMock('TopicsBehavior', ['setTopicValue', 'afterSave']);
		$topicsMock->expects($this->any())
			->method('setTopicValue')
			->will($this->returnValue(true));
		$topicsMock->expects($this->any())
			->method('afterSave')
			->will($this->returnValue(true));

		ClassRegistry::removeObject('TopicsBehavior');
		ClassRegistry::addObject('TopicsBehavior', $topicsMock);
		$this->$model->Behaviors->load('Topics');
	}

/**
 * テストDataの取得
 *
 * @param string $id quizId
 * @param string $status
 * @return array
 */
	private function __getData($id = 6, $status = '1') {
		$dataGet = new QuizDataGetTest();
		$data = $dataGet->getData($id);

		$data['Frame']['id'] = 6;
		return $data;
	}

/**
 * Saveのテスト
 *
 * @param array $data 登録データ
 * @dataProvider dataProviderSave
 * @return array 登録後のデータ
 */
	public function testSave($data) {
		$model = $this->_modelName;
		$method = $this->_methodName;

		//チェック用データ取得
		if (isset($data[$this->$model->alias]['id'])) {
			$before = $this->$model->find('first', array(
				'recursive' => -1,
				'conditions' => array('id' => $data[$this->$model->alias]['id']),
			));
			$saveData = Hash::remove($data, $this->$model->alias . '.id');
		} else {
			$saveData = $data;
		}

		//テスト実行
		$result = $this->$model->$method($saveData);
		$this->assertNotEmpty($result);
		$lastInsertId = $this->$model->getLastInsertID();

		//登録データ取得
		$latest = $this->$model->find('first', array(
			'recursive' => -1,
			'conditions' => array('id' => $lastInsertId),
		));

		$actual = $latest;

		//前のレコードのis_latestのチェック
		if (isset($before)) {
			$after = $this->$model->find('first', array(
				'recursive' => -1,
				'conditions' => array('id' => $data[$this->$model->alias]['id']),
			));
			$this->assertFalse($after[$this->$model->alias]['is_latest']);
			$actual[$this->$model->alias] = Hash::remove($actual[$this->$model->alias], 'modified');
			$actual[$this->$model->alias] = Hash::remove($actual[$this->$model->alias], 'modified_user');
		} else {
			$actual[$this->$model->alias] = Hash::remove($actual[$this->$model->alias], 'created');
			$actual[$this->$model->alias] = Hash::remove($actual[$this->$model->alias], 'created_user');
			$actual[$this->$model->alias] = Hash::remove($actual[$this->$model->alias], 'modified');
			$actual[$this->$model->alias] = Hash::remove($actual[$this->$model->alias], 'modified_user');

			$data[$this->$model->alias]['key'] = OriginalKeyBehavior::generateKey($this->$model->name, $this->$model->useDbConfig);
			$before[$this->$model->alias] = array();
		}
		// afterFindでDBテーブル構造以外のものがくっついてくるので
		$actual = Hash::remove($actual, 'QuizPage');

		$expected[$this->$model->alias] = Hash::merge(
			$before[$this->$model->alias],
			$data[$this->$model->alias],
			array(
				'id' => $lastInsertId,
				'is_active' => true,
				'is_latest' => true
			)
		);
		$expected[$this->$model->alias] = Hash::remove($expected[$this->$model->alias], 'modified');
		$expected[$this->$model->alias] = Hash::remove($expected[$this->$model->alias], 'modified_user');

		$this->assertEquals($expected, $actual);

		return $latest;
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
		$data = $this->__getData(6);
		$data['Quiz']['status'] = '1';

		$results = array();
		// * 編集の登録処理
		$results[0] = array($data);

		return $results;
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
		$data = $this->dataProviderSave()[0][0];

		return array(
			array($data, 'Quizzes.Quiz', 'save'),
			array($data, 'Quizzes.QuizPage', 'saveQuizPage'),
			array($data, 'Quizzes.QuizFrameDisplayQuiz', 'saveDisplayQuiz'),
		);
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
		$data = $this->dataProviderSave()[0][0];

		return array(
			array($data, 'Quizzes.Quiz'),
		);
	}

}
