<?php
/**
 * quiz content list view template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

echo $this->NetCommonsHtml->script(array(
	'/authorization_keys/js/authorization_keys.js',
));
?>
<article class="block-setting-body">
	<?php echo $this->BlockTabs->main(BlockTabsHelper::MAIN_TAB_BLOCK_INDEX); ?>

	<?php echo $this->BlockIndex->description(__d('quizzes', 'You can download only the issued quiz data.')); ?>

	<div class="tab-content">
		<?php echo $this->BlockIndex->addLink('',
		array(
			'controller' => 'quiz',
			'action' => 'add',
			'frame_id' => Current::read('Frame.id'),
			'block_id' => Current::read('Block.id'),
		)); ?>

		<div id="nc-quiz-setting-<?php echo Current::read('Frame.id'); ?>">
			<?php echo $this->BlockIndex->startTable(); ?>
				<thead>
				<tr>
					<?php echo $this->BlockIndex->tableHeader(
					'Quiz.status', __d('quizzes', 'Status'),
					array('sort' => true, 'type' => false)
					); ?>
					<?php echo $this->BlockIndex->tableHeader(
					'Quiz.title', __d('quizzes', 'Title'),
					array('sort' => true)
					); ?>
					<?php echo $this->BlockIndex->tableHeader(
					'Quiz.modified', __d('net_commons', 'Updated date'),
					array('sort' => true, 'type' => 'datetime')
					); ?>
					<?php echo $this->BlockIndex->tableHeader(
						'', __d('quizzes', 'Answer CSV'),
						array('type' => 'center')
					); ?>
					<?php echo $this->BlockIndex->tableHeader(
						'', __d('quizzes', 'Templates'),
						array('type' => 'center')
					); ?>
				</tr>
				</thead>
				<tbody>
				<?php foreach ((array)$quizzes as $quiz) : ?>
				<?php echo $this->BlockIndex->startTableRow($quiz['Quiz']['key']); ?>
					<?php echo $this->BlockIndex->tableData(
						'',
						$this->QuizStatusLabel->statusLabelManagementWidget($quiz),
						array('escape' => false)
					); ?>

					<?php echo $this->BlockIndex->tableData(
						'',
				$quiz['Quiz']['title'],
						array(
						'escape' => false,
						'editUrl' => array(
						'plugin' => 'quizzes',
						'controller' => 'quiz_edit',
						'action' => 'edit_question',
						$quiz['Quiz']['key'],
						'frame_id' => Current::read('Frame.id')
						)
					)); ?>

					<?php echo $this->BlockIndex->tableData(
						'',
						$quiz['Quiz']['modified'],
						array('type' => 'datetime')
					); ?>

					<?php if ($quiz['Quiz']['all_answer_count'] > 0): ?>
						<?php echo $this->BlockIndex->tableData(
							'',
							$this->AuthKeyPopupButton->popupButton(
								array(
									'url' => NetCommonsUrl::actionUrl(array(
									'plugin' => 'quizzes',
									'controller' => 'quiz_blocks',
									'action' => 'download',
									Current::read('Block.id'),
									$quiz['Quiz']['key'],
									'frame_id' => Current::read('Frame.id'))),
									'popup-title' => __d('authorization_keys', 'Compression password'),
									'popup-label' => __d('authorization_keys', 'Compression password'),
									'popup-placeholder' => __d('authorization_keys', 'please input compression password'),
									)
								),
								array('escape' => false, 'type' => 'center')
							); ?>
					<?php else: ?>
						<td></td>
					<?php endif; ?>
					<?php if ($quiz['Quiz']['status'] == WorkflowComponent::STATUS_PUBLISHED): ?>
						<?php echo $this->BlockIndex->tableData(
							'',
							$this->BackTo->linkButton('',
								NetCommonsUrl::actionUrl(array(
									'plugin' => 'quizzes',
									'controller' => 'quiz_blocks',
									'action' => 'export',
									Current::read('Block.id'),
									$quiz['Quiz']['key'],
									'frame_id' => Current::read('Frame.id'))
								),
								array('class' => 'btn btn-warning', 'icon' => 'export')
							),
							array('escape' => false, 'type' => 'center')
						); ?>
					<?php else: ?>
						<td></td>
					<?php endif; ?>
				<?php echo $this->BlockIndex->endTableRow(); ?>
				<?php endforeach; ?>
				</tbody>
			<?php echo $this->BlockIndex->endTable(); ?>
			<?php echo $this->element('NetCommons.paginator'); ?>
		</div>
	</div>
</article>