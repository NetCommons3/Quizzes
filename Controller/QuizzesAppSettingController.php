<?php
/**
 * Questionnaires AppController
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('AppController', 'Controller');
App::uses('QuizzesComponent', 'Quizzes.Controller/Component');

/**
 * QuizzesAppController
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Questionnaires\Controller
 */
class QuizzesAppSettingController extends QuizzesAppController {

/**
 * use components
 *
 * @var array
 */
	public $components = array(
		'Security',
		'Pages.PageLayout',
		'Quizzes.Quizzes',
	);

/**
 * beforeFilter
 *
 * @return void
 * @throws NotFoundException
 */
	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->deny('index');
		// 設定画面を表示する前にこのルームのアンケートブロックがあるか確認
		// 万が一、まだ存在しない場合には作成しておく
		// afterFrameSaveが呼ばれないような状況の想定
		$frame['Frame'] = Current::read('Frame');
		if (! $frame['Frame']) {
			throw new NotFoundException();
		}
		$this->Quiz->afterFrameSave($frame);
	}
}
