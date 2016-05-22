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
	'/quizzes/js/quizzes_edit.js',
));
$jsQuiz = NetCommonsAppController::camelizeKeyRecursive(QuizzesAppController::changeBooleansToNumbers($this->data));
?>

<article
	id="nc-quizzes-setting-edit"
	 ng-controller="QuizzesEdit"
	 ng-init="initialize(<?php echo Current::read('Frame.id'); ?>,
	 						<?php echo (int)$isPublished; ?>,
							<?php echo h(json_encode($jsQuiz)); ?>)">

	<?php echo $this->element('Quizzes.QuizEdit/quiz_title'); ?>

	<?php echo $this->Wizard->navibar('edit'); ?>

	<?php $this->NetCommonsForm->unlockField('QuizPage'); ?>

	<div class="panel panel-default">

	<?php echo $this->NetCommonsForm->create('Quiz', $formOptions);

		/* NetCommonsお約束:プラグインがデータを登録するところではFrame.id,Block.id,Block.keyの３要素が必ず必要 */
		echo $this->NetCommonsForm->hidden('Frame.id');
		echo $this->NetCommonsForm->hidden('Block.id');
		echo $this->NetCommonsForm->hidden('Block.key');

		echo $this->NetCommonsForm->hidden('Quiz.key');
		echo $this->NetCommonsForm->hidden('Quiz.import_key');
		echo $this->NetCommonsForm->hidden('Quiz.export_key');
	?>
		<div class="panel-body">

			<label class="h2"><?php echo __d('quizzes', 'Setting of format'); /* '形式の設定' */ ?></label>
			<div class="row">
				<div class="form-group col-xs-11 col-xs-offset-1">
					<?php echo $this->element('Quizzes.QuizEdit/Edit/quiz_attribute', array('isPublished' => $isPublished)); ?>
				</div>
			</div>

			<label class="h2"><?php echo __d('quizzes', 'Setting method of implementation'); /* '実施方法の設定' */ ?></label>
			<div class="row">
				<div class="col-xs-11 col-xs-offset-1">
					<?php echo $this->element('Quizzes.QuizEdit/Edit/quiz_method/period'); ?>

					<?php if (Current::read('Room.space_id') == Space::PUBLIC_SPACE_ID): ?>
						<?php echo $this->element('Quizzes.QuizEdit/Edit/quiz_method/public_method'); ?>
					<?php else: ?>
						<?php echo $this->element('Quizzes.QuizEdit/Edit/quiz_method/group_method'); ?>
					<?php endif; ?>
				</div>
			</div>

			<hr />

			<?php echo $this->Workflow->inputComment('Quiz.status'); ?>
		</div>
		<?php echo $this->Wizard->workflowButtons('Quiz.status', null, null, true); ?>

	<?php echo $this->NetCommonsForm->end(); ?>

	<?php if ($this->request->params['action'] === 'edit' && !empty($this->data['Quiz']['key']) && $this->Workflow->canDelete('Quiz', $this->data)) : ?>
		<div class="panel-footer text-right">
			<?php echo $this->element('Quizzes.QuizEdit/Edit/delete_form'); ?>
		</div>
	<?php endif; ?>

	<?php echo $this->Workflow->comments(); ?>

</article>
