<?php
/**
 * Quizzes FrameSettingsController
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('QuizzesAppSettingController', 'Quizzes.Controller');

/**
 * QuizFrameSettingsController
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Controller
 */
class QuizFrameSettingsController extends QuizzesAppSettingController {

/**
 * layout
 *
 * @var array
 */
	public $layout = 'NetCommons.setting';

/**
 * use model
 *
 * @var array
 */
	public $uses = array(
		'Blocks.Block',
		'Frames.Frame',
		'Quizzes.Quiz',
		'Quizzes.QuizFrameSetting',
		'Quizzes.QuizFrameDisplayQuiz',
	);

/**
 * use components
 *
 * @var array
 */
	public $components = array(
		'NetCommons.Permission' => array(
			//アクセスの権限
			'allow' => array(
				'edit' => 'page_editable',
			),
		),
		'Quizzes.Quizzes',
		'Paginator',
	);

/**
 * use helpers
 *
 * @var array
 */
	public $helpers = array(
		'Blocks.BlockTabs' => array(
			'mainTabs' => array(
				'block_index' => array('url' => array('controller' => 'quiz_blocks')),
				'role_permissions' => array('url' => array('controller' => 'quiz_block_role_permissions')),
				'frame_settings' => array('url' => array('controller' => 'quiz_frame_settings')),
				'mail_settings' => array('url' => array('controller' => 'quiz_mail_settings')),
			),
		),
		'NetCommons.DisplayNumber',
		'NetCommons.Date',
	);

/**
 * edit method
 *
 * @return void
 */
	public function edit() {
		// Postデータ登録
		if ($this->request->is('put') || $this->request->is('post')) {
			if ($this->QuizFrameSetting->saveFrameSettings($this->request->data)) {
				$this->NetCommons->setFlashNotification(__d('net_commons', 'Successfully saved.'), array(
					'class' => 'success',
				));
				$this->redirect(NetCommonsUrl::backToPageUrl(true));
				return;
			}
			$this->NetCommons->handleValidationError($this->QuizFrameSetting->validationErrors);
		} else {
			$frame = $this->QuizFrameSetting->find('first', array(
				'conditions' => array(
					'frame_key' => Current::read('Frame.key'),
				),
				'order' => 'QuizFrameSetting.id DESC'
			));
			// ここに来る前にbeforeFilterで必ず$frameに相当する情報は作られていることが
			// 保証されているのでチェックは行わないことにする
			//if (!$frame) {
			//	$frame = $this->QuizFrameSetting->getDefaultFrameSetting();
			//}
			$this->request->data['QuizFrameSetting'] = $frame['QuizFrameSetting'];
			$this->request->data['Frame'] = Current::read('Frame');
			$this->request->data['Block'] = Current::read('Block');
		}

		$quizzes = $this->Quiz->find('all', array(
			'fields' => array(
				'Quiz.id',
				'Quiz.key',
				'Quiz.status',
				'Quiz.title',
				'Quiz.answer_start_period',
				'Quiz.answer_end_period',
				'Quiz.modified',
				'QuizFrameDisplayQuiz.id',
				'QuizFrameDisplayQuiz.frame_key',
				'QuizFrameDisplayQuiz.quiz_key',
			),
			'conditions' => $this->Quiz->getBaseCondition(),
			'order' => array('Quiz.created' => 'DESC'),
			'recursive' => -1,
			'joins' => array(
				array(
					'table' => 'quiz_frame_display_quizzes',
					'alias' => 'QuizFrameDisplayQuiz',
					'type' => 'LEFT',
					'conditions' => array(
						'QuizFrameDisplayQuiz.quiz_key = Quiz.key',
						'QuizFrameDisplayQuiz.frame_key' => Current::read('Frame.key'),
					),
				)
			)
		));
		$this->set('quizzes', $quizzes);
	}
}