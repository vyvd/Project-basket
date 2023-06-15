<?php
include __DIR__.'/../../../classes/idiorm.class.php';
include_once 'db-connect.php';
include_once 'access-functions.php';
sec_session_start();


// ORM setup
//define("DB_HOST", "localhost");     // The host you want to connect to.
//define("DB_USER", "staging_nsaukrebuild");    // The database username.
//define("DB_PASSWORD", "i{1X}Rcer-A,");    // The database password.
//define("DB_DATABASE", "staging_nsaukrebuild");    // The database name.

define("DB_HOST", getenv('DB_HOST'));     // The host you want to connect to.
define("DB_USER", getenv('DB_USERNAME'));    // The database username.
define("DB_PASSWORD", getenv('DB_PASS'));    // The database password.
define("DB_DATABASE", getenv('DB_NAME'));    // The database name.
// configure ORM
ORM::configure('mysql:host='.DB_HOST.';dbname='.DB_DATABASE);
ORM::configure('username', DB_USER);
ORM::configure('password', DB_PASSWORD);
ORM::configure('driver_options', array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
ORM::configure('error_mode', PDO::ERRMODE_WARNING);
ORM::configure('caching', true);
ORM::configure('caching_auto_clear', true);

define("DATE_NEW_SITE_LIVE", "2021-05-09 00:00:00");
define("SITE_URL", "http://rebuild.newskillsacademy.co.uk/");

if (login_check($mysqli) == true) {
    $logged = 'in';
	$_SESSION['loggedin']='1';
	$userid = $_SESSION['user_id'];
	$fullname = $_SESSION['fullname'];
	if(isset($_SESSION['owner']) && $_SESSION['owner']=='') {
		$owner = $_SESSION['owner'];
	} else { 
		$owner = $userid;
	}
	$get_access_level = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT admin_user FROM ap_members WHERE id=$owner"));
	$admin_user = $get_access_level['admin_user'];

} else {
	$logged = 'out';
	$_SESSION['loggedin']='0';
	header('Location: index');
}
/* ===========================================
		Date Period Filter 
   	========================================= */
if(isset($_SESSION['start_date'])){$start_date = $_SESSION['start_date'];}
if(isset($_SESSION['end_date'])){$end_date = $_SESSION['end_date'];}
if(empty($start_date)){$start_date = date('Y-m-d', strtotime('today - 364 days'));}
if(empty($end_date)){$end_date= date('Y-m-d', strtotime('today + 1 day'));}

/* ===========================================
		Langauge Support  
   	========================================= */
	if(isset($_SESSION['language'])){
		$language = $_SESSION['language'];
	}
	if(empty($language)){
		$language='en'; 
		$_SESSION['language'] = $language;
	}
	include($_SERVER['DOCUMENT_ROOT'].'/affiliates/lang/'.$language.'.php'); 
	if($_SERVER['REMOTE_ADDR'] == '127.0.0.1') {
	    ini_set('display_errors', true);
	    ini_set('display_startup_errors', true);
	    error_reporting(E_ALL);
	}
