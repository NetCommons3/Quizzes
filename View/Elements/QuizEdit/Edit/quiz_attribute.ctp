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
<?php /* アンケートタイトル設定 */
echo $this->NetCommonsForm->input('title', array(
	'label' => __d('quizzes', 'Title'),
	'ng-model' => 'quiz.quiz.title'
));
?>

<?php
if ($isPublished) {
	$disabled = true;
} else {
	$disabled = false;
}
?>

<div class="form-horizontal">
	<div class="form-group">
		<?php
			echo $this->NetCommonsForm->label('estimated_time', __d('quizzes', '時間の目安'), array(
			'class' => 'col-lg-2 control-label'
			));
		?>
		<div class="col-lg-10">
		<?php
		echo $this->NetCommonsForm->input('estimated_time', array(
			'label' => false,
			'type' => 'number',
			'div' => 'form-inline',
			'class' => 'form-control',
			'after' => ' 分',
			'ng-model' => 'quiz.quiz.estimatedTime',
			'disabled' => $disabled,
			'aria-describedby' => 'quizPassLineHelp'
		));
		?>
		</div>
	</div>
	<div class="form-group">
		<?php
			echo $this->NetCommonsForm->label('passing_grade', __d('quizzes', '合格点'), array(
			'class' => 'col-lg-2 control-label'
		));
		?>
		<div class="col-lg-10">
		<?php
		echo $this->NetCommonsForm->input('passing_grade', array(
			'label' => false,
			'type' => 'number',
			'div' => 'form-inline',
			'class' => 'form-control',
			'ng-model' => 'quiz.quiz.passingGrade',
			'after' => ' 点以上を合格とする',
			'disabled' => $disabled,
			'aria-describedby' => 'quizPassLineHelp'
		));
		?>
		</div>
	</div>
	<span id="quizPassLineHelp" class="help-block col-lg-offset-2">※未設定にしておくと合否判定は行われません。　※実施後は変更できません。</span>
</div>

<?php
	echo $this->QuestionEdit->quizAttributeCheckbox('is_repeat_allow',
	__d('quizzes', '繰り返し回答をさせる'));
?>
<div class="quiz-supplemental-item" ng-show="quiz.quiz.isRepeatAllow==1">
	<?php
	echo $this->QuestionEdit->quizAttributeCheckbox('is_repeat_until_passing',
	__d('quizzes', '繰り返しできるのは合格するまでとする'));
	?>
</div>
<?php
echo $this->QuestionEdit->quizAttributeCheckbox('is_page_random',
	__d('quizzes', 'ページの表示順序をランダムにする'));
echo $this->QuestionEdit->quizAttributeCheckbox('is_correct_show',
	__d('quizzes', '採点結果画面に「正解・解説」を表示する。'));
echo $this->QuestionEdit->quizAttributeCheckbox('is_total_show',
	__d('quizzes', '採点結果画面に正答率の集計グラフを合わせて表示する'));
