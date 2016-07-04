<?php
/**
 * QuizBlocksController
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('AppController', 'Controller');
App::uses('QuizzesAppSettingController', 'Quizzes.Controller');
App::uses('TemporaryFolder', 'Files.Utility');
App::uses('CsvFileWriter', 'Files.Utility');
App::uses('ZipDownloader', 'Files.Utility');
App::uses('QuizFrameSetting', 'Quizzes.Model');

/**
 * BlocksController
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Controller
 */
class QuizBlocksController extends QuizzesAppSettingController {

/**
 * csv download item count handling unit
 *
 * @var int
 */
	const	QUIZ_CSV_UNIT_NUMBER = 1000;

/**
 * layout
 *
 * @var array
 */
	public $layout = 'NetCommons.setting';

/**
 * use models
 *
 * @var array
 */
	public $uses = array(
		'Quizzes.Quiz',
		'Quizzes.QuizFrameSetting',
		'Quizzes.QuizAnswerSummary',
		'Quizzes.QuizAnswerSummaryCsv',
		'Blocks.Block',
		'Quizzes.QuizExport',
	);

/**
 * use components
 *
 * @var array
 */
	public $components = array(
		'NetCommons.Permission' => array(
			//アクセスの権限
			'allow' => array(
				'index,download,export' => 'block_editable',
			),
		),
		'Paginator',
	);

/**
 * use helpers
 *
 * @var array
 */
	public $helpers = array(
		'Session',
		'Blocks.BlockForm',
		'Blocks.BlockTabs' => array(
			'mainTabs' => array(
				'block_index' => array('url' => array('controller' => 'quiz_blocks')),
				'role_permissions' => array('url' => array('controller' => 'quiz_block_role_permissions')),
				'frame_settings' => array('url' => array('controller' => 'quiz_frame_settings')),
				'mail_settings' => array('url' => array('controller' => 'quiz_mail_settings')),
			),
		),
		'Blocks.BlockIndex',
		'NetCommons.NetCommonsForm',
		'NetCommons.Date',
		'AuthorizationKeys.AuthKeyPopupButton',
	);

/**
 * index
 *
 * @return void
 */
	public function index() {
		// 条件設定値取得
		$conditions = $this->Quiz->getBaseCondition();

		// データ取得
		$this->paginate = array(
			'conditions' => $conditions,
			'page' => 1,
			'order' => array('modified' => 'desc'),
			//'limit' => QuizFrameSetting::QUIZ_DEFAULT_DISPLAY_NUM_PER_PAGE,
			'recursive' => 0,
		);
		$quiz = $this->paginate('Quiz');
		if (! $quiz) {
			$this->view = 'not_found';
			return;
		}

		$this->set('quizzes', $quiz);
	}

/**
 * download
 *
 * @return void
 * @throws InternalErrorException
 */
	public function download() {
		// NetCommonsお約束：コンテンツ操作のためのURLには対象のコンテンツキーが必ず含まれている
		// まずは、そのキーを取り出す
		// アンケートキー
		$quizKey = $this->_getQuizKeyFromPass();
		// キー情報をもとにデータを取り出す
		$quiz = $this->QuizAnswerSummaryCsv->getQuizForAnswerCsv($quizKey);
		if (! $quiz) {
			$this->_setFlashMessageAndRedirect(
				__d('quizzes', 'Designation of the quiz does not exist.'));
			return;
		}
		// 圧縮用パスワードキーを求める
		if (! empty($this->request->data['AuthorizationKey']['authorization_key'])) {
			$zipPassword = $this->request->data['AuthorizationKey']['authorization_key'];
		} else {
			$this->_setFlashMessageAndRedirect(
				__d('quizzes', 'Setting of password is required always to download answers.'));
			return;
		}

		try {
			$tmpFolder = new TemporaryFolder();
			$csvFile = new CsvFileWriter(array(
				'folder' => $tmpFolder->path
			));
			// 回答データを一気に全部取得するのは、データ爆発の可能性があるので
			// QUIZ_CSV_UNIT_NUMBER分に制限して取得する
			$offset = 0;
			do {
				$datas = $this->QuizAnswerSummaryCsv->getAnswerSummaryCsv(
					$quiz,
					self::QUIZ_CSV_UNIT_NUMBER, $offset);
				// CSV形式で書きこみ
				foreach ($datas as $data) {
					$csvFile->add($data);
				}
				$dataCount = count($datas);	// データ数カウント
				$offset += $dataCount;		// 次の取得開始位置をずらす
			} while ($dataCount == self::QUIZ_CSV_UNIT_NUMBER);
			// データ取得数が制限値分だけとれている間は繰り返す

		} catch (Exception $e) {
			// NetCommonsお約束:エラーメッセージのFlash表示
			$this->_setFlashMessageAndRedirect(__d('quizzes', 'download error'));
			return;
		}
		// Downloadの時はviewを使用しない
		$this->autoRender = false;
		// ダウンロードファイル名決定 アンケート名称をつける
		$zipFileName = $quiz['Quiz']['title'] . '.zip';
		$downloadFileName = $quiz['Quiz']['title'] . '.csv';
		// 出力
		return $csvFile->zipDownload(rawurlencode($zipFileName), $downloadFileName, $zipPassword);
	}

/**
 * export
 *
 * template file about quiz export action
 *
 * @return void
 */
	public function export() {
		// NetCommonsお約束：コンテンツ操作のためのURLには対象のコンテンツキーが必ず含まれている
		// まずは、そのキーを取り出す
		// 小テストキー
		$quizKey = $this->_getQuizKeyFromPass();
		// キー情報をもとにデータを取り出す
		$quiz = $this->QuizAnswerSummaryCsv->getQuizForAnswerCsv($quizKey);
		if (! $quiz) {
			$this->_setFlashMessageAndRedirect(
				__d('quizzes', 'Designation of the quiz does not exist.'));
			return;
		}

		try {
			// zipファイル準備
			$zipFile = new ZipDownloader();

			// Export用のデータ配列を取得する
			$zipData = $this->QuizExport->getExportData($quizKey);

			// Export用ファイルデータをZIPファイルに出力する
			// ※この中でWYSISWYGエディタデータは適宜処理されている
			$this->QuizExport->putToZip($zipFile, $zipData);

			// アーカイブ閉じる
			$zipFile->close();
		} catch(Exception $e) {
			$this->_setFlashMessageAndRedirect(
				__d('quizzes', 'export error' . $e->getMessage()));
			return;
		}
		// 大外枠zipファイル準備
		$zipWrapperFile = new ZipDownloader();
		// 小テストデータファイルのフィンガープリントを得る
		$fingerPrint = sha1_file($zipFile->path, false);
		// フィンガープリントをアーカイブに加える
		$zipWrapperFile->addFromString(QuizzesComponent::QUIZ_FINGER_PRINT_FILENAME, $fingerPrint);
		// 本体ファイルを
		$zipWrapperFile->addFile($zipFile->path, QuizzesComponent::QUIZ_TEMPLATE_FILENAME);
		// export-key 設定
		$this->Quiz->saveExportKey($quiz['Quiz']['id'], $fingerPrint);

		// viewを使用しない
		$this->autoRender = false;

		// ダウンロード出力ファイル名確定
		$exportFileName = $quiz['Quiz']['title'] . '.zip';
		// 出力
		return $zipWrapperFile->download(rawurlencode($exportFileName));
	}

/**
 * _setFlashMessageAndRedirect
 *
 * @param string $message flash error message
 *
 * @return void
 */
	protected function _setFlashMessageAndRedirect($message) {
		$this->NetCommons->setFlashNotification(
			$message,
			array(
				'interval' => NetCommonsComponent::ALERT_VALIDATE_ERROR_INTERVAL
			));
		$this->redirect(NetCommonsUrl::actionUrl(array(
			'controller' => 'quiz_blocks',
			'action' => 'index',
			'frame_id' => Current::read('Frame.id')
		)));
	}

}
