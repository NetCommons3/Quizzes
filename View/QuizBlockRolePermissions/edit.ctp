<?php
/**
 * QuizBlockRolePermission edit template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>
<div class="block-setting-body">
	<?php echo $this->BlockTabs->main(BlockTabsComponent::BLOCK_TAB_PERMISSION); ?>

	<div class="tab-content">
		<?php echo $this->element('Blocks.edit_form', array(
		'model' => 'QuizBlockRolePermission',
		'callback' => 'Quizzes.QuizBlockRolePermissions/edit_form',
		'cancelUrl' => NetCommonsUrl::backToIndexUrl('default_setting_action'),
		)); ?>
	</div>
</div>
