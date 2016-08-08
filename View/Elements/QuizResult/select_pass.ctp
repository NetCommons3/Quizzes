<?php
/**
 * quiz result select pass view template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
$url = array(
	'plugin' => 'quizzes',
	'controller' => 'quiz_result',
	'action' => 'index',
	'key' => $quiz['Quiz']['key'],
);
?>

<span class="btn-group">
	<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
		<?php echo $list[$currentStatus]; ?>
		<span class="caret"></span>
	</button>
	<ul class="dropdown-menu" role="menu">
		<?php foreach ($list as $key => $label) : ?>
			<li <?php echo ($key === $currentStatus ? ' class="active"' : ''); ?>>
				<?php
				if ($key == '') {
					$key = null;
				}
			?>
				<?php echo $this->Paginator->link($label, array($keyName => $key), array('url' => $url)); ?>
			</li>
		<?php endforeach; ?>
	</ul>
</span>
