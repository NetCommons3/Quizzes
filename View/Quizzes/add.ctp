<?php
/**
 * quiz create view template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

echo $this->element('Quizzes.scripts');
echo $this->NetCommonsHtml->script(array(
	'/quizzes/js/quizzes_add.js',
));
$jsPastQuizzes = NetCommonsAppController::camelizeKeyRecursive($pastQuizzes);
?>

<div ng-controller="QuizzesAdd"
	 ng-init="initialize(
	 	'<?php echo $this->data['ActionQuizAdd']['create_option']; ?>',
	 	<?php echo h(json_encode($jsPastQuizzes)); ?>)">
	<div class="row">

		<div class="col-lg-12">
			<p>
				<?php echo __d('quizzes', 'You can create a new quiz . Please choose from that are displayed how to create below .'); /*新しい小テストを作ることができます。作成方法を下に表示されている中から選んでください。*/ ?>
			</p>
		</div>

		<?php echo $this->NetCommonsForm->create('ActionQuizAdd', array(
		'type' => 'file',
		)); ?>
		<?php echo $this->NetCommonsForm->hidden('Frame.id'); ?>
		<?php echo $this->NetCommonsForm->hidden('Block.id'); ?>

		<div class="form-group col-lg-12">
			<?php echo $this->element('Quizzes.Quizzes/create_new'); ?>
		</div>

		<div class="form-group col-lg-12">
			<?php echo $this->element('Quizzes.Quizzes/create_template'); ?>
		</div>

		<div class="form-group col-lg-12">
			<?php echo $this->element('Quizzes.Quizzes/create_reuse'); ?>
		</div>

		<div class="text-center">
			<?php echo $this->BackTo->pageLinkButton(__d('net_commons', 'Cancel'), array('icon' => 'remove')); ?>
			<?php echo $this->Button->save(__d('net_commons', 'NEXT'), array('icon' => 'chevron-right')) ?>
		</div>

		<?php echo $this->NetCommonsForm->end(); ?>
	</div>
</div>