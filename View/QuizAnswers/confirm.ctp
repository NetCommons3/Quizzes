<?php
/**
 * quiz page setting view template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>
<?php echo $this->element('Quizzes.scripts'); ?>

<article id="nc-quizzes-confirm">

	<?php echo $this->element('Quizzes.QuizAnswers/answer_header'); ?>

	<?php echo $this->element('Quizzes.QuizAnswers/answer_test_mode_header'); ?>

	<p>
		<?php echo __d('quizzes', 'Please confirm your answers.'); ?>
	</p>

	<?php echo $this->NetCommonsForm->create('QuizAnswer'); ?>
	<?php echo $this->NetCommonsForm->hidden('Frame.id'); ?>
	<?php echo $this->NetCommonsForm->hidden('Block.id'); ?>
	<?php echo $this->NetCommonsForm->hidden('Quiz.id', array('value' => $quiz['Quiz']['id'])); ?>
	<?php echo $this->NetCommonsForm->hidden('QuizAnswerSummary.id'); ?>

	<?php foreach($quiz['QuizPage'] as $pIndex => $page): ?>
		<?php foreach($page['QuizQuestion'] as $qIndex => $question): ?>

				<div class="well form-control-static">
					<div class="form-group">
					<label class="pull-right text-muted">
						<?php echo sprintf(__d('quizzes', '(Allotment %3d)'), $question['allotment']); ?>
					</label>

					<label class="control-label">
						<?php echo sprintf(__d('quizzes', 'Question %2d :'), $question['serial_number'] + 1); ?>
					</label>

					<p >
						<?php echo h($question['question_value']); ?>
					</p>

					<?php echo $this->QuizAnswer->answer($question, true); ?>
					</div>
				</div>
		<?php endforeach; ?>
	<?php endforeach; ?>

	<div class="text-center">

		<a class="btn btn-default" href="<?php echo $this->NetCommonsHtml->url(array(
																	'controller' => 'quiz_answers',
																	'action' => 'view',
																	'block_id' => Current::read('Block.id'),
																	'key' => $quiz['Quiz']['key'],
																	'frame_id' => Current::read('Frame.id'))); ?>">
			<span class="glyphicon glyphicon-chevron-left"></span>
			<?php echo __d('quizzes', 'Start over'); ?>
		</a>

		<?php echo $this->NetCommonsForm->button(
		__d('net_commons', 'Confirm'),
		array(
		'class' => 'btn btn-primary',
		'name' => 'confirm_' . 'quiz',
		)) ?>
	</div>
	<?php echo $this->NetCommonsForm->end(); ?>

</article>