<?php
/**
 * quiz quiestion setting view template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

echo $this->element('Quizzes.scripts');
echo $this->NetCommonsHtml->script(array(
	'/quizzes/js/quizzes_edit_question.js',
));
$jsQuiz = NetCommonsAppController::camelizeKeyRecursive(QuizzesAppController::changeBooleansToNumbers($this->data));
?>

<?php
	if ($isPublished) {
		$elementFolder = 'Quizzes.QuizEdit/EditQuestion/options_after_published/';
	} else {
		$elementFolder = 'Quizzes.QuizEdit/EditQuestion/options_before_published/';
	}
?>

<div id="nc-quizzes-question-edit"
	 ng-controller="QuizzesEditQuestion"
	 ng-init="initialize(<?php echo Current::read('Frame.id'); ?>,
	 						<?php echo (int)$isPublished; ?>,
							<?php echo h(json_encode($jsQuiz)); ?>,
							'<?php echo h($newQuestionLabel); ?>',
							'<?php echo h($newChoiceLabel); ?>')">

	<?php
	echo $this->NetCommonsForm->create('QuizQuestion', $formOptions);
	echo $this->NetCommonsForm->hidden('Frame.id');
	echo $this->NetCommonsForm->hidden('Block.id');
	echo $this->NetCommonsForm->hidden('Quiz.key');
	/* Wizard中は一時保存ステータスで回さないとWorkflowに叱られる */
	echo $this->NetCommonsForm->hidden('Quiz.status', array('value' => WorkflowComponent::STATUS_IN_DRAFT));
	?>

	<?php $this->NetCommonsForm->unlockField('QuizPage'); ?>

	<div class="modal-body">
		<?php echo $this->QuestionEdit->getEditFlowChart(1); ?>
		<?php echo $this->element('Quizzes.QuizEdit/quiz_title'); ?>
		<uib-tabset>
			<uib-tab ng-repeat="(pageIndex, page) in quiz.quizPage" active="page.tabActive">
				<uib-tab-heading>
					{{pageIndex+1}}<span class="glyphicon glyphicon-exclamation-sign text-danger" ng-if="page.hasError"></span>
				</uib-tab-heading>

				<div class="tab-body">
					<div class="row">
						<?php echo $this->element($elementFolder . 'page_lead'); ?>
					</div>

					<?php echo $this->element('Quizzes.QuizEdit/EditQuestion/add_question_button'); ?>
					<div class="clearfix"></div>

						<uib-accordion close-others="true">
							<uib-accordion-group
									class="form-horizontal"
									ng-repeat="(qIndex, question) in page.quizQuestion"
									is-open="question.isOpen">

								<uib-accordion-heading>
									<?php /* 質問ヘッダーセット（移動ボタン、削除ボタンなどの集合体 */
										echo $this->element('Quizzes.QuizEdit/EditQuestion/accordion_heading'); ?>
									<div class="clearfix"></div>
								</uib-accordion-heading>
								<?php echo $this->element('Quizzes.QuizEdit/EditQuestion/hidden_question_info_set'); ?>

								<?php /* ここから質問本体設定 */

									/* 配点 */
									echo $this->QuestionEdit->questionInput('QuizPage.{{pageIndex}}.QuizQuestion.{{qIndex}}.allotment',
										__d('quizzes', 'Allotment'),
										array(	'type' => 'number',
												'div' => 'form-inline',
												'class' => 'form-control',
												'string-to-number' => '',
												'ng-model' => 'question.allotment',
												'ng-disabled' => 'isPublished != 0',
												'after' => ' / ' . '{{getAllotmentSum()}}',
										));
									/* 質問文 */
									echo $this->QuestionEdit->questionInput('QuizPage.{{pageIndex}}.QuizQuestion.{{qIndex}}.question_value',
										__d('quizzes', 'question sentence'),
										array('type' => 'wysiswyg',
											'required' => true,
											'placeholder' => '問題文を入れてください',
											'id' => false,
											'ng-model' => 'question.questionValue',
											'ui-tinymce' => 'tinymce.options',
											'rows' => 5,
											'ng-disabled' => 'isPublished != 0',
										));
									/* 質問種別 */
									echo $this->QuestionEdit->questionInput('QuizPage.{{pageIndex}}.QuizQuestion.{{qIndex}}.question_type',
										__d('quizzes', 'Question type'),
										array('type' => 'select',
											'required' => true,
											'options' => $questionTypeOptions,
											'ng-model' => 'question.questionType',
											'ng-change' => 'changeQuestionType($event, {{pageIndex}}, {{qIndex}})',
											'ng-disabled' => 'isPublished != 0',
											'empty' => null
										));
								?>
								<div class="row form-group">
									<div class="col-sm-12">
										<div class="well">
											<div ng-if="question.questionType == <?php echo QuizzesComponent::TYPE_SELECTION; ?>">
												<?php echo $this->element($elementFolder . 'simple_choice_option'); ?>
											</div>
											<div ng-if="question.questionType == <?php echo QuizzesComponent::TYPE_MULTIPLE_SELECTION; ?>">
												<?php echo $this->element($elementFolder . 'simple_choice_option'); ?>
											</div>
											<div ng-if="question.questionType == <?php echo QuizzesComponent::TYPE_WORD; ?>">
												<?php echo $this->element($elementFolder . 'word_option'); ?>
											</div>
											<div ng-if="question.questionType == <?php echo QuizzesComponent::TYPE_MULTIPLE_WORD; ?>">
												<?php echo $this->element($elementFolder . 'multiple_word_option'); ?>
											</div>
											<div ng-if="question.questionType == <?php echo QuizzesComponent::TYPE_TEXT_AREA; ?>">
												<?php echo $this->element($elementFolder . 'text_area_option'); ?>
											</div>
										</div>
									</div>
								</div >
						</uib-accordion-group>
					</uib-accordion>


					<?php echo $this->element('Quizzes.QuizEdit/EditQuestion/add_question_button'); ?>

					<div class="text-center" ng-if="isPublished == 0">
						<button class="btn btn-danger" type="button"
								ng-disabled="quiz.quizPage.length < 2"
								ng-click="deletePage($index, '<?php echo __d('quizzes', 'Do you want to delete this page?'); ?>')">
							<span class="glyphicon glyphicon-remove"></span><?php echo __d('quizzes', 'Delete this page'); ?>
						</button>
					</div>
				</div>
			</uib-tab>
			<?php if (! $isPublished): ?>
				<a class="quiz-add-page-tab" ng-click="addPage($event)">
					<span class="glyphicon glyphicon-plus"></span>
					<span class=""><?php echo __d('quizzes', 'Add Page'); ?></span>
				</a>
			<?php endif; ?>
		</uib-tabset>


	</div>
	<div class="modal-footer">
		<div class="text-center">
			<?php echo $this->Button->cancel(__d('net_commons', 'Cancel'), $cancelUrl, array('icon' => 'remove')); ?>
			<?php echo $this->Button->save(__d('net_commons', 'NEXT'), array('icon' => 'chevron-right')) ?>
		</div>
	</div>
	<?php echo $this->NetCommonsForm->end(); ?>
</div>