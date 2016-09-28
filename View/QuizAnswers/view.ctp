<?php
/**
 * quiz answer view template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>
<?php echo $this->element('Quizzes.scripts'); ?>

<article id="nc-quizzes-answer-<?php echo Current::read('Frame.id'); ?>">

	<?php echo $this->element('Quizzes.QuizAnswers/answer_header'); ?>

	<?php echo $this->element('Quizzes.QuizAnswers/answer_test_mode_header'); ?>

	<?php if ($quizPage['page_sequence'] > 0): ?>
		<?php $progress = round(($quizPageIndex / $quiz['Quiz']['page_count']) * 100); ?>
		<div class="row">
			<div class="col-sm-8">
			</div>
			<div class="col-sm-4">
				<div class="progress">
					<uib-progressbar class="progress-striped" value="<?php echo $progress ?>" type="warning"><?php echo $progress ?>%</uib-progressbar>
				</div>
			</div>
		</div>
	<?php endif; ?>

	<?php
		echo $this->NetCommonsForm->create('QuizAnswer', array('type' => 'post'));
		echo $this->NetCommonsForm->hidden('Frame.id');
		echo $this->NetCommonsForm->hidden('Block.id');
		echo $this->NetCommonsForm->hidden('QuizPage.page_sequence');
		echo $this->NetCommonsForm->hidden('QuizAnswerSummary.id');
	?>

	<?php if ($quizPage['is_page_description']): ?>
	<div>
		<?php echo $quizPage['page_description']; ?>
	</div>
	<?php endif; ?>

	<?php foreach($quizPage['QuizQuestion'] as $index => $question): ?>
	<div class="form-group
							<?php if ($this->Form->isFieldError('QuizAnswer.' . $question['key'] . '.answer_value')): ?>
							has-error
							<?php endif ?>">

		<label class="pull-right text-muted">
			<?php /* (配点%3d点) */
			echo __d('quizzes', '(Allotment %3d)',
			$question['allotment']
			); ?>
		</label>

		<label class="control-label">
			<?php /* 問題%2d：*/
			echo __d('quizzes', 'Question %2d :',
			$question['serial_number'] + 1
			); ?>
		</label>

		<p >
			<?php echo $question['question_value']; ?>
		</p>

		<?php echo $this->QuizAnswer->answer($question); ?>
	</div>
	<br />
	<?php endforeach; ?>

	<div class="text-center">
		<?php echo $this->NetCommonsForm->button(
		__d('net_commons', 'NEXT') . ' <span class="glyphicon glyphicon-chevron-right"></span>',
		array(
		'class' => 'btn btn-primary',
		'name' => 'next_' . '',
		)) ?>
	</div>
	<?php echo $this->NetCommonsForm->end(); ?>

</article>
