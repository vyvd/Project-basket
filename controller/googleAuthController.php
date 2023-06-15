<?php

use builders\google\auth\GoogleAuth;

class googleAuthController extends Controller
{

    public function googleSheetsAuthenticator()
    {
        require_once(APP_ROOT_PATH . 'builders/google/sheets/GoogleSheets.php');
        require_once(APP_ROOT_PATH . 'repositories/helpers/RepositoryHelpers.php');
        $googleSheets = new GoogleSheets();
        $data = $googleSheets->authInit();
        echo json_encode($data);
        exit();
    }

    public function googleAuthRedirectHandler()
    {
        require_once(APP_ROOT_PATH . 'builders/google/auth/GoogleAuthRedirectInterface.php');
        return GoogleAuthRedirectInterface::callEntity($_GET['entity']);
    }
}