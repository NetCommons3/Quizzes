<?php
/**
 * quizzes word setting view template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?><div class="row">
	<div class="col-lg-12" ng-repeat="(correctIndex, correct) in question.quizCorrect" >
		<div class="form-inline">
			<?php echo $this->element('Quizzes.QuizEdit/EditQuestion/options_before_published/word'); ?>
		</div>
		<div class="text-info small pull-right">
			<?php echo __d('quizzes', 'You can not use the character of | for choice text '); ?>
		</div>
	</div>
</div>
<?php echo $this->element('Quizzes.QuizEdit/EditQuestion/commentary');
