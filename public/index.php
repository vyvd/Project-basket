<?php
require __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__, '../.newskills-env');
$dotenv->load();


header("X-XSS-Protection: 0");
header('X-Content-Type-Options: nosniff');
header("Strict-Transport-Security:max-age=63072000");

ini_set("session.cookie_httponly", true);
ini_set('session.cookie_secure', 1);
session_start();

ob_start("ob_gzhandler");

// SET THE DEFAULT TIMEZONE FOR THIS WEBSITE
date_default_timezone_set('Europe/London');

define("SITE_URL", getenv('SITE_URL'));

define("SITE_NAME", getenv('SITE_NAME'));

$site_type = getenv('SITE_TYPE');
if(empty($site_type)) {
    $site_type = 'uk';
}
define("SITE_TYPE", $site_type);

$domain_name = getenv('DOMAIN_NAME');
if(empty($domain_name)) {
    $domain_name = 'newskillsacademy.co.uk';
}
define("DOMAIN_NAME", $domain_name);

define("ADMIN_EMAIL", getenv('ADMIN_EMAIL'));

define("IMPORT_BASE_URL", getenv('IMPORT_BASE_URL'));
define("IMPORT_BASE_UK_URL", getenv('IMPORT_BASE_UK_URL'));

define("SYNC_BASE_URL", getenv('SYNC_BASE_URL'));

define("PAYMENT_MODE", getenv('PAYMENT_MODE'));
define("SITE_ENVIRONMENT", getenv('SITE_ENVIRONMENT'));

if(PAYMENT_MODE == 'sandbox'){
    define("STRIPE_PUBLISHABLE_KEY", getenv('STRIPE_PUBLISHABLE_KEY_TEST'));
    define("STRIPE_SECRET_KEY", getenv('STRIPE_SECRET_KEY_TEST'));

    define("PAYPAL_CLIENT_ID", getenv('PAYPAL_CLIENT_ID_TEST'));
    define("PAYPAL_SECRET", getenv('PAYPAL_SECRET_TEST'));
    define("PAYPAL_URL", getenv('PAYPAL_URL_TEST'));

    define("PAYPAL_CLIENT_ID_NEW", getenv('PAYPAL_CLIENT_ID_NEW_TEST'));
    define("PAYPAL_SECRET_NEW", getenv('PAYPAL_SECRET_NEW_TEST'));
}else{
    define("STRIPE_PUBLISHABLE_KEY", getenv('STRIPE_PUBLISHABLE_KEY'));
    define("STRIPE_SECRET_KEY", getenv('STRIPE_SECRET_KEY'));

    define("PAYPAL_CLIENT_ID", getenv('PAYPAL_CLIENT_ID'));
    define("PAYPAL_SECRET", getenv('PAYPAL_SECRET'));
    define("PAYPAL_URL", getenv('PAYPAL_URL'));

    define("PAYPAL_CLIENT_ID_NEW", getenv('PAYPAL_CLIENT_ID_NEW'));
    define("PAYPAL_SECRET_NEW", getenv('PAYPAL_SECRET_NEW'));
}

define("FACEBOOK_APP_ID", getenv('FACEBOOK_APP_ID'));
define("FACEBOOK_APP_SECRET", getenv('FACEBOOK_APP_SECRET'));

define("FB_BUSINESS_SDK_ACCESS_TOKEN", getenv('FB_BUSINESS_SDK_ACCESS_TOKEN'));
define("FB_BUSINESS_PIXEL_ID", getenv('FB_BUSINESS_PIXEL_ID'));

define("GA_KEY_1", getenv('GA_KEY_1'));
define("GA_KEY_2", getenv('GA_KEY_2'));
define("GTM_KEY_1", getenv('GTM_KEY_1'));
define("SNAP_KEY_1", getenv('SNAP_KEY_1'));
define("TIKTOK_KEY_1", getenv('TIKTOK_KEY_1'));
define("BING_KEY_1", getenv('BING_KEY_1'));

define("XO_SITE_URL", getenv('XO_SITE_URL'));
define("XO_API_URL", getenv('XO_SITE_URL').'api/');

define("NSFA_API_URL", getenv('NSFA_API_URL'));

define("NSA_APP_URL", getenv('NSA_APP_URL'));
define("NSA_APP_API_URL", getenv('NSA_APP_URL').'api/');

define("APP_ROOT_PATH", __DIR__ . '/../');
define("BASE_PATH", APP_ROOT_PATH . 'base/');

define("TO_PATH", __DIR__);
define("TO_PATH_CDN", __DIR__."/assets/cdn/");

define("TO_URL_CDN", SITE_URL."assets/cdn");

// DEFINE MAINT. MODE
define("RECAPTCHA_SITE_KEY", getenv('RECAPTCHA_SITE_KEY'));
define("RECAPTCHA_SECRET_KEY", getenv('RECAPTCHA_SECRET_KEY'));

define("MAINT_MODE", getenv('MAINT_MODE'));

if(MAINT_MODE == "On") {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}else{
    error_reporting(E_ERROR | E_PARSE);
}

// BLUME
$currentUserID = "";
$currentUserID_front = "";
//Added by Zubaer
$currentUserEmailFront = "";


// SESSION VARS.
if (isset($_SESSION['idx'])) {
    $decryptedID = base64_decode($_SESSION['idx']);
    $id_array = explode("p3h9xfn8sq03hs2234", $decryptedID);
    $currentUserID = $id_array[1];

}

// the ID of the signed in admin (used on the admin system only)
define("CUR_ID", $currentUserID);

if (isset($_SESSION['idx_front'])) {
    $decryptedID_front = base64_decode($_SESSION['idx_front']);
    $id_array_front = explode("p3h9xfn8sq03hs2234", $decryptedID_front);
    $currentUserID_front = $id_array_front[1];

    //Added by Zubaer
    $currentUserEmailFront = $_SESSION['nsa_email_front'];

}


// the ID of the signed in user (frontend, client facing)
define("CUR_ID_FRONT", $currentUserID_front);

//Added by Zubaer
define("CUR_EMAIL_FRONT", $currentUserEmailFront);

// getaddress.io api key @todo: add this to .env file
define("GETADDRESSIO_KEY", "sCVxUuje70Oj5sK2x-bYfw31311");

$signedIn = false;

if(CUR_ID_FRONT != "") {
    $signedIn = true;
}

if($_SESSION["adminAccessed"] == "yes") {
    define("ADMIN_ACCESSED", true);
} else {
    define("ADMIN_ACCESSED", false);
}

define("SIGNED_IN", $signedIn);

// current order ID, if any
define("ORDER_ID", $_SESSION["orderID"]);

// include composer packages
//include '../vendor/autoload.php';
include __DIR__ . '/../classes/WpProQuiz_Model_AnswerTypes.class.php';
include __DIR__ . '/../classes/idiorm.class.php';


// stripe API keys
//define("STRIPE_PUBLIC", "pk_test_GKFXh6Jg3OtGt3fSRHiOdbW6");
//define("STRIPE_SECRET", "sk_test_7box6FtcHtuiCN2eyfcyrWMe");

//\Stripe\Stripe::setApiKey(STRIPE_SECRET);

// DB details
define("DB_NAME", getenv('DB_NAME'));
define("DB_USERNAME", getenv('DB_USERNAME'));
define("DB_PASS", getenv('DB_PASS'));
define("DB_HOST", getenv('DB_HOST'));

// configure ORM
ORM::configure('mysql:host='.getenv('DB_HOST').';dbname='.getenv('DB_NAME'));
ORM::configure('username', getenv('DB_USERNAME'));
ORM::configure('password', getenv('DB_PASS'));
ORM::configure('driver_options', array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
ORM::configure('error_mode', PDO::ERRMODE_WARNING);
ORM::configure('caching', true);
ORM::configure('caching_auto_clear', true);


// Tesco Clubcard
define("TC_KEY", "85778018-a03c-4fe8-80a6-6485627e56d0");
define("TC_TOKEN", "TES0862Test-b803921e-3b34-4245-bad6-e82e12424f92");
define("TC_SUPPLIER", "TES0862_Test");

// VAT
define("VAT_RATE", "20");

// AWS S3
define('AWS_KEY', getenv('AWS_KEY'));
define('AWS_SECRET_KEY', getenv('AWS_SECRET_KEY'));
define('AWS_ACCOUNT_ID', getenv('AWS_ACCOUNT_ID'));
define('AWS_BUCKET_NAME', getenv('AWS_BUCKET_NAME'));

// AWS Products
define('AWS_KEY_PRODUCT', getenv('AWS_KEY_PRODUCT'));
define('AWS_SECRET_KEY_PRODUCT', getenv('AWS_SECRET_KEY_PRODUCT'));
define('AMAZON_ASSOCIATE_ID_PRODUCT', getenv('AMAZON_ASSOCIATE_ID_PRODUCT'));
$courseCopyConnections = include_once(APP_ROOT_PATH . 'config/db-connections-config.php');
define('COURSE_COPY_CONNECTIONS', $courseCopyConnections);
// include our controller
include_once(__DIR__ . "/../controller/Controller.php");

// initialize page
$controller = new Controller();

$controller->invoke($_SERVER['REQUEST_URI']);

