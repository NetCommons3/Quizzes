<?php
/**
 * quizzes word setting(after published) view template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>
<?php echo $this->NetCommonsForm->label('', __d('quizzes', 'Word to the correct answer')); /* 正解とする単語 */?>
<div class="well">
    <span ng-repeat="correctWord in correct.correct">
        {{correctWord}},
        &nbsp;
    </span>
</div>
