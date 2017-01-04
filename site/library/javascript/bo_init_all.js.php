<?php
	include 'init_core.js.php';
?>

document.observe('dom:loaded', function() {

	Core.init();
	
	if( $('p7PMnav') ) {
		P7_initPM(1,5,1,-20,10);
		$$("#p7PMnav li a").each( function(link) {
			if( link.href.match(/#$/) ) {
				Event.observe(link, 'click', function(event){ Event.stop(event); return false; });
			} else if ( link.hasClassName('logout') ) {
				Event.observe(link, 'click', function(event){ 
					if( ! confirm(Core.Messages.get('bo.logout.confirmation')) ) {
						Event.stop(event); return false;
					}
				});
			}
		});
	}
});
