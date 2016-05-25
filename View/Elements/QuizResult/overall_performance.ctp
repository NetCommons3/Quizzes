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
		<?php /* ※繰り返し受験を認めている場合は、データは全て直近の回答をもとに算出しています。*/
		echo __d('quizzes', 'If you allow repeat examination , data is based on the last answer .'); ?>
	</span>
	<h2>
		<?php /* 全体成績 */
		echo __d('quizzes', 'Overall performance');
		?>
	</h2>
	<div class="clearfix"></div>
	<?php echo $this->TableList->startTable(); ?>
		<tr>
			<?php echo $this->TableList->tableHeader('', __d('quizzes', 'sum of  examinees'), array('type' => 'numeric'));/* 総受験者数 */ ?>
			<?php echo $this->TableList->tableHeader('', __d('quizzes', 'average time'), array('type' => 'numeric'));/* 平均所要時間 */ ?>
			<?php echo $this->TableList->tableHeader('', __d('quizzes', 'Average'), array('type' => 'numeric'));/* 平均点 */ ?>
			<?php echo $this->TableList->tableHeader('', __d('quizzes', 'Highscore'), array('type' => 'numeric'));/* 最高点 */ ?>
			<?php echo $this->TableList->tableHeader('', __d('quizzes', 'Lowscore'), array('type' => 'numeric'));/* 最低点 */ ?>
			<?php echo $this->TableList->tableHeader('', __d('quizzes', 'Dispersion'), array('type' => 'numeric'));/* 分散 */ ?>
		</tr>
		<?php echo $this->TableList->startTableRow(); ?>
			<?php echo $this->TableList->tableData('', $general['general']['number_pepole'], array('type' => 'numeric'));/* 総受験者数 */ ?>
			<?php if ($general['general']['avg_time'] < 60) {
				$avgTime = sprintf(__d('quizzes', '%d sec'), /* %d秒 */
						$general['general']['avg_time']);
									} else {
				$avgTime = sprintf(__d('quizzes', '%d min'), /* %d分 */
					$general['general']['avg_time'] / 60);
			}
			echo $this->TableList->tableData('', $avgTime, array('type' => 'numeric')); ?>
			<?php echo $this->TableList->tableData('', $general['general']['avg_score'], array('type' => 'numeric')); ?>
			<?php echo $this->TableList->tableData('', $general['general']['max_score'], array('type' => 'numeric')); ?>
			<?php echo $this->TableList->tableData('', $general['general']['min_score'], array('type' => 'numeric')); ?>
			<?php echo $this->TableList->tableData('', $general['general']['samp_score'], array('type' => 'numeric')); ?>
		<?php echo $this->TableList->endTableRow(); ?>
	<?php echo $this->TableList->endTable(); ?>
</section>
