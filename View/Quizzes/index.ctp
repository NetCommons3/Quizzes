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

echo $this->element('Quizzes.scripts');
?>

<article id="nc-quizzes-<?php echo Current::read('Frame.id'); ?>" class="nc-content-list">

	<?php echo $this->element('Quizzes.Quizzes/add_button'); ?>

	<div class="pull-left">
		<?php echo $this->element('Quizzes.Quizzes/answer_status'); ?>
	</div>

	<div class="clearfix"></div>

		<?php foreach($quizzes as $quiz): ?>
			<article>
				<div class="row">
					<div class="col-md-9 col-xs-12 quiz-index-title">

						<?php echo $this->QuizStatusLabel->statusLabel($quiz);?>

						<?php echo $this->element('Quizzes.answer_timing', array('quiz' => $quiz)); ?>

						<h2>
							<?php echo $this->element('Quizzes.passed_icon', array('quizKey' => $quiz['Quiz']['key'], 'passQuizKeys' => $passQuizKeys)); ?>
							<?php echo h($quiz['Quiz']['title']); ?>
						</h2>
						<p>
							<?php echo $this->element('Quizzes.pass_line', array('quiz' => $quiz)); ?>
							<?php echo $this->element('Quizzes.answer_count', array('quiz' => $quiz)); ?>
						</p>
					</div>

					<div class="col-md-3 col-xs-12" >
						<div class="pull-right h3">
							<?php echo $this->QuizAnswerButton->getAnswerButtons($quiz); ?>
							<?php echo $this->QuizResultButton->getResultButtons($quiz, array('icon' => 'stats')); ?>
							<div class="clearfix"></div>
						</div>
					</div>
				</div>
				<?php if (in_array($quiz['Quiz']['key'], $notScoringQuizKeys)): ?>
				<div class="col-md-12 col-xs-12 alert alert-warning alert-dismissible" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<?php
					echo $this->QuizGradeLink->getGradePageLink($quiz);
					?>
				</div>
				<?php endif; ?>

				<?php if ($this->Workflow->canEdit('Quiz', $quiz)) : ?>
					<?php echo $this->element('Quizzes.Quizzes/detail_for_editor', array('quiz' => $quiz)); ?>
				<?php endif ?>
			</article>
		<?php endforeach; ?>

	<?php echo $this->element('NetCommons.paginator'); ?>

</article>