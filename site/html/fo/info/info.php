
<h1>Information page</h1>

<?php
	foreach($items as $item)
	{
		$elt = $item['elt'];
?>

<!-- <?php echo $item['title']; ?> -->
<div class="bar">
	<label id="bar_<?php echo $item['key']; ?>"><?php echo $item['title']; ?></label>
</div>
<div class="frame" id="frame_<?php echo $item['key']; ?>">
<?php 
	if( is_array($elt) )
	{
		ob_start();
		Common::print_r($elt);
		$content = ob_get_contents();
		ob_end_clean();
		$content = preg_replace("/\[([a-zA-Z0-9_]+)\]( =>)/", "[<label class='key'>\\1</label>]\\2", $content);
		$content = preg_replace("/(=> )([[:alpha:]]+( [[:alpha:]]+)?)(\n|\r)([\s\r\n ]+\()/", "\\1<label class='type'>\\2</label>\\4\\5", $content);
		$content = preg_replace("/(=> )([0-9]+(\.[0-9]+)?)(\n|\r)/", "\\1<label class='num'>\\2</label>\\4", $content);
		$content = preg_replace("/(Array)([\n\r]+\()/", "<label class='type'>\\1</label>\\2", $content);
		
		echo "$content\n";
	}
	else
	{
		ob_start();
		eval($elt);
		$content = ob_get_contents();
		ob_end_clean();
		echo "$content\n";
	}
?>
	
	<div class="fold_btn"><img src="<?php echo Media::IMAGE("info/bottom_fold"); ?>" id="fold_<?php echo $item['key']; ?>" /></div>
	<div class="clear"></div>
</div>

<?php
	}
?>


<p class="footer"></p>
