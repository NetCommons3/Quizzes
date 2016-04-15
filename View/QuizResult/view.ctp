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
'/components/nvd3/nv.d3.min.js',
'/components/angular-nvd3/dist/angular-nvd3.min.js',
'/quizzes/js/quizzes_result.js'
));
echo $this->NetCommonsHtml->css('/components/nvd3/nv.d3.css');
if ($scoreHistory) {
	$jsScoreHistory = NetCommonsAppController::camelizeKeyRecursive($scoreHistory);
} else {
	$jsScoreHistory = array();
}

?>

<article ng-controller="QuizResultView" ng-init="initialize(<?php echo h(json_encode($jsScoreHistory)); ?>)">
	<?php echo $this->element('Quizzes.QuizAnswers/answer_header'); ?>

	<?php if ($scoreHistory): ?>
		<h2><?php echo sprintf('%sさんの成績', $handleName); ?></h2>
		<section>
			<h3>得点推移</h3>
			<nvd3 options="opt" data="data"></nvd3>
		</section>
	<?php endif; ?>

	<?php echo $this->element('Quizzes.QuizResult/overall_performance'); ?>

	<?php if ($summaryList): ?>
	<section>
	<h3>成績履歴</h3>
	<table class="table quiz-result-table">
		<tr class="">
			<th><a href="#">回</a></th>
			<th>完答</th>
			<th>合格</th>
			<th>時間内</th>
			<th><a href="">日時</a></th>
			<th><a href="">時間</a></th>
			<th><a href="">得点</a></th>
			<th><a href="">偏差値</a></th>
			<th><a href="">採点</a></th>
		</tr>
		<?php foreach ($summaryList as $summary): ?>
			<tr class="<?php echo $this->QuizResult->getPassClass($quiz, $summary); ?>" >
				<td class="text-right">
					<?php echo $this->NetCommonsHtml->link($summary['QuizAnswerSummary']['answer_number'], NetCommonsUrl::actionUrl(array(
					'plugin' => 'quizzes',
					'controller' => 'quiz_answers',
					'action' => 'grading',
					'block_id' => Current::read('Block.id'),
					'key' => $quiz['Quiz']['key'],
					$summary['QuizAnswerSummary']['id'],
					'frame_id' => Current::read('Frame.id')
					))); ?>
				</td>
				<td class="text-center">
					<?php echo $this->QuizResult->getComplete($quiz, $summary); ?>
				</td>
				<td class="text-center">
					<?php echo $this->QuizResult->getPassing($quiz, $summary); ?>
				</td>
				<td class="text-center">
					<?php echo $this->QuizResult->getWithinTime($quiz, $summary); ?>
				</td>
				<td>
					<?php echo $summary['QuizAnswerSummary']['answer_finish_time']; ?>
				</td>
				<td class="text-right">
					<?php echo $this->QuizResult->getElapsed($quiz, $summary); ?>
				</td>
				<td class="text-right">
					<?php echo $summary['QuizAnswerSummary']['summary_score']; ?>
				</td>
				<td class="text-right">
					<?php echo $this->QuizResult->getStdScore($general, $summary); ?>
				</td>
				<td class="text-center">
					<?php if (! $summary['QuizAnswerSummary']['is_grade_finished']): ?>
						<span class="label label-danger">未採点あり</span>
					<?php endif; ?>
				</td>
			</tr>
		<?php endforeach; ?>
	</table>

	<?php echo $this->element('NetCommons.paginator'); ?>

	</section>
	<?php endif; ?>

	<div class="text-center">
		<?php echo $this->BackTo->pageLinkButton('小テストＴＯＰへ戻る', array('icon' => 'remove', 'iconSize' => 'lg')); ?>
		<?php if ($this->Workflow->canEdit('Quiz', $quiz)) : ?>
			<?php echo $this->BackTo->linkButton('受験者一覧に戻る',
			NetCommonsUrl::actionUrl(array(
			'action' => 'index',
			'block_id' => Current::read('Block.id'),
			'key' => $quiz['Quiz']['key'],
			'frame_id' => Current::read('Frame.id')
			)),
			array('icon' => 'remove', 'iconSize' => 'lg')); ?>
		<?php endif; ?>
	</div>

</article>