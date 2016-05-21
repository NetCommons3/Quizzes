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
<?php /* アンケート期間設定 */
echo $this->QuestionEdit->quizAttributeCheckbox('answer_timing',
__d('quizzes', 'limit the answer period'), /* 回答期間を制限する */
array(
'ng-true-value' => '\'' . QuizzesComponent::USES_USE . '\'',
'ng-false-value' => '\'' . QuizzesComponent::USES_NOT_USE . '\'',
));
?>
<div class="row" ng-show="quiz.quiz.answerTiming == '<?php echo QuizzesComponent::USES_USE; ?>'">
	<div class="col-sm-5">
		<?php
							echo $this->QuestionEdit->quizAttributeDatetime('answer_start_period', false,
		array('min' => '', 'max' => 'answer_end_period'));
		?>
	</div>
	<div class="col-sm-1">
		<?php echo __d('quizzes', ' - '); ?>
	</div>
	<div class="col-sm-5">
		<?php
							echo $this->QuestionEdit->quizAttributeDatetime('answer_end_period', false,
		array('min' => 'answer_start_period', 'max' => ''));
		?>
	</div>
</div>
