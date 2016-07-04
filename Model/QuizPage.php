<?php
/**
 * QuizPage Model
 *
 * @property Language $Language
 * @property Quiz $Quiz
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
 * Summary for QuizPage Model
 */
class QuizPage extends QuizzesAppModel {

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
	public $validate = array();

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Quiz' => array(
			'className' => 'Quizzes.Quiz',
			'foreignKey' => 'quiz_id',
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
		'QuizQuestion' => array(
			'className' => 'Quizzes.QuizQuestion',
			'foreignKey' => 'quiz_page_id',
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
			'QuizQuestion' => 'Quizzes.QuizQuestion',
		]);
	}
/**
 * getDefaultPage
 * get default data of quiz page
 *
 * @return array
 */
	public function getDefaultPage() {
		$page = array(
			'page_title' => __d('quizzes', 'First Page'),
			'page_sequence' => 0,
			'key' => '',
			'is_page_description' => QuizzesComponent::USES_NOT_USE,
			'page_description' => '',
		);
		$page['QuizQuestion'][0] = $this->QuizQuestion->getDefaultQuestion();

		return $page;
	}

/**
 * setPageToQuiz
 * setup page data to quiz array
 *
 * @param array &$quiz quiz data
 * @return void
 */
	public function setPageToQuiz(&$quiz) {
		// ページデータが小テストデータの中にない状態でここが呼ばれている場合、
		if (!isset($quiz['QuizPage'])) {
			$pages = $this->find('all', array(
				'conditions' => array(
					'quiz_id' => $quiz['Quiz']['id'],
				),
				'order' => array('page_sequence ASC'),
				'recursive' => -1));

			$quiz['QuizPage'] = Hash::combine($pages, '{n}.QuizPage.page_sequence', '{n}.QuizPage');
		}
		$quiz['Quiz']['page_count'] = 0;
		foreach ($quiz['QuizPage'] as &$page) {
			if (isset($page['id'])) {
				$this->QuizQuestion->setQuestionToPage($quiz, $page);
			}
			$quiz['Quiz']['page_count']++;
		}
	}
/**
 * Called during validation operations, before validation. Please note that custom
 * validation rules can be defined in $validate.
 *
 * @param array $options Options passed from Model::save().
 * @return bool True if validate operation should continue, false to abort
 * @link http://book.cakephp.org/2.0/en/models/callback-methods.html#beforevalidate
 * @see Model::save()
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 */
	public function beforeValidate($options = array()) {
		$pageIndex = $options['pageIndex'];
		// Pageモデルは繰り返し判定が行われる可能性高いのでvalidateルールは最初に初期化
		// mergeはしません
		$this->validate = array(
			'page_sequence' => array(
				'numeric' => array(
					'rule' => array('naturalNumber', true),
					//'allowEmpty' => false,
					//'required' => true,
					'message' => __d('quizzes', 'page sequence is illegal.')
				),
				'comparison' => array(
					'rule' => array('comparison', '==', $pageIndex),
					'message' => __d('quizzes', 'page sequence is illegal.')
				),
			),
		);
		// validates時にはまだquiz_idの設定ができないのでチェックしないことにする
		// quiz_idの設定は上位のQuestionnaireクラスで責任を持って行われるものとする

		parent::beforeValidate($options);

		// 付属の質問以下のvalidate
		if (! isset($this->data['QuizQuestion'][0])) {
			$this->validationErrors['page_pickup_error'][] =
				__d('quizzes', 'please set at least one question.');
		} else {
			$validationErrors = array();
			foreach ($this->data['QuizQuestion'] as $qIndex => $question) {
				// 質問データバリデータ
				$this->QuizQuestion->create();
				$this->QuizQuestion->set($question);
				$options['questionIndex'] = $qIndex;
				if (! $this->QuizQuestion->validates($options)) {
					$validationErrors['QuizQuestion'][$qIndex] =
						$this->QuizQuestion->validationErrors;
				}
			}
			$this->validationErrors += $validationErrors;
		}
		return true;
	}

/**
 * saveQuizPage
 * save QuizPage data
 *
 * @param array &$quizPages quiz pages
 * @throws InternalErrorException
 * @return bool
 */
	public function saveQuizPage(&$quizPages) {
		$this->loadModels([
			'QuizQuestion' => 'Quizzes.QuizQuestion',
		]);

		// QuizPageが単独でSaveされることはない
		// 必ず上位のQuizのSaveの折に呼び出される
		// なので、$this->setDataSource('master');といった
		// 決まり処理は上位で行われる
		// ここでは行わない

		foreach ($quizPages as &$page) {
			// アンケートは履歴を取っていくタイプのコンテンツデータなのでSave前にはID項目はカット
			// （そうしないと既存レコードのUPDATEになってしまうから）
			$page = Hash::remove($page, 'QuizPage.id');
			$this->create();
			if (! $this->save($page, false)) {	// validateは上位のquizで済んでいるはず
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}

			$pageId = $this->id;

			$page = Hash::insert($page, 'QuizQuestion.{n}.quiz_page_id', $pageId);
			// もしもQuestionやChoiceのsaveがエラーになった場合は、
			// QuestionやChoiceのほうでInternalExceptionErrorが発行されるのでここでは何も行わない
			$this->QuizQuestion->saveQuizQuestion($page['QuizQuestion']);
		}
		return true;
	}

}
