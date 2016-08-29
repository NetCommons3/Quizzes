<?php
/**
 * quizzes page answer view template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>
<article>
	<?php echo __d('quizzes', 'not found this quiz.'); ?>

	<?php if ($displayType == QuizzesComponent::DISPLAY_TYPE_LIST): ?>
		<div class="text-center">
			<?php echo $this->LinkButton->toList(__d('quizzes', 'Back to the quiz list')); ?>
		</div>
	<?php endif; ?>
</article>
