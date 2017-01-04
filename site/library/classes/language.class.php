<?php

/******************************
 *        LangSQL Class       *
 ******************************/

/**
 * @desc-start
 * This class simply keeps SQL table names and save them into propected static members.
 * Two tables inherit from it: Lang and LangDAL (Data Access Layer)
 * @desc-end
 */
 
class LangSQL
{
	protected static $sql_langs_table, $sql_keys_table, $sql_texts_table, $sqlJoinView;
	
	// Initilisation function of this class. To be called before calling any function
	// @param $session_key key used to store data in the $_SESSION var
	// @param $sql_langs_table MySQL table name listing every language available for this Website
	// @param $sql_keys_table MySQL table name listing every localized resource
	// @param $sql_texts_table MySQL table name containing text/content for localized resource related to languages
	// @param $sqlJoinView MySQL view name containing the associed text/content for localized resources
	public static function init($sql_langs_table, $sql_keys_table, $sql_texts_table, $sqlJoinView)
	{
		self::$sql_langs_table = $sql_langs_table;
		self::$sql_keys_table  = $sql_keys_table;
		self::$sql_texts_table = $sql_texts_table;
		self::$sqlJoinView     = $sqlJoinView;
	}
};


/** -------------------- End of Class -------------------- */


/******************************
 *        LangDAL Class       *
 ******************************/

/**
 * @desc-start
 * Data Access Layer (DAL) class for Localization (language Class)
 * It contains functions returning SQL (string) to perform.
 * WARNING: you have to use DB::xxxx() functions to get what you want
 * @desc-end
 */


interface ILangDAL
{
	public static function TranslateKey($key, $lang = null, $select = "*");
	public static function GetLanguages($boFilter);
	public static function GetMatchingKeys($regexp);
	public static function SearchText($text, $lang, $backOffice, $type = TYPE_ALL, $select = "*");
	public static function SearchResourceID($resName, $backOffice = ALL, $type = TYPE_ALL, $select = "*");
	public static function CreateResourceKey($resName, $type, $tiny, $boRsc);
	public static function ReplaceText($resIdx, $lang, $text);
	public static function FindTranslation($resIdx, $lang, $alias = null);
	public static function FindResourceToEdit($resName, $resIdMD5, $all = false);
	public static function ChangeResourceKey($resIdx, $resNewName);
	public static function ChangeResourceProperties($resIdx, $newType, $newTiny);
	public static function DeleteResource($resIdx);
	public static function SearchSpecificKey($text, $keyMatching, $lang = null);
	public static function RscInfoToArray($sqlRow);
}



/** -------------------- End of Class -------------------- */



/******************************
 *         Lang Class         *
 ******************************/

/**
 * @desc-start
 * This class enables you to easily retrieve translated texts from your SQL database.
 * An instance has to be created and initialized. Then you have function to change easily _
 *   the current language and retrieve data in the appropriated language.
 *
 * Here are detailed explanation about how it works:
 *   You have to have a main SQL table containing the list of languages available. _
 *   This main table should have 2 mandatory fields : a key and a table name (should be unique). _
 *   The table name is the name of another SQL table and so this table should exists, _
 *     (the existance of this lang table will be tested).
 *   Then we try to determine for the current context which language to use (cf: 'computeLanguage()'): _
 *     You have to initialize two $_GET tags (used in the .htaccess file). _
 *     The first one is used to change the language of the website. _
 *     The second is to use another language without changing the defined language (for the same end-user). _
 *     Then we can just diaply a page with a different language without changing the current settings.
 *   Then we try to use the current language, but if there is none, the last parameter is used. _
 *     If it is null we will go through the list of language preferences of the browser. _
 *     Finally we use the the last parameter value as a key code. _
 *     If none language could be applied, the default language will be used.
 *
 * WARNING : This class needs the MySQL connection to be initialized and activated !!!
 * @desc-end
 *
 * Here is the list of not static functions:
 *    - Lang($language = null)
 *
 *    - translation($text_code)
 *    - switchTo($language)
 *
 * Here is the list of STATIC functions:
 *    - Lang::init($session_key, $sql_langs_table, $sql_keys_table, $sql_texts_table, $sqlJoinView, $default_language)
 *    - Lang::localizeBackOffice($acceptedForBackOffice_field)
 *
 *    - Lang::translate($text_code)
 *    - Lang::translateKeys($ereg_code, $lang_code = null, $return_array = true)
 *    - Lang::translateQuantity($key, $quantity, $qPattern = '%', $handleZeroCase = false)
 *    - Lang::getAssocArray($sqlQuery, $key, $value, $returnEmptyArrayIfItFails = true, $log_error = true)
 *    - Lang::getCurrentLanguage()
 *    - Lang::getSessionLanguage()
 *    - Lang::isLanguageSet()
 *    - Lang::computeLanguage($change_lang_code, $current_lang_code)
 *    - Lang::getAvailableLanguages()
 *    - Lang::getOtherAvailableLanguages()
 *    - Lang::getLanguage([$lang_code])
 *
 *    - Lang::changeLanguageLink($langCode, [$params])
 *    - Lang::HTML($page, [$params])
 *    - Lang::CSS($page, [$params])
 *    - Lang::JS($page, [$params])
 *    - Lang::IMAGE($page, [$params, [$lang]])
 *    - Lang::DATA($page)
 *    - Lang::AJAX($page, [$params])
 *    - Lang::addMessages($keys, [$tab])
 *    - Lang::getDate([$dtime, [$fulltime, [$longFormat]]])
 */
 


/* Class definition */
class Lang extends LangSQL
{

	// key used to store data in the $_SESSION var
	private static $sessionKey = null;


	// Key of the current language and the languages list stored inside the session for normal/admin context
	const KEY_LANGS_LIST   = 'langs';
	const KEY_CURRENT_LANG = 'current_lang';
	
	const KEY_FRONT_OFFICE = 'FrontOffice';
	const KEY_BACK_OFFICE  = 'BackOffice';
	
	const FRENCH_LANG = 'fr';
	
	// BO/FO: Represents in which context we are
	private static $currentOffice = Lang::KEY_FRONT_OFFICE;
	
	
	// save the code of the curent language used
	private static $currentLanguageCode = null;
	
	// default language code if none can be found with other methods
	private static $defaultLanguageCode;
	
	// fields used to define if the language is available in admin section (defined in Lang::localizeBackOffice())
	private static $acceptLangInBO = null;
	
	private static $isLanguageSet = false;
	
	
	// NOT STATIC field, save the current language for instances
	private $languageCode = null;
	
	
	/******************************************
	 *          not static functions          *
	 ******************************************/
	
	// Not-static constructor.
	// Allow to create a 'Lang' object to retrieve translated texts to the language you want
	// @param $language Language code. If null the context language code will be used instead
	public function Lang($language = null)
	{
		$this->switchTo_($language, 'new Lang()');
	}
	
	
	// switch to another language (usefull in loops to avoid creating many languages instances)
	// @param $language Language code
	// @return Returns true if the language has been successfully changed
	public function switchTo($language)
	{
		return $this->switchTo_($language, 'switchTo()');
	}
	
	// Translate a text with the current language (or the language given as parameter)
	// @param $text_code Text code key
	// @param $language Language code (use the default language if null). If not null only change the language temporarily
	// @param $apply_text_transformation Uses Text::toXXX() function to convert the text retrieved
	// @param $warning_message Display a warning message if failure (only applicable in local context and when Debug mode is ON)
	// @return Returns the converted text, null if fails (and warning message for local context)
	public function translation($text_code, $language = null, $apply_text_transformation = true, $warning_message = true)
	{
		if( is_null($language) )
			return self::translate_($text_code, $this->languageCode, $apply_text_transformation, $warning_message);
		if( self::getLanguage($language) )
			return self::translate_($text_code, $language, $apply_text_transformation, $warning_message);
		if( $warning_message )
			Common::reportRunningWarning("[Class Language] 'translation()': the language code '$language' doesn't exist !");
		return null;
	}
	
	
	/******************************************
	 *            static functions            *
	 ******************************************/
	
	// Initilisation function of this class. To be called before calling any function
	// @param $session_key key used to store data in the $_SESSION var
	// @param $default_language The default language to use if none parameters are given and browser prefered languages not working
	public static function init($session_key, $default_language)
	{
		if( $session_key )
		{
			self::$sessionKey = $session_key;
			if( ! isset($_SESSION[$session_key]) )
			{
				self::checkExistingTable(self::$sql_langs_table);
				self::checkExistingTable(self::$sql_keys_table);
				self::checkExistingTable(self::$sql_texts_table);
				self::checkExistingTable(self::$sqlJoinView);
				
				// Retrieve the avialable languages (WARNING : kill the process if it fails !!!)
				$langs = self::retrieveAvailableLanguages($langs_table);
				// If the Back Office is localized
				if( self::$acceptLangInBO )
					$adminLangs = self::retrieveAvailableLanguages($langs_table, true);
				$_SESSION[self::$sessionKey] = Array( 
					self::KEY_FRONT_OFFICE => Array(self::KEY_LANGS_LIST => $langs),
					self::KEY_BACK_OFFICE  => Array(self::KEY_LANGS_LIST => $adminLangs)
				);
			}
			// set the default language
			if( $def_lang_code = self::langCodeExists($default_language) )
				self::$defaultLanguageCode = $def_lang_code;
			else
				Common::reportFatalError("[Class Language] The default language code hasn't been found !");
		}
		else
			Common::reportFatalError("[Class Language] The key to store language data into the session is null !");
	}
	
	// Should be called before Lang::init()
	// Used to say which field in the LangTable determine if the language can be used in admin context
	// @param $acceptedForBackOffice_field Field within the MySQL table listing available languages and their codes
	public static function localizeBackOffice($acceptLangInBO = true)
	{
		if( Common::isAdminContext() && $acceptLangInBO )
			self::$currentOffice = self::KEY_BACK_OFFICE;
		self::$acceptLangInBO = $acceptLangInBO;
	}
	
	// Translate a text with the current language
	// @param $text_code Text code to convert
	// @param $apply_text_transformation Uses Text::toXXX() function to convert the text retrieved
	// @param $warning_message Display a warning message if failure (only applicable in local context and when Debug mode is ON)
	// @return Returns the converted text, null if failure (+Warning)
	public static function translate($text_code, $apply_text_transformation = true, $warning_message = true)
	{
		return self::translate_($text_code, null, $warning_message);
	}
	
	// Convert many texts in a row
	// Use the REGEXP Sql cmd to match the key and return all matching keys with the corresponding translation
	// @param $ereg_code RegExp used to filter texts to translate
	// @param $return_array Used for failure case: if true return an empty array, return null otherwise
	// @return Returns an assoc-array[key => value]. An empty array or null if failure (+warning)
	public static function translateKeys($ereg_code, $lang_code = null, $return_array = true)
	{
		if( $langCode = self::getLanguage($lang_code) )
		{
			$sqlQuery = LangDAL::GetMatchingKeys($ereg_code);
			$rows = DB::select($sqlQuery, false);
			if( is_array($rows) )
			{
				$ret = Array();
				foreach($rows as $row)
					$ret[$row->key] = self::translate_($row->key);
				return $ret;
			}
			Common::reportRunningWarning("[Class Language] translateKeys('$ereg_code'): Malformed REGEXP !");
			return ($return_array ? Array() : null);
		}
		Common::reportRunningWarning("[Class Language] Language not found for translateKeys(): `$lang_code` !");
	}
	
	
	// Make easier case when a message to display is supporting a singular/plural text
	// Look at String::setQuantity() for further details.
	// @param $key Language key for translation/localization
	// @param $quantity Number of items for this text. If $quantity is negative same behaviour as if equals to 1 (one)
	// @param $qPattern Quantity pattern (String) used in the localized text that will be remplaced by the quantity
	// @return Returns the localized text converted onto singular/plural form if ressource well formated.
	public static function translateQuantity($key, $quantity, $qPattern = '%')
	{
		$text = self::translate($key);
		return String::setQuantity($text, $quantity, $qPattern);
	}
	
	
	// See DB::getAssocArray() for more details.
	// Here, the values are localization keys. So instead of returning these keys, we save the translated value.
	// @param $sqlQuery SQL query to perfom
	// @param $key name of one of the field returned that will be used as a key for the returned array
	// @param $value The returned array has only the SQL row field as value instead of the full row
	// @param $returnEmptyArrayIfItFails If true returns an empty array if the query fails, instead of null to use foreach() without additional if condition
	// @param $log_error If true and an error occured perform error-reporting actions (display/db-log/email)
	// @return Returns the assoc-array if success, null or an empty array if failure (depending of the optional parameter $returnEmptyArrayIfItFails)
	public static function getAssocArray($sqlQuery, $key, $value, $returnEmptyArrayIfItFails = true, $log_error = true)
	{
		if( $rows = DB::getAssocArray($sqlQuery, $key, $value, $returnEmptyArrayIfItFails, $log_error) )
		{
			$ret = Array();
			foreach( $rows as $key => $val )
				$ret[$key] = self::translate($val);
			return $ret;
		}
		else
			return rows;
	}
	
	
	// Get the current context language: this doesn't mean the language saved into the session, but the one used to display this page
	// @return Returns the current language code
	public static function getCurrentLanguage()
	{
		if( ! SETTINGS_MULTI_LINGUAGES )
			return DEFAULT_LANGUAGE;
		return self::$currentLanguageCode;
	}
	
	
	// Get the session language (the one registered in the session)
	// @return Returns the session language code
	public static function getSessionLanguage()
	{
		if( self::$sessionKey )
			return $_SESSION[self::$sessionKey][self::$currentOffice][self::KEY_CURRENT_LANG];
	}
	
	// Check if the language parameter has been given
	// @return Returns true if the request Url contains the language code
	public static function isLanguageSet()
	{
		return self::$isLanguageSet;
	}
	
	
	// Compute the languages for the session and the current page
	// We need the two $_GET keys to define the current language and the default code
	// WARNING : be aware that even CSS/JS/IMG will go through this function too
	// If current language is set to French ('fr'), change the date() rendering lang
	// @param $change_lang_code $_GET key used to set the language into the session
	// @param $current_lang_code $_GET key used to set the language for this page
	// @return Fatal error message if enable to determine the language
	public static function computeLanguage($change_lang_code, $current_lang_code)
	{
		// retrieve the current registered language
		$session_code = self::getSessionLanguage();
		// change langue or force it, depending of the existance of $_GET($change_lang_code] (eg: for JS files)
		if( Vars::defined($current_lang_code)  
			&& self::setContextLanguage(Vars::get($current_lang_code), Vars::defined($change_lang_code) || !$session_code) ) // Register the language only if change_lang key is defined or if none language is registered yet
		{ self::$isLanguageSet = true; }
		// get the registered language (in the session)
		elseif( $session_code && self::setContextLanguage($session_code) )
		{ self::$isLanguageSet = true; }
		// try to set it with the one of the browser languages or the default language
		else
		{
			$res = false;
			foreach(Client::getSupportedLanguages() as $lang_code)
				if( $code = self::langCodeExists($lang_code) )
					if( $res = self::setContextLanguage($code, true) )
						break;
			if( ! $res && ! self::setContextLanguage(self::$defaultLanguageCode, true) )
				Common::reportFatalError("[Class Language] Unable to choice a language to use !");
		}
		Vars::remove($change_lang_code);
		Vars::remove($current_lang_code);
		
		// Change the language to french for the date display
		if( self::getCurrentLanguage() == 'fr' )
			setlocale (LC_TIME, 'fr_FR', 'fra');
	}


	// Get the session array containing the available languages
	// @param $isAdminTable if null, returns the array corresponding to the current page viewed BO/FO. Otherwise force it to the BO or FO regarding to its value.
	// @return Returns an array containing language codes
	public static function getAvailableLanguages($isAdminTable = null)
	{
		if( $languages = self::getLanguageArray($isAdminTable) )
		{
			$codes = Array();
			foreach($languages as $key => $value)
				$codes[] = $key;
			return $codes;
		}
		else
			Common::reportRunningWarning("[Class Language] None language table found !");
	}
	
	// Get the session array containing the other languages available (excluding the current language)
	// @return Returns an array containing other language codes
	public static function getOtherAvailableLanguages()
	{
		if( $languages = self::getLanguageArray(null) )
		{
			$codes = Array();
			foreach($languages as $key => $value)
				if( $key != self::getCurrentLanguage() )
					$codes[] = $key;
			return $codes;
		}
		else
			Common::reportRunningWarning("[Class Language] None language table found !");
	}
	
	// Get the corresponding language code
	// @param $lang_code Language code for the table name to get. If null, works with the current language
	// @return Returns the associated table name, or null
	public static function getLanguage($lang_code = null)
	{
		if( is_null($lang_code) )
			return self::$currentLanguageCode;
		$langs = self::getAvailableLanguages();
		if( in_array($lang_code, $langs) )
			return $lang_code;
		return null;
	}
	
	
	/******************************************
	 *         links static functions         *
	 ******************************************/
	
	private static function createLink($page, $contentType, $params, $lang = null)
	{
		$page = Media::createLink($page, $contentType, $params);
		if( is_null($lang) )
			$lang = self::getCurrentLanguage();
		return Common::getPageURL("$lang/$page");
	}
	
	// Create the Url to change language
	// @param $langCode New language code
	// @param $params Parameters to add to the links. If null, $_GET parameters are added
	// @return Returns an Url to go to a page ith another language
	public static function changeLanguageLink($langCode, $params = null)
	{
		if( is_null($params) )
			$params = $_GET;
		$page = ContextDAL::changeLanguageLink(Context::getPageCode(), $langCode);
		return self::createLink($page, Page::KEY_HTML, $params, "to_$langCode"); 
	}
	
	// Create a localized HTML Url
	// @param $page Page code
	// @param $params Parameters to add to this link
	// @return Returns a link for an HTML page
	public static function HTML($page, $params = null)
	{
		return self::createLink($page, Page::KEY_HTML, $params);
	}
	
	// Create a localized AJAX Url
	// @param $page Page code
	// @param $params Parameters to add to this link
	// @return Returns a link for an HTML page
	public static function AJAX($page, $params = null)
	{
		if( is_null($page) )
			$page = Page::getPageCodeOrName();
		if( ! strstr($page, '.') )
			$page .= ".html";
		return self::createLink($page, Page::KEY_AJAX, $params);
	}
	
	// Create a localized CSS Url
	// @param $page Page code
	// @param $params Parameters to add to this link
	// @return Returns a CSS link 
	public static function CSS($page, $params = null)
	{
		return self::createLink($page, Page::KEY_CSS, $params);
	}
	
	// Create a localized JavaScript Url
	// @param $page Page code
	// @param $params Parameters to add to this link
	// @return Returns a JavaScript link
	public static function JS($page, $params = null)
	{
		return self::createLink($page, Page::KEY_JS, $params);
	}
	
	// Create a localized image Url
	// @param $page Page code
	// @param $params Parameters to add to this link
	// @param $lang Language code. If null, the current language is used
	// @return Returns a link for a picture
	public static function IMAGE($page, $params = null, $lang = null)
	{
		$lang_img = DIR_IMAGES . self::getCurrentLanguage() . '/'; 
		return self::createLink(Media::IMAGE_($page, Array($lang_img, DIR_IMAGES)), Page::KEY_IMAGE, $params, $lang);
	}
	
	// Create a localized attachment Url
	// @param $page Page code
	// @param $params Parameters to add to this link
	// @return Returns an attachment	link
	public static function DATA($page)
	{
		return self::createLink($page, Page::KEY_DATA, null, null);
	}
	
	
	// Used by HTML and JavaScript pages. _
	// This function is usefull to add localized texts to the JS Messages object to display localized texts
	// @param $keys Array or imploded keys (by '|' or ':')
	// @param $tab Left padding for the command (for nice indentation)
	// @return Write the JavaScript command to localize these texts
	public static function addMessages($keys, $tab = '') // keys should be separated by ':' or '|' (associated value should be a JS type)
	{
		$regexp = ( is_array($keys) ) ? implode('|', $keys) : str_replace(':', '|', $keys);
		$vals = self::translateKeys(str_replace('.', "\.", "^(".$regexp.")$"));
		foreach($vals as $key => $value)
			Media::addEscapedMessage($key, $value, $tab);
	}
	
	// Get the language depending of the current language: calls Common::getDate{Fr or Us}()
	// @param $dtime A date, or the current datetime if null
	// @param $fulltime include time too or only the day ?
	// @param $longFormat Date in text format instead of only digits
	// @return Returns converted time onto French or US format
	public static function getDate($dtime = null, $fulltime = false, $longFormat = false)
	{
		if( self::getCurrentLanguage() == self::FRENCH_LANG )
			return Common::getDateFr($dtime, $fulltime, $longFormat);
		else
			return Common::getDateUS($dtime, $fulltime, $longFormat);
	}
	
	
	/******************************************
	 *            private functions           *
	 ******************************************/
	
	// try to switch to the language designated by the language code
	private function switchTo_($language, $calling_func)
	{
		if( $language && ($code = self::langCodeExists($language)) )
		{
			$this->languageCode = $code;
			return true;
		}
		elseif( ! is_null(self::$currentLanguageCode) )
		{
			$this->languageCode = self::$currentLanguageCode;
			return true;
		}
		else
			Common::reportRunningWarning("[Class Language] '$calling_func': the language code '$language' doesn't exist !");
		return false;
	}
	
	
	/******************************************
	 *        private static functions        *
	 ******************************************/
	
	// translate the value code using the given code language
	// if the SQL query fails the log system will be used (email + SQL table)
	// '%other_key%' will refer to another text (same language)
	// '¤data_key¤' will refer to a data key (from the data table)
	// "HTML('link','text')" will be converted into an HTML link (with language support)
	// "DATA('link','text')" will be converted into a DATA link (without language support)
	private static function translate_($text_code, $lang_code = null, $apply_text_transformation = true, $warning_message = true)
	{
		$STR = 'a-zA-Z0-9à-ü\_\-\/\. \?\=\#\&;';
		$KEY = 'a-zA-Z0-9\\_\\.';
		
		if( $langCode = self::getLanguage($lang_code) )
		{
			$sqlQuery = LangDAL::TranslateKey($text_code, $langCode, "`content`, `type`");
			if( $row = DB::getOneRow($sqlQuery) )
			{
				$text = $row->content;
				// translate inner keys
				while( $nb = preg_replace("/%([".$KEY."]*)%/i", $text, $res) )
					$text = str_replace($res[0], self::translate_($res[1], $lang_code, $apply_text_transformation, $warning_message), $text);
				// peek data
				while( $nb = preg_replace("/¤([".$KEY."]*)¤/i", $text, $res) )
					$text = str_replace($res[0], DataDAL::getDataValue($res[1]), $text);
				// create HTML links
				preg_match_all("#HTML\(\'([".$STR."$]*)\',(\\s*)\'([".$STR."$]*)\'\)#", $text, $out);
				if(count($out) == 4)
					foreach($out[0] as $key => $val)
						$text = str_replace($val, "<a href=\"".self::HTML($out[1][$key])."\">".$out[3][$key]."</a>", $text);
				// create HTML links
				preg_match_all("#IMAGE\(\'([".$STR."$]*)\'\)#", $text, $out);
				if(count($out) == 4)
					foreach($out[0] as $key => $val)
						$text = str_replace($val, "<img src=\"".self::IMAGE($out[1][$key])."\" />", $text);
				// create DATA links
				preg_match_all("#DATA\(\'([".$STR."$]*)\',(\\s*)\'([".$STR."$]*)\'\)#", $text, $out);
				if(count($out) == 4)
					foreach($out[0] as $key => $val)
						$text = str_replace($val, "<a href=\"".Media::DATA($out[1][$key])."\">".$out[3][$key]."</a>", $text);
				// render the text in the right form
				return $apply_text_transformation ? String::convert($text, $row->type) : $text;
			}
			elseif( ! is_null($lang_code) && $lang_code != self::$defaultLanguageCode )
			{
				// if none value is defined for this language, we try to get the value corresponding to the default language
				return self::translate_($text_code, self::$defaultLanguageCode, $apply_text_transformation, $warning_message);
			}
			elseif( $warning_message )
			{
				Common::reportRunningWarning("[Class Language] translate('$text_code'): key not found !");
				return null;
			}
		}
		elseif( $warning_message )
		{
			Common::reportRunningWarning("[Class Language] translate('$text_code') in this language: `$lang_code` !");
			return null;
		}
	}
	
	// Check if the table exists. Otherwise raise an warning message and ends
	private static function checkExistingTable($tableSQL)
	{
		if( ! DB::isExistingTable($tableSQL) )
			Common::reportFatalError("[Class Language] The SQL table `$tableSQL` doesn't exist !");
	}
	
	
	// search for languages in the MySQL table and store them into the session
	private static function retrieveAvailableLanguages($langs_table, $boFilter = false)
	{
		// retrieves the languages
		if( $langs = DB::getAssocArray(LangDAL::GetLanguages($boFilter), 'langID', 'langName') )
			return $langs;
		// if no language found, we raise a terminating error
		Common::reportFatalError("[Class Language] The SQL language table `$langs_table` is empty !");
	}
	
	
	// Returns the session array containing the languages available
	private static function getLanguageArray($adminLangs = null)
	{
		if( is_null($adminLangs) )
			$key = self::$currentOffice;
		else
			$key = $adminLangs ? self::KEY_BACK_OFFICE : self::KEY_FRONT_OFFICE;
		if( self::$sessionKey )
			return $_SESSION[self::$sessionKey][$key][self::KEY_LANGS_LIST];
	}

	
	// try to change the current context language with the given language code
	// if '$session_registration' is true, save the code to the Session
	private static function setContextLanguage($code, $session_registration = false)
	{
		$langs = self::getLanguageArray();
		if( $langs && isset($langs[$code]) )
		{
			self::$currentLanguageCode = $code;
			if( $session_registration )
				$_SESSION[self::$sessionKey][self::$currentOffice][self::KEY_CURRENT_LANG] = $code;
			return true;
		}
	}
	
	
	// the sessions data should be filled !!!
	private static function langCodeExists($code)
	{
		$code = strtolower($code);
		foreach(self::getLanguageArray() as $key => $value)
			if( strtolower($key) == $code )
				return $key;
		return null;
	}
};







/****************************************************
//
// Generation script for the Main Table
// --------------------------------------
//
; You can add every field you want !!!
;
CREATE TABLE `language` (
  `langID` varchar(3) NOT NULL,
  `langName` varchar(10) NOT NULL,
  `isAdminLang` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`langID`),
  UNIQUE KEY `keys` (`langName`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `language` (`langID`, `langName`, `isAdminLang`) VALUES 
('en', 'English', 1),
('fr', 'Français', 1);

//
// Generation script for a Lang Table
// --------------------------------------
//
CREATE TABLE IF NOT EXISTS `table_xxx` (
  `key` varchar(30) NOT NULL,
  `string` text NOT NULL,
  `type` enum('html','text','js','int','float') NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

//
*****************************************************/
?>