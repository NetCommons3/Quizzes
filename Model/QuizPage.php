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
		'Wysiwyg.Wysiwyg' => array(
			'fields' => array('page_description'),
		),
		'M17n.M17n' => array(
			'associations' => array(
				'QuizQuestion' => array(
					'class' => 'Quizzes.QuizQuestion',
					'foreignKey' => 'quiz_page_id',
				),
			),
			'afterCallback' => false,
		)
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
 * getPageForQuiz
 * setup page data to quiz array
 *
 * @param array &$quiz quiz data
 * @param array $pages all quiz-page data in this quiz
 * @param array $questions all quiz-question data in this quiz
 * @return void
 */
	public function getPageForQuiz(&$quiz, $pages, $questions) {
		$targetPages = Hash::extract(
			$pages,
			'{n}.QuizPage[quiz_id=' . $quiz['Quiz']['id'] . ']'
		);
		$targetPages = Hash::sort($targetPages, '{n}.page_sequence', 'asc');
		foreach ($targetPages as &$page) {
			if (isset($page['id'])) {
				$this->QuizQuestion->getQuestionForPage($page, $questions);
			}
		}
		$quiz['QuizPage'] = $targetPages;
		$quiz['Quiz']['page_count'] = count($targetPages);
		$quiz['Quiz']['question_count'] = array_sum(
			Hash::extract($targetPages, '{n}.question_count')
		);
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
		if (! $this->id && isset($query['conditions']['quiz_id'])) {
			$query['conditions']['quiz_id'] = $this->getQuizIdsForM17n($query['conditions']['quiz_id']);
			$query['conditions']['OR'] = array(
				'language_id' => Current::read('Language.id'),
				'is_translation' => false,
			);

			return $query;
		}

		return parent::beforeFind($query);
	}

/**
 * 多言語データ取得のため、当言語のquiz_idから全言語のquiz_idを取得する
 *
 * @param id $quizId 当言語のquiz_id
 * @return array
 */
	public function getQuizIdsForM17n($quizId) {
		$quiz = $this->Quiz->find('first', array(
			'recursive' => -1,
			'callbacks' => false,
			'fields' => array('id', 'key', 'is_active', 'is_latest'),
			'conditions' => array('id' => $quizId),
		));

		$conditions = array(
			'key' => Hash::get($quiz, 'Quiz.key', '')
		);
		if (Hash::get($quiz, 'Quiz.is_latest')) {
			$conditions['is_latest'] = true;
		} else {
			$conditions['is_active'] = true;
		}

		$quizIds = $this->Quiz->find('list', array(
			'recursive' => -1,
			'callbacks' => false,
			'fields' => array('id', 'id'),
			'conditions' => $conditions,
		));

		return array_values($quizIds);
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

				$data = $this->QuizQuestion->data['QuizQuestion'];
				unset($this->QuizQuestion->data['QuizQuestion']);
				$this->data['QuizQuestion'][$qIndex] = array_merge($data, $this->QuizQuestion->data);
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
			$tmpPage = array();
			$tmpPage['QuizPage'] = $page;
			$tmpPage['Block'] = Current::read('Block');
			$this->create();
			if (! $this->save($tmpPage, false)) {	// validateは上位のquizで済んでいるはず
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

/**
 * deleteQuizPage
 *
 * 小テストページ情報削除、配下の質問情報に削除命令を実施
 *
 * @param int $quizId 小テストID
 * @return bool
 */
	public function deleteQuizPage($quizId) {
		$quizPages = $this->find('all', array(
			'conditions' => array(
				'QuizPage.quiz_id' => $quizId
			),
			'recursive' => -1
		));
		foreach ($quizPages as $page) {
			if (! $this->QuizQuestion->deleteQuizQuestion($page['QuizPage']['id'])) {
				return false;
			}
			if (! $this->delete($page['QuizPage']['id'], false)) {
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
				'QuizPage.key' => $key,
				'OR' => array(
					'Quiz.is_active' => true,
					'Quiz.is_latest' => true,
				),
			),
			'joins' => array(
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
