<?php
/**
 * QuizFrameSetting Test Case
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Your Name <yourname@domain.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('QuizFrameSetting', 'Quizzes.Model');

/**
 * Summary for QuizFrameSetting Test Case
 */
class QuizFrameSettingTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.quizzes.quiz_frame_setting',
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
		$this->QuizFrameSetting = ClassRegistry::init('Quizzes.QuizFrameSetting');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->QuizFrameSetting);

		parent::tearDown();
	}

}
