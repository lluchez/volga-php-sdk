<?php
	function sel_fct($item){ return $item['key']; }
	$fcts = array_map('sel_fct', $items);
?>

function toogleFrame(elt) {
	elt.toggleClassName('unfolded');
	new Effect.toggle(elt.linkedTo, 'blind', {duration: 0.3});
}

function createEventHandlers() {
	var elts = '<?php echo implode("|", $fcts); ?>'.split('|');
	$A(elts).each( function(elt) {
		var bar = $('bar_'+elt), frame = $('frame_'+elt), fold = $('fold_'+elt);
		bar.linkedTo = frame;
		fold.bar = bar;
		frame.setStyle({display: 'none'});
		bar.observe ('click', function(event) { toogleFrame(event.element()); });
		fold.observe('click', function(event) { toogleFrame(event.element().bar); });
	});
}


Core.whenReady( function() {
	createEventHandlers();
	resizeLeftPanel_TO();
});
