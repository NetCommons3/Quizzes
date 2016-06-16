<?php
/**
 * View/Elements/QuizAnswers/answer_test_mode_headerテスト用Controller
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('AppController', 'Controller');

/**
 * View/Elements/QuizAnswers/answer_test_mode_headerテスト用Controller
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\test_app\Plugin\TestQuizzes\Controller
 */
class TestViewElementsQuizAnswersAnswerTestModeHeaderController extends AppController {

/**
 * answer_test_mode_header
 *
 * @return void
 */
	public function answer_test_mode_header() {
		$this->autoRender = true;
	}

}
