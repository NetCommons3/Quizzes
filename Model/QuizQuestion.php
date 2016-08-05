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
 * 配点デフォルト値
 */
	const	QUIZ_QUESTION_DEFAULT_ALLOTMENT = 10;

/**
 * use behaviors
 *
 * @var array
 */
	public $actsAs = array(
		'NetCommons.OriginalKey',
		'Quizzes.QuizQuestionValidate',
		'Wysiwyg.Wysiwyg' => array(
			'fields' => array('question_value', 'commentary'),
		),
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
 * Constructor. Binds the model's database table to the object.
 *
 * @param bool|int|string|array $id Set this ID for this model on startup,
 * can also be an array of options, see above.
 * @param string $table Name of database table to use.
 * @param string $ds DataSource connection name.
 * @see Model::__construct()
 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
 */
	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);

		$this->loadModels([
			'QuizChoice' => 'Quizzes.QuizChoice',
			'QuizCorrect' => 'Quizzes.QuizCorrect',
		]);
	}

/**
 * Called during validation operations, before validation. Please note that custom
 * validation rules can be defined in $validate.
 *
 * @param array $options Options passed from Model::save().
 * @return bool True if validate operation should continue, false to abort
 * @link http://book.cakephp.org/2.0/en/models/callback-methods.html#beforevalidate
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @see Model::save()
 */
	public function beforeValidate($options = array()) {
		$qIndex = $options['questionIndex'];
		// Questionモデルは繰り返し判定が行われる可能性高いのでvalidateルールは最初に初期化
		// mergeはしません
		$this->validate = array(
			'question_sequence' => array(
				'numeric' => array(
					'rule' => array('numeric'),
					'message' => __d('net_commons', 'Invalid request.'),
				),
				'comparison' => array(
					'rule' => array('comparison', '==', $qIndex),
					'message' => __d('quizzes', 'question sequence is illegal.')
				),
			),
			'question_type' => array(
				'inList' => array(
					'rule' => array('inList', QuizzesComponent::$typesList),
					'message' => __d('net_commons', 'Invalid request.'),
				),
			),
			'question_value' => array(
				'notBlank' => array(
					'rule' => array('notBlank'),
					'message' => __d('quizzes', 'Please input question text.'),
				),
			),
			'is_choice_random' => array(
				'boolean' => array(
					'rule' => array('boolean'),
					'message' => __d('net_commons', 'Invalid request.'),
				),
			),
			'is_choice_horizon' => array(
				'boolean' => array(
					'rule' => array('boolean'),
					'message' => __d('net_commons', 'Invalid request.'),
				),
			),
			'is_order_fixed' => array(
				'boolean' => array(
					'rule' => array('boolean'),
					'message' => __d('net_commons', 'Invalid request.'),
				),
			),
			'allotment' => array(
				'numeric' => array(
					'rule' => array('naturalNumber'),
					'required' => true,
					'allowEmpty' => false,
					'message' => __d('quizzes', 'Please enter a number greater than 0 .'),
				),
			),
		);
		// validates時にはまだquiz_page_idの設定ができないのでチェックしないことにする
		// quiz_page_idの設定は上位のQuizPageクラスで責任を持って行われるものとする

		parent::beforeValidate($options);

		return true;
	}
/**
 * getDefaultQuestion
 * get default data of quiz question
 *
 * @return array
 */
	public function getDefaultQuestion() {
		$question = array(
			'question_sequence' => 0,
			'question_value' => __d('quizzes', 'New Question') . '1',
			'question_type' => QuizzesComponent::TYPE_SELECTION,
			'is_choice_random' => QuizzesComponent::USES_NOT_USE,
			'is_choice_horizon' => QuizzesComponent::USES_NOT_USE,
			'is_order_fixed' => QuizzesComponent::USES_NOT_USE,
			'allotment' => self::QUIZ_QUESTION_DEFAULT_ALLOTMENT,
			'commentary' => '',
		);
		$question['QuizChoice'] = $this->QuizChoice->getDefaultChoice();
		$question['QuizCorrect'] = $this->QuizCorrect->getDefaultCorrect();
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
				// 万が一ShufflePageComponentを通らなかったときのための保険
				$question['QuizQuestion']['serial_number'] = $question['QuizQuestion']['question_sequence'];
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
		// QuizQuestionが単独でSaveされることはない
		// 必ず上位のQuizのSaveの折に呼び出される
		// なので、$this->setDataSource('master');といった
		// 決まり処理は上位で行われる
		// ここでは行わない

		foreach ($questions as &$question) {
			// 小テストは履歴を取っていくタイプのコンテンツデータなのでSave前にはID項目はカット
			// （そうしないと既存レコードのUPDATEになってしまうから）
			$question = Hash::remove($question, 'QuizQuestion.id');
			$question['QuizQuestion'] = $question;
			$question['Block'] = Current::read('Block');

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
