<?php
/**
 * ページ先頭に何か問題文を入れるときのテンプレ
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>
<div class="col-xs-12">
	<div class="checkbox">
		<label>
			<?php /* 問題ページの先頭に文章を入れる */
				echo $this->NetCommonsForm->checkbox('QuizPage.{{pageIndex}}.is_page_description', array(
					'type' => 'checkbox',
					'div' => false,
					'label' => __d('quizzes', 'Place the text at the top of the page'),
					'class' => '',
					'error' => false,
					'ng-model' => 'page.isPageDescription',
					'ng-checked' => 'page.isPageDescription == ' . QuizzesComponent::USES_USE,
					));
				?>
		</label>
	</div>
</div>
<div  class="col-xs-12" ng-show="page.isPageDescription == 1">
	<?php /* ページ冒頭文 */
	echo $this->NetCommonsForm->wysiwyg('QuizPage.{{pageIndex}}.page_description',
	array(//'type' => 'wysiwyg',
	'id' => false,
	'label' => false,
	'ng-model' => 'page.pageDescription',
	'ng-disabled' => 'isPublished != 0',
	));
	?>
</div>
<?php echo $this->element('Quizzes.QuizEdit/EditQuestion/hidden_page_info_set');
