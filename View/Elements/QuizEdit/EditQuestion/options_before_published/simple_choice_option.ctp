<?php
/**
 * 質問の種別によって異なる詳細設定のファイル
 * このファイルでは択一選択、複数選択タイプをフォローしている
 * 2015.03.12現在では「テンプレートデータから読み込み」の部分が未対応です
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
		<button type="button" class="btn btn-default pull-right" ng-click="addChoice($event, pageIndex, qIndex, question.quizChoice.length);">
			<span class="glyphicon glyphicon-plus"></span><?php echo __d('quizzes', 'add choices'); ?>
		</button>
		<?php /* 選択肢を横並びにする */ ?>
		<label class="checkbox-inline">
			<?php echo $this->NetCommonsForm->checkbox('QuizPage.{{pageIndex}}.QuizQuestion.{{qIndex}}.is_choice_horizon',
			array(
			'value' => QuizzesComponent::USES_USE,
			'ng-model' => 'question.isChoiceHorizon',
			'ng-checked' => 'question.isChoiceHorizon == ' . QuizzesComponent::USES_USE
			));
			?>
			<?php echo __d('quizzes', 'horizontal choices'); ?>
			<?php echo $this->element(
			'Quizzes.QuizEdit/ng_errors', array(
			'errorArrayName' => 'question.errorMessages.isChoiceHorizon',
			)); ?>
		</label>
		<?php /* 選択肢をランダムにする */ ?>
		<label class="checkbox-inline">
			<?php echo $this->NetCommonsForm->checkbox('QuizPage.{{pageIndex}}.QuizQuestion.{{qIndex}}.is_choice_random',
			array(
			'value' => QuizzesComponent::USES_USE,
			'ng-model' => 'question.isChoiceRandom',
			'ng-checked' => 'question.isChoiceRandom == ' . QuizzesComponent::USES_USE
			));
			?>
			<?php echo __d('quizzes', 'randomaize choices'); ?>
			<?php echo $this->element(
			'Quizzes.QuizEdit/ng_errors', array(
			'errorArrayName' => 'question.errorMessages.isChoiceRandom',
			)); ?>
		</label>
	</div>
</div>
<div class="row">
	<div class="col-sm-12">

		<ul class="list-group quiz-edit-choice-list-group">
			<li class="list-group-item" ng-repeat="(cIndex, choice) in question.quizChoice" >
				<div class="form-inline pull-right">
					<button class="btn btn-default" type="button"
							ng-disabled="question.quizChoice.length < 2"
							ng-click="deleteChoice($event, pageIndex, qIndex, choice.choiceSequence)">
						<span class="glyphicon glyphicon-remove"> </span>
					</button>
				</div>
				<div class="radio pull-right quiz-edit-correct-option" ng-if="question.questionType == <?php echo QuizzesComponent::TYPE_SELECTION; ?>">
					<label>
						<?php echo $this->NetCommonsForm->radio('QuizPage.{{pageIndex}}.QuizQuestion.{{qIndex}}.QuizCorrect.0.correct',
						array('{{choice.choiceLabel}}' => __d('quizzes', '正解にする')),
						array(
						'label' => false,
						'ng-checked' => 'choice.choiceLabel == question.quizCorrect[0].correct',
						'hiddenField' => false
						));
						?>

					</label>
				</div>
				<div class="checkbox pull-right quiz-edit-correct-option" ng-if="question.questionType == <?php echo QuizzesComponent::TYPE_MULTIPLE_SELECTION; ?>">
					<label>
						<?php echo $this->NetCommonsForm->checkbox('QuizPage.{{pageIndex}}.QuizQuestion.{{qIndex}}.QuizCorrect.0.correct.',
						array(
						'label' => false,
						'value' => '{{choice.choiceLabel}}',
						'ng-checked' => 'isCorrect(choice.choiceLabel, question.quizCorrect[0].correct)',
						'hiddenField' => false
						));
						?>
						<?php echo __d('quizzes', '正解にする'); ?>
					</label>
				</div>
				<div class="form-inline">
					<?php echo $this->element('Quizzes.QuizEdit/EditQuestion/options_before_published/choice'); ?>
				</div>
			</li>
		</ul>
		<p class="text-info small pull-right">
			<?php echo __d('quizzes', 'You can not use the character of | for choice text '); ?>
		</p>
	</div>
</div>

<?php echo $this->element('Quizzes.QuizEdit/EditQuestion/commentary'); ?>

<?php
/* まだデータテンプレートからの読み込み方式が提唱されていないのでコメントアウトしておく FUJI: 2015.03.11
<div class="row text-center">
	<select class="form-control">
		<option>データテンプレートから選択肢を読み込む</option>
	</select>
</div>
*/