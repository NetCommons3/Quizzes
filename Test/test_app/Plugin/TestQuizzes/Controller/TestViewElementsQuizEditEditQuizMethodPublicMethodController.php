<?php
/**
 * View/Elements/QuizEdit/Edit/quiz_method/public_methodテスト用Controller
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('AppController', 'Controller');

/**
 * View/Elements/QuizEdit/Edit/quiz_method/public_methodテスト用Controller
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\test_app\Plugin\TestQuizzes\Controller
 */
class TestViewElementsQuizEditEditQuizMethodPublicMethodController extends AppController {

/**
 * public_method
 *
 * @return void
 */
	public function public_method() {
		$this->autoRender = true;
	}

}
