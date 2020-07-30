<?php
/**
 * QuizEdit Controller
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('AppController', 'Controller');

/**
 * QuizEditController
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Controller
 */
class QuizEditController extends QuizzesAppController {

/**
 * edit Quiz session key
 *
 * @var int
 */
	const	QUIZ_EDIT_SESSION_INDEX = 'Quizzes.quizEdit.';

/**
 * post QuizQuestions session key
 *
 * @var int
 */
	const	QUIZ_POST_QUESTION_SESSION_INDEX = 'Quizzes.postQuizQuestion.';

/**
 * use model
 *
 * @var array
 */
	public $uses = array(
		'Mails.MailSetting'
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
				'edit,edit_question,delete' => 'content_creatable',
			),
		),
		'Quizzes.Quizzes',
		'Quizzes.QuizzesShuffle',
	);

/**
 * use helpers
 *
 */
	public $helpers = array(
		'NetCommons.Token',
		'Workflow.Workflow',
		'Wysiwyg.Wysiwyg',
		'NetCommons.Wizard' => array(
			'navibar' => array(
				'edit_question' => array(
					'url' => array(
						'controller' => 'quiz_edit',
						'action' => 'edit_question',
					),
					'label' => array('quizzes', 'Set questions'),
				),
				'edit' => array(
					'url' => array(
						'controller' => 'quiz_edit',
						'action' => 'edit',
					),
					'label' => array('quizzes', 'Set quiz'),
				),
			),
			'cancelUrl' => null
		),
		'Quizzes.QuizQuestionEdit',
		'Blocks.BlockTabs' => array(
			'mainTabs' => array(
				'block_index' => array('url' => array('controller' => 'quiz_blocks')),
				'role_permissions' => array('url' => array('controller' => 'quiz_block_role_permissions')),
				'frame_settings' => array('url' => array('controller' => 'quiz_frame_settings')),
				'mail_settings' => array('url' => array('controller' => 'quiz_mail_settings')),
			),
		),
		'Quizzes.QuizAnswerCorrect'
	);

/**
 * target Quiz　
 *
 */
	protected $_quiz = null;

/**
 * post QuizQuestions　
 *
 */
	protected $_postQuiz = null;

/**
 * session index
 *
 */
	protected $_sessionIndex = null;

/**
 * beforeFilter
 *
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();
		// NetCommonsお約束：編集画面へのURLに編集対象のコンテンツキーが含まれている
		// まずは、そのキーを取り出す
		// アンケートキー
		$QuizKey = $this->_getQuizKeyFromPass();

		// セッションインデックスパラメータ
		$sessionName = self::QUIZ_EDIT_SESSION_INDEX . $this->_getQuizEditSessionIndex();
		if ($this->request->is('post') || $this->request->is('put')) {
			// ウィザード画面なのでセッションに記録された前画面データが必要
			$this->_quiz = $this->Session->read($sessionName);
			if (! $this->_quiz) {
				// セッションタイムアウトの場合
				return;
			}
		} else {
			// redirectで来るか、もしくは本当に直接のURL指定で来るかのどちらか
			// セッションに記録された値がある場合はそちらを優先
			if ($this->Session->check($sessionName)) {
				$this->_quiz = $this->Session->read($sessionName);
			} elseif (! empty($QuizKey)) {
				// アンケートキーの指定がある場合は過去データ編集と判断
				// 指定されたアンケートデータを取得
				// NetCommonsお約束：履歴を持つタイプのコンテンツデータはgetWorkflowContentsで取り出す
				$this->_quiz = $this->Quiz->getWorkflowContents('first', array(
					'recursive' => 0,
					'conditions' => array(
						$this->Quiz->alias . '.key' => $QuizKey
					)
				));
			}
		}
		if ($QuizKey) {
			// NetCommonsお約束：編集の場合には改めて編集権限をチェックする必要がある
			// getWorkflowContentsはとりあえず自分が「見られる」コンテンツデータを取ってきてしまうので
			if (! $this->Quiz->canEditWorkflowContent($this->_quiz)) {
				$this->_quiz = null;
			}
		}
		// ここへは設定画面の一覧から来たのか、一般画面の一覧から来たのか
		$this->_decideSettingLayout();
	}

/**
 * Before render callback. beforeRender is called before the view file is rendered.
 *
 * Overridden in subclasses.
 *
 * @return void
 */
	public function beforeRender() {
		parent::beforeRender();

		//ウィザード
		foreach ($this->helpers['NetCommons.Wizard']['navibar'] as &$actions) {
			$urlParam = $actions['url'];
			$urlParam = Hash::merge($urlParam, $this->request->params['named']);
			foreach ($this->request->params['pass'] as $passParam) {
				$urlParam[$passParam] = null;
			}
			$actions['url'] = $urlParam;

			if (! isset($actions['url']['s_id'])) {
				$actions['url']['s_id'] = $this->_getQuizEditSessionIndex();
			}
		}
	}

/**
 * edit question method
 *
 * @throws BadRequestException
 * @return void
 */
	public function edit_question() {
		// 処理対象の小テストデータが見つかっていない場合、エラー
		if (empty($this->_quiz)) {
			$this->setAction('edit_not_found');
			return;
		}
		// Postの場合
		if ($this->request->is('post') || $this->request->is('put')) {

			$postQuiz = $this->request->data;
			if (! empty($postQuiz['QuizPage'])) {
				if ($this->request->is('ajax')) {
					$this->_postPage($postQuiz);
					$this->view = 'edit_json';
					return;
				}
			} else {
				$Quiz = $this->_quiz;

				// 質問編集画面からのPOSTは、発行後は書き換えない 未発行の場合はPostデータを上書き設定
				// ただし解説は上書きする
				$accumSessName =
					self::QUIZ_POST_QUESTION_SESSION_INDEX . $this->_getQuizEditSessionIndex();
				$accumulationPost = $this->Session->read($accumSessName);

				// 蓄積データは消す
				$this->Session->delete($accumSessName);

				if ($this->Quiz->hasPublished($Quiz) == 0) {
					// 未発行の場合はPostデータをまるごとすり替え
					$Quiz['QuizPage'] = $accumulationPost['QuizPage'];
				} else {
					// 解説はいつでも上書きする
					$this->_setCommentary($Quiz['QuizPage'], $accumulationPost['QuizPage']);
				}
				$this->Quiz->clearQuizId($Quiz, true);

				$Quiz['QuizPage'] = QuizzesAppController::changeBooleansToNumbers($Quiz['QuizPage']);

				// バリデート
				$this->Quiz->set($Quiz);
				if (! $this->Quiz->validates(array('validate' => Quiz::QUIZ_VALIDATE_TYPE))) {
					$this->__setupViewParameters($Quiz, '');
					return;
				}

				// バリデートがOKであればPOSTで出来上がったデータをセッションキャッシュに書く
				$this->Session->write(self::QUIZ_EDIT_SESSION_INDEX . $this->_sessionIndex, $Quiz);

				// 次の画面へリダイレクト
				$this->redirect($this->_getActionUrl('edit'));
			}
		} else {
			// アンケートデータが取り出せている場合、それをキャッシュに書く
			$this->Session->write(
				self::QUIZ_EDIT_SESSION_INDEX . $this->_getQuizEditSessionIndex(),
				$this->_sorted($this->_quiz));
			$this->__setupViewParameters($this->_quiz, '');

			// Getの場合、蓄積データは消す
			$accumSessName =
				self::QUIZ_POST_QUESTION_SESSION_INDEX . $this->_getQuizEditSessionIndex();
			$this->Session->delete($accumSessName);
		}
	}

/**
 * _postPage
 *
 * 分割送信されている編集された質問情報をまとめ上げる
 *
 * @param array $postPage 分割送信された質問情報（１質問ずつ送信）
 * @return void
 */
	protected function _postPage($postPage) {
		// 小テストデータに作成されたPost質問データをかぶせる
		// （質問作成画面では質問データ属性全てをPOSTしているのですり替えでOK）
		$sessionName = self::QUIZ_POST_QUESTION_SESSION_INDEX . $this->_getQuizEditSessionIndex();
		$accumulationPost = $this->Session->read($sessionName);
		if (! $accumulationPost) {
			$accumulationPost = array();
		}

		$postPage = $this->_changeBoolean($postPage);

		// マージ
		$accumulationPost = Hash::merge($accumulationPost, $postPage);

		// マージ結果をセッションに記録
		$this->Session->write(
			self::QUIZ_POST_QUESTION_SESSION_INDEX . $this->_sessionIndex, $accumulationPost);
	}

/**
 * edit method
 *
 * @throws BadRequestException
 * @return void
 */
	public function edit() {
		// 処理対象のアンケートデータが見つかっていない場合、エラー
		if (empty($this->_quiz)) {
			$this->setAction('edit_not_found');
			return;
		}

		// Postの場合
		if ($this->request->is('post') || $this->request->is('put')) {
			$postQuiz = $this->request->data;

			$beforeStatus = $this->_quiz['Quiz']['status'];

			// 設定画面ではアンケート本体に纏わる情報のみがPOSTされる
			$Quiz = Hash::merge($this->_quiz, $postQuiz);

			// 指示された編集状態ステータス
			$Quiz['Quiz']['status'] = $this->Workflow->parseStatus();

			// それをDBに書く
			$saveQuiz = $this->Quiz->saveQuiz($Quiz);
			// エラー
			if ($saveQuiz == false) {
				$Quiz['Quiz']['status'] = $beforeStatus;
				$this->__setupViewParameters($Quiz, $this->_getActionUrl('edit_quesiton'));
				return;
			}
			// 成功時 セッションに書き溜めた編集情報を削除
			$this->Session->delete(self::QUIZ_EDIT_SESSION_INDEX . $this->_getQuizEditSessionIndex());
			if ($this->layout == 'NetCommons.setting') {
				$this->redirect(NetCommonsUrl::backToIndexUrl('default_setting_action'));
			} else {
				// 保存をやり直しているのだからセッションに回答中データがあったら消してしまう
				$this->QuizzesShuffle->clear($saveQuiz['Quiz']['key']);
				// 回答画面（詳細）へリダイレクト×
				// 発行したときはページの最初に戻るべきとの指摘アリ
				if ($saveQuiz['Quiz']['status'] == WorkflowComponent::STATUS_PUBLISHED) {
					$this->redirect(NetCommonsUrl::backToPageUrl());
				} else {
					$action = 'test_mode';
					$urlArray = array(
						'controller' => 'quiz_answers',
						'action' => $action,
						Current::read('Block.id'),
						$this->_getQuizKey($saveQuiz),
						'frame_id' => Current::read('Frame.id'),
					);
					$this->redirect(NetCommonsUrl::actionUrl($urlArray));
				}
			}
		} else {
			// 指定されて取り出したアンケートデータをセッションキャッシュに書く
			$this->Session->write(
				self::QUIZ_EDIT_SESSION_INDEX . $this->_getQuizEditSessionIndex(),
				$this->_quiz);
			$this->__setupViewParameters($this->_quiz, $this->_getActionUrl('edit_question'));
		}
		$comments = $this->Quiz->getCommentsByContentKey(Hash::get($this->_quiz, 'Quiz.key'));
		$this->set('comments', $comments);
	}

/**
 * edit_not_found method
 *
 * @return void
 */
	public function edit_not_found() {
		if ($this->request->is('post') || $this->request->is('put')) {
			$this->throwBadRequest();
			return;
		}
		$url = $this->_getCancelBackUrl();
		$this->set('cancelUrl', $url);
	}

/**
 * delete method
 *
 * @return void
 */
	public function delete() {
		if (! $this->request->is('delete')) {
			$this->throwBadRequest();
			return;
		}

		//削除権限チェック
		if (! $this->Quiz->canDeleteWorkflowContent($this->_quiz)) {
			$this->throwBadRequest();
			return;
		}

		// 削除処理
		if (! $this->Quiz->deleteQuiz($this->request->data)) {
			$this->throwBadRequest();
			return;
		}

		$this->Session->delete(self::QUIZ_EDIT_SESSION_INDEX . $this->_sessionIndex);

		$this->redirect($this->_getCancelBackUrl());
	}

/**
 * cancel method
 *
 * @return void
 */
	public function cancel() {
		$this->Session->delete(self::QUIZ_EDIT_SESSION_INDEX . $this->_sessionIndex);
		$this->redirect(NetCommonsUrl::backToPageUrl());
	}

/**
 * _getActionUrl method
 *
 * @param string $method 遷移先アクション名
 * @return void
 */
	protected function _getActionUrl($method) {
		$urlArray = array(
			'controller' => Inflector::underscore($this->name),
			'action' => $method,
			Current::read('Block.id'),
			$this->_getQuizKey($this->_quiz),
			'frame_id' => Current::read('Frame.id'),
			's_id' => $this->_getQuizEditSessionIndex()
		);
		if (Current::isSettingMode()) {
			$urlArray['q_mode'] = 'setting';
		}
		$this->log($urlArray, 'debug');
		return NetCommonsUrl::actionUrl($urlArray);
	}

/**
 * __setupViewParameters method
 *
 * @param array $Quiz アンケートデータ
 * @param string $backUrl BACKボタン押下時の戻るパス
 * @return void
 */
	private function __setupViewParameters($Quiz, $backUrl) {
		$isPublished = $this->Quiz->hasPublished($Quiz);

		// エラーメッセージはページ、質問、選択肢要素のそれぞれの場所に割り当てる
		$this->NetCommons->handleValidationError($this->Quiz->validationErrors);
		$flatError = Hash::flatten($this->Quiz->validationErrors);
		$newFlatError = array();
		foreach ($flatError as $key => $val) {
			if (preg_match('/^(.*)\.(.*)\.(.*)$/', $key, $matches)) {
				$newFlatError[$matches[1] . '.error_messages.' . $matches[2] . '.' . $matches[3]] = $val;
			}
		}
		$Quiz = Hash::merge($Quiz, Hash::expand($newFlatError));

		$this->set('backUrl', $backUrl);

		$ajaxPostUrl = $this->_getActionUrl($this->action);

		$this->set('ajaxPostUrl', $ajaxPostUrl);
		$this->set('postUrl', array('url' => $ajaxPostUrl));

		$this->set('formOptions', array('url' => $this->_getActionUrl($this->action), 'type' => 'post'));
		$this->set('cancelUrl', array('url' => $this->_getCancelBackUrl()));
		$this->set('deleteUrl', array('url' => $this->_getActionUrl('delete')));

		$this->set('questionTypeOptions', $this->Quizzes->getQuestionTypeOptionsWithLabel());
		$this->set('isPublished', $isPublished);

		$this->set('quizKey', Hash::get($Quiz, 'Quiz.key'));
		
		$this->request->data = $Quiz;
		$this->request->data['Frame'] = Current::read('Frame');
		$this->request->data['Block'] = Current::read('Block');
	}

/**
 * _setCommentary
 *
 * 発行後のPOSTデータから解説文だけ設定する
 * dstとsrcのページ構成、質問構成は全く同じであることを前提とする
 *
 * @param array &$dst 設定さき
 * @param array $src 設定ソース
 * @return void
 */
	protected function _setCommentary(&$dst, $src) {
		foreach ($src as $pageIndex => $page) {
			foreach ($page['QuizQuestion'] as $qIndex => $question) {
				$dst[$pageIndex]['QuizQuestion'][$qIndex]['commentary'] = $question['commentary'];
			}
		}
	}
/**
 * _getCancelBackUrl
 *
 * セッティングモードか通常モードからかで編集画面からの戻りＵＲＬを分ける
 *
 * @return array
 */
	protected function _getCancelBackUrl() {
		if ($this->layout == 'NetCommons.setting') {
			$retArr = NetCommonsUrl::backToIndexUrl('default_setting_action');
		} else {
			$retArr = NetCommonsUrl::backToPageUrl();
		}
		return $retArr;
	}
}
