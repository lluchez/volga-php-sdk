
function resizeLeftPanel()
{
	if( $('viewport') ) {
		var h = $('viewport').getHeight(), hImgSpiral = 43;
		$('left-side').setStyle({height: h+'px'});
		var base = Math.floor(h / hImgSpiral) * hImgSpiral;
		var hDivSpiral = ( h > base && h < base+22 ) ? base : h;
		var diff = (h == hDivSpiral) ? (h - (base+22)) : (21+h-base);
		var marginTop = (Math.floor(diff / 2));
		if( hDivSpiral + marginTop > h )
			hDivSpiral = h - marginTop;
		$('spirales').setStyle({'height': hDivSpiral+'px', 'marginTop': marginTop+'px'});
		return true;
	}
}

function resizeLeftPanel_TO() {
	if( resizeLeftPanel() )
		setTimeout("resizeLeftPanel_TO()", 100);
}

<?php
	include 'init_core.js.php';
?>


document.observe('dom:loaded', function() {
	resizeLeftPanel();
	
	Core.init();
	
	if( $('p7PMnav') ) {
		P7_initPM(1, 5, 1, -20, 10);
		$$("#p7PMnav li a").each( function(link) {
			if( link.href.match(/#$/) ) {
				Event.observe(link, 'click', function(event){ Event.stop(event); return false; });
			}
		});
	}
});
