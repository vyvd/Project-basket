<?php
class reziController extends Controller {

    public function getToken() {

        $account = ORM::for_table("accounts")->select("id")->select("email")->find_one(CUR_ID_FRONT);

        if(CUR_ID_FRONT == "") {
            exit;
        }

        $data = array("uid" => $account->id, "email" => $account->email);
        $data_string = json_encode($data);

        $url = "https://us-central1-rezi-3f268.cloudfunctions.net/connect";

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $headers = array(
            "Authorization: Bearer b51e27e8f5944499577906aba131927541fd080a7a0f7183136bda5b0110a8da",
            "Content-Type: application/json",
            'Content-Length: ' . strlen($data_string)
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);


        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);

        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $resp = curl_exec($curl);
        curl_close($curl);

        $resp = json_decode($resp);

        return $resp->token;


    }

    public function renderIframe() {



        $token = $this->getToken();

        if($token == "") {
            $this->setAlertDanger("There is currently an issue connecting to our CV builder. Please contact our support or try again later.");
        } else {
            ?>
            <iframe src="https://nsa.rezi.io/login/?token=<?= $token ?>" style="border:0;margin:0;width:100%;height:800px;"></iframe>
            <?php
        }




    }

    public function renderIframeJobs() {



        ?>
        <iframe src="https://newskillsacademy.yourjobboard.io/?country=gb" style="border:0;margin:0;width:100%;height:800px;"></iframe>
        <?php




    }

}