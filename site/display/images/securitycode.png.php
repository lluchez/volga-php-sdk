<?php

define('LEN_SECURECODE', 7);

function CreateBlankPNG($w, $h)
{
	$im = imagecreatetruecolor($w, $h);
	imagesavealpha($im, true);
	$transparent = imagecolorallocatealpha($im, 0, 0, 0, 127);
	imagefill($im, 0, 0, $transparent);
	return $im;
}

function ImageRandomColorAllocate($img)
{
	$quota = 350;
	$colors = Array();
	for($i=0; $i<3; $i++)
	{
		$max = ($quota>255) ? 255 : $quota;
		$c = rand(0,$max);
		$quota -= $c;
		$colors[$i] = $c;
	}
	return ImageColorAllocate($img, $colors[1], $colors[2], $colors[0]);
}

function generateSecurityImage($length = LEN_SECURECODE, $pathimg = null)
{
	header ("Content-type: image/png");
	if( is_null($pathimg) )
		$pathimg = DIR_IMAGES.'bg_security_code/';
	
	// generating the password
	$ciphers = 'abcdefghjkmnpqrstuvwxyz';
	$ciphers = strToUpper($ciphers).'2345689';
	$password = '';
	for($ct=0; $ct<$length; $ct++)
		$password .= $ciphers{mt_rand()%strlen($ciphers)};

	//Select random background image
	$bgurl = $pathimg.'bg'.rand(1,7).'.png';
	$img_bkgd = imageCreateFromPNG($bgurl);
	$bckg_info = getimagesize($bgurl);

	
	$img_ciphers = CreateBlankPNG($bckg_info[0], $bckg_info[1]);
	$font = DIR_FONTS."HARNGTON.TTF";
	$offset = 0;
	for($i=0; $i<$length; $i++)
	{
		$offset += rand(2,4);
		$textstr = $password{$i};
		$size = 18;
		$angle = rand(-20, 20);
		$color = ImageRandomColorAllocate($img_ciphers);
		$textsize = imagettfbbox($size, $angle, $font, $textstr);
		$txtW = abs($textsize[2]-$textsize[0]);
		$txtH = abs($textsize[5]-$textsize[3]);
		$marginH = ($bckg_info[1]-$txtH)/2;
		ImageTTFText($img_ciphers, $size, $angle, $offset,$size+rand(1, $marginH), $color, $font, $textstr);
		$offset += $txtW;
	}
	$marginW = $bckg_info[0]-$offset;
	imagecopy($img_bkgd, $img_ciphers, $marginW/2,0, 0,0, $bckg_info[0],$bckg_info[1]);
	imagedestroy($img_ciphers);
	
	imagePNG($img_bkgd);
	imagedestroy($img_bkgd);
	return $password;
}




$pass = generateSecurityImage();

//$_SESSION[SESSION_SECURITY_CODE] = md5($pass);

?>