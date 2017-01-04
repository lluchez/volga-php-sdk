
Core.init = function()
{
	// Add localized messages
<?php
	Lang::addMessages('(Thumbnails.([A-Za-z]+)|Popup.([A-Za-z.]+))', "\t");
	Lang::addMessages('Validate.ErrorPanel.([A-Za-z]+)', "\t");
	if( Common::isAdminContext() /*&& Authentication::isLogged()*/ ) // or disable cache for this page, or add '&logged=true'
		Lang::addMessages('bo.logout.confirmation', "\t");
?>
	
	// Global data
	Object.extend(Core.Options, {AjaxEncoding: '<?php echo ENCTYPE_CHARSET; ?>'});
	Core.isInit = true;
<?php echo Common::isLocalContext() ? "\tCore.isLocalContext = true;\n" : ""; ?>
};

