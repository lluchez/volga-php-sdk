<?php

/** --------------- Access Restriction --------------- */
$superAdmin = Authentication::isSuperAdmin();
$checks = $superAdmin ? Array() : Array
(
	'editTexts' =>  Authentication::getPrivileges('editTexts'),
	'addTexts' =>  Authentication::getPrivileges('addTexts'),
	'delTexts' =>  Authentication::getPrivileges('delTexts'),
	'editData' =>  Authentication::getPrivileges('editData'),
	'editAccount' => Authentication::getPrivileges('editAccount'),
	'langs' => $superAdmin
);
//Common::print_r($checks); die();
/** --------------- Access Restriction --------------- */

$menu_txt = new Menu(Array('#', 'datatext', 'text'), null, 'l');
$menu_txt->addChild(new Menu(Array('texts'), 'editTexts'));
$menu_txt->addChild(new Menu(Array('data'), 'editData'));

$menu_admin = new Menu(Array('#', 'adm_menu', 'key'), null, 'm');
$menu_admin->addChild(new Menu(Array('users'), 'editAccount'));
$menu_admin->addChild(new Menu(Array('langs'), 'langs'));

$menu_info = new Menu(Array('#', 'information', 'info'), null, 'm');
$menu_info->addChild(new Menu(Array('stats', 'cnx', 'stats')));
$menu_info->addChild(new Menu(Array('help')));
$menu_info->addChild(new Menu(Array('contact')));

$menu = new RootMenu(true, 'p7PMnav');
$menu->setPadding("\t\t\t\t");
$menu->addChild($menu_txt);
$menu->addChild($menu_admin);
$menu->addChild($menu_info);
$menu->addChild(new Menu('logout', null, 'm'));

$menu->removeNonGrantedItems($checks);
?>
			<!-- ------------------ MENU ------------------ -->
			<div id="menu">
				
<?php
				echo $menu;
?>
					
				<div style="clear: left"></div>
			</div>
			<!-- --------------- End of MENU --------------- -->
