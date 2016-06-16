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
			echo $this->NetCommonsForm->label(
			'estimated_time',
			__d('quizzes', 'Estimated time'), /* 時間の目安 */
			array(
			'class' => 'col-xs-2 control-label'
			));
		?>
		<div class="col-xs-10">
		<?php
		echo $this->NetCommonsForm->input('estimated_time', array(
			'label' => false,
			'type' => 'number',
			'div' => 'form-inline',
			'class' => 'form-control',
			'after' => __d('quizzes', ' min'), /* 分 */
			'min' => 0,
			'ng-model' => 'quiz.quiz.estimatedTime',
			'disabled' => $disabled,
			'aria-describedby' => 'quizPassLineHelp',
			'ng-change' => 'changePassLine()'
		));
		?>
		</div>
	</div>
	<div class="form-group">
		<?php
			echo $this->NetCommonsForm->label(
			'passing_grade',
			__d('quizzes', 'Passing score'), /* 合格点 */
			array(
			'class' => 'col-xs-2 control-label'
		));
		?>
		<div class="col-xs-10">
		<?php
		echo $this->NetCommonsForm->input('passing_grade', array(
			'label' => false,
			'type' => 'number',
			'div' => 'form-inline',
			'class' => 'form-control',
			'ng-model' => 'quiz.quiz.passingGrade',
			'after' => __d('quizzes', ' it\'s the passing score') . ' / {{quiz.quiz.perfectScore}}', /* 点以上を合格とする */
			'min' => 0,
			'max' => '{{quiz.quiz.perfectScore}}',
			'disabled' => $disabled,
			'aria-describedby' => 'quizPassLineHelp',
			'ng-change' => 'changePassLine()'
		));
		?>
		</div>
	</div>
	<span id="quizPassLineHelp" class="help-block col-xs-offset-2">
		<?php /* ※0にしておくと判定は行われません。　※実施後は変更できません。 */
		echo __d('quizzes', '! If set 0 for passing score, then pass - fail decision will be not performed. <br /> ! Once you start, you cannnot edit it.');
		?>
	</span>
</div>

<?php
	echo $this->QuestionEdit->quizAttributeCheckbox('is_repeat_allow',
	__d('quizzes', 'Repeat answer')); /* 繰り返し回答をさせる */
?>
	<div class="row">
		<div class="col-xs-11 col-xs-offset-1" ng-show="quiz.quiz.isRepeatAllow==1">
			<?php
	echo $this->QuestionEdit->quizAttributeCheckbox('is_repeat_until_passing',
			__d('quizzes', 'is allowed until pass'), /* 繰り返しできるのは合格するまでとする */
			array(
			'ng-disabled' => '!(hasPassLine())'
			));
			?>
		</div>
	</div>
<?php
echo $this->QuestionEdit->quizAttributeCheckbox('is_page_random',
	__d('quizzes', 'Random page')); /* ページの表示順序をランダムにする */
echo $this->QuestionEdit->quizAttributeCheckbox('is_correct_show',
	__d('quizzes', 'display the  correct answer and commentary ')); /*採点結果画面に「正解・解説」を表示する。*/
echo $this->QuestionEdit->quizAttributeCheckbox('is_total_show',
	__d('quizzes', 'Display a graph of the  correct answers ratio')); /* 採点結果画面に正答率の集計グラフを合わせて表示する */
