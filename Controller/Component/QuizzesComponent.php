<?php
/**
 * Quizzes Component
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('Component', 'Controller');

/**
 * QuizzesComponent
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Questionnaires\Controller
 */
class QuizzesComponent extends Component {

/**
 * quiz create options
 *
 * @var string
 */
	const QUIZ_CREATE_OPT_NEW = 'create';
	const QUIZ_CREATE_OPT_REUSE = 'reuse';
	const QUIZ_CREATE_OPT_TEMPLATE = 'template';

/**
 * permission. permit / not permit
 *
 * @var string
 */
	const PERMISSION_NOT_PERMIT = '0';
	const PERMISSION_PERMIT = '1';

/**
 * uses. use / not use
 *
 * @var string
 */
	const USES_NOT_USE = '0';
	const USES_USE = '1';

/**
 * expression. show / not show
 *
 * @var string
 */
	const EXPRESSION_NOT_SHOW = '0';
	const EXPRESSION_SHOW = '1';

/**
 * action. act / not act
 *
 * @var string
 */
	const ACTION_NOT_ACT = '0';
	const ACTION_BEFORE_ACT = '1';
	const ACTION_ACT = '2';

/**
 * type. selection
 *
 * @var string
 */
	const TYPE_SELECTION = '1';

/**
 * type. multiple selection
 *
 * @var string
 */
	const TYPE_MULTIPLE_SELECTION = '2';

/**
 * type. word
 *
 * @var string
 */
	const TYPE_WORD = '3';

/**
 * type. text area
 *
 * @var string
 */
	const TYPE_TEXT_AREA = '4';

/**
 * type. FILL_BLANK
 *
 * @var string
 */
	const TYPE_MULTIPLE_WORD = '5';

/**
 * types list
 *
 * @var array
 */
	static public $typesList = array(
		self::TYPE_SELECTION,
		self::TYPE_MULTIPLE_SELECTION,
		self::TYPE_WORD,
		self::TYPE_TEXT_AREA,
		self::TYPE_MULTIPLE_WORD,
	);

/**
 * grade status
 *
 * @var string
 */
	const STATUS_GRADE_YET = '0';
	const STATUS_GRADE_FAIL = '1';
	const STATUS_GRADE_PASS = '2';
	const STATUS_GRADE_NONE = '3';
	// NONEの状態値はDBでは使用せず、コード処理で使用

/**
 * display type. single
 *
 * @var string
 */
	const DISPLAY_TYPE_SINGLE = '0';

/**
 * display type. list
 *
 * @var string
 */
	const DISPLAY_TYPE_LIST = '1';

/**
 * test answer status, peform( means on test or Publish )
 *
 * @var string
 */
	const TEST_ANSWER_STATUS_PEFORM = '0';

/**
 * test answer status, test
 *
 * @var string
 */
	const TEST_ANSWER_STATUS_TEST = '1';

/**
 * percentage unit
 * @var string
 */
	const PERCENTAGE_UNIT = '%';

/**
 * not operation(=nop) mark
 * @var string
 */
	const NOT_OPERATION_MARK = '--';

/**
 * answer delimiter
 *
 * @var string
 */
	const ANSWER_DELIMITER = '#||||||#';

/**
 * quiz period stat
 *
 * @var integer
 */
	const QUIZ_PERIOD_STAT_IN = 1;
	const QUIZ_PERIOD_STAT_BEFORE = 2;
	const QUIZ_PERIOD_STAT_END = 3;

/**
 * quiz template exoprt file name
 *
 * @var string
 */
	const QUIZ_TEMPLATE_EXPORT_FILENAME = 'ExportQuiz.zip';
	const QUIZ_TEMPLATE_FILENAME = 'Quizzes.zip';
	const QUIZ_JSON_FILENAME = 'Quizzes.json';
	const QUIZ_FINGER_PRINT_FILENAME = 'finger_print.txt';

/**
 * getSortOrders
 *
 * @return array
 */
	public static function getSortOrders() {
		return array(
			'Quiz.modified DESC' => __d('quizzes', 'New Modified'),
			'Quiz.created ASC' => __d('quizzes', 'Registration order'),
			'Quiz.title ASC' => __d('quizzes', 'Title'),
			'Quiz.answer_end_period ASC' => __d('quizzes', 'End period'),
		);
	}

/**
 * 質問タイプのデータ配列を返す
 *
 * @return array 質問タイプの定値とそれに相応するラベル
 */
	public function getQuestionTypeOptionsWithLabel() {
		return array(
			self::TYPE_SELECTION => __d('quizzes', 'Single choice'),
			self::TYPE_MULTIPLE_SELECTION => __d('quizzes', 'Multiple choice'),
			self::TYPE_WORD => __d('quizzes', 'Word'),
			self::TYPE_MULTIPLE_WORD => __d('quizzes', 'Multiple word'),
			self::TYPE_TEXT_AREA => __d('quizzes', 'Free style'),
		);
	}

/**
 * isSelectionInputType
 *
 * @param int $type quiz type
 * @return bool
 */
	public static function isSelectionInputType($type) {
		// 択一選択、複数選択、リスト選択 などの単純選択タイプであるか
		if ($type == self::TYPE_SELECTION) {
			return true;
		}
		if ($type == self::TYPE_MULTIPLE_SELECTION) {
			return true;
		}
		return false;
	}

/**
 * isMultipleAnswerType
 *
 * @param int $type quiz type
 * @return bool
 */
	public static function isMultipleAnswerType($type) {
		if ($type == self::TYPE_MULTIPLE_SELECTION) {
			return true;
		}
		if ($type == self::TYPE_MULTIPLE_WORD) {
			return true;
		}
		return false;
	}
}
