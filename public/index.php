<?php

$_SERVER["REQUEST_URI"] = str_replace('index.php','',$_SERVER["REQUEST_URI"]);




$serverName = $_SERVER['SERVER_NAME'];

$locale = 'es_AR';
$currency = 'ARS';
$timezone = "America/Argentina/Buenos_Aires";
$id_pais = 14;
$id_pais_origem = 2;
$url_portal = "//atletas-info.com";
$sigla = "ar";

$url_site = "http://{$serverName}";
$url_admin = "http://zend.teste/";

define("LOCALE",$locale);
define("PAIS",$id_pais);
define("ORIGEM",$id_pais_origem);
define("CURRENCY",$currency);
define("TIMEZONE",$timezone);
define("URL_ADM",$url_admin);
define("URL_SITE",$url_site);
define("URL_PORTAL",$url_portal);



// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);

if($_GET['esdras'] == 'error') 
{
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

$application->bootstrap()->run();