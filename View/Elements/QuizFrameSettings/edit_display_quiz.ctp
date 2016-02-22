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

<div class="quiz-list-wrapper">
	<table class="table table-hover quiz-table-vcenter">
		<tr>
			<th>
				<?php echo __d('quizzes', 'Display'); ?>
				<div class="text-center" ng-if="quizFrameSettings.displayType == <?php echo QuizzesComponent::DISPLAY_TYPE_LIST; ?>">
					<?php $this->NetCommonsForm->unlockField('all_check'); ?>
					<?php echo $this->NetCommonsForm->checkbox('all_check', array(
					'ng-model' => 'WinBuf.allCheck',
					'ng-change' => 'allCheckClicked()',
					'label' => false,
					'div' => false,
					'class' => '',
					)); ?>
				</div>
			</th>
			<th>
				<?php echo $this->Paginator->sort('Quiz.status', __d('quizzes', 'Status')); ?>
			</th>
			<th>
				<?php echo $this->Paginator->sort('Quiz.title', __d('quizzes', 'Title')); ?>
			</th>
			<th>
				<?php echo $this->Paginator->sort('Quiz.answer_start_period', __d('quizzes', 'Implementation date')); ?>
			</th>
			<th>
				<?php echo $this->Paginator->sort('Quiz.modified', __d('net_commons', 'Updated date')); ?>
			</th>
		</tr>
		<?php foreach ((array)$quizzes as $index => $quest): ?>
		<tr class="animate-repeat btn-default">
			<td>
				<div class="text-center" ng-show="quizFrameSettings.displayType == <?php echo QuizzesComponent::DISPLAY_TYPE_LIST; ?>">
					<?php echo $this->NetCommonsForm->checkbox('List.QuizFrameDisplayQuizzes.' . $index . '.is_display', array(
					'options' => array(true => ''),
					'label' => false,
					'div' => false,
					'class' => '',
					//'value' => $quest['Quiz']['key'],
					//'checked' => (isset($quest['QuizFrameDisplayQuiz']['quiz_key'])) ? true : false,
					'ng-model' => 'isDisplay[' . $index . ']'
					));
					?>
					<?php echo $this->NetCommonsForm->hidden('QuizFrameDisplayQuizzes.' . $index . '.quiz_key', array('value' => $quest['Quiz']['key'])); ?>
				</div>
				<div class="text-center"  ng-show="quizFrameSettings.displayType == <?php echo QuizzesComponent::DISPLAY_TYPE_SINGLE; ?>">
					<?php echo $this->NetCommonsForm->radio('Single.QuizFrameDisplayQuizzes.quiz_key',
					array($quest['Quiz']['key'] => ''), array(
					'legend' => false,
					'label' => false,
					'div' => false,
					'class' => false,
					'hiddenField' => false,
					'checked' => (isset($quest['QuizFrameDisplayQuiz']['quiz_key'])) ? true : false,
					));
					?>
				</div>
			</td>
			<td>
				<?php echo $this->QuizStatusLabel->statusLabelManagementWidget($quest);?>
			</td>
			<td>
				<?php echo $quest['Quiz']['title']; ?>
			</td>
			<td>
				<?php if ($quest['Quiz']['answer_timing'] == QuizzesComponent::USES_USE): ?>
				<?php echo $this->Date->dateFormat($quest['Quiz']['answer_start_period']); ?>
				<?php echo __d('quizzes', ' - '); ?>
				<?php echo $this->Date->dateFormat($quest['Quiz']['answer_end_period']); ?>
				<?php endif ?>
			</td>
			<td>
				<?php echo $this->Date->dateFormat($quest['Quiz']['modified']); ?>
			</td>
		</tr>
		<?php endforeach; ?>
	</table>
</div>

<?php echo $this->element('NetCommons.paginator');

