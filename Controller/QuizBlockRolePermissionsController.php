<?php
/**
 * QuizBlockRolePermissions Controller
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('QuizzesAppSettingController', 'Quizzes.Controller');

/**
 * QuizBlockRolePermissions Controller
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Quizzes\Controller
 */
class QuizBlockRolePermissionsController extends QuizzesAppSettingController {

/**
 * layout
 *
 * @var array
 */
	public $layout = 'NetCommons.setting';

/**
 * use models
 *
 * @var array
 */
	public $uses = array(
		'Quizzes.QuizSetting',
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
				'edit' => 'block_permission_editable',
			),
		),
	);

/**
 * use helpers
 *
 * @var array
 */
	public $helpers = array(
		'Blocks.BlockRolePermissionForm',
		'Blocks.BlockTabs' => array(
			'mainTabs' => array(
				'block_index' => array(
					'url' => array('controller' => 'quiz_blocks')
				),
				'role_permissions' => array(
					'url' => array('controller' => 'quiz_block_role_permissions')
				),
				'frame_settings' => array(
					'url' => array('controller' => 'quiz_frame_settings')
				),
				'mail_settings' => array(
					'url' => array('controller' => 'quiz_mail_settings')
				),
			),
		),
		'NetCommons.Date',
	);

/**
 * edit
 *
 * @return void
 */
	public function edit() {
		$quizSetting = $this->QuizSetting->getSetting();
		$permissions = $this->Workflow->getBlockRolePermissions(
			array(
				'content_creatable',
				'content_publishable'
			)
		);
		$this->set('roles', $permissions['Roles']);
		if ($this->request->is('post')) {
			if ($this->QuizSetting->saveQuizSetting($this->request->data)) {
				$this->NetCommons->setFlashNotification(__d('net_commons', 'Successfully saved.'), array(
					'class' => 'success',
				));
				$this->redirect(NetCommonsUrl::backToPageUrl(true));
				return;
			}
			$this->NetCommons->handleValidationError($this->QuizSetting->validationErrors);
			$this->request->data['BlockRolePermission'] = Hash::merge(
				$permissions['BlockRolePermissions'],
				$this->request->data['BlockRolePermission']
			);
			return;
		}
		$this->request->data['QuizSetting'] = $quizSetting['QuizSetting'];
		$this->request->data['Block'] = $quizSetting['Block'];
		$this->request->data['BlockRolePermission'] = $permissions['BlockRolePermissions'];
		$this->request->data['Frame'] = Current::read('Frame');
	}
}
