<?php
/**
 * ページ先頭に何か問題文を入れるときのテンプレ
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>

<div class="col-xs-12" ng-show="page.isPageDescription == <?php echo QuizzesComponent::USES_USE; ?>">
    <p ng-bind-html="page.pageDescription"></p>
    {{page.pageDescription}}
</div>
