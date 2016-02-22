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
					echo $this->QuestionEdit->quizAttributeCheckbox('is_no_member_allow',
	__d('quizzes', '非会員の回答を許す' . '<span class="text-muted" ng-show="quiz.quiz.isNoMemberAllow == 1">　　非会員にも回答を許す場合は、画像認証または認証キーのチェックをONにすることを推奨します。</span>'));
	?>

	<div ng-show="quiz.quiz.isNoMemberAllow == 1" style="padding-left:30px;">
		<?php
					echo $this->QuestionEdit->quizAttributeCheckbox('is_image_authentication',
		__d('quizzes', 'テスト開始時に画像認証を求める'),
		array(
		'ng-disabled' => 'quiz.quiz.isKeyPassUse == ' . QuizzesComponent::USES_USE));

		echo $this->QuestionEdit->quizAttributeCheckbox('is_key_pass_use',
		__d('quizzes', 'テスト開始時に認証キーの入力を求める'),
		array(
		'ng-disabled' => 'quiz.quiz.isImageAuthentication == ' . QuizzesComponent::USES_USE));

		echo $this->element('AuthorizationKeys.edit_form', [
		'options' => array(
		'ng-show' => 'quiz.quiz.isKeyPassUse != 0',
		)]);
		?>
		<?php
			echo $this->NetCommonsForm->hidden('is_open_mail_send', array('value' => QuizzesComponent::USES_NOT_USE));
		echo $this->NetCommonsForm->hidden('open_mail_subject', array('value' => ''));
		echo $this->NetCommonsForm->hidden('open_mail_body', array('value' => ''));
		?>
	</div>
</div>
