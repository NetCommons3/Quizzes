<?php
/**
 * QuizAnswerSummary Test Case
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('QuizAnswerSummary', 'Quizzes.Model');

/**
 * Summary for QuizAnswerSummary Test Case
 */
class QuizAnswerSummaryTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.quizzes.quiz_answer_summary',
		'plugin.quizzes.user',
		'plugin.quizzes.quiz_answer'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->QuizAnswerSummary = ClassRegistry::init('Quizzes.QuizAnswerSummary');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->QuizAnswerSummary);

		parent::tearDown();
	}

}
