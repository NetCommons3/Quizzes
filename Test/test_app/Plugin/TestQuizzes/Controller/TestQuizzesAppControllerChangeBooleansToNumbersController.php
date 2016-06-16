<?php
/**
 * QuizzesAppController::changeBooleansToNumbers()テスト用Controller
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('QuizzesAppController', 'Quizzes.Controller');

/**
 * QuizzesAppController::changeBooleansToNumbers()テスト用Controller
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\test_app\Plugin\TestQuizzes\Controller
 */
class TestQuizzesAppControllerChangeBooleansToNumbersController extends QuizzesAppController {

/**
 * changeBooleansToNumbers
 *
 * @return void
 */
	public function changeBooleansToNumbers() {
		$this->autoRender = true;
	}

}
