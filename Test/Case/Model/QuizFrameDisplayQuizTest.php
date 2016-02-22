<?php
/**
 * QuizFrameDisplayQuiz Test Case
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('QuizFrameDisplayQuiz', 'Quizzes.Model');

/**
 * Summary for QuizFrameDisplayQuiz Test Case
 */
class QuizFrameDisplayQuizTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.quizzes.quiz_frame_display_quiz',
		'plugin.quizzes.user',
		'plugin.quizzes.role',
		'plugin.quizzes.language',
		'plugin.quizzes.plugin',
		'plugin.quizzes.plugins_role',
		'plugin.quizzes.room',
		'plugin.quizzes.space',
		'plugin.quizzes.rooms_language',
		'plugin.quizzes.roles_room',
		'plugin.quizzes.block_role_permission',
		'plugin.quizzes.room_role_permission',
		'plugin.quizzes.roles_rooms_user',
		'plugin.quizzes.user_role_setting',
		'plugin.quizzes.users_language'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->QuizFrameDisplayQuiz = ClassRegistry::init('Quizzes.QuizFrameDisplayQuiz');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->QuizFrameDisplayQuiz);

		parent::tearDown();
	}

}
