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
		<a class="btn btn-primary" href="<?php echo $this->NetCommonsHtml->url(array(
																			'controller' => 'quiz_answers',
																			'action' => 'view',
																			'block_id' => Current::read('Block.id'),
																			'key' => $quiz['Quiz']['key'],
																			'frame_id' => Current::read('Frame.id'))); ?>">
			<span class="glyphicon glyphicon-chevron-left"></span>
			<?php echo __d('quizzes', 'Challenge once again'); /* この問題にもう一度挑戦する */ ?>
		</a>
	<?php else: ?>
		<?php /* 最初に戻る */
			echo $this->LinkButton->toList(__d('quizzes', 'Finished'), null, array('icon' => 'remove'));
			?>
	<?php endif; ?>
<?php endif; ?>

<a class="btn btn-default" href="<?php echo $this->NetCommonsHtml->url(array(
																				'controller' => 'quiz_result',
																				'action' => 'view',
																				'block_id' => Current::read('Block.id'),
																				'key' => $quiz['Quiz']['key'],
																				'frame_id' => Current::read('Frame.id'),
																				$summary['QuizAnswerSummary']['id'])); ?>">
	<?php echo __d('quizzes', 'Results confirmed'); /* 成績確認 */ ?>
</a>
