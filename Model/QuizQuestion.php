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
	const QUIZ_QUESTION_DEFAULT_ALLOTMENT = 10;

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
		//多言語
		'M17n.M17n' => array(
			'commonFields' => array(
				'question_sequence',
				'question_type',
				'is_choice_random',
				'is_choice_horizon',
				'is_order_fixed',
				'allotment',
			),
			'associations' => array(
				'QuizChoice' => array(
					'class' => 'Quizzes.QuizChoice',
					'foreignKey' => 'quiz_question_id',
				),
				'QuizCorrect' => array(
					'class' => 'Quizzes.QuizCorrect',
					'foreignKey' => 'quiz_question_id',
				),
			),
			'afterCallback' => false,
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
 * getQuestionForPage
 * setup page data to quiz array
 *
 * @param array &$page quiz-page data
 * @param array $questions all question data in this quiz
 * @return void
 */
	public function getQuestionForPage(&$page, $questions) {
		$targetQuestions = Hash::extract(
			$questions,
			'{n}.QuizQuestion[quiz_page_id=' . $page['id'] . ']'
		);
		$targetQuestions = Hash::sort($targetQuestions, '{n}.question_sequence', 'asc');
		foreach ($targetQuestions as &$question) {
			$targetChoices = Hash::extract(
				$questions,
				'{n}.QuizChoice.{n}[quiz_question_id=' . $question['id'] . ']'
			);
			$targetCorrects = Hash::extract(
				$questions,
				'{n}.QuizCorrect.{n}[quiz_question_id=' . $question['id'] . ']'
			);
			$question['QuizChoice'] = $targetChoices;
			$question['QuizCorrect'] = $targetCorrects;
			// 万が一ShufflePageComponentを通らなかったときのための保険
			$question['serial_number'] = $question['question_sequence'];
			$page['QuizQuestion'][] = $question;
		}
		$page['question_count'] = count($targetQuestions);
	}

/**
 * Called before each find operation. Return false if you want to halt the find
 * call, otherwise return the (modified) query data.
 *
 * @param array $query Data used to execute this query, i.e. conditions, order, etc.
 * @return mixed true if the operation should continue, false if it should abort; or, modified
 *  $query to continue with new $query
 * @link http://book.cakephp.org/2.0/en/models/callback-methods.html#beforefind
 */
	public function beforeFind($query) {
		//hasManyで実行されたとき、多言語の条件追加
		if (! $this->id && ! empty($query['conditions']['quiz_page_id'])) {
			$quizPageId = $query['conditions']['quiz_page_id'];
			$query['conditions']['quiz_page_id'] = $this->getQuizPageIdsForM17n($quizPageId);
			$query['conditions']['OR'] = array(
				'QuizQuestion.language_id' => Current::read('Language.id'),
				'QuizQuestion.is_translation' => false,
			);

			return $query;
		}

		return parent::beforeFind($query);
	}

/**
 * 多言語データ取得のため、当言語のquiz_page_idから全言語のquiz_page_idを取得する
 *
 * @param id $quizPageId 当言語のquiz_page_id
 * @return array
 */
	public function getQuizPageIdsForM17n($quizPageId) {
		$quizPage = $this->QuizPage->find('first', array(
			'recursive' => -1,
			'callbacks' => false,
			'fields' => array('id', 'key', 'quiz_id'),
			'conditions' => array('id' => $quizPageId),
		));

		$quizPageIds = $this->QuizPage->find('list', array(
			'recursive' => -1,
			'callbacks' => false,
			'fields' => array('id', 'id'),
			'conditions' => array(
				'quiz_id' => $this->QuizPage->getQuizIdsForM17n($quizPage['QuizPage']['quiz_id']),
				'key' => $quizPage['QuizPage']['key']
			),
		));

		return array_values($quizPageIds);
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
			$tmpQ = array();
			$tmpQ['QuizQuestion'] = $question;
			$tmpQ['Block'] = Current::read('Block');

			$this->create();
			if (! $this->save($tmpQ, false)) {
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

/**
 * deleteQuizQuestion
 *
 * 小テスト問題情報削除、配下の選択肢、正解情報も削除
 *
 * @param int $quizPageId 小テストページID
 * @return bool
 */
	public function deleteQuizQuestion($quizPageId) {
		$quizQuestions = $this->find('all', array(
			'conditions' => array(
				'QuizQuestion.quiz_page_id' => $quizPageId
			),
			'recursive' => -1
		));
		foreach ($quizQuestions as $question) {
			if (! $this->QuizChoice->deleteAll(array(
				'QuizChoice.quiz_question_id' => $question['QuizQuestion']['id']))) {
				return false;
			}
			if (! $this->QuizCorrect->deleteAll(array(
				'QuizCorrect.quiz_question_id' => $question['QuizQuestion']['id']))) {
				return false;
			}
			if (! $this->delete($question['QuizQuestion']['id'], false)) {
				return false;
			}
		}
		return true;
	}

/**
 * getAliveCondition
 * 現在使用中状態であるか判断する。CleanUpプラグインで使用
 *
 * @param array $key 判断対象のデータのキー
 * @return array
 */
	public function getAliveCondition($key) {
		return array(
			'conditions' => array(
				'QuizQuestion.key' => $key,
				'OR' => array(
					'Quiz.is_active' => true,
					'Quiz.is_latest' => true,
				),
			),
			'joins' => array(
				array(
					'table' => 'quiz_pages',
					'alias' => 'QuizPage',
					'type' => 'INNER',
					'conditions' => array(
						$this->alias . '.quiz_page_id = QuizPage.id'
					)
				),
				array(
					'table' => 'quizzes',
					'alias' => 'Quiz',
					'type' => 'INNER',
					'conditions' => array(
						'QuizPage.quiz_id = Quiz.id'
					)
				)
			)
		);
	}
}
