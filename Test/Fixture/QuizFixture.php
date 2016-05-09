<?php
/**
 * QuizFixture
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

/**
 * Summary for QuizFixture
 */
class QuizFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'key' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'language_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		'is_active' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '公開中データか否か'),
		'is_latest' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '最新編集データであるか否か'),
		'block_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index'),
		'status' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 4, 'unsigned' => false, 'comment' => 'public status, 1: public, 2: public pending, 3: draft during 4: remand | 公開状況  1:公開中、2:公開申請中、3:下書き中、4:差し戻し |'),
		'title' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '小テストタイトル', 'charset' => 'utf8'),
		'sub_title' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '小テストサブタイトル', 'charset' => 'utf8'),
		'icon_name' => array('type' => 'string', 'null' => false, 'length' => 128, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'answer_timing' => array('type' => 'integer', 'null' => false, 'default' => '1', 'length' => 4, 'unsigned' => false),
		'answer_start_period' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'answer_end_period' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'is_no_member_allow' => array('type' => 'boolean', 'null' => true, 'default' => '0', 'comment' => '非会員の回答を許可するか | 0:許可しない | 1:許可する'),
		'is_key_pass_use' => array('type' => 'boolean', 'null' => true, 'default' => '0', 'comment' => 'キーフレーズによる回答ガードを設けるか | 0:キーフレーズガードは用いない | 1:キーフレーズガードを用いる'),
		'is_repeat_allow' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'perfect_score' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => '満点'),
		'quiz_score' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => 'これまでの受験者の総得点'),
		'answer_count' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => 'これまでの回答数'),
		'is_correct_show' => array('type' => 'boolean', 'null' => true, 'default' => '1', 'comment' => '正解を表示するか否か | 0:表示しない | 1:表示する'),
		'is_total_show' => array('type' => 'boolean', 'null' => true, 'default' => '1', 'comment' => '集計結果を表示するか否か | 0:表示しない | 1:表示する'),
		'total_comment' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '結果表示ページの先頭に書くメッセージコメント', 'charset' => 'utf8'),
		'is_image_authentication' => array('type' => 'boolean', 'null' => true, 'default' => '0', 'comment' => 'SPAMガード項目を表示するか否か | 0:表示しない | 1:表示する'),
		'is_page_random' => array('type' => 'boolean', 'null' => true, 'default' => '0', 'comment' => 'ページ表示順序ランダム化'),
		'import_key' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'export_key' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'created_user' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified_user' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'fk_quizzes_blocks1_idx' => array('column' => 'block_id', 'unique' => 0)
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
			'key' => 'Lorem ipsum dolor sit amet',
			'language_id' => 1,
			'is_active' => 1,
			'is_latest' => 1,
			'block_id' => 1,
			'status' => 1,
			'title' => 'Lorem ipsum dolor sit amet',
			'sub_title' => 'Lorem ipsum dolor sit amet',
			'icon_name' => 'Lorem ipsum dolor sit amet',
			'answer_timing' => 1,
			'answer_start_period' => '2015-12-28 03:54:25',
			'answer_end_period' => '2015-12-28 03:54:25',
			'is_no_member_allow' => 1,
			'is_key_pass_use' => 1,
			'is_repeat_allow' => 1,
			'perfect_score' => 1,
			'quiz_score' => 1,
			'answer_count' => 1,
			'is_correct_show' => 1,
			'is_total_show' => 1,
			'total_comment' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
			'is_image_authentication' => 1,
			'is_page_random' => 1,
			'import_key' => 'Lorem ipsum dolor sit amet',
			'export_key' => 'Lorem ipsum dolor sit amet',
			'created_user' => 1,
			'created' => '2015-12-28 03:54:25',
			'modified_user' => 1,
			'modified' => '2015-12-28 03:54:25'
		),
	);

}
