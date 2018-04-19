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
class AddCorrectLabel extends NetCommonsMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_correct_label';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'quiz_corrects' => array(
					'correct_label' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '複数単語の場合に使用。見出しラベル', 'charset' => 'utf8', 'after' => 'correct_sequence'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'quiz_corrects' => array('correct_label'),
			),
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
		$this->loadModels([
			'QuizCorrect' => 'Quizzes.QuizCorrect',
		]);
		$this->QuizCorrect->updateAll(
			array('QuizCorrect.correct_label' => 'QuizCorrect.correct_sequence + 1')
		);
		return true;
	}
}
