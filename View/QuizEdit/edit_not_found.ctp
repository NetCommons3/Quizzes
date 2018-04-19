<?php
/**
 * quizzes page edit view template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>
<?php if (Current::permission('block_editable') && Current::isSettingMode()) : ?>
	<?php echo $this->BlockTabs->main(BlockTabsHelper::MAIN_TAB_BLOCK_INDEX); ?>
<?php endif ?>
<article>
    <?php echo __d('quizzes', 'not found this quiz.'); ?>

    <div class="text-center">
        <?php echo $this->BackTo->linkButton(__d('net_commons', 'Cancel'), $cancelUrl); ?>
    </div>
</article>
