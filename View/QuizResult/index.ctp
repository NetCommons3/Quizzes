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
'/quizzes/js/quizzes_result.js'
));
echo $this->NetCommonsHtml->css('/components/nvd3/build/nv.d3.css');
$jsScoreDistribute = NetCommonsAppController::camelizeKeyRecursive($general['score_distribution']);
?>
<article ng-controller="QuizResult"
		 ng-init="initialize(<?php echo h(json_encode($jsScoreDistribute)); ?>)">
	<?php echo $this->element('Quizzes.QuizAnswers/answer_header'); ?>

	<?php echo $this->element('Quizzes.QuizResult/overall_performance'); ?>

	<section>
		<h2>
			<?php echo __d('quizzes', 'Score distribution'); /* 得点分布 */ ?>
		</h2>
		<?php if ($general['general']): ?>
		<nvd3 options="opt" data="data"></nvd3>
		<?php else: ?>
			<?php echo __d('quizzes', 'Answer that ended the scoring is not yet.'); ?>
		<?php endif; ?>
	</section>

	<section class="clearfix">
		<div class="form-inline pull-right">
			<div class="form-group quiz-list-select">
				<label>
					<?php echo __d('quizzes', 'Display persons:'); /* 表示人数： */ ?>
				</label>
				<?php echo $this->DisplayNumber->dropDownToggle(); ?>
			</div>
			<?php echo $this->element('Quizzes.QuizResult/filters'); ?>
		</div>
		<h2>
			<?php echo __d('quizzes', 'Examinee list'); /* 受験者一覧 */ ?>
		</h2>
		<div class="clearfix"></div>
		<?php echo $this->TableList->startTable(); ?>
		<tr>
			<?php echo $this->TableList->tableHeader('User.handlename', __d('quizzes', 'Answer\'s'), array('type' => 'handle', 'sort' => true));/* 解答者名 */ ?>
			<?php echo $this->TableList->tableHeader('AnswerNumber.answer_number', __d('quizzes', 'Number'), array('type' => 'numeric', 'sort' => true));/* 回数 */ ?>
			<?php
				if ($quiz['Quiz']['passing_grade'] > 0) {
					echo $this->TableList->tableHeader('', __d('quizzes', 'Pass'), array('type' => 'center'));/* 合格 */
				}
			?>
			<?php
				if ($quiz['Quiz']['estimated_time'] > 0) {
					echo $this->TableList->tableHeader('', __d('quizzes', 'In time'), array('type' => 'center'));/* 時間内 */
				}
			?>
			<?php echo $this->TableList->tableHeader('QuizAnswerSummary.summary_score', __d('quizzes', 'Last score'), array('type' => 'numeric', 'sort' => true));/* 直近の得点 */ ?>
			<?php echo $this->TableList->tableHeader('', __d('quizzes', 'Deviation'), array('type' => 'numeric'));/* 偏差値 */ ?>
			<?php echo $this->TableList->tableHeader('Statistics.avg_elapsed_second', __d('quizzes', 'Average time'), array('type' => 'numeric', 'sort' => true));/* 平均時間 */ ?>
			<?php echo $this->TableList->tableHeader('Statistics.max_score', __d('quizzes', 'Highscore'), array('type' => 'numeric', 'sort' => true));/* 最高点 */ ?>
			<?php echo $this->TableList->tableHeader('Statistics.min_score', __d('quizzes', 'Lowscore'), array('type' => 'numeric', 'sort' => true));/* 最低点 */ ?>
			<?php echo $this->TableList->tableHeader('', __d('quizzes', 'Graded'), array('type' => 'center'));/* 採点 */ ?>
		</tr>
		<?php foreach ($summaryList as $summary): ?>
		<tr class="<?php echo $this->QuizResult->getPassClass($quiz, $summary); ?>">
			<?php echo $this->TableList->tableData('', $this->QuizResult->getHandleNameLink($quiz, $summary),
				array('type' => 'text', 'escape' => false)); ?>
			<?php echo $this->TableList->tableData('', $this->QuizResult->getAnswerNumber($quiz, $summary),
				array('type' => 'numeric')); ?>
			<?php
				if ($quiz['Quiz']['passing_grade'] > 0) {
					echo $this->TableList->tableData('', $this->QuizResult->getPassing($quiz, $summary),
						array('type' => 'center', 'escape' => false));
				}
			?>
			<?php
				if ($quiz['Quiz']['estimated_time'] > 0) {
					echo $this->TableList->tableData('', $this->QuizResult->getWithinTime($quiz, $summary),
						array('type' => 'center', 'escape' => false));
				}
			?>
			<?php echo $this->TableList->tableData('', $this->QuizResult->getScore($summary),
				array('type' => 'numeric')); ?>
			<?php echo $this->TableList->tableData('', $this->QuizResult->getStdScore($general, $summary),
				array('type' => 'numeric')); ?>
			<?php echo $this->TableList->tableData('', $this->QuizResult->getAvgElapsed($quiz, $summary),
				array('type' => 'numeric')); ?>
			<?php echo $this->TableList->tableData('', $this->QuizResult->getMaxScore($quiz, $summary),
				array('type' => 'numeric')); ?>
			<?php echo $this->TableList->tableData('', $this->QuizResult->getMinScore($quiz, $summary),
				array('type' => 'numeric')); ?>
			<?php echo $this->TableList->tableData('', $this->QuizResult->getNotScoring($quiz, $summary),
				array('type' => 'center', 'escape' => false)); ?>
		</tr>
		<?php endforeach; ?>
		<?php echo $this->TableList->endTable(); ?>
		<span class="help-block">
			<?php echo
			__d('quizzes', '! not logged answer in has been treated as a solution of the only all once because it is impossible to history management .'); /* ※未ログインでの回答は履歴管理が不可能なため全て１度だけの解答として扱われています。 */?>
		</span>

		<?php echo $this->element('NetCommons.paginator'); ?>

	</section>

	<div class="text-center">
		<?php echo $this->BackTo->pageLinkButton(__d('quizzes', 'Back to top')); ?>
	</div>

</article>