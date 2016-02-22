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
	<div class="col-sm-12">
<hr />
		<div class="nc-wysiwyg-alert">
			<?php
			/* 解説 */
			echo $this->NetCommonsForm->input('QuizPage.{{pageIndex}}.QuizQuestion.{{qIndex}}.commentary',
			array('type' => 'wysiswyg',
			'id' => false,
			'div' => false,
			'label' => __d('quizzes', '解説（正解とセットで表示されます）'),
			'ng-model' => 'question.commentary',
			'ui-tinymce' => 'tinymce.options',
			'rows' => 5,
			));
			?>
		</div>
	</div>
</div>
