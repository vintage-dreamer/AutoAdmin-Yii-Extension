<?php
$this->pageTitle = Yii::t('AutoAdmin.access', 'Authentication');
Yii::app()->clientScript
	->registerCssFile(AutoAdmin::$assetPathCSS.'/login.css');

$this->breadcrumbs = array(
	$this->pageTitle,
);
?>

<h1><?=$this->pageTitle?></h1>

<p class="greeting"><?=Yii::t('AutoAdmin.access', 'You\'ve been entered as <b>{userName}</b>. It\'s nice to see you!', array('{userName}'=>$userName))?></p>