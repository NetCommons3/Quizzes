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
$urlParams = Hash::merge(array(
	'controller' => 'quiz_result',
	'action' => 'index'),
	$this->params['named']);
$named = $this->Paginator->params['named'];
$named['page'] = '1';
$url = $named;
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
				<?php echo $this->Paginator->link($label, Hash::merge($url, array($keyName => $key))); ?>
			</li>
		<?php endforeach; ?>
	</ul>
</span>
