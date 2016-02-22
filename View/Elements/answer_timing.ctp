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
<?php if ($quiz['Quiz']['answer_timing'] == QuizzesComponent::USES_USE): ?>
    <strong>
        <?php echo $this->Date->dateFormat($quiz['Quiz']['answer_start_period']); ?>
        <?php echo __d('quizzes', ' - '); ?>
        <?php echo $this->Date->dateFormat($quiz['Quiz']['answer_end_period']); ?>
    </strong>
<?php endif;
