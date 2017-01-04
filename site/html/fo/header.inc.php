		<div id="frame">
			
			<div id="spirales"></div>
			<div id="left-side"></div>
			
			<div id="viewport">
				<div id="title">
					<ul id="set_language">
						<li><label><?php echo Lang::translate('change_language'); ?>: </label></li>
<?php
				foreach(Lang::getOtherAvailableLanguages() as $langCode)
					echo "\t\t\t\t\t\t<li class='flag'><a href='".Lang::changeLanguageLink($langCode)."'><img src='".Lang::IMAGE('flag', null, $langCode)."' title='".Lang::translate("lang_$langCode")."' /></a></li>\n";
?>
					</ul>
<?php
	include DIR_PHP.'menu.inc.php';
?>
				
				</div>
<?php
		Head::generateErrorPanel("\t\t\t\t");
?>
				<div id="sub-content">
					
					<div class="top-content"></div>
					
					<div class="middle-content">
						<div id="content">

<!-------- Beginning of the main content -------->

<?php
	if( ! preg_match("#^(info|api|home)$#", Context::getPageCode()) )
		echo "<h1>".Context::getPageName()."</h1>\n";
?>
