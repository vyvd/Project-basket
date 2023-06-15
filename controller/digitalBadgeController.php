<?php
class digitalBadgeController extends Controller {

    public function createBadgeClassOneYear($account) {

        $url = 'https://us-staging.newskillsacademy.com/';

        // LOGIN
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url.'admin/login',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>'{
                "email": "root@example.com",
                "password": "d8e133e477bf320498f9d63586df040bdc44f5e5"
            }',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $json = json_decode($response);

        $accessToken = $json->access_token;

        // actually create the class
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url.'admin/badgeclass',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'image'=> new CURLFILE(SITE_URL.'assets/cdn/digitalBadges/1yearbadge.png'),
                'name'=>'One Year Unlimited Learning Anniversary-'.$account->id.rand(1,99999),
                'description'=>'Earned when studying with an Unlimited Learning Membership at New Skills Academy for a year',
                'criteria'=>'[{"url": "https://newskillsacademy.co.uk/", "narrative": "Unlimited Learning Membership at New Skills Academy for a year"}]'
            ),
            CURLOPT_HTTPHEADER => array(
                'Authorization: bearer '.$accessToken,
                'Content-Type: multipart/form-data'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $json = json_decode($response);

        return $json;

    }

    public function createOneYearBadge() {

        $account = ORM::for_table("accounts")->find_one(2);

        $url = 'https://us-staging.newskillsacademy.com/';

        $badgeClass = $this->createBadgeClassOneYear($account);

        // LOGIN
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url.'admin/login',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>'{
                "email": "root@example.com",
                "password": "d8e133e477bf320498f9d63586df040bdc44f5e5"
            }',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $json = json_decode($response);

        $accessToken = $json->access_token;


        // GIVE THE BADGE, badge_id is from the above method
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://us-staging.newskillsacademy.com/",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>'{
                "badge_id": "'.$badgeClass->badge_id.'", 
                "recipient": {
                    "email": "'.$account->email.'",
                    "url": "'.SITE_URL.'profile/'.$account->id.'"
                },
                "evidence": "Confirms that New Skills Academy awarded this to '.$account->firstname.' '.$account->lastname.' ('.$account->id.') after being an Unlimited Learning Member for 1 year since November 13 2021."
            }',
            CURLOPT_HTTPHEADER => array(
                'Authorization: bearer '.$accessToken,
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $json = json_decode($response);

        // add to db
        $digitalBadge = ORM::For_table("digitalBadges")->create();

        $digitalBadge->accountID = $account->id;
        $digitalBadge->badgeID = $json->id;
        $digitalBadge->badgeJson = $response;
        $digitalBadge->set_expr("whenIssued", "NOW()");
        $digitalBadge->imgUrl = $json->image;

        $digitalBadge->save();


    }

}