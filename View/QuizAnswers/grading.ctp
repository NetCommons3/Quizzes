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
?>
<?php
	echo $this->element('Quizzes.scripts');
	echo $this->NetCommonsHtml->script(array(
		'/components/d3/d3.min.js',
		'/components/nvd3/build/nv.d3.min.js',
		'/components/angular-nvd3/dist/angular-nvd3.min.js',
		'/quizzes/js/quizzes_grade.js'
	));
	echo $this->NetCommonsHtml->css('/components/nvd3/build/nv.d3.css');
$jsCorrectRate = NetCommonsAppController::camelizeKeyRecursive($this->QuizGrading->correctRate($quiz));
?>
<article id="nc-quizzes-answer-<?php echo Current::read('Frame.id'); ?>"
		 ng-controller="QuizGrade"
		 ng-init="initialize(<?php echo h(json_encode($jsCorrectRate)); ?>)">

	<section>
		<?php echo $this->element('Quizzes.QuizAnswers/answer_grade_header'); ?>
	</section>

	<?php echo $this->element('Quizzes.QuizAnswers/answer_test_mode_header'); ?>

	<section>
		<?php echo $this->element('Quizzes.QuizAnswers/grade'); ?>
	</section>

	<div class="text-center">
		<?php echo $this->element('Quizzes.QuizAnswers/grade_button'); ?>
	</div>

</article>
