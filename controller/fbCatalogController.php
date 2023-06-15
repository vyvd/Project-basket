<?php
require_once(__DIR__ . '/courseController.php');

class fbCatalogController extends Controller {


    /**
     * @var accountController
     */
    protected $accounts;

    public function __construct()
    {

        $this->getFBCatalog();

    }

    public function getFBCatalog() {

        //Get generated fb catalog.xml
        //echo '<h1>Get FB Catalog File</h1>';

    }

    private function getSalePrice($price, $currencyID = "1") {

        if($currencyID == "2") {
            $sale_price = $price;

            if($price == 290) {
                $sale_price = 75;
            } else if($price == 240) {
                $sale_price = 75;
            } else if($price == 120) {
                $sale_price = 39;
            } else if($price == 110) {
                $sale_price = 30;
            } else if($price == 50) {
                $sale_price = 16;
            } else if($price == 25) {
                $sale_price = 10;
            }
        } else {
            $sale_price = $price;

            if($price == 240) {
                $sale_price = 50;
            } else if($price == 180) {
                $sale_price = 35;
            } else if($price == 100) {
                $sale_price = 25;
                //$sale_price = 30;
            } else if($price == 80) {
                //$sale_price = 15;
                $sale_price = 20;
            } else if($price == 40) {
                $sale_price = 10;
            } else if($price == 20) {
                $sale_price = 5;
            }
        }



        return $sale_price;

    }

    //Generate at a certain time interval using Cron
    public function generateCatalog() {

        $googNs = \FluidXml\fluidns('g',   'http://base.google.com/ns/1.0');
        //$xslNs  = \FluidXml\fluidns('xsl', 'http://www.w3.org/1999/XSL/Transform');

        $rss = \FluidXml\fluidxml(null);               // A document without a root node.
        //$rss->namespace($googNs, $xslNs);    // Let's register the namespaces associated with our document.
        $rss->namespace($googNs);    // Let's register the namespaces associated with our document.

        $root = $rss->addChild('rss', true, ['version' => '2.0']); // Our document is with no root node, let's create one.

        $rootNode = $root[0];                // Accessing the node as array returns the associated DOMNode.
        // In this case $root[0] is the first and only associated DOMNode.
        // Now we can access the DOMNode APIs.

        // Let's add the namespaces on the root node using the DOMNode setAttributeNS() interface.
        $rootNode->setAttributeNS('http://www.w3.org/2000/xmlns/', "xmlns:{$googNs->id()}", $googNs->uri());
        //$rootNode->setAttributeNS('http://www.w3.org/2000/xmlns/', "xmlns:{$xslNs->id()}",  $xslNs->uri());

        // Done, the root node is filled with the namespaces declarations.
        // We can continue as usual.
        $channel = $rss->add('channel', true);

        $title = $channel->add('title', true);
        $title->addCdata('New Skills Academy Products');

        $link = $channel->add('link', true);
        $link->addCdata('https://newskillsacademy.co.uk/');

        $channel->add('description', 'New Skills Academy Product List RSS feed');



        $course_controller = new courseController();
        $courses = $this->getAllCourses();

        //var_dump($courses[2]);
        //die();

        foreach ($courses as $course) {

            $course_title = $course->title;

            $course_id = $course->id;
            $oldProductID = $course->productID;

            $item_id = empty($oldProductID) ? $course_id : $oldProductID;

            $imploded_cats = '';
            $categories = $course_controller->getCourseCategories($course_id);
            if(!empty($categories)) {
                $imploded_cats = implode(', ', $categories);
            }

            $course_url = SITE_URL.'course/'.$course->slug;
            $course_image = $course_controller->getCourseImage($course->id);
            $course_price = $course->price. ' GBP';
            $sale_price = $this->getSalePrice($course->price). ' GBP';
            $availability = $course->hidden == '1' ? 'out of stock' : 'in stock';


            $item = $channel->add('item', true);

            $item->add('g:id', $item_id)
                ->add('g:google_product_category', 'Business &amp; Industrial &gt; Retail')
                ->add('g:product_type', $imploded_cats)
                ->add('g:link', $course_url)
                ->add('g:image_link', $course_image)
                ->add('g:availability', $availability)
                ->add('g:price', $course_price)
                ->add('g:sale_price', $sale_price)
                ->add('g:brand', 'New Skills Academy')
                ->add('g:condition', 'new');


            $title = $item->add('g:title', true);
            $title->addCdata($course_title);

            $description = $item->add('g:description', true);

            $nsa_course_description = $course->additionalContent;
            if(empty($nsa_course_description)) {
                $nsa_course_description = $course_title;
            }

            //$course_description = strip_tags($course->additionalContent);
            $nsa_course_description = strip_tags($nsa_course_description);

            //$description->addCdata($course_description);

            $nsa_course_description = (strlen($nsa_course_description) > 9990) ? substr($nsa_course_description,0,9980).'...' : $nsa_course_description;

            $description->addCdata($nsa_course_description);

            $mpn = $item->add('g:mpn', true);
            $mpn->addCdata($item_id);

        }

        // add subscriptions
        //$subs = ORM::for_table("premiumSubscriptionsPlans")->where_not_equal("id", "2")->find_many();
        $subs = ORM::for_table("premiumSubscriptionsPlans")->where_not_in("id", array(1, 2))->find_many();

        foreach($subs as $sub) {


            $item = $channel->add('item', true);

            $item_id = $sub->id;

            //$course_price = $item->price;
            $course_price = $sub->price;

            $item->add('g:id', $item_id)
                ->add('g:google_product_category', 'Business &amp; Industrial &gt; Retail')
                ->add('g:product_type', '')
                ->add('g:link', SITE_URL.'subscription')
                ->add('g:image_link', SITE_URL.'assets/images/course-devices-img.png')
                ->add('g:availability', 'in stock')
                ->add('g:price', $course_price)
                ->add('g:sale_price', $course_price)
                ->add('g:brand', 'New Skills Academy')
                ->add('g:condition', 'new');


            $title = $item->add('g:title', true);
            //$title->addCdata($course_title);
            $title->addCdata('Unlimited Learning Plan');

            $description = $item->add('g:description', true);
            $course_description = 'Start your Unlimited Learning membership today for only '.$course_price;
            //$course_description = strip_tags($course->additionalContent);
            $description->addCdata($course_description);

            $mpn = $item->add('g:mpn', true);
            $mpn->addCdata($item_id);


        }


        $rss->save('assets/cdn/fb-catalog.xml', true);

        echo $rss;


    }

    public function generateCatalogAllCurrencies() {

        $currencies = ORM::for_table("currencies")->find_many();

        // iterate through each currency
        foreach($currencies as $currency) {

            $googNs = \FluidXml\fluidns('g',   'http://base.google.com/ns/1.0');
            //$xslNs  = \FluidXml\fluidns('xsl', 'http://www.w3.org/1999/XSL/Transform');

            $rss = \FluidXml\fluidxml(null);               // A document without a root node.
            //$rss->namespace($googNs, $xslNs);    // Let's register the namespaces associated with our document.
            $rss->namespace($googNs);    // Let's register the namespaces associated with our document.

            $root = $rss->addChild('rss', true, ['version' => '2.0']); // Our document is with no root node, let's create one.

            $rootNode = $root[0];                // Accessing the node as array returns the associated DOMNode.
            // In this case $root[0] is the first and only associated DOMNode.
            // Now we can access the DOMNode APIs.

            // Let's add the namespaces on the root node using the DOMNode setAttributeNS() interface.
            $rootNode->setAttributeNS('http://www.w3.org/2000/xmlns/', "xmlns:{$googNs->id()}", $googNs->uri());
            //$rootNode->setAttributeNS('http://www.w3.org/2000/xmlns/', "xmlns:{$xslNs->id()}",  $xslNs->uri());

            // Done, the root node is filled with the namespaces declarations.
            // We can continue as usual.
            $channel = $rss->add('channel', true);

            $title = $channel->add('title', true);
            $title->addCdata('New Skills Academy Products');

            $link = $channel->add('link', true);
            $link->addCdata('https://newskillsacademy.com/');

            $channel->add('description', 'New Skills Academy Product List RSS feed - '.$currency->code);



            $course_controller = new courseController();
            //$courses = $this->getAllCourses();

            /*
            if($currency->code == "GBP") {
                $courses = ORM::for_table("courses")->where("usImport", "0")->find_many();
            } else {
                $courses = ORM::for_table("courses")->where("usImport", "1")->find_many();
            }
            */

            $courses = ORM::for_table("courses")->find_many();

            /*
            if($currency->code == "GBP") {
                $courses = ORM::for_table("courses")->find_many();
            } else {
                $courses = ORM::for_table("courses")->where("usImport", "1")->find_many();
            }
            */

            //var_dump($courses[2]);
            //die();

            foreach ($courses as $course) {

                $course_title = $course->title;

                $course_id = $course->id;
                $oldProductID = $course->productID;

                $item_id = empty($oldProductID) ? $course_id : $oldProductID;

                if($currency->code == "USD") {
                    $item_id = $course_id;
                }

                $imploded_cats = '';
                $categories = $course_controller->getCourseCategories($course_id);
                if(!empty($categories)) {
                    $imploded_cats = implode(', ', $categories);
                }

                $course_url = 'https://newskillsacademy.com/course/'.$course->slug;
                if($currency->code == "GBP") {
                    $course_url = SITE_URL.'course/'.$course->slug;
                }
                $course_image = $course_controller->getCourseImage($course->id);
                $course_price_raw = $this->getCoursePrice($course, $currency->id);
                $course_price = $this->getCoursePrice($course, $currency->id). ' '.$currency->short;
                //$sale_price = $this->getSalePrice($course_price, $currency->id). ' '.$currency->short;
                $sale_price = $this->getSalePrice($course_price_raw, $currency->id). ' '.$currency->short;
                $availability = $course->hidden == '1' ? 'out of stock' : 'in stock';


                $item = $channel->add('item', true);

                $item->add('g:id', $item_id)
                    ->add('g:google_product_category', 'Business &amp; Industrial &gt; Retail')
                    ->add('g:product_type', $imploded_cats)
                    ->add('g:link', $course_url)
                    ->add('g:image_link', $course_image)
                    ->add('g:availability', $availability)
                    ->add('g:price', $course_price)
                    ->add('g:sale_price', $sale_price)
                    ->add('g:brand', 'New Skills Academy')
                    ->add('g:condition', 'new');


                $title = $item->add('g:title', true);
                $title->addCdata($course_title);

                $description = $item->add('g:description', true);

                $nsa_course_description = $course->additionalContent;
                if(empty($nsa_course_description)) {
                    $nsa_course_description = $course_title;
                }

                //$course_description = strip_tags($course->additionalContent);
                $nsa_course_description = strip_tags($nsa_course_description);

                //$description->addCdata($course_description);

                $nsa_course_description = (strlen($nsa_course_description) > 9990) ? substr($nsa_course_description,0,9980).'...' : $nsa_course_description;

                $description->addCdata($nsa_course_description);

                $mpn = $item->add('g:mpn', true);
                $mpn->addCdata($item_id);

            }

            // add subscriptions
            //$subs = ORM::for_table("premiumSubscriptionsPlans")->where_not_equal("id", "2")->find_many();
            $subs = ORM::for_table("premiumSubscriptionsPlans")->where_not_in("id", array(1, 2))->find_many();

            foreach($subs as $sub) {


                $item = $channel->add('item', true);

                $item_id = $sub->id;

                //$course_price = $item->price;
                $course_price = $sub->price;

                $item->add('g:id', $item_id)
                    ->add('g:google_product_category', 'Business &amp; Industrial &gt; Retail')
                    ->add('g:product_type', '')
                    ->add('g:link', SITE_URL.'subscription')
                    ->add('g:image_link', SITE_URL.'assets/images/course-devices-img.png')
                    ->add('g:availability', 'in stock')
                    ->add('g:price', $course_price)
                    ->add('g:sale_price', $course_price)
                    ->add('g:brand', 'New Skills Academy')
                    ->add('g:condition', 'new');


                $title = $item->add('g:title', true);
                //$title->addCdata($course_title);
                $title->addCdata('Unlimited Learning Plan');

                $description = $item->add('g:description', true);
                $course_description = 'Start your Unlimited Learning membership today for only '.$course_price;
                //$course_description = strip_tags($course->additionalContent);
                $description->addCdata($course_description);

                $mpn = $item->add('g:mpn', true);
                $mpn->addCdata($item_id);


            }


            $rss->save('assets/cdn/fb-catalog-'.$currency->code.'.xml', true);

            echo $rss;


        }




    }

    public function generateCatalogAllCurrenciesDynamic() {

        $currencies = ORM::for_table("currencies")->find_many();

        // iterate through each currency
        foreach($currencies as $currency) {

            $googNs = \FluidXml\fluidns('g',   'http://base.google.com/ns/1.0');
            //$xslNs  = \FluidXml\fluidns('xsl', 'http://www.w3.org/1999/XSL/Transform');

            $rss = \FluidXml\fluidxml(null);               // A document without a root node.
            //$rss->namespace($googNs, $xslNs);    // Let's register the namespaces associated with our document.
            $rss->namespace($googNs);    // Let's register the namespaces associated with our document.

            $root = $rss->addChild('rss', true, ['version' => '2.0']); // Our document is with no root node, let's create one.

            $rootNode = $root[0];                // Accessing the node as array returns the associated DOMNode.
            // In this case $root[0] is the first and only associated DOMNode.
            // Now we can access the DOMNode APIs.

            // Let's add the namespaces on the root node using the DOMNode setAttributeNS() interface.
            $rootNode->setAttributeNS('http://www.w3.org/2000/xmlns/', "xmlns:{$googNs->id()}", $googNs->uri());
            //$rootNode->setAttributeNS('http://www.w3.org/2000/xmlns/', "xmlns:{$xslNs->id()}",  $xslNs->uri());

            // Done, the root node is filled with the namespaces declarations.
            // We can continue as usual.
            $channel = $rss->add('channel', true);

            $title = $channel->add('title', true);
            $title->addCdata('New Skills Academy Products');

            $link = $channel->add('link', true);
            $link->addCdata('https://newskillsacademy.com/');

            $channel->add('description', 'New Skills Academy Product List RSS feed (Dynamic Ref Codes) - '.$currency->code);



            $course_controller = new courseController();
            //$courses = $this->getAllCourses();

            /*
            if($currency->code == "GBP") {
                $courses = ORM::for_table("courses")->where("usImport", "0")->find_many();
            } else {
                $courses = ORM::for_table("courses")->where("usImport", "1")->find_many();
            }
            */

            $courses = ORM::for_table("courses")->find_many();

            /*
            if($currency->code == "GBP") {
                $courses = ORM::for_table("courses")->find_many();
            } else {
                $courses = ORM::for_table("courses")->where("usImport", "1")->find_many();
            }
            */

            //var_dump($courses[2]);
            //die();

            $existingItemIDs = array();

            foreach ($courses as $course) {

                $course_title = $course->title;

                $course_id = $course->id;
                $oldProductID = $course->productID;

                $item_id = empty($oldProductID) ? $course_id : $oldProductID;

                if($currency->code == "USD") {
                    $item_id = $course_id;
                }

                $imploded_cats = '';
                $categories = $course_controller->getCourseCategories($course_id);
                if(!empty($categories)) {
                    $imploded_cats = implode(', ', $categories);
                }

                $course_url = 'https://newskillsacademy.com/course/'.$course->slug;
                if($currency->code == "GBP") {
                    $course_url = SITE_URL.'course/'.$course->slug;
                }
                $course_image = $course_controller->getCourseImage($course->id);
                $course_price_raw = $this->getCoursePrice($course, $currency->id);
                $course_price = $this->getCoursePrice($course, $currency->id). ' '.$currency->code;
                //$sale_price = $this->getSalePrice($course_price, $currency->id). ' '.$currency->short;
                $sale_price = $this->getSalePrice($course_price_raw, $currency->id). ' '.$currency->code;
                $availability = $course->hidden == '1' ? 'out of stock' : 'in stock';

                $refCat = ORM::for_table("currenciesDynamicRefCatPricing")
                    ->where("currencyID", $currency->id)
                    ->where("price", $this->getCoursePrice($course, $currency->id))
                    ->find_one();

                $urlAppend = '';

                if($refCat->ref != "") {
                    $urlAppend = '?ref='.$refCat->ref;
                }

                if(!in_array($item_id, $existingItemIDs) && $course_image != "" && $this->getCoursePrice($course, $currency->id) != "") {



                    $item = $channel->add('item', true);

                    $item->add('g:id', $item_id)
                        ->add('g:google_product_category', 'Business &amp; Industrial &gt; Retail')
                        ->add('g:product_type', $imploded_cats)
                        ->add('g:link', $course_url.$urlAppend)
                        ->add('g:image_link', $course_image)
                        ->add('g:availability', $availability)
                        ->add('g:price', $course_price)
                        ->add('g:sale_price', $sale_price)
                        ->add('g:brand', 'New Skills Academy')
                        ->add('g:condition', 'new');


                    $title = $item->add('g:title', true);
                    $title->addCdata($course_title);

                    $description = $item->add('g:description', true);

                    $nsa_course_description = $course->additionalContent;
                    if(empty($nsa_course_description)) {
                        $nsa_course_description = $course_title;
                    }

                    //$course_description = strip_tags($course->additionalContent);
                    $nsa_course_description = strip_tags($nsa_course_description);

                    //$description->addCdata($course_description);

                    $nsa_course_description = (strlen($nsa_course_description) > 9990) ? substr($nsa_course_description,0,9980).'...' : $nsa_course_description;

                    $description->addCdata($nsa_course_description);

                    $mpn = $item->add('g:mpn', true);
                    $mpn->addCdata($item_id);

                    array_push($existingItemIDs, $item_id);
                }

            }

            // add subscriptions
            //$subs = ORM::for_table("premiumSubscriptionsPlans")->where_not_equal("id", "2")->find_many();
            $subs = ORM::for_table("premiumSubscriptionsPlans")->where_not_in("id", array(1, 2))->find_many();

            foreach($subs as $sub) {


                $item = $channel->add('item', true);

                $item_id = $sub->id;

                $course_price = $sub->price;

                $item->add('g:id', $item_id)
                    ->add('g:google_product_category', 'Business &amp; Industrial &gt; Retail')
                    ->add('g:product_type', '')
                    ->add('g:link', SITE_URL.'subscription')
                    ->add('g:image_link', SITE_URL.'assets/images/course-devices-img.png')
                    ->add('g:availability', 'in stock')
                    ->add('g:price', $course_price)
                    ->add('g:sale_price', $course_price)
                    ->add('g:brand', 'New Skills Academy')
                    ->add('g:condition', 'new');


                $title = $item->add('g:title', true);
                $title->addCdata('Unlimited Learning Plan');

                $description = $item->add('g:description', true);
                $course_description = 'Start your Unlimited Learning membership today for only '.$course_price;
                $description->addCdata($course_description);

                $mpn = $item->add('g:mpn', true);
                $mpn->addCdata($item_id);


            }


            $rss->save('assets/cdn/fb-catalog-'.$currency->code.'-dynamic-refs.xml', true);


        }




    }


}