<?php
	$a = Common::isAdminContext();
	define('ITEM_BKGD_OUT', $a ? '#9BF' : 'black');
	define('ITEM_TEXT_OUT', $a ? '#FEE' : 'white');
	define('SUB_BKGD_OUT', $a ? '#EEF' : '#C5A270');
	define('SUB_TEXT_OUT', $a ? '#8AC': 'white');
	
	define('BKGD_OVER', $a ? 'white': '#B59260');
	define('SUB_TEXT_OVER', $a ? '#8AC': 'black');
	
	define('OUTTER_BORDER', $a ? '#68A' : 'black');
	define('INNER_BORDER', $a ? '#8AC' : '#654');

?>
/* 
  -----------------------------------
  PopMenu Magic Style Sheet
  by Project Seven Development
  www.projectseven.com
  Menu Type: Horizontal
  Style Theme:5 -Cool Box
  -----------------------------------
*/
ul#p7PMnav, #p7PMnav ul {
	z-index: 500;
	margin: 0;
	padding: 0;
}
#p7PMnav li {
	list-style-type: none;
	margin: 0;
	padding: 0;
}
#p7PMnav ul, #p7PMnav ul li ul {
	padding: 0;
	border: 2px solid <?php echo INNER_BORDER; ?>;
	border-top: 1px solid <?php echo OUTTER_BORDER; ?>;
	border-left: 1px solid <?php echo OUTTER_BORDER; ?>;
}
#p7PMnav a {
	display: block;
	background-color: <?php echo ITEM_BKGD_OUT; ?>;
	border: 1px solid <?php echo ITEM_BKGD_OUT; ?>;
	padding: 6px 8px 4px;
	color: <?php echo ITEM_TEXT_OUT; ?>;
	font-weight: bold;
	line-height:1;
	text-decoration: none;
}
#p7PMnav ul a {
	padding: 4px 10px;
	background-color: <?php echo SUB_BKGD_OUT; ?>;
	font-weight: bold;
	color: <?php echo SUB_TEXT_OUT; ?>;
}
#p7PMnav a:hover, #p7PMnav a:active, #p7PMnav a:focus/*, #p7PMnav li ul.p7PMshow a*/ {
	background-color: <?php echo BKGD_OVER; ?>;
	font-weight: bold;
	color: <?php echo SUB_TEXT_OVER; ?>;
	border-color: <?php echo ITEM_BKGD_OUT; ?>;
}
#p7PMnav ul a:hover, #p7PMnav ul a:active, #p7PMnav ul a:focus {
	font-weight: bold;
	letter-spacing: .01px;
}
/* ------ the trigger link styles ------- */

/*the normal trigger links */
#p7PMnav ul .p7PMtrg, #p7PMnav ul .p7PMon {
	background-image:  url('<?php echo Media::IMAGE("p7PM/dark_east"); ?>');
	background-repeat:	no-repeat;
	background-position: right center;
}
/* the active trigger link style */
#p7PMnav ul .p7PMon {
	background-color: <?php echo BKGD_OVER; ?>;
	color: <?php echo SUB_TEXT_OVER; ?>;
}
/*the submenu classes */
#p7PMnav .p7PMhide {
	border: 0;
}
#p7PMnav .p7PMshow {
	left: auto;
	z-index: 501;
}
/* Top level menu width */
#p7PMnav li {
	float: left;
	width: 100px;
}
#p7PMnav li.m       { width: 125px; }
#p7PMnav li.l       { width: 150px; }
#p7PMnav li.xl     { width: 175px; }
#p7PMnav li.xxl   { width: 200px; }
#p7PMnav li.xxxl { width: 250px; }

#p7PMnav ul li {
	float: none;
}
/* 2nd and subsequent Submenu widths */
#p7PMnav ul, #p7PMnav ul li {
	width: 172px;
}


<?php
if( Common::isAdminContext() )
{
?>

/* additionnal icons */
#p7PMnav a {
	padding-right: 0;
	background-repeat:	no-repeat;
	background-position: right center;
}

#p7PMnav a.top {
	background-repeat:	no-repeat;
	background-position: 2px center;
	padding-left: 21px;
}

#p7PMnav a.contact { background-image:  url('<?php echo Media::IMAGE("p7PM/contact"); ?>'); }
#p7PMnav a.search { background-image:  url('<?php echo Media::IMAGE("p7PM/search"); ?>'); }
#p7PMnav a.logout { background-image:  url('<?php echo Media::IMAGE("p7PM/logout"); ?>'); }
#p7PMnav a.langs { background-image:  url('<?php echo Media::IMAGE("p7PM/add_lang"); ?>'); }
#p7PMnav a.users { background-image:  url('<?php echo Media::IMAGE("p7PM/add_user"); ?>'); }
#p7PMnav a.stats { background-image:  url('<?php echo Media::IMAGE("p7PM/stats"); ?>'); }
#p7PMnav a.texts { background-image:  url('<?php echo Media::IMAGE("p7PM/texts"); ?>'); }
#p7PMnav a.text { background-image:  url('<?php echo Media::IMAGE("p7PM/text"); ?>'); }
#p7PMnav a.data { background-image:  url('<?php echo Media::IMAGE("p7PM/data"); ?>'); }
#p7PMnav a.help { background-image:  url('<?php echo Media::IMAGE("p7PM/help"); ?>'); }
#p7PMnav a.info { background-image:  url('<?php echo Media::IMAGE("p7PM/info"); ?>'); }
#p7PMnav a.key { background-image:  url('<?php echo Media::IMAGE("p7PM/key"); ?>'); }


<?php
}
else
{
?>
#p7PMnav a.top {
	background: url('<?php echo Media::IMAGE("p7PM/overlay"); ?>') repeat;
	text-shadow: black 2px 2px 2px;
}
#p7PMnav a.top:hover {
	color: black;
	text-shadow: white 2px 2px 2px;
}

#p7PMnav ul a {
	text-shadow: grey 1px 1px 1px;
}

<?php
}

if( Client::isMSIE() )
{
	$version = intval(Client::getBrowserVersion(true));
	if( $version < 6 )
	{
?>
#p7PMnav a { height:1em; }
#p7PMnav li { height:1em; }
#p7PMnav ul li { float: left; clear: both; width: 100%; }
<?php
	}
	elseif ( $version == 6 )
	{
?>
#p7PMnav ul li { clear: none; }
#p7PMnav ul li { float: left; clear: both; width: 100%; }
<?php
	}
	elseif ( $version == 7 )
	{
?>
#p7PMnav a { zoom:100%; }
#p7PMnav ul li { float: left; clear: both; width: 100%; }
<?php
	}
}
?>
