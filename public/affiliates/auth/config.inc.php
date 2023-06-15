<?php
/*require __DIR__ . '/../../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/../../');
$dotenv->load();

if(!defined('ENVIRONMENT_LOCAL')) {
    define('ENVIRONMENT_LOCAL', 'local');
}
if(!defined('ENVIRONMENT_STAGE')) {
    define('ENVIRONMENT_STAGE', 'stage');
}
if(!defined('ENVIRONMENT_PROD')) {
    define('ENVIRONMENT_PROD', 'prod');
}
if(!defined('ENVIRONMENT')) {
    define('ENVIRONMENT', getenv('ENVIRONMENT'));
}
=
if (ENVIRONMENT === ENVIRONMENT_LOCAL) {

    define('DEBUG', true);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

} else {

    error_reporting(0);
    @ini_set('display_errors', 0);
    define('DEBUG', false);

}*/

define('AFFILIATES_ROOT_DIR', __DIR__);
require AFFILIATES_ROOT_DIR . '/../../../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(AFFILIATES_ROOT_DIR, '/../../../.newskills-env');
$dotenv->load();
define('DEBUG', true);
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

$protocol = 'http://';

if( isset($_SERVER['HTTPS'] ) ) {
    $protocol = 'https://';
}

$affiliate_base_url = $protocol.$_SERVER['HTTP_HOST'].'/affiliates/';


/*
if($_SERVER['REMOTE_ADDR'] == '127.0.0.1') {
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
}
*/



function credentials_wp($wp_config) {
    //$wp_config = $cpanel.'/public_html/wp-config.php';
    $get_creds_file = file_get_contents($wp_config);
    $data_db_name = preg_match_all('#define\(\'DB_NAME\',\s*\'(.*?)\'\);#', $get_creds_file, $matches_name, PREG_SET_ORDER);
    $data_db_user = preg_match_all('#define\(\'DB_USER\',\s*\'(.*?)\'\);#', $get_creds_file, $matches_user, PREG_SET_ORDER);
    $data_db_pass = preg_match_all('#define\(\'DB_PASSWORD\',\s*\'(.*?)\'#', $get_creds_file, $matches_pass, PREG_SET_ORDER);
    $table_prefix = preg_match_all('#\$table_prefix\s*=\s*\'(.*?)\'\s*;#', $get_creds_file, $matches_prefix, PREG_SET_ORDER);
    $the_data_name = end($matches_name);
    $the_data_user = end($matches_user);
    $the_data_pass = end($matches_pass);
    $the_data_prefix = end($matches_prefix);
    $db_name = $the_data_name[1];
    $db_user = $the_data_user[1];
    $db_pass = $the_data_pass[1];
    $table_prefix = $the_data_prefix[1];
    $host = 'localhost';
    return array(
        'db_name' => $db_name,
        'db_user' => $db_user,
        'db_pass' => $db_pass,
        'db_host' => $host
    );
}

//if(ENVIRONMENT != ENVIRONMENT_PROD) {

    define("HOST", getenv('DB_HOST'));     // The host you want to connect to.
    define("USER", getenv('DB_USERNAME'));    // The database username.
    define("PASSWORD", getenv('DB_PASS'));    // The database password.
    define("DATABASE", getenv('DB_NAME'));    // The database name.
    define("CAN_REGISTER", "any");
    define("DEFAULT_ROLE", "member");

//} else {
//
//    $data_creds = credentials_wp($_SERVER['DOCUMENT_ROOT'].'/wp-config.php');
//    define("HOST", $data_creds['db_host']);     // The host you want to connect to.
//    define("USER", $data_creds['db_user']);    // The database username.
//    define("PASSWORD", $data_creds['db_pass']);    // The database password.
//    define("DATABASE", $data_creds['db_name']);    // The database name.
//    define("CAN_REGISTER", "any");
//    define("DEFAULT_ROLE", "member");
//
//}


define("SECURE", FALSE);    // FOR DEVELOPMENT ONLY!!!!

//$DOMAIN = $_SERVER['SERVER_NAME'];
$DOMAIN = $_SERVER['HTTP_HOST'];
//SET TO THE NAME OF THE FOLDER YOUR INSTALLATION IS INSIDE
$INSTALL_FOLDER = 'affiliates';
//URL WHERE YOU WILL GENERALLY WANT AFFILIATES TO SEND TRAFFIC TO
$main_url = 'http://www.'.$_SERVER['HTTP_HOST'];
$domain_path = $DOMAIN.'/'.$INSTALL_FOLDER;
//var_dump($_SERVER, $main_url, $DOMAIN);
