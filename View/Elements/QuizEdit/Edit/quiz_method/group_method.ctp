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
<div class="form-group quiz-group">

	<?php
	echo $this->NetCommonsForm->hidden('is_no_member_allow', array('value' => QuizzesComponent::USES_NOT_USE));
	echo $this->NetCommonsForm->hidden('is_image_authentication', array('value' => QuizzesComponent::USES_NOT_USE));
	echo $this->NetCommonsForm->hidden('is_key_pass_use', array('value' => QuizzesComponent::USES_NOT_USE));

	echo $this->QuestionEdit->quizAttributeCheckbox('is_open_mail_send',
	__d('quizzes', '小テスト開始時に会員にメールを送る'));
	?>
	<div ng-show="quiz.quiz.isOpenMailSend == <?php echo QuizzesComponent::USES_USE; ?>">
		<?php
						echo $this->NetCommonsForm->input('open_mail_subject', array(
		'label' => __d('quizzes', 'open mail subject'),
		'ng-model' => 'quizzes.quiz.openMailSubject'));
		echo $this->NetCommonsForm->wysiwyg('open_mail_body', array(
		'label' => __d('quizzes', 'open mail text'),
		'ng-model' => 'quizzes.quiz.openMailBody'));
		?>
	</div>
</div>
