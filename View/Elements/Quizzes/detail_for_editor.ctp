<?php
/**
 * quiz list detail view template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>
<div class="row">
	<div class="col-md-12 col-xs-12">
		<div class=" well well-sm">
			<div class="pull-right">
				<?php echo $this->Button->editLink('', array(
				'plugin' => 'quizzes',
				'controller' => 'quiz_edit',
				'action' => 'edit_question',
				'key' => $quiz['Quiz']['key'])); ?>
			</div>
			<small>
				<dl class="quiz-editor-dl">
					<dt><?php echo __d('quizzes', 'Author'); ?></dt>
					<dd>
						<?php echo $this->NetCommonsHtml->handleLink(
						$quiz,
						array('avatar' => false),
						array(),
						'TrackableCreator'); ?>
					</dd>
					<dt><?php echo __d('quizzes', 'Modified by'); ?></dt>
					<dd>
						<?php echo $this->NetCommonsHtml->handleLink(
						$quiz,
						array('avatar' => false),
						array(),
						'TrackableUpdater'); ?>
						(<?php echo $this->Date->dateFormat($quiz['Quiz']['modified']); ?>)
					</dd>
				</dl>
				<dl class="quiz-editor-dl">
					<dt><?php echo __d('quizzes', 'Pages'); ?></dt>
					<dd><?php echo $quiz['Quiz']['page_count']; ?></dd>
					<dt><?php echo __d('quizzes', 'Questions'); ?></dt>
					<dd><?php echo $quiz['Quiz']['question_count']; ?></dd>
					<dt><?php echo __d('quizzes', 'Answers' ); ?></dt>
					<dd><?php echo $quiz['Quiz']['all_answer_count']; ?></dd>
				</dl>
				<div class="clearfix"></div>
			</small>
		</div>
	</div>
</div>
