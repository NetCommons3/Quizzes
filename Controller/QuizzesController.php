<?php
/**
 * Quizzes Controller
 *
 * @property PaginatorComponent $Paginator
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('QuizzesAppController', 'Quizzes.Controller');
/**
 * Quizzes Controller
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
class QuizzesController extends QuizzesAppController {

/**
 * quiz view filter
 *
 * @var string
 */
	const QUIZ_ANSWER_VIEW_ALL = 'viewall';
	const QUIZ_ANSWER_UNANSWERED = 'unanswered';
	const QUIZ_ANSWER_ANSWERED = 'answered';
	const QUIZ_ANSWER_TEST = 'test';

/**
 * use model
 *
 * @var array
 */
	public $uses = array(
		'Quizzes.QuizAnswerSummary',
		'Quizzes.QuizAnswer',
		'Quizzes.QuizFrameSetting',
		'Quizzes.QuizFrameDisplayQuiz',
		'Files.FileModel',					// FileUpload
		'PluginManager.Plugin',
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
				'add' => 'content_creatable',
			),
		),
		'Quizzes.Quizzes',
		'Quizzes.QuizzesOwnAnswerQuiz',	// 回答済み小テスト管理
		'Quizzes.QuizzesOwnAnswer',		// 回答ID管理
		'Quizzes.QuizzesPassQuiz',		// 合格小テスト管理
		'Quizzes.QuizzesAnswerStart',
		'Quizzes.QuizzesShuffle',
		'Paginator',
	);

/**
 * use helpers
 *
 * @var array
 */
	public $helpers = array(
		'Workflow.Workflow',
		'NetCommons.Date',
		'NetCommons.DisplayNumber',
		'NetCommons.TitleIcon',
		'NetCommons.Button',
		'Quizzes.QuizStatusLabel',
		'Quizzes.QuizGradeLink',
		'Quizzes.QuizAnswerButton',
		'Quizzes.QuizResultButton',
	);

/**
 * beforeFilter
 *
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();
		// ここへは設定画面の一覧から来たのか、一般画面の一覧から来たのか
		$this->_decideSettingLayout();
	}

/**
 * index method
 *
 * @return void
 */
	public function index() {
		// この閲覧者が「回答スタートした」記録をセッションから消しておく
		$this->QuizzesAnswerStart->deleteStartQuizOfThisUser();
		// テストページ記憶も消しておく
		$this->QuizzesShuffle->clear();

		// 表示方法設定値取得
		list(, $displayNum, $sort, $dir) =
			$this->QuizFrameSetting->getQuizFrameSetting(Current::read('Frame.key'));

		// 条件設定値取得
		$conditions = $this->Quiz->getCondition();

		// データ取得の条件設定
		$this->Paginator->settings = array_merge(
			$this->Paginator->settings,
			array(
				'conditions' => $conditions,
				'page' => 1,
				'order' => array($sort => $dir),
				'limit' => $displayNum,
				'recursive' => 0,
			)
		);
		// 絞込の指定がない場合は、デフォルトは全て表示です
		if (! isset($this->params['named']['answer_status'])) {
			$this->request->params['named']['answer_status'] = self::QUIZ_ANSWER_VIEW_ALL;
		}
		// データ取得
		$quizzes = $this->paginate('Quiz', $this->_getPaginateFilter());

		$this->set('quizzes', $quizzes);
		$this->set('currentStatus', $this->request->params['named']['answer_status']);
		$this->set('filterList', $this->_getFilterSelectList());

		// 回答済み、合格済、未採点ありなどの小テストキー配列を確保する
		$this->__setOwnAnsweredQuizKeys();
		$this->__setPassQuizKeys();
		$this->__setNotScoringQuizKeys();
		$this->__setAnswerSummaryIdWithQuizKey();

		if (count($quizzes) == 0) {
			$this->view = 'Quizzes/no_quiz';
		}
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		// POSTされたデータを読み取り
		if ($this->request->is('post')) {
			// Postデータをもとにした新アンケートデータの取得をModelに依頼する
			$actionModel = ClassRegistry::init('Quizzes.ActionQuizAdd', 'true');
			if ($quiz = $actionModel->createQuiz($this->request->data)) {
				$tm = $this->_getQuizEditSessionIndex();
				// 作成中アンケートデータをセッションキャッシュに書く
				$this->Session->write('Quizzes.quizEdit.' . $tm, $quiz);
				// 次の画面へリダイレクト
				$urlArray = array(
					'controller' => 'quiz_edit',
					'action' => 'edit_question',
					Current::read('Block.id'),
					'frame_id' => Current::read('Frame.id'),
					's_id' => $tm,
				);
				if ($this->layout == 'NetCommons.setting') {
					$urlArray['q_mode'] = 'setting';
				}
				$this->redirect(NetCommonsUrl::actionUrl($urlArray));
				return;
			} else {
				// データに不備があった場合
				$this->NetCommons->handleValidationError($actionModel->validationErrors);
			}
		} else {
			// 初期表示の場合は、create_optionは初期値として「ＮＥＷ」を設定する
			$this->request->data['ActionQuizAdd']['create_option'] = QuizzesComponent::QUIZ_CREATE_OPT_NEW;
		}

		// 過去データ 取り出し
		$pastQuizzes = $this->Quiz->find('all',
			array(
				'conditions' => $this->Quiz->getCondition(),
				'offset' => 0,
				'limit' => 1000,
				'recursive' => -1,
				'order' => array('Quiz.modified DESC'),
			));
		$this->set('pastQuizzes', $pastQuizzes);
		if ($this->layout == 'NetCommons.setting') {
			$this->set('cancelUrl', NetCommonsUrl::backToIndexUrl('default_setting_action'));
		} else {
			$this->set('cancelUrl', NetCommonsUrl::backToPageUrl());
		}

		// NetCommonsお約束：投稿のデータはrequest dataに設定する
		$this->request->data['Frame'] = Current::read('Frame');
		$this->request->data['Block'] = Current::read('Block');
	}

/**
 * _getFilterSelectList method
 *
 * @return array
 */
	protected function _getFilterSelectList() {
		$list = array(
			self::QUIZ_ANSWER_VIEW_ALL => __d('quizzes', 'View All'),
			self::QUIZ_ANSWER_UNANSWERED => __d('quizzes', 'Unanswered'),
			self::QUIZ_ANSWER_ANSWERED => __d('quizzes', 'Answered'),
		);
		if (Current::permission('content_creatable')) {
			$list[self::QUIZ_ANSWER_TEST] = __d('quizzes', 'Test');
		}
		return $list;
	}

/**
 * _getPaginateFilter method
 *
 * @return array
 */
	protected function _getPaginateFilter() {
		$filter = array();

		if ($this->request->params['named']['answer_status'] == self::QUIZ_ANSWER_TEST) {
			$filter = array(
				'Quiz.status !=' => WorkflowComponent::STATUS_PUBLISHED
			);
			return $filter;
		}

		$filterCondition = array('Quiz.key' => $this->QuizzesOwnAnswerQuiz->getOwnAnsweredKeys());
		if ($this->request->params['named']['answer_status'] == self::QUIZ_ANSWER_UNANSWERED) {
			$filter = array(
				'NOT' => $filterCondition
			);
		} elseif ($this->request->params['named']['answer_status'] == self::QUIZ_ANSWER_ANSWERED) {
			$filter = array(
				$filterCondition
			);
		}
		return $filter;
	}

/**
 * Set view value of answered quiz keys
 * 閲覧者がすでに回答し終えている小テストのキーの配列を確保する
 *
 * @return void
 */
	private function __setOwnAnsweredQuizKeys() {
		if ($this->request->params['named']['answer_status'] == self::QUIZ_ANSWER_UNANSWERED) {
			$this->set('ownAnsweredKeys', array());
			return;
		}

		$this->set('ownAnsweredKeys', $this->QuizzesOwnAnswerQuiz->getOwnAnsweredKeys());
		$this->set('ownAnsweredCounts', $this->QuizzesOwnAnswerQuiz->getOwnAnsweredCounts());
	}

/**
 * Set view value of passed quiz keys
 * 閲覧者がすでに合格している小テストのキーの配列を確保する
 *
 * @return void
 */
	private function __setPassQuizKeys() {
		$this->set('passQuizKeys', $this->QuizzesPassQuiz->getPassQuizKeys());
	}

/**
 * Set view value of not scoring quiz keys
 * 閲覧者が解答し終えていて、かつ、未採点である小テストのキーの配列を確保する
 *
 * @return void
 */
	private function __setNotScoringQuizKeys() {
		// 自分が編集権限を持つ小テストの回答サマリか
		// もしくは自分が解答したサマリ
		$ownAnswerSummaryIds = $this->QuizzesOwnAnswer->getAnsweredSummaryIds();
		$canGradingSummaryIds = $this->QuizAnswerSummary->getCanGradingSummary();
		$summaryIds = array_flip($ownAnswerSummaryIds) + array_flip($canGradingSummaryIds);
		$summaryIds = array_flip($summaryIds);
		$notScoringQuiz = $this->QuizAnswer->getNotScoringQuizKey(
			$summaryIds
		);
		$notScoringQuiz = Hash::combine(
			$notScoringQuiz,
			'{n}.QuizAnswerSummary.quiz_key',
			'{n}.QuizAnswerSummary.quiz_key'
		);
		$this->set('notScoringQuizKeys', $notScoringQuiz);
	}
/**
 * 自分の回答サマリIDと小テストキーの対応付けデータ
 *
 * @return void
 */
	private function __setAnswerSummaryIdWithQuizKey() {
		$ownAnswerSummaryIds = $this->QuizzesOwnAnswer->getAnsweredSummaryIds();
		$answeredSummaryMap = $this->QuizAnswerSummary->find('all', array(
			'fields' => array('id', 'quiz_key'),
			'conditions' => array(
				'id' => $ownAnswerSummaryIds,
			),
			'recursive' => -1,
		));
		$answeredSummaryMap = Hash::combine(
			$answeredSummaryMap,
			'{n}.QuizAnswerSummary.quiz_key',
			'{n}.QuizAnswerSummary.id'
		);
		$this->set('ownAnswerdSummaryMap', $answeredSummaryMap);
	}

}
