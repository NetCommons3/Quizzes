<?php
/**
 * answer header view template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>
<?php
	echo $this->NetCommonsForm->hidden('is_no_member_allow', array('value' => QuizzesComponent::USES_NOT_USE));
	echo $this->NetCommonsForm->hidden('is_image_authentication', array('value' => QuizzesComponent::USES_NOT_USE));
	echo $this->NetCommonsForm->hidden('is_key_pass_use', array('value' => QuizzesComponent::USES_NOT_USE));
