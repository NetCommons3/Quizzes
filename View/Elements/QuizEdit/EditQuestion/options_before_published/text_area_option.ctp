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
<?php echo __d('quizzes', '記述式の場合、正解の設定はありません。'); ?>
        <br />
<?php echo $this->element('Quizzes.QuizEdit/EditQuestion/commentary');
