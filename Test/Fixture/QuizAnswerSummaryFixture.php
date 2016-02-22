<?php
/**
 * QuizAnswerSummaryFixture
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

/**
 * Summary for QuizAnswerSummaryFixture
 */
class QuizAnswerSummaryFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'answer_status' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 4, 'unsigned' => false, 'comment' => '回答状態 1ページずつ表示するようなアンケートの場合、途中状態か否か | 0:回答未完了 | 1:回答完了'),
		'test_status' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 4, 'unsigned' => false, 'comment' => 'テスト時の回答かどうか 0:本番回答 | 1:テスト時回答'),
		'answer_number' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '回答回数　ログインして回答している人物の場合に限定して回答回数をカウントする'),
		'summary_score' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false),
		'answer_start_time' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '回答開始時刻 小テストのView画面を開いた時刻'),
		'answer_finish_time' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '回答完了時刻 確認ボタンをクリックした時刻'),
		'elapsed_time' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false, 'comment' => '回答にかかった時間(秒)'),
		'quiz_key' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'session_value' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'アンケート回答した時のセッション値を保存します。', 'charset' => 'utf8'),
		'user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'ログイン後、アンケートに回答した人のusersテーブルのid。未ログインの場合NULL'),
		'created_user' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified_user' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => 1,
			'answer_status' => 1,
			'test_status' => 1,
			'answer_number' => 1,
			'summary_score' => 1,
			'answer_start_time' => '2015-12-28 04:29:23',
			'answer_finish_time' => '2015-12-28 04:29:23',
			'elapsed_time' => 1,
			'quiz_key' => 'Lorem ipsum dolor sit amet',
			'session_value' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
			'user_id' => 1,
			'created_user' => 1,
			'created' => '2015-12-28 04:29:23',
			'modified_user' => 1,
			'modified' => '2015-12-28 04:29:23'
		),
	);

}
