<?php

/******************************
 *        Class String        *
 ******************************/

/**
 * @desc-start
 * This class is a static class. Hence no instance is needed and you can
 *   directly calls functions by prefixing them with 'String::'
 * @desc-end
 *
 * Here is the list of functions:
 *	- String::clean($string)
 *	- String::safeSQL($string)
 *	- String::fixeNewLineChar($string)
 *	- String::cleanFilename($filename)
 *
 *	- String::isValidEmail($email)
 *
 *	- String::toJS($text)
 *	- String::toText($text)
 *	- String::convert($text, $type)
 *	- String::decode($text)
 *
 *	- String::setQuantity($text, $quantity, $qPattern = '%')
 */






class String
{
	// Keys for the types of resources. 
	// WARNING!!! Same names as used in DB
	const TEXT_TYPE_HTML =  'html';
	const TEXT_TYPE_TEXT =  'text';
	const TEXT_TYPE_JS =    'js';
	const TEXT_TYPE_INT =   'int';
	const TEXT_TYPE_FLOAT = 'float';
	
	// Remove useless spaces and force the newline char as '\n'
	public static function clean($string)
	{
		$string = trim($string);
		$string = str_replace("\r", substr_count($string, "\n") ? '' : "\n", $string);
		$string = preg_replace("/( +)/", " ", $string);
		return $string;
	}
	
	// Clean the string to be used in SQL commands
	public static function safeSQL($string)
	{
		return mysql_real_escape_string(self::clean($string));
		//return addslashes(self::clean($string));
	}
	
	// replace every \r by null or by \n
	public static function fixeNewLineChar($string)
	{
		return str_replace('\r', (strstr($string, '\n') ? '' : '\n'), $string);
	}
	
	// add nb \t at the beginning of each line
	function addTabPadding($string, $nb)
	{
		$padding = str_pad('', $nb, "\t");
		return $padding . implode("\n".$padding, explode("\n", self::fixeNewLineChar($string)));
	}
	
	// converts accents and remove quotes
	public static function cleanFilename($filename)
	{
		$filename = strtr($filename, 'àáâãäåòóôõöøèéêëçìíîïùúûüÿñ', 'aaaaaaooooooeeeeciiiiuuuuyn'); 
		//$filename = str_replace(' ', '_', $filename);
		$filename = str_replace("'", '', $filename);
		$filename = str_replace('"', '', $filename);
		return $filename;
	}
	
	// Check if the $email looks valid
	public static function isValidEmail($email)
	{
		return preg_match("/^([^@])+@[A-Za-z0-9\-_\.]+\.[a-z]{2,5}$/i", $email);
	}
	
	// escpae string to be included withing JS script (JS text)
	public static function toJS($text, $use_decode_fct = true)
	{
		$text = addcslashes($text, "\n\r\t\\\"'");
		//$text = str_replace("<", "&lt;", $text); // for inclusion in HTML
		//$text = str_replace(">", "&gt;", $text);
		return $use_decode_fct ? self::decode($text) : $text;
	}
	
	public static function toText($text, $use_decode_fct = true)
	{
		$text = str_replace("\n", "<br />", htmlspecialchars($text));
		return $use_decode_fct ? self::decode($text) : $text;
	}
	
	public static function toHTML($text, $use_decode_fct = true)
	{
		return $use_decode_fct ? self::decode($text) : $text;
	}
	
	// convert a text with the associated type method
	public static function convert($text, $type, $use_decode_fct = true)
	{
		$text = self::fixeNewLineChar($text);
		switch( $type )
		{
			case self::TEXT_TYPE_TEXT:
				$text = self::toText($text, $use_decode_fct);
				break;
			
			case self::TEXT_TYPE_JS:
				$text = self::toJS($text, $use_decode_fct);
				break;
				
			default: //(self::TEXT_TYPE_HTML, self::TEXT_TYPE_INT, self::TEXT_TYPE_FLOAT)
				$text = $use_decode_fct ? self::decode($text) : $text;
		}
		return $text;
	}
	
	
	// convert a text with the associated type method
	public static function decode($text)
	{
		if( preg_match("#UTF-8#i", ENCTYPE_CHARSET) )
			return utf8_encode($text);
		return $text;
	}
	
	
	// Make easier case when a message to display is supporting a singular/plural text
	// The lang key referred should be well formated: "(q{digit}:{text_none}|{text_singular}|{text_plural})" where _
	//  {digit} contains only numbers (set a different number for each pattern), _
	//  {text_none} is the none-text to replace (do not use '|' in it), _
	//  {text_singular} is the singular text to replace (do not use '|' or ')' in it) and _
	//  {text_plural} is the plural text to replace (do not use ')' in it).
	// If you do not want to handle the Zero-case, use this pattern instead: "(q{digit}:{text_singular}|{text_plural})"
	// @param $key Language key for translation/localization
	// @param $quantity Number of items for this text. If $quantity is negative same behaviour as if equals to 1 (one)
	// @param $qPattern Quantity pattern (String) used in the localized text that will be remplaced by the quantity
	// @return Returns the localized text converted into singular/plural form if ressource well formated.
	public static function setQuantity($text, $quantity, $qPattern = '%')
	{
		if( $text )
		{
			$quantity = intval($quantity);
			$regexp = "#\(q\\d+\:(([^\|]*)\|)?([^\|\)]*)\|([^\)]*)\)#";
			if( preg_match_all($regexp, $text, $matches, PREG_SET_ORDER ) )
			{
				foreach($matches as $match)
				{
					$index = ($quantity<2 ? 3 : 4);
					if( $match[1] && $quantity == 0 )
						$index -= 1;
					$text = str_replace($match[0], $match[$index], $text);
				}
			}
			return str_replace($qPattern, $quantity, $text);
		}
		return $text;
	}

};



?>