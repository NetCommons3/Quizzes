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
    <span class="pull-right help-block">※繰り返し受験を認めている場合は、データは全て直近の回答をもとに算出しています。</span>
    <h3>全体成績</h3>
    <table class="table">
        <tr class="">
            <th>総受験者数</th>
            <th>平均所要時間</th>
            <th>平均点</th>
            <th>最高点</th>
            <th>最低点</th>
            <th>分散</th>
        </tr>
        <tr>
            <td><?php echo $general['general']['number_pepole']; ?></td>
            <td>
                <?php if ($general['general']['avg_time'] < 60): ?>
                   <?php echo sprintf('%d秒', $general['general']['avg_time']); ?>
                <?php else: ?>
                    <?php echo sprintf('%d分', $general['general']['avg_time'] / 60); ?>
                <?php endif; ?>
            </td>
            <td><?php echo $general['general']['avg_score']; ?></td>
            <td><?php echo $general['general']['max_score']; ?></td>
            <td><?php echo $general['general']['min_score']; ?></td>
            <td><?php echo $general['general']['samp_score']; ?></td>
        </tr>
    </table>
</section>
