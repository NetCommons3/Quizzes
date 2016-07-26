<?php
/**
 * QuizExport::getExportData()のテスト
 *
 * @property QuizExport $QuizExport
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsGetTest', 'NetCommons.TestSuite');
App::uses('QuizzesComponent', 'Quizzes.Controller/Component');

/**
 * QuizExport::getExportData()のテスト
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\Model\QuizExport
 */
class QuizExportGetExportDataTest extends NetCommonsGetTest {

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
		'plugin.m17n.language',
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
 * Model name
 *
 * @var array
 */
	protected $_modelName = 'QuizExport';

/**
 * Method name
 *
 * @var array
 */
	protected $_methodName = 'getExportData';

/**
 * getExportData
 *
 * @param string $quizKey 収集対象のアンケートキー
 * @param array $expected 期待値（取得したキー情報）
 * @dataProvider dataProviderGet
 *
 * @return void
 */
	public function testGetExportData($quizKey, $expected) {
		$model = $this->_modelName;
		$method = $this->_methodName;

		//テスト実行
		$result = $this->$model->$method($quizKey);

		//チェック
		if (is_bool($expected)) {
			$this->assertEquals($result, $expected);
		} else {
			foreach ($expected as $expect) {
				$this->assertTrue(Hash::check($result, $expect), $expect . ' is not found');
			}
		}
	}

/**
 * getExportDataのDataProvider
 *
 * #### 戻り値
 *  - array 取得するキー情報
 *  - array 期待値 （取得したキー情報）
 *
 * @return array
 */
	public function dataProviderGet() {
		$expect = array(
			//'version', travis ではここがうまくかない FUJI
			// 2016.08段階では英語データ未作成　FUJI
			//'Quizzes.{n}.Quiz[language_id=1]',
			'Quizzes.{n}.Quiz[language_id=2]',
		);
		return array(
			array('quiz_error', false),
			array('acc5e94c9617ed332cc2ef4d013ae686', $expect),
		);
	}
}
