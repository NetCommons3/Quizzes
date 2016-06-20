<?php
/**
 * quiz start view template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

echo $this->element('Quizzes.scripts');
?>

<article>
	<?php echo $this->element('Quizzes.QuizAnswers/answer_header'); ?>
	<?php echo $this->element('Quizzes.QuizAnswers/answer_test_mode_header'); ?>

	<div class="row">

		<?php echo $this->NetCommonsForm->create('QuizAnswer', array('type' => 'post')); ?>
		<?php echo $this->NetCommonsForm->hidden('Frame.id', array('value' => $frameId)); ?>
		<?php echo $this->NetCommonsForm->hidden('Block.id', array('value' => $blockId)); ?>

		<div class="form-group col-xs-12">
			<p class="lead">
				<?php /* これから小テストを始めます。用意ができたら下の「テストを開始する」ボタンを押して、テストを開始してください。*/
				echo __d('quizzes', 'Start quiz. When you are ready , press the "Start the quiz" button and  start the quiz .'); ?>
			</p>
			<p>
				<?php /* ※回答中のデータが残っている場合には、テストは再開となります。*/
				echo __d('quizzes', 'If you still have the data in the answer , the test will be resumed.'); ?>
			</p>
			<?php if ($quiz['Quiz']['is_image_authentication'] == QuizzesComponent::USES_USE): ?>
				<?php echo $this->element('VisualCaptcha.visual_captcha', array()); ?>
			<?php endif; ?>
			<?php if ($quiz['Quiz']['is_key_pass_use'] == QuizzesComponent::USES_USE): ?>
				<?php echo $this->element('AuthorizationKeys.authorization_key'); ?>
			<?php endif; ?>
		</div>

		<div class="text-center">
			<?php echo $this->BackTo->pageLinkButton(__d('net_commons', 'Cancel'), array('icon' => 'remove')); ?>
			<?php /* テストを開始する */
			echo $this->Button->save(__d('quizzes', 'Start the quiz'), array('icon' => 'chevron-right')) ?>
		</div>

		<?php echo $this->NetCommonsForm->end(); ?>
	</div>
</article>
