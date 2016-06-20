<?php
/**
 * quiz result list filter view template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>
<div class="form-group quiz-list-select">
	<?php if ($quiz['Quiz']['passing_grade'] > 0 || $quiz['Quiz']['estimated_time'] > 0): ?>
	<label>
		<?php echo __d('quizzes', 'filtered:'); /* 絞り込み： */ ?>
	</label>
	<?php endif; ?>

	<?php if ($quiz['Quiz']['passing_grade'] > 0): ?>
	<?php echo $this->element('Quizzes.QuizResult/select_pass', array(
	'list' => array(
	'' => __d('quizzes', 'Score'), // 得点
	'2' => __d('quizzes', 'Pass'), // 合格
	'1' => __d('quizzes', 'Failure') // 不合格
	),
	'currentStatus' => $passFilterStatus,
	'keyName' => 'passing_status')); ?>
	<?php endif; ?>

	<?php if ($quiz['Quiz']['estimated_time'] > 0): ?>
	<?php echo $this->element('Quizzes.QuizResult/select_pass', array(
	'list' => array(
	'' => __d('quizzes', 'In time'), // 時間内
	'2' => __d('quizzes', 'Pass'),
	'1' => __d('quizzes', 'Failure')
	),
	'currentStatus' => $winthinTimeFilterStatus,
	'keyName' => 'within_time_status')); ?>
	<?php endif; ?>
</div>
