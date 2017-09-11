<?php
/**
 * Quiz::fixtureから目的のデータを取得する
 *
 * @property Quiz $Quiz
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsCakeTestCase', 'NetCommons.TestSuite');
App::uses('QuizFixture', 'Quizzes.Test/Fixture');
App::uses('QuizPageFixture', 'Quizzes.Test/Fixture');
App::uses('QuizQuestionFixture', 'Quizzes.Test/Fixture');
App::uses('QuizChoiceFixture', 'Quizzes.Test/Fixture');
App::uses('QuizCorrectFixture', 'Quizzes.Test/Fixture');
App::uses('QuizzesComponent', 'Quizzes.Controller/Component');

/**
 * Quizテストのためのデータ取得
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Quizzes\TestSuite
 */
class QuizDataGetTest extends NetCommonsCakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.quizzes.quiz',
		'plugin.quizzes.quiz_page',
		'plugin.quizzes.quiz_question',
		'plugin.quizzes.quiz_choice',
		'plugin.quizzes.quiz_correct',
		'plugin.quizzes.quiz_answer_summary',
		'plugin.quizzes.quiz_answer',
		'plugin.quizzes.quiz_setting',
		'plugin.quizzes.quiz_frame_setting',
		'plugin.quizzes.quiz_frame_display_quiz',
		'plugin.workflow.workflow_comment',
	);

/**
 * テストDataの取得
 *
 * @param string $id quizId
 * @param string $status ステータス
 * @return array
 */
	public function getData($id = 2, $status = 0) {
		$fixtureQuiz = new QuizFixture();
		$rec = Hash::extract($fixtureQuiz->records, '{n}[id=' . $id . ']');
		$data['Quiz'] = $rec[0];
		if ($status != 0) {
			$data['Quiz']['status'] = $status;
		}

		$fixturePage = new QuizPageFixture();
		$rec = Hash::extract($fixturePage->records, '{n}[quiz_id=' . $data['Quiz']['id'] . ']');
		$data['QuizPage'] = $rec;

		$fixtureQuestion = new QuizQuestionFixture();
		$fixtureChoice = new QuizChoiceFixture();
		$fixtureCorrect = new QuizCorrectFixture();

		foreach ($data['QuizPage'] as $pIdx => $page) {
			$pageId = $page['id'];

			$rec = Hash::extract($fixtureQuestion->records, '{n}[quiz_page_id=' . $pageId . ']');
			$data['QuizPage'][$pIdx]['QuizQuestion'] = $rec;

			foreach ($data['QuizPage'][$pIdx]['QuizQuestion'] as $qIdx => $question) {
				$questionId = $question['id'];

				$rec = Hash::extract($fixtureChoice->records, '{n}[quiz_question_id=' . $questionId . ']');
				if ($rec) {
					$data['QuizPage'][$pIdx]['QuizQuestion'][$qIdx]['QuizChoice'] = $rec;
				}
				$rec = Hash::extract($fixtureCorrect->records, '{n}[quiz_question_id=' . $questionId . ']');
				foreach ($rec as &$r) {
					$r['correct'] = explode(QuizzesComponent::ANSWER_DELIMITER, $r['correct']);
				}
				if ($rec) {
					$data['QuizPage'][$pIdx]['QuizQuestion'][$qIdx]['QuizCorrect'] = $rec;
				}
			}
		}
		$data['Frame']['id'] = 6;
		return $data;
	}
}
