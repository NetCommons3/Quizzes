<div class="form-inline">
	<div>
		<input class="form-control" type="text" ng-model="correct.newWordCorrect" ng-change="change()"/>
		<button type="button" class="btn btn-default btn-sm" ng-click="addCorrectWord($event, pageIndex, qIndex, correctIndex, correct.newWordCorrect)">
			<span class=""><?php echo __d('quizzes', 'Add Correct') ?></span>
		</button>
	</div>

	<div>
		<span ng-repeat="correctWord in correct.correctSplit" >
			<span class="label label-default" ng-click="removeCorrectWord(event, pageIndex, qIndex, correctIndex, correctWord)" style="cursor:pointer ">
				{{correctWord}}
				&nbsp;
				<span class="glyphicon glyphicon-remove"><span class="sr-only">Remove this correct</span> </span>
			</span>
			&nbsp;
		</span>
	</div>
	<?php echo
	$this->NetCommonsForm->hidden('QuizPage.{{pageIndex}}.QuizQuestion.{{qIndex}}.QuizCorrect.{{correctIndex}}.correct', array(
	'ng-value' => 'correct.correct')); ?>
</div>