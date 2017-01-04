<?php

/*******************************
 *     Class DB (DataBase)     *
 *******************************/

/**
 * @desc-start
 * This class is used to make easier transferts with MySQL databases.
 * SQL errors are tracked and will be displayed, sent by email and/or stored in a log table.
 * @desc-end
 *
 * Here is the list of functions:
 *		- DB::Database($local_settings, $web_settings, [$log_table, [$log_email]])
 *		- DB::disconnect()
 *		- DB::isConnected()
 *
 *		- DB::changeCatalog($base)
 *		- DB::setLogTable($table)
 *		- DB::setLogEmail($mail)
 *
 *		- DB::update($sqlQuery, [$report_error])
 *		- DB::isValidQuery($sqlQuery)
 *    - DB::insert($table, $values, [$report_error])
 *		- DB::select($sqlQuery, [$returnEmptyArrayIfItFails, [$report_error]])
 *		- DB::getAssocArray($sqlQuery, $key, [$value, [$returnEmptyArrayIfItFails, [$report_error]]])
 *		- DB::getOneRow($sqlQuery)
 *		- DB::getField($sqlQuery, [$field])
 *
 *		- DB::isExistingTable($table)
 *
 *		- DB::getLastError([$pattern])
 *		- DB::getLastInsertedId()
 *		- DB::getAutoIncValue($table)
 *
 * @desc-start
 * If SQL errors had occured they are saved on the `log` table _
 *   and sent by email (according to init configuration).
 * In local context and if 'SETTINGS_DEBUG_MODE' is set to true, error will be shown on the page, _
 *   but in Web context or just by setting this var to false and only traces will be saves _ 
 *   to the log (table/emails).
 * The raising error method is based on 'Common::reportRunningWarning()'. Have a look at this _
 *   this class to learn more, or modify display settings.
 * @desc-end
 */

class DB
{
	// connection information
	private static $host = null, $base;

	// last resource used and connection ID
	private static $connexionId = null;
	private static $resourceId = null;
	
	// log options
	private static $log_table = null;
	private static $log_email = null;
	
	// log errors
	private static $lastErrNum = 0, $lastErrDesc = '';
	
	
	/******************************************
	 *  Constructor and disconnect functions  *
	 ******************************************/
	
	// Initilisation function of this class. To be called before calling any function. Connection to the MySQL datbase
	// @param $local_settings Array of local settings with these keys ('host'/'base'/'user'/'pass')
	// @param $web_settings Array of Web settings (when deployed on a remote server)
	// @param $data_table define the SQL `data` table which stores main data (can be null)
	// @param $data_fields default value is: Array('key' => 'key', 'value' => 'value', 'type' => 'type')
	// @param $log_table define the SQL Log table to list SQL query errors (can be null)
	// @param $log_email define the destination email address to receive alerts concerning SQL query errors. If not null, it should be a valid email (that match casual RegEx)
	public static function init($local_settings, $web_settings, $log_table = null, $log_email = null)
	{
		if( is_null(self::$host) )
		{
			$settings = Common::isLocalContext() ? $local_settings : $web_settings;
			
			self::$host = $settings['host'];
			
			// Connection t the database
			self::$connexionId = @mysql_connect(self::$host, $settings['user'], $settings['pass'])
				or Common::reportFatalError("Unable to connect to the database '".self::$host."' !\nCheck your SQL settings.");
			
			// Connection to the catalog/base
			self::changeCatalog($settings['base']);
			
			// [Log] set a new log table
			self::setLogTable($log_table);
			
			// [Log] set a new log email address
			self::setLogEmail($log_email);
		}
	}
	
	// Disconnect the Database and free reserved resources.
	// Automatically called by overrided destructor
	public static function disconnect()
	{
		// free unfreed resource
		self::freeResource();
		// close the connexion
		@mysql_close(self::$connexionId);
		self::$connexionId = false;
	}
	
	// Override of the default destructor
	public function __destruct()
	{
		self::disconnect();
	}
	
	// Test if the connection is still opened and has not been closed
	// @return Returns true if DB is connected
	public static function isConnected()
	{
		return self::$connexionId;
	}
	
	
	
	/***************************************
	 *  Set log infos and current catalog  *
	 ***************************************/

	// Try to connect to the catalog/base or send a fatal error
	// @param $base base/catalog name
	// @return Returns a fatal error if catalog not found
	public static function changeCatalog($base)
	{
		self::$base = $base;
		@mysql_select_db($base) or
				Common::reportFatalError("Unable to connect to the SQL base '$base' on '".self::$host."' !\nCheck your SQL settings.");
	}

	
	// Set the log table to track SQL syntax errors or attacks
	// Then SQL errors are automatically saved into the DB
	// @param $table table name for the SQL-errors log
	// @return Can return a Warning if table name invalid
	public static function setLogTable($table)
	{
		if( empty($table) )
			self::$log_table = null;
		elseif( self::isExistingTable($table) )
			self::$log_table = $table;
		else
			Common::reportRunningWarning("Invalid table name for Database::setLogTable('$table') !");
	}
	
	// Set the log email to track SQL syntax errors or attacks
	// Then SQL errors are automatically sent by email to this address
	// @param $mail email address to send the reports
	// @return Can return a Warning if email is not correct
	public static function setLogEmail($mail)
	{
		if( empty($mail) )
			self::$log_email = null;
		elseif( String::isValidEmail($mail) )
			self::$log_email = $mail;
		else
			Common::reportRunningWarning("Invalid email address for Database::setLogEmail('$mail') !");
	}
	
	
	
	/***************************************
	 *           Query functions           *
	 ***************************************/
	
	// Perform an SQL query. Do Log stuffs if it fails
	// @param $sqlQuery SQL query to perfom
	// @param $log_error If true and an error occured perform error-reporting actions (display/db-log/email)
	// @return Returns true if the SQL query run succesfully
	public static function update($sqlQuery, $log_error = true)
	{
		if( self::isConnected() )
		{
			// Free the previous resource if still not freed
			self::freeResource();
			
			// For log-table
			$res = false; 
			
			// Perform the query and returns true
			if( self::$resourceId = @mysql_query($sqlQuery) )
			{
				self::$lastErrDesc = '';
				self::$lastErrNum  = 0;
				return true;
			}
			
			// save errors
			$sql_error = self::$lastErrDesc = mysql_error();
			$sql_errno = self::$lastErrNum  = mysql_errno();
			
			if( ! $log_error ) // if we don't log errors, we can skip the rest of this function
				return false;
			
			
			// add a row in the Log table
			if( self::$log_table )
			{
				$params = Array(Client::getSessionID(), Date("Y-m-d H:i:s"), $sqlQuery, $_SERVER['REQUEST_URI'], $sql_errno, $sql_error);
				$res = @mysql_query(self::buildInsertQuery(self::$log_table, $params, false));
				@mysql_free_result($res);
			}
			
			// send an email
			if( self::$log_email )
			{
				$url = Common::getDomain().$_SERVER['REQUEST_URI'];
				$message = "<html><body>An SQL error has occured on the following server:\n"
									. "<b>Server</b>: ".self::$host."\n<b>Base</b>: ".self::$base."\n"
									. "<b>Url</b>: <a href='$url'>$url</a>\n"
									. "<b>SQL Error</b>: $sql_error (code: sql_errno)\n";
				if( $res ) // if an error/log row has been inserted
					"<i>Saved in '".self::$log_table."' as record #".mysql_insert_id()."</i>\n";
				$message .= "</body></html>";
				Common::mail(self::$log_email, "SQL Error", str_replace("\n", '<br />', $message), self::$log_email);
			}
			
			Common::reportRunningWarning("SQL Error: $sql_error");
		}
		else
			self::reportDatabaseClosed();
		return false;
	}
	
	// Execute a query without error-reporting support. This is a macro for DB::update($sqlQuery, false).
	// This function is mainly used to check whether if a table exists
	// @param $sqlQuery SQL query to perfom
	// @return Returns false if it fails, but none warning will be displayed or sent
	public static function isValidQuery($sqlQuery)
	{
		return self::update($sqlQuery, false);
	}
	
	
	// Values will be converted to prevent errors according to their type: _
	//   Strings are quoted and converted with String::safeSQL, _
	//   Boolean are converted to 0/1, _
	//   null values are converted to NULL
	private function convertValue($value)
	{
		if( is_string($value) )
			return "'".String::safeSQL($value)."'";
		elseif( is_int($value) || is_float($value) )
			return $value;
		elseif( is_bool($value) )
			return $value ? 1 : 0;
		elseif( is_null($value) )
			return 'NULL';
		else
			return "''";
	}
	
	// Create and run an INSERT INTO query. 
	// Values will be converted to prevent errors according to their type: _
	//   Strings are quoted and converted with String::safeSQL, _
	//   Boolean are converted to 0/1, _
	//   null values are converted to NULL
	// @param $table SQL table where to insert the row
	// @param $values Array containing the values of the row. 
	// @param $log_error If true and an error occured perform error-reporting actions (display/db-log/email)
	// @return Returns true if it ran correctly
	public static function insert($table, $values, $log_error = true)
	{
		return self::update(self::buildInsertQuery($table, $values), $log_error);
	}
	
	// Create an INSERT INTO query string. See DB::insert().
	private static function buildInsertQuery($table, $values, $log_error = true)
	{
		$vals = Array();
		foreach( $values as $key )
			$vals[] = self::convertValue($key);
		return "INSERT INTO `$table` VALUES(" . implode(',', $vals) . ")";
	}
	
	// Create and run an REPLATE INTO query. 
	// Values will be converted to prevent errors according to their type: _
	//   Strings are quoted and converted with String::safeSQL, _
	//   Boolean are converted to 0/1, _
	//   null values are converted to NULL
	// @param $table SQL table where to insert the row
	// @param $values Array containing the values of the row. 
	// @param $log_error If true and an error occured perform error-reporting actions (display/db-log/email)
	// @return Returns true if it ran correctly
	public static function replace($table, $values, $log_error = true)
	{
		$vals = Array();
		foreach( $values as $key )
			$vals[] = self::convertValue($key);
		$sqlQuery = "REPLACE INTO `$table` VALUES(" . implode(',', $vals) . ")";
		return self::update($sqlQuery);
	}
	
	// Perform a SELECT SQL query and returns all the affected rows
	// See Common::assocSQLRow() and Common::assocSQLRows() to cast the results (for easier handeling)
	// @param $sqlQuery SQL query to perfom
	// @param $returnEmptyArrayIfItFails If true returns an empty array if the query fails, instead of null to use foreach() without additional if condition
	// @param $log_error If true and an error occured perform error-reporting actions (display/db-log/email)
	// @return Returns the array if success, null or an empty array if failure (depending of the optional parameter $returnEmptyArrayIfItFails)
	public static function select($sqlQuery, $returnEmptyArrayIfItFails = true, $log_error = true)
	{
		// perform the query
		if( self::update($sqlQuery, $log_error) ) // No need to check if the connexion is still open and free the last resource: BD::update() do this
		{
			// array to store our rows returned
			$array = Array();
			
			// loops on returned rows and push them in the result array
			while( $res = mysql_fetch_object(self::$resourceId) )
				$array[] = $res;
			
			return $array;
		}
		elseif( $returnEmptyArrayIfItFails )
			return Array();
		else
			return null;
	}
	
	// Similiar to DB::select() but return an array with custom keys (keys are computed related to the table ID)
	// [code]
	// DB::getAssocArray("SELECT id, brand, doors from `cars` WHERE `id`>5", 'id');
	// Return Array(
	// _ [6] => stdClass Object ( [id] => 6, [brand] => Renault, [doors] => 5 ), ...
	// )
	// DB::getAssocArray("SELECT id, brand, doors from `cars` WHERE `id`>5", 'id', 'brand');
	// Return Array(
	// _ [6] => Renault, ...
	// )
	// [/code]
	// @param $sqlQuery SQL query to perfom
	// @param $key name of one of the field returned that will be used as a key for the returned array
	// @param $value If value is given the returned array has only the SQL row field as value instead of the full row
	// @param $returnEmptyArrayIfItFails If true returns an empty array if the query fails, instead of null to use foreach() without additional if condition
	// @param $log_error If true and an error occured perform error-reporting actions (display/db-log/email)
	// @return Returns the assoc-array if success, null or an empty array if failure (depending of the optional parameter $returnEmptyArrayIfItFails)
	public static function getAssocArray($sqlQuery, $key, $value = null, $returnEmptyArrayIfItFails = true, $log_error = true)
	{
		// No need to check if the connexion is still open and free the last resource: BD::update() do this
		
		// perform the query
		if( self::update($sqlQuery, $log_error) )
		{
			// array to store our rows returned
			$array = Array();
			
			// loops on returned rows and push them in the result array
			while( $res = mysql_fetch_object(self::$resourceId) )
				$array[$res->$key] = $value ? $res->$value : $res;
			
			return $array;
		}
		elseif( $returnEmptyArrayIfItFails )
			return Array();
		else
			return null;
	}
	
	
	// Make a SELECT SQL query and returns only the first affected rows
	// @param $sqlQuery SQL query to perfom
	// @return Returns the first SQL row or null
	public static function getOneRow($sqlQuery)
	{
		// No need to check if the connexion is still open and free the last resource: BD::update() do this
		
		// perform the query
		if( self::update($sqlQuery) )
		{
			// get the first row, and returns it
			return $res = mysql_fetch_object(self::$resourceId);
		}
		else
			return null;
	}
	
	// Make a SELECT SQL query and returns a field value of the first affected rows (not the full row, but only one field)
	// @param $sqlQuery SQL query to perfom
	// @param $field field of the row to return. If null, the function will try to determine/compute it
	// @return Returns the corresponding field value or null
	public static function getField($sqlQuery, $field = null)
	{
		// No need to check if the connexion is still open and free the last resource: BD::update() do this
		if( $field = self::extractSelectedFieldFromQuery($sqlQuery, $field) )
		{
			// Retrieve the firt row
			if( $row = self::getOneRow($sqlQuery) )
				return $row->$field;
		}
		return null;
	}
	
	
	/***************************************
	 *        Other useful functions       *
	 ***************************************/
	
	// Test if a table exists
	// @param $table Table name of the current MySQL database
	// @return Returns null if it fails
	public static function isExistingTable($table)
	{
		// No need to check if the connexion is still open and free the last resource: BD::update/isValidQuery() do this
		
		// Try to get information from this table
		return self::isValidQuery("SHOW COLUMNS FROM `$table`");
	}
	
	
	/***************************************
	 *            Get functions            *
	 ***************************************/
	
	// Returns the last error that has occured and the corresponding error code
	// @param $pattern Pattern of the returned string (%0: error description, %1: error number)
	// @return Returns the formated last error string
	public static function getLastError($pattern = "%0 (%1)")
	{
		if( self::isConnected() )
		{
			$pattern = str_replace("%1",  self::$lastErrNum, $pattern);
			$pattern = str_replace("%0",  self::$lastErrDesc, $pattern);
			return $pattern;
		}
		else
			self::reportDatabaseClosed();
	}
	
	// Get the last Id of the Auto_Increment field (updated in the current context/connection)
	// @return Returns last generated ID
	public static function getLastInsertedId()
	{
		if( self::isConnected() )
			return @mysql_insert_id();
		else
			self::reportDatabaseClosed();
	}
	
	// Get the next Id to use for the given table (watcht out concurency access)
	// @param $table table name where to get the auto_inc value
	// @return Returns the AUTO_INCREMENT value of this table
	public static function getAutoIncValue($table)
	{
		if( $row = self::getOneRow("SHOW TABLE STATUS FROM `".self::$base."` WHERE name = `$table`") )
			return $row->Auto_increment ;
		return null;
	}
	
	
	
	
	/***************************************
	 *      Other private functions        *
	 ***************************************/
	
	// Free the memory linked to the previous query
	private static function freeResource()
	{
		if( self::$resourceId )
			@mysql_free_result(self::$resourceId);
		self::$resourceId = null;
	}
	
	
	private static function reportDatabaseClosed()
	{
		Common::reportRunningWarning("[Database Class] The connexion has been closed. Check your PHP code !!!");
	}
	
	private static function extractSelectedFieldFromQuery($sql, $field)
	{
		if( empty($field) )
		{
			// Search the filtered field from the SQL query
			// It also handles renaming: 'SELECT COUNT(*) AS `xxx`'
			$sql = trim($sql);
			$sql2 = strtoupper($sql);
			
			// Check if it really starts with 'SELECT' and contains 'FROM'
			$arr = explode("FROM", $sql);
			if( substr($sql2, 0, 6) != 'SELECT' || count($arr) != 2 )
				return null;
			
			// Retrieve the part where the field is supposed to be
			$field = trim(substr($arr[0], 6));
			
			// We don't handle the case when there are comas : ','
			if( strstr($field, ',') )
				return null;
			
			// explode the string on spaces, but clean it first from multi-spaces chars
			$arr = explode(" ", String::clean($field));
			$index = 0;
			// if we find 'AS', then select the next field;
			foreach($arr as $key => $value)
				if( strtoupper($value) == 'AS' )
					{ $index = $key+1; break; }
			if( $index )
				$field = $arr[$index];
		}
		// Clean quotes
		if( $field{0} == '`' || $field{0} == "'" || $field{0} == '"' )
			$field = substr($field, 1, -1);
		return $field;
	}


// Fonction qui supprime toutes les lignes avec jointures en cascade
/* Exemple :
	$array = Array( 
			Array("table" => "animal", "key" => "numAnimal", "join" => "animal"),
			Array("table" => "produit", "key" => "numProduit", "join" => "produit"),
			Array("table" => "conseil", "key" => "numConseil")
	);
	$bd->deleteOnCascade($array, 5);
*/
/*
	// function deleteOnCascade($array, $value, $first = true)
	{
		//echo "<pre>"; print_r($array); echo "</pre>";
		if( count($array) )
		{
			if( isset($array[0]['join']) )
			{
				$row0 = array_shift($array);
				$arr = self::queryToArray("SELECT ".$array[0]['key']." FROM ".$array[0]['table']
						." WHERE ".$row0['join']." = ".$value);
				foreach($arr as $key)
					self::deleteOnCascade($array, $key->$array[0]['key'], false);
				
				//echo "DELETE FROM ".$array[0]['table']
				//		." WHERE ".$row0['join']." = ".$value."<br />\n";
				self::update("DELETE FROM ".$array[0]['table']
						." WHERE ".$row0['join']." = ".$value, true);
			}
			else
				$row0 = $array[0];
			
			if( $first )
			{
				echo "DELETE FROM ".$row0['table']
						." WHERE ".$row0['cle']." = ".$value."<br />\n";
				self::update("DELETE FROM ".$row0['table']
						." WHERE ".$row0['cle']." = ".$value);
			}
		}
	}
	*/
}



/** -------------------- End of Class -------------------- */


/******************************
 *     IDataDAL interface     *
 ******************************/

/**
 * @desc-start
 * Data Access Layer (DAL) interface for Data.
 * It provides methods definition for access to Data properties.
 * @desc-end
 */


interface IDataDAL
{
	// Initilization for this interface.
	// $data_fields keys should be: 'key', 'value' and 'type'. Default values are: 'key'=>'key, etc.
	// @param $data_table Name of the SQL table containing the Data
	// @param $data_fields Assoc-Array containing the name of the fields to access to the SQL properties of Data table
	public static function init($data_table = null, $data_fields = null);
	
	// Get the list of Data keys
	// @return Get the list of Data keys
	public static function listDataKeys();
	
	// Get the value of the Data table
	// @param $key Key of the Data table to get the associated value
	// @return Returns the corresponding value (converted according to his type), or display a Warning message
	public static function getDataValue($key);
	
	// Get a full row of the Data table
	// @param $key Key of the Data table to get the associated row
	// @return Returns the corresponding row, or display a Warning message
	public static function getDataRow($key);
	
	// Change the value of a Data-field
	// @param $key Data key to change the corresponding value
	// @param $value New value for this key
	public static function updateDataValue($key, $value);
	
	// Get the field name corresponding to the given key for the Array given at initialization ($data_fields)
	// @param $key 'key', 'value' or 'type' is attended.
	// @return Returns something like self::$data_fields[$key]
	public static function getDataFieldKey($key);
};
	
	
/****************************************************
//
// Generation script for the Log Table
// -----------------------------------
//
CREATE TABLE `sql_log`
(
	`logId` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`logDate` DATETIME NOT NULL ,
	`logSqlQuery` TEXT NOT NULL ,
	`logUrl` TEXT NOT NULL ,
	`logErrNumber` SMALLINT NOT NULL ,
	`logErrMsg` TEXT NOT NULL
) ENGINE = innodb;
//
//
// Generation script for the Data Table
// ------------------------------------
//
CREATE TABLE IF NOT EXISTS `data` (
  `key` varchar(20) NOT NULL,
  `desc` text NOT NULL,
  `type` enum('html','text','js','int','float') NOT NULL DEFAULT 'text',
  `value` text NOT NULL,
  UNIQUE KEY `nom` (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

//
*****************************************************/
?>