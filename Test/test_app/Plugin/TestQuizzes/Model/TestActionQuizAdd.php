<?php
/**
 * ActionQuizAddModel
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('ActionQuizAdd', 'Quizzes.Model');

/**
 * ActionQuizAddModel
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Model
 */
class TestActionQuizAdd extends ActionQuizAdd {

/**
 * Use table config
 *
 * @var string
 */
	public $useTable = 'quizzes';

/**
 * Use alias config
 *
 * @var string
 */
	public $alias = 'ActionQuizAdd';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
	);

/**
 * getNewQuiz
 *
 * @return void
 * @throws InternalErrorException
 */
	public function getNewQuiz() {
		App::uses('TemporaryUploadFile', 'TestFiles.Utility');
		$this->returnValue = parent::getNewQuiz();
		return $this->returnValue;
	}
}