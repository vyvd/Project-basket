<?php

class GoogleAuthRedirectInterface
{

    public static function callEntity(string $entity) {
        switch ($entity) {
            case 'sheets':
                return self::getGoogleSheetsAuth();
        }
    }

    private static function getGoogleSheetsAuth() {
        require_once(APP_ROOT_PATH . 'builders/google/sheets/GoogleSheets.php');
        $gSheets = new GoogleSheets();
        return $gSheets->authRedirectHandler();
    }
}