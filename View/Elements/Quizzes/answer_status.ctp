<?php
/**
 * quiz comment template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

$urlParams = Hash::merge(array(
	'controller' => 'quizzes',
	'action' => 'index'),
	$this->params['named']);
?>

<div class="form-group quiz-list-select">

	<label><?php echo __d('quizzes', 'Answer status'); ?></label>

	<span class="btn-group">
		<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
			<?php echo $filterList[$currentStatus]; ?>
			<span class="caret"></span>
		</button>
		<ul class="dropdown-menu" role="menu">
			<?php foreach ($filterList as $key => $status) : ?>
				<li<?php echo ($status === $currentStatus ? ' class="active"' : ''); ?>>
					<?php echo $this->NetCommonsHtml->link($status,
						Hash::merge($urlParams, array('answer_status' => $key))
					); ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</span>

</div>