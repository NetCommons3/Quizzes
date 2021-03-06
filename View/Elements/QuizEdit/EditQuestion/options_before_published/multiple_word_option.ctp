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
	<div class="col-xs-12">
		<button type="button" class="btn btn-default pull-right" ng-click="addCorrect($event, pageIndex, qIndex);">
			<span class="glyphicon glyphicon-plus"></span>&nbsp;<?php echo __d('quizzes', 'add words set'); ?>
		</button>
		<label class="checkbox-inline">
			<?php echo $this->NetCommonsForm->checkbox('QuizPage.{{pageIndex}}.QuizQuestion.{{qIndex}}.is_order_fixed',
			array(
			'value' => QuizzesComponent::USES_USE,
			'ng-model' => 'question.isOrderFixed',
			'ng-checked' => 'question.isOrderFixed == ' . QuizzesComponent::USES_USE
			));
			?>
			<?php echo __d('quizzes', 'the word order to a fixed'); ?>
		</label>
	</div>
</div>
<div class="row" ng-repeat="(correctIndex, correct) in question.quizCorrect">
	<div class="col-xs-12">
		<div class="row">
			<div class="col-xs-10 form-inline">
				<?php echo $this->element('Quizzes.QuizEdit/EditQuestion/options_before_published/word', array('number' => '{{correctIndex + 1}}')); ?>
			</div>
			<div class="col-xs-2 text-right">
				<button class="btn btn-default" type="button"
						ng-disabled="question.quizCorrect.length < 2"
						ng-click="deleteCorrect($event, pageIndex, qIndex, correctIndex)">
					<span class="glyphicon glyphicon-remove"> </span>
				</button>
			</div>
		</div>
		<hr ng-if="$last == false" />
	</div>
	<!--
	<div class="text-info small pull-right">
		<?php echo __d('quizzes', 'You can not use the character of |, : for choice text '); ?>
	</div>
	-->
</div>
<?php echo $this->element('Quizzes.QuizEdit/EditQuestion/commentary');
