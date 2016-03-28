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

App::uses('QuizBlocksController', 'Quizzes.Controller');

/**
 * QuizFrameSettingsController
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Controller
 */
class QuizFrameSettingsController extends QuizzesAppController {

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
		if ($this->request->is('put')) {
			if ($this->QuizFrameSetting->saveFrameSettings($this->request->data)) {
				$this->NetCommons->setFlashNotification(__d('net_commons', 'Successfully saved.'), array(
					'class' => 'success',
				));
				$this->redirect(NetCommonsUrl::backToPageUrl());
				return;
			}
			$this->NetCommons->handleValidationError($this->QuizFrameSetting->validationErrors);
		}

		$conditions = array(
			'block_id' => Current::read('Block.id'),
			'is_latest' => true,
		);
		$this->paginate = array(
			'fields' => array('Quiz.*', 'QuizFrameDisplayQuiz.*'),
			'conditions' => $conditions,
			'page' => 1,
			'order' => array('Quiz.created' => 'DESC'),
			'limit' => 1000,
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
		);
		$quizzes = $this->paginate('Quiz');

		$frame = $this->QuizFrameSetting->find('first', array(
			'conditions' => array(
				'frame_key' => Current::read('Frame.key'),
			),
			'order' => 'QuizFrameSetting.id DESC'
		));
		if (!$frame) {
			$frame = $this->QuizFrameSetting->getDefaultFrameSetting();
		}

		$this->set('quizzes', $quizzes);
		$this->set('quizFrameSettings', $frame['QuizFrameSetting']);
		$this->request->data['QuizFrameSetting'] = $frame['QuizFrameSetting'];
		$this->request->data['Frame'] = Current::read('Frame');
		$this->request->data['Block'] = Current::read('Block');
	}
}