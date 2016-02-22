<?php
/**
 * QuizChoice Model
 *
 * @property Language $Language
 * @property QuizQuestion $QuizQuestion
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('QuizzesAppModel', 'Quizzes.Model');

/**
 * Summary for QuizCorrect Model
 */
class QuizCorrect extends QuizzesAppModel {

/**
 * use behaviors
 *
 * @var array
 */
	public $actsAs = array(
		'NetCommons.OriginalKey',
	);

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'key' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				'on' => 'update', // Limit validation to 'create' or 'update' operations
			),
		),
		'language_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'correct_sequence' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'correct' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'update', // Limit validation to 'create' or 'update' operations
			),
		),
		'quiz_question_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Language' => array(
			'className' => 'Language',
			'foreignKey' => 'language_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'QuizQuestion' => array(
			'className' => 'Quizzes.QuizQuestion',
			'foreignKey' => 'quiz_question_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

/**
 * saveQuizCorrect
 * save QuizChoice data
 *
 * @param array &$corrects quiz correct
 * @throws InternalErrorException
 * @return bool
 */
	public function saveQuizCorrect(&$corrects) {
		foreach ($corrects as &$correct) {
			$correct = Hash::remove($correct, 'QuizCorrect.id');
			if (is_array($correct['correct'])) {
				$correct['correct'] = implode(QuizzesComponent::ANSWER_DELIMITER, $correct['correct']);
			}
			$this->create();
			if (!$this->save($correct, false)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
		}
		return true;
	}
}
