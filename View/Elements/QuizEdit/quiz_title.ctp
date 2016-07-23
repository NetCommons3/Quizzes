<?php
/**
 * quiz edit title template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>
<!--<div class="bg-info">-->
<h1 ng-cloak class="">
    {{quiz.quiz.title}}
    <?php if ($this->action != 'edit'): ?>
    <small>
      <div class="help-block small">
       <?php echo __d('quizzes', 'If you want to change the quiz title, please edit in "Set quiz" step.'); ?>
       </div>
    </small>
    <?php endif; ?>
</h1>
