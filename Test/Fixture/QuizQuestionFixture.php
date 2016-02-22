<?php
/**
 * QuizQuestionFixture
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

/**
 * Summary for QuizQuestionFixture
 */
class QuizQuestionFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'key' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'language_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index'),
		'question_sequence' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'comment' => '質問表示順'),
		'question_value' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '質問文', 'charset' => 'utf8'),
		'question_type' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 4, 'unsigned' => false, 'comment' => '質問タイプ | 1:択一選択 | 2:複数選択 | 3:単語 | 4:記述式 | 5:穴埋め
'),
		'is_require' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '回答必須フラグ | 0:不要 | 1:必須'),
		'is_choice_random' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '選択肢表示順序ランダム化 | 質問タイプが1:択一選択 2:複数選択 6:マトリクス（択一） 7:マトリクス（複数） のとき有効 ただし、６，７については行がランダムになるだけで列はランダム化されない'),
		'allotment' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => '配点
'),
		'correct' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '正解
', 'charset' => 'utf8'),
		'commentary' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '解説
', 'charset' => 'utf8'),
		'quiz_page_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index'),
		'created_user' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified_user' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'fk_quiz_question_quiz_page1_idx' => array('column' => 'quiz_page_id', 'unique' => 0),
			'fk_quiz_questions_languages1_idx' => array('column' => 'language_id', 'unique' => 0)
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
			'question_sequence' => 1,
			'question_value' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
			'question_type' => 1,
			'is_require' => 1,
			'is_choice_random' => 1,
			'allotment' => 1,
			'correct' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
			'commentary' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
			'quiz_page_id' => 1,
			'created_user' => 1,
			'created' => '2015-12-28 04:28:45',
			'modified_user' => 1,
			'modified' => '2015-12-28 04:28:45'
		),
	);

}
