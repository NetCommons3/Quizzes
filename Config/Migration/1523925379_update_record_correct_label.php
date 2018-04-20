<?php
/**
 * 複数単語の時の回答欄の見出しラベル追加
 *
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsMigration', 'NetCommons.Config/Migration');
/**
 * 複数単語の時の回答欄の見出しラベル追加
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Config\Migration
 */
class UpdateRecordCorrectLabel extends NetCommonsMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'update_record_correct_label';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
		),
		'down' => array(
		),
	);

/**
 * Before migration callback
 *
 * @param string $direction Direction of migration process (up or down)
 * @return bool Should process continue
 */
	public function before($direction) {
		return true;
	}

/**
 * After migration callback
 *
 * @param string $direction Direction of migration process (up or down)
 * @return bool Should process continue
 */
	public function after($direction) {
		if ($direction == 'down') {
			return true;
		}
		$this->QuizCorrect = $this->generateModel('QuizCorrect');
		$this->QuizCorrect->updateAll(
			array('QuizCorrect.correct_label' => 'QuizCorrect.correct_sequence + 1')
		);
		return true;
	}
}
