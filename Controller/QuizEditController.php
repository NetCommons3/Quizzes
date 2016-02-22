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
 * use model
 *
 * @var array
 */
	public $uses = array(
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
	);

/**
 * use helpers
 *
 */
	public $helpers = array(
		'Workflow.Workflow',
		'Quizzes.QuestionEdit',
		'Quizzes.QuizAnswerCorrect'
	);

/**
 * target Quiz　
 *
 */
	protected $_quiz = null;

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
		if ($this->request->is('post')) {
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
				// NetCommonsお約束：編集の場合には改めて編集権限をチェックする必要がある
				// getWorkflowContentsはとりあえず自分が「見られる」コンテンツデータを取ってきてしまうので
				if (! $this->Quiz->canEditWorkflowContent($this->_quiz)) {
					$this->_quiz = null;
				}
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
		// 処理対象のアンケートデータが見つかっていない場合、エラー
		if (empty($this->_quiz)) {
			$this->throwBadRequest();
			return false;
		}

		// Postの場合
		if ($this->request->is('post')) {

			$postQuiz = $this->request->data;

			// アンケートデータに作成されたPost質問データをかぶせる
			// （質問作成画面では質問データ属性全てをPOSTしているのですり替えでOK）
			$Quiz = $this->_quiz;
			$Quiz['Quiz'] = Hash::merge($this->_quiz['Quiz'], $postQuiz['Quiz']);

			// 発行後のアンケートは質問情報は書き換えない
			// 未発行の場合はPostデータを上書き設定して
			if ($this->Quiz->hasPublished($Quiz) == 0) {
				$this->log($postQuiz['QuizPage'], 'debug');
				$Quiz['QuizPage'] = $postQuiz['QuizPage'];
			} else {
				// booleanの値がPOST時と同じようになるように調整
				$Quiz['QuizPage'] = QuizzesAppController::changeBooleansToNumbers($Quiz['QuizPage']);
			}

			// バリデート
			$this->Quiz->set($Quiz);
			if (! $this->Quiz->validates()) {
				$this->__setupViewParameters($Quiz, '');
				return;
			}

			// バリデートがOKであればPOSTで出来上がったデータをセッションキャッシュに書く
			$this->Session->write(self::QUIZ_EDIT_SESSION_INDEX . $this->_sessionIndex, $Quiz);

			// 次の画面へリダイレクト
			$this->redirect($this->_getActionUrl('edit'));
		} else {
			// アンケートデータが取り出せている場合、それをキャッシュに書く
			$this->Session->write(
				self::QUIZ_EDIT_SESSION_INDEX . $this->_getQuizEditSessionIndex(),
				$this->_sorted($this->_quiz));
			$this->__setupViewParameters($this->_quiz, '');
		}
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
			$this->throwBadRequest();
			return;
		}

		// Postの場合
		if ($this->request->is('post')) {
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
			// ページトップへリダイレクト
			$this->redirect(NetCommonsUrl::backToPageUrl());

		} else {
			// 指定されて取り出したアンケートデータをセッションキャッシュに書く
			$this->Session->write($this->_getQuizEditSessionIndex(), $this->_quiz);
			$this->__setupViewParameters($this->_quiz, $this->_getActionUrl('edit_question'));
		}
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

		$this->redirect(NetCommonsUrl::backToPageUrl());
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
		return NetCommonsUrl::actionUrl(array(
			'controller' => Inflector::underscore($this->name),
			'action' => $method,
			Current::read('Block.id'),
			$this->_getQuizKey($this->_quiz),
			'frame_id' => Current::read('Frame.id'),
			's_id' => $this->_getQuizEditSessionIndex()
		));
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
		$this->set('formOptions', array('url' => $this->_getActionUrl($this->action), 'type' => 'post'));
		$this->set('cancelUrl', $this->_getActionUrl('cancel'));
		$this->set('questionTypeOptions', $this->Quizzes->getQuestionTypeOptionsWithLabel());
		$this->set('newQuestionLabel', __d('questionnaires', 'New Question'));
		$this->set('newChoiceLabel', __d('quizzes', 'new choice'));
		$this->set('isPublished', $isPublished);
		$this->request->data = $Quiz;
		$this->request->data['Frame'] = Current::read('Frame');
		$this->request->data['Block'] = Current::read('Block');
	}
}
