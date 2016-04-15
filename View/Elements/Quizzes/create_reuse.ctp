<?php
/**
 * quiz add create reuse element
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>

<?php echo $this->NetCommonsForm->radio('create_option',
	array(QuizzesComponent::QUIZ_CREATE_OPT_REUSE => __d('quizzes', 'Re-use past quiz')),
	array('ng-model' => 'createOption',
	'hiddenField' => false,
	'ng-disabled' => 'pastQuizzes.length == 0',
	));
?>
<div class="row form-horizontal" uib-collapse="createOption != '<?php echo QuizzesComponent::QUIZ_CREATE_OPT_REUSE; ?>'">
	<div class="col-lg-11 col-lg-offset-1">
		<div class="form-group">
			<div class="col-lg-3">
				<?php echo $this->NetCommonsForm->label('past_search',
					__d('quizzes', 'Past quiz') . $this->element('NetCommons.required')); ?>
			</div>
			<?php echo $this->NetCommonsForm->input('past_search', array(
				'type' => 'search',
				'label' => false,
				'div' => 'col-lg-9',
				'required' => true,
				'id' => 'quizzes_past_search_filter',
				'ng-model' => 'q.quiz.title',
				'placeholder' => __d('quizzes', 'Refine by entering the part of the quiz name')
			));?>

			<ul class="col-lg-12 quiz-select-box form-control">
				<li class="animate-repeat btn-default"
					ng-repeat="item in pastQuizzes | filter:q" ng-model="$parent.pastQuizSelect"
					uib-btn-radio="item.quiz.id" uncheckable>

					{{item.quiz.title}}

					<?php echo $this->element('Quizzes.ng_status_label',
					array('status' => 'item.quiz.status', 'periodRangeStat' => 'item.quiz.periodRangeStat')); ?>

					<span ng-if="item.quiz.answerTiming == <?php echo QuizzesComponent::USES_USE; ?>">
					(
						{{item.quiz.answerStartPeriod | ncDatetime}}
						<?php echo __d('quizzes', ' - '); ?>
						{{item.quiz.answerEndPeriod | ncDatetime}}
						<?php echo __d('quizzes', 'Implementation'); ?>
					)
					</span>
				</li>
			</ul>
		</div>
		<?php $this->NetCommonsForm->unlockField('past_quiz_id'); ?>
		<?php echo $this->NetCommonsForm->hidden('past_quiz_id', array('ng-value' => 'pastQuizSelect')); ?>
		<div class="has-error">
			<?php echo $this->NetCommonsForm->error('past_quiz_id', null, array('class' => 'help-block')); ?>
		</div>
	</div>
</div>
