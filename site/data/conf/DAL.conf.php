<?php

/** ------------------ DATA ACCESS LAYER ------------------ **/



class ContextDAL implements IContextDAL
{
	private static $pages_table = null, $wrapped_table = null;
	private static $currentLanguage = null;

	private static function genKey($code)
	{
		return "page.$code.title";
	}
	
	public static function init($pages_table_array)
	{
		self::$pages_table = $pages_table_array;
		$assoc = SETTINGS_MULTI_LINGUAGES ? $pages_table_array[Lang::getCurrentLanguage()] : $pages_table_array;
		if( ! is_array($assoc) )
			Common::reportFatalError("[Class ContextDAL] The init() parameter should be a correct array !");
		self::$wrapped_table = $assoc;
	}
	
	public static function determineNameFromCode($code)
	{
		if( $pagename = self::$wrapped_table[$code] )
			$code = $pagename;
		elseif( SETTINGS_MULTI_LINGUAGES )
		{
			if( $sqlRow = DB::getOneRow(LangDAL::TranslateKey(self::genKey($code))) )
				$code = $sqlRow->content;
		}
		return ($code);
	}
	
	public static function determineCodeFromName($name)
	{
		foreach(self::$wrapped_table as $key => $value)
			if( $name == $value )
				return $key;
		if( SETTINGS_MULTI_LINGUAGES )
		{
			if( $field = DB::getField(LangDAL::SearchSpecificKey($name, 'page\.([a-z_]+)\.title')) )
			{
				$parts = explode('.', $field);
				return $parts[1];
			}
			foreach(self::$pages_table as $table)
				foreach($table as $key => $value)
					if( $name == $value )
						return $key;
			if( $field = DB::getField(LangDAL::SearchSpecificKey($name, 'page\.([a-z_]+)\.title', '')) )
			{
				$parts = explode('.', $field);
				return $parts[1];
			}
		}
		return null;
	}
	
	public static function changeLanguageLink($code, $lang)
	{
		if( SETTINGS_MULTI_LINGUAGES )
		{
			$arr = self::$pages_table[$lang];
			if( is_array($arr) && $arr[$code] )
				$code = $arr[$code];
			elseif( $sqlRow = DB::getOneRow(LangDAL::TranslateKey(self::genKey($code), $lang)) )
				$code = $sqlRow->content;
			return ($code);
			
		}
		else
			Common::reportRunningWarning("[Class ContextDAL] changeLanguageLink() not authorized !");
		return $code;
	}
};





/* Class definition */
class LangDAL extends LangSQL implements ILangDAL
{
	private static function JoinViewKeys() { return "`locID`, `key`, `type`, `tiny`, `admin`, `locIdx`, `langIdx`, `content`"; }
	private static function KeysTableKeys() { return "`locID`, `key`, `type`, `tiny`, `admin`"; }
	
	
	// Get the row or field corresponding to the translation of a key (will return 0 or 1 row)
	// @param $key Resource key
	// @param $lang Language code
	// @param $select Clause SELECT, Default is '*'
	// @return Returns an SQL query
	public static function TranslateKey($key, $lang = null, $select = "*")
	{
		if( is_null($lang) )
			$lang = Lang::getCurrentLanguage();
		if( $select == '*' )
			$select = self::JoinViewKeys();
		return "SELECT $select FROM `".self::$sqlJoinView."` WHERE `langIdx`='"
			.String::safeSQL($lang)."' AND `key`='".String::safeSQL($key)."' LIMIT 0,1";
	}
	
	// @param $boFilter If true, returns only Back Office languages
	// @return Returns an SQL query
	public static function GetLanguages($boFilter)
	{
		return "SELECT `langID`, `langName`, `isAdminLang` FROM `".self::$sql_langs_table."`".($boFilter ? " WHERE `isAdminLang`" : '');
	}
	
	// @param $regexp RegExp matched to resource keys
	// @return Returns an SQL query
	public static function GetMatchingKeys($regexp)
	{
		return "SELECT `key` FROM `".self::$sql_keys_table."` WHERE `key` REGEXP '".String::safeSQL($regexp)."'";
	}
	
	// @param $text Text to search for
	// @param $lang Associed language
	// @param $backOffice can be YES, NO or ALL
	// @param $type Resource type
	// @param $select Clause SELECT
	// @return Returns an SQL query
	public static function SearchText($text, $lang, $backOffice, $type = TYPE_ALL, $select = "*")
	{
		if( $backOffice != ALL )
			$adm = "AND ".($backOffice == YES ? '' : 'NOT ')."`admin`";
		if( $select == '*' )
			$select = self::JoinViewKeys();
		$sql = "SELECT $select FROM `".self::$sqlJoinView."` WHERE `langIdx`='".String::safeSQL($lang)
			."' AND INSTR(`content`, CONVERT(\"".String::safeSQL($text)."\" USING utf8))>0 $adm";
		if( $type != TYPE_ALL )
			$sql .= " AND `type`='".String::safeSQL($type)."'";
		return $sql;
	}
	
	// This SQL command may return several rows if $resName is surronded by percents ('%$resName%')
	// @param $resName RegExp to match Resource name
	// @return Returns an SQL command returning 0,1 or several rows
	public static function SearchResourceID($resName, $backOffice = ALL, $type = TYPE_ALL, $select = "*")
	{
		if( $select == '*' )
			$select = self::KeysTableKeys();
		$sql = "SELECT $select FROM `".self::$sql_keys_table."` WHERE `key` LIKE '".String::safeSQL($resName)."'";
		if( $backOffice != ALL )
			$sql .= " AND ".($backOffice == YES ? '' : 'NOT ')."`admin`";
		if( $type != TYPE_ALL )
			$sql .= " AND `type`='".String::safeSQL($type)."'";
		return "$sql"; // LIMIT 0,1"; -- to remove, because '%' can be around $resName and so returns several rows.
	}
	
	
	public static function CreateResourceKey($resName, $type, $tiny, $boRsc)
	{
		$sql = "INSERT INTO `".self::$sql_keys_table."` VALUES (NULL, '".String::safeSQL($resName)
			."', '".String::safeSQL($type)."', ".($tiny ? '1' : '0').", ".($boRsc ? '1' : '0').")";
		return $sql;
	}
	
	public static function ReplaceText($resIdx, $lang, $text)
	{
		$sql = "REPLACE INTO `".self::$sql_texts_table."` VALUES (".intval($resIdx)
			.", '".String::safeSQL($lang)."', '".String::safeSQL($text)."')";
		return $sql;
	}
	
	public static function FindTranslation($resIdx, $lang, $alias = null)
	{
		$sql = "SELECT `content` ".($alias ? "AS '$alias' " : '')."FROM `".self::$sql_texts_table
			."` WHERE `locIdx` = ".intval($resIdx)." AND `langIdx` = '".String::safeSQL($lang)."'";
		return $sql;
	}
	
	public static function FindResourceToEdit($resName, $resIdMD5, $all = false)
	{
		$sql = "SELECT ".($all ? self::KeysTableKeys() : "`locID`")." FROM `".self::$sql_keys_table
			."` WHERE `key` = '".String::safeSQL($resName)."' AND MD5(`locID`) = '".String::safeSQL($resIdMD5)."'";
		return $sql;
	}
	
	public static function ChangeResourceKey($resIdx, $resNewName)
	{
		$sql = "UPDATE `".self::$sql_keys_table."` SET `key` = '".String::safeSQL($resNewName)."' WHERE `locID` = ".intval($resIdx);
		return $sql;
	}
	
	public static function ChangeResourceProperties($resIdx, $newType, $newTiny)
	{
		$newTiny = ($newTiny == NO) ? 0 : 1;
		$sql = "UPDATE `".self::$sql_keys_table."` SET `type` = '".String::safeSQL($newType)
			."', `tiny` = $newTiny WHERE `locID` = ".intval($resIdx);
		return $sql;
	}
	
	public static function DeleteResource($resIdx)
	{
		return Array(
			"DELETE FROM `".self::$sql_texts_table."` WHERE `locIdx` = ".intval($resIdx),
			"DELETE FROM `".self::$sql_keys_table."` WHERE `locID` = ".intval($resIdx)
		);
	}
	
	// @param $text Text to search for
	// @param $keyMatching RegExp matching the key
	// @param $lang Text language to search for, current language if null
	// @return Returns an SQL query returning the ressource key
	public static function SearchSpecificKey($text, $keyMatching, $lang = null)
	{
		if( is_null($lang) )
			$lang = Lang::getCurrentLanguage();
		$sql = "SELECT `key` FROM `".self::$sqlJoinView."` WHERE `content` = '"
			.String::safeSQL($text)."' AND `key` REGEXP '".String::safeSQL($keyMatching)."'";
		if( $lang )
			$sql .= " AND `langIdx`='".String::safeSQL($lang)."'"; 
		return $sql;
	}
	
	// Transform an SQL row into an array 
	// Keys of this array are: 'id'(int), 'key'(string), 'type'(string), 'tiny'(bool) and 'isBO'(bool)
	// @param $sqlRow SQL row got by DB::select/queryToArray/getOneRow/getField()
	// @return Returns the corresponding array, null if parameter not as expected
	public static function RscInfoToArray($sqlRow)
	{
		
		if( $sqlRow && $sqlRow->locID)
			return Array(
				'id' => $sqlRow->locID,
				'key' => $sqlRow->key,
				'type' => $sqlRow->type,
				'tiny' => (bool)$sqlRow->tiny,
				'isBO' => (bool)$sqlRow->admin
			);
	}
};




/* Class definition */
class DataDAL implements IDataDAL
{
	// `data` table
	private static $data_table = null;
	private static $data_fields = null;
	
	// Initilization for this interface.
	// $data_fields keys should be: 'key', 'value' and 'type'. Default values are: 'key'=>'key, etc.
	// @param $data_table Name of the SQL table containing the Data
	// @param $data_fields Assoc-Array containing the name of the fields to access to the SQL properties of Data table
	public static function init($data_table = null, $data_fields = null)
	{
		// ------ Data Table ------
		if( empty($data_table) )
			self::$data_table = null;
		elseif( DB::isExistingTable($data_table) )
			self::$data_table = $data_table;
		else
			Common::reportRunningWarning("Invalid table name for Database::setDataTable('$data_table') !");
		
		// ------ Data Fields ------
		if( is_null($data_fields) )
			$data_fields = Array();
		self::$data_fields = $data_fields + Array('key' => 'key', 'value' => 'value', 'type' => 'type');
	}

	// Get the list of Data keys
	// @return Get the list of Data keys
	public static function listDataKeys()
	{
		return DB::select("SELECT ".self::GetFieldList()." FROM `".self::$data_table."`");
	}
	
	// Get the value of the Data table
	// @param $key Key of the Data table to get the associated value
	// @return Returns the corresponding value (converted according to his type), or display a Warning message
	public static function getDataValue($key)
	{
		if( self::$data_table )
		{
			$val  = self::$data_fields['value'];
			$type = self::$data_fields['type'];
			if( $row = DB::getOneRow("SELECT `$val`,`$type` FROM `".self::$data_table."` WHERE `".self::$data_fields['key']."`='$key'") )
				return String::convert($row->$val, $row->$type);
			else
				Common::reportRunningWarning("[Database DataDAL] getDataValue() : no key for `$key` !");
		}
		else
			Common::reportRunningWarning("[Database DataDAL] getDataValue() : the Data table is not defined !");
	}
	
	// Get a full row of the Data table
	// @param $key Key of the Data table to get the associated row
	// @return Returns the corresponding row, or display a Warning message
	public static function getDataRow($key)
	{
		if( self::$data_table )
			return DB::getOneRow("SELECT ".self::GetFieldList()." FROM `".self::$data_table."` WHERE `".self::$data_fields['key']."`='$key'");
		else
			Common::reportRunningWarning("[Database DataDAL] getDataValue() : the Data table is not defined !");
	}
	
	// Change the value of a Data-field
	// @param $key Data key to change the corresponding value
	// @param $value New value for this key
	public static function updateDataValue($key, $value)
	{
		if( self::$data_table )
			return DB::update("UPDATE `".self::$data_table."` SET `".self::$data_fields['value']."` = '".String::safeSQL($value)
					."' WHERE `".self::$data_fields['key']."`='".String::safeSQL($key)."'");
		else
			Common::reportRunningWarning("[Database DataDAL] getDataValue() : the Data table is not defined !");
	}
	
	// Get the field name corresponding to the given key for the Array given at initialization ($data_fields)
	// @param $key 'key', 'value' or 'type' is attended.
	// @return Returns something like self::$data_fields[$key]
	public static function getDataFieldKey($key)
	{
		if( is_array(self::$data_fields) )
			return self::$data_fields[$key];
		return null;
	}
	
	
	private static function GetFieldList() { return "`key`,`type`,`value`,`tiny`,`validator`"; }
}


?>