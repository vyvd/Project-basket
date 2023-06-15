<?php

use Facebook\Facebook;

require_once(__DIR__ . '/accountController.php');
require_once(__DIR__ . '/rewardsAssignedController.php');

class facebookController extends Controller {

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

    /**
     * @throws \Facebook\Exceptions\FacebookSDKException
     */
    public function __construct()
    {
        $this->post = $_POST;
        $this->get = $_GET;

        $this->fb = new Facebook([
            'app_id' => FACEBOOK_APP_ID,
            'app_secret' => FACEBOOK_APP_SECRET,
            'default_graph_version' => 'v2.10',
            //'default_access_token' => '{access-token}', // optional
        ]);

        $this->accounts = new accountController();
        $this->rewardsAssigned = new rewardsAssignedController();
    }


    public function likeFacebookPage($accountID): array
    {
        //die($accountID);
        $account = $this->accounts->getAccountByID($accountID);
        $data = [
            'accessToken' => null,
            'facebookLoginUrl' => null,
            'likedFacebookPage' => false,
        ];

        if(empty($account->fbAccessToken)) {
            $helper = $this->fb->getRedirectLoginHelper();
            $accessToken = $helper->getAccessToken();

            if(!empty($accessToken)) {

                $oAuth2Client = $this->fb->getOAuth2Client();

                $longLivedAccessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);

                $isExpired = $longLivedAccessToken->isExpired();

                if($isExpired) {
                    $longLivedAccessToken = $oAuth2Client->getLongLivedAccessToken($longLivedAccessToken);
                }

                $account->fbAccessToken = $longLivedAccessToken;
                $account->save();
                $data['accessToken'] = $longLivedAccessToken;

            } else {

                $callback_url = 'https://'.$_SERVER['HTTP_HOST'].strtok($_SERVER["REQUEST_URI"],'?');

                $permissions = ['email', 'user_likes']; // optional
                $callback    = $callback_url;
                $facebook_loginUrl    = $helper->getLoginUrl($callback, $permissions);

                $data['facebookLoginUrl'] = $facebook_loginUrl;
                //echo '<a href="' . $facebook_loginUrl . '">Log in with Facebook!</a>';
            }


        } else {

            //var_dump($longLivedAccessToken);

            try{

                $response = $this->fb->get('/me/likes/881453945238990', $account->fbAccessToken);
                //$response = $fb->get('/me/likes/134362656727752', $_SESSION['facebook_access_token']);

                $likes = $response->getGraphEdge();

                $likes_array = json_decode($likes);

                if( empty($likes_array) ) {

                    //echo 'User hasn\'t liked our page!';

                } else {

                    //echo 'User has liked our page!';
                    $data['likedFacebookPage'] = true;
                    $this->rewardsAssigned->assignReward($accountID, 'facebook', false);
                    //update_user_meta( $user_id, 'facebook_award_status', true );

                }

            } catch(\Exception $e) {
                $account->fbAccessToken = null;
                $account->save();
            }

//            $response = $fb->get('/me/likes??limit=100', $longLivedAccessToken);
//
//            //var_dump($response);
//
//            $likes = $response->getGraphEdge();
//
//            $likes_array = $likes->asArray();
//
//            $liked_page_names = array();
//
//            //var_dump($likes_array);
//
//            foreach ($likes_array as $key => $liked_page) {
//                array_push($liked_page_names, $liked_page['name']);
//            }
//
//            //var_dump($liked_page_names);
//
//            $GLOBALS['liked_page_names'] = $liked_page_names;
//
//            update_user_meta( $user_id, 'liked_page_names', $liked_page_names );


        }

//        echo json_encode($data);
//        die;
        return $data;
    }

}