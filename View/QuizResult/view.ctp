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
<?php
echo $this->element('Quizzes.scripts');
echo $this->NetCommonsHtml->script(array(
'/components/d3/d3.min.js',
'/components/nvd3/build/nv.d3.min.js',
'/components/angular-nvd3/dist/angular-nvd3.min.js',
'/quizzes/js/quizzes_messages_constant.js',
'/quizzes/js/quizzes_result.js'
));
echo $this->NetCommonsHtml->css('/components/nvd3/build/nv.d3.css');
if ($scoreHistory) {
	$jsScoreHistory = NetCommonsAppController::camelizeKeyRecursive($scoreHistory);
} else {
	$jsScoreHistory = array();
}

?>

<article ng-controller="QuizResultView" ng-init="initialize(<?php echo h(json_encode($jsScoreHistory)); ?>)">
	<?php echo $this->element('Quizzes.QuizAnswers/answer_header'); ?>

	<?php if ($scoreHistory): ?>
		<h2>
			<?php echo
				sprintf(
					__d('quizzes', '%s \'s grade'), // %sさんの成績
					$handleName
				); ?>
		</h2>
		<section>
			<h3>
				<?php echo __d('quizzes', 'Score history'); /* 得点推移 */ ?>
			</h3>
			<nvd3 options="opt" data="data"></nvd3>
		</section>
	<?php endif; ?>

	<?php echo $this->element('Quizzes.QuizResult/overall_performance'); ?>

	<?php if ($summaryList): ?>
	<section>
	<h3>
		<?php echo __d('quizzes', 'Grade history'); /* 成績履歴 */ ?>
	</h3>
	<?php echo $this->TableList->startTable(); ?>
		<tr >
			<?php echo $this->TableList->tableHeader('QuizAnswerSummary.answer_number', __d('quizzes', 'Number'), array('type' => 'numeric', 'sort' => true));/* 回 */ ?>
			<?php echo $this->TableList->tableHeader('', __d('quizzes', 'Complete'), array('type' => 'center', 'sort' => false));/* 完答 */ ?>
			<?php echo $this->TableList->tableHeader('', __d('quizzes', 'Pass'), array('type' => 'center', 'sort' => false));/* 合格 */ ?>
			<?php echo $this->TableList->tableHeader('', __d('quizzes', 'In time'), array('type' => 'center', 'sort' => false));/* 時間内 */ ?>
			<?php echo $this->TableList->tableHeader('QuizAnswerSummary.answer_finish_time', __d('quizzes', 'Date'), array('type' => 'datetime', 'sort' => true));/* 日時 */ ?>
			<?php echo $this->TableList->tableHeader('QuizAnswerSummary.elapsed_second', __d('quizzes', 'Elapsed'), array('type' => 'numeric', 'sort' => true));/* 時間 */ ?>
			<?php echo $this->TableList->tableHeader('QuizAnswerSummary.summary_score', __d('quizzes', 'Score'), array('type' => 'numeric', 'sort' => true));/* 得点 */ ?>
			<?php echo $this->TableList->tableHeader('', __d('quizzes', 'Deviation'), array('type' => 'numeric', 'sort' => false));/* 偏差値 */ ?>
			<?php echo $this->TableList->tableHeader('', __d('quizzes', 'Graded'), array('type' => 'center', 'sort' => false));/* 採点 */ ?>
		</tr>
		<?php foreach ($summaryList as $summary): ?>
			<tr class="<?php echo $this->QuizResult->getPassClass($quiz, $summary); ?>" >
				<?php
				echo $this->TableList->tableData('',
				$this->NetCommonsHtml->link($summary['QuizAnswerSummary']['answer_number'], NetCommonsUrl::actionUrl(array(
					'plugin' => 'quizzes',
					'controller' => 'quiz_answers',
					'action' => 'grading',
					'block_id' => Current::read('Block.id'),
					'key' => $quiz['Quiz']['key'],
					$summary['QuizAnswerSummary']['id'],
					'frame_id' => Current::read('Frame.id')
				))),
				array('type' => 'numeric', 'escape' => false)); ?>
				<?php echo $this->TableList->tableData('',
					$this->QuizResult->getComplete($quiz, $summary),
					array('type' => 'center', 'escape' => false)); ?>
				<?php echo $this->TableList->tableData('',
					$this->QuizResult->getPassing($quiz, $summary),
					array('type' => 'center', 'escape' => false)); ?>
				<?php echo $this->TableList->tableData('',
					$this->QuizResult->getWithinTime($quiz, $summary),
					array('type' => 'center', 'escape' => false)); ?>
				<?php echo $this->TableList->tableData('',
					$summary['QuizAnswerSummary']['answer_finish_time'],
					array('type' => 'datetime')); ?>
				<?php echo $this->TableList->tableData('',
					$this->QuizResult->getElapsed($quiz, $summary),
					array('type' => 'numeric')); ?>
				<?php echo $this->TableList->tableData('',
					$summary['QuizAnswerSummary']['summary_score'],
					array('type' => 'numeric')); ?>
				<?php echo $this->TableList->tableData('',
					$this->QuizResult->getStdScore($general, $summary),
					array('type' => 'numeric')); ?>
				<td class="text-center">
					<?php if (! $summary['QuizAnswerSummary']['is_grade_finished']): ?>
						<span class="label label-danger">
							<?php echo __d('quizzes', 'Ungraded'); /* 未採点あり */ ?>
						</span>
					<?php endif; ?>
				</td>
			</tr>
		<?php endforeach; ?>
	<?php echo $this->TableList->endTable(); ?>

	<?php echo $this->element('NetCommons.paginator'); ?>

	</section>
	<?php endif; ?>

	<div class="text-center">
		<?php echo $this->BackTo->indexLinkButton(__d('quizzes', 'Back to the quiz top')); ?>
		<?php if ($this->Workflow->canEdit('Quiz', $quiz)) : ?>
			<?php echo
			$this->BackTo->linkButton(
			__d('quizzes', 'Back to the examinee list'), // 受験者一覧に戻る
			NetCommonsUrl::actionUrl(array(
			'action' => 'index',
			'block_id' => Current::read('Block.id'),
			'key' => $quiz['Quiz']['key'],
			'frame_id' => Current::read('Frame.id')
			))); ?>
		<?php endif; ?>
	</div>

</article>