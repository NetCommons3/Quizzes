<?php
/**
 * QuizQuestionValidateBehavior::validates()テスト用Model
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('AppModel', 'Model');

/**
 * QuizQuestionValidateBehavior::validates()テスト用Model
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Test\test_app\Plugin\TestQuizzes\Model
 */
class TestQuizQuestionValidateBehaviorValidatesModel extends AppModel {

/**
 * Use table config
 *
 * @var string
 */
	public $useTable = 'quiz_questions';

/**
 * Use alias config
 *
 * @var string
 */
	public $alias = 'QuizQuestion';

/**
 * 使用ビヘイビア
 *
 * @var array
 */
	public $actsAs = array(
		'Quizzes.QuizQuestionValidate'
	);

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'QuizPage' => array(
			'className' => 'Quizzes.QuizPage',
			'foreignKey' => 'quiz_page_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'QuizChoice' => array(
			'className' => 'Quizzes.QuizChoice',
			'foreignKey' => 'quiz_question_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'QuizCorrect' => array(
			'className' => 'Quizzes.QuizCorrect',
			'foreignKey' => 'quiz_question_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);

}
