
div.bar
{
	border: 1px solid #000;
	font-size: 1.1em;
	font-weight: bold;
	padding: 0.125em 0.5em;
	background-image: url('<?php echo Media::IMAGE("info/bar"); ?>');
	background-repeat: repeat-x;
}

div#bar_<?php echo $items[0]['key'] ; ?>.bar
{
	margin-top: 1em;
}

div.bar label
{
	background-image: url('<?php echo Media::IMAGE("info/fold"); ?>');
	background-repeat: no-repeat;
	background-position: center left;
	padding-left: 1em;
	cursor: pointer;
	color: #DDD;
}

div.bar label.unfolded
{
	background-image: url('<?php echo Media::IMAGE("info/unfold"); ?>');
}

div.bar label:hover
{
	color: white;
}


div.frame
{
	background-color: #EAEAFF;
	border: 1px dashed grey;
	border-top: none;
	margin-bottom: 1em;
	padding: 0.5em 1em;
}

div.frame pre { margin: 0; }
div.frame pre label.key  { color: #C60; }
div.frame pre label.type { color: #03C; }
div.frame pre label.num  { color: #F00; }


p.footer { }


div.frame div.fold_btn { bottom: 0; right: 0; }
div.frame div.fold_btn img { cursor: pointer; }
div.frame div.clear { clear: right; }
