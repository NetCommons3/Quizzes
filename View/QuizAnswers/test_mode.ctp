<?php
/**
 * quiz page test_mode view template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

echo $this->element('Quizzes.scripts');
?>
<article id="nc-quizzes-answer-<?php Current::read('Frame.id'); ?>">

	<?php echo $this->element('Quizzes.QuizAnswers/answer_test_mode_header'); ?>

	<?php echo $this->element('Quizzes.QuizAnswers/answer_header'); ?>

	<?php echo $this->NetCommonsForm->create('QuizAnswer'); ?>

	<?php echo $this->NetCommonsForm->hidden('Frame.id'); ?>
	<?php echo $this->NetCommonsForm->hidden('Block.id'); ?>

	<div class="row">
		<div class="col-sm-12">
			<h3><?php echo __d('quizzes', 'Quiz answer period'); ?></h3>
			<?php if ($quiz['Quiz']['answer_timing'] == QuizzesComponent::USES_USE): ?>
			<?php echo date('Y/m/d H:i', strtotime($quiz['Quiz']['answer_start_period'])); ?>
			<?php echo __d('quizzes', ' - '); ?>
			<?php echo date('Y/m/d H:i', strtotime($quiz['Quiz']['answer_end_period'])); ?>
			<?php else: ?>
			<?php echo __d('quizzes', 'do not set the answer period'); ?>
			<?php endif; ?>
		</div>
	</div>

	<div class="row">
		<div class="col-sm-12">
			<h3><?php echo __d('quizzes', 'Quiz method'); ?></h3>
			<ul>
				<li>
					<?php if ($quiz['Quiz']['is_no_member_allow'] == QuizzesComponent::USES_USE): ?>
					<?php echo __d('quizzes', 'accept the non-members answer'); ?>
					<?php else: ?>
					<?php echo __d('quizzes', 'do not accept the non-members answer'); ?>
					<?php endif; ?>
				</li>

				<li>
					<?php if ($quiz['Quiz']['is_key_pass_use'] == QuizzesComponent::USES_USE): ?>
					<?php echo __d('quizzes', 'use key phrase'); ?>
					<dl class="dl-horizontal">
						<dt><?php echo __d('quizzes', 'key phrase'); ?>:</dt>
						<dd><?php echo h($quiz['AuthorizationKey']['authorization_key']); ?></dd>
					</dl>
					<?php else: ?>
					<?php echo __d('quizzes', 'do not use key phrase'); ?>
					<?php endif; ?>
				</li>
				<li>
					<?php if ($quiz['Quiz']['is_image_authentication'] == QuizzesComponent::USES_USE): ?>
					<?php echo __d('quizzes', 'do image authentication'); ?>
					<?php else: ?>
					<?php echo __d('quizzes', 'do not image authentication'); ?>
					<?php endif; ?>
				</li>

				<li>
					<?php if ($quiz['Quiz']['is_repeat_allow'] == QuizzesComponent::USES_USE): ?>
					<?php echo __d('quizzes', 'forgive the repetition of the answer'); ?>
					<?php else: ?>
					<?php echo __d('quizzes', 'do not forgive the repetition of the answer'); ?>
					<?php endif; ?>
				</li>
				<li>
					<?php if ($quiz['Quiz']['is_repeat_until_passing'] == QuizzesComponent::USES_USE): ?>
					<?php echo __d('quizzes', 'forgive the repetition of the answer until passing'); ?>
					<?php endif; ?>
				</li>

				<li>
					<?php if ($quiz['Quiz']['is_page_random'] == QuizzesComponent::USES_USE): ?>
					<?php echo __d('quizzes', 'Display order is random page'); ?>
					<?php else: ?>
					<?php echo __d('quizzes', 'Display order of order of page'); ?>
					<?php endif; ?>
				</li>

				<li>
					<?php if ($quiz['Quiz']['is_correct_show'] == QuizzesComponent::USES_USE): ?>
					<?php echo __d('quizzes', 'Correct answer is displayed on the scoring screen'); ?>
					<?php else: ?>
					<?php echo __d('quizzes', 'Correct answer is not displayed'); ?>
					<?php endif; ?>
				</li>

				<li>
					<?php if ($quiz['Quiz']['is_total_show'] == QuizzesComponent::USES_USE): ?>
					<?php echo __d('quizzes', 'Percentage of correct answers are displayed on the scoring screen'); ?>
					<?php else: ?>
					<?php echo __d('quizzes', 'Percentage of correct answers are not displayed'); ?>
					<?php endif; ?>
				</li>

				<li>
					<?php if ($quiz['Quiz']['is_answer_mail_send'] == QuizzesComponent::USES_USE): ?>
					<?php echo __d('quizzes', 'Deliver e-mail when submitted'); ?>
					<?php else: ?>
					<?php echo __d('quizzes', 'do not deliver e-mail when submitted'); ?>
					<?php endif; ?>
				</li>

				<li>
					<?php if ($quiz['Quiz']['is_open_mail_send'] == QuizzesComponent::USES_USE): ?>
					<?php echo __d('quizzes', 'Deliver e-mail when started'); ?>
					<?php else: ?>
					<?php echo __d('quizzes', 'do not deliver e-mail when started'); ?>
					<?php endif; ?>
				</li>
			</ul>
		</div>
	</div>

	<div class="text-center">
		<?php echo $this->BackTo->pageLinkButton(__d('net_commons', 'Cancel'), array('icon' => 'remove')); ?>
		<?php echo $this->NetCommonsForm->button(__d('quizzes', 'Start the test answers of this quiz') . ' <span class="glyphicon glyphicon-chevron-right"></span>',
		array(
		'class' => 'btn btn-primary',
		'name' => 'next_' . '',
		)) ?>
	</div>
	<?php echo $this->NetCommonsForm->end(); ?>

</article>