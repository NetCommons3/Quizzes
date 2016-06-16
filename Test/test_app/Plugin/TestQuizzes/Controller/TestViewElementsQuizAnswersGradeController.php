<?php
/**
 * View/Elements/QuizAnswers/gradeテスト用Controller
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('AppController', 'Controller');

/**
 * View/Elements/QuizAnswers/gradeテスト用Controller
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\test_app\Plugin\TestQuizzes\Controller
 */
class TestViewElementsQuizAnswersGradeController extends AppController {

/**
 * grade
 *
 * @return void
 */
	public function grade() {
		$this->autoRender = true;
	}

}
