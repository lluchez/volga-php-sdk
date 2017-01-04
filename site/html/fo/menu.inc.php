<?php

/** _________ ACHIEVEMENTS _________ */
$menu_achievements = new Menu(Array('#', 'achievements'), null, 'l');
$menu_progs = $menu_achievements->addChild(new Menu(Array('progs')));
$menu_achievements->addChild(new Menu('sites'));
$menu_achievements->addChild(new Menu('drawings'));
// sub items
foreach( Lang::getAssocArray("SELECT * FROM `prog_category`", 'cID', 'cName') as $key => $val )
	$menu_progs->addChild(new CustomSubMenu(Context::HTML('progs', true, Array('cat_idx' => $key)), $val));

/** _________ Information _________ */
$menu_info = new Menu(Array('#', 'information'));
$menu_info->addChild(new Menu(Array('otherInfo')));
$menu_trips = new Menu(Array('trips'));
$menu_trips->addChild(new CustomSubMenu(Context::HTML('trips', true, Array('country' => '1')), 'Cambodia'));
$menu_trips->addChild(new CustomSubMenu(Context::HTML('trips', true, Array('country' => '2')), 'Thailand'));
$menu_info->addChild($menu_trips);

/** _________ Others _________ */
$menu_others = new Menu(Array('#', 'others'));
$menu_others->addChild(new Menu(Array('tools')));
$menu_others->addChild(new Menu(Array('websites')));

/** _________ Build the menu _________ */
$menu = new RootMenu(false, 'p7PMnav');
$menu->setPadding("\t\t\t\t\t");
$menu->addChild(new Menu(Array('home')));
$menu->addChild($menu_achievements);
$menu->addChild(new Menu(Array('cv')));
$menu->addChild($menu_info);
$menu->addChild($menu_others);



?>
				<!-- ------------------ MENU ------------------ -->
				<div id="menu">
					
<?php
					echo $menu;
?>
						
					<div style="clear: left"></div>
				</div>
				<!-- --------------- End of MENU --------------- -->

