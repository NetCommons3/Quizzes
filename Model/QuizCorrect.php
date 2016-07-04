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
 * Called during validation operations, before validation. Please note that custom
 * validation rules can be defined in $validate.
 *
 * @param array $options Options passed from Model::save().
 * @return bool True if validate operation should continue, false to abort
 * @link http://book.cakephp.org/2.0/en/models/callback-methods.html#beforevalidate
 * @see Model::save()
 */
	public function beforeValidate($options = array()) {
		// モデルは繰り返し判定が行われる可能性高いのでvalidateルールは最初に初期化
		// mergeはしません
		$this->validate = array(
			//'correct_sequence' => array(
			//	'numeric' => array(
			//		'rule' => array('numeric'),
			//		'message' => __d('net_commons', 'Invalid request.'),
			//	),
			//),
			'correct' => array(
				'notBlank' => array(
					'rule' => array('notBlank'),
					// 正解は必ず設定してください。
					'message' => __d('quizzes', 'Please set correct answer.'),
				),
			),
		);
		// validates時にはまだquiz_question_idの設定ができないのでチェックしないことにする
		// quiz_question_idの設定は上位のQuestionnaireQuestionクラスで責任を持って行われるものとする
		if (is_array($this->data['QuizCorrect']['correct'])) {
			$this->data['QuizCorrect']['correct'] =
				implode(QuizzesComponent::ANSWER_DELIMITER, $this->data['QuizCorrect']['correct']);
		}

		return parent::beforeValidate($options);
	}
/**
 * AfterFind Callback function
 *
 * @param array $results found data records
 * @param bool $primary indicates whether or not the current model was the model that the query originated on or whether or not this model was queried as an association
 * @return mixed
 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
 */
	public function afterFind($results, $primary = false) {
		foreach ($results as &$val) {
			$val[$this->alias]['correct'] =
				explode(QuizzesComponent::ANSWER_DELIMITER, $val[$this->alias]['correct']);
		}
		return $results;
	}

/**
 * getDefaultCorrect
 * get default data of quiz correct
 * このデフォルト値は択一選択がデフォルトであることが前提である
 *
 * @return array
 * @see QuizChoice::getDefaultChoice()
 */
	public function getDefaultCorrect() {
		return	array(
			array(
				'correct_sequence' => 0,
				'correct' => array(__d('quizzes', 'New Choice') . '1'),
			)
		);
	}

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
