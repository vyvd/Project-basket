<?php

use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\api\DefaultApi;
use Amazon\ProductAdvertisingAPI\v1\ApiException;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\GetItemsRequest;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\GetItemsResource;
use Amazon\ProductAdvertisingAPI\v1\Configuration;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\SearchItemsRequest;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\SearchItemsResource;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\PartnerType;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\ProductAdvertisingAPIClientException;

class amazonController extends Controller {

    /**
     * @var mixed
     */
    protected $amazonAPI;


    public function __construct()
    {
    }

    public function getAmazonProducts($asinIds = null)
    {
        if($_GET['asinIds']){
            $asinIds = explode(',', $_GET['asinIds']);
        }

        $config = new Configuration();

        /*
         * Add your credentials
         */
        # Please add your access key here
        $config->setAccessKey(AWS_KEY_PRODUCT);
        # Please add your secret key here
        $config->setSecretKey(AWS_SECRET_KEY_PRODUCT);

        # Please add your partner tag (store/tracking id) here
        $partnerTag = AMAZON_ASSOCIATE_ID_PRODUCT;

        /*
         * PAAPI host and region to which you want to send request
         * For more details refer:
         * https://webservices.amazon.com/paapi5/documentation/common-request-parameters.html#host-and-region
         */
        $config->setHost('webservices.amazon.co.uk');
        $config->setRegion('eu-west-1');

        $apiInstance = new DefaultApi(
        /*
         * If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
         * This is optional, `GuzzleHttp\Client` will be used as default.
         */
            new GuzzleHttp\Client(),
            $config
        );

        # Choose item id(s)
        $itemIds = $asinIds;

        /*
         * Choose resources you want from GetItemsResource enum
         * For more details, refer: https://webservices.amazon.com/paapi5/documentation/get-items.html#resources-parameter
         */
        $resources = [
            GetItemsResource::ITEM_INFOTITLE,
            GetItemsResource::OFFERSLISTINGSPRICE,
            GetItemsResource::IMAGESPRIMARYSMALL,
        ];

        # Forming the request
        $getItemsRequest = new GetItemsRequest();
        $getItemsRequest->setItemIds($itemIds);
        $getItemsRequest->setPartnerTag($partnerTag);
        $getItemsRequest->setPartnerType('Associates');
        $getItemsRequest->setResources($resources);

        # Validating request
        $invalidPropertyList = $getItemsRequest->listInvalidProperties();
        $length = count($invalidPropertyList);
        if ($length > 0) {
            echo "Error forming the request", PHP_EOL;
            foreach ($invalidPropertyList as $invalidProperty) {
                echo $invalidProperty, PHP_EOL;
            }
            return;
        }

        try
        {
            $getItemsResponse = $apiInstance->getItems($getItemsRequest);
            $result = json_decode($getItemsResponse);
            if(isset($result->ItemsResult->Items)) {
                if(!empty($result->ItemsResult->Items)) {
                    if(!empty($result->ItemsResult->Items)) {
                        echo '<ul class="styled-list amazon-products-sidebar">';
                        foreach($result->ItemsResult->Items as $item) {
                            ?>
                            <li>
                                <a target="_blank" href="<?php echo $item->DetailPageURL ?>">
                                    <img style="max-width: 65px;" src="<?php echo $item->Images->Primary->Small->URL ?>">
                                    <span><?php echo $item->ItemInfo->Title->DisplayValue ?></span>
                                </a>
                            </li>
                            <?php
                        }
                        echo '</ul>';
                    }
                } else {
                    echo 'There are no items with those ASINS';
                }
            } else {
                echo 'Items are not to be found.';
            }
        }
        catch(Exception $e)
        {
            echo "<pre>";
            print_r($e);
            echo $e->getMessage();
        }
    }

}