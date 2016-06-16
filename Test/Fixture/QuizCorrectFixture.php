<?php
/**
 * QuizCorrectFixture
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

/**
 * Summary for QuizCorrectFixture
 */
class QuizCorrectFixture extends CakeTestFixture {

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
		'key' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'language_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index'),
		'correct_sequence' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'comment' => '順番'),
		'correct' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '正解', 'charset' => 'utf8'),
		'quiz_question_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index'),
		'created_user' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified_user' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'fk_quiz_correct_quiz_question1_idx' => array('column' => 'quiz_question_id', 'unique' => 0),
			'fk_quiz_correct_languages1_idx' => array('column' => 'language_id', 'unique' => 0)
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
			'key' => '60b4818a6c4f1a8f2b6c6d013bceb851',
			'language_id' => '2',
			'correct_sequence' => '0',
			'correct' => '新規選択肢1',
			'quiz_question_id' => '1',
			'created_user' => '1',
			'created' => '2016-06-06 23:12:46',
			'modified_user' => '1',
			'modified' => '2016-06-06 23:12:46'
		),
		array(
			'id' => '2',
			'key' => '9a2de3f7209104757e2b2028aab4379c',
			'language_id' => '2',
			'correct_sequence' => '0',
			'correct' => '新規選択肢1#||||||#新規選択肢3',
			'quiz_question_id' => '2',
			'created_user' => '1',
			'created' => '2016-06-07 01:37:49',
			'modified_user' => '1',
			'modified' => '2016-06-07 01:37:49'
		),
		array(
			'id' => '3',
			'key' => 'aafe3cb72d18cb5ee12e1be4ab1d571f',
			'language_id' => '2',
			'correct_sequence' => '0',
			'correct' => '新規選択肢1',
			'quiz_question_id' => '3',
			'created_user' => '1',
			'created' => '2016-06-08 07:03:18',
			'modified_user' => '1',
			'modified' => '2016-06-08 07:03:18'
		),
		array(
			'id' => '4',
			'key' => '0db89656af3bce88b926cfb57845e586',
			'language_id' => '2',
			'correct_sequence' => '0',
			'correct' => '新規選択肢1',
			'quiz_question_id' => '5',
			'created_user' => '1',
			'created' => '2016-06-08 08:41:38',
			'modified_user' => '1',
			'modified' => '2016-06-08 08:41:38'
		),
		array(
			'id' => '5',
			'key' => 'eb0f1744bad6924a76feb031f2170fda',
			'language_id' => '2',
			'correct_sequence' => '0',
			'correct' => '新規選択肢1',
			'quiz_question_id' => '6',
			'created_user' => '1',
			'created' => '2016-06-08 08:42:26',
			'modified_user' => '1',
			'modified' => '2016-06-08 08:42:26'
		),
		array(
			'id' => '6',
			'key' => '5c8434984642102bf8e3e9d4540a2395',
			'language_id' => '2',
			'correct_sequence' => '0',
			'correct' => '新規選択肢1',
			'quiz_question_id' => '8',
			'created_user' => '1',
			'created' => '2016-06-10 01:30:03',
			'modified_user' => '1',
			'modified' => '2016-06-10 01:30:03'
		),
		array(
			'id' => '7',
			'key' => '3324c64b73023725601c108f298ede48',
			'language_id' => '2',
			'correct_sequence' => '0',
			'correct' => '新規選択肢1',
			'quiz_question_id' => '9',
			'created_user' => '1',
			'created' => '2016-06-10 01:30:36',
			'modified_user' => '1',
			'modified' => '2016-06-10 01:30:36'
		),
		array(
			'id' => '8',
			'key' => 'bb2a2e919feceafdfa7707c303197dff',
			'language_id' => '2',
			'correct_sequence' => '0',
			'correct' => '新規選択肢1',
			'quiz_question_id' => '10',
			'created_user' => '1',
			'created' => '2016-06-10 01:31:18',
			'modified_user' => '1',
			'modified' => '2016-06-10 01:31:18'
		),
		array(
			'id' => '9',
			'key' => '87baad7ad6fccb89be2e5d2533d78e73',
			'language_id' => '2',
			'correct_sequence' => '0',
			'correct' => '新規選択肢1',
			'quiz_question_id' => '11',
			'created_user' => '1',
			'created' => '2016-06-10 01:34:14',
			'modified_user' => '1',
			'modified' => '2016-06-10 01:34:14'
		),
		array(
			'id' => '10',
			'key' => '96ae82c194363cc9c600b532e6e0a622',
			'language_id' => '2',
			'correct_sequence' => '0',
			'correct' => '新規選択肢1',
			'quiz_question_id' => '12',
			'created_user' => '1',
			'created' => '2016-06-10 01:35:04',
			'modified_user' => '1',
			'modified' => '2016-06-10 01:35:04'
		),
		array(
			'id' => '11',
			'key' => '4442051839dad918273bab49c5390bdd',
			'language_id' => '2',
			'correct_sequence' => '0',
			'correct' => '新規選択肢1',
			'quiz_question_id' => '13',
			'created_user' => '1',
			'created' => '2016-06-10 01:36:06',
			'modified_user' => '1',
			'modified' => '2016-06-10 01:36:06'
		),
		array(
			'id' => '12',
			'key' => '624ff769ed1aff2820d2c1d7cbcc83a1',
			'language_id' => '2',
			'correct_sequence' => '0',
			'correct' => '新規選択肢1',
			'quiz_question_id' => '14',
			'created_user' => '1',
			'created' => '2016-06-10 01:37:46',
			'modified_user' => '1',
			'modified' => '2016-06-10 01:37:46'
		),
		array(
			'id' => '13',
			'key' => '26d0538583b528732fc89809eae51e55',
			'language_id' => '2',
			'correct_sequence' => '0',
			'correct' => '新規選択肢1',
			'quiz_question_id' => '15',
			'created_user' => '1',
			'created' => '2016-06-10 01:39:11',
			'modified_user' => '1',
			'modified' => '2016-06-10 01:39:11'
		),
		array(
			'id' => '14',
			'key' => 'e19145bc2b2b4bd2bf15be9244116479',
			'language_id' => '2',
			'correct_sequence' => '0',
			'correct' => '新規選択肢1',
			'quiz_question_id' => '16',
			'created_user' => '1',
			'created' => '2016-06-10 01:41:40',
			'modified_user' => '1',
			'modified' => '2016-06-10 01:41:40'
		),
		array(
			'id' => '15',
			'key' => 'a8fcc83abd566b0e9a7fde66bb08afa3',
			'language_id' => '2',
			'correct_sequence' => '0',
			'correct' => '新規選択肢1#||||||#新規選択肢3',
			'quiz_question_id' => '17',
			'created_user' => '1',
			'created' => '2016-06-10 01:41:40',
			'modified_user' => '1',
			'modified' => '2016-06-10 01:41:40'
		),
		array(
			'id' => '16',
			'key' => 'b1cad771d65f9e8f97291ffdd2ce0914',
			'language_id' => '2',
			'correct_sequence' => '0',
			'correct' => 'AA#||||||#aa#||||||#ああ',
			'quiz_question_id' => '18',
			'created_user' => '1',
			'created' => '2016-06-10 01:41:40',
			'modified_user' => '1',
			'modified' => '2016-06-10 01:41:40'
		),
		array(
			'id' => '17',
			'key' => 'a02b58eb07d83c46cea47e60bb78926f',
			'language_id' => '2',
			'correct_sequence' => '0',
			'correct' => '新規選択肢1',
			'quiz_question_id' => '19',
			'created_user' => '1',
			'created' => '2016-06-10 01:55:21',
			'modified_user' => '1',
			'modified' => '2016-06-10 01:55:21'
		),
		array(
			'id' => '18',
			'key' => '7201d87e5cb873cf6d2e84ac0e918c6d',
			'language_id' => '2',
			'correct_sequence' => '0',
			'correct' => '新規選択肢1',
			'quiz_question_id' => '20',
			'created_user' => '1',
			'created' => '2016-06-10 02:08:26',
			'modified_user' => '1',
			'modified' => '2016-06-10 02:08:26'
		),
		array(
			'id' => '19',
			'key' => 'f8de587506b26f27a5783ad4f6169226',
			'language_id' => '2',
			'correct_sequence' => '0',
			'correct' => '新規選択肢1',
			'quiz_question_id' => '21',
			'created_user' => '1',
			'created' => '2016-06-10 02:10:55',
			'modified_user' => '1',
			'modified' => '2016-06-10 02:10:55'
		),
		array(
			'id' => '20',
			'key' => 'a89a8c982b2a14bab4cfa7274efa0a1a',
			'language_id' => '2',
			'correct_sequence' => '0',
			'correct' => '新規選択肢1',
			'quiz_question_id' => '22',
			'created_user' => '1',
			'created' => '2016-06-10 02:12:11',
			'modified_user' => '1',
			'modified' => '2016-06-10 02:12:11'
		),
		array(
			'id' => '21',
			'key' => 'f1a7c33795e387decf9a245cf9d31596',
			'language_id' => '2',
			'correct_sequence' => '0',
			'correct' => '新規選択肢1',
			'quiz_question_id' => '23',
			'created_user' => '1',
			'created' => '2016-06-10 02:13:03',
			'modified_user' => '1',
			'modified' => '2016-06-10 02:13:03'
		),
		array(
			'id' => '22',
			'key' => 'c7face9bf2ea6a5c0327c3450c712f72',
			'language_id' => '2',
			'correct_sequence' => '0',
			'correct' => '新規選択肢1',
			'quiz_question_id' => '24',
			'created_user' => '1',
			'created' => '2016-06-10 02:13:56',
			'modified_user' => '1',
			'modified' => '2016-06-10 02:13:56'
		),
		array(
			'id' => '23',
			'key' => '79a24843ca97e7ae69651b153514e243',
			'language_id' => '2',
			'correct_sequence' => '0',
			'correct' => '新規選択肢1',
			'quiz_question_id' => '25',
			'created_user' => '1',
			'created' => '2016-06-10 02:14:33',
			'modified_user' => '1',
			'modified' => '2016-06-10 02:14:33'
		),
		array(
			'id' => '24',
			'key' => '52e65a3a9e592144ed48e0f70583a91e',
			'language_id' => '2',
			'correct_sequence' => '0',
			'correct' => '新規選択肢1',
			'quiz_question_id' => '26',
			'created_user' => '1',
			'created' => '2016-06-10 02:15:38',
			'modified_user' => '1',
			'modified' => '2016-06-10 02:15:38'
		),
		array(
			'id' => '25',
			'key' => '2037c15258e97c745bfd1e927b2b1357',
			'language_id' => '2',
			'correct_sequence' => '0',
			'correct' => '新規選択肢1',
			'quiz_question_id' => '27',
			'created_user' => '3',
			'created' => '2016-06-10 03:05:55',
			'modified_user' => '3',
			'modified' => '2016-06-10 03:05:55'
		),
		array(
			'id' => '26',
			'key' => '23cc641996ac5ef075eea15c757e59a0',
			'language_id' => '2',
			'correct_sequence' => '0',
			'correct' => '新規選択肢1',
			'quiz_question_id' => '28',
			'created_user' => '3',
			'created' => '2016-06-10 03:06:40',
			'modified_user' => '3',
			'modified' => '2016-06-10 03:06:40'
		),
		array(
			'id' => '27',
			'key' => '9a7cf8ef338dcb3499c5956d04b9bca0',
			'language_id' => '2',
			'correct_sequence' => '0',
			'correct' => '新規選択肢1',
			'quiz_question_id' => '29',
			'created_user' => '3',
			'created' => '2016-06-10 03:07:18',
			'modified_user' => '3',
			'modified' => '2016-06-10 03:07:18'
		),
		array(
			'id' => '28',
			'key' => '29e5977d301a3d8e9852d8fb68243403',
			'language_id' => '2',
			'correct_sequence' => '0',
			'correct' => '新規選択肢1',
			'quiz_question_id' => '30',
			'created_user' => '4',
			'created' => '2016-06-10 03:08:40',
			'modified_user' => '4',
			'modified' => '2016-06-10 03:08:40'
		),
		array(
			'id' => '29',
			'key' => '5a5a9a10e19b0f66ba2f744ea78260af',
			'language_id' => '2',
			'correct_sequence' => '0',
			'correct' => '新規選択肢1',
			'quiz_question_id' => '31',
			'created_user' => '4',
			'created' => '2016-06-10 03:09:21',
			'modified_user' => '4',
			'modified' => '2016-06-10 03:09:21'
		),
		array(
			'id' => '30',
			'key' => 'f9921e279d756a28a4bd0fbf98222de3',
			'language_id' => '2',
			'correct_sequence' => '0',
			'correct' => '新規選択肢1',
			'quiz_question_id' => '32',
			'created_user' => '4',
			'created' => '2016-06-10 03:10:04',
			'modified_user' => '4',
			'modified' => '2016-06-10 03:10:04'
		),
		array(
			'id' => '31',
			'key' => '5efcc659146a0cb6800ac9c8cf3be421',
			'language_id' => '2',
			'correct_sequence' => '0',
			'correct' => '新規選択肢1',
			'quiz_question_id' => '33',
			'created_user' => '1',
			'created' => '2016-06-10 03:11:03',
			'modified_user' => '1',
			'modified' => '2016-06-10 03:11:03'
		),
		array(
			'id' => '32',
			'key' => 'afb838e81dc3f20bf6dd7edcb6faa6c1',
			'language_id' => '2',
			'correct_sequence' => '0',
			'correct' => '新規選択肢1',
			'quiz_question_id' => '34',
			'created_user' => '1',
			'created' => '2016-06-10 03:11:26',
			'modified_user' => '1',
			'modified' => '2016-06-10 03:11:26'
		),
		array(
			'id' => '33',
			'key' => 'a5b7684902538672c9057aca64d14334',
			'language_id' => '2',
			'correct_sequence' => '0',
			'correct' => '新規選択肢1',
			'quiz_question_id' => '35',
			'created_user' => '1',
			'created' => '2016-06-10 03:11:48',
			'modified_user' => '1',
			'modified' => '2016-06-10 03:11:48'
		),
		array(
			'id' => '34',
			'key' => '2bc4912c825f1f1b59470a228376ec2a',
			'language_id' => '2',
			'correct_sequence' => '0',
			'correct' => '新規選択肢1',
			'quiz_question_id' => '36',
			'created_user' => '1',
			'created' => '2016-06-10 03:12:17',
			'modified_user' => '1',
			'modified' => '2016-06-10 03:12:17'
		),
		array(
			'id' => '35',
			'key' => '0ec8c3106044af790dd9a224ce938885',
			'language_id' => '2',
			'correct_sequence' => '0',
			'correct' => '新規選択肢1',
			'quiz_question_id' => '37',
			'created_user' => '1',
			'created' => '2016-06-10 03:12:35',
			'modified_user' => '1',
			'modified' => '2016-06-10 03:12:35'
		),
		array(
			'id' => '36',
			'key' => '801acde48323c0109a88aa3fd44abec1',
			'language_id' => '2',
			'correct_sequence' => '0',
			'correct' => '新規選択肢1',
			'quiz_question_id' => '38',
			'created_user' => '1',
			'created' => '2016-06-10 03:12:54',
			'modified_user' => '1',
			'modified' => '2016-06-10 03:12:54'
		),
		array(
			'id' => '37',
			'key' => '9ca3219ae8d4a1c092066d808c3b2ca1',
			'language_id' => '2',
			'correct_sequence' => '0',
			'correct' => '新規選択肢1',
			'quiz_question_id' => '39',
			'created_user' => '1',
			'created' => '2016-06-10 03:13:38',
			'modified_user' => '1',
			'modified' => '2016-06-10 03:13:38'
		),
		array(
			'id' => '38',
			'key' => '31950430d20ae0ab67b41b8f9b3fe56e',
			'language_id' => '2',
			'correct_sequence' => '0',
			'correct' => '新規選択肢1',
			'quiz_question_id' => '40',
			'created_user' => '3',
			'created' => '2016-06-10 03:14:46',
			'modified_user' => '3',
			'modified' => '2016-06-10 03:14:46'
		),
		array(
			'id' => '39',
			'key' => 'f36ee6d6c6eb483e0e0ee088b395f80a',
			'language_id' => '2',
			'correct_sequence' => '0',
			'correct' => '新規選択肢1',
			'quiz_question_id' => '41',
			'created_user' => '4',
			'created' => '2016-06-10 03:15:58',
			'modified_user' => '4',
			'modified' => '2016-06-10 03:15:58'
		),
		array(
			'id' => '40',
			'key' => '4f81802f46af8b6aa4698ae3c4e99e69',
			'language_id' => '2',
			'correct_sequence' => '0',
			'correct' => '新規選択肢1',
			'quiz_question_id' => '42',
			'created_user' => '3',
			'created' => '2016-06-10 03:17:08',
			'modified_user' => '3',
			'modified' => '2016-06-10 03:17:08'
		),
		array(
			'id' => '41',
			'key' => '955aa243f9af0f08dea94c4a2cd509ed',
			'language_id' => '2',
			'correct_sequence' => '0',
			'correct' => '新規選択肢1',
			'quiz_question_id' => '43',
			'created_user' => '4',
			'created' => '2016-06-10 03:18:21',
			'modified_user' => '4',
			'modified' => '2016-06-10 03:18:21'
		),
		array(
			'id' => '42',
			'key' => '865b465ec0aeec790bda93cd610a0534',
			'language_id' => '2',
			'correct_sequence' => '0',
			'correct' => '新規選択肢1',
			'quiz_question_id' => '44',
			'created_user' => '3',
			'created' => '2016-06-10 03:19:37',
			'modified_user' => '3',
			'modified' => '2016-06-10 03:19:37'
		),
		array(
			'id' => '43',
			'key' => '75ac2924c68061c1de09b20501701796',
			'language_id' => '2',
			'correct_sequence' => '0',
			'correct' => '新規選択肢1',
			'quiz_question_id' => '45',
			'created_user' => '4',
			'created' => '2016-06-10 05:30:41',
			'modified_user' => '4',
			'modified' => '2016-06-10 05:30:41'
		),
		array(
			'id' => '44',
			'key' => '7e5814abc5689cc429e5010b870d6918',
			'language_id' => '2',
			'correct_sequence' => '0',
			'correct' => '新規選択肢1',
			'quiz_question_id' => '46',
			'created_user' => '1',
			'created' => '2016-06-10 06:06:21',
			'modified_user' => '1',
			'modified' => '2016-06-10 06:06:21'
		),
		array(
			'id' => '45',
			'key' => '43891eb37827ce8441d8bc440cf469ea',
			'language_id' => '2',
			'correct_sequence' => '0',
			'correct' => '新規選択肢1',
			'quiz_question_id' => '47',
			'created_user' => '1',
			'created' => '2016-06-10 06:06:52',
			'modified_user' => '1',
			'modified' => '2016-06-10 06:06:52'
		),
		array(
			'id' => '46',
			'key' => 'd27af9f300c36df789dac5ad87163145',
			'language_id' => '2',
			'correct_sequence' => '0',
			'correct' => '新規選択肢1',
			'quiz_question_id' => '48',
			'created_user' => '1',
			'created' => '2016-06-10 06:08:11',
			'modified_user' => '1',
			'modified' => '2016-06-10 06:08:11'
		),
		array(
			'id' => '47',
			'key' => '204a0e5f37030ff2f828be5de3f1733e',
			'language_id' => '2',
			'correct_sequence' => '0',
			'correct' => '新規選択肢1#||||||#新規選択肢2',
			'quiz_question_id' => '49',
			'created_user' => '1',
			'created' => '2016-06-10 06:09:49',
			'modified_user' => '1',
			'modified' => '2016-06-10 06:09:49'
		),
		array(
			'id' => '48',
			'key' => 'ec59150387de6478e549faaac110fcfc',
			'language_id' => '2',
			'correct_sequence' => '0',
			'correct' => 'AA#||||||#aa#||||||#ああ',
			'quiz_question_id' => '50',
			'created_user' => '1',
			'created' => '2016-06-10 06:11:08',
			'modified_user' => '1',
			'modified' => '2016-06-10 06:11:08'
		),
		array(
			'id' => '49',
			'key' => '61b4b782881e2f2cc0c44aa086d505a0',
			'language_id' => '2',
			'correct_sequence' => '0',
			'correct' => 'AA#||||||#aa#||||||#ああ',
			'quiz_question_id' => '51',
			'created_user' => '1',
			'created' => '2016-06-10 06:12:15',
			'modified_user' => '1',
			'modified' => '2016-06-10 06:12:15'
		),
		array(
			'id' => '50',
			'key' => '7466c110bb777a7f9474367bfa42fa81',
			'language_id' => '2',
			'correct_sequence' => '0',
			'correct' => 'BB#||||||#bb',
			'quiz_question_id' => '51',
			'created_user' => '1',
			'created' => '2016-06-10 06:12:15',
			'modified_user' => '1',
			'modified' => '2016-06-10 06:12:15'
		),
		array(
			'id' => '51',
			'key' => '7e5a15039a19e872d03112cfe6ac9a8f',
			'language_id' => '2',
			'correct_sequence' => '0',
			'correct' => 'CC#||||||#cc',
			'quiz_question_id' => '51',
			'created_user' => '1',
			'created' => '2016-06-10 06:12:15',
			'modified_user' => '1',
			'modified' => '2016-06-10 06:12:15'
		),
		array(
			'id' => '52',
			'key' => 'e90426936b92c4404aa436c11954843f',
			'language_id' => '2',
			'correct_sequence' => '0',
			'correct' => '新規選択肢1',
			'quiz_question_id' => '53',
			'created_user' => '1',
			'created' => '2016-06-10 06:16:13',
			'modified_user' => '1',
			'modified' => '2016-06-10 06:16:13'
		),
		array(
			'id' => '53',
			'key' => 'c769b59d7e4b31e2fb4687a8a5394523',
			'language_id' => '2',
			'correct_sequence' => '0',
			'correct' => '新規選択肢1',
			'quiz_question_id' => '54',
			'created_user' => '1',
			'created' => '2016-06-10 06:17:05',
			'modified_user' => '1',
			'modified' => '2016-06-10 06:17:05'
		),
		array(
			'id' => '54',
			'key' => '0bb3fa8ca4341da1508b3ad486dd7335',
			'language_id' => '2',
			'correct_sequence' => '0',
			'correct' => '新規選択肢1#||||||#新規選択肢2#||||||#新規選択肢3',
			'quiz_question_id' => '55',
			'created_user' => '1',
			'created' => '2016-06-10 06:18:17',
			'modified_user' => '1',
			'modified' => '2016-06-10 06:18:17'
		),
		array(
			'id' => '55',
			'key' => 'b02c3f33c9dbf87a8a0a0baeaa5da0b8',
			'language_id' => '2',
			'correct_sequence' => '0',
			'correct' => 'AA#||||||#aa#||||||#ああ',
			'quiz_question_id' => '56',
			'created_user' => '1',
			'created' => '2016-06-10 06:19:45',
			'modified_user' => '1',
			'modified' => '2016-06-10 06:19:45'
		),
		array(
			'id' => '56',
			'key' => 'ddd3d2f49399f4d609b52f5c27296726',
			'language_id' => '2',
			'correct_sequence' => '0',
			'correct' => 'BB#||||||#bb',
			'quiz_question_id' => '56',
			'created_user' => '1',
			'created' => '2016-06-10 06:19:45',
			'modified_user' => '1',
			'modified' => '2016-06-10 06:19:45'
		),
		array(
			'id' => '57',
			'key' => '0f9d04c638d3e26242142cbf9a64792f',
			'language_id' => '2',
			'correct_sequence' => '0',
			'correct' => 'CC#||||||#cc',
			'quiz_question_id' => '56',
			'created_user' => '1',
			'created' => '2016-06-10 06:19:45',
			'modified_user' => '1',
			'modified' => '2016-06-10 06:19:45'
		),
	);

}
