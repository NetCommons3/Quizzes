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
<article>
    <p>
        <?php echo __d('quizzes', 'you will not be able to answer this quiz.'); ?>
        <?php echo $this->QuizResultButton->getResultButtons($quiz, array(
            'title' => '&nbsp;' . __d('quizzes', 'Results confirmed'),
            'icon' => 'stats',
        )); ?>
        <?php if ($displayType == QuizzesComponent::DISPLAY_TYPE_LIST): ?>
            <div class="text-center">
                <?php echo $this->BackTo->pageLinkButton(__d('quizzes', 'Back to Top'), array('icon' => 'chevron-left')); ?>
            </div>
        <?php endif; ?>
    </p>
</article>