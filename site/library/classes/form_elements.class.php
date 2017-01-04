<?php

/**
 * These classes have been designed to simply create end-user forms.
 * You can then create a from, add fieldsets, add inputs/select/textarea...
 *
 * This file contains the following classes:
 *  - InputField: static class to simple handle name/ID and avoid duplication of code
 *  - Input:      <input type=text' />, 
 *  - Textarea:   <textarea></textarea>
 *  - Select:     <select> <option /><option /> </select>
 *  - Radio:      set of <input type='radio' />
 *  - Checkbox:   set of <input type='checkbox' />
 */



/************************************************
 *           Static Class InputField            *
 ************************************************/

class InputField
{
	protected $properties;
	// private static function getID_($input) { if( preg_replace("/ id=['\"]?([a-z0-9_\-]+)/i", $input, $val) ) return $val[1]; }
	// private static function getName_($input) { if( preg_replace("/ name=['\"]?([a-z0-9_\-]+)/i", $input, $val) ) return $val[1]; }
	
	public function getID() { return $this->properties['id']; }
	public function getName() { return $this->properties['name']; }
	public function getType() { return 'undefined'; }
	public function isPassword() { return false; }
	public function isFile() { return false; }
}

/** ---------------------- End of Class ---------------------- */



/************************************************
 *                 Class Input                  *
 ************************************************/

class Input extends InputField
{
	private static $default_props = Array('type' => 'text');

	public function Input($array = null)
	{
		if( is_null($array) )
			$array = Array();
		elseif( is_string($array) )
			$array = Array('id' => $array);
		$array = $array + Input::$default_props;
		if( !array_key_exists('name', $array) && $id = $array['id'] )
			$array['name'] = $id;
		if( !array_key_exists('id', $array) && $name = $array['name'] )
			$array['id'] = $name;
		if( ! Form::$usesCSS3Features )
			$array['class'] .= $array['class'] ? (' '."type_".$array['type']) : ("type_".$array['type']);
		$this->properties = $array;
	}
	
	public function getType() { return 'input'; }
	public function isPassword() { return $this->properties['type'] == 'password'; }
	public function isFile() { return $this->properties['type'] == 'file'; }
	
	public function toString($tab = '')
	{
		if( is_array($this->properties) )
		{
			$array = $this->properties;
			$text = null;
			if( array_key_exists('init', $array) && ! strstr('checkbox|radio', strtolower($array['type'])) )
			{
				$array['value'] = $array['init'];
				unset($array['init']);
			}
			if( Form::$retrieveGetValues && array_key_exists('name', $array) && Vars::defined($array['name']) )
				$array['value'] = Vars::get($array['name']);
			if( array_key_exists('value', $array) && strstr('checkbox|radio', strtolower($array['type'])) )
			{
				$array['checked'] = 'checked';
				$array['value'] = 'on';
			}
			if( array_key_exists('text', $array) )
			{
				$text = "\n$tab<label";
				if( $id = $this->getID() )
					$text .= " for=\"$id\"";
				$text .= ">".$array['text']."</label>";
				unset($array['text']);
			}
			$out = "$tab<input";
			foreach( $array as $key => $val )
				$out .= " $key=\"".String::toJS($val)."\"";
			return "$out />$text";
		}
	}
}

/** ---------------------- End of Class ---------------------- */



/************************************************
 *                Class Textarea                *
 ************************************************/

class Textarea extends InputField
{
	public function Textarea($array = null)
	{
		if( is_null($array) )
			$array = Array();
		elseif( is_string($array) )
			$array = Array('id' => $array);
		if( !array_key_exists('name', $array) && $id = $array['id'] )
			$array['name'] = $id;
		if( !array_key_exists('id', $array) && $name = $array['name'] )
			$array['id'] = $name;
		if( array_key_exists('rows', $array) )
			$array['style'] = 'height: auto; '.$array['style'];
		if( array_key_exists('cols', $array) )
			$array['style'] = 'width: auto; '.$array['style'];
		$this->properties = $array;
	}

	public function getType() { return 'textarea'; }
	
	public function toString($tab = '')
	{
		if( is_array($this->properties) )
		{
			$array = $this->properties;
			if( array_key_exists('init', $array) )
			{
				$array['value'] = $array['init'];
				unset($array['init']);
			}
			if( Form::$retrieveGetValues && array_key_exists('name', $array) && Vars::defined($array['name']) )
				$array['value'] = Vars::get($array['name']);
			$value = $array['value'];
			unset($array['value']);
			$out = "$tab<textarea";
			foreach( $array as $key => $val )
				$out .= " $key=\"".String::toJS($val)."\"";
			return "$out>".String::decode($value)."</textarea>";
		}
	}
}

/** ---------------------- End of Class ---------------------- */



/************************************************
 *                 Class Select                 *
 ************************************************/
 
class Select extends InputField
{
	private $values; // array('id1' => 'val1', ...);
	
	public function Select($values, $array = null, $default = null)
	{
		if( is_null($array) )
			$array = Array();
		elseif( is_string($array) )
			$array = Array('id' => $array);
		if( !array_key_exists('name', $array) && $id = $array['id'] )
			$array['name'] = $id;
		if( !array_key_exists('id', $array) && $name = $array['name'] )
			$array['id'] = $name;
		if( !array_key_exists('value', $array) && !is_null($default) )
			$array['value'] = $default;
		$this->properties = $array;
		$this->values     = $values;
	}

	public function getType() { return 'select'; }
	
	public function toString($tab = '')
	{
		if( is_array($this->properties) )
		{
			$array = $this->properties;
			$default = null;
			if( array_key_exists('init', $array) )
			{
				$array['value'] = $array['init'];
				unset($array['init']);
			}
			if( Form::$retrieveGetValues && array_key_exists('name', $array) && Vars::defined($array['name']) )
				$array['value'] = Vars::get($array['name']);
			if( isset($array['value']) )
			{
				$default = $array['value'];
				unset($array['value']);
			}
			$out = "$tab<select";
			foreach( $array as $key => $val )
				$out .= " $key=\"".String::toJS($val)."\"";
			$out .= ">\n";
			$i = 0;
			foreach( $this->values as $key => $val )
			{
				$out .= "$tab\t<option value='$key'";
				if( (is_int($default) && $i++ == $default) || (! is_int($default) && $key == $default) )
					$out .= " selected=\"selected\"";
				$out .= ">".String::toJS($val, false)."</option>\n";
			}
			return "$out$tab</select>";
		}
	}
}

/** ---------------------- End of Class ---------------------- */


/************************************************
 *                 Class Radio                  *
 ************************************************/
 
class Radio extends InputField
{
	private $values; // array('id1' => 'val1', ...);
	private $idBasedOnKey = true; // true: {name}_{id}, false: {name}_{1/2/3}
	
	public function Radio($name, $values, $array = null, $default = null)
	{
		if( is_null($array) )
			$array = Array();
		if( array_key_exists('IDBasedOnKey', $array) )
			$this->idBasedOnKey = $array['IDBasedOnKey'];
		elseif( array_key_exists('useID', $array) )
			$this->idBasedOnKey = $array['useID'];
		unset($array['IDBasedOnKey']);
		unset($array['useID']);
		$array['name'] = $name;
		if( !array_key_exists('value', $array) && ! is_null($default) )
			$array['value'] = $default;
		$this->properties = $array;
		$this->values     = $values;
	}

	public function getType() { return 'radio'; }
	public function getID()   { return null; }
	public function getName() { return null; }
	
	public function toString($tab = '')
	{
		if( is_array($this->properties) )
		{
			$array = $this->properties;
			$default = $this->default;
			if( array_key_exists('init', $array) )
			{
				$array['value'] = $array['init'];
				unset($array['init']);
			}
			if( Form::$retrieveGetValues && Vars::defined($array['name']) )
				$array['value'] = Vars::get($array['name']);
			if( isset($array['value']) )
			{
				$default = $array['value'];
				unset($array['value']);
			}
			$commonProperties = '';
			foreach( $array as $key => $val )
				$commonProperties .= " $key=\"".String::toJS($val)."\"";
			
			$out = "$tab<div>\n";
			$baseID = $array['name'].'_';
			$i = 0;
			foreach( $this->values as $key => $val )
			{
				$properties = "$commonProperties value=\"$key\"";
				$out .= "$tab\t<p><input type=\"radio\" id=\"";
				$id = $baseID. ( $this->idBasedOnKey ? $key : $i++ );
				if( (is_int($default) && $i++ == $default) || (! is_int($default) && $key == $default) )
					$properties .= " checked=\"checked\"";
				$out .= "$id\"$properties /><label for=\"$id\">".String::toText($val)."</label></p>\n";
			}
			return "$out$tab</div>";
		}
	}
}

/** ---------------------- End of Class ---------------------- */


/************************************************
 *                Class Checkbox                *
 ************************************************/
 
class Checkbox extends InputField
{
	private $values; // array('id1' => 'val1', ...);
	private $idBasedOnKey = true; // true: {name}_{id}, false: {name}_{1/2/3}
	
	public function Checkbox($name, $values, $array = null, $default = null)
	{
		if( is_null($array) )
			$array = Array();
		if( array_key_exists('IDBasedOnKey', $array) )
			$this->idBasedOnKey = $array['IDBasedOnKey'];
		elseif( array_key_exists('useID', $array) )
			$this->idBasedOnKey = $array['useID'];
		unset($array['IDBasedOnKey']);
		unset($array['useID']);
		$array['name'] = $name;
		if( !array_key_exists('value', $array) && is_string($default) )
			$array['value'] = $default;
		elseif( is_array($default) )
			$array['value'] = explode(';',$default);
		$this->properties = $array;
		$this->values     = $values;
	}

	public function getType() { return 'radio'; }
	public function getID()   { return null; }
	public function getName() { return null; }
	
	public function toString($tab = '')
	{
		if( is_array($this->properties) )
		{
			$array = $this->properties;
			// compute the initial/default value
			$default = $this->default;
			if( array_key_exists('init', $array) )
			{
				$vals = $array['init'];
				$array['value'] = ( is_array($vals) ) ? implode(';', $vals) : $vals;
				unset($array['init']);
			}
			if( Form::$retrieveGetValues && Vars::defined($array['name']) )
			{
				$vals = Vars::get($array['name']);
				$array['value'] = ( is_array($vals) ) ? implode(';', $vals) : $vals;
			}
			if( isset($array['value']) )
			{
				$default = $array['value'];
				unset($array['value']);
			}
			$default = ";$default;";
			$baseID = $array['name'].'_';
			$array['name'] .= '[]';
			$commonProperties = '';
			foreach( $array as $key => $val )
				$commonProperties .= " $key=\"".String::toJS($val)."\"";
			
			$out = "$tab<div>\n";
			$i = 0;
			foreach( $this->values as $key => $val )
			{
				$properties = "$commonProperties value=\"$key\"";
				$out .= "$tab\t<p><input type=\"checkbox\" id=\"";
				$id = $baseID. ( $this->idBasedOnKey ? $key : $i++ );
				if( (is_int($default) && $i++ == $default) || (is_string($default) && strstr($default, ";$key;")) )
					$properties .= " checked=\"checked\"";
				$out .= "$id\"$properties /><label for=\"$id\">".String::toText($val)."</label></p>\n";
			}
			return "$out$tab</div>";
		}
	}
}

?>