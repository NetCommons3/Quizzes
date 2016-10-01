<?php
/**
 * Questionnaires AppController
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('AppController', 'Controller');
App::uses('QuizzesComponent', 'Quizzes.Controller/Component');

/**
 * QuizzesAppController
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Questionnaires\Controller
 */
class QuizzesAppController extends AppController {

/**
 * use model
 *
 * @var array
 */
	public $uses = array(
		'Quizzes.Quiz',
	);

/**
 * use components
 *
 * @var array
 */
	public $components = array(
		'Security',
		'Pages.PageLayout',
		'Quizzes.Quizzes',
		'Quizzes.QuizzesOwnAnswerQuiz',
	);

/**
 * isAbleTo
 * Whether access to survey of the specified ID
 * Forced URL hack guard
 * And against the authority of the state of the specified quiz respondents put a guard
 * It is not in the public state
 * Out of period
 * Stopped
 * Repeatedly answer
 * You are not logged in to not forgive other than member
 *
 * @param array $quiz 対象となる小テスト
 * @return bool
 */
	public function isAbleTo($quiz) {
		// 指定のアンケートの状態と回答者の権限を照らし合わせてガードをかける
		// 編集権限を持っていない場合
		//   公開状態にない
		//   期間外
		//   停止中
		//   繰り返し回答
		//   会員以外には許してないのに未ログインである

		// 編集権限を持っている場合
		//   公開状態にない場合はALL_OK
		//
		// 　公開状態になっている場合は
		//   期間外
		//   停止中
		//   繰り返し回答
		//   会員以外には許してないのに未ログインである

		// 公開状態が「公開」になっている場合は編集権限の有無にかかわらず共通だ
		// なのでまずは公開状態だけを確認する

		// 編集権限があればオールマイティＯＫなのでこの後の各種チェックは不要！
		if ($this->Quiz->canEditWorkflowContent($quiz)) {
			return true;
		}
		// 編集権限がない場合は、もろもろのチェックを行うこと

		// 読み取り権限がない？
		if (! $this->Quiz->canReadWorkflowContent()) {
			// それはだめだ
			return false;
		}

		// 基本、権限上、見ることができるコンテンツだ
		// しかし、アンケート独自の条件部分のチェックを行う必要がある
		// 期間外
		if ($quiz['Quiz']['answer_timing'] == QuizzesComponent::USES_USE
			&& $quiz['Quiz']['period_range_stat'] != QuizzesComponent::QUIZ_PERIOD_STAT_IN) {
			return false;
		}

		// 会員以外には許していないのに未ログイン
		if ($quiz['Quiz']['is_no_member_allow'] == QuizzesComponent::PERMISSION_NOT_PERMIT) {
			$userId = Current::read('User.id');
			if (empty($userId)) {
				return false;
			}
		}

		return true;
	}

/**
 * isAbleToAnswer 指定されたIDに回答できるかどうか
 * 強制URLハックのガード
 * 指定のアンケートの状態と回答者の権限を照らし合わせてガードをかける
 * 公開状態にない
 * 期間外
 * 停止中
 * 繰り返し回答
 * 会員以外には許してないのに未ログインである
 *
 * @param array $quiz 対象となる小テスト
 * @return bool
 */
	public function isAbleToAnswer($quiz) {
		// 未公開のものは編集権限がる人にはAll-OKだが、それ以外の人にはAll-NG
		if ($quiz['Quiz']['status'] != WorkflowComponent::STATUS_PUBLISHED) {
			if ($this->Quiz->canEditWorkflowContent($quiz)) {
				return true;
			}
			return false;
		}
		// 繰り返し回答を許していないのにすでに回答済みか
		if ($quiz['Quiz']['is_repeat_allow'] == QuizzesComponent::PERMISSION_NOT_PERMIT) {
			if ($this->QuizzesOwnAnswerQuiz->checkOwnAnsweredKeys($quiz['Quiz']['key'])) {
				return false;
			}
		}
		// 解答期限外
		if ($quiz['Quiz']['period_range_stat'] != QuizzesComponent::QUIZ_PERIOD_STAT_IN) {
			return false;
		}

		return true;
	}

/**
 * canGrade
 *
 * 採点できるかどうか
 *
 * @param array $quiz 対象となる小テスト
 * @return bool
 */
	public function canGrade($quiz) {
		return Current::permission('content_publishable');
	}
/**
 * _sorted method
 * to sort a given array by key
 *
 * @param array $obj data array
 * @return array ソート後配列
 */
	protected function _sorted($obj) {
		// シーケンス順に並び替え、かつ、インデックス値は０オリジン連番に変更
		// ページ配列もないのでそのまま戻す
		if (!Hash::check($obj, 'QuizPage.{n}')) {
			return $obj;
		}
		$obj['QuizPage'] = Hash::sort($obj['QuizPage'], '{n}.page_sequence', 'asc', 'numeric');

		foreach ($obj['QuizPage'] as &$page) {
			if (Hash::check($page, 'QuizQuestion.{n}')) {
				$page['QuizQuestion'] = Hash::sort(
					$page['QuizQuestion'],
					'{n}.question_sequence',
					'asc',
					'numeric'
				);

				foreach ($page['QuizQuestion'] as &$question) {
					if (Hash::check($question, 'QuizChoice.{n}')) {
						$question['QuizChoice'] = Hash::sort(
							$question['QuizChoice'],
							'{n}.choice_sequence',
							'asc',
							'numeric'
						);
					}
				}
			}
		}
		return $obj;
	}

/**
 * changeBooleansToNumbers method
 * to change the Boolean value of a given array to 0,1
 *
 * @param array $data data array
 * @return array
 */
	public static function changeBooleansToNumbers(Array $data) {
		// Note the order of arguments and the & in front of $value
		array_walk_recursive($data, 'self::converter');
		return $data;
	}

/**
 * __converter method
 * to change the Boolean value to 0,1
 *
 * @param array &$value value
 * @param string $key key
 * @return void
 * @SuppressWarnings("unused")
 */
	public static function converter(&$value, $key) {
		if (is_bool($value)) {
			$value = ($value ? '1' : '0');
		}
	}

/**
 * _changeBoolean
 *
 * JSから送られるデータはbooleanの値のものがtrueとかfalseの文字列データで来てしまうので
 * 正式なbool値に変換しておく
 *
 * @param array $orig 元データ
 * @return array 変換後の配列データ
 */
	protected function _changeBoolean($orig) {
		$new = [];

		foreach ($orig as $key => $value) {
			if (is_array($value)) {
				$new[$key] = $this->_changeBoolean($value);
			} else {
				if ($value === 'true') {
					$value = true;
				}
				if ($value === 'false') {
					$value = false;
				}
				$new[$key] = $value;
			}
		}
		return $new;
	}

/**
 * _getQuizKeyFromPass
 *
 * @return string
 */
	protected function _getQuizKeyFromPass() {
		if (isset($this->params['key'])) {
			if (strpos($this->params['key'], 's_id:') === 0) {
				return '';
			}
			return $this->params['key'];
		}
		return '';
	}

/**
 * _getQuizEditSessionIndex
 *
 * @return string
 */
	protected function _getQuizEditSessionIndex() {
		if (isset($this->_sessionIndex) && $this->_sessionIndex !== null) {
			return $this->_sessionIndex;
		}
		if (isset($this->params['named']['s_id'])) {
			$tm = $this->params['named']['s_id'];
		} else {
			$tm = Security::hash(microtime(true), 'md5');
		}
		$this->_sessionIndex = $tm;
		return $tm;
	}

/**
 * _getQuizKey
 *
 * @param array $quiz Quiz data
 * @return string
 */
	protected function _getQuizKey($quiz) {
		if (isset($quiz['Quiz']['key'])) {
			return $quiz['Quiz']['key'];
		} else {
			return '';
		}
	}
/**
 * _decideSettingLayout
 *
 * セッティング系の画面からの流れなのかどうかを判断し、レイアウトを決める
 *
 * @return void
 */
	protected function _decideSettingLayout() {
		$isSetting = Hash::get($this->request->params, 'named.q_mode');
		if ($isSetting == 'setting') {
			if (Current::permission('block_editable')) {
				$this->layout = 'NetCommons.setting';
			}
			return;
		}
	}
}
