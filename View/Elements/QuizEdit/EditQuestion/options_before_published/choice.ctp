<?php
/**
 * quiz page setting view template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>
<input type="text"
	   name="data[QuizPage][{{pageIndex}}][QuizQuestion][{{qIndex}}][QuizChoice][{{choice.choiceSequence}}][choice_label]"
	   class="form-control input-sm"
	   ng-model="choice.choiceLabel"
	   nc-focus = "true"
	   ng-blur="resetMultipleCorrect(pageIndex, qIndex)"
		/>

<?php echo $this->element(
	'Quizzes.QuizEdit/ng_errors', array(
	'errorArrayName' => 'choice.errorMessages.choiceLabel',
)); ?>

<input type="hidden"
	   name="data[QuizPage][{{pageIndex}}][QuizQuestion][{{qIndex}}][QuizChoice][{{choice.choiceSequence}}][choice_sequence]"
	   ng-value="choice.choiceSequence"
		/>
<input type="hidden"
	   name="data[QuizPage][{{pageIndex}}][QuizQuestion][{{qIndex}}][QuizChoice][{{choice.choiceSequence}}][key]"
	   ng-value="choice.key"
		/>