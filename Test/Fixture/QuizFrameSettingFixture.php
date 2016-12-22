<?php
/**
 * QuizFrameSettingFixture
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

/**
 * Summary for QuizFrameSettingFixture
 */
class QuizFrameSettingFixture extends CakeTestFixture {

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => '1',
			'display_type' => '1',
			'display_num_per_page' => '5',
			'sort_type' => 'Quiz.modified DESC',
			'frame_key' => 'frame_3',
			'created_user' => null,
			'created' => '2016-06-07 02:33:27',
			'modified_user' => '1',
			'modified' => '2016-06-08 07:02:22'
		),
	);

/**
 * Initialize the fixture.
 *
 * @return void
 */
	public function init() {
		require_once App::pluginPath('Quizzes') . 'Config' . DS . 'Schema' . DS . 'schema.php';
		$this->fields = (new QuizzesSchema())->tables[Inflector::tableize($this->name)];
		parent::init();
	}

}
