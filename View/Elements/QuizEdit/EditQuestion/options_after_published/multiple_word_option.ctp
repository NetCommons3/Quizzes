<?php
/**
 * 複数単語設定テンプレート
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>
<div class="row">
	<div class="col-sm-12">
		<label class="checkbox-inline">
			<?php echo $this->NetCommonsForm->checkbox('QuizPage.{{pageIndex}}.QuizQuestion.{{qIndex}}.is_order_fixed',
			array(
			'value' => QuizzesComponent::USES_USE,
			'ng-model' => 'question.isOrderFixed',
			'ng-checked' => 'question.isOrderFixed == ' . QuizzesComponent::USES_USE,
			'disabled' => 'disabled',
			));
			?>
			<?php echo __d('quizzes', 'the word order to a fixed'); ?>
		</label>
	</div>
</div>
<div class="row">
	<div class="col-lg-12" ng-repeat="(correctIndex, correct) in question.quizCorrect" >
		<div class="form-inline well">
			<?php echo $this->element('Quizzes.QuizEdit/EditQuestion/options_after_published/word'); ?>
		</div>
	</div>
</div>
<?php echo $this->element('Quizzes.QuizEdit/EditQuestion/commentary');
