<?php
/**
 * View/Elements/QuizEdit/EditQuestion/hidden_page_info_setテスト用Controller
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('AppController', 'Controller');

/**
 * View/Elements/QuizEdit/EditQuestion/hidden_page_info_setテスト用Controller
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\test_app\Plugin\TestQuizzes\Controller
 */
class TestViewElementsQuizEditEditQuestionHiddenPageInfoSetController extends AppController {

/**
 * hidden_page_info_set
 *
 * @return void
 */
	public function hidden_page_info_set() {
		$this->autoRender = true;
	}

}
