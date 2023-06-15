<?php

require_once(APP_ROOT_PATH . 'builders/google/auth/GoogleAuth.php');
require_once(APP_ROOT_PATH . 'helpers/UtilHelpers.php');

class GoogleSheets extends GoogleAuth
{

    private $spreadSheetId;

    private $googleSheets;

    public function __construct()
    {
        parent::__construct();
        $this->setRedirectUrl(
            SITE_URL . 'blume/google/auth/redirect?entity=sheets'
        );
        $this->setClientCredentials(
            APP_ROOT_PATH . 'storage/google/sheets/client_credentials.json'
        );
        $this->googleSheets = new Google_Service_Sheets($this->client);
        $this->setScope([
            Google\Service\Sheets::SPREADSHEETS,
            Google\Service\Sheets::SPREADSHEETS_READONLY,
            Google\Service\Sheets::DRIVE,
            Google\Service\Sheets::DRIVE_READONLY,
            Google\Service\Sheets::DRIVE_FILE,
        ]);
    }

    public function extractSheetIdFromUrl($url)
    {
        $splitUrl = parse_url($url);
        $splitUrl = explode('/', $splitUrl['path']);
        $findIndex = array_search('d', $splitUrl);
        if ($findIndex === false) {
            return false;
        }
        $findIndex = $findIndex + 1;
        if (!isset($splitUrl[$findIndex]) || $splitUrl[$findIndex] === '') {
            return false;
        }
        return $splitUrl[$findIndex];
    }

    private function getRange(Google\Service\Sheets\Sheet $sheetObj) {
        $gridProperties = $sheetObj->getProperties()->getGridProperties();
        $columnCount = $gridProperties->getColumnCount();
        $rowCount = $gridProperties->getRowCount();
        $endRange = UtilHelpers::getNameFromNumber($columnCount);
        return "{$sheetObj->getProperties()->getTitle()}!A1:{$endRange}{$rowCount}";
    }

    private function getSheetByTitle(string $title) {
        $this->googleSheets->spreadsheets->get($this->spreadSheetId);
        $getSheets = $this->googleSheets->spreadsheets->get($this->spreadSheetId);
        foreach ($getSheets->getSheets() as $sheetData) {
            if ($title === $sheetData->getProperties()->getTitle()) {
                return $sheetData;
            }
        }
        return false;
    }


    public function fetchSpreadsheet($url, $sheet, ?string $range = null)
    {
        $authenticate = $this->authInit();
        if (!$authenticate['success']) {
            return $authenticate;
        }
        $sheetId = $this->extractSheetIdFromUrl($url);
        if (!$sheetId) {
            return false;
        }
        $this->spreadSheetId = $sheetId;

        if (empty($range)) {
            $getSheet = $this->getSheetByTitle($sheet);
            if (!$getSheet) {
                return false;
            }
            $range = $this->getRange($getSheet);
        }
        $params = [
            'ranges' => $range,
            'majorDimension' => 'ROWS',
        ];
        $response = $this->googleSheets->spreadsheets_values->batchGet($this->spreadSheetId, $params);
        $valueRanges = $response->getValueRanges();

        if (isset($valueRanges[array_key_first($valueRanges)])) {
            return [
                'success' => true,
                'data' =>  $valueRanges[array_key_first($valueRanges)]
            ];
        }
        return [
            'success' => false
        ];
    }

}