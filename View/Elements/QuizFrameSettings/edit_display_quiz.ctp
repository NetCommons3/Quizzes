<?php
/**
 * Quiz frame display setting
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>
<?php echo $this->NetCommonsForm->label(__d('quizzes', 'select display quizzes.')); ?>
<?php $this->NetCommonsForm->unlockField('List'); ?>
<?php echo $this->NetCommonsForm->hidden('Single.QuizFrameDisplayQuiz.quiz_key', array('value' => '')); ?>

<div class="quiz-list-wrapper">
	<table class="table table-hover quiz-table-vcenter">
		<tr>
			<th>
				<div class="text-center" ng-if="quizFrameSettings.displayType == <?php echo QuizzesComponent::DISPLAY_TYPE_LIST; ?>">
					<?php $this->NetCommonsForm->unlockField('all_check'); ?>
					<?php echo $this->NetCommonsForm->checkbox('all_check', array(
					'ng-model' => 'WinBuf.allCheck',
					'ng-change' => 'allCheckClicked()',
					)); ?>
				</div>
			</th>
			<th>
				<a href="" ng-click="status=!status;sort('quizzes.status', status)"><?php echo __d('quizzes', 'Status'); ?></a>
			</th>
			<th>
				<a href="" ng-click="title=!title;sort('quizzes.title', title)"><?php echo __d('quizzes', 'Title'); ?></a>
			</th>
			<th>
				<a href="" ng-click="answerStartPeriod=!answerStartPeriod;sort('quizzes.answerStartPeriod', answerStartPeriod)"><?php echo __d('quizzes', 'Implementation date'); ?></a>
			</th>
			<th>
				<a href="" ng-click="modified=!modified;sort('quizzes.modified', modified)"><?php echo __d('net_commons', 'Updated date'); ?></a>
			</th>
		</tr>
		<tr class="animate-repeat btn-default" ng-repeat="(index, quiz) in quizzes">
			<td>
				<div class="text-center" ng-show="quizFrameSettings.displayType == <?php echo QuizzesComponent::DISPLAY_TYPE_LIST; ?>">
					<?php echo $this->NetCommonsForm->checkbox('List.QuizFrameDisplayQuiz.{{index}}.is_display', array(
					'options' => array(true => ''),
					'label' => false,
					'div' => 'form-inline',
					'ng-model' => 'isDisplay[index]'
					));
					?>
					<?php echo $this->NetCommonsForm->hidden('List.QuizFrameDisplayQuiz.{{index}}.quiz_key', array('ng-value' => 'quiz.quiz.key')); ?>
				</div>
				<div class="text-center"  ng-show="quizFrameSettings.displayType == <?php echo QuizzesComponent::DISPLAY_TYPE_SINGLE; ?>">
					<?php echo $this->NetCommonsForm->radio('Single.QuizFrameDisplayQuiz.quiz_key',
					array('{{quiz.quiz.key}}' => ''), array(
					'label' => false,
					'div' => 'form-inline',
					'hiddenField' => false,
					'ng-model' => 'quiz.quizFrameDisplayQuiz.quizKey',
					));
					?>
				</div>
			</td>
			<td>
				<?php echo $this->element('Quizzes.ng_status_label', array('status' => 'quiz.quiz.status', 'periodRangeStat' => 'quiz.quiz.periodRangeStat')); ?>
			</td>
			<td>
				<img ng-if="quiz.quiz.titleIcon != ''" ng-src="{{quiz.quiz.titleIcon}}" class="nc-title-icon" />
				{{quiz.quiz.title}}
			</td>
			<td>
				<span ng-if="quiz.quiz.answerTiming == <?php echo QuizzesComponent::USES_USE; ?>">
				(
					{{quiz.quiz.answerStartPeriod | ncDatetime}}
					<?php echo __d('quizzes', ' - '); ?>
					{{quiz.quiz.answerEndPeriod | ncDatetime}}
					<?php echo __d('quizzes', 'Implementation'); ?>
					)
				</span>
			</td>
			<td>
				{{quiz.quiz.modified | ncDatetime}}
			</td>
		</tr>
	</table>
</div>
