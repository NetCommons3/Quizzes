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
<?php if (in_array($quizKey, $passQuizKeys)): ?>
    <?php echo $this->TitleIcon->titleIcon('/net_commons/img/title_icon/10_051_pass.svg'); ?>
<?php endif;
