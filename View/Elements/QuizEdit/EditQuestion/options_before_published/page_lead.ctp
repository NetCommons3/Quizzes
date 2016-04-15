<div class="col-sm-12">
    <div class="checkbox">
        <label>
            <?php echo $this->NetCommonsForm->input('QuizPage.{{pageIndex}}.is_page_description', array(
            'type' => 'checkbox',
            'div' => false,
            'label' => __d('quizzes', '問題ページの先頭に文章を入れる'),
            'class' => '',
            'error' => false,
            'ng-model' => 'page.isPageDescription',
            'ng-checked' => 'page.isPageDescription == ' . QuizzesComponent::USES_USE,
            ));
            ?>
        </label>
    </div>
</div>
<div  class="col-sm-12" ng-show="page.isPageDescription == 1">
    <?php /* ページ冒頭文 */
								echo $this->NetCommonsForm->wysiwyg('QuizPage.{{pageIndex}}.page_description',
    array('type' => 'wysiswyg',
    'id' => false,
    'label' => false,
    'ng-model' => 'page.pageDescription',
    'ui-tinymce' => 'tinymce.options',
    'rows' => 5,
    'ng-disabled' => 'isPublished != 0',
    ));
    ?>
</div>
<?php echo $this->element('Quizzes.QuizEdit/EditQuestion/hidden_page_info_set'); ?>
