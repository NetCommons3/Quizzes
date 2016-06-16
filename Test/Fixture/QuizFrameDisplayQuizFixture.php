<?php
/**
 * QuizFrameDisplayQuizFixture
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

/**
 * Summary for QuizFrameDisplayQuizFixture
 */
class QuizFrameDisplayQuizFixture extends CakeTestFixture {

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
		'frame_key' => array('type' => 'string', 'null' => false, 'default' => null, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'quiz_key' => array('type' => 'string', 'null' => false, 'default' => null, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'created_user' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified_user' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'quiz_key' => array('column' => 'quiz_key', 'unique' => 0),
			'fk_quiz_frame_display_quizzes_quiz_idx' => array('column' => 'frame_key', 'unique' => 0)
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
			'id' => '8',
			'frame_key' => 'frame_3',
			'quiz_key' => '5fdb4f0049f3bddeabc49cd2b72c6ac9',
			'created_user' => '1',
			'created' => '2016-06-10 01:30:03',
			'modified_user' => '1',
			'modified' => '2016-06-10 01:30:03'
		),
		array(
			'id' => '9',
			'frame_key' => 'frame_3',
			'quiz_key' => 'a2cf0e48f281be7c3cc35f0920f047ca',
			'created_user' => '1',
			'created' => '2016-06-10 01:30:36',
			'modified_user' => '1',
			'modified' => '2016-06-10 01:30:36'
		),
		array(
			'id' => '10',
			'frame_key' => 'frame_3',
			'quiz_key' => 'a916437af184b4a185f685a93099adca',
			'created_user' => '1',
			'created' => '2016-06-10 01:31:18',
			'modified_user' => '1',
			'modified' => '2016-06-10 01:31:18'
		),
		array(
			'id' => '11',
			'frame_key' => 'frame_3',
			'quiz_key' => '9432ebe6ef6b15e1fb5d8b3a36bdf044',
			'created_user' => '1',
			'created' => '2016-06-10 01:34:15',
			'modified_user' => '1',
			'modified' => '2016-06-10 01:34:15'
		),
		array(
			'id' => '12',
			'frame_key' => 'frame_3',
			'quiz_key' => '52fc8a15a76b4f315db20e319de5c6d0',
			'created_user' => '1',
			'created' => '2016-06-10 01:35:04',
			'modified_user' => '1',
			'modified' => '2016-06-10 01:35:04'
		),
		array(
			'id' => '13',
			'frame_key' => 'frame_3',
			'quiz_key' => 'b7d334fb8303a381689cdd90a18f36b1',
			'created_user' => '1',
			'created' => '2016-06-10 01:36:06',
			'modified_user' => '1',
			'modified' => '2016-06-10 01:36:06'
		),
		array(
			'id' => '14',
			'frame_key' => 'frame_3',
			'quiz_key' => '2065174f8ffb95d9e766db1894be6529',
			'created_user' => '1',
			'created' => '2016-06-10 01:37:46',
			'modified_user' => '1',
			'modified' => '2016-06-10 01:37:46'
		),
		array(
			'id' => '15',
			'frame_key' => 'frame_3',
			'quiz_key' => '20a3ff261c2f7276f29ba6082dfa2277',
			'created_user' => '1',
			'created' => '2016-06-10 01:39:12',
			'modified_user' => '1',
			'modified' => '2016-06-10 01:39:12'
		),
		array(
			'id' => '16',
			'frame_key' => 'frame_3',
			'quiz_key' => 'acc5e94c9617ed332cc2ef4d013ae686',
			'created_user' => '1',
			'created' => '2016-06-10 01:41:40',
			'modified_user' => '1',
			'modified' => '2016-06-10 01:41:40'
		),
		array(
			'id' => '17',
			'frame_key' => 'frame_3',
			'quiz_key' => 'a2c3c1ffa95bbd7deab1d53406f4cf42',
			'created_user' => '1',
			'created' => '2016-06-10 01:55:21',
			'modified_user' => '1',
			'modified' => '2016-06-10 01:55:21'
		),
		array(
			'id' => '18',
			'frame_key' => 'frame_3',
			'quiz_key' => '7a32c4f0c47d05fa43953b06cf23e0f2',
			'created_user' => '1',
			'created' => '2016-06-10 02:08:26',
			'modified_user' => '1',
			'modified' => '2016-06-10 02:08:26'
		),
		array(
			'id' => '19',
			'frame_key' => 'frame_3',
			'quiz_key' => '58688715449e27e5af9ded1f90dd2bc8',
			'created_user' => '1',
			'created' => '2016-06-10 02:12:12',
			'modified_user' => '1',
			'modified' => '2016-06-10 02:12:12'
		),
		array(
			'id' => '20',
			'frame_key' => 'frame_3',
			'quiz_key' => '013b3356de3c85d5dd27a8c0f9cf30c8',
			'created_user' => '1',
			'created' => '2016-06-10 02:13:04',
			'modified_user' => '1',
			'modified' => '2016-06-10 02:13:04'
		),
		array(
			'id' => '21',
			'frame_key' => 'frame_3',
			'quiz_key' => 'b1fc3e74d1fdf47e06d76d41fad41067',
			'created_user' => '1',
			'created' => '2016-06-10 02:13:57',
			'modified_user' => '1',
			'modified' => '2016-06-10 02:13:57'
		),
		array(
			'id' => '22',
			'frame_key' => 'frame_3',
			'quiz_key' => '7d53bb7028f45c56df0e0822498b90b9',
			'created_user' => '1',
			'created' => '2016-06-10 02:14:33',
			'modified_user' => '1',
			'modified' => '2016-06-10 02:14:33'
		),
		array(
			'id' => '23',
			'frame_key' => 'frame_3',
			'quiz_key' => 'c389a74ef01516f9b3e477afcf3dfa02',
			'created_user' => '1',
			'created' => '2016-06-10 02:15:38',
			'modified_user' => '1',
			'modified' => '2016-06-10 02:15:38'
		),
		array(
			'id' => '24',
			'frame_key' => 'frame_3',
			'quiz_key' => '468e3c55607b0c1d5cf55ddad51f836a',
			'created_user' => '3',
			'created' => '2016-06-10 03:05:56',
			'modified_user' => '3',
			'modified' => '2016-06-10 03:05:56'
		),
		array(
			'id' => '25',
			'frame_key' => 'frame_3',
			'quiz_key' => '1bfdd89e1f15f81d2d7190893910ad19',
			'created_user' => '3',
			'created' => '2016-06-10 03:06:40',
			'modified_user' => '3',
			'modified' => '2016-06-10 03:06:40'
		),
		array(
			'id' => '26',
			'frame_key' => 'frame_3',
			'quiz_key' => '13001359fc46bd17c03451906eee7e4e',
			'created_user' => '3',
			'created' => '2016-06-10 03:07:19',
			'modified_user' => '3',
			'modified' => '2016-06-10 03:07:19'
		),
		array(
			'id' => '27',
			'frame_key' => 'frame_3',
			'quiz_key' => '39e6aa5d38fff3230276e0abf408c9a6',
			'created_user' => '4',
			'created' => '2016-06-10 03:08:41',
			'modified_user' => '4',
			'modified' => '2016-06-10 03:08:41'
		),
		array(
			'id' => '28',
			'frame_key' => 'frame_3',
			'quiz_key' => 'cc38fc4c532f2252c3d0861df0c8866c',
			'created_user' => '4',
			'created' => '2016-06-10 03:09:21',
			'modified_user' => '4',
			'modified' => '2016-06-10 03:09:21'
		),
		array(
			'id' => '29',
			'frame_key' => 'frame_3',
			'quiz_key' => '41e2b809108edead2f30adc37f51e979',
			'created_user' => '4',
			'created' => '2016-06-10 03:10:05',
			'modified_user' => '4',
			'modified' => '2016-06-10 03:10:05'
		),
		array(
			'id' => '30',
			'frame_key' => 'frame_3',
			'quiz_key' => 'e9329d3567b76c1b880e1a80a74c12f5',
			'created_user' => '1',
			'created' => '2016-06-10 03:13:39',
			'modified_user' => '1',
			'modified' => '2016-06-10 03:13:39'
		),
		array(
			'id' => '31',
			'frame_key' => 'frame_3',
			'quiz_key' => '257b711744f8fb6ba8313a688a9de52f',
			'created_user' => '3',
			'created' => '2016-06-10 03:14:46',
			'modified_user' => '3',
			'modified' => '2016-06-10 03:14:46'
		),
		array(
			'id' => '32',
			'frame_key' => 'frame_3',
			'quiz_key' => 'e3eee47e033eccc3f42c02d75678235b',
			'created_user' => '4',
			'created' => '2016-06-10 03:15:58',
			'modified_user' => '4',
			'modified' => '2016-06-10 03:15:58'
		),
		array(
			'id' => '33',
			'frame_key' => 'frame_3',
			'quiz_key' => '9e003ee9cd538f7a4cf9d73b0b3470c4',
			'created_user' => '3',
			'created' => '2016-06-10 03:17:08',
			'modified_user' => '3',
			'modified' => '2016-06-10 03:17:08'
		),
		array(
			'id' => '34',
			'frame_key' => 'frame_3',
			'quiz_key' => '4f02540a2a10aeffbcc079e73961d4ad',
			'created_user' => '4',
			'created' => '2016-06-10 03:18:22',
			'modified_user' => '4',
			'modified' => '2016-06-10 03:18:22'
		),
		array(
			'id' => '35',
			'frame_key' => 'frame_3',
			'quiz_key' => 'ac3198521c927f4c25f7a14e64e286ea',
			'created_user' => '3',
			'created' => '2016-06-10 03:19:38',
			'modified_user' => '3',
			'modified' => '2016-06-10 03:19:38'
		),
		array(
			'id' => '36',
			'frame_key' => 'frame_3',
			'quiz_key' => '5f687070b3dbecb005cf000d95048a44',
			'created_user' => '4',
			'created' => '2016-06-10 05:30:42',
			'modified_user' => '4',
			'modified' => '2016-06-10 05:30:42'
		),
		array(
			'id' => '37',
			'frame_key' => 'frame_3',
			'quiz_key' => '64f129efa1cc9f1f21feaa7052f3b86c',
			'created_user' => '1',
			'created' => '2016-06-10 06:08:12',
			'modified_user' => '1',
			'modified' => '2016-06-10 06:08:12'
		),
		array(
			'id' => '38',
			'frame_key' => 'frame_3',
			'quiz_key' => '7ac353d879f3ec845f2333d405793afe',
			'created_user' => '1',
			'created' => '2016-06-10 06:09:50',
			'modified_user' => '1',
			'modified' => '2016-06-10 06:09:50'
		),
		array(
			'id' => '39',
			'frame_key' => 'frame_3',
			'quiz_key' => 'e2df50a46cf637df61d65faead3cb79e',
			'created_user' => '1',
			'created' => '2016-06-10 06:11:09',
			'modified_user' => '1',
			'modified' => '2016-06-10 06:11:09'
		),
		array(
			'id' => '40',
			'frame_key' => 'frame_3',
			'quiz_key' => '11dbd7ce61d2d677088bdc87a902b67a',
			'created_user' => '1',
			'created' => '2016-06-10 06:12:16',
			'modified_user' => '1',
			'modified' => '2016-06-10 06:12:16'
		),
		array(
			'id' => '41',
			'frame_key' => 'frame_3',
			'quiz_key' => '83b294e176a8c8026d4fbdb07ad2ed7f',
			'created_user' => '1',
			'created' => '2016-06-10 06:14:22',
			'modified_user' => '1',
			'modified' => '2016-06-10 06:14:22'
		),
		array(
			'id' => '42',
			'frame_key' => 'frame_3',
			'quiz_key' => '92055d0ef850fa264d4e730bf7ba9d1b',
			'created_user' => '1',
			'created' => '2016-06-10 06:16:14',
			'modified_user' => '1',
			'modified' => '2016-06-10 06:16:14'
		),
		array(
			'id' => '43',
			'frame_key' => 'frame_3',
			'quiz_key' => '9d6adcb8886e46bd3404535c561e6398',
			'created_user' => '1',
			'created' => '2016-06-10 06:17:06',
			'modified_user' => '1',
			'modified' => '2016-06-10 06:17:06'
		),
		array(
			'id' => '44',
			'frame_key' => 'frame_3',
			'quiz_key' => '5cd22110e513bf7e3964b223212c329e',
			'created_user' => '1',
			'created' => '2016-06-10 06:18:20',
			'modified_user' => '1',
			'modified' => '2016-06-10 06:18:20'
		),
		array(
			'id' => '45',
			'frame_key' => 'frame_3',
			'quiz_key' => '4f5afb0a25dabf24ce9697ea5b07abcc',
			'created_user' => '1',
			'created' => '2016-06-10 06:19:46',
			'modified_user' => '1',
			'modified' => '2016-06-10 06:19:46'
		),
	);

}
