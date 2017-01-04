var thumbnails = Array();
var tooltip = null;

function addListeners() {
	var thumbs_links = $('web_sites').select('a.thumbs');
	thumbs_links.each( function(link) {
		var id = parseInt(link.id.replace(/thumbs_id_/, ''), 10);
		Element.observe(link, 'click', function(event){ new Core.Thumbnails(thumbnails[id]); event.stop(); });
	});
	
	var offline_p = $('web_sites').select('p.offline');
	offline_p.each( function(p) {
		Element.observe(p, 'mouseover', tooltip.show.bindAsEventListener(tooltip));
		Element.observe(p, 'mouseout',  tooltip.hide.bindAsEventListener(tooltip));
	});
}


function createThumbnailsLists() {
<?php
	$groups = Array();
	$photos = DB::select("SELECT * FROM `website_photos` ORDER BY `phSiteIDx`, `phOrder`");
	foreach($photos as $photo)
		$groups[$photo->phSiteIDx][] = Media::DATA('websites/'.$photo->phFilename);
	foreach($groups as $i=>$group)
		echo "\tthumbnails[$i] = Array('".implode("', '", $group)."');\n";
?>
}

Core.whenReady( function() {
	tooltip = new Core.ToolTip('tooltip', {followMouse: true, posX: -125});
	addListeners();
	createThumbnailsLists();
});