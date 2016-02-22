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
		'quiz_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'page_sequence' => array(
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
 * getDefaultPage
 * get default data of quiz page
 *
 * @return array
 */
	public function getDefaultPage() {
		$this->QuizQuestion = ClassRegistry::init('Quizzes.QuizQuestion', true);

		$page = array(
			'page_title' => __d('quizzes', 'First Page'),
			'page_sequence' => 0,
			'key' => '',
			'route_number' => 0,
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
		$this->QuizQuestion = ClassRegistry::init('Quizzes.QuizQuestion', true);
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
