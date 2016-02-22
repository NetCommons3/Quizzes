<?php
/**
 * QuizQuestion Model
 *
 * @property Language $Language
 * @property QuizPage $QuizPage
 * @property QuizChoice $QuizChoice
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('QuizzesAppModel', 'Quizzes.Model');

/**
 * Summary for QuizQuestion Model
 */
class QuizQuestion extends QuizzesAppModel {

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
		'question_sequence' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'question_type' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'is_require' => array(
			'boolean' => array(
				'rule' => array('boolean'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'is_choice_random' => array(
			'boolean' => array(
				'rule' => array('boolean'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'allotment' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'quiz_page_id' => array(
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

/**
 * getDefaultQuestion
 * get default data of quiz question
 *
 * @return array
 */
	public function getDefaultQuestion() {
		$this->QuizChoice = ClassRegistry::init('Quizzes.QuizChoice', true);
		$question = array(
			'question_sequence' => 0,
			'question_value' => __d('quizzes', 'New Question') . '1',
			'question_type' => QuizzesComponent::TYPE_SELECTION,
			'allotment' => 0,
			'commentary' => '',
			'is_choice_random' => QuizzesComponent::USES_NOT_USE,
		);
		$question['QuizChoice'][0] = $this->QuizChoice->getDefaultChoice();
		return $question;
	}

/**
 * setQuestionToPage
 * setup page data to quiz array
 *
 * @param array &$quiz quiz data
 * @param array &$page quiz page data
 * @return void
 */
	public function setQuestionToPage(&$quiz, &$page) {
		$questions = $this->find('all', array(
			'conditions' => array(
				'quiz_page_id' => $page['id'],
			),
			'order' => array(
				'question_sequence' => 'asc',
			)
		));

		if (!empty($questions)) {
			foreach ($questions as $question) {
				if (isset($question['QuizChoice'])) {
					$question['QuizQuestion']['QuizChoice'] = $question['QuizChoice'];
				}
				if (isset($question['QuizCorrect'])) {
					$question['QuizQuestion']['QuizCorrect'] = $question['QuizCorrect'];
				}
				$page['QuizQuestion'][] = $question['QuizQuestion'];
				$quiz['Quiz']['question_count']++;
			}
		}
	}

/**
 * saveQuizQuestion
 * save QuizQuestion data
 *
 * @param array &$questions quiz questions
 * @throws InternalErrorException
 * @return bool
 */
	public function saveQuizQuestion(&$questions) {
		$this->loadModels([
			'QuizCorrect' => 'Quizzes.QuizCorrect',
			'QuizChoice' => 'Quizzes.QuizChoice',
		]);
		// QuizQuestionが単独でSaveされることはない
		// 必ず上位のQuizのSaveの折に呼び出される
		// なので、$this->setDataSource('master');といった
		// 決まり処理は上位で行われる
		// ここでは行わない

		foreach ($questions as &$question) {
			// 小テストは履歴を取っていくタイプのコンテンツデータなのでSave前にはID項目はカット
			// （そうしないと既存レコードのUPDATEになってしまうから）
			$question = Hash::remove($question, 'QuizQuestion.id');

			$this->create();
			if (! $this->save($question, false)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}

			$questionId = $this->id;

			if (isset($question['QuizChoice'])) {
				$question = Hash::insert($question, 'QuizChoice.{n}.quiz_question_id', $questionId);
				// もしもChoiceのsaveがエラーになった場合は、
				// ChoiceのほうでInternalExceptionErrorが発行されるのでここでは何も行わない
				$this->QuizChoice->saveQuizChoice($question['QuizChoice']);
			}
			if (isset($question['QuizCorrect'])) {
				$question = Hash::insert($question, 'QuizCorrect.{n}.quiz_question_id', $questionId);
				$this->QuizCorrect->saveQuizCorrect($question['QuizCorrect']);
			}
		}
		return true;
	}
}
