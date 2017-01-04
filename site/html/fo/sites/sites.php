<?php
	$sql = "SELECT * FROM `website` ORDER BY `wCreationYear` DESC, `wID` DESC";
	$sites = DB::select($sql);
	
	$descriptions = Lang::translateKeys("^wsites\.desc\.");
	$languages    = Lang::translateKeys("^lang");
	
	function generateLanguagesField($langs)
	{
		global $languages;
		$ret = Array();
		$ls = explode(',',$langs);
		foreach($ls as $l)
			if( $lang = $languages["lang_$l"] )
				$ret[] = $lang;
		return implode(', ', $ret);
	}
?>
<div id="tooltip" style="display: none;"><?php echo Lang::translate('wsites.offline.text'); ?></div>
<div id="web_sites">
<?php
	foreach($sites as $site)
	{
		$link = $site->wLink;
		if( $link ) $link = "<a href=\"{$link}\" target='_blank'>{$link}</a>";
		$nbImgs = DB::getField("SELECT COUNT(*) FROM `website_photos` WHERE `phSiteIDx`={$site->wID}");
		$txtNbImgs = Lang::translateQuantity('wsites.label.nb_images', $nbImgs);
		if( $nbImgs ) $txtNbImgs = "<a id='thumbs_id_{$site->wID}' href='#'class='thumbs'>{$txtNbImgs}</a>";
?>
	<div class="site" id="site_<?php echo $site->wID; ?>">
		<img src="<?php echo Media::DATA('websites/logo/'.$site->wLogo); ?>" title="" />
		<div class="info">
			<h3><?php echo $site->wName; ?></h3>
			<p><span>Date</span>: <label><?php echo $site->wCreationYear; ?></label></p>
			<p><span>Langues</span>: <label><?php echo generateLanguagesField($site->wLanguages); ?></label></p>
<?php if( $link ): ?>
			<p><span>Lien</span>: <label><?php echo $link; ?></label></p>
<?php endif; ?>
			<p><span>Aperçus</span>: <label><?php echo $txtNbImgs; ?></label></p>
			<hr />
			<p><span>Description</span>: <?php echo $descriptions[$site->wDescription]; ?></p>
<?php if( $site->wOfflineReason ): ?>
			<hr />
			<p class='offline'><span>Offline reason</span>: <?php echo Lang::translate($site->wOfflineReason); ?></p>
<?php endif; ?>
		</div>
	</div>
<?php
	}
?>
</div>