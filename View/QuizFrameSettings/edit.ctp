<?php
/**
 * quiz frame setting view template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

echo $this->element('Quizzes.scripts');
echo $this->NetCommonsHtml->script(array(
'/quizzes/js/quizzes_frame.js',
));
$jsQuizFrameSettings = NetCommonsAppController::camelizeKeyRecursive(QuizzesAppController::changeBooleansToNumbers($quizFrameSettings));
$jsQuizzes = NetCommonsAppController::camelizeKeyRecursive(QuizzesAppController::changeBooleansToNumbers($quizzes));
?>

<article class="nc-quiz-frame-settings-content-list-"
		 ng-controller="QuizzesFrame"
		 ng-init="initialize(<?php echo h(json_encode($jsQuizzes)); ?>,
	 	<?php echo h(json_encode($jsQuizFrameSettings)); ?>)">

	<?php echo $this->BlockTabs->main(BlockTabsHelper::MAIN_TAB_FRAME_SETTING); ?>

	<div class="tab-content">

		<?php echo $this->element('Blocks.edit_form', array(
			'model' => 'QuizFrameSetting',
			'callback' => 'Quizzes.QuizFrameSettings/edit_form',
			'cancelUrl' => NetCommonsUrl::backToPageUrl(),
			)); ?>

	</div>

</article>