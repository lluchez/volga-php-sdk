		<div id="frame">
			<div id="banner">
				<ul id="set_language">
					<li><label><?php echo Lang::translate('change_language'); ?>: </label></li>
<?php
				foreach(Lang::getOtherAvailableLanguages() as $langCode)
				//foreach(Lang::getAvailableLanguages() as $langCode)
					echo "\t\t\t\t\t<li class='flag'><a href='".Lang::changeLanguageLink($langCode)."'><img src='".Lang::IMAGE('flag', null, $langCode)."' title='".Lang::translate("lang_$langCode")."' /></a></li>\n";
?>
				</ul>
<?php
			include DIR_PHP."menu.inc.php";
?>
			</div>



			<div id="body">
<?php
		Head::generateErrorPanel("\t\t\t\t");
?>

<!-------- Beginning of the main content -------->


