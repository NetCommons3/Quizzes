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
 * Summary for QuizChoice Model
 */
class QuizChoice extends QuizzesAppModel {

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
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'QuizQuestion' => array(
			'className' => 'Quizzes.QuizQuestion',
			'foreignKey' => 'quiz_question_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

/**
 * Called during validation operations, before validation. Please note that custom
 * validation rules can be defined in $validate.
 *
 * @param array $options Options passed from Model::save().
 * @return bool True if validate operation should continue, false to abort
 * @link http://book.cakephp.org/2.0/en/models/callback-methods.html#beforevalidate
 * @see Model::save()
 */
	public function beforeValidate($options = array()) {
		$choiceIndex = $options['choiceIndex'];
		// Choiceモデルは繰り返し判定が行われる可能性高いのでvalidateルールは最初に初期化
		// mergeはしません
		$this->validate = array(
			'choice_label' => array(
				'notBlank' => array(
					'rule' => array('notBlank'),
					'message' => __d('quizzes', 'Please input choice text.'),
				),
				'choiceLabel' => array(
					'rule' => array('custom', '/^(?!.*\#\|\|\|\|\|\|\#).*$/'),
					'message' =>
						__d('quizzes', 'You can not use the string of #||||||# for choice text.'),
				),
			),
			'choice_sequence' => array(
				'naturalNumber' => array(
					'rule' => array('naturalNumber', true),
					'allowEmpty' => false,
					'required' => true,
					'message' => __d('quizzes', 'choice sequence is illegal.')
				),
				'comparison' => array(
					'rule' => array('comparison', '==', $choiceIndex),
					'message' => __d('quizzes', 'choice sequence is illegal.')
				),
			),
		);
		// validates時にはまだquestionnaire_question_idの設定ができないのでチェックしないことにする
		// questionnaire_question_idの設定は上位のQuestionnaireQuestionクラスで責任を持って行われるものとする

		return parent::beforeValidate($options);
	}

/**
 * getDefaultChoice
 * get default data of quiz choice
 *
 * @return array
 */
	public function getDefaultChoice() {
		return	array(
			array(
				'choice_sequence' => 0,
				'choice_label' => __d('quizzes', 'New Choice') . '1',
			),
			array(
				'choice_sequence' => 1,
				'choice_label' => __d('quizzes', 'New Choice') . '2',
			),
			array(
				'choice_sequence' => 2,
				'choice_label' => __d('quizzes', 'New Choice') . '3',
			),
		);
	}

/**
 * saveQuizChoice
 * save QuizChoice data
 *
 * @param array &$choices quiz choices
 * @throws InternalErrorException
 * @return bool
 */
	public function saveQuizChoice(&$choices) {
		foreach ($choices as &$choice) {
			$choice = Hash::remove($choice, 'QuizChoice.id');
			$this->create();
			if (!$this->save($choice, false)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
		}
		return true;
	}
}
