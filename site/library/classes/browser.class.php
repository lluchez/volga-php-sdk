<?php
/*****************************************************************

    File name: browser.php
    Author: Gary White
    Last modified: November 10, 2003
    
    **************************************************************

    Copyright (C) 2003  Gary White
    
    This program is free software; you can redistribute it and/or
    modify it under the terms of the GNU General Public License
    as published by the Free Software Foundation; either version 2
    of the License, or (at your option) any later version.
    
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details at:
    http://www.gnu.org/copyleft/gpl.html

    **************************************************************

    Browser class
    
		@desc-start
    Identifies the user's OS, Browser and browser's version by parsing the HTTP_USER_AGENT string sent to the server
		@desc-end
    
    Typical Usage:
    
        require_once($_SERVER['DOCUMENT_ROOT'].'/include/browser.php');
        $br = new Browser;
        echo "$br->Platform, $br->Name version $br->Version";
    
    For operating systems, it will correctly identify:
        Microsoft Windows
        MacIntosh
        Linux

    Anything not determined to be one of the above is considered to by Unix
    because most Unix based browsers seem to not report the operating system.
    The only known problem here is that, if a HTTP_USER_AGENT string does not
    contain the operating system, it will be identified as Unix. For unknown
    browsers, this may not be correct.
    
    For browsers, it should correctly identify all versions of:
        Amaya
				Chrome
        Galeon
        iCab
        Internet Explorer
            For AOL versions it will identify as Internet Explorer (AOL) and the version
            will be the AOL version instead of the IE version.
        Konqueror
        Lynx
        Mozilla
        Netscape Navigator/Communicator
        OmniWeb
        Opera
        Pocket Internet Explorer for handhelds
        Safari
        WebTV
*****************************************************************/

class Browser
{
	// Vars
	var $Name, $Version, $Platform, $UserAgent;
	var $AOL = false;
	
	// Start the scan/computation of the end-user platform
	public function Browser()
	{
		$agent = $_SERVER['HTTP_USER_AGENT'];
		
		// initialize properties
		$info = Array();
		$info['platform'] = null;
		$info['browser'] = null;
		$info['version'] = null;
		$this->UserAgent = $agent;
		
		// find operating system
		if (preg_match("/win/i", $agent))
			$info['platform'] = "Windows";
		elseif (preg_match("/mac/i", $agent))
			$info['platform'] = "MacOS";
		elseif (preg_match("/linux/i", $agent))
			$info['platform'] = "Linux";
		elseif (preg_match("/Unix/i", $agent))
			$info['platform'] = "Unix";
		elseif (preg_match("@OS/2@i", $agent))
			$info['platform'] = "OS/2";
		elseif (preg_match("/BeOS/i", $agent))
			$info['platform'] = "BeOS";
		elseif (preg_match("/BSD/i", $agent))
			$info['platform'] = "BSD";
		elseif (preg_match("/Sun/i", $agent))
			$info['platform'] = "SunOS";
		elseif (preg_match("/Amiga/i", $agent))
			$info['platform'] = "Amiga";
		
		if (preg_match("/iPhone|iPod/i",$agent))
		{
			$info['browser'] = 'iPhone';
			$info['version'] = '?';
			$info['platform'] = 'Phone OS';
		}
		// test for Opera        
		elseif (preg_match("/opera/i",$agent))
		{
			$val = stristr($agent, "opera");
			if (strstr($val, "/"))
			{
				$val = explode("/",$val);
				$info['browser'] = $val[0];
				$val = explode(" ",$val[1]);
				$info['version'] = $val[0];
			}
			else
			{
				$val = explode(" ",stristr($val,"opera"));
				$info['browser'] = $val[0];
				$info['version'] = $val[1];
			}
		}
		// test for Google Chrome
		elseif(preg_match("/chrome/i", $agent))
		{
			$info['browser']="Chrome";
			$val = stristr($agent, "Chrome");
			$val = explode("/",$val);
			$val = explode(" ",$val[1]);
			$info['version'] = $val[0];
		}
		// test for WebTV
		elseif(preg_match("/webtv/i",$agent))
		{
			$val = explode("/",stristr($agent,"webtv"));
			$info['browser'] = $val[0];
			$info['version'] = $val[1];
		}
		// test for MS Internet Explorer version 1
		elseif(preg_match("/microsoft internet explorer/i", $agent))
		{
			$info['browser'] = "MSIE";
			$info['version'] = "1.0";
			$var = stristr($agent, "/");
			if (preg_replace("/308|425|426|474|0b1/", $var))
				$info['version'] = "1.5";
		}
		// test for NetPositive
		elseif(preg_match("/NetPositive/i", $agent))
		{
			$val = explode("/",stristr($agent,"NetPositive"));
			$info['platform'] = "BeOS";
			$info['browser'] = $val[0];
			$info['version'] = $val[1];
		}
		// test for MS Internet Explorer
		elseif(preg_match("/msie/i",$agent) && !preg_match("/opera/i",$agent))
		{
			$val = explode(" ",stristr($agent,"msie"));
			$info['browser'] = $val[0];
			$info['version'] = $val[1];
		}
		// test for MS Pocket Internet Explorer
		elseif(preg_match("/mspie/i",$agent) || preg_match('/pocket/i', $agent))
		{
			$val = explode(" ",stristr($agent,"mspie"));
			$info['browser'] = "MSPIE";
			$info['platform'] = "WindowsCE";
			if (preg_match("/mspie/i", $agent))
				$info['version'] = $val[1];
			else
			{
				$val = explode("/",$agent);
				$info['version'] = $val[1];
			}
		}
		// test for Galeon
		elseif(preg_match("/galeon/i",$agent))
		{
			$val = explode(" ",stristr($agent,"galeon"));
			$val = explode("/",$val[0]);
			$info['browser'] = $val[0];
			$info['version'] = $val[1];
		}
		// test for Konqueror
		elseif(preg_match("/Konqueror/i",$agent))
		{
			$val = explode(" ",stristr($agent,"Konqueror"));
			$val = explode("/",$val[0]);
			$info['browser'] = $val[0];
			$info['version'] = $val[1];
		}
		// test for iCab
		elseif(preg_match("/icab/i",$agent))
		{
			$val = explode(" ",stristr($agent,"icab"));
			$info['browser'] = $val[0];
			$info['version'] = $val[1];
		}
		// test for OmniWeb
		elseif(preg_match("/omniweb/i",$agent))
		{
			$val = explode("/",stristr($agent,"omniweb"));
			$info['browser'] = $val[0];
			$info['version'] = $val[1];
		}
		// test for Phoenix
		elseif(preg_match("/Phoenix/i", $agent))
		{
			$info['browser'] = "Phoenix";
			$val = explode("/", stristr($agent,"Phoenix/"));
			$info['version'] = $val[1];
		}
		// test for Firebird
		elseif(preg_match("/firebird/i", $agent))
		{
			$info['browser']="Firebird";
			$val = stristr($agent, "Firebird");
			$val = explode("/",$val);
			$info['version'] = $val[1];
		}
		// test for Firefox
		elseif(preg_match("/Firefox/i", $agent))
		{
			$info['browser']="Firefox";
			$val = stristr($agent, "Firefox");
			$val = explode("/",$val);
			$info['version'] = $val[1];
		}
		// test for Mozilla Alpha/Beta Versions
		elseif(preg_match("/mozilla/i",$agent) && preg_match("/rv:[0-9].[0-9][a-b]/i",$agent) && !preg_match("/netscape/i",$agent))
		{
			$info['browser'] = "Mozilla";
			$val = explode(" ",stristr($agent,"rv:"));
			preg_match("/rv:[0-9].[0-9][a-b]/i",$agent,$val);
			$info['version'] = str_replace("rv:","",$val[0]);
		}
		// test for Mozilla Stable Versions
		elseif(preg_match("/mozilla/i",$agent) && preg_match("/rv:[0-9]\.[0-9]/i",$agent) && !preg_match("/netscape/i",$agent))
		{
			$info['browser'] = "Mozilla";
			$val = explode(" ",stristr($agent,"rv:"));
			preg_match("/rv:[0-9]\.[0-9]\.[0-9]/i",$agent,$val);
			$info['version'] = str_replace("rv:","",$val[0]);
		}
		// test for Lynx & Amaya
		elseif(preg_match("/libwww/i", $agent))
		{
			if (preg_match("/amaya/i", $agent))
			{
				$val = explode("/",stristr($agent,"amaya"));
				$info['browser'] = "Amaya";
				$val = explode(" ", $val[1]);
				$info['version'] = $val[0];
			}
			else
			{
				$val = explode("/",$agent);
				$info['browser'] = "Lynx";
				$info['version'] = $val[1];
			}
		}
		// test for Safari
		elseif(preg_match("/safari/i", $agent))
		{
			$info['browser'] = "Safari";
			$info['version'] = "";
		}
		// remaining two tests are for Netscape
		elseif(preg_match("/netscape/i",$agent))
		{
			$val = explode(" ",stristr($agent,"netscape"));
			$val = explode("/",$val[0]);
			$info['browser'] = $val[0];
			$info['version'] = $val[1];
		}
		elseif(preg_match("/mozilla/i",$agent) && !preg_match("/rv:[0-9]\.[0-9]\.[0-9]/i",$agent))
		{
			$val = explode(" ",stristr($agent,"mozilla"));
			$val = explode("/",$val[0]);
			$info['browser'] = "Netscape";
			$info['version'] = $val[1];
		}

		// clean up extraneous garbage that may be in the name
		$info['browser'] = preg_replace("/[^a-z,A-Z]/", "", $info['browser']);
		// clean up extraneous garbage that may be in the version        
		$info['version'] = preg_replace("/[^0-9,.,a-z,A-Z]/", "", $info['version']);

		// check for AOL
		if (preg_match("/AOL/i", $agent))
		{
			$var = stristr($agent, "AOL");
			$var = explode(" ", $var);
			$this->AOL = preg_replace("/[^0-9,.,a-z,A-Z]/", "", $var[1]);
		}

		// finally assign our properties
		$this->Name = $info['browser'];
		$this->Version = $info['version'];
		$this->Platform = $info['platform'];
	}
}
?>