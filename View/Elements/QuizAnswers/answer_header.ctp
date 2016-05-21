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
<?php if ($this->Workflow->canEdit('Quiz', $quiz)): ?>
	<div class="pull-right">
		<?php echo $this->Button->editLink('', array(
		'plugin' => 'quizzes',
		'controller' => 'quiz_edit',
		'action' => 'edit_question',
		'key' => $quiz['Quiz']['key'])); ?>
	</div>
<?php endif; ?>

<h1>
	<?php echo $this->Workflow->label($quiz['Quiz']['status']); ?>
	<?php echo h($quiz['Quiz']['title']); ?>
	<small>
		<br />
		<?php echo $this->element('Quizzes.pass_line', array('quiz' => $quiz)); ?>
	</small>
</h1>
