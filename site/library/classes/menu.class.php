<?php

/******************************
 * Class Menu (for p7popMenu) *
 ******************************/

/**
 * This file contains 2 classes is used to create a menu using p7popMenu pluggin: Menu / RootMenu
 *
 * Here is the list of functions for this class:
 *		- Menu($prop, $filter = null)
 *		  - addChild($child)
 *		  - toString($pad)
 * 
 *		- RootMenu($id = null)
 *      - addChild($child)
 *		  - setPadding($padding)
 *		  - removeNonGrantedItems($grants_table, $children = null)
 *		  - __toString() // to use echo $my_menu (you can use setPadding() to set left spacer)
 *		  - toString($pad = null)
 *
 *
 * @desc-start
 * This class is used to add sub-items menu working with the JS/CSS component: p7popMenu.
 * Do not use this class if you do not used localized texts or multi langual feature is turned off. See RootMenu class to know more.
 * Items have this specificities (given in the Array $prop: 1st constructor parameter):
 * - 1st: Link: give the page code key or '#'
 * - 2nd: Text: give a code. Full text key will be computed this way: '(bo/site).(/s)menu.{$text}'. _
 *         bo/site depends of Back Office/Front Office. _
 *         's' is added if it's not a top item menu.
 * - 3rd: Classes: can be a string (will be exploded on ' '), an array. If null, will be the a copy of the 2nd parameter
 * @desc-end
 */



 
class Menu
{

	protected $children = Array()/* Array */, $properties = Array() /* Array */, $grants_key = null /* String */, $level = -1;
	
	static $sizesScale = Array(9, 14, 23, 28, 35);
	static $sizes = Array(null, 'm', 'l', 'xl', 'xxl', 'xxxl');
	
	// Constructor: create a sub-item that will be added to the top menu
	// @param $prop Array containing up to 3 values: keyLink, keyText, classes. If one is missing the value is the one with index n-1. Can be a string that will be duplicated for each key
	// @param $filter key for the permissions array given as parameter when displaying the full menu
	public function Menu($prop, $filter = null)
	{
		$this->grants_key = $filter;    // key of the grants array used to remove it (if access forbidden)
		$p = is_string($prop) ? Array($prop) : $prop;
		list($link, $text, $class) = $p;
		if( is_null($text) )  $text = $link;
		
		$this->properties['keyLink'] = $link;
		$this->properties['keyText'] = $text;
		$this->properties['classes'] = $this->getClasses($class, $text);
	}
	
	// Change the scale of sizes used to change the size of top menus
	// @param $sizes Array containing sizes (5 increasing integers) corresponding to the threshold to switch to longer tabs
	public static function setScaleSizes($sizes)
	{
		if( is_array($sizes) && (count($sizes)==5) )
			self::$sizesScale = $sizes;
	}
	
	// Compute the classes
	// @param $class 3rd key of properties
	// @param $is_null default value if this 3rd value is empty (casted an an array)
	// @return Returns an array containing the classes
	protected function getClasses($class, $is_null)
	{
		if( is_null($class) )
			return is_array($is_null) ? $is_null : Array($is_null);
		elseif( is_array($class) )
			return $class;
		elseif( is_string($class) && $class )
			return explode(' ', trim($class));
		return Array();
	}
	
	// Set the text of this item menu
	// @param $text new text
	public function setText($text)
	{
		$this->properties['text'] = $text;
	}
	
	// @param $admin (bool) Define if we are in the admin context
	// @return Returns the item link
	private function getText($admin = true)
	{
		$keyText = ($admin ? 'bo.' : 'site.').($this->level ? 's' : '')."menu.".$this->properties['keyText'];
		if( ! $this->properties['text'] )
			$this->properties['text'] = Lang::translate($keyText);
		return $this->properties['text'];
	}
	
	// Set the link of this item menu
	// @param $text new link
	public function setLink($link)
	{
		$this->properties['link'] = $link;
	}
	
	// @return Returns the item link
	public function getLink()
	{
		if( $this->properties['link'] )
			return $this->properties['link'];
		elseif( $this->properties['keyLink'] == '#' )
			return '#';
		else
			return Context::HTML($this->properties['keyLink'], true);
	}
	
	// Add a new class to this item
	// @param $class CSS Class to add
	public function addClass($class)
	{
		$this->properties['classes'][] = $class;
	}
	
	// Add a child to the current menu and change the child and child's children items level
	// @param $child Child created by Menu()
	// @return Returns the child itself. Hence you can do: '$item = $menu->addChild(new Menu(...))' and then add subitems to $item
	public function addChild($child)
	{
		$this->children[] = $child;
		$child->setLevel($this->level+1);
		return $child;
	}
	
	// Change the level of the item within the tree structure and all his sub-items.
	// Default level is -1: the root. First item (top items) are level 0.
	// @param $level New level
	protected function setLevel($level)
	{
		$this->level = $level;
		foreach($this->children as $child)
			$child->setLevel($level+1);
	}
	

	// Return a string representation of the menu (ready to be displayed by 'echo')
	// @param $level Item level (starts at 0 for top items)
	// @param $admin If admin context
	// @param $pad Left padding
	// @return Returns the menu as a string
	public function toString($admin, $pad)
	{
		$classLI = $this->level ? null : $this->getMenuSize($admin);
		$o ="$pad<li". ($classLI ? " class='$classLI'" : ''). ">";
		
		if( count($this->children) == 0 ) // no child
			return $o.$this->createLink($admin)."</li>\n";
		
		$o .= "\n$pad\t".$this->createLink($admin)."\n$pad\t<ul>\n";
		foreach($this->children as $child)
			$o .= $child->toString($admin, "$pad\t\t");
		return "$o$pad\t</ul>\n$pad</li>\n";
	}
	
	// Create the item link <a />
	// @param $admin If admin context
	// @return Returns the corresponding link
	private function createLink($admin)
	{
		$classes = $this->properties['classes'];
		if( count($this->children) && $this->level )
			$classes[] = 'p7PMon';
		if( $this->level == 0 )
			$classes[] = 'top';
		return "<a href=\"".$this->getLink()."\" class='".implode(' ', $classes)."'>".$this->getText($admin)."</a>";
	}
	
	// Check this text length and retrieve the size index, which will be converted into a classname
	// @param $text Text to get index from
	// @return Returns the corresponding index: -1 for small text, up to 4
	private function getSizeIndexForText($text)
	{
		$len = strlen($text);
		foreach(self::$sizesScale as $i => $size)
			if( $len <= $size )
				return $i-1;
		return $i;
	}
	
	// Return the menu size for the <li />. Only called for level zero items.
	// @param $admin Is admin context ?
	// @return Returns null or the CSS className
	private function getMenuSize($admin)
	{
		$size = $this->getSizeIndexForText($this->getText($admin));
		//foreach($this->children as $child)
		//	$size = Common::max($size, $this->getSizeIndexForText($child->getText($admin, true)));
		return self::$sizes[$size+1];
	}
}


/** ---------------------- End of Class ---------------------- */

/**
 * @desc-start
 * This class is used to add sub-items menu working with the JS/CSS component: p7popMenu.
 * It extends Menu.
 * Menu default behaviour uses keys define texts and links. Here we directly gives the right text/link.
 * This class has not been designed to be an level zero item, so it wont't probably work fine.
 * @desc-end
 */

class CustomSubMenu extends Menu
{
	// Constructor: create a sub-item that will be added to the top menu
	// @param $link Item link
	// @param $text Item text
	// @param $classes classes to add to this link (can be a string or an array)
	// @param $filter key for the permissions array given as parameter when displaying the full menu
	public function CustomSubMenu($link, $text, $classes = null, $filter = null)
	{
		$this->properties['link'] = $link;
		$this->properties['text'] = $text;
		$this->properties['classes'] = $this->getClasses($classes, Array()); // is_null($classes) ? Array() : (is_string($classes) ? explode(' ', trim($classes)) :  $classes);
		$this->grants_key = $filter;    // key of the grants array used to remove it (if access forbidden)
	}
	
	
	// Create the item link <a />
	// @param $admin If admin context
	// @return Returns the corresponding link
	private function createLink()
	{
		return "<a href=\"".$this->properties['link']."\" class='".implode(' ', $this->properties['classes'])."'>".$this->properties['text']."</a>";
	}
}





/** ---------------------- End of Class ---------------------- */

/**
 * @desc-start
 * This class is used to create a menu working with the JS/CSS component: p7popMenu.
 * See Menu class to know more and add children
 * @desc-end
 */

define('MENU_ID', 'p7PMnav');


class RootMenu extends Menu
{
	private $id, $padding = '', $admin;
	
	// Constructor
	// @param $id Menu ID (for HTML display). Default value is: MENU_ID
	public function RootMenu($adminContext, $id = null)
	{
		$this->id = is_null($id) ? MENU_ID : $id;
		$this->admin = (bool)$adminContext;
	}
	
	// Define left padding
	// @param $padding Left padding for display
	public function setPadding($padding)
	{
		$this->padding = $padding;
	}
	
	// Maybe you do not want some items to be displayed according to granted access.
	// Hence you have to provide an assoc-array defined which sections are restricted. _
	// As you have gave grant keys to your subitems, if the according privilege is false, _
	// the subitem won't be displayed. Use it before displaying the menu.
	// [code]
	// $menu = new RootMenu(...); 
	// // add sub items
	// $menu->addChild(mew Menu(Array('list'), 'list_grant', ));
	// $grants = Array('list_grant' => ! Common::isLocalContext());
	// $menu->removeNonGrantedItems($grants);
	// // remove the item 'list' if we are in a local context
	// [/code]
	// @param $grants_table Assoc-array defined which sections are restricted
	// @param $children Used for recursivity. Let it empty (or null value)
	public function removeNonGrantedItems($grants_table = Array(), &$children = null)
	{
		global $cpt;
		if( is_array($grants_table) && count($grants_table) )
		{
			if( is_null($children) )
				$children = &$this->children;
			foreach($children as $i => $child)
			{
				$key = $child->grants_key;
				if( (is_null($key) || (is_string($key) && (! isset($grants_table[$key]) || $grants_table[$key]) ) ) && 
						( $this->removeNonGrantedItems($grants_table, $children[$i]->children) || $child->getLink() != '#' ) )
				{ /* Do Nothing */ }
				else
					unset($children[$i]);
			}
			return count($children) > 0;
		}
	}
	
	// Overrides the __toString() function
	public function __toString() { return $this->toString(); }
	
	// Return a string representation of the menu
	// @param $pad Lef padding for indentation
	// Return the manu as a string
	public function toString($pad = null)
	{
		if( ! is_string($pad) )
			$pad = $this->padding;
		
		$o = "$pad<ul id=\"$this->id\">\n\n";
		foreach($this->children as $child)
			$o .= $child->toString($this->admin, "$pad\t");
		$o .= "\n$pad</ul>\n\n";
		return $o;
	}
}

?>