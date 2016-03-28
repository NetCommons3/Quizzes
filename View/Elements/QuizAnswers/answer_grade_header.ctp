<?php
/**
 * answer grade header view template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>
<?php if ($gradePass == QuizzesComponent::STATUS_GRADE_YET): ?>
    <div class="alert well">
<?php elseif ($gradePass == QuizzesComponent::STATUS_GRADE_PASS): ?>
    <div class="alert alert-success">
<?php elseif ($gradePass == QuizzesComponent::STATUS_GRADE_FAIL): ?>
    <div class="alert alert-danger">
<?php else: ?>
    <div class="alert">
<?php endif; ?>
    <?php echo $this->element('Quizzes.QuizAnswers/answer_header'); ?>
    <div class="h2">
        <?php if ($gradePass == QuizzesComponent::STATUS_GRADE_PASS): ?>
            <?php echo $this->TitleIcon->titleIcon('/net_commons/img/title_icon/10_051_pass.svg'); ?>
        <?php endif; ?>
        <?php if (is_null($summary['QuizAnswerSummary']['summary_score'])): ?>
            未採点があります。まだ得点は出されていません。
        <?php else: ?>
            <?php echo sprintf('得点%d点', $summary['QuizAnswerSummary']['summary_score']); ?>
        <?php endif; ?>
        <small>
            <?php echo sprintf('解答にかかった時間：%d分', $summary['QuizAnswerSummary']['elapsed_second'] / 60); ?>
            <?php if ($quiz['Quiz']['estimated_time'] > 0 && $quiz['Quiz']['estimated_time'] * 60 < $summary['QuizAnswerSummary']['elapsed_second']): ?>
            時間オーバーです
            <?php endif; ?>
        </small>
    </div>
</div>
