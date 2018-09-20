<?php
/**
 * QuizBehavior Behavior
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('ModelBehavior', 'Model');

/**
 * QuizBehavior Behavior
 *
 * @package  Quizzes\Quizzes\Model\Befavior\QuizBehavior
 * @author Allcreator <info@allcreator.net>
 */
class QuizBehavior extends ModelBehavior {

/**
 * 与えられたQuizの配列データからIDとKey情報を抜き出して、それぞれの配列データを返す
 *
 * @param Model $model モデル
 * @param array $quizzes Quiz配列情報
 * @return array
 */
	public function getQuizIdsAndKeys(Model $model, $quizzes) {
		$ids = array();
		$keys = array();
		foreach ($quizzes as $quiz) {
			if (!isset($quiz['Quiz'])) {
				continue;
			}
			// この場合はcount
			if (! isset($quiz['Quiz']['id'])) {
				continue;
			}
			// この場合はdelete
			if (! isset($quiz['Quiz']['key'])) {
				continue;
			}
			// この場合はlist取得
			if (! isset($quiz['Quiz']['answer_timing'])) {
				continue;
			}
			$ids[] = $quiz['Quiz']['id'];
			$keys[] = $quiz['Quiz']['key'];
		}
		return array($ids, $keys);
	}

/**
 * 与えられたQuizの配列ID配列から合致するPage情報を抜き出して、IDとPage配列データを返す
 *
 * @param Model $model モデル
 * @param array $quizIds QuizIDの配列
 * @return array
 */
	public function getQuizPageIdsAndPages(Model $model, $quizIds) {
		$ids = array();
		$pages = array();

		if (empty($quizIds)) {
			return array($ids, $pages);
		}

		// Quiz.idの配列から対応するQuizPageの配列を取得
		$quizPages = $model->QuizPage->find('all', array(
			'conditions' => array(
				'QuizPage.quiz_id' => $quizIds,
			),
			'order' => array(
				'QuizPage.quiz_id ASC',
				'QuizPage.page_sequence ASC'
			),
			'recursive' => -1
		));

		foreach ($quizPages as $page) {
			$ids[] = $page['QuizPage']['id'];
			$pages[$page['QuizPage']['quiz_id']][$page['QuizPage']['page_sequence']] = $page['QuizPage'];
		}
		return array($ids, $pages);
	}

/**
 * 与えられたQuizPageの配列ID配列から合致するQuestion情報を抜き出して、Question配列データを返す
 *
 * @param Model $model モデル
 * @param array $quizPageIds QuizPageIDの配列
 * @return array
 */
	public function getQuizQuestions(Model $model, $quizPageIds) {
		$questions = array();

		if (empty($quizPageIds)) {
			return $questions;
		}

		// QuizPage.idの配列から対応するQuizQuestionの配列を取得
		$quizQuestions = $model->QuizQuestion->find('all', array(
			'conditions' => array(
				'QuizQuestion.quiz_page_id' => $quizPageIds,
			),
			'order' => array(
				'QuizQuestion.quiz_page_id ASC',
				'QuizQuestion.question_sequence ASC'
			),
		));
		foreach ($quizQuestions as &$q) {
			$pageId = $q['QuizQuestion']['quiz_page_id'];
			$seqNo = $q['QuizQuestion']['question_sequence'];
			$q['QuizQuestion']['QuizChoice'] = $q['QuizChoice'];
			$q['QuizQuestion']['QuizCorrect'] = $q['QuizCorrect'];
			$questions[$pageId][$seqNo] = $q['QuizQuestion'];
		}
		return $questions;
	}
/**
 * 与えられたQuizのKEY配列から合致する回答数情報を抜き出して、回答数配列データを返す
 *
 * @param Model $model モデル
 * @param array $quizKeys QuizKEYの配列
 * @return array
 */
	public function getQuizAnswerCounts(Model $model, $quizKeys) {
		$answerCounts = array();

		if (empty($quizKeys)) {
			return $answerCounts;
		}

		// Quiz.idの配列から対応するQuizAnswerCountの配列を取得
		$model->QuizAnswerSummary->virtualFields = array('all_answer_count' => 'COUNT(quiz_key)');
		$quizAnswerCts = $model->QuizAnswerSummary->find('all', array(
			'fields' => array(
				'QuizAnswerSummary.quiz_key',
				'QuizAnswerSummary.all_answer_count'
			),
			'conditions' => array(
				'QuizAnswerSummary.quiz_key' => $quizKeys,
				'QuizAnswerSummary.answer_status' => QuizzesComponent::ACTION_ACT,
				'QuizAnswerSummary.test_status' => QuizzesComponent::TEST_ANSWER_STATUS_PEFORM
			),
			'group' => 'QuizAnswerSummary.quiz_key',
			'recursive' => -1
		));
		$model->QuizAnswerSummary->virtualFields = array();

		foreach ($quizAnswerCts as $cts) {
			$quizKey = $cts['QuizAnswerSummary']['quiz_key'];
			$answerCounts[$quizKey] = $cts['QuizAnswerSummary']['all_answer_count'];
		}
		return $answerCounts;
	}

}