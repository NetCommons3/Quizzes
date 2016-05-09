<?php
/**
 * quiz result overall view template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>
<section>
    <span class="pull-right help-block">
        <?php echo __d('quizzes', '※繰り返し受験を認めている場合は、データは全て直近の回答をもとに算出しています。'); ?>
    </span>
    <h3>
        <?php echo __d('quizzes', '全体成績'); ?>
    </h3>
    <table class="table quiz-result-table">
        <tr>
            <th><?php echo __d('quizzes', '総受験者数'); ?></th>
            <th><?php echo __d('quizzes', '平均所要時間'); ?></th>
            <th><?php echo __d('quizzes', '平均点'); ?></th>
            <th><?php echo __d('quizzes', '最高点'); ?></th>
            <th><?php echo __d('quizzes', '最低点'); ?></th>
            <th><?php echo __d('quizzes', '分散'); ?></th>
        </tr>
        <tr>
            <td class="text-right"><?php echo $general['general']['number_pepole']; ?></td>
            <td class="text-right">
                <?php if ($general['general']['avg_time'] < 60): ?>
                   <?php echo sprintf(__d('quizzes', '%d秒'), $general['general']['avg_time']); ?>
                <?php else: ?>
                    <?php echo sprintf(__d('quizzes', '%d分'), $general['general']['avg_time'] / 60); ?>
                <?php endif; ?>
            </td>
            <td class="text-right"><?php echo $general['general']['avg_score']; ?></td>
            <td class="text-right"><?php echo $general['general']['max_score']; ?></td>
            <td class="text-right"><?php echo $general['general']['min_score']; ?></td>
            <td class="text-right"><?php echo $general['general']['samp_score']; ?></td>
        </tr>
    </table>
</section>
