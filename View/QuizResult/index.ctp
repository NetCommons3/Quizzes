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
$jsScoreDistribute = NetCommonsAppController::camelizeKeyRecursive($general['score_distribution']);
?>
<article ng-controller="QuizResult"
		 ng-init="initialize(<?php echo h(json_encode($jsScoreDistribute)); ?>)">
	<?php echo $this->element('Quizzes.QuizAnswers/answer_header'); ?>

	<?php echo $this->element('Quizzes.QuizResult/overall_performance'); ?>

	<section>
		<h3>得点分布</h3>
		<nvd3 options="opt" data="data"></nvd3>
	</section>

	<section class="clearfix">
		<div class="form-inline pull-right">
			<div class="form-group quiz-list-select">
				<label>表示人数：</label>
				<?php echo $this->DisplayNumber->dropDownToggle(); ?>
			</div>
			<div class="form-group quiz-list-select">
				<label>絞り込み：</label>
				<?php echo $this->element('Quizzes.QuizResult/select_pass', array(
				'list' => array(
					'' => '得点',
					'2' => '合格',
					'1' => '不合格'
				),
				'currentStatus' => $passFilterStatus,
				'keyName' => 'passing_status')); ?>
				<?php echo $this->element('Quizzes.QuizResult/select_pass', array(
					'list' => array(
						'' => '時間内',
						'2' => '合格',
						'1' => '不合格'
					),
					'currentStatus' => $winthinTimeFilterStatus,
					'keyName' => 'within_time_status')); ?>
			</div>
		</div>
		<h3>受験者一覧</h3>
		<table class="table">
			<tr>
				<th>
					<?php echo $this->Paginator->sort('User.handlename', __d('quizzes', '解答者名')); ?>
				</th>
				<th>
					<?php echo $this->Paginator->sort('QuizAnswerSummary.answer_number', __d('quizzes', '回数')); ?>
				</th>
				<th>合格</th>
				<th>時間内</th>
				<th>
					<?php echo $this->Paginator->sort('QuizAnswerSummary.summary_score', __d('quizzes', '直近の得点')); ?>
				</th>
				<th>偏差値</th>
				<th>
					<?php echo $this->Paginator->sort('Statistics.avg_elapsed_second', __d('quizzes', '平均時間')); ?>
				</th>
				<th>
					<?php echo $this->Paginator->sort('Statistics.max_score', __d('quizzes', '最高点')); ?>
				</th>
				<th>
					<?php echo $this->Paginator->sort('Statistics.min_score', __d('quizzes', '最低点')); ?>
				</th>
				<th>
					<?php echo __d('quizzes', '採点'); ?>
				</th>
			</tr>
			<?php foreach ($summaryList as $summary): ?>
				<tr class="<?php echo $this->QuizResult->getPassClass($quiz, $summary); ?>">
					<td>
						<?php echo $this->QuizResult->getHandleNameLink($quiz, $summary); ?>
					</td>
					<td><?php echo h($summary['QuizAnswerSummary']['answer_number']); ?></td>
					<td>
						<?php echo $this->QuizResult->getPassing($quiz, $summary); ?>
					</td>
					<td>
						<?php echo $this->QuizResult->getWithinTime($quiz, $summary); ?>
					</td>
					<td>
						<?php echo $this->QuizResult->getScore($summary); ?>
					</td>
					<td>
						<?php echo $this->QuizResult->getStdScore($general, $summary); ?>
					</td>
					<td>
						<?php echo $this->QuizResult->getAvgElapsed($quiz, $summary); ?>
					</td>
					<td>
						<?php echo h($summary['Statistics']['max_score']); ?>
					</td>
					<td>
						<?php echo h($summary['Statistics']['min_score']); ?>
					</td>
					<td>
						<?php echo $this->QuizResult->getNotScoring($quiz, $summary); ?>
					</td>
				</tr>
			<?php endforeach; ?>
		</table>
		<span class="help-block">※未ログインでの回答は履歴管理が不可能なため全て１度だけの解答として扱われています。</span>

		<?php echo $this->element('NetCommons.paginator'); ?>

	</section>

	<div class="text-center">
		<?php echo $this->BackTo->pageLinkButton('小テストＴＯＰへ戻る', array('icon' => 'remove', 'iconSize' => 'lg')); ?>
	</div>

</article>