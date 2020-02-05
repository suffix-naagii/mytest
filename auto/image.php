<?php
	session_start();
	$scode = generateRandomString(6);
	
	$_SESSION['security_number']=md5($scode);
	setcookie('sc_',$_SESSION['security_number'],time()+60*10);

	$img=imagecreatefromjpeg("resources/images/texture.jpg");
	$security_number = empty($scode) ? 'error' : $scode;
	$image_text=$security_number;
	$red=rand(0,255);
	$green=rand(0,255);
	$blue=rand(0,255);
	$text_color=imagecolorallocate($img,100,100,100);
	$text=imagettftext($img,16,rand(-10,10),rand(5,15),rand(25,35),$text_color,"fonts/courbd.ttf",$image_text);
	header("Content-type:image/jpeg");
	header("Content-Disposition:inline ; filename=secure.jpg");
	imagejpeg($img);
	imagedestroy($img);

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}
?>