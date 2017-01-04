<?php

/**
 * These classes have been designed to simply create end-user forms.
 * You can then create a from, add fieldsets, add inputs/select/textarea...
 *
 * This main file contains several classes: Form/Fieldset/Element
 *  - Forms: contains everything and the main properties of a standard <form>
 *      It also contains the header (with a title) and the footer (buttons reset/submit)
 *  - Fieldset: it's basically a <fieldset> and then you can add Elements to it
 *  - Element: It's a set composed on the label and the input/field element
 *
 * Here is the structure:
 *  - Form (containing a header with a title)
 *    - Fieldset
 *      - Element
 *        - <label />
 *        - Input/Textarea/Select/Radio/Checkbox
 *      - Element
 *        - <label />
 *        - Input/Textarea/Select/Radio/Checkbox
 *    - Fieldset
 *      - Element
 *        - <label />
 *        - Input/Textarea/Select/Radio/Checkbox
 *    - Footer (automatically created if not empty)
 *      - <input type='reset' />
 *      - <input type='submit' />
 *
 *  \Form Methods
 *		- Form::init($usesCSS3, $text_required, [$retrieveGetValues])
 *		- Form::useGetVales($retrieveGetValues)
 *
 *		- Form($title, [$width = '', $properties = Array(), $padding = 0, $classes = Array()])
 *		- setId($id, $setNameToo = false)
 *		- setPadding($value)
 *		- addClassName()
 *
 *		- addFieldset($title, $p = Array())
 *		- addButton($p = Array())
 *		- addHiddenField($properties)
 *
 *  \Fieldset Methods
 *		- Fieldset($title, $elements = null)
 *		- addElement($element)
 *
 *  \Element Methods
 *		- Element($field, $title, $subtitle = '', $required = false)
 *
 * See 'form_elements.class.php' for further details on the field classes: Input/Textarea/Select/Radio/Checkbox
 */

 

/**
 * @desc-start
 * This class helps you to easily create forms (<form />) for your Website.
 * Instead of creating an HTML structure you can thanks to these OOP classes create a form with only few lines.
 * See the other related classes to know more: Fieldset, Element, Input, Textarea, Select, Radio, Checkbox.
 * @desc-end
 */
 

/************************************************
 *                  Class Form                  *
 ************************************************/
 
class Form
{
	// static members
	public static $usesCSS3Features = null, $textRequiredField = null, $retrieveGetValues = true;
	private static $submitName = 'submit';
	
	private static $sizes = Array('-' => 'small', '' => 'medium', '+' => 'large', '++' => 'xlarge');
	
	// private members
	private $title, $properties, $padding = '', $classes;
	private $fieldsets = Array(), $hiddenInputs = Array(), $submitButtons = Array();

	
	/**********************
	 *   Static methods   *
	 **********************/
	
	public static function init($usesCSS3, $text_required, $retrieveGetValues = true)
	{
		self::$usesCSS3Features  = $usesCSS3; // advanced display of the form
		self::$textRequiredField = $text_required; // text displayed to indicate that the text is mandatory/required
		self::$retrieveGetValues = $retrieveGetValues; // try to use the value of the <input> from $_GET array
	}
	
	// use it before displaying the form to change the rendering mode
	public static function useGetVales($retrieveGetValues)
	{
		self::$retrieveGetValues = $retrieveGetValues;
	}
	
	
	/**********************
	 *   Init functions   *
	 **********************/
	
	// for $width, uses '-', '', '+' or '++'
	// $properties: assoc-array for 'method', 'id', 'name', 'action', 'enctype', etc...
	public function Form($title, $width = '', $properties = Array(), $padding = 0, $classes = Array())
	{
		$this->title = $title;
		$properties = Common::notNull($properties, Array());
		$classes    = Common::notNull($classes, Array());
		$settings = Array('method' => 'post', 'action' => Context::HTML());
		$this->properties = $properties + $settings;
		$this->classes = $classes;
		$width = Common::notNull(self::$sizes[$width], $width);
		$this->addClassName($width);
		$this->setPadding($padding);
		$this->addClassName(self::$usesCSS3Features ? 'withCss3' : 'noCss3');
	}
	
	// define the ID of the form, and optionally the name
	public function setId($id, $setNameToo = false)
	{
		$this->properties['id'] = $id;
		if( $setNameToo )
		{
			if( is_string($setNameToo) )
				$this->properties['name'] = $setNameToo;
			else
				$this->properties['name'] = $id;
		}
	}
	
	// define left padding to have a clean source code
	public function setPadding($value)
	{
		if( is_int($value) )
			$value = str_pad('', $value, "\t");
		elseif( ! is_string($value) )
			$value = '';
		$this->padding = $value;
	}
	
	// add a className
	public function addClassName()
	{
		foreach(func_get_args() as $key)
			if( $key )
				$this->classes[] = $key;
	}
	
	// set the form's 'action' property (Url to send the data)
	public function setLocation($url)
	{
		$url = strstr($url, '/') ? $url : Context::HTML($url);
		$this->properties['action'] = $url;
	}
	
	/**********************
	 *      functions     *
	 **********************/
	
	// $elements (optional) is an array 
	public function addFieldset($title, $id = null, $elements = Array())
	{
		$field = new Fieldset($title, $id, $elements);
		$this->fieldsets[] = $field;
		return $field;
	}
	
	// add a button to the footer part
	// if is_string($properties), the button will be the submit field with $properties as text
	// if !$properties['type'], default value = 'submit'
	// it creates a 'Input' object (cf form_elements.class.php)
	public function addButton($properties = Array())
	{
		$default = Array('name' => self::$submitName, 'type' => 'submit');
		$props = Array();
		if( is_string($properties) )
			$props = Array( 'value' => $properties );
		elseif( is_array($properties) )
			$props = $properties;
		$button = new Input($default + $props);
		$this->submitButtons[] = $button;
	}
	
	// add an hidden field to the form
	// If $properties is a string, it represents its name. The corresponding value will be the associated $_GET value.
	// Otherwise, $properties is an Array containing the HTML attributes for <input /> creation
	// @param $properties Array or String
	public function addHiddenField($properties)
	{
		if( is_string($properties) )
			$properties = Array( 'name' => $properties, 'value' => Vars::get($properties) );
		$properties['type'] = 'hidden';
		$hidden = new Input($properties);
		$this->hiddenInputs[] = $hidden;
	}
	
	/**********************
	 *  Display function  *
	 **********************/
	 
	public function __toString() { return $this->toString(); }
	
	public function toString()
	{
		$properties = $this->properties;
		if( count($this->classes) )
			$properties['class'] = implode(' ', $this->classes);
		else
			unset($properties['class']);
		$tab = $this->padding;
		$out = "$tab<form";
		
		$elts = Array('pass' => 0, 'file' => 0);
		foreach($this->fieldsets as $fieldset)
		{
			$nb = $fieldset->countSpecialElements();
			$elts['pass'] += $nb['pass'];
			$elts['file'] += $nb['file'];
		}
		if( $elts['file'] )
			$properties = Array('method' => 'post', 'enctype' => 'multipart/form-data') + $properties;
		elseif( $elts['pass'] )
			$properties = Array('method' => 'post') + $properties;
		
		foreach( $properties as $key => $val )
			$out .= " $key=\"".String::toText($val)."\"";
		$out .= ">\n";
		if( ! is_null($this->title) )
			$out .= "$tab\t<div class='header'>$this->title</div>\n";
		foreach($this->hiddenInputs as $hidden)
			$out .= $hidden->toString("$tab\t") . "\n";
		foreach($this->fieldsets as $fieldset)
			$out .= $fieldset->toString("$tab\t");
		if( count($this->submitButtons) )
		{
			$out .= "$tab\t<div class='footer'>\n";
			foreach($this->submitButtons as $button)
				$out .= $button->toString("$tab\t\t") . "\n";
			$out .= "$tab\t</div>\n";
		}
		$out .= "$tab</form>\n";
		return $out;
	}
}

/** ---------------------- End of Class ---------------------- */



/************************************************
 *                Class Fieldset                *
 ************************************************/


class Fieldset
{
	private $title, $elements = Array(), $id = null;
	
	
	public function Fieldset($title, $id = null, $elements = null)
	{
		$this->title = $title;
		$this->id    = $id;
		if( is_array($elements) )
			foreach($elements as $elt)
				$this->addElement($elt);
	}
	
	public function addElement($element, $className = null)
	{
		if( ! is_null($className) )
			$element->addClasses($className);
		$this->elements[] = $element;
	}
	
	public function countSpecialElements()
	{
		$ret = Array('pass' => 0, 'file' => 0);
		foreach($this->elements as $elt)
		{
			if( $elt->isPassword() ) $ret['pass']++;
			if( $elt->isFile() )     $ret['file']++;
		}
		return $ret;
	}
	
	public function toString($tab = '')
	{
		$id = $this->id ? " id='$this->id'" : '';
		$out = "$tab<fieldset$id>\n$tab\t<legend>$this->title</legend>\n";
		$tab2 = $tab;
		if( ! Form::$usesCSS3Features )
		{
			$tab2 .= "\t";
			$out .= "$tab2<table class='form'>\n";
		}
		foreach($this->elements as $elt)
			$out .= $elt->toString($tab2."\t");
		if( ! Form::$usesCSS3Features )
			$out .= "$tab2</table>\n";
		$out .= "$tab</fieldset>\n";
		return $out;
	}
}

/** ---------------------- End of Class ---------------------- */



/************************************************
 *                Class Element                 *
 ************************************************/

class Element
{
	private $field;
	private $title, $subtitle, $required, $visible = true, $classes = Array();
	
	// field is an input/field: Input/Textarea/Select/...
	public function Element($field, $title, $subtitle = '', $required = false)
	{
		$this->field    = $field;
		$this->title    = $title;
		$this->subtitle = $subtitle;
		$this->required = $required;
	}
	
	public function isPassword() { return $this->field->isPassword(); }
	public function isFile()     { return $this->field->isFile(); }
	
	public function hide()
	{
		$this->visible = false;
	}
	
	public function addClasses()
	{
		foreach(func_get_args() as $key)
			$this->classes[] = $key;
	}
	
	public function toString($tab = '')
	{
		$classes = count($this->classes) ? (' class="'.implode(' ', $this->classes).'"') : '';
		$style = $this->visible ? '' : ' style="display: none;"';
		$rowID = 'row_'.$this->field->getID();
		if( Form::$usesCSS3Features )
			{ $tab2 = $tab; $nl = ''; $out = "$tab<div id='$rowID'$classes$style>\n"; }
		else
			{ $tab2 = "$tab\t"; $nl = "<br />"; $out = "$tab<tr id='$rowID'$style>\n$tab2<td class='label'>\n"; }
		$id = $this->field->getID();
		$out .= "$tab2\t<label".($id ? " for=\"$id\"" : '').">$this->title"
		     . ($this->subtitle ? ("$nl<span>".$this->subtitle.'</span>') : '')."</label>\n";
		if( ! Form::$usesCSS3Features )
			$out .= "$tab2</td>\n$tab2<td class='element'>\n";
		$out .= is_string($this->field) ? "$tab2\t$this->field\n" : ($this->field->toString("$tab2\t")."\n");
		if( $this->required )
			$out .= "$tab2\t<span class='required' title=\"".Form::$textRequiredField."\">*</span>\n";
		return $out.(Form::$usesCSS3Features ? "$tab</div>" : "$tab2</td>\n$tab</tr>")."\n";
	}
}

include DIR_CLASSES . 'form_elements.class.php';

?>