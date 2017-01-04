var dl_images = null, thumbs_links = null;
var thumbnails = Array();

function addListeners() {
	dl_images = $('programs').select('tr.title a img');
	dl_images.each( function(img) {
		Element.observe(img, 'mouseover', function(event){img._src=img.src; img.src="<?php echo Media::IMAGE('progs/download_over'); ?>";});
		Element.observe(img, 'mouseout', function(event){img.src = img._src;});
	});
	
	thumbs_links = $('programs').select('a.thumbs');
	thumbs_links.each( function(link) {
		var id = parseInt(link.id.replace(/thumbs_id_/, ''), 10);
		Element.observe(link, 'click', function(event){ new Core.Thumbnails(thumbnails[id]); event.stop(); });
	});
}

function removeListeners() {
	if( dl_images ) {
		dl_images.each( function(img) {
			Element.stopObserving(img, 'mouseover');
			Element.stopObserving(img, 'mouseout');
		} );
		dl_images = null;
	}
	
	if( thumbs_links ) {
		thumbs_links.each( function(link) {
			Element.stopObserving(link, 'click');
		} );
		thumbs_links = null;
	}
}

function createThumbnailsLists() {
<?php
	$groups = Array();
	$photos = DB::select("SELECT `phProgIDx`, `phFilename` FROM `prog_photos` ORDER BY `phProgIDx`, `phOrder`");
	foreach($photos as $photo)
		$groups[$photo->phProgIDx][] = Media::DATA('progs/thumbnails/'.$photo->phFilename);
	foreach($groups as $i=>$group)
		echo "\tthumbnails[$i] = Array('".implode("', '", $group)."');\n";
?>
}

Core.whenReady( function() {
	var form = new Core.Form('search_form', null, {
			prepareSubmit: function() {
				removeListeners();
				$('programs').innerHTML = "";
				$('processing').show();
				resizeLeftPanel();
			},
			ajaxCallback: function(response) {
				$('programs').innerHTML = response.responseText;
				$('processing').hide();
				resizeLeftPanel();
				addListeners();
				form.isAlreadySubmited = false;
			}
		}
	);
	
	addListeners();
	createThumbnailsLists();
});