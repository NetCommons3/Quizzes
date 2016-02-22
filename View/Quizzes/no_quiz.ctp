<?php
/**
 * questionnaire page setting view template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
/* FUJI echo $this->element('Quizzes.scripts');*/
?>


<div ng-controller="Questionnaires">

	<article>

		<?php echo $this->element('Quizzes.Quizzes/add_button'); ?>

		<div class="pull-left">
			<?php echo $this->element('Quizzes.Quizzes/answer_status'); ?>
		</div>

		<div class="clearfix"></div>

		<p>
			<?php echo __d('quizzes', 'no quiz'); ?>
		</p>

		<?php if (Current::permission('content_creatable')) : ?>
			<p>
				<?php echo __d('quizzes', 'Please create new quiz by pressing the "+" button.'); ?>
			</p>
		<?php endif ?>

	</article>

</div>

