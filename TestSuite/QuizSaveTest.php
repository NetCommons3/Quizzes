<?php
/**
 * QuizSaveTest
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsModelTestCase', 'NetCommons.TestSuite');

/**
 * QuizSaveTest
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\NetCommons\TestSuite
 * @codeCoverageIgnore
 */
abstract class QuizSaveTest extends NetCommonsModelTestCase {

/**
 * Model name
 *
 * @var array
 */
	protected $_modelName = '';

/**
 * Method name
 *
 * @var array
 */
	protected $_methodName = '';

/**
 * Saveのテスト
 *
 * @param array $data 登録データ
 * @dataProvider dataProviderSave
 * @return void
 */
	public function testSave($data) {
		$model = $this->_modelName;
		$method = $this->_methodName;

		//チェック用データ取得
		if (isset($data[$model][0]['id'])) {
			$before = $this->$model->find('first', array(
				'recursive' => -1,
				'conditions' => array('id' => $data[$this->$model->alias][0]['id']),
			));
		}

		//テスト実行
		$result = $this->$model->$method($data[$model]);
		$this->assertNotEmpty($result);

		//idのチェック
		if (isset($data[$this->$model->alias][0]['id'])) {
			$id = $data[$this->$model->alias][0]['id'];
		} else {
			$id = $this->$model->getLastInsertID();
		}

		//登録データ取得
		$actual = $this->$model->find('first', array(
			'recursive' => -1,
			'conditions' => array('id' => $id),
		));

		$actual[$this->$model->alias] = Hash::remove($actual[$this->$model->alias], 'modified');
		$actual[$this->$model->alias] = Hash::remove($actual[$this->$model->alias], 'modified_user');
		// 配列⇔文字列があって一致が見られない
		$actual[$this->$model->alias] = Hash::remove($actual[$this->$model->alias], 'correct');
		if (! isset($data[$this->$model->alias][0]['id'])) {
			$actual[$this->$model->alias] = Hash::remove($actual[$this->$model->alias], 'created');
			$actual[$this->$model->alias] = Hash::remove($actual[$this->$model->alias], 'created_user');

			if ($this->$model->hasField('key') && !isset($data[$this->$model->alias][0]['key'])) {
				$data[$this->$model->alias][0]['key'] =
					OriginalKeyBehavior::generateKey($this->$model->name, $this->$model->useDbConfig);
			}
			$before[$this->$model->alias] = array();
		}

		$expected[$this->$model->alias] = Hash::merge(
			$before[$this->$model->alias],
			$data[$this->$model->alias][0],
			array(
				'id' => $id,
			)
		);
		$expected[$this->$model->alias] = Hash::remove($expected[$this->$model->alias], 'modified');
		$expected[$this->$model->alias] = Hash::remove($expected[$this->$model->alias], 'modified_user');
		$expected = Hash::remove($expected, $this->$model->alias . '.QuizQuestion');
		$expected = Hash::remove($expected, $this->$model->alias . '.QuizChoice');
		$expected = Hash::remove($expected, $this->$model->alias . '.QuizCorrect');
		// 配列⇔文字列があって
		$expected = Hash::remove($expected, $this->$model->alias . '.correct');

		$this->assertEquals($expected[$this->$model->alias], $actual[$this->$model->alias]);
	}

/**
 * SaveのExceptionErrorテスト
 *
 * @param array $data 登録データ
 * @param string $mockModel Mockのモデル
 * @param string $mockMethod Mockのメソッド
 * @dataProvider dataProviderSaveOnExceptionError
 * @return void
 */
	public function testSaveOnExceptionError($data, $mockModel, $mockMethod) {
		$model = $this->_modelName;
		$method = $this->_methodName;

		$this->_mockForReturnFalse($model, $mockModel, $mockMethod);

		$this->setExpectedException('InternalErrorException');
		$this->$model->$method($data);
	}
}
