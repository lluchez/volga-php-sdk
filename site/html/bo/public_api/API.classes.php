<?php


class APIFunction
{
	public $name, $args;
	protected $className, $desc, $is_static = false, $inherited = false;
	
	
	public function APIFunction($line, $file, $className, $comments)
	{
		$this->className = $className;
		$this->is_static = strstr($line, 'static ') ? true : false;
		$this->desc      = $comments;
		if( preg_match("/function ([a-z0-9_]+)(?:\s*)\(([^\{]*)/i", trim($line), $matches) )
		{
			$this->name = $matches[1];
			$this->args = substr($matches[2], 0, 0-strlen(strrchr($matches[2], ')')));
		}
		else
			die("Unable to parse the function: $line (file: '$file')");
	}
	
	public function getFullName($args = true)
	{
		$name = $this->name;
		if( $this->is_static )
			$name = $this->className.'::'.$name;
		if( $args )
			$name .= '(<args>' . $this->args . '</args>)';
		return $name;
	}
	
	public function getAnchorName()
	{
		$name = $this->name;
		$name = $this->className. ($this->is_static ? '::' : '.' ) .$name;
		return $name;
	}
	
	public function getComments()
	{
		$buffer = '<p>';
		$params = Array();
		$return = null;
		$code_section = false;
		$phpBuffer = '';
		foreach($this->desc as $line)
		{
			if( preg_match("/\@param (\\$[a-z0-9\_]+) (.*)$/i", $line, $matches) )
				$params[] = "<li><label>".$matches[1]."</label>: ".$matches[2]."</li>";
			elseif( preg_match("/\@return (.*)$/", $line, $matches) )
				$return .= $matches[1]. ' ';
			elseif( preg_match("/^\[code\]$/", $line) ) {
				$buffer .= "</p><pre><b>Code:</b>\n"; $code_section = true; $phpBuffer = '';
			} elseif( preg_match("/^\[\/code\]$/", $line) ) {
				$buffer .= $phpBuffer."</pre><p>"; $code_section = false; // can use a function to add color to PHP code
			} elseif( $code_section ) {
				$b = (substr($line, -2) == ' _') ? substr($line, 0, -2).' ' : "$line\n";
				$phpBuffer .= ($b{0} == '_' || $b{0} == '.') ? ' '.substr($b, 1) : $b;
			} else {
				$buffer .= (substr($line, -2) == ' _') ? substr($line, 0, -2).' ' : "$line<br />";
			}
		}
		$buffer .= "</p>";
		if( count($params) )
			$buffer .= "<h4>Parameters:</h4><ul>".implode('', $params)."</ul>";
		if( $return )
			$buffer .= "<h4>Returns:</h4><p class='return'>$return</p>";
		return $buffer;
	}
	
	public function inherits($className)
	{
		if( ! $this->is_static ) // keep this test !
			$this->className = $className;
		$this->inherited = true;
	}
	
	public function isInherited() { return $this->inherited; }
	
	public function __clone() { } 
	
};


class APIClass
{
	public $phpFile, $name = null, $comments = null, $isInterface = false;
	public $inherits = null, $constants = Array(), $functions = Array(), $classesNeeded = Array();
	
	private $comment_buffer = Array();

	public function APIClass($fileName, $content, $otherClasses)
	{
		$this->phpFile = $fileName;
		$q = "(?:\'|\")";
		$n = "([^(\'|\")]*)";
		
		$comment_opened = false;
		foreach($content as $line)
		{
			$line = trim($line);
			if( $comment_opened ) // ------ comments already opened ------
			{
				$line2 = trim(preg_replace("/(\*\/)$/", '', $line));
				if( $line != $line2 )
					$comment_opened = false;
				$this->addToCommentBuffer($line2);
			}
			elseif( preg_match("#^(\/\/)(.*)#", $line, $matches) || preg_match("#^(\/\*\*)(.*)#", $line, $matches) ) // ------ comments opening ------
			{
				$line = trim($matches[2]);
				if( $matches[1] == '//' )
				{
					$this->addToCommentBuffer($line);
				}
				else
				{
					$comment_opened = true;
					$line2 = trim(preg_replace("/(\*\/)$/", '', $line));
					if( $line != $line2 )
						$comment_opened = false;
					$this->addToCommentBuffer($line2);
				}
			}
			elseif( preg_match("#(.*)(\*\/)$#", $line, $matches) ) // ------ comments closing ------
			{
				$this->addToCommentBuffer(trim($matches[1]));
			}
			else // ------ search for stuffs ------
			{
				if( preg_match("/^(class|interface) ((?:[a-z0-9])+)(?: extends ((?:[a-z0-9])+))?/i", $line, $matches) )
				{
					if( ! is_null($this->name) )
						die("Error in class: '$fileName'. It should contain the class separator comment !");
					$this->isInterface = preg_match('/^interface$/i', $matches[1]);
					$this->name = $matches[2];
					$this->inherits = $matches[3];
					$this->comments = $this->comment_buffer;
					$this->comment_buffer = Array();
					if( $this->inherits )
					{
						foreach($otherClasses as $otherClass)
						{
							if( $otherClass->name == $this->inherits )
							{
								foreach($otherClass->functions as $fct)
								{
									if( $fct->name != $otherClass->name )
									{
										$newFunction = clone $fct;
										$newFunction->inherits($this->name);
										$this->functions[] = $newFunction;
									}
								}
							}
						}
					}
				}
				elseif( preg_match("/define\($q$n$q,(?:\s)*$q?$n$q?\)/", $line, $matches) )
				{
					$this->constants[$matches[1]] = $matches[2];
				}
				elseif( preg_match("/^(?:[a-zA-Z0-9\s]* )?function ([^_])/", $line) && ! preg_match('/(private|protected)\s/', $line) )
				{
					$this->functions[] = new APIFunction($line, $fileName, /*$this->isInterface ? substr($this->name,1) :*/ $this->name, $this->comment_buffer);
					$this->comment_buffer = Array();
				}
				elseif( preg_match("/([A-Z0-9]+)\:\:/i", $line, $matches) )
				{
					$this->classesNeeded[$matches[1]] = true;
				}
				elseif( $line )
				{
					$this->comment_buffer = Array();
				}
			}
			
		}
		
		unset($this->classesNeeded[$this->name]);
		unset($this->classesNeeded['self']);
		$this->classesNeeded = array_keys($this->classesNeeded);
		sort($this->classesNeeded);
	}
	
	
	
	
	public function getComments()
	{
		$buffer = Array();
		$save = false;
		foreach($this->comments as $line)
		{
			if( strstr($line, '@desc-start') )
				$save = true;
			elseif( strstr($line, '@desc-end') )
				$save = false;
			elseif( $save )
				$buffer[] = (substr($line, -2) == ' _') ? substr($line, 0, -2) : "$line<br />";
		}
		return implode(' ', $buffer);
	}
	
	
	private function addToCommentBuffer($line)
	{
		$line2 = preg_replace("/((\*){2,})/", '', $line);
		$line2 = trim(preg_replace("/(\*)$/", '', $line2));
		if( $line2 != $line )
			return;
		$line = trim(preg_replace("/^(\*)/", '', $line2));
		
		if( $line )
			$this->comment_buffer[] = String::toText($line);
	}
	
	public function __clone()
	{
		$this->constants 			= clone($this->constants);
		$this->functions 			= clone($this->functions);
		$this->classesNeeded  = clone($this->classesNeeded);
	}
	
};


?>