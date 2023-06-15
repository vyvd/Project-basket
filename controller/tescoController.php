<?php
/*
    Used to validate Tesco Clubcard voucher codes. Based on what Tesco return, we can discount orders for Clubcard customers.
*/
class tescoController extends Controller {

    public function getTransactionID() {
        
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < 20; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        
        return $randomString;
        
    }

    public function connect($method, $json) {

        $url = 'https://clubcard.api.tesco.com/v1.0/'.$method;

        $ch = curl_init();

        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: appKeyToken=TES0862-b558c559-3722-4ebe-976c-6395cae7ff04&appKey=85778018-a03c-4fe8-80a6-6485627e56d0'
        ));
        
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $result = curl_exec($ch);

        curl_close($ch);
    
        return json_decode($result, true);

    }
    
    public function validateCode($code) {
        
        $transactionID = $this->getTransactionID();
        $requestID = $this->getTransactionID();
        
        $json = '{ "TransactionID": "'.$transactionID.'", "TransactionDateTime": "'.date('d/m/Y H:i:s').'", "RequestType": "Validate", "SupplierCode": "TES0862", "TokenDetailsList": [ { "RequestId": "'.$requestID.'", "TokenCode": "'.$code.'" } ] }';

        $result = $this->connect("TokenProcessorService/ManageToken", $json);
        
        $data = array(
            'Status' => $result["TokenDetailsList"]["0"]["TokenStatus"],
            'Value' => $result["TokenDetailsList"]["0"]["TokenValue"]
        );
    
        
        return $data;
        
    }
    
    public function redeemCode($code) {
        
        $transactionID = $this->getTransactionID();
        $requestID = $this->getTransactionID();
        
        $json = '{ "TransactionID": "'.$transactionID.'", "TransactionDateTime": "'.date('d/m/Y H:i:s').'", "RequestType": "Redeem", "SupplierCode": "TES0862", "TokenDetailsList": [ { "RequestId": "'.$requestID.'", "TokenCode": "'.$code.'" } ] }';

        $result = $this->connect("TokenProcessorService/ManageToken", $json);
        
        if($result["TokenDetailsList"]["0"]["TokenStatus"] == "Redeemed") {
            return true;
        } else {
            return false;
        }
    }


}