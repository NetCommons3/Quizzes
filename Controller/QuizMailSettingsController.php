<?php
/**
 * Quiz Mail Setting Controller
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('MailSettingsController', 'Mails.Controller');

/**
 * Quiz Mail Setting Controller
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Questionnaires\Controller
 */
class QuizMailSettingsController extends MailSettingsController {

/**
 * use helpers
 *
 * @var array
 */
	public $helpers = array(
		'Blocks.BlockRolePermissionForm',
		'Blocks.BlockTabs' => array(
			'mainTabs' => array(
				'block_index' => array('url' => array('controller' => 'quiz_blocks')),
				'role_permissions' => array('url' => array('controller' => 'quiz_block_role_permissions')),
				'frame_settings' => array('url' => array('controller' => 'quiz_frame_settings')),
				'mail_settings' => array('url' => array('controller' => 'quiz_mail_settings')),
			),
		),
	);

}
