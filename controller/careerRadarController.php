<?php
class careerRadarController extends Controller {

    public function getToken() {

        $account = ORM::for_table("accounts")->find_one(CUR_ID_FRONT);

        if(CUR_ID_FRONT == "") {
            exit;
        }

        $url = "https://careerradar.org/career/api/auth?username=".$account->email."&forenames=".$account->firstname."&surname=".$account->lastname;

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $headers = array(
            "Authorization: Basic ".base64_encode('NSA:NZxqus55hY'),
            "Content-Type: application/json",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);


        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");

        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $resp = curl_exec($curl);
        curl_close($curl);

        $resp = json_decode($resp);

        return $resp->token;


    }

    public function renderIframe() {

        $url = urldecode($this->get["url"]);

        if($url == "" || $url == "dash") {

            $token = $this->getToken();

            if($token == "") {
                $this->setAlertDanger("There is currently an issue connecting to Career Radar. Please contact our support or try again later.");
            } else {
                ?>
                <iframe src="https://careerradar.org/?token=<?= $token ?>" style="border:0;margin:0;width:100%;height:800px;"></iframe>
                <?php
            }

        } else {

            if($url == "dash") {
                ?>
                <iframe src="https://careerradar.org/userprofile" style="border:0;margin:0;width:100%;height:800px;"></iframe>
                <?php
            } else if($url == "job") {
                ?>
                <iframe src="https://careerradar.org/?page=job" style="border:0;margin:0;width:100%;height:800px;"></iframe>
                <?php
            } else {
                ?>
                <iframe src="https://careerradar.org/?page=occupation" style="border:0;margin:0;width:100%;height:800px;"></iframe>
                <?php
            }

        }


    }

}