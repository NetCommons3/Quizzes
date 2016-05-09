<?php
/**
 * answer grade button view template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>
<?php if ($isMineAnswer): ?>
	<?php if ($gradePass === QuizzesComponent::STATUS_GRADE_FAIL): ?>
		<a class="btn btn-primary btn-lg" href="<?php echo NetCommonsUrl::actionUrl(array(
																			'controller' => 'quiz_answers',
																			'action' => 'view',
																			Current::read('Block.id'),
																			$quiz['Quiz']['key'],
																			'frame_id' => Current::read('Frame.id'))); ?>">
			<span class="glyphicon glyphicon-chevron-left"></span>
			<?php echo __d('quizzes', 'この問題にもう一度挑戦する'); ?>
		</a>
	<?php else: ?>
		<?php echo $this->BackTo->pageLinkButton(__d('quizzes', '最初に戻る'), array(
			'icon' => 'remove',
			'iconSize' => 'lg'));
			?>
	<?php endif; ?>
<?php endif; ?>

<a class="btn btn-primary btn-lg" href="<?php echo NetCommonsUrl::actionUrl(array(
																				'controller' => 'quiz_result',
																				'action' => 'view',
																				Current::read('Block.id'),
																				$quiz['Quiz']['key'],
																				'frame_id' => Current::read('Frame.id'),
																				$summary['QuizAnswerSummary']['id'])); ?>">
	<?php echo __d('quizzes', '成績確認'); ?>
</a>
