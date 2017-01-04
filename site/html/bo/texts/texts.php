<?php
	include 'form_creation.inc.php';
	
	/* - - - - Creation of bars and panels - - - - */
	foreach($panels as $i => $paneldID)
	{
		if( $forms[$paneldID] && ($display_result_panel || ($paneldID != VIEWING_MODE_VIEW_RESULTS)) )
		{
?>
<div class="bar"<?php if($i==0) { echo ' style="margin-top: 1em;"'; } ?> id="bar_<?php echo $paneldID; ?>">
	<label id="label_<?php echo $paneldID; ?>"><?php echo Lang::translate("bo.texts.$paneldID.barTitle"); ?></label>
</div>
<div id="frame_<?php echo $paneldID; ?>" class="frame">
<?php
	/* - - - - Success message - - - - */
	if( Vars::defined(GET_RESOURCE_CREATED) && $panel_to_display == $paneldID )
	{
		$res = Vars::get(GET_IDSEARCH_KEY);
		$res = ($superAdmin && Vars::defined(GET_RES_ID)) ? ("'$res' <span>(#".Vars::get(GET_RES_ID).')</span>') : "'<i>$res</i>'";
		$text = str_replace('%1', $res, Lang::translate('bo.texts.create.success'));
?>
<div class="message" id="<?php echo MESSAGE_PANEL_ID; ?>">
	<label class="success"><?php echo $text; ?></label>
</div>
<?php
	}
	
	echo $forms[$paneldID];
	//if( $forms[$paneldID] )
	//	echo $forms[$paneldID];
?>
</div>
<?php
		}
	}
?>