<?php
/**
 * answer grade body view template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>
<?php foreach($quiz['QuizPage'] as $pIndex => $page): ?>
    <?php foreach($page['QuizQuestion'] as $qIndex => $question): ?>
        <div class="form-group">
            <?php echo $this->QuizGrading->grading($quiz, $pIndex, $qIndex, $question, $summary); ?>
        </div>
    <?php endforeach; ?>
<?php endforeach;
