<?php

/**
 * Class apiController
 *
 * Base apiController
 * @author Zubaer <zubaerahammed223@gmail.com>
 */


class apiController extends Controller {

    public function __construct()
    {

        $bearer_token = $this->getBearerToken();
        $nsa_api_token = getenv('NSA_API_TOKEN');

        //var_dump($bearer_token);
        //var_dump($nsa_api_token);

        if($bearer_token !== $nsa_api_token) {


            echo json_encode(array(
                'status' => 401,
                'data'   => 'Unauthorized'
            ));
            exit;

        }

        // call Controller's constructor
        //parent::__construct();
    }

    /**
     * Get header Authorization
     * */
    private function getAuthorizationHeader(){
        $headers = null;
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        }
        else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            //print_r($requestHeaders);
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        return $headers;
    }

    /**
     * get access token from header
     * */
    private function getApiKey() {

        $api_key = isset($_GET['api_key']) && !empty($_GET['api_key']) ? $_GET['api_key'] : null;

        return $api_key;
    }

    /**
     * get access token from header
     * */
    private function getBearerToken() {
        $headers = $this->getAuthorizationHeader();
        // HEADER: Get the access token from the header
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                return $matches[1];
            }
        } else {

            return $this->getApiKey();

        }
        return null;
    }

}

?>
