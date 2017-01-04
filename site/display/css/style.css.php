
/** --------------------------
 *        Layout
 * -------------------------- */

body {
	background-color: beige;
}

#frame {
	position: relative;
	width: 996px;
	margin-left: auto;
	margin-right: auto;
	text-align: left;
}

#body {	padding: 5px; }


#left-side {
	position: absolute;
	height: 500px;
	width: 22px;
	left: 22px;
	background: url('<?php echo Media::IMAGE('layout/left-border'); ?>') repeat-y;
}

#spirales {
	position: absolute;
	height: 100px;
	width: 76px;
	background: url('<?php echo Media::IMAGE('layout/spirales'); ?>') repeat-y;
	z-index: 2;
	left: 0;
}

#viewport {
	margin-left: 44px;
	background: url('<?php echo Media::IMAGE('layout/bkgd'); ?>');
	padding: 0 5px 5px 40px;
}

#title {
	height: 120px;
	background: url('<?php echo Media::IMAGE('layout/banner'); ?>') no-repeat;
	margin-left: 20px;
	position: relative;
	display: block;
}

#set_language { float: right; margin: 30px 75px 0 0; list-style-type: none; }
#set_language li { float: left; margin-right: 7px; }
#set_language label { font-weight: bold; text-shadow: silver 2px 2px 2px;; }

#sub-content { width: 890px; }
#sub-content div.top-content {
	height: 38px;
	background: url('<?php echo Media::IMAGE('layout/vp/top'); ?>');
}
#sub-content div.middle-content {
	background: url('<?php echo Media::IMAGE('layout/vp/middle'); ?>');
	padding: 0 38px;
	border-top: 1px solid transparent;
	border-bottom: 1px solid transparent;
}
#sub-content div.bottom-content {
	height: 48px;
	background: url('<?php echo Media::IMAGE('layout/vp/bottom'); ?>');
}

div#content { position: relative; display: block; }
div#content h1 { margin: 0; padding: 0 1em 0.25em; }


#menu {
	clear: both;
	position: relative;
	top: 42px;
	float: right;
	margin-right: 80px;
}


<?php
	if( Client::isOldMSIE() )
	{
?>
/** CSS Fixes for browsers not compliant with CSS3 */

body #sub-content div#content { border: 0; }

hr.content-spacer {
	height: 1px;
	color: #F0F0F0;
	margin: 0;
	padding: 0;
}
<?php
	}
	
	if( ! Client::isCompliantWithCSS3() )
	{
		echo "body #left-side { width: 80px; left: 0; }\n";
		echo "#set_language { margin-right: 35px; }\n";
		echo "#sub-content div.middle-content { border: 0; }\n";
		echo "#menu { margin-right: 0px; right: 80px; }\n";
	}
?>

/** --------------------------
 *      Pages properties
 * -------------------------- */

#frame #content h1 { color: #856230; text-align: left; border-bottom: 1px dashed #856230; text-shadow: silver 2px 2px 2px; }
#frame #content a, #frame #content a:visited, #frame #content a:link { color: #856230; text-decoration: none; }
#frame #content a.under, #frame #content a.under:link, #frame #content a.under:link { border-bottom: 1px dashed #856230; }
#frame #content a:hover { color: #553200; text-decoration: underline; border-bottom: 0; }
