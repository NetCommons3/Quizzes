<?php
/**
 * 質問の種別によって異なる詳細設定のファイル
 * このファイルでは択一選択、複数選択タイプをフォローしている
 * 発行後は編集できなくなるため、変数不可のテンプレートを用意している
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
		<?php /* 選択肢を横並びにする */ ?>
		<label class="checkbox-inline">
			<?php echo $this->NetCommonsForm->checkbox('QuizPage.{{pageIndex}}.QuizQuestion.{{qIndex}}.is_choice_horizon',
			array(
			'value' => QuizzesComponent::USES_USE,
			'ng-model' => 'question.isChoiceHorizon',
			'ng-checked' => 'question.isChoiceHorizon == ' . QuizzesComponent::USES_USE,
			'disabled' => 'disabled',
			));
			?>
			<?php echo __d('quizzes', 'horizontal choices'); /* 横並びに表示する */ ?>
		</label>
		<?php /* 選択肢をランダムにする */ ?>
		<label class="checkbox-inline">
			<?php echo $this->NetCommonsForm->checkbox('QuizPage.{{pageIndex}}.QuizQuestion.{{qIndex}}.is_choice_random',
			array(
			'value' => QuizzesComponent::USES_USE,
			'ng-model' => 'question.isChoiceRandom',
			'ng-checked' => 'question.isChoiceRandom == ' . QuizzesComponent::USES_USE,
			'disabled' => 'disabled',
			));
			?>
			<?php echo __d('quizzes', 'randomaize choices'); /* ランダムな順番で表示する */?>
		</label>
	</div>
</div>
<div class="row">
	<div class="col-xs-12">

		<ul class="list-group quiz-edit-choice-list-group">
			<li class="list-group-item" ng-repeat="(cIndex, choice) in question.quizChoice" >
				<div class="form-inline">
					<?php echo $this->element('Quizzes.QuizEdit/EditQuestion/options_after_published/choice'); ?>
					<span class="text-info pull-right" ng-if="question.quizCorrect[0].correct.indexOf(choice.choiceLabel) !== -1">
						<?php echo __d('quizzes', 'correct answer'); /* 正解 */ ?>
					</span>
				</div>
			</li>
		</ul>
	</div>
</div>

<?php echo $this->element('Quizzes.QuizEdit/EditQuestion/commentary');
