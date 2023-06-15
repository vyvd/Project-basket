<?php

//use Facebook\Facebook;

require_once(__DIR__ . '/accountController.php');
//require_once('rewardsAssignedController.php');

use FacebookAds\Api;
use FacebookAds\Logger\CurlLogger;
use FacebookAds\Object\ServerSide\ActionSource;
use FacebookAds\Object\ServerSide\Content;
use FacebookAds\Object\ServerSide\CustomData;
use FacebookAds\Object\ServerSide\DeliveryCategory;
use FacebookAds\Object\ServerSide\Event;
use FacebookAds\Object\ServerSide\EventRequest;
use FacebookAds\Object\ServerSide\UserData;


class facebookBusinessSDKController extends Controller {

    /**
     * @var Facebook
     */
    protected $fb;

    /**
     * @var accountController
     */
    protected $accounts;

    /**
     * @var rewardsAssignedController
     */
    protected $rewardsAssigned;


    protected $access_token;

    protected static $pixel_id;

    protected $api;

    /**
     * @throws \Facebook\Exceptions\FacebookSDKException
     */
    public function __construct()
    {
        $this->post = $_POST;
        $this->get = $_GET;

        //var_dump(FB_BUSINESS_SDK_ACCESS_TOKEN);
        //var_dump(FB_BUSINESS_PIXEL_ID);

        $this->access_token = FB_BUSINESS_SDK_ACCESS_TOKEN;
        self::$pixel_id = FB_BUSINESS_PIXEL_ID;


        $this->api = Api::init(null, null, $this->access_token, false);
        $this->api->setLogger(new CurlLogger());

        //var_dump($this->api);

        /*
        $this->fb = new Facebook([
            'app_id' => FACEBOOK_APP_ID,
            'app_secret' => FACEBOOK_APP_SECRET,
            'default_graph_version' => 'v2.10',
            //'default_access_token' => '{access-token}', // optional
        ]);
        */

        //$this->accounts = new accountController();
        //$this->rewardsAssigned = new rewardsAssignedController();


    }


    public static function createPurchaseEvent($contents_data, $total_price, $content_ids = array(), $event_id = false) {

        return self::createEvent('Purchase', $contents_data, $content_ids, $total_price, $event_id );

    }

    public static function createViewContentEvent($contents_data, $content_ids = array(), $event_id = false) {

        return self::createEvent('ViewContent', $contents_data, $content_ids, false, $event_id );

    }

    public static function createAddToCartEvent($contents_data, $content_ids = array(), $event_id = false) {

        return self::createEvent('AddToCart', $contents_data, $content_ids, false, $event_id );

    }

    public static function createEvent($event_name, $contents_data = array(), $content_ids = array(), $total_price = false, $event_id = false) {

        //var_dump(CUR_EMAIL_FRONT);

        $emails = array();

        if(!empty(CUR_EMAIL_FRONT)) {
            array_push($emails, CUR_EMAIL_FRONT);
        }

        try {

            $current_time_miliseconds = round(microtime(true) * 1000);

            $fbc_event_param = 'fb.1.'.$current_time_miliseconds.'.AbCdEfGhIjKlMnOpQrStUvWxYz1234567890'.rand(1000,9999999999);
            $fbp_event_param = 'fb.1.'.$current_time_miliseconds.'.1098115397'.rand(1000,9999999999);


            $user_data = (new UserData())
                ->setEmails($emails)
                //->setPhones(array('12345678901', '14251234567'))
                // It is recommended to send Client IP and User Agent for Conversions API Events.
                ->setClientIpAddress($_SERVER['REMOTE_ADDR'])
                ->setClientUserAgent($_SERVER['HTTP_USER_AGENT'])
                //->setFbc('fb.1.1554763741205.AbCdEfGhIjKlMnOpQrStUvWxYz1234567890')
                ->setFbc($fbc_event_param)
                ->setFbp($fbp_event_param);

            //var_dump($user_data);

            $contents = array();

            foreach ($contents_data as $content_data) {

                $content = (new Content());

                if(isset($content_data['content_id'])) {
                    $content->setProductId($content_data['content_id']);
                }

                if(isset($content_data['content_title'])) {
                    $content->setTitle($content_data['content_title']);
                }

                if(isset($content_data['quantity'])) {
                    $content->setQuantity($content_data['quantity']);
                }

                if(isset($content_data['price'])) {
                    $content->setItemPrice($content_data['price']);
                }

                if(isset($content_data['categories'])) {
                    $content->setCategory($content_data['categories']);
                }


                if(isset($content_data['content_type'])) {
                    $content->set ($content_data['content_type']);
                }

                $content->setDeliveryCategory(DeliveryCategory::HOME_DELIVERY);

                array_push($contents, $content);

            }

            $currencyCode = 'gbp';

            if(SITE_TYPE != "uk") {
                $currencyCode = 'usd';
            }

            $custom_data = (new CustomData())
                ->setCurrency($currencyCode)
                ->setContentType('product');

            if(!empty($contents)) {
                $custom_data->setContents($contents);
            }

            if(!empty($content_ids)) {
                $custom_data->setContentIds($content_ids);
            }

            if(!empty($total_price)) {
                $custom_data->setValue($total_price);
            }



            $current_page_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";


            $event = (new Event())
                ->setEventName($event_name)
                ->setEventTime(time())
                ->setEventSourceUrl($current_page_link)
                ->setUserData($user_data)
                ->setCustomData($custom_data)
                ->setActionSource(ActionSource::WEBSITE);


            if(!empty($event_id)) {
                $event->setEventId($event_id);
            }


            return $event;

        } catch (Exception $e) {

            //var_dump($e->getMessage());

            return false;

        }

    }

    public static function executeEvents($events) {

        //$events = array();
        //array_push($events, $event);

        //var_dump($this->pixel_id);

        //var_dump($events);

        try {


            $request = (new EventRequest(self::$pixel_id))
                //->setTestEventCode('TEST7210')
                ->setTestEventCode('TEST245')
                ->setEvents($events);



            $response = $request->execute();

            //var_dump('pixel_id', self::$pixel_id);

            //print_r($response);

        } catch (Exception $e) {

            //var_dump($e->getMessage());

        }



    }


}