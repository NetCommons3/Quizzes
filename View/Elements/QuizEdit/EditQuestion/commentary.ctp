<?php
/**
 * answer header view template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>
<div class="row">
	<div class="col-xs-12">
<hr />
		<div class="nc-wysiwyg-alert">
			<?php
			/* 解説 */
			/* 解説（正解とセットで表示されます）*/
			echo $this->NetCommonsForm->wysiwyg('QuizPage.{{pageIndex}}.QuizQuestion.{{qIndex}}.commentary',
			array(
			'id' => false,
			'div' => false,
			'label' => __d('quizzes', 'Commentary ( will be displayed in the correct answer and set )'),
			'ng-model' => 'question.commentary',
			));
			?>
		</div>
	</div>
</div>
