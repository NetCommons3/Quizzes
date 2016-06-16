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
 * Travisの環境など、ローカル開発環境以外でテストコードを動作させると
 * テスト用エクスポートテンプレートファイルとバージョンが違うよというエラーが
 * 成功パターンの試験のためにはバージョンチェックは無条件に成功を返してほしい
 * そのために作成されたモック
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Model
 */
class TestActionQuizAddSuccess extends ActionQuizAdd {

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
/**
 * _checkVersion
 *
 * @param array $jsonData バージョンが含まれたJson
 * @return bool
 */
	protected function _checkVersion($jsonData) {
		return true;
	}
}