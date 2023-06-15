<?php
class courseLanguageController extends Controller {

    public function access($account, $course) {

        $cudooCourseIDs = explode(",", $course->cudooCourseIDs);

        $account->email = str_replace("+", "", $account->email);

        $cudooData = array(

            'firstname' => $account->firstname,
            'lastname' => $account->lastname,
            'email' => $account->email,
            'password' => CUR_ID_FRONT."PW1234",
            'course_ids' => $cudooCourseIDs,
            'partner' => "beaeducation"
        );

        $ch = curl_init( 'https://languageschool.newskillsacademy.co.uk/api/partner/create_new_partner' );

        $payload = json_encode( $cudooData );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));

        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

        $result = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($result);

        header('Location: '.$result->login_url);

    }

}