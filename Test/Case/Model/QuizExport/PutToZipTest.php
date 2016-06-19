<?php
/**
 * QuizExport::putToZip()のテスト
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
App::uses('ZipDownloader', 'TestFiles.Utility');
App::uses('QuizDataGetTest', 'Quizzes.TestSuite');

/**
 * QuizExport::putToZip()のテスト
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\Model\QuizExport
 */
class QuizExportPutToZipTest extends NetCommonsGetTest {

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
		'plugin.quizzes.quiz_setting',
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
	protected $_methodName = 'putToZip';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		//テストプラグインのロード
		NetCommonsCakeTestCase::loadTestPlugin($this, 'Quizzes', 'TestFiles');
	}

/**
 * putToZip
 *
 * @param string $quizKey 収集対象のアンケートキー
 * @param array $expected 期待値（取得したキー情報）
 * @dataProvider dataProviderGet
 *
 * @return void
 */
	public function testPutToZip($quizKey, $expected) {
		$model = $this->_modelName;
		$method = $this->_methodName;

		App::uses('ZipDownloader', 'TestFiles.Utility');

		$langCount = 1;	// 1 = 言語数 FUJI 当面日本語のみ
		$quizId = intval($expected['quizId']);

		$data = $this->$model->getExportData($quizKey);
		$zipFile = new ZipDownloader();
		//テスト実行
		$this->$model->$method($zipFile, $data);

		//チェック
		$addFiles = Hash::expand(array_flip($zipFile->addFiles));

		// ページ先頭質問文が言語数×ページ数文あるかチェック
		$records = Hash::extract($addFiles, 'Quizzes.{n}.QuizPage.{n}.page_description.zip');
		$this->assertEqual(count($records), $langCount * $expected['pageCount']);
		// 質問文が言語数×質問数文あるかチェック
		$records = Hash::extract($addFiles, 'Quizzes.{n}.QuizPage.{n}.QuizQuestion.{n}.question_value.zip');
		$this->assertEqual(count($records), $langCount * $expected['questionCount']);
		// 解説文が言語数×質問数文あるかチェック
		$records = Hash::extract($addFiles, 'Quizzes.{n}.QuizPage.{n}.QuizQuestion.{n}.commentary.zip');
		$this->assertEqual(count($records), $langCount * $expected['questionCount']);

		// ZIPファイルに追加されたJsonコードはアンケートの構造と同じか
		$jsonQuiz = json_decode($zipFile->addStrings[QuizzesComponent::QUIZ_JSON_FILENAME], true);
		$dataGet = new QuizDataGetTest();
		$orgQuiz = $dataGet->getData($quizId);
		$this->assertTrue($this->_hasSameArray($orgQuiz, $jsonQuiz['Quizzes'][0]));
	}
/**
 * _hasSameArray
 *
 * @param array $part 期待値
 * @param array $hole 実際のデータ
 * @return bool
 */
	protected function _hasSameArray($part, $hole) {
		$flatPart = Hash::flatten($part);
		$flatHole = Hash::flatten($hole);
		foreach ($flatPart as $key => $val) {
			if (preg_match('/\.(id|key|question_value|question_value|commentary|page_description|created_user|created|modified_user|modified)$/', $key) == 1) {
				continue;
			}
			if (array_key_exists($key, $flatHole)) {
				$find = $flatHole[$key];
				if ($find != $val) {
					return false;
				}
			} else {
				return false;
			}
		}
		return true;
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
		return array(
			// アンケートキー,ページ数,質問数
			array('acc5e94c9617ed332cc2ef4d013ae686', array(
				'pageCount' => 3,
				'questionCount' => 3,
				'quizId' => 15)),
		);
	}
}
