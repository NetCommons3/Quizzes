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
<?php
	echo $this->QuizQuestionEdit->quizAttributeCheckbox('is_no_member_allow', __d('quizzes', 'Answer of nonmember'));
?>

<div class="col-xs-11 col-xs-offset-1" ng-show="quiz.quiz.isNoMemberAllow == 1" >
	<?php
	/* テスト開始時に画像認証を求める */
	echo $this->QuizQuestionEdit->quizAttributeCheckbox('is_image_authentication',
	__d('quizzes', 'image authentication at the start'),
	array(
	'ng-disabled' => 'quiz.quiz.isKeyPassUse == ' . QuizzesComponent::USES_USE));

	/* テスト開始時に認証キーの入力を求める */
	echo $this->QuizQuestionEdit->quizAttributeCheckbox('is_key_pass_use',
	__d('quizzes', 'key phrase at the start'),
	array(
	'ng-disabled' => 'quiz.quiz.isImageAuthentication == ' . QuizzesComponent::USES_USE));

	echo $this->element('AuthorizationKeys.edit_form', [
	'options' => array(
	'ng-show' => 'quiz.quiz.isKeyPassUse != 0',
	)]);
	?>
	<span class="help-block">
		<?php
			/* 非会員にも回答を許す場合は、画像認証または認証キーのチェックをONにすることを推奨します。 */
			echo __d('quizzes', 'If you forgive the answer also to non-members , it is recommended that you check the image authentication or key phrase to ON.');
		?>
	</span>
</div>
