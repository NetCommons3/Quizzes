<?php
/**
 * quiz add create new element
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>

<?php echo $this->NetCommonsForm->radio('create_option',
	array(QuizzesComponent::QUIZ_CREATE_OPT_NEW => __d('quizzes', 'Create new quiz')),
	array('ng-model' => 'createOption',
	'hiddenField' => false,
	));
?>
<div class="row" uib-collapse="createOption != '<?php echo QuizzesComponent::QUIZ_CREATE_OPT_NEW; ?>'">
	<div class="col-xs-11 col-xs-offset-1">
		<?php echo $this->NetCommonsForm->input('title', array(
		'label' => __d('quizzes', 'Quiz title'),
		'required' => true,
		'placeholder' => __d('quizzes', 'Please input quiz title'),
		'nc-focus' => '{{createOption == \'' . QuizzesComponent::QUIZ_CREATE_OPT_NEW . '\'}}'
		)); ?>
	</div>
</div>
