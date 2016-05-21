<?php
/**
 * QuizGrading Helper
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator Co., Ltd. <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
App::uses('AppHelper', 'View/Helper');
/**
 * Quiz grading Helper
 *
 * @author Allcreator Co., Ltd. <info@allcreator.net>
 * @package NetCommons\Quizzes\View\Helper
 */
class QuizGradingHelper extends AppHelper {

/**
 * Other helpers used by FormHelper
 *
 * @var array
 */
	public $helpers = array(
		'NetCommons.NetCommonsForm',
		'NetCommons.TitleIcon',
		'Form',
		'Quizzes.QuizAnswerCorrect'
	);

/**
 * 採点時のの回答、正解表示
 *
 * @param array $quiz 小テストデータ
 * @param int $pageIndex ページインデックス
 * @param int $questionIndex 質問インデックス
 * @param int $serialIndex 質問通番
 * @param array $question 問題
 * @param array $answers 回答
 * @return string 回答のHTML
 */
	public function grading($quiz, $pageIndex, $questionIndex, $serialIndex, $question, $answers) {
		$answer = Hash::extract(
			$answers,
			'QuizAnswer.{n}[quiz_question_key=' . $question['key'] . ']'
		);
		if ($answer) {
			$answer = $answer[0];
		}
		$summary = $answers['QuizAnswerSummary'];
		$ret = $this->getScoreLabel($question, $answer);
		$ret .= $this->getQuestionLabel($serialIndex, $question, $answer);
		$ret .= $question['question_value'];
		$ret .= '<dl class="quiz-grading-data">';
		$ret .= $this->getAnswer($question, $answer);
		if ($quiz['Quiz']['is_correct_show'] == true) {
			$ret .= $this->QuizAnswerCorrect->getCorrect($question, $answer);
		}
		$ret .= $this->getToGrade($quiz, $summary, $pageIndex, $questionIndex, $question, $answer);
		if ($quiz['Quiz']['is_total_show'] == true) {
			$ret .= $this->getCorrectTotal($question);
		}
		$ret .= '</dl>';
		return $ret;
	}
/**
 * 配点・得点表示
 *
 * @param array $question 問題
 * @param array $answer 回答
 * @return string 配点得点の文字列
 */
	public function getScoreLabel($question, $answer) {
		$ret = '<label class="pull-right text-muted">';
		if ($answer['correct_status'] != QuizzesComponent::STATUS_GRADE_YET) {
			$ret .= sprintf(
				__d('quizzes', '(%3d / %3d)'),	// '(%3d点 / 配点%3d点)'
				$answer['score'],
				$question['allotment']);
		} else {
			$ret .= sprintf(
				__d('quizzes', '(Ungraded / %3d)'),	// (未採点 / 配点%3d点)
				$question['allotment']
			);
		}
		$ret .= '</label>';
		return $ret;
	}

/**
 * 解答表示
 *
 * @param array $question 問題
 * @param array $answer 回答
 * @return string 解答の文字列
 */
	public function getAnswer($question, $answer) {
		$ret = '<dt>' . __d('quizzes', 'your score') . '</dt>';	// あなたの解答
		$ret .= '<dd>';
		if ($question['question_type'] == QuizzesComponent::TYPE_MULTIPLE_WORD) {
			foreach ($answer['answer_value'] as $index => $ans) {
				$ret .= sprintf('%s (%d) %s <br />',
					$this->_getMark($answer['answer_correct_status'][$index]), $index + 1, $ans);
			}
		} else {
			$yourAns = '';
			foreach ($answer['answer_value'] as $index => $ans) {
				$yourAns .= sprintf(' %s %s /',
					$this->_getMark($answer['answer_correct_status'][$index]), $ans);
			}
			$ret .= trim($yourAns, '/');

		}
		$ret .= '</dd>';
		return $ret;
	}
/**
 * 正答状態マーク取得
 *
 * @param int $status 正答状態
 * @return string 正解・不正解マーク
 */
	protected function _getMark($status) {
		if ($status == QuizzesComponent::STATUS_GRADE_FAIL) {
			return '<span class="label label-warning">' . __d('quizzes', 'miss') . '</span>';
		}
		if ($status == QuizzesComponent::STATUS_GRADE_PASS) {
			return '<span class="label label-success">' . __d('quizzes', 'clear') . '</span>';
		}
		return '';
	}
/**
 * 採点用
 *
 * @param array $quiz 小テスト
 * @param array $summary 回答サマリ
 * @param int $pageIndex ページインデックス
 * @param int $questionIndex 質問インデックス
 * @param array $question 問題
 * @param array $answer 回答
 * @return string 採点用input群文字列
 */
	public function getToGrade($quiz, $summary, $pageIndex, $questionIndex, $question, $answer) {
		if ($question['question_type'] != QuizzesComponent::TYPE_TEXT_AREA) {
			return '';
		}
		if (! $this->_View->Workflow->canEdit('Quiz', $quiz)) {
			return '';
		}
		$fieldNameBase = 'QuizAnswer.' . $answer['id'] . '.';
		$ret = '<dt>' . __d('quizzes', 'Graded') . '</dt>';
		$ret .= '<dd><div class="form-inline"><div class="form-group">';
		$ret .= $this->Form->input($fieldNameBase . 'correct_status', array(
			'type' => 'radio',
			'options' => array(
				QuizzesComponent::STATUS_GRADE_YET => __d('quizzes', 'Ungraded'), // 未採点
				QuizzesComponent::STATUS_GRADE_PASS => __d('quizzes', 'Correct'), // 正解
				QuizzesComponent::STATUS_GRADE_FAIL => __d('quizzes', 'Wrong'), // 不正解
			),
			'div' => false,
			'legend' => false,
			'label' => false,
			'before' => '<label class="radio-inline">',
			'separator' => '</label><label class="radio-inline">',
			'after' => '</label>',
			'error' => false,
		));
		$ret .= '&nbsp;&nbsp;';
		$ret .= $this->Form->input($fieldNameBase . 'score', array(
			'div' => 'form-group',
			'label' => __d('quizzes', 'points'), // 点数
			'class' => 'form-control',
			'type' => 'number',
			'max' => $question['allotment'],
			'min' => 0
		));
		$ret .= $this->Form->hidden($fieldNameBase . 'id', array('value' => $answer['id']));
		$ret .= sprintf(__d('quizzes', ' / %d '), $question['allotment']); //  / %d 点
		$ret .= '</div></div></dd>';
		return $ret;
	}
/**
 * 正答比率グラフ表示
 *
 * @param array $question 問題
 * @return string グラフ表示用AngularDirectionタグ
 */
	public function getCorrectTotal($question) {
		$questionId = $question['id'];
		$ret = '<dt>' . __d('quizzes', 'Correct answer ratio') . '</dt>'; // 正答比率
		$ret .= '<dd>';
		$ret .= '<nvd3 options="config"';
		$ret .= ' data=' . "'" . 'data["' . $questionId . '"]' . "'></nvd3>";
		$ret .= '</dd>';
		return $ret;
	}
/**
 * 問題ラベル（正解・不正解ラベル付き）
 *
 * @param int $questionIndex 質問インデックス
 * @param array $question 問題
 * @param array $answer 回答
 * @return string 回答のHTML
 */
	public function getQuestionLabel($questionIndex, $question, $answer) {
		$ret = '<label class="control-label">';
		$ret .= sprintf(__d('quizzes', 'Question %2d:'), $questionIndex + 1); // 問題%2d：
		$ret .= $this->getGradingLabel($answer);
		$ret .= '</label>';
		return $ret;
	}
/**
 * 正解・不正解ラベル
 *
 * @param array $answer 回答
 * @return string 回答のHTML
 */
	public function getGradingLabel($answer) {
		if (! isset($answer['correct_status'])) {
			$class = 'default';
			$label = __d('quizzes', 'Unanswered'); // 未回答
		}
		if ($answer['correct_status'] == QuizzesComponent::STATUS_GRADE_YET) {
			$class = 'danger';
			$label = __d('quizzes', 'Ungraded'); // 未採点
		} elseif ($answer['correct_status'] == QuizzesComponent::STATUS_GRADE_FAIL) {
			$class = 'warning';
			$label = __d('quizzes', 'Wrong'); // 不正解
		} else {
			$class = 'success';
			$label = __d('quizzes', 'Correct'); // 正解
		}
		$ret = sprintf('<span class="label label-%s">%s</span>', $class, $label);
		return $ret;
	}
/**
 * グラフ用正答比率配列
 *
 * @param array $quiz 小テストデータ
 * @return array グラフ用正答比率データ配列
 */
	public function correctRate($quiz) {
		$correctRate = array();
		foreach ($quiz['QuizPage'] as $page) {
			foreach ($page['QuizQuestion'] as $question) {
				$correctRate[$question['id']] = [
					['key' => __d('quizzes', 'Ungraded, Unanswered'), // 未採点・未回答
						'color' => '#777777',
						'values' => [['value' => $question['rest_percentage']]]
					],
					['key' => __d('quizzes', 'Correct'), // 正解
						'color' => '#5cb85c',
						'values' => [['value' => $question['correct_percentage']]]
					],
					['key' => __d('quizzes', 'Wrong'), // 不正解
						'color' => '#f0ad4e',
						'values' => [['value' => $question['wrong_percentage']]]
					],
				];
			}
		}
		return $correctRate;
	}
/**
 * 採点画面のヘッダ
 *
 * @param array $quiz 小テストデータ
 * @param int $gradePass 合格状況
 * @param array $summary 回答まとめ
 * @return string header描画HTML
 */
	public function getGradeHeader($quiz, $gradePass, $summary) {
		$headerClass = '';
		$textClass = 'text-info';
		if ($gradePass == QuizzesComponent::STATUS_GRADE_YET) {
			$headerClass = 'well well-sm';
		} elseif ($gradePass == QuizzesComponent::STATUS_GRADE_PASS) {
			$headerClass = 'alert-success';
			$textClass = 'text-success';
		} elseif ($gradePass == QuizzesComponent::STATUS_GRADE_FAIL) {
			$headerClass = 'alert-danger';
			$textClass = 'text-danger';
		}
		$ret = '<div class="alert ' . $headerClass . ' h1">';
		$ret .= '<small><span class="' . $textClass . '">';
		if ($gradePass == QuizzesComponent::STATUS_GRADE_PASS) {
			$ret .= $this->TitleIcon->titleIcon('/net_commons/img/title_icon/10_051_pass.svg');
		}
		$ret .= $this->_getScoreSummary($quiz, $summary);
		$ret .= '<br />';
		$ret .= $this->_getElapseTimeSummary($quiz, $summary);

		$ret .= '</span></small></div>';
		return $ret;
	}
/**
 * 採点画面のヘッダ（得点部
 *
 * @param array $quiz 小テストデータ
 * @param array $summary 回答まとめ
 * @return string header描画HTML
 */
	protected function _getScoreSummary($quiz, $summary) {
		$ret = sprintf(
			__d('quizzes', 'Score %d'), // 得点%d点
			$summary['QuizAnswerSummary']['summary_score']
		);
		if ($summary['QuizAnswerSummary']['is_grade_finished'] == false) {
			$notScoring = Hash::extract(
				$summary,
				'QuizAnswer.{n}[correct_status=' . QuizzesComponent::STATUS_GRADE_YET . ']'
			);
			$notScorePoint = 0;
			foreach ($notScoring as $not) {
				$notScoreQuestion = Hash::extract(
					$quiz['QuizPage'],
					'{n}.QuizQuestion.{n}[key=' . $not['quiz_question_key'] . ']'
				);
				foreach ($notScoreQuestion as $question) {
					$notScorePoint += $question['allotment'];
				}
			}
			// + 未採点分が%d点あります
			$ret .= sprintf(
				__d('quizzes', ' + There are ungraded question %d points.'),
				$notScorePoint
			);
		}
		return $ret;
	}
/**
 * 採点画面のヘッダ（時間部
 *
 * @param array $quiz 小テストデータ
 * @param array $summary 回答まとめ
 * @return string header描画HTML
 */
	protected function _getElapseTimeSummary($quiz, $summary) {
		$elapsedSec = $summary['QuizAnswerSummary']['elapsed_second'];
		$elapsedMin = $elapsedSec / 60;
		$ret = sprintf(
			__d('quizzes', 'Elapsed : %d min.'), // 解答にかかった時間：%d分
			$elapsedMin
		);
		if ($quiz['Quiz']['estimated_time'] > 0 && $quiz['Quiz']['estimated_time'] * 60 < $elapsedSec) {
			$ret .= '<span class="text-danger">';
			$ret .= sprintf(
				__d('quizzes', '  Overtime %d min.'), //   %d分オーバーです
				($elapsedSec - $quiz['Quiz']['estimated_time'] * 60) / 60);
			$ret .= '</span>';
		}
		return $ret;
	}

}