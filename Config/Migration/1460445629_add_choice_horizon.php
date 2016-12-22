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
class AddChoiceHorizon extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_choice_horizon';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_table' => array(
			),
			'alter_field' => array(
				'quiz_answer_summaries' => array(
					'answer_status' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 4, 'unsigned' => false, 'comment' => '途中状態か否か | 0:回答未完了 | 1:回答完了確認待ち | 2:確認完了'),
					'summary_score' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => '得点'),
					'passing_status' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => '0:合格判定なし 1:合格 2:不合格'),
					'within_time_status' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => '0:時間判定なし 1:時間内 2:時間オーバー'),
				),
				'quiz_answers' => array(
					'correct_status' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => '0:未採点 1:不正解 2:正解'),
				),
			),
			'create_field' => array(
				'quiz_questions' => array(
					'is_choice_horizon' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'after' => 'is_choice_random'),
				),
			),
		),
		'down' => array(
			'drop_table' => array(
			),
			'alter_field' => array(
				'quiz_answer_summaries' => array(
					'answer_status' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 4, 'unsigned' => false, 'comment' => '途中状態か否か | 0:回答未完了 | 1:回答完了確認待ち | 2:完了'),
					'summary_score' => array('type' => 'integer', 'null' => false, 'default' => 0, 'unsigned' => false, 'comment' => '得点'),
					'passing_status' => array('type' => 'integer', 'null' => false, 'default' => '0', 'comment' => '0:合格判定なし 1:不合格 2:合格 '),
					'within_time_status' => array('type' => 'integer', 'null' => false, 'default' => '0', 'comment' => '0:時間判定なし 1:時間オーバー 2:時間内 '),
				),
				'quiz_answers' => array(
					'correct_status' => array('type' => 'integer', 'null' => false, 'default' => 0, 'comment' => '0:未採点 1:不正解 2:正解'),
				),
			),
			'drop_field' => array(
				'quiz_questions' => array('is_choice_horizon'),
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
