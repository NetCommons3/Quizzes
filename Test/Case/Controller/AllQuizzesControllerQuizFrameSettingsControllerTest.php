<?php
/**
 * All QuizFrameSettingsController Test suite
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsTestSuite', 'NetCommons.TestSuite');

/**
 * All QuizFrameSettingsController Test suite
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\Case\QuizFrameSettingsController
 */
class AllQuizzesControllerQuizFrameSettingsControllerTest extends NetCommonsTestSuite {

/**
 * All QuizFrameSettingsController Test suite
 *
 * @return NetCommonsTestSuite
 * @codeCoverageIgnore
 */
	public static function suite() {
		$name = preg_replace('/^All([\w]+)Test$/', '$1', __CLASS__);
		$suite = new NetCommonsTestSuite(sprintf('All %s tests', $name));
		$suite->addTestDirectoryRecursive(__DIR__ . DS . 'QuizFrameSettingsController');
		return $suite;
	}

}
