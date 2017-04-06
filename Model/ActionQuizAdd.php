<?php
/**
 * ActionQuizAdd Model
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */

App::uses('QuizzesAppModel', 'Quizzes.Model');
App::uses('TemporaryUploadFile', 'Files.Utility');
App::uses('UnZip', 'Files.Utility');
App::uses('WysiwygZip', 'Wysiwyg.Utility');

/**
 * Summary for ActionQuizAdd Model
 */
class ActionQuizAdd extends QuizzesAppModel {

/**
 * Use table config
 *
 * @var bool
 */
	public $useTable = 'quizzes';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
	);

/**
 * Called during validation operations, before validation. Please note that custom
 * validation rules can be defined in $validate.
 *
 * @param array $options Options passed from Model::save().
 * @return bool True if validate operation should continue, false to abort
 * @link http://book.cakephp.org/2.0/en/models/callback-methods.html#beforevalidate
 * @see Model::save()
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 */
	public function beforeValidate($options = array()) {
		$this->validate = Hash::merge($this->validate, array(
			'create_option' => array(
				'rule' => array(
					'inList', array(
						QuizzesComponent::QUIZ_CREATE_OPT_NEW,
						QuizzesComponent::QUIZ_CREATE_OPT_REUSE,
						QuizzesComponent::QUIZ_CREATE_OPT_TEMPLATE)),
				'message' => __d('quizzes', 'Please choose create option.'),
				'required' => true
			),
			'title' => array(
				'rule' => array(
					'requireWhen', 'create_option', QuizzesComponent::QUIZ_CREATE_OPT_NEW),
				'message' => __d('net_commons', 'Please input %s.', __d('quizzes', 'Title')),
				'required' => false,
			),
			'past_quiz_id' => array(
				'requireWhen' => array(
					'rule' => array('requireWhen', 'create_option', QuizzesComponent::QUIZ_CREATE_OPT_REUSE),
					'message' => __d('quizzes', 'Please select past quiz.'),
				),
				'checkPastQuiz' => array(
					'rule' => array('checkPastQuiz'),
					'message' => __d('quizzes', 'Please select past quiz.'),
				),
			),
		));

		return parent::beforeValidate($options);
	}

/**
 * createQuiz
 * 小テストデータを作成する
 *
 * @param array $data 作成する小テストデータ
 * @return array|bool
 */
	public function createQuiz($data) {
		// 渡されたQuizデータを自Modelデータとする
		$this->set($data);
		// データチェック
		if ($this->validates()) {
			// Postデータの内容に問題がない場合は、そのデータをもとに新しい小テストデータを作成
			$quiz = $this->getNewQuiz();
			return $quiz;
		} else {
			return false;
		}
	}

/**
 * requireWhen
 *
 * @param mixed $check チェック対象入力データ
 * @param string $sourceField チェック対象フィールド名
 * @param mix $sourceValue チェック値
 * @return bool
 */
	public function requireWhen($check, $sourceField, $sourceValue) {
		// チェックすべきかどうかの判定データが、指定の状態かチェック
		if ($this->data['ActionQuizAdd'][$sourceField] != $sourceValue) {
			// 指定状態でなければ問題なし
			return true;
		}
		// 指定の状態であれば、チェック対象データがちゃんと入っているか確認する
		// Validation::notBlank($check);
		if (! array_shift($check)) {
			// 指定のデータが指定の値になっている場合は、このデータ空っぽの場合はエラー
			return false;
		}
		return true;
	}

/**
 * checkPastQuiz
 *
 * @param mix $check チェック対象入力データ
 * @return bool
 */
	public function checkPastQuiz($check) {
		if ($this->data['ActionQuizAdd']['create_option'] != QuizzesComponent::QUIZ_CREATE_OPT_REUSE) {
			return true;
		}
		$this->Quiz = ClassRegistry::init('Quizzes.Quiz', true);
		$baseCondition = $this->Quiz->getBaseCondition(array(
			'Quiz.id' => $check['past_quiz_id']
		));
		$cnt = $this->Quiz->find('count', array(
			'conditions' => $baseCondition,
			'recursive' => -1
		));
		if ($cnt == 0) {
			return false;
		}
		return true;
	}

/**
 * getNewQuiz
 *
 * @return array
 */
	public function getNewQuiz() {
		$this->Quiz = ClassRegistry::init('Quizzes.Quiz', true);
		$this->QuizPage = ClassRegistry::init('Quizzes.QuizPage', true);
		$this->QuizQuestion = ClassRegistry::init('Quizzes.QuizQuestion', true);
		$createOption = $this->data['ActionQuizAdd']['create_option'];

		// 指定された作成のオプションによって処理分岐
		if ($createOption == QuizzesComponent::QUIZ_CREATE_OPT_NEW) {
			// 空の新規作成
			$quiz = $this->_createNew();
		} elseif ($createOption == QuizzesComponent::QUIZ_CREATE_OPT_REUSE) {
			// 過去データからの作成
			$quiz = $this->_createFromReuse();
		} elseif ($createOption == QuizzesComponent::QUIZ_CREATE_OPT_TEMPLATE) {
			// テンプレートファイルからの作成
			$quiz = $this->_createFromTemplate();
		}
		return $quiz;
	}

/**
 * _createNew
 *
 * @return array QuizData
 */
	protected function _createNew() {
		// 小テストデータを新規に作成する
		// 新規作成の場合、タイトル文字のみ画面で設定されPOSTされる
		// Titleをもとに、小テストデータ基本構成を作成し返す

		// デフォルトデータをもとに新規作成
		$quiz = $this->_getDefaultQuiz(array(
			'title' => $this->data['ActionQuizAdd']['title']));
		// 小テストデータを返す
		return $quiz;
	}

/**
 * _createFromReuse
 *
 * @return array QuizData
 */
	protected function _createFromReuse() {
		// 小テストデータを過去の小テストデータをもとにして作成する
		// 過去からの作成の場合、参考にする過去の小テストのidのみPOSTされてくる
		// (orgin_idではなくidである点に注意！)
		// idをもとに、過去の小テストデータを取得し、
		// そのデータから今回作成する小テストデータ基本構成を作成し返す

		// 過去の小テストのコピー・クローンで作成
		$quiz = $this->_getQuizCloneById($this->data['ActionQuizAdd']['past_quiz_id']);
		return $quiz;
	}

/**
 * _getDefaultQuiz
 * get default data of quizzes
 *
 * @param array $addData add data to Default data
 * @return array
 */
	protected function _getDefaultQuiz($addData) {
		$quiz = array();
		$quiz['Quiz'] = Hash::merge(
			array(
				'block_id' => Current::read('Block.id'),
				'title' => '',
				'key' => '',
				'status' => WorkflowComponent::STATUS_IN_DRAFT,
				'passing_grade' => 0,
				'estimated_time' => 0,
				'answer_timing' => QuizzesComponent::USES_NOT_USE,
				'is_no_member_allow' => QuizzesComponent::USES_NOT_USE,
				'is_key_pass_use' => QuizzesComponent::USES_NOT_USE,
				'is_image_authentication' => QuizzesComponent::USES_NOT_USE,
				'is_repeat_allow' => QuizzesComponent::USES_NOT_USE,
				'is_repeat_until_passing' => QuizzesComponent::USES_NOT_USE,
				'is_page_random' => QuizzesComponent::USES_NOT_USE,
				'perfect_score' => 0,
				'is_correct_show' => QuizzesComponent::USES_USE,
				'is_total_show' => QuizzesComponent::USES_USE,
			),
			$addData);

		$quiz['QuizPage'][0] = $this->QuizPage->getDefaultPage($quiz);
		return $quiz;
	}

/**
 * _getQuizCloneById 指定されたIDにの小テストデータのクローンを取得する
 *
 * @param int $quizId 小テストID(編集なのでoriginではなくRAWなIDのほう
 * @return array
 */
	protected function _getQuizCloneById($quizId) {
		// 前もってValidate処理で存在確認されている場合しか
		// この関数が呼ばれないので$quizの判断は不要
		$quiz = $this->Quiz->find('first', array(
			'conditions' => array('Quiz.id' => $quizId),
		));
		// ID値のみクリア
		$this->clearQuizId($quiz);

		return $quiz;
	}

/**
 * _createFromTemplate
 *
 * @return array QuizData
 */
	protected function _createFromTemplate() {
		// 小テストデータをUPLOADされた小テストテンプレートファイルのデータをもとにして作成する
		// テンプレートからの作成の場合、テンプレートファイルがUPLOADされてくる
		// アップされたファイルをもとに、小テストデータを解凍、取得し、
		// そのデータから今回作成する小テストデータ基本構成を作成し返す
		if (empty($this->data['ActionQuizAdd']['template_file']['name'])) {
			$this->validationErrors['template_file'][] =
				__d('quizzes', 'Please input template file.');
			return null;
		}

		try {
			// アップロードファイルを受け取り、
			// エラーチェックはない。ここでのエラー時はInternalErrorExceptionとなる
			$uploadFile = new TemporaryUploadFile(Hash::get($this->data, 'ActionQuizAdd.template_file'));

			// アップロードファイル解凍
			$unZip = new UnZip($uploadFile->path);
			$temporaryFolder = $unZip->extract();
			// エラーチェック
			if (! $temporaryFolder) {
				$this->validationErrors['template_file'][] = __d('quizzes', 'illegal import file.');
				return null;
			}
			// フィンガープリント確認
			$fingerPrint = $this->__checkFingerPrint($temporaryFolder->path);
			if ($fingerPrint === false) {
				$this->validationErrors['template_file'][] = __d('quizzes', 'illegal import file.');
				return null;
			}
			// テンプレートファイル本体をテンポラリフォルダに展開する。
			$quizZip = new UnZip($temporaryFolder->path . DS . QuizzesComponent::QUIZ_TEMPLATE_FILENAME);
			if (! $quizZip->extract()) {
				$this->validationErrors['template_file'][] = __d('quizzes', 'illegal import file.');
				return null;
			}
			// jsonファイルを読み取り、PHPオブジェクトに変換
			$jsonFilePath = $quizZip->path . DS . QuizzesComponent::QUIZ_JSON_FILENAME;
			$jsonFile = new File($jsonFilePath);
			if (! $jsonFile->exists()) {
				// ファイルがない？
				return null;
			}
			$jsonData = $jsonFile->read();
			$jsonQuiz = json_decode($jsonData, true);
		} catch (Exception $ex) {
			$this->validationErrors['template_file'][] = __d('quizzes', 'file upload error.');
			return null;
		}
		// 初めにファイルに記載されている小テストプラグインのバージョンと
		// 現サイトの小テストプラグインのバージョンを突合し、差分がある場合はインポート処理を中断する。
		if ($this->_checkVersion($jsonQuiz) === false) {
			$this->validationErrors['template_file'][] = __d('quizzes', 'version is different.');
			return null;
		}
		// バージョンが一致した場合、データをメモリ上に構築
		$quizzes = $this->_getQuizzes($quizZip->path, $jsonQuiz['Quizzes'], $fingerPrint);

		// 現在の言語環境にマッチしたデータを返す
		return $quizzes[0];
	}

/**
 * _getQuizzes
 *
 * @param string $folderPath path string to import zip file exist
 * @param array $quizzes quiz data in import json file
 * @param string $importKey import key (hash string)
 * @return array QuizData
 */
	protected function _getQuizzes($folderPath, $quizzes, $importKey) {
		$wysiswyg = new WysiwygZip();

		foreach ($quizzes as &$q) {
			// WysIsWygのデータを入れなおす
			$flatQuiz = Hash::flatten($q);
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
						// keyと同じ名前のフォルダの下にあるkeyの名前のZIPファイルを渡して
						// その返ってきた値をこのカラムに設定
						$value = $wysiswyg->getFromWysiwygZip(
							$folderPath . DS . $value,
							$model->alias . '.' . $columnName
						);
					}
				}
			}
			$q = Hash::expand($flatQuiz);
			$q['Quiz']['import_key'] = $importKey;
		}
		return $quizzes;
	}

/**
 * __checkFingerPrint
 *
 * @param string $folderPath folder path
 * @return string finger print string
 */
	private function __checkFingerPrint($folderPath) {
		// フィンガープリントファイルを取得
		$file = new File($folderPath . DS . QuizzesComponent::QUIZ_FINGER_PRINT_FILENAME, false);
		if (! $file->exists()) {
			// フィンガープリントファイルがない？
			return false;
		}
		$fingerPrint = $file->read();

		// ファイル内容から算出されるハッシュ値と指定されたフットプリント値を比較し
		// 同一であれば正当性が保証されたと判断する（フォーマットチェックなどは行わない）
		$quizZipFile = $folderPath . DS . QuizzesComponent::QUIZ_TEMPLATE_FILENAME;
		if (! file_exists($quizZipFile)) {
			return false;
		}
		if (sha1_file($quizZipFile, false) != $fingerPrint) {
			return false;
		}
		$file->close();
		return $fingerPrint;
	}

/**
 * _checkVersion
 *
 * @param array $jsonData バージョンが含まれたJson
 * @return bool
 */
	protected function _checkVersion($jsonData) {
		// バージョン情報を取得するためComposer情報を得る
		$Plugin = ClassRegistry::init('PluginManager.Plugin');
		$composer = $Plugin->getComposer('netcommons/quizzes');
		if (!$composer) {
			return false;
		}
		if (!isset($jsonData['version'])) {
			return false;
		}
		if ($composer['version'] != $jsonData['version']) {
			return false;
		}
		return true;
	}
}
