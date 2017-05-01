<?php
/**
 * QuizzesOwnAnswerComponentテスト用Controller
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('AppController', 'Controller');

/**
 * QuizzesOwnAnswerComponentテスト用Controller
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\test_app\Plugin\TestQuizzes\Controller
 */
class TestQuizzesOwnAnswerComponentController extends AppController {

/**
 * 使用コンポーネント
 *
 * @var array
 */
	public $components = array(
		'Quizzes.QuizzesOwnAnswer'
	);

/**
 * index
 *
 * @return void
 */
	public function index() {
		$this->autoRender = true;
	}
/**
 * index_with_login
 *
 * @return void
 */
	public function index_with_login() {
		$this->autoRender = true;
		$this->view = 'index';
	}

}
