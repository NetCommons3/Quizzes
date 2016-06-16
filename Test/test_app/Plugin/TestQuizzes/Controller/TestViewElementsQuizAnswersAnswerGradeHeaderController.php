<?php
/**
 * View/Elements/QuizAnswers/answer_grade_headerテスト用Controller
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('AppController', 'Controller');

/**
 * View/Elements/QuizAnswers/answer_grade_headerテスト用Controller
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\test_app\Plugin\TestQuizzes\Controller
 */
class TestViewElementsQuizAnswersAnswerGradeHeaderController extends AppController {

/**
 * answer_grade_header
 *
 * @return void
 */
	public function answer_grade_header() {
		$this->autoRender = true;
	}

}
