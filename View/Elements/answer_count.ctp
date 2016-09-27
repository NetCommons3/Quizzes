<?php
/**
 * answer count view template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>
<?php if (isset($ownAnsweredCounts[$quiz['Quiz']['key']])): ?>
    <span class="quiz-passing-text">
    <?php echo __d('quizzes', 'Number: %d', $ownAnsweredCounts[$quiz['Quiz']['key']]); ?>
    </span>
<?php endif;
