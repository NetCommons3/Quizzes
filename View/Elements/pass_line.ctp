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
<?php if ($quiz['Quiz']['estimated_time'] > 0): ?>
	<span class="quiz-passing-text">
		<?php
		echo __d('quizzes', 'Estimated time : %d min ', /* 時間の目安：%d分 */
		$quiz['Quiz']['estimated_time']
		); ?>
	</span>
<?php endif; ?>

<?php if ($quiz['Quiz']['passing_grade'] > 0): ?>
	<span class="quiz-passing-text">
		<?php
		echo __d('quizzes', 'The pass score : %d ', /* 合格点：%d点 */
		$quiz['Quiz']['passing_grade']
		); ?>
	</span>
<?php endif; ?>

<?php if ($quiz['Quiz']['perfect_score'] > 0): ?>
	<span class="quiz-passing-text">
		<?php
		echo __d('quizzes', 'Perfect score : %d ', /* 満点：%d点 */
		$quiz['Quiz']['perfect_score']
		); ?>
	</span>
<?php endif;
