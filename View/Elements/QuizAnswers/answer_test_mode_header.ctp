<?php
/**
 * quiz comment template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>
<?php if ($this->Workflow->canEdit('Quiz', $quiz) && $quiz['Quiz']['status'] != WorkflowComponent::STATUS_PUBLISHED) : ?>

	<div class="alert alert-info">
		<?php echo __d('quizzes', 'This quiz is being temporarily stored . You can quiz test before performed in this page . If you want to modify or change the quiz , you will be able to edit by pressing the [ Edit question ] button in the upper-right corner .'); ?>
	</div>

<?php endif;