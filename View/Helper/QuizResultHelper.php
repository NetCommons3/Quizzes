<?php
/**
 * QuizResult Helper
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator Co., Ltd. <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
App::uses('AppHelper', 'View/Helper');
/**
 * Quiz result Helper
 *
 * @author Allcreator Co., Ltd. <info@allcreator.net>
 * @package NetCommons\Quizzes\View\Helper
 */
class QuizResultHelper extends AppHelper {

/**
 * Other helpers used by FormHelper
 *
 * @var array
 */
	public $helpers = array(
		'NetCommonsForm',
		'NetCommonsHtml',
		'Form'
	);

/**
 * 会員ハンドル名＋個人成績へのリンク
 *
 * @param array $quiz 小テストデータ
 * @param array $summary 回答サマリ
 * @return string 会員ハンドル名＋個人成績へのリンク
 */
	public function getHandleNameLink($quiz, $summary) {
		$url = NetCommonsUrl::actionUrl(array(
			'plugin' => 'quizzes',
			'controller' => 'quiz_result',
			'action' => 'view',
			'block_id' => Current::read('Block.id'),
			'key' => $quiz['Quiz']['key'],
			$summary['QuizAnswerSummary']['id'],
			'frame_id' => Current::read('Frame.id')));
		if (isset($summary['User']['handlename'])) {
			$userName = h($summary['User']['handlename']);
		} else {
			$userName = __d('quizzes', 'Guest');
		}
		return $this->NetCommonsHtml->link($userName, $url);
	}
/**
 * 合格チェック
 *
 * @param array $quiz 小テストデータ
 * @param array $summary 回答サマリ
 * @return string 合格状態
 */
	public function getPassClass($quiz, $summary) {
		// 合格判断なし
		if ($quiz['Quiz']['passing_grade'] == 0 && $quiz['Quiz']['estimated_time'] == 0) {
			return '';
		}
		// デフォルト合格
		$passing = QuizzesComponent::STATUS_GRADE_PASS;
		$withinTime = QuizzesComponent::STATUS_GRADE_PASS;
		// 得点合格判断
		if ($quiz['Quiz']['passing_grade'] != 0) {
			$passing = $this->_getValue($summary, 'passing_status');
		}
		if ($quiz['Quiz']['estimated_time'] != 0) {
			$withinTime = $this->_getValue($summary, 'within_time_status');
		}
		if ($passing == QuizzesComponent::STATUS_GRADE_PASS && $withinTime == QuizzesComponent::STATUS_GRADE_PASS) {
			return 'success';
		}
		return '';
	}
/**
 * 得点合格チェック
 *
 * @param array $quiz 小テストデータ
 * @param array $summary 回答サマリ
 * @return string 合格状態
 */
	public function getPassing($quiz, $summary) {
		if ($quiz['Quiz']['passing_grade'] == 0) {
			return '';
		}
		// ログイン者で履歴がある場合は履歴から判断
		// 未ログイン者は履歴が持てないので、それぞれで判断
		$passingStatus = $this->_getValue($summary, 'passing_status');
		if ($passingStatus == QuizzesComponent::STATUS_GRADE_PASS) {
			return '<span class="text-success glyphicon glyphicon-ok"></span>';
		}
		if ($passingStatus == QuizzesComponent::STATUS_GRADE_FAIL) {
			return '';
		}
		return '<span class="text-warning glyphicon glyphicon-warning-sign"></span>';
	}
/**
 * 時間内チェック
 *
 * @param array $quiz 小テストデータ
 * @param array $summary 回答サマリ
 * @return string 合格状態
 */
	public function getWithinTime($quiz, $summary) {
		if ($quiz['Quiz']['estimated_time'] == 0) {
			return '';
		}
		// ログイン者で履歴がある場合は履歴から判断
		// 未ログイン者は履歴が持てないので、それぞれで判断
		$withinTimeStatus = $this->_getValue($summary, 'within_time_status');
		if ($withinTimeStatus == QuizzesComponent::STATUS_GRADE_PASS) {
			return '<span class="text-success glyphicon glyphicon-ok"></span>';
		}
		if ($withinTimeStatus == QuizzesComponent::STATUS_GRADE_FAIL) {
			return '';
		}
		return '<span class="text-warning glyphicon glyphicon-warning-sign"></span>';
	}
/**
 * 平均所要時間
 *
 * @param array $quiz 小テストデータ
 * @param array $summary 回答サマリ
 * @return string 平均所要時間
 */
	public function getAvgElapsed($quiz, $summary) {
		if (is_null($summary['Statistics']['avg_elapsed_second'])) {
			//$second = $summary['QuizAnswerSummary']['elapsed_second'];
			return '';
		} else {
			$second = $summary['Statistics']['avg_elapsed_second'];
		}
		$ret = sprintf('%01.2f分', $second / 60);
		return $ret;
	}
/**
 * 直近の得点
 *
 * @param array $summary 回答サマリ
 * @return string 得点
 */
	public function getScore($summary) {
		if ($summary['QuizAnswerSummary']['is_grade_finished'] == false) {
			return '-';
		}
		return $summary['QuizAnswerSummary']['summary_score'];
	}
/**
 * 直近の偏差値
 *
 * @param array $general 統計情報
 * @param array $summary 回答サマリ
 * @return string 偏差値
 */
	public function getStdScore($general, $summary) {
		if ($summary['QuizAnswerSummary']['is_grade_finished'] == false) {
			return '-';
		}
		$variance = $general['general']['samp_score'];
		$std = sqrt($variance);
		$avg = $general['general']['avg_score'];
		$stdDiv = ($summary['QuizAnswerSummary']['summary_score'] - $avg) * 10 / $std;
		$stdScore = 50 + $stdDiv;
		return round($stdScore);
	}
/**
 * 未採点チェック
 *
 * @param array $quiz 小テストデータ
 * @param array $summary 回答サマリ
 * @return string 合格状態
 */
	public function getNotScoring($quiz, $summary) {
		if (! is_null($summary['Statistics']['not_scoring'])) {
			$value = $summary['Statistics']['not_scoring'];
		} else {
			$value = $summary['QuizAnswerSummary']['passing_status'];
		}
		if ($value == QuizzesComponent::STATUS_GRADE_YET) {
			return '<span class="label label-danger">未採点あり</span>';
		}
		return '';
	}
/**
 * 完答チェック
 *
 * @param array $quiz 小テストデータ
 * @param array $summary 回答サマリ
 * @return string 完答状態
 */
	public function getComplete($quiz, $summary) {
		if ($summary['QuizAnswerSummary']['answer_status'] == QuizzesComponent::ACTION_ACT) {
			return '<span class="text-success glyphicon glyphicon-ok"></span>';
		}
		return '';
	}
/**
 * 所要時間
 *
 * @param array $quiz 小テストデータ
 * @param array $summary 回答サマリ
 * @return string 平均所要時間
 */
	public function getElapsed($quiz, $summary) {
		$second = $summary['QuizAnswerSummary']['elapsed_second'];
		$ret = sprintf('%01.2f分', $second / 60);
		return $ret;
	}

/**
 * チェック値取り出し
 *
 * @param array $summary 回答サマリ
 * @param string $fieldName カラム名
 * @return int 値
 */
	protected function _getValue($summary, $fieldName) {
		if (isset($summary['Statistics']) && ! is_null($summary['Statistics'][$fieldName])) {
			$value = $summary['Statistics'][$fieldName];
		} else {
			$value = $summary['QuizAnswerSummary'][$fieldName];
		}
		return $value;
	}
}