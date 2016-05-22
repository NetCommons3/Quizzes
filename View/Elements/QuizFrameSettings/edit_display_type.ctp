<?php
/**
 * Quiz frame display setting
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>
<label><?php echo __d('quizzes', 'Quiz display setting'); ?></label>
<?php echo $this->NetCommonsForm->radio('display_type',
array(
QuizzesComponent::DISPLAY_TYPE_SINGLE => __d('quizzes', 'Show only one quiz'),
QuizzesComponent::DISPLAY_TYPE_LIST => __d('quizzes', 'Show quizzes list')),
array(
	'div' => 'form-inline',
	'hiddenField' => false,
	'ng-model' => 'quizFrameSettings.displayType',
	'error' => true,
));
