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
// FUJI App::uses('WysIsWygDownloader', 'Quizzes.Utility');

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
				'message' => sprintf(__d('net_commons', 'Please input %s.'), __d('quizzes', 'Title')),
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
 * アンケートデータを作成する
 *
 * @param array $data 作成するアンケートデータ
 * @return array|bool
 */
	public function createQuiz($data) {
		// 渡されたQuizデータを自Modelデータとする
		$this->set($data);
		// データチェック
		if ($this->validates()) {
			// Postデータの内容に問題がない場合は、そのデータをもとに新しいアンケートデータを作成
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
		if (empty(array_shift($check))) {
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
		$cnt = $this->Quiz->find('count', array('id' => $check));
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
		// アンケートデータを新規に作成する
		// 新規作成の場合、タイトル文字のみ画面で設定されPOSTされる
		// Titleをもとに、アンケートデータ基本構成を作成し返す

		// デフォルトデータをもとに新規作成
		$quiz = $this->_getDefaultQuiz(array(
			'title' => $this->data['ActionQuizAdd']['title']));
		// アンケートデータを返す
		return $quiz;
	}

/**
 * _createFromReuse
 *
 * @return array QuizData
 */
	protected function _createFromReuse() {
		// アンケートデータを過去のアンケートデータをもとにして作成する
		// 過去からの作成の場合、参考にする過去のアンケートのidのみPOSTされてくる
		// (orgin_idではなくidである点に注意！)
		// idをもとに、過去のアンケートデータを取得し、
		// そのデータから今回作成するアンケートデータ基本構成を作成し返す

		// 過去のアンケートのコピー・クローンで作成
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
				'is_correct_show' => QuizzesComponent::USES_USE,
				'is_total_show' => QuizzesComponent::USES_USE,
				'answer_timing' => QuizzesComponent::USES_NOT_USE,
				'is_key_pass_use' => QuizzesComponent::USES_NOT_USE,
			),
			$addData);

		$quiz['QuizPage'][0] = $this->QuizPage->getDefaultPage($quiz);
		return $quiz;
	}

/**
 * _getQuizCloneById 指定されたIDにのアンケートデータのクローンを取得する
 *
 * @param int $quizId アンケートID(編集なのでoriginではなくRAWなIDのほう
 * @return array
 */
	protected function _getQuizCloneById($quizId) {
		$quiz = $this->Quiz->find('first', array(
			'conditions' => array('Quiz.id' => $quizId),
		));

		if (!$quiz) {
			return $this->getDefaultQuiz(array('title' => ''));
		}
		// ID値のみクリア
		$this->Quiz->clearQuizId($quiz);

		return $quiz;
	}

/**
 * _createFromTemplate
 *
 * @return array QuizData
 */
	protected function _createFromTemplate() {
		// アンケートデータをUPLOADされたアンケートテンプレートファイルのデータをもとにして作成する
		// テンプレートからの作成の場合、テンプレートファイルがUPLOADされてくる
		// アップされたファイルをもとに、アンケートデータを解凍、取得し、
		// そのデータから今回作成するアンケートデータ基本構成を作成し返す

		// アップロードファイルを受け取り、
		$uploadFile = new TemporaryUploadFile(Hash::get($this->data, 'ActionQuizAdd.template_file'));
		// エラーチェック
		if (! $uploadFile) {
			$this->validationErrors['Quiz']['template_file'] = __d('quizzes', 'file upload error.');
			return null;
		}

		// アップロードファイル解凍
		$unZip = new UnZip($uploadFile->path);
		$temporaryFolder = $unZip->extract();
		// エラーチェック
		if (! $temporaryFolder) {
			$this->validationErrors['Quiz']['template_file'] = __d('quizzes', 'illegal import file.');
			return null;
		}

		// フィンガープリント確認
		$fingerPrint = $this->__checkFingerPrint($temporaryFolder->path);
		if ($fingerPrint === false) {
			$this->validationErrors['Quiz']['template_file'] = __d('quizzes', 'illegal import file.');
			return null;
		}

		// アンケートテンプレートファイル本体をテンポラリフォルダに展開する。
		$quizZip = new UnZip($temporaryFolder->path . DS . QuizzesComponent::QUIZ_TEMPLATE_FILENAME);
		if (! $quizZip->extract()) {
			$this->validationErrors['Quiz']['template_file'] = __d('quizzes', 'illegal import file.');
			return null;
		}

		// jsonファイルを読み取り、PHPオブジェクトに変換
		$jsonFilePath = $quizZip->path . DS . QuizzesComponent::QUIZ_JSON_FILENAME;
		$jsonFile = new File($jsonFilePath);
		$jsonData = $jsonFile->read();
		$jsonQuiz = json_decode($jsonData, true);

		// 初めにファイルに記載されているアンケートプラグインのバージョンと
		// 現サイトのアンケートプラグインのバージョンを突合し、差分がある場合はインポート処理を中断する。
		if ($this->__checkVersion($jsonQuiz) === false) {
			$this->validationErrors['Quiz']['template_file'] = __d('quizzes', 'version is different.');
			return null;
		}

		// バージョンが一致した場合、アンケートデータをメモリ上に構築
		$quizzes = $this->_getQuizzes(
			$quizZip->path,
			$jsonQuiz['Quizzes'],
			$fingerPrint);

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
		$wysiswyg = new WysIsWygDownloader();

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
						$value = $wysiswyg->getFromWysIsWygZIP($folderPath . DS . $value, $model->alias . '.' . $columnName);
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
		$fingerPrint = $file->read();

		// ファイル内容から算出されるハッシュ値と指定されたフットプリント値を比較し
		// 同一であれば正当性が保証されたと判断する（フォーマットチェックなどは行わない）
		$quizZipFile = $folderPath . DS . QuizzesComponent::QUIZ_TEMPLATE_FILENAME;
		if (sha1_file($quizZipFile, false) != $fingerPrint) {
			return false;
		}
		$file->close();
		return $fingerPrint;
	}

/**
 * __checkVersion
 *
 * @param array $jsonData バージョンが含まれたJson
 * @return bool
 */
	private function __checkVersion($jsonData) {
		// バージョン情報を取得するためComposer情報を得る
		$Plugin = ClassRegistry::init('Plugins.Plugin');
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
