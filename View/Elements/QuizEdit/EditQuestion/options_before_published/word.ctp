<?php
/**
 * quizzes word setting view template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>
<div class="panel panel-default">
	<div class="panel-body">
		<?php if (isset($number)): ?>
	<?php echo $number; ?>
		<?php echo $this->NetCommonsForm->input(
			'QuizPage.{{pageIndex}}.QuizQuestion.{{qIndex}}.QuizCorrect.{{correctIndex}}.correct_label',
			array(
			'type' => 'text',
			'ng-model' => 'correct.correctLabel',
			//'ng-value' => '"(' . $number . ')"',
			'size' => 2,
			'style' => 'margin-right: 1em;'
			)
		); ?>
		&nbsp;:&nbsp;
		<?php endif; ?>
		<span class="" ng-repeat="correctWord in correct.correct" >
			<span class="btn btn-default btn-sm"
				  ng-click="removeCorrectWord(event, pageIndex, qIndex, correctIndex, correctWord)">
				{{correctWord}}
				&nbsp;
				<span class="glyphicon glyphicon-remove"><span class="sr-only">Remove this correct</span> </span>
			</span>
			&nbsp;
			<?php echo $this->NetCommonsForm->hidden('QuizPage.{{pageIndex}}.QuizQuestion.{{qIndex}}.QuizCorrect.{{correctIndex}}.correct.',
			array('ng-value' => 'correctWord')); ?>
		</span>
		<span class="help-block" ng-if="correct.correct.length == 0">
			<?php echo __d('quizzes', 'The correct answer is not yet . Please set the word .'); ?>
		</span>
	</div>
</div>
<?php
echo $this->NetCommonsForm->hidden('QuizPage.{{pageIndex}}.QuizQuestion.{{qIndex}}.QuizCorrect.{{correctIndex}}.correctSequence',
	array('ng-value' => '{{correctIndex}}'));
?>

<div class="form-horizontal">
	<div class="quiz-edit-add-word-area">
		<?php echo $this->NetCommonsForm->label('', __d('quizzes', 'Correct word')); ?>
		<input class="form-control" type="text" ng-model="correct.newWordCorrect" ng-change="change()"/>
		<button
				type="button" class="btn btn-default btn-sm"
				ng-click="addCorrectWord($event, pageIndex, qIndex, correctIndex, correct.newWordCorrect)">
			<span class=""><?php echo __d('net_commons', 'Add') ?></span>
		</button>
	</div>
</div>
