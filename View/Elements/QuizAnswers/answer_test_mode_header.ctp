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
<?php if ($this->Workflow->canEdit('Quiz', $quiz)) : ?>

	<?php
		$answerHeaderClass = '';
		if ($quiz['Quiz']['status'] != WorkflowComponent::STATUS_PUBLISHED) {
			$answerHeaderClass = 'alert alert-info';
		}
	?>

	<div class="<?php echo $answerHeaderClass; ?>">
		<div class="pull-right">
			<?php echo $this->Button->editLink('', array(
			'plugin' => 'quizzes',
			'controller' => 'quiz_edit',
			'action' => 'edit_question',
			'key' => $quiz['Quiz']['key'])); ?>
		</div>

		<?php if ($quiz['Quiz']['status'] != WorkflowComponent::STATUS_PUBLISHED): ?>
		<h3><?php echo __d('quizzes', 'Test Mode'); ?></h3>
		<div class="clearfix"></div>
		<p>
			<?php echo __d('quizzes',
						'This quiz is being temporarily stored . You can quiz test before performed in this page . If you want to modify or change the quiz , you will be able to edit by pressing the [ Edit question ] button in the upper-right corner .'); ?>
		</p>
		<?php endif; ?>
	</div>

<?php endif;