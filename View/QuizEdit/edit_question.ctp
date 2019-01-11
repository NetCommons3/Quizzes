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
$jsPostData = $this->QuizQuestionEdit->getJsPostData($quizKey, $ajaxPostUrl);
?>
<?php echo $this->element('NetCommons.javascript_alert'); ?>

<?php
	if ($isPublished) {
		$elementFolder = 'Quizzes.QuizEdit/EditQuestion/options_after_published/';
	} else {
		$elementFolder = 'Quizzes.QuizEdit/EditQuestion/options_before_published/';
	}
?>
<?php if (Current::permission('block_editable') && (Current::isSettingMode() || $this->PageLayout->layoutSetting)) : ?>
	<?php echo $this->BlockTabs->main(BlockTabsHelper::MAIN_TAB_BLOCK_INDEX); ?>
<?php endif ?>

<article id="nc-quizzes-question-edit"
	 ng-controller="QuizzesEditQuestion"
	 ng-init="initialize(<?php echo $isPublished; ?>,
	 						'<?php echo $ajaxPostUrl; ?>',
	 						<?php echo h(json_encode($jsPostData)); ?>,
							<?php echo h(json_encode($jsQuiz)); ?>
							)">

	<?php
	echo $this->NetCommonsForm->create('QuizQuestion', $formOptions);
	echo $this->NetCommonsForm->hidden('Frame.id');
	echo $this->NetCommonsForm->hidden('Block.id');
	echo $this->NetCommonsForm->hidden('Quiz.key');
	?>

	<?php echo $this->element('Quizzes.QuizEdit/quiz_title'); ?>

	<?php echo $this->Wizard->navibar('edit_question'); ?>

	<?php $this->NetCommonsForm->unlockField('QuizPage'); ?>

	<div class="panel panel-default">

		<div class="panel-body">

			<uib-tabset>
				<uib-tab ng-repeat="(pageIndex, page) in quiz.quizPage" active="page.tabActive">
					<uib-tab-heading ng-cloak>
						{{pageIndex+1}}<span class="glyphicon glyphicon-exclamation-sign text-danger" ng-if="page.hasError"></span>
					</uib-tab-heading>

					<div class="tab-body">
						<?php echo $this->QuizQuestionEdit->quizNgError('page.pagePickupError'); ?>

						<div class="row">
							<?php echo $this->element($elementFolder . 'page_lead'); ?>
						</div>

						<?php echo $this->element('Quizzes.QuizEdit/EditQuestion/add_question_button'); ?>
						<div class="clearfix"></div>

							<div uib-accordion ng-cloak close-others="true">
								<div uib-accordion-group
										class="form-horizontal panel-default"
										ng-repeat="(qIndex, question) in page.quizQuestion"
										is-open="question.isOpen">

									<div uib-accordion-heading>
										<?php /* 質問ヘッダーセット（移動ボタン、削除ボタンなどの集合体 */
											echo $this->element('Quizzes.QuizEdit/EditQuestion/accordion_heading'); ?>
										<div class="clearfix"></div>
									</div>
									<?php echo $this->element('Quizzes.QuizEdit/EditQuestion/hidden_question_info_set'); ?>
									<?php /* ここから質問本体設定 */

										/* 配点 */
										echo $this->QuizQuestionEdit->questionInput('QuizPage.{{pageIndex}}.QuizQuestion.{{qIndex}}.allotment',
											__d('quizzes', 'Allotment'),
											array(	'type' => 'number',
													'required' => true,
													'div' => 'form-inline',
													'class' => 'form-control',
													'string-to-number' => '',
													'ng-model' => 'question.allotment',
													'disabled' => $isPublished,
													'after' => ' / ' . '{{getAllotmentSum()}}',
											));
										/* 質問文 問題文を入れてください */
										echo $this->QuizQuestionEdit->questionInput('QuizPage.{{pageIndex}}.QuizQuestion.{{qIndex}}.question_value',
											__d('quizzes', 'question sentence'),
											array('type' => 'wysiwyg',
											'ui-tinymce' => 'tinymce.options',
											'required' => true,
											'placeholder' => __d('quizzes', 'Please input the question statement'),
											'id' => false,
											'ng-model' => 'question.questionValue',
											'disabled' => $isPublished,
										));
										/* 質問種別 */
										/* 質問種別 */
										echo $this->QuizQuestionEdit->questionInput('QuizPage.{{pageIndex}}.QuizQuestion.{{qIndex}}.question_type',
											__d('quizzes', 'Question type'),
											array('type' => 'select',
												'required' => true,
												'options' => $questionTypeOptions,
												'ng-model' => 'question.questionType',
												'ng-change' => 'changeQuestionType($event, {{pageIndex}}, {{qIndex}})',
												'disabled' => $isPublished,
												'empty' => null
											));
									?>
									<div class="row form-group">
										<div class="col-xs-12">
											<div class="well">

												<?php echo $this->QuizQuestionEdit->quizNgError('question.questionPickupError'); ?>

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
							</div>
						</div>


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
		<div class="panel-footer text-center">
			<?php echo $this->Wizard->buttons(
				'edit_question',
				$cancelUrl,
				[],
				['type' => 'button', 'ng-click' => 'post(\'edit_question\')'],
				true); ?>
		</div>
		<?php echo $this->NetCommonsForm->end(); ?>
		<?php echo $this->QuizQuestionEdit->quizGetFinallySubmit($postUrl); ?>
	</div>

</article>