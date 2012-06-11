<?
Yii::app()->clientScript
	->registerCssFile(AutoAdmin::$assetPathCSS.'/edit.css')
	->registerScriptFile(AutoAdmin::$assetPathJS.'/edit.js');

$url = AAHelperUrl::replaceParam($baseURL, 'action', ($actionType == 'edit' ? 'update' : 'insert'));

if(empty($this->breadcrumbs))
	$this->breadcrumbs[$this->pageTitle] = AAHelperUrl::stripParam($url, array('action', 'id'));
else
{
	$key = array_search($this->pageTitle, $this->breadcrumbs);
	if(is_numeric($key))
	{
		unset($this->breadcrumbs[$key]);
		$this->breadcrumbs[$this->pageTitle] = AAHelperUrl::stripParam($url, array('action', 'id'));
	}
}
?>

<h1><?=$this->pageTitle?></h1>
<?
if(!empty($clientData['subtitle']))
{
	?><h2><?=$clientData['subtitle']?></h2><?
}
if(!empty($clientData['subhtml']))
{
	echo $clientData['subhtml'];
}

if($actionType == 'edit')
{	//Display subheader within information about data unit (which is beeing edited now)
	$h2MaxParts = 2;
	$h2 = '';
	$i = 0;
	foreach($fields as $field)
	{
		if($field->isReadonly && $field->type=='string' && $field->showInList)
		{
			$h2 .= ($h2 ? '. ' : '').$field->value;
			if(++$i >= $h2MaxParts)
				break;
		}
	}
	if($h2)
	{
		?><h2><?=$h2?></h2><?
		$this->pageTitle = $h2;
	}
}
if(!empty($clientData['subtitle']))
{
	?><h2><?=$clientData['subtitle']?></h2><?
}

echo CHtml::form($url, 'post', array('id'=>'editform', 'enctype'=>'multipart/form-data'));
echo CHtml::hiddenField('interface', $interface);
$itemsI = 0;
$tabindex = 1;

foreach($fields as $field)
{
	if($field->isReadonly && ($actionType == 'edit' || $field->type != 'foreign'))
		continue;
	?>
	<div class="item<?=(($itemsI%4 < 2) ? ' m':'')?> block_<?=$field->type?><?=($field->allowNull ? ' nullf' : '')?>">
		<?=$field->formInput($this, array('tabindex'=>$tabindex))?>
		<?
		if($field->description)
		{
			?><div class="desc"><?=$field->description?></div><?
		}
		?>
	</div>
	<?
	if($field->type == 'date')
		$tabindex += 3;
	elseif($field->type == 'date')
		$tabindex += 6;
	else
		$tabindex++;

	if(!(++$itemsI%2))
	{
		?><br clear="all"/><?
	}
}
if(!empty($iframes))
{
	?>
	
	<?
	if($actionType == 'add')
	{
		?><div class="item"><div class="iframe-na"><i><?=Yii::t('AutoAdmin.form', 'Submit the form in order to be able to edit additional links')?>.</i></div></div><?
	}
	else
	{
		$bkp = $bindKeysParent;
		array_push($bkp, $bindKeys);
		foreach($iframes as $iframe)
		{
			$iframeUrl = ($this->action->id=='index' ? './' : '../')."foreign-{$iframe['action']}/";
			$iframeUrl = AAHelperUrl::update($iframeUrl, null, array(
					'bkp'		=> $bkp,
					'bk'		=> $fields->pk,
					'foreign'	=> AAHelperUrl::encodeParam($iframe['foreign']),
				));
			?>
			<div class="item<?=(!empty($iframe['wide']) || in_array('wide', $iframe) ? ' wide' : '')?>">
			<?
			echo CHtml::tag('iframe', array(
					'src'	=> $iframeUrl,
				),
				null, true);
			if($field->description)
			{
				?><div class="desc"><?=$field->description?></div><?
			}
			?>
			</div>
			<?
		}
	}
}
?>
<div class="br">&nbsp;</div>
<?=CHtml::submitButton(Yii::t('AutoAdmin.common', 'Save'), array('name'=>null));?>
<?=CHtml::closeTag('form');?>

<? $this->renderPartial($viewsPath.'footer', array('isGuest'=>$isGuest, 'userName'=>$userName, 'userLevel'=>$userLevel));?>