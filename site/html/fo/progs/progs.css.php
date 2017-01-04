div#programs { margin: 1em 5px; }

div#programs center.progs_found { margin-bottom: 8px; margin-top: -3px; }
div#programs center.progs_found label { font-weight: bold; color: green; border-bottom: 1px dashed green; padding: 0 15px; }
div#programs center.<?php echo Client::isCompliantWithCSS3() ? 'progs_found.' : ''; ?>warning label { color: orange !important; border-bottom: 1px dashed orange !important; }

div#programs table.cat_games { background-color: #E4E4FF; }
div#programs table.cat_tools { background-color: #FFFFF0; }
div#programs table.cat_multi { background-color: #E4E4E4; }

div#programs table.prog { 
	border: 1px solid black;
	border-collapse: collapse;
	clear: right;
	width: 100%;
	margin: 4px 0 3px;
}

div#programs table.prog td.image img { border: 1px dotted #AAA; background-color: white; }
div#programs table.prog td { padding: 2px 3px; }

div#programs table.prog tr.title td {
	font-size: 1.25em; font-weight: bold;
	text-align: center;
	color: #A58250; text-shadow: silver 2px 2px 2px;
	padding-bottom: 3px;
	border-bottom: 1px dotted #A58250;
	background-color: beige;
	padding: 2px 0 0;
}

div#programs table.prog tr.title td a {
	float: right; margin: 1px 3px -1px;
}

div#programs table.prog .info label {
	font-weight: bold;
	border-bottom: 1px dotted #444;
}

div#programs table.prog td.desc { border-top: 1px dotted grey; }
div#programs table.prog td.desc p { text-align: justify; margin: 0; padding: 2px 10px 0; }
div#programs table.prog td.image { width: 40px; text-align: center; vertical-align: middle; }

#processing { text-align: center; margin: 5px;}

<?php
if( ! Client::isCompliantWithCSS3() )
{
?>
div#programs table.prog tr.title td a { margin-top: -20px; }
<?php
}
?>