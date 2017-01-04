
/** ----------- Bars/Sections ----------- */

div.bar {
	border: 1px solid #000;
	font-size: 1.1em;
	font-weight: bold;
	padding: 0.125em 0.5em;
	background-image: url('<?php echo Media::IMAGE("info/bar"); ?>');
	background-repeat: repeat-x;
}

div#bar_<?php echo $panels[0]; ?>.bar { margin-top: 1em; }


div.bar label {
	background-image: url('<?php echo Media::IMAGE("info/fold"); ?>');
	background-repeat: no-repeat;
	background-position: center left;
	padding-left: 1em;
	cursor: pointer;
	color: #DDD;
}

div.bar label.unfolded { background-image: url('<?php echo Media::IMAGE("info/unfold"); ?>'); }

div.bar label:hover{ color: white; }


div.frame {
	background-color: #EAEAFF;
	border: 1px dashed grey;
	border-top: none;
	margin-bottom: 1em;
	padding: 0.5em 1em;
}

div.message label span { font-size: 90%; font-style: italic; font-weight: normal; }



/** ----------- Results ----------- */

table.results { background-color: white; width: 80%; margin: 0 auto; border: 1px solid #888; border-collapse: collapse; }
table.results thead td { background-color: black; color: white; font-weight: bold; padding: 1px 5px; }
table.results tr td { border: 1px solid #888; cursor: default; }
table.results tbody tr td { padding: 0 2px; }
table.results tbody tr.bo { background-color: beige; }
table.results tbody tr.bo td.keys { background: url('<?php echo Media::IMAGE("icons/admin_rsc"); ?>') no-repeat right center; }
table.results tbody tr:hover td, table.results tbody tr.over td, table.results tbody tr.bo:hover td { background-color: #CDF; }
table.results tbody td.keys div.idx { display: none; }
table.results tbody td.actions { width: 40px; text-align: center; vertical-align: middle; }
table.results tbody td.actions img { cursor: pointer; }

<?php 
if( Client::isCompliantWithCSS3() )
{
?>
table.results tbody tr {  }
table.results tbody tr td.text div { max-height: 5em; overflow-y: auto; }
<?php 
}
?>


/** ----------- Keys auto-completion ----------- */

div.autocomplete {
	position: absolute;
	width: 250px;
	background-color: white;
	border: 1px solid #888;
	margin: 0;
	padding: 0;
}
div.autocomplete ul { list-style-type: none; margin: 0; padding: 0; }
div.autocomplete ul li {
	list-style-type: none;
	display: block;
	margin: 0;
	padding: 2px;
	cursor: pointer;
	color: #888;
}
div.autocomplete ul li high { font-weight: bold; font-style: italic; color: #000; font-size: 0.9em; }
div.autocomplete ul li.selected { background-color: #ffb; }
div.autocomplete ul li.not_found { 
	background-color: #DDD; 
	cursor: default; 
	font-style: italic; 
	font-weight: bold;
	padding-left: 10px; 
	color: #F44;
}