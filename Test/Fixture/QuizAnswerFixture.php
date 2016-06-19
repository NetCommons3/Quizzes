<?php
/**
 * QuizAnswerFixture
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

/**
 * Summary for QuizAnswerFixture
 */
class QuizAnswerFixture extends CakeTestFixture {

/**
 * Import
 *
 * @var array
 */
	public $import = array('connection' => 'master');

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'answer_value' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '回答した文字列を設定する\\n選択肢、択一の場合は、選択肢のid値:ラベルを入れる\\n\\n複数選択肢\\nこれらの場合は、(id値):(ラベル)を|つなぎで並べる。\\n', 'charset' => 'utf8'),
		'answer_correct_status' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'correct_status' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => '0:未採点 1:正解 2:不正解'),
		'score' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false),
		'quiz_answer_summary_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index'),
		'quiz_question_key' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'created_user' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified_user' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'fk_quiz_answer_quiz_answer_summary1_idx' => array('column' => 'quiz_answer_summary_id', 'unique' => 0)
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
			'id' => '1',
			'answer_value' => '新規選択肢1',
			'answer_correct_status' => '2',
			'correct_status' => '2',
			'score' => '10',
			'quiz_answer_summary_id' => '1',
			'quiz_question_key' => '1804ecc7feac3c3dfde61d586458cc3d',
			'created_user' => '1',
			'created' => '2016-06-07 01:36:31',
			'modified_user' => '1',
			'modified' => '2016-06-07 01:36:36'
		),
		array(
			'id' => '2',
			'answer_value' => '新規選択肢1#||||||#新規選択肢3',
			'answer_correct_status' => '2#||||||#2',
			'correct_status' => '2',
			'score' => '10',
			'quiz_answer_summary_id' => '2',
			'quiz_question_key' => '3894de2dca96864ef9f50a77122a3aeb',
			'created_user' => '1',
			'created' => '2016-06-07 01:38:11',
			'modified_user' => '1',
			'modified' => '2016-06-07 01:38:15'
		),
		array(
			'id' => '3',
			'answer_value' => '',
			'answer_correct_status' => '1',
			'correct_status' => '1',
			'score' => '0',
			'quiz_answer_summary_id' => '3',
			'quiz_question_key' => '3894de2dca96864ef9f50a77122a3aeb',
			'created_user' => '1',
			'created' => '2016-06-07 01:42:00',
			'modified_user' => '1',
			'modified' => '2016-06-07 01:42:05'
		),
		array(
			'id' => '4',
			'answer_value' => '新規選択肢3',
			'answer_correct_status' => '2',
			'correct_status' => '1',
			'score' => '0',
			'quiz_answer_summary_id' => '4',
			'quiz_question_key' => '3894de2dca96864ef9f50a77122a3aeb',
			'created_user' => '1',
			'created' => '2016-06-07 02:24:02',
			'modified_user' => '1',
			'modified' => '2016-06-07 02:24:07'
		),
		array(
			'id' => '5',
			'answer_value' => '新規選択肢1',
			'answer_correct_status' => '2',
			'correct_status' => '2',
			'score' => '10',
			'quiz_answer_summary_id' => '5',
			'quiz_question_key' => '4b2c1dd06cfe4c4654c1a98852d5e485',
			'created_user' => null,
			'created' => '2016-06-08 07:03:55',
			'modified_user' => null,
			'modified' => '2016-06-08 07:03:59'
		),
		array(
			'id' => '6',
			'answer_value' => 'eeeee',
			'answer_correct_status' => '',
			'correct_status' => '0',
			'score' => '0',
			'quiz_answer_summary_id' => '5',
			'quiz_question_key' => '8d65363cd807f75f11c60b32156a4daa',
			'created_user' => null,
			'created' => '2016-06-08 07:03:55',
			'modified_user' => null,
			'modified' => '2016-06-08 07:03:55'
		),
		array(
			'id' => '7',
			'answer_value' => '新規選択肢1',
			'answer_correct_status' => '2',
			'correct_status' => '2',
			'score' => '10',
			'quiz_answer_summary_id' => '6',
			'quiz_question_key' => '4b2c1dd06cfe4c4654c1a98852d5e485',
			'created_user' => '4',
			'created' => '2016-06-08 07:51:39',
			'modified_user' => '4',
			'modified' => '2016-06-08 07:51:44'
		),
		array(
			'id' => '8',
			'answer_value' => 'ddddd',
			'answer_correct_status' => '',
			'correct_status' => '0',
			'score' => '0',
			'quiz_answer_summary_id' => '6',
			'quiz_question_key' => '8d65363cd807f75f11c60b32156a4daa',
			'created_user' => '4',
			'created' => '2016-06-08 07:51:39',
			'modified_user' => '4',
			'modified' => '2016-06-08 07:51:39'
		),
		array(
			'id' => '9',
			'answer_value' => '新規選択肢3',
			'answer_correct_status' => '1',
			'correct_status' => '1',
			'score' => '0',
			'quiz_answer_summary_id' => '7',
			'quiz_question_key' => '4b2c1dd06cfe4c4654c1a98852d5e485',
			'created_user' => null,
			'created' => '2016-06-08 08:40:45',
			'modified_user' => null,
			'modified' => '2016-06-08 08:40:49'
		),
		array(
			'id' => '10',
			'answer_value' => 'fffffffffffffffff',
			'answer_correct_status' => '',
			'correct_status' => '0',
			'score' => '0',
			'quiz_answer_summary_id' => '7',
			'quiz_question_key' => '8d65363cd807f75f11c60b32156a4daa',
			'created_user' => null,
			'created' => '2016-06-08 08:40:45',
			'modified_user' => null,
			'modified' => '2016-06-08 08:40:45'
		),
		array(
			'id' => '11',
			'answer_value' => '新規選択肢3',
			'answer_correct_status' => '',
			'correct_status' => '0',
			'score' => '0',
			'quiz_answer_summary_id' => '8',
			'quiz_question_key' => '4290bdcbb150a3edb4dd329661dc8e0b',
			'created_user' => null,
			'created' => '2016-06-08 08:41:58',
			'modified_user' => null,
			'modified' => '2016-06-08 08:41:58'
		),
		array(
			'id' => '12',
			'answer_value' => '新規選択肢2',
			'answer_correct_status' => '1',
			'correct_status' => '1',
			'score' => '0',
			'quiz_answer_summary_id' => '9',
			'quiz_question_key' => '1fdbc818666cbe5a050be98e349a875c',
			'created_user' => '1',
			'created' => '2016-06-08 08:42:43',
			'modified_user' => '1',
			'modified' => '2016-06-08 08:42:46'
		),
		array(
			'id' => '13',
			'answer_value' => 'rrrrrrrrr',
			'answer_correct_status' => '',
			'correct_status' => '0',
			'score' => '0',
			'quiz_answer_summary_id' => '9',
			'quiz_question_key' => '0de078c6cca22484e0084ac6a8c7afe9',
			'created_user' => '1',
			'created' => '2016-06-08 08:42:43',
			'modified_user' => '1',
			'modified' => '2016-06-08 08:42:43'
		),
		array(
			'id' => '14',
			'answer_value' => '新規選択肢1',
			'answer_correct_status' => '2',
			'correct_status' => '2',
			'score' => '10',
			'quiz_answer_summary_id' => '10',
			'quiz_question_key' => '1fdbc818666cbe5a050be98e349a875c',
			'created_user' => null,
			'created' => '2016-06-09 09:27:51',
			'modified_user' => null,
			'modified' => '2016-06-09 09:27:55'
		),
		array(
			'id' => '15',
			'answer_value' => '',
			'answer_correct_status' => '',
			'correct_status' => '0',
			'score' => '0',
			'quiz_answer_summary_id' => '10',
			'quiz_question_key' => '0de078c6cca22484e0084ac6a8c7afe9',
			'created_user' => null,
			'created' => '2016-06-09 09:27:51',
			'modified_user' => null,
			'modified' => '2016-06-09 09:27:51'
		),
		array(
			'id' => '16',
			'answer_value' => '新規選択肢1',
			'answer_correct_status' => '2',
			'correct_status' => '2',
			'score' => '10',
			'quiz_answer_summary_id' => '11',
			'quiz_question_key' => '0984f470eb7a6453b8ed8f9602fa8744',
			'created_user' => null,
			'created' => '2016-06-10 06:20:46',
			'modified_user' => null,
			'modified' => '2016-06-10 06:20:53'
		),
		array(
			'id' => '17',
			'answer_value' => '新規選択肢3',
			'answer_correct_status' => '1',
			'correct_status' => '1',
			'score' => '0',
			'quiz_answer_summary_id' => '12',
			'quiz_question_key' => '0984f470eb7a6453b8ed8f9602fa8744',
			'created_user' => null,
			'created' => '2016-06-10 06:21:32',
			'modified_user' => null,
			'modified' => '2016-06-10 06:21:39'
		),
		array(
			'id' => '18',
			'answer_value' => '',
			'answer_correct_status' => '1',
			'correct_status' => '1',
			'score' => '0',
			'quiz_answer_summary_id' => '13',
			'quiz_question_key' => '0984f470eb7a6453b8ed8f9602fa8744',
			'created_user' => null,
			'created' => '2016-06-10 06:23:21',
			'modified_user' => null,
			'modified' => '2016-06-10 06:23:32'
		),
		array(
			'id' => '19',
			'answer_value' => '',
			'answer_correct_status' => '1',
			'correct_status' => '1',
			'score' => '0',
			'quiz_answer_summary_id' => '15',
			'quiz_question_key' => '6594db1a6175375e8c64db3288ca4bdb',
			'created_user' => null,
			'created' => '2016-06-10 06:25:02',
			'modified_user' => null,
			'modified' => '2016-06-10 06:25:08'
		),
		array(
			'id' => '20',
			'answer_value' => '新規選択肢2#||||||#新規選択肢3',
			'answer_correct_status' => '2#||||||#1',
			'correct_status' => '1',
			'score' => '0',
			'quiz_answer_summary_id' => '17',
			'quiz_question_key' => '6594db1a6175375e8c64db3288ca4bdb',
			'created_user' => null,
			'created' => '2016-06-10 06:26:01',
			'modified_user' => null,
			'modified' => '2016-06-10 06:26:07'
		),
		array(
			'id' => '21',
			'answer_value' => '新規選択肢1#||||||#新規選択肢2',
			'answer_correct_status' => '2#||||||#2',
			'correct_status' => '2',
			'score' => '10',
			'quiz_answer_summary_id' => '18',
			'quiz_question_key' => '6594db1a6175375e8c64db3288ca4bdb',
			'created_user' => null,
			'created' => '2016-06-10 06:26:41',
			'modified_user' => null,
			'modified' => '2016-06-10 06:26:47'
		),
		array(
			'id' => '22',
			'answer_value' => '',
			'answer_correct_status' => '1',
			'correct_status' => '1',
			'score' => '0',
			'quiz_answer_summary_id' => '19',
			'quiz_question_key' => 'd57779bc6eec5710d711881050d825b5',
			'created_user' => null,
			'created' => '2016-06-10 06:30:05',
			'modified_user' => null,
			'modified' => '2016-06-10 06:30:12'
		),
		array(
			'id' => '23',
			'answer_value' => 'aa',
			'answer_correct_status' => '2',
			'correct_status' => '2',
			'score' => '10',
			'quiz_answer_summary_id' => '20',
			'quiz_question_key' => 'd57779bc6eec5710d711881050d825b5',
			'created_user' => null,
			'created' => '2016-06-10 06:31:17',
			'modified_user' => null,
			'modified' => '2016-06-10 06:31:24'
		),
		array(
			'id' => '24',
			'answer_value' => 'AAbb',
			'answer_correct_status' => '1',
			'correct_status' => '1',
			'score' => '0',
			'quiz_answer_summary_id' => '21',
			'quiz_question_key' => 'd57779bc6eec5710d711881050d825b5',
			'created_user' => null,
			'created' => '2016-06-10 06:31:52',
			'modified_user' => null,
			'modified' => '2016-06-10 06:31:58'
		),
		array(
			'id' => '25',
			'answer_value' => 'ああ',
			'answer_correct_status' => '2',
			'correct_status' => '2',
			'score' => '10',
			'quiz_answer_summary_id' => '22',
			'quiz_question_key' => 'd57779bc6eec5710d711881050d825b5',
			'created_user' => null,
			'created' => '2016-06-10 06:32:25',
			'modified_user' => null,
			'modified' => '2016-06-10 06:32:33'
		),
		array(
			'id' => '26',
			'answer_value' => 'ああ#||||||#いい#||||||#うう',
			'answer_correct_status' => '2#||||||#1#||||||#1',
			'correct_status' => '1',
			'score' => '0',
			'quiz_answer_summary_id' => '23',
			'quiz_question_key' => 'ca5816303caf3a27bad5a4754c75c40e',
			'created_user' => null,
			'created' => '2016-06-10 06:33:07',
			'modified_user' => null,
			'modified' => '2016-06-10 06:33:13'
		),
		array(
			'id' => '27',
			'answer_value' => 'cc#||||||#bb#||||||#AA',
			'answer_correct_status' => '2#||||||#2#||||||#2',
			'correct_status' => '2',
			'score' => '10',
			'quiz_answer_summary_id' => '24',
			'quiz_question_key' => 'ca5816303caf3a27bad5a4754c75c40e',
			'created_user' => null,
			'created' => '2016-06-10 06:33:49',
			'modified_user' => null,
			'modified' => '2016-06-10 06:33:54'
		),
		array(
			'id' => '28',
			'answer_value' => '#||||||##||||||#',
			'answer_correct_status' => '1#||||||#1#||||||#1',
			'correct_status' => '1',
			'score' => '0',
			'quiz_answer_summary_id' => '25',
			'quiz_question_key' => 'ca5816303caf3a27bad5a4754c75c40e',
			'created_user' => null,
			'created' => '2016-06-10 06:34:21',
			'modified_user' => null,
			'modified' => '2016-06-10 06:34:27'
		),
		array(
			'id' => '29',
			'answer_value' => '',
			'answer_correct_status' => '',
			'correct_status' => '0',
			'score' => '0',
			'quiz_answer_summary_id' => '26',
			'quiz_question_key' => '9cc4e8ba1f575fb349e74c5f958c4a69',
			'created_user' => null,
			'created' => '2016-06-10 06:34:57',
			'modified_user' => null,
			'modified' => '2016-06-10 06:34:57'
		),
		array(
			'id' => '30',
			'answer_value' => '正解で部分点でお願いします',
			'answer_correct_status' => '',
			'correct_status' => '2',
			'score' => '8',
			'quiz_answer_summary_id' => '27',
			'quiz_question_key' => '9cc4e8ba1f575fb349e74c5f958c4a69',
			'created_user' => null,
			'created' => '2016-06-10 06:36:41',
			'modified_user' => '1',
			'modified' => '2016-06-10 06:41:42'
		),
		array(
			'id' => '31',
			'answer_value' => '正解で満点でお願いします',
			'answer_correct_status' => '',
			'correct_status' => '2',
			'score' => '10',
			'quiz_answer_summary_id' => '28',
			'quiz_question_key' => '9cc4e8ba1f575fb349e74c5f958c4a69',
			'created_user' => null,
			'created' => '2016-06-10 06:37:15',
			'modified_user' => '1',
			'modified' => '2016-06-10 06:42:23'
		),
		array(
			'id' => '32',
			'answer_value' => '間違い部分点でお願いします',
			'answer_correct_status' => '',
			'correct_status' => '1',
			'score' => '2',
			'quiz_answer_summary_id' => '29',
			'quiz_question_key' => '9cc4e8ba1f575fb349e74c5f958c4a69',
			'created_user' => null,
			'created' => '2016-06-10 06:38:01',
			'modified_user' => '1',
			'modified' => '2016-06-10 06:43:53'
		),
		array(
			'id' => '33',
			'answer_value' => '間違いで０点でお願いします',
			'answer_correct_status' => '',
			'correct_status' => '1',
			'score' => '0',
			'quiz_answer_summary_id' => '30',
			'quiz_question_key' => '9cc4e8ba1f575fb349e74c5f958c4a69',
			'created_user' => null,
			'created' => '2016-06-10 06:38:57',
			'modified_user' => '1',
			'modified' => '2016-06-10 06:40:14'
		),
		array(
			'id' => '34',
			'answer_value' => '未採点のままにしてください',
			'answer_correct_status' => '',
			'correct_status' => '0',
			'score' => '0',
			'quiz_answer_summary_id' => '31',
			'quiz_question_key' => '9cc4e8ba1f575fb349e74c5f958c4a69',
			'created_user' => '4',
			'created' => '2016-06-10 06:47:03',
			'modified_user' => '4',
			'modified' => '2016-06-10 06:47:03'
		),
		array(
			'id' => '35',
			'answer_value' => '正解満点でお願いします',
			'answer_correct_status' => '',
			'correct_status' => '2',
			'score' => '10',
			'quiz_answer_summary_id' => '32',
			'quiz_question_key' => '9cc4e8ba1f575fb349e74c5f958c4a69',
			'created_user' => '4',
			'created' => '2016-06-10 06:47:50',
			'modified_user' => '1',
			'modified' => '2016-06-10 06:51:42'
		),
		array(
			'id' => '36',
			'answer_value' => '間違い部分点でお願いします',
			'answer_correct_status' => '',
			'correct_status' => '1',
			'score' => '4',
			'quiz_answer_summary_id' => '33',
			'quiz_question_key' => '9cc4e8ba1f575fb349e74c5f958c4a69',
			'created_user' => '4',
			'created' => '2016-06-10 06:48:47',
			'modified_user' => '1',
			'modified' => '2016-06-10 06:51:09'
		),
		array(
			'id' => '37',
			'answer_value' => '新規選択肢1',
			'answer_correct_status' => '2',
			'correct_status' => '2',
			'score' => '10',
			'quiz_answer_summary_id' => '34',
			'quiz_question_key' => '3e656c320d940f9738f4593fa98da529',
			'created_user' => '1',
			'created' => '2016-06-07 01:36:31',
			'modified_user' => '1',
			'modified' => '2016-06-07 01:36:36'
		),
	);

}
