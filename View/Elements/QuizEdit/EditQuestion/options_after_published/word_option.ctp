<?php
/**
 * quizzes word setting(after published) view template view template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?><div class="row">
    <div class="col-xs-12" ng-repeat="(correctIndex, correct) in question.quizCorrect" >
        <?php echo $this->element('Quizzes.QuizEdit/EditQuestion/options_after_published/word'); ?>
    </div>
</div>
<?php echo $this->element('Quizzes.QuizEdit/EditQuestion/commentary');
