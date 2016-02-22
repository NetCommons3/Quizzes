<?php
/**
 * quiz accordion heading template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>

<div class="pull-right" ng-if="isPublished == 0">
	<div class="btn-group" dropdown dropdown-append-to-body>
		<button type="button" class="btn btn-default" dropdown-toggle >
			<?php echo __d('quizzes', 'copy to another page'); ?>
			<span class="caret"></span>
		</button>
		<ul class="dropdown-menu" role="menu">
			<li role="presentation" class="dropdown-header"><?php echo __d('quizzes', 'destination page number'); ?></li>
			<li ng-repeat="(copyPageIndex, copyPage) in quiz.quizPage">
				<a href="#" ng-click="copyQuestionToAnotherPage($event, pageIndex, qIndex, copyPage.pageSequence)">{{1 * copyPage.pageSequence + 1}}</a>
			</li>
		</ul>
	</div>
	<button class="btn btn-danger " type="button"
			ng-disabled="page.quizQuestion.length < 2"
			ng-click="deleteQuestion($event, pageIndex, qIndex, '<?php echo __d('quizzes', 'Do you want to delete this question ?'); ?>')">
		<span class="glyphicon glyphicon-remove"> </span>
	</button>
</div>

<button ng-if="isPublished == 0"
		class="btn btn-default pull-left"
		type="button"
		ng-disabled="$first"
		ng-click="moveQuestion($event, pageIndex, qIndex, qIndex-1)">
	<span class="glyphicon glyphicon-arrow-up"></span>
</button>

<button ng-if="isPublished == 0"
		class="btn btn-default pull-left"
		type="button"
		ng-disabled="$last"
		ng-click="moveQuestion($event, pageIndex, qIndex, qIndex+1)">
	<span class="glyphicon glyphicon-arrow-down"></span>
</button>

<span class="quiz-accordion-header-title">
	<span class="glyphicon glyphicon-exclamation-sign text-danger" ng-if="question.hasError"></span>
	{{question.questionValue | htmlToPlaintext : 15}}
</span>

<span class="glyphicon glyphicon-exclamation-sign text-danger" ng-if="question.hasError"></span>
