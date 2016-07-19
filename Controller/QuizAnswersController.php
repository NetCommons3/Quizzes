<?php
/**
 * QuizAnswers Controller
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('AppController', 'Controller');

/**
 * QuizAnswersController
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Controller
 */
class QuizAnswersController extends QuizzesAppController {

/**
 * use model
 *
 * @var array
 */
	public $uses = array(
		'Quizzes.QuizPage',
		'Quizzes.QuizAnswerSummary',
		'Quizzes.QuizAnswer',
		'Quizzes.QuizAnswerGrade',
		'Quizzes.QuizFrameSetting',
	);

/**
 * use components
 *
 * @var array
 */
	public $components = array(
		'NetCommons.Permission',
		'AuthorizationKeys.AuthorizationKey' => array(
			'operationType' => 'embedding',
			'model' => 'Quiz',
			'contentId' => 0),
		'VisualCaptcha.VisualCaptcha' => array(
			'operationType' => 'embedding'),
		'Quizzes.QuizzesOwnAnswerQuiz',	// 回答済み小テスト管理
		'Quizzes.QuizzesOwnAnswer',		// 回答ID管理
		'Quizzes.QuizzesPassQuiz',		// 合格小テスト管理
		'Quizzes.QuizzesAnswerStart',
		'Quizzes.QuizzesShuffle',
	);

/**
 * use helpers
 *
 */
	public $helpers = [
		'NetCommons.Date',
		'NetCommons.TitleIcon',
		'Workflow.Workflow',
		'Quizzes.QuizAnswer',
		'Quizzes.QuizGrading'
	];

/**
 * target quiz data
 *
 */
	private $__quiz = null;

/**
 * frame setting display type
 */
	private $__displayType = null;

/**
 * target isAbleToAnswer Action
 *
 */
	private $__ableToAnswerAction = ['start', 'view', 'confirm'/*, 'grading'*/];

/**
 * beforeFilter
 * NetCommonsお約束：できることならControllerのbeforeFilterで実行可/不可の判定して流れを変える
 *
 * @return void
 */
	public function beforeFilter() {
		// ゲストアクセスOKのアクションを設定
		$this->Auth->allow('start', 'view', 'confirm', 'grading', 'no_more_answer');

		// 親クラスのbeforeFilterを済ませる
		parent::beforeFilter();

		// NetCommonsお約束：編集画面へのURLに編集対象のコンテンツキーが含まれている
		// まずは、そのキーを取り出す
		$quizKey = $this->_getQuizKeyFromPass();

		// キーで指定されたデータを取り出しておく
		$conditions = $this->Quiz->getBaseCondition(
			array('Quiz.key' => $quizKey)
		);
		$this->__quiz = $this->Quiz->find('first', array(
			'conditions' => $conditions,
		));
		if (! $this->__quiz) {
			$this->setAction('throwBadRequest');
		}
		// 現在の表示形態を調べておく
		list($this->__displayType) = $this->QuizFrameSetting->getQuizFrameSetting(
			Current::read('Frame.key')
		);
		$this->set('displayType', $this->__displayType);

		// 以下のisAbleto..の内部関数にてNetCommonsお約束である編集権限、参照権限チェックを済ませています
		// 閲覧可能か
		if (! $this->isAbleTo($this->__quiz)) {
			// 不可能な時は「回答できません」画面を出すだけ
			$this->setAction('no_more_answer');
			return;
		}
		if (in_array($this->action, $this->__ableToAnswerAction)) {
			// 回答可能か
			if (!$this->isAbleToAnswer($this->__quiz)) {
				// 回答が不可能な時は「回答できません」画面を出すだけ
				$this->setAction('no_more_answer');
				return;
			}
		}
		$this->AuthorizationKey->contentId = $this->__quiz['Quiz']['id'];
	}

/**
 * test_mode
 *
 * テストモード回答のとき、一番最初に表示するページ
 * 一覧表示画面で「テスト」ボタンがここへ誘導するようになっている。
 * どのようなアンケートであるのかの各種属性設定をわかりやすくまとめて表示する表紙的な役割を果たす。
 *
 * あくまで作成者の便宜のために表示しているものであるので、最初のページだったら必ずここを表示といったような
 * 強制的redirectなどは設定しない。なので強制URL-Hackしたらこの画面をスキップすることだって可能。
 * 作成者への「便宜」のための親切心ページなのでスキップしたい人にはそうさせてあげるのでよいと考える。
 *
 * @return void
 */
	public function test_mode() {
		$status = $this->__quiz['Quiz']['status'];
		// テストモード確認画面からのPOSTや、現在のアンケートデータのステータスが公開状態の時
		// 次へリダイレクト
		if ($this->request->is('post') || $status == WorkflowComponent::STATUS_PUBLISHED) {
			$this->redirect(NetCommonsUrl::actionUrl(array(
				'controller' => 'quiz_answers',
				'action' => 'view',
				Current::read('Block.id'),
				$this->_getQuizKey($this->__quiz),
				'frame_id' => Current::read('Frame.id')
			)));
			return;
		}
		$this->request->data['Frame'] = Current::read('Frame');
		$this->request->data['Block'] = Current::read('Block');
		$this->set('quiz', $this->__quiz);
	}

/**
 * start
 *
 * 一番最初に表示するページ
 *
 * @return void
 */
	public function start() {
		$quiz = $this->__quiz;
		$quizKey = $this->_getQuizKey($this->__quiz);
		// setActionで移動してきた場合、Requestに設定するとBlackHoleへ連れていかれることが判明
		// なので全てSetでやりくり
		$this->set('frameId', Current::read('Frame.id'));
		$this->set('blockId', Current::read('Block.id'));
		$this->set('quiz', $quiz);
		$this->set('quizPage', $quiz['QuizPage'][0]);

		// POSTチェック
		if ($this->request->is('post')) {

			// ガードチェック：デフォルトはOKとしておく
			$chkFlg = true;

			// 認証キーが使用されることになって入る場合
			if ($quiz['Quiz']['is_key_pass_use'] == QuizzesComponent::USES_USE) {
				if (! $this->AuthorizationKey->check()) {
					$chkFlg = false;
				}
			}
			// 画像認証が使用されることになって入る場合
			if ($quiz['Quiz']['is_image_authentication'] == QuizzesComponent::USES_USE) {
				if (! $this->VisualCaptcha->check()) {
					$chkFlg = false;
				}
			}

			// OKだったら
			if ($chkFlg == true) {
				// スタートしたことをセッションに記載
				$this->QuizzesAnswerStart->saveStartQuizOfThisUser($quizKey);

				// 回答サマリレコードを取得、または作成
				//$summaryId = $this->QuizAnswerSummary->saveStartSummary($quiz);
				$this->QuizzesOwnAnswerQuiz->forceGetProgressiveSummaryOfThisUser($quiz);

				// 回答サマリIDをセッションに記録
				///////$this->QuizzesOwnAnswerQuiz->saveProgressiveSummaryOfThisUser($quizKey, $summaryId);

				// ページランダム表示対応
				$this->QuizzesShuffle->shufflePage($quiz);
				// 選択肢ランダム表示対応
				$this->QuizzesShuffle->shuffleChoice($quiz);

				$url = NetCommonsUrl::actionUrl(array(
					'controller' => 'quiz_answers',
					'action' => 'view',
					Current::read('Block.id'),
					$quizKey,
					'frame_id' => Current::read('Frame.id'),
				));
				$this->redirect($url);
			}
		}
	}
/**
 * view method
 * Display the question of the quiz , to accept the answer input
 *
 * @return void
 */
	public function view() {
		$quiz = $this->__quiz;
		$quizKey = $this->_getQuizKey($this->__quiz);

		// スタート画面を表示して、時間スタートしてるか
		// してなかったらスタート画面へ
		if (! $this->_gardQuiz($quizKey)) {
			return;
		}
		$summary = $this->QuizzesOwnAnswerQuiz->getProgressiveSummaryOfThisUser($quizKey);
		if (empty($summary)) {
			$this->setAction('throwBadRequest');
		}

		// ページランダム表示対応
		$this->QuizzesShuffle->shufflePage($quiz);
		// 選択肢ランダム表示対応
		$this->QuizzesShuffle->shuffleChoice($quiz);

		// 次ページ
		$nextPageSeq = 0;	// デフォルト

		if ($this->request->is('post') && $this->request->data('QuizAnswer')) {
			// 保存エラーの場合は今のページを再表示するので前準備
			$nextPageSeq = $this->data['QuizPage']['page_sequence'];
			if ($this->QuizAnswer->saveAnswer($this->data, $quiz, $summary)) {
				// 回答データがあり、無事保存できたら次ページを取得する
				$nextPageSeq = $this->QuizzesShuffle->getNextPage($quiz, $nextPageSeq);
			}
			// 次ページはもう存在しない
			if ($nextPageSeq === false) {
				// 状態を確認待ちに変えて
				$this->QuizAnswerSummary->saveAnswerEndSummary($summary['QuizAnswerSummary']['id']);
				// 確認画面へ
				$url = NetCommonsUrl::actionUrl(array(
					'controller' => 'quiz_answers',
					'action' => 'confirm',
					Current::read('Block.id'),
					$quizKey,
					'frame_id' => Current::read('Frame.id'),
				));
				$this->redirect($url);
				return;
			}
		} else {
			$setAnswers = $this->QuizAnswer->getProgressiveAnswerOfThisSummary($summary);
			$this->request->data['QuizAnswer'] = $this->_setAnswerToView($setAnswers);
		}
		// 質問情報をView変数にセット
		$this->request->data['Frame'] = Current::read('Frame');
		$this->request->data['Block'] = Current::read('Block');
		$this->request->data['QuizPage'] = $quiz['QuizPage'][$nextPageSeq];
		$this->request->data['QuizAnswerSummary'] = $summary['QuizAnswerSummary'];
		$this->set('quiz', $quiz);
		$this->set('quizPage', $quiz['QuizPage'][$nextPageSeq]);
		$this->set('quizPageIndex', $nextPageSeq);
		$this->NetCommons->handleValidationError($this->QuizAnswer->validationErrors);
	}
/**
 * confirm method
 *
 * @return void
 */
	public function confirm() {
		$quizKey = $this->_getQuizKey($this->__quiz);
		if (! $this->_gardQuiz($quizKey)) {
			return;
		}

		// 回答中サマリレコード取得
		$summary = $this->QuizzesOwnAnswerQuiz->getProgressiveSummaryOfThisUser(
			$this->_getQuizKey($this->__quiz));
		if (!$summary) {
			$this->setAction('throwBadRequest');
		}

		// ページランダム表示対応
		$this->QuizzesShuffle->shufflePage($this->__quiz);
		// 選択肢ランダム表示対応
		$this->QuizzesShuffle->shuffleChoice($this->__quiz);

		// POSTチェック
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($summary['QuizAnswerSummary']['id'] != $this->request->data('QuizAnswerSummary.id')) {
				$this->setAction('throwBadRequest');
			}
			// 回答を確定して採点
			$this->QuizAnswer->saveConfirmAnswer($this->__quiz, $summary);
			// サマリ状態を完了に変える
			$newSummary = $this->QuizAnswerSummary->saveEndSummary(
				$this->__quiz,
				$summary['QuizAnswerSummary']['id']
			);
			if (! $newSummary) {
				$this->setAction('throwBadRequest');
			}
			// 回答済み小テストキーをセッション記録
			$this->QuizzesOwnAnswerQuiz->saveOwnAnsweredKeys($quizKey);
			// 回答済みサマリIDをセッション記録
			$this->QuizzesOwnAnswer->saveAnsweredSummaryIds($newSummary['id']);

			// 合格済み記録
			$isPassAnswer = $this->QuizAnswerSummary->isPassAnswer($this->__quiz, $newSummary);
			if ($isPassAnswer == QuizzesComponent::STATUS_GRADE_PASS) {
				$this->QuizzesPassQuiz->savePassQuizKeys($quizKey);
			}

			// この閲覧者が「回答スタートした」記録をセッションから消しておく
			$this->QuizzesAnswerStart->deleteStartQuizOfThisUser();

			// 採点画面へ行く
			$url = NetCommonsUrl::actionUrl(array(
				'controller' => 'quiz_answers',
				'action' => 'grading',
				Current::read('Block.id'),
				$this->_getQuizKey($this->__quiz),
				'frame_id' => Current::read('Frame.id'),
				$newSummary['id'] // SaveしたサマリのID
			));
			$this->redirect($url);
		}

		// 回答情報取得
		// 回答情報並べ替え
		$setAnswers = $this->QuizAnswer->getProgressiveAnswerOfThisSummary($summary);

		// 質問情報をView変数にセット
		$this->request->data['Frame'] = Current::read('Frame');
		$this->request->data['Block'] = Current::read('Block');
		$this->request->data['QuizAnswerSummary'] = $summary['QuizAnswerSummary'];
		$this->set('quiz', $this->__quiz);
		$this->request->data['QuizAnswer'] = $this->_setAnswerToView($setAnswers);
		$this->set('answers', $setAnswers);
	}

/**
 * grading method
 *
 * @return void
 * @throws ForbiddenException
 */
	public function grading() {
		$quiz = $this->__quiz;

		$summaryId = Hash::get($this->params['pass'], '0');
		$summary = $this->QuizAnswerSummary->findById($summaryId);
		if (! $summary) {
			throw new ForbiddenException(__d('net_commons', 'Forbidden Request'));
		}
		// 編集者でない場合は
		// 自分の回答したサマリか確認
		// 編集者は何でも見せてよい
		// 自分のならば採点結果を出してもよい
		$canEdit = $this->Quiz->canEditWorkflowContent($quiz);
		$isMineAnswer = $this->QuizzesOwnAnswer->checkOwnAnsweredSummaryId($summaryId);
		if (!$canEdit && !$isMineAnswer) {
			$this->setAction('throwBadRequest');
		}
		// 採点?
		if ($this->request->is('post') || $this->request->is('put')) {
			// 編集権限のない人が採点できないからチェック
			if (! $canEdit) {
				$this->setAction('throwBadRequest');
			}
			$grade = $this->request->data['QuizAnswerGrade'];
			$validate = $this->QuizAnswerGrade->validateMany($grade, array(
				'quiz' => $quiz,
				'answerSummary' => $summary
			));
			if ($validate) {
				$this->QuizAnswerGrade->saveGrade($quiz, $summaryId, $grade);
				$summary = $this->QuizAnswerSummary->findById($summaryId);
			}
		} else {
			$this->request->data['QuizAnswerGrade'] = Hash::combine($summary['QuizAnswer'], '{n}.id', '{n}');
		}
		$gradePass = $this->QuizAnswerSummary->isPassAnswer($this->__quiz, $summary);
		$this->QuizAnswerSummary->getCorrectRate($quiz);
		$this->set('quiz', $quiz);
		$this->set('summary', $summary);
		$this->set('passQuizKeys', $this->QuizzesPassQuiz->getPassQuizKeys());
		$this->set('gradePass', $gradePass);
		$this->set('hasFreeStyleQuestion', QuizzesComponent::hasFreeStyleQuestion($quiz));
		$this->set('isMineAnswer', $isMineAnswer);
		$this->NetCommons->handleValidationError($this->QuizAnswerGrade->validationErrors);

		if ($canEdit) {
			$this->view = 'grading_form';
		}
	}

/**
 * no_more_answer method
 * 条件によって回答できないアンケートにアクセスしたときに表示
 *
 * @return void
 */
	public function no_more_answer() {
	}

/**
 * _gardQuiz method
 *
 * @param string $quizKey 小テストキー
 * @return bool
 */
	protected function _gardQuiz($quizKey) {
		if (! $this->QuizzesAnswerStart->checkStartedQuizKeys($quizKey)) {
			$url = NetCommonsUrl::actionUrl(array(
				'controller' => 'quiz_answers',
				'action' => 'start',
				Current::read('Block.id'),
				$quizKey,
				'frame_id' => Current::read('Frame.id'),
			));
			$this->redirect($url);
			return false;
		}
		return true;
	}
/**
 * _setAnswerToView method
 *
 * 内部処理の都合上、解答のデータは配列として保持している
 * しかし、択一選択、単語回答のときは答えが単純な文字列でないと画面に再描画できないので
 * 画面表示前に配列から文字列に修正する
 *
 * @param array $answers 回答データ
 * @return array
 */
	protected function _setAnswerToView($answers) {
		if (! $answers) {
			return false;
		}
		$ret = array();
		foreach ($answers as $ans) {
			$qKey = $ans['QuizAnswer']['quiz_question_key'];
			$question = Hash::extract($this->__quiz, 'QuizPage.{n}.QuizQuestion.{n}[key=' . $qKey . ']');
			if (! QuizzesComponent::isMultipleAnswerType($question[0]['question_type'])) {
				if (is_array($ans['QuizAnswer']['answer_value'])) {
					$ans['QuizAnswer']['answer_value'] = $ans['QuizAnswer']['answer_value'][0];
				}
			}
			$ret[$qKey][] = $ans['QuizAnswer'];
		}
		return $ret;
	}
}
