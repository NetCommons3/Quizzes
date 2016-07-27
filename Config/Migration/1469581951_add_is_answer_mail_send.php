<?php
/**
 * Quizzes Migration file
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

/**
 * Quizzes Migration
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Config\Migration
 */
class AddIsAnswerMailSend extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_is_answer_mail_send';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_table' => array(
			),
			'drop_field' => array(
			),
			'create_field' => array(
				'quizzes' => array(
					'is_answer_mail_send' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'after' => 'is_total_show'),
				),
			),
		),
		'down' => array(
			'drop_table' => array(
			),
			'create_field' => array(
			),
			'drop_field' => array(
				'quizzes' => array('is_answer_mail_send'),
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
		return true;
	}
}
