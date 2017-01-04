<?php

/******************************
 *         Class Vars         *
 ******************************/

/**
 * @desc-start
 * This class is used to handle $_GET and $_POST vars.
 * Theses two variables below are super globals,
 *   so none 'global' keyword need to be used.
 * @desc-end
 *
 * Here is the list of functions:
 *		- Vars::init()
 *
 *		- Vars::clean($key)
 *		- Vars::safeSQL($key)
 *		- Vars::get($key, [$default_value])
 *    - Vars::set($key, $value)
 *    - Vars::remove($key)
 *    - Vars::cleanAfterAjaxCall()
 *		- Vars::defined($keys)
 *		- Vars::pDefined($keys)
 *		- Vars::match($ereg, [$field])
 *
 *		- Vars::isPostContext()
 *		- Vars::clearPost()
 */

 
 

/* Class definition */
class Vars
{

	private static $__GET;

	public static function init()
	{
		// merge the two arrays inside $_GET, so we can only work with $_GET
		self::$__GET = $_GET;
		$_GET += $_POST;
		
		// do some cleaning if 'get_magic_quotes_gpc' parameter is on, and remove strange keys (starting by '|')
		foreach($_GET as $key => $val)
		{
			if( $key{0} == '|' )
				unset($_GET[$key]); // strange issue (URL rewriting error !?)
			elseif( is_string($val) )
			{
				if( get_magic_quotes_gpc() ) // magic_quotes mode fix
					$_GET[$key] = stripslashes($val);
				if( preg_match("#UTF-8#i", ENCTYPE_CHARSET) )
					$_GET[$key] = utf8_decode($_GET[$key]);
				if( ! Common::isAdminContext() && preg_match("#<script[^>]*>#", $val) )
					Common::reportFatalError(htmlspecialchars("A parameter sent contains <script/> tags! Page stopped for safety"));
			}
		}
	}


	
/**********************************
 * Get $_GET values with cleaning *
 **********************************/
	
	// Remove useless spaces and force the newline char as '\n' from the specified get-key
	public static function clean($key)
	{
		return String::clean(self::get($key));
	}
	
	// Clean the specified get-key to be used in SQL commands
	public static function safeSQL($key)
	{
		return String::safeSQL(self::get($key));
	}
	
	// Retrieve a key in the $_GET array
	public static function get($key, $default_value = null)
	{
		if( self::defined($key) )
			return $_GET[$key];
		else
			return $default_value;
	}
	
	// Set a new value into the $_GET array
	public static function set($key, $value)
	{
		$_GET[$key] = $value;
	}
	
	// Unset a data from the $_GET array
	public static function remove($key)
	{
		unset($_GET[$key]);
	}
	
	// Clean all $_GET vars from UTF8 enctype, to avoid strange caracters
	public static function cleanAfterAjaxCall()
	{
		if( preg_match('/^ISO-/', ENCTYPE_CHARSET) )
			foreach($_GET as $key => $val)
				$_GET[$key] = utf8_decode($val);
	}
	
	
/**************************
 * Isset/defined function *
 **************************/
	
	// Test the existance of keys in the combined $_GET array
	// if the first arg is not an array, it will loop on each given arg to test all of them
	// @param $array Array of keys to test. If not an array, array of keys will be all given arguments
	// @param $index. If $array is an Assoc-Array, referes to the key to use. Let it to null if not needed
	// @return Return true if every key is defined in either $_GET or $_POST array
	public static function defined($array, $index = null)
	{
		if( ! is_array($array) )
		{
			$array = func_get_args();
			$index = null;
		}
		foreach($array as $key)
		{
			if( is_array($key) && !is_null($index) )
			{
				if( ! self::defined($key[$index]) )
					return false;
			}			
			elseif( is_string($key) )
			{
				if( ! isset($_GET[$key]) )
					return false;
			}
		}
		return true;
	}
	
	// Test the existance of keys in the $_POST array.
	// Use this function to test form returns from your users.
	// Give a list of keys as parameters (inner-use of func_get_args())
	// @return Return true if every key is defined in $_POST array and if 
	public static function pDefined()
	{
		$array = func_get_args();
		foreach($array as $key)
		{
			if( ! isset($_POST[$key]) )
				return false;
		}
		return self::isPostContext();
	}
	
	
	// $field will be ignored if not an integer (represents the index of the group catched by the RegExp)
	// [code]
	// // Example: $_GET = Array('pref_id' => 5, 'pref_test' => 1, 'aa' => 45);
	// Vars::match("/^pref_(.*)$/", 1); // will return : Array('id' => 5, 'test' => 1);
	// Vars::match("/^pref_(.*)$/"); // will return : Array('pref_id' => 5, 'pref_test' => 1);
	// [/code]
	// @param $ereg to apply. Example: '/^my_prefix_/' or '/ends_with$/'
	// @param $field if mentionned represent the index of cached group to return
	// @return Returns the $_GET array with the RegExp filtered
	public static function match($ereg, $field = null) // 
	{
		$array = Array();
		foreach($_GET as $key => $val)
			if( preg_match($ereg, $key, $acc) )
				$array[is_int($field) ? $acc[$field] : $key] = $val;
		return $array;
	}
	
/**************************
 *      POST function     *
 **************************/
 
	// Returns true if there are some vars in the $_POST array
	// It also test if the request really comes from this server, to provide external attacks
	public static function isPostContext()
	{
		return ($_POST) && Common::isRightReferer();
	}
	
	// clear data containing in the $_POST array
	public static function clearPost()
	{
		unset( $_POST );
	}
	
};
?>