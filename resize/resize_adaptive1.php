<?php
/**
 * PhpThumb Library Example File
 *
 * This file contains example usage for the PHP Thumb Library
 *
 * PHP Version 5 with GD 2.0+
 * PhpThumb : PHP Thumb Library <http://phpthumb.gxdlabs.com>
 * Copyright (c) 2009, Ian Selby/Gen X Design
 *
 * Author(s): Ian Selby <ian@gen-x-design.com>
 *
 * Licensed under the MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @author Ian Selby <ian@gen-x-design.com>
 * @copyright Copyright (c) 2009 Gen X Design
 * @link http://phpthumb.gxdlabs.com
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @version 3.0
 * @package PhpThumb
 * @subpackage Examples
 * @filesource
 */

require_once 'ThumbLib.inc.php';
error_reporting(0);

$w_img = $_GET[w];
$h_img = $_GET[h];
$pathe_img = "".$_GET[p_img];
$pos = strpos($pathe_img, "_thumb".$w_img."x".$h_img);
//$pathe_thumb = str_replace("http://superativo.approval.adm.br/admin", "", $pathe_img);
$pathe_thumb = str_replace(".jpg", "_thumb".$w_img."x".$h_img.".jpg", $pathe_img);

//echo $pathe_img."<br>".$pathe_thumb;die();

//chown($pathe_img, 'nobody');

//var_dump(get_current_user());die();

if ($pos === false) {
	//echo $path_thumb;
 	//echo "<br>A thumb nao existe";
 	try
	{
    /*$thumb = PhpThumbFactory::create($pathe_img);
 	$thumb->adaptiveResize($w_img, $h_img);
 	$thumb->save($pathe_thumb);*/
 	$thumb = PhpThumbFactory::create("$pathe_img");
	$thumb->adaptiveResize(300, 300);
	$thumb->save($pathe_img, 'png');
	//Doo_Chmod($pathe_img, "777");
 	//$thumb->show();
	}
	catch (Exception $e)
	{
	     // handle error here however you'd like
		echo($e);
	}
} else {
    echo $pathe_img;
}
//echo $path_img;die;
?>
