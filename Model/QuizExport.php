<?php
/**
 * QuizExport Model
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */

App::uses('QuizzesAppModel', 'Quizzes.Model');
App::uses('WysiwygZip', 'Wysiwyg.Utility');

/**
 * Summary for Quiz Model
 */
class QuizExport extends QuizzesAppModel {

/**
 * Use table config
 *
 * @var bool
 */
	public $useTable = 'quizzes';

/**
 * use behaviors
 *
 * @var array
 */
	public $actsAs = array(
		'AuthorizationKeys.AuthorizationKey',
	);

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array();

/**
 * Constructor. Binds the model's database table to the object.
 *
 * @param bool|int|string|array $id Set this ID for this model on startup,
 * can also be an array of options, see above.
 * @param string $table Name of database table to use.
 * @param string $ds DataSource connection name.
 * @see Model::__construct()
 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
 */
	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);

		$this->loadModels([
			'Quiz' => 'Quizzes.Quiz',
			'QuizPage' => 'Quizzes.QuizPage',
			'QuizQuestion' => 'Quizzes.QuizQuestion',
		]);
	}

/**
 * getExportData
 *
 * @param string $quizKey 小テストキー
 * @return array QuizData for Export
 */
	public function getExportData($quizKey) {
		// 小テストデータをjsonにして記述した内容を含むZIPファイルを作成する
		$zipData = array();

		// バージョン情報を取得するためComposer情報を得る
		$Plugin = ClassRegistry::init('PluginManager.Plugin');
		$composer = $Plugin->getComposer('netcommons/quizzes');
		// 最初のデータは小テストプラグインのバージョン
		$zipData['version'] = $composer['version'];

		// 言語数分
		$Language = ClassRegistry::init('M17n.Language');
		$languages = $Language->getLanguage();

		$quizzes = array();
		foreach ($languages as $lang) {
			// 指定の小テストデータを取得
			$quiz = $this->Quiz->find('first', array(
				'conditions' => array(
					'Quiz.key' => $quizKey,
					'Quiz.is_active' => true,
					'Quiz.is_latest' => true,
					'Quiz.language_id' => $lang['Language']['id']
				),
				'recursive' => 0
			));
			// 指定の言語データがない場合もあることを想定
			if (empty($quiz)) {
				continue;
			}
			$quiz = Hash::remove($quiz, 'Block');
			$quiz = Hash::remove($quiz, 'TrackableCreator');
			$quiz = Hash::remove($quiz, 'TrackableUpdater');
			$this->clearQuizId($quiz);
			$quizzes[] = $quiz;
		}
		// Exportするデータが一つも見つからないって
		if (empty($quiz)) {
			return false;
		}
		$zipData['Quizzes'] = $quizzes;
		return $zipData;
	}

/**
 * putToZip
 *
 * @param ZipDownloader $zipFile ZIPファイルオブジェクト
 * @param array $zipData zip data
 * @return void
 */
	public function putToZip($zipFile, $zipData) {
		$wysiswyg = new WysiwygZip();

		// 小テストデータの中でもWYSISWYGデータのものについては
		// フォルダ別に確保(フォルダの中にZIPがある）
		$flatQuiz = Hash::flatten($zipData);
		foreach ($flatQuiz as $key => &$value) {
			$model = null;
			if (strpos($key, 'QuizQuestion.') !== false) {
				$model = $this->QuizQuestion;
			} elseif (strpos($key, 'QuizPage.') !== false) {
				$model = $this->QuizPage;
			} elseif (strpos($key, 'Quiz.') !== false) {
				$model = $this->Quiz;
			}
			if (!$model) {
				continue;
			}
			$columnName = substr($key, strrpos($key, '.') + 1);
			if ($model->hasField($columnName)) {
				if ($model->getColumnType($columnName) == 'text') {
					$wysiswygZipFile = $wysiswyg->createWysiwygZip($value, $model->alias . '.' . $columnName);
					$wysiswygFileName = $key . '.zip';
					$zipFile->addFile($wysiswygZipFile, $wysiswygFileName);
					$value = $wysiswygFileName;
				}
			}
		}
		$quiz = Hash::expand($flatQuiz);
		// jsonデータにして書き込み
		$zipFile->addFromString(
			QuizzesComponent::QUIZ_JSON_FILENAME,
			json_encode($quiz));
	}
}
