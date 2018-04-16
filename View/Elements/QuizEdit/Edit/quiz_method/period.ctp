<?php
/**
 * answer header view template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>
<div class="form-group">

	<?php /* アンケート期間設定 */
	echo $this->QuizQuestionEdit->quizAttributeCheckbox('answer_timing',
	__d('quizzes', 'limit the answer period'), /* 回答期間を制限する */
	array());
	?>
	<div class="row" ng-show="quiz.quiz.answerTiming == '<?php echo QuizzesComponent::USES_USE; ?>'">
		<div class="col-xs-11 col-xs-offset-1">
			<div class="form-inline">
				<div class="input-group">
					<?php
						echo $this->QuizQuestionEdit->quizAttributeDatetime('answer_start_period',
						array(
							'label' => false,
							'min' => '',
							'max' => 'answer_end_period',
							'div' => false,
							'error' => false));
					?>
					<span class="input-group-addon">
						<span class="glyphicon glyphicon-minus"></span>
					</span>
					<?php
						echo $this->QuizQuestionEdit->quizAttributeDatetime('answer_end_period',
						array(
							'label' => false,
							'min' => 'answer_start_period',
							'max' => '',
							'div' => false,
							'error' => false
						));
					?>
				</div>
				<?php echo $this->NetCommonsForm->error('answer_start_period'); ?>
				<?php echo $this->NetCommonsForm->error('answer_end_period'); ?>
			</div>
		</div>
	</div>
</div>
