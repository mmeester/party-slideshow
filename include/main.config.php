<?php 

session_start();

$GLOBALS['debug'] = true;

// Show errors only on development sites, but enable error reporting for logging
if(isset($GLOBALS['debug']) && $GLOBALS['debug']===true)
{
	ini_set('display_errors', true);
	error_reporting(E_ALL | E_STRICT);
}
else {
	ini_set('display_errors', false);
	error_reporting(E_ALL ^ E_NOTICE);
}

$folder = '';
$GLOBALS['document_root'] = getenv("DOCUMENT_ROOT").$folder;
$GLOBALS['url'] = $_SERVER['SERVER_NAME'].$folder;
$GLOBALS['domain'] = '.'.$GLOBALS['url'].$folder;

date_default_timezone_set('Europe/Amsterdam');

// clang token
/* define('CLANG_TOKEN', ""); */

//Facebook variables
$GLOBALS['facebook']['app_id'] = "135643783296217";
$GLOBALS['facebook']['app_secret'] = "104701a5908b249b5666c0605f2d798a";

$GLOBALS['facebook']['extended_accestoken'] = 'CAAB7XgeE9NkBAGzU7FGihMhFxroce0QFMykzqYbJ8kLgGKrFG6BHUbgOGsrlP7gJ1yi6u3QZBr7sxjfqjT8EdjK7pZAnF3fOsUD6HEk7CIZBZCPGsvYwmPiNO2NPJuF2OHLRP5neOXLl6zq9CSZBt';

$args=array();
$args['access_token'] = $GLOBALS['facebook']['extended_accestoken'];

// include autoloading class
require_once($GLOBALS['document_root'] . '/include/classes/Autoloading.php');
$autoloader = new ClassAutoloader();

// include functions
require_once($GLOBALS['document_root'] . '/include/functions.inc.php');

// Set the magic word for all hashing, change this and everyone needs to change its password
define('MAGIC_WORD', '');

require_once('db.config.php');

// Setup Smarty
$smarty = new Smarty();
//$smarty->force_compile = true;
$smarty->debugging = false;
$smarty->caching = false;

$smarty->assign('url', $GLOBALS['url']);
$smarty->assign('fb_id', $GLOBALS['facebook']['app_id']);


?>