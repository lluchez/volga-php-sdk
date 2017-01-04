
/********************************
 *      Default properties      *
 ********************************/
 
<?php
	if( Client::getBrowserName() == 'Opera' )
		echo "html { font-size: 90%; }\n";
	else
		echo "html { font-size: 100%; }\n";
?>


body { 
  margin: 0; 
  padding: 1em;<?php /* Remettre à zéro si nécessaire. */  ?> 
  font-family: Helvetica, Verdana, Arial, sans-serif;
  /* font-family: Arial, Helvetica, FreeSans, sans-serif; */
  font-size: .8em;
  line-height: 1.4;
  color: black; 
  background: white; 
	<?php /*position: relative;*/ ?>/* do not set to 'relative' => popup won't be positionned well !!! */
} 
 
/* Titres */ 
h1, h2, h3, h4, h5, h6 { 
  margin: 1em 0 .5em 0;
  line-height: 1.2; 
  font-weight: bold;
  font-style: normal; 
} 
h1 { font-size: 1.75em; } 
h2 { font-size: 1.50em; } 
h3 { font-size: 1.25em; } 
h4 { font-size: 1.00em; } 

ul, ol { margin: .75em 0 .75em 32px; padding: 0; } 
 
p { margin: .75em 0; }

a { text-decoration: underline; }
a img { border: none; } 
 
em { font-style: italic; } 
strong { font-weight: bold; } 
input, button, select { vertical-align: middle; }

<?php
	if( preg_match('/^(MSIE|Opera)$/i', Client::getBrowserName()) )
		echo "pre { font-size: 85%; }\n";
?>

.success { color: green; }
.error { color: red; }
.warning { color: orange; }





/********************************
 *         Popup Windows        *
 ********************************/

div#popupOverlay {
	position: fixed;
	top: 0; left: 0;
	height: 100%; width: 100%;
	z-index: 10000;
	background-color:#000000;
	/*opacity: 0.6; set in Core.js */
}

div#popup {
	display: block;
	position: absolute;
	top: 50%; left: 50%;
	background-color: white;
	z-index: 10002;
}


#popup div.title {
	background: url('<?php echo Media::IMAGE('popup/bg1'); ?>') repeat-x;
	background-position: 0 4px;
	top: -4px;
	position: relative;
	color: black;
}

#popup div.title div.dragger {
	margin: 0 5px;
	text-align: center;
	background: url('<?php echo Media::IMAGE('popup/bg2'); ?>') repeat-x;
	text-shadow: silver 2px 2px 2px;
	height: 27px;
	cursor: default;
}

#popup div.title div.dragger h1 {
	margin: 0 15px 0 -2px;
	padding: 0;
	padding-top: 4px;
	font-size: 1.3em;
	text-shadow: silver 2px 2px 2px;
}

#popup div.title div.bl, #popup div.title div.br {
	position: absolute;
	top: 0;
}

#popup div.title div.bl {
	left:0; width: 9px; height: 9px;
	background: url('<?php echo Media::IMAGE('popup/lt'); ?>') no-repeat;
}
#popup div.title div.br { 
	right: 0; width: 20px; height: 20px;
	background: url('<?php echo Media::IMAGE('popup/rt'); ?>') no-repeat top right; 
}

#popup div.title div.br div#popupClose { 
	background: url('<?php echo Media::IMAGE('popup/exit'); ?>');
	width: 16px; height: 17px;
	margin-top: 3px;
	cursor: pointer;
}

#popup div.title div.br div#popupClose.over { background: url('<?php echo Media::IMAGE('popup/exit_over'); ?>'); }


#popup div.content { margin: 0; padding: 0.5em; color: black; }
#popup.hasIcon div.content { text-align: center; }
#popup.error { background: #FDE8E8 url('<?php echo Media::IMAGE('popup/error'); ?>') no-repeat 10px 30px; }
#popup.warning { background: #FFFFE0 url('<?php echo Media::IMAGE('popup/warning'); ?>') no-repeat 10px 30px; }
#popup.information { background: #E0E0FF url('<?php echo Media::IMAGE('popup/info'); ?>') no-repeat 10px 30px; }


#popup div#pop-bottom {
	clear: left;
	position: absolute;
	bottom: 0;
	width: 100%;
	margin: 0;
	text-align: center;
	padding-bottom: 0.25em;
}

#popup div#pop-bottom input#popupBtn {
	width: 107px;
	height: 25px;
	background-image: url('<?php echo Media::IMAGE('popup/button'); ?>');
	border: 0;
	cursor: pointer;
}

#popup div#pop-bottom input#popupBtn.over { background-image: url('<?php echo Media::IMAGE('popup/button_over'); ?>'); }
#popup.drag div.title h1 { cursor: move !important; }


/********************************
 *      Thumbnails Windows      *
 ********************************/

#popup.normal { text-align: center; }
#popup.normal #pop-bottom div { cursor: pointer; background-repeat: no-repeat; }
#popup.normal #pop-bottom div label { cursor: pointer; }


#popup.normal div#pop-bottom label#thumb_label { 
	display: block; width: 40px; margin: 0 auto;
	padding: 3px; border: 1px dotted #888; background-color: #DDE;
}
#popup.normal div#pop-bottom div { font-weight: bold; padding-top: 4px; height: 24px; <?php echo Client::isCompliantWithCSS3() ? 'display: inline-block' : 'display: block; width: 80px'; ?>; }
#popup.normal div#pop-bottom div.over { color: #888; }

#popup.normal div#pop-bottom div#thumb_previous {
	position: absolute; left: 3px; padding-left: 30px; text-align: left; 
	background-image: url('<?php echo Media::IMAGE("popup/thumb_prev"); ?>'); background-position: left center;
}
#popup.normal div#pop-bottom div#thumb_next {
	position: absolute; right: 3px; padding-right: 30px; text-align: right; 
	background-image: url('<?php echo Media::IMAGE("popup/thumb_next"); ?>'); background-position: right center;
}

#popup.normal div#thumb_loading {
	width: 100px; height: 100px; margin: auto;
	background-image: url('<?php echo Media::IMAGE("popup/loading2"); ?>');
}

