<?php
/**
 * quiz setting view template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
echo $this->element('Quizzes.scripts');
echo $this->NetCommonsHtml->script(array(
	'/components/moment/min/moment.min.js',
	'/components/moment/min/moment-with-locales.min.js',
	'/components/tinymce-dist/tinymce.min.js',
	'/components/angular-ui-tinymce/src/tinymce.js',
	'/net_commons/js/wysiwyg.js',
	'/quizzes/js/quizzes_edit.js',
));
$jsQuiz = NetCommonsAppController::camelizeKeyRecursive(QuizzesAppController::changeBooleansToNumbers($this->data));
?>

<?php echo $this->QuestionEdit->getEditFlowChart(2); ?>

<div
	id="nc-quizzes-setting-edit"
	 ng-controller="QuizzesEdit"
	 ng-init="initialize(<?php echo Current::read('Frame.id'); ?>,
	 						<?php echo (int)$isPublished; ?>,
							<?php echo h(json_encode($jsQuiz)); ?>)">

	<?php echo $this->NetCommonsForm->create('Quiz', $formOptions);

		/* NetCommonsお約束:プラグインがデータを登録するところではFrame.id,Block.id,Block.keyの３要素が必ず必要 */
		echo $this->NetCommonsForm->hidden('Frame.id');
		echo $this->NetCommonsForm->hidden('Block.id');
		echo $this->NetCommonsForm->hidden('Block.key');

		echo $this->NetCommonsForm->hidden('Quiz.key');
		echo $this->NetCommonsForm->hidden('Quiz.import_key');
		echo $this->NetCommonsForm->hidden('Quiz.export_key');
	?>
		<div class="modal-body">
			<label class="h3"><?php echo __d('quizzes', '形式の設定'); ?></label>
			<div class="form-group quiz-group">
				<?php echo $this->element('Quizzes.QuizEdit/Edit/quiz_attribute', array('isPublished' => $isPublished)); ?>
			</div>

			<label class="h3"><?php echo __d('quizzes', '実施方法の設定'); ?></label>
			<div class="form-group quiz-group">
				<?php echo $this->element('Quizzes.QuizEdit/Edit/quiz_method/period'); ?>
			</div>

			<?php if (Current::read('Room.space_id') == Space::PUBLIC_SPACE_ID): ?>
				<?php echo $this->element('Quizzes.QuizEdit/Edit/quiz_method/public_method'); ?>
			<?php else: ?>
				<?php echo $this->element('Quizzes.QuizEdit/Edit/quiz_method/group_method'); ?>
			<?php endif; ?>

			<?php echo $this->Workflow->inputComment('Quiz.status'); ?>
		</div>
		<?php echo $this->Workflow->buttons('Quiz.status', $cancelUrl, true, $backUrl); ?>

	<?php echo $this->NetCommonsForm->end(); ?>

	<?php if ($this->request->params['action'] === 'edit' && !empty($this->data['Quiz']['key']) && $this->Workflow->canDelete('Quiz', $this->data)) : ?>
		<div class="panel-footer text-right">
			<?php echo $this->element('Quizzes.QuizEdit/Edit/delete_form'); ?>
		</div>
	<?php endif; ?>

	<?php echo $this->Workflow->comments(); ?>

</div>
