<?php

require_once(__DIR__ . '/courseController.php');
require_once(__DIR__ . '/journeyController.php');
require_once(__DIR__ . '/orderController.php');
require_once(APP_ROOT_PATH . 'helpers/RewardHelpers.php');

use Carbon\Carbon;

class moosendController extends Controller {

    private $rewardHelpers;

    public function __construct()
    {
        parent::__construct();
        $this->rewardHelpers = new RewardHelpers();
    }

    public function checkIfSubscribed($email) {


        $api_key = '81802e21-83f2-4ed2-b604-a31a463fb046';
        $list_id = 'a0c3759c-3748-4782-bf33-08d520f7614d';

        $klaviyo_api_key = 'pk_a345558aa7ebf10b0f7b96f082ad8da60f';
        $klaviyo_list_id = 'TbMmpL';

        // anything other than GBP to go into a different list
        $currency = $this->currentCurrency();

        if($currency->code != "GBP" || SITE_TYPE != "uk") {
            $list_id = 'fd3b8a24-f67d-4de4-bfc6-3d11978447a4';
            $klaviyo_api_key = 'pk_f14889064dfe92fb68dc63a57a3ad28b9c';
            $klaviyo_list_id = 'YaKzWA';
        }

        $email = urlencode($email);

        $ch = curl_init( "https://api.moosend.com/v3/subscribers/{$list_id}/view.json?apikey={$api_key}&Email=".$email );
        
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json') );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_VERBOSE, true );
        
        $result = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($result);


        $ch = curl_init("https://a.klaviyo.com/api/v2/list/{$klaviyo_list_id}/get-members?api_key={$klaviyo_api_key}");

        $data = array("emails" => [$email]);
        $payload = json_encode($data);

        curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_VERBOSE, true );

        $result = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($result);

        return !empty($result);

    }

    public function addSubscriber($firstname = null, $email = null) {

        $api_key = '81802e21-83f2-4ed2-b604-a31a463fb046';
        $list_id = 'a0c3759c-3748-4782-bf33-08d520f7614d';

        $klaviyo_api_key = 'pk_a345558aa7ebf10b0f7b96f082ad8da60f';
        $klaviyo_list_id = 'TbMmpL';

        // anything other than GBP to go into a different list
        $currency = $this->currentCurrency();

        if($currency->code != "GBP" || SITE_TYPE != "uk") {
            $list_id = 'fd3b8a24-f67d-4de4-bfc6-3d11978447a4';
            $klaviyo_api_key = 'pk_f14889064dfe92fb68dc63a57a3ad28b9c';
            $klaviyo_list_id = 'YaKzWA';
        }

        $name = $this->post['firstname'] ?? $firstname;
        $user_email = $this->post['email'] ?? $email;

        $user = ORM::For_table("accounts")->where("email", $user_email)->find_one();

        $custom_fields = array();
        $klaviyo_custom_fields = array();

        if($user->id != "") {

            $courseAssigned = ORM::for_table("coursesAssigned")
                ->where("accountID", $user->id)
                ->find_one();

            if(!empty($courseAssigned) && $courseAssigned->id != "") {

                $course = ORM::for_table("courses")->find_one($courseAssigned->courseID);

                $categories = array();

                $courseCategories = ORM::for_table("courseCategoryIDs")->where("course_id", $course->id)->find_many();

                foreach($courseCategories as $cat) {

                    $catData = ORM::for_table("courseCategories")->find_one($cat->category_id);

                    array_push($categories, $catData->title);

                }

                $imploded_cat_names = implode(',', $categories);

                $custom_fields = array(
                    'First course Title' => $course->title,
                    'Course Category' => $imploded_cat_names,
                    'user_id' => $user->id,
                    'First name' => $user->firstname,
                    'Last name' => $user->lastname
                );

                $klaviyo_custom_fields['First Course Title'] = $course->title;
                $klaviyo_custom_fields['Course Category'] =  $imploded_cat_names;

                $courseModules = ORM::for_table("courseModules")->where('courseID', $courseAssigned->courseID)->order_by_asc('ord')->find_one();
                $quiz = ORM::for_table("quizzes")->where('moduleID', $courseModules->id)->order_by_asc('id')->find_one();

                if(!empty($quiz)) {
                    $result = ORM::for_table("quizResults")->where('quizID', $quiz->id)->find_one();
                    $score = !empty($result) ? $result->percentage : ''; // fallback when a user is yet to complete a quiz
                    $klaviyo_custom_fields['Quiz Score Percentage'] = $score;
                }
            }

            if($user->subActive == 1) {
                $sub = ORM::for_table('subscriptions')->where('accountID', $user->id)->order_by_desc('whenUpdated')->find_one();
                                
                switch($sub->premiumSubPlanID) {
                    default: $subPlan = ''; break;
                    case 1: $subPlan = 'Monthly'; break;
                    case 2: $subPlan = 'Bi-Annual'; break;
                    case 3: $subPlan = 'Annual'; break;
                }

                $klaviyo_custom_fields['Subscription Plan'] = $subPlan;
                $klaviyo_custom_fields['Renewal Date'] = $sub->nextPaymentDate;

                $subSchedule = ORM::for_table('subscription_schedules')->where('subscriptionID', $sub->id)->find_one();
                $paymentStatus = $subSchedule->isPayed;

                switch($paymentStatus) {
                    default: $paymentStatus = ''; break;
                    case 0: $paymentStatus = 'Pending'; break;
                    case 1: $paymentStatus = 'Success'; break;
                    case 2: $paymentStatus = 'Failed'; break;
                }

                if($sub->status === 2) {
                    $paymentStatus = 'Expired Subscription';
                } else if($sub->status === 3) {
                    $paymentStatus = 'Cancelled Subscription';
                }

                $klaviyo_custom_fields['Payment Status'] = $paymentStatus;
            }

        }

        //Add interests a user.
        $custom_fields_format_array = [];
        foreach ($custom_fields as $field_name => $field_value) {
            $custom_fields_format_array[] = $field_name.'='.$field_value;
        }

        $data = array(
            'Name' => $name,
            'Email' => $user_email,
            //'HasExternalDoubleOptIn' => false,
            "CustomFields" => $custom_fields_format_array
        );

        $ch = curl_init( "https://api.moosend.com/v3/subscribers/{$list_id}/subscribe.json?apikey={$api_key}" );

        $payload = json_encode( $data );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

        $result = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($result);


        $klaviyo_data = new stdClass();
        $klaviyo_data->profiles = array(
            "email" => $user_email,
            "First Name" => $user->firstname,
            "Last Name" => $user->lastname,
            "Currency ID" => $currency->id,
            "Total Spend" => $user->totalSpend,
            "Total Courses" => $user->totalCourses,
            "Last Active" => $user->lastActive,
            "Sub Active" => $user->subActive,
        );

        if(!empty($klaviyo_custom_fields)) {
            $klaviyo_data->profiles["First Course Title"] = $klaviyo_custom_fields['First Course Title'] ?? '';
            $klaviyo_data->profiles["Course Category"] = $klaviyo_custom_fields['Course Category'] ?? '';
            $klaviyo_data->profiles['Quiz Score Percentage'] = $klaviyo_custom_fields['Quiz Score Percentage'] ?? '';
            
            if($user->subActive == 1) {
                $klaviyo_data->profiles['Subscription Plan'] = $klaviyo_custom_fields['Subscription Plan'];
                $klaviyo_data->profiles['Renewal Date'] = $klaviyo_custom_fields['Renewal Date'];
                $klaviyo_data->profiles['Payment Status'] = $klaviyo_custom_fields['Payment Status'] ?? '';
            }
        }

        $ch = curl_init("https://a.klaviyo.com/api/v2/list/{$klaviyo_list_id}/subscribe?api_key={$klaviyo_api_key}");
       
        $payload = json_encode( $klaviyo_data );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

        $result = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($result);

        return $result;

    }

    public function updateNewsletterSubscriber($name, $user_email, $custom_fields, $subscription_check = true) {

        $klaviyo_api_key = 'pk_a345558aa7ebf10b0f7b96f082ad8da60f';
        $api_key = '81802e21-83f2-4ed2-b604-a31a463fb046';
        $list_id = 'a0c3759c-3748-4782-bf33-08d520f7614d';

        // anything other than GBP to go into a different list
        $currency = $this->currentCurrency();
        if($currency->code != "GBP") {
            $list_id = 'fd3b8a24-f67d-4de4-bfc6-3d11978447a4';
            $klaviyo_api_key = 'pk_f14889064dfe92fb68dc63a57a3ad28b9c';
        }

        if($subscription_check) {
            if(!$this->checkIfSubscribed($user_email)) {
                return false;
            }
        }

        $custom_fields_format_array = [];
        foreach ($custom_fields as $field_name => $field_value) {
            $custom_fields_format_array[] = $field_name.'='.$field_value;
        }

        //Add interests a user.
        $data = array(
            'Name' => $name,
            'Email' => $user_email,
            //'HasExternalDoubleOptIn' => false,
            "CustomFields" => $custom_fields_format_array
        );

        $ch = curl_init( "https://api.moosend.com/v3/subscribers/{$list_id}/subscribe.json?apikey={$api_key}" );

        $payload = json_encode( $data );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
       
        $result = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($result);

        
        $personId = $this->getProfileDetails($user_email, $api_key, true);
        if(!$personId) {
            return;
        }

        $data = $this->createDataArray($user_email, $custom_fields, $klaviyo_api_key);

        $ch = curl_init( "https://a.klaviyo.com/api/v1/person/{$personId}?api_key={$klaviyo_api_key}" );


        $payload = json_encode( $data );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
       
        $result = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($result);

    }

    public function addSubscriberCourseProgress($name, $user_email, $custom_fields) {

        $klaviyo_api_key = 'pk_a345558aa7ebf10b0f7b96f082ad8da60f';
        $klaviyo_list_id = 'XLMsBN';

        // anything other than GBP to go into a different list
        $currency = $this->currentCurrency();

        if($currency->code != "GBP") {
            $klaviyo_api_key = 'pk_f14889064dfe92fb68dc63a57a3ad28b9c';
            $klaviyo_list_id = 'UFTwzg';
        }

        // subscribe to list
        $data = $this->createDataArray($user_email, $custom_fields, $klaviyo_api_key);

        $ch = curl_init("https://a.klaviyo.com/api/v2/list/{$klaviyo_list_id}/subscribe?api_key={$klaviyo_api_key}");

        $payload = json_encode( $data );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

        $result = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($result); 

        $personID = $this->getProfileDetails($user_email, $klaviyo_api_key, true);
        
        // update existing profile with the latest details
        if($personID) {
            $queryParams = "Course ID={$custom_fields['Course ID']}&Course Name={$custom_fields['Course Name']}&Course Progress={$custom_fields['Course Progress']}".
                            "&Course Category={$custom_fields['Course Category']}&Quiz Score Percentage={$custom_fields['Quiz Score Percentage']}".
                            "&Last Study Date={$custom_fields['Last Study Date']}";

            $queryParams = str_replace (' ', '%20', $queryParams);

            $ch = curl_init( "https://a.klaviyo.com/api/v1/person/{$personID}?api_key={$klaviyo_api_key}&{$queryParams}" );

            curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
            curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

            $result = curl_exec($ch);
            curl_close($ch);
            $result = json_decode($result);
        }


    }

    public function updateSubscribersListExisting() {

        exit;

        // manually ran to add existing subscription details to the subscribers moosend list
        $api_key = '81802e21-83f2-4ed2-b604-a31a463fb046';
        $list_id = 'bd1251aa-6cd2-4fd1-9e5d-68fd97beb09a';

        $subs = ORM::for_table("subscriptions")
            ->where('status', 1)
            ->where('isPremium', 1)
            ->find_many();


        foreach($subs as $sub) {

            $custom_fields_format_array = [];
            $custom_fields = array();

            // get account and check subscription plan
            $account = ORM::For_table("accounts")->find_one($sub->accountID);

            if($account->email != "") {
                $name = $account->firstname;
                $user_email = $account->email;

                $accountSubPlan = ORM::for_table("premiumSubscriptionsPlans")
                    ->find_one($sub->premiumSubPlanID);

                if($accountSubPlan->months == "") {
                    $accountSubPlan->months = "12";
                }

                $custom_fields = array(
                    'sub_type' => $accountSubPlan->months
                );

                foreach ($custom_fields as $field_name => $field_value) {
                    $custom_fields_format_array[] = $field_name.'='.$field_value;
                }

                $data = array(
                    'Name' => $name,
                    'Email' => $user_email,
                    "CustomFields" => $custom_fields_format_array
                );

                $ch = curl_init( "https://api.moosend.com/v3/subscribers/{$list_id}/subscribe.json?apikey={$api_key}" );

                $payload = json_encode( $data );
                curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
                curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));

                curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

                $result = curl_exec($ch);
                curl_close($ch);

                $result = json_decode($result);

                ?>
                <p>Sub account <?= $sub->accountID ?> was updated</p>
                <?php

                exit;
            }
        }
    }

    private function getProfileID($email, $apiKey) {

        $ch = curl_init("https://a.klaviyo.com/api/v2/people/search?email={$email}&api_key={$apiKey}");
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));

        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

        $result = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($result);

        return $result && isset($result->id) ? $result->id : null;
    }

    public function getProfileDetails($email, $apiKey, $returnProfileId = false) {

        $profileId = $this->getProfileID($email, $apiKey);

        if(empty($profileId)) {
            return;
        }

        if($returnProfileId) {
            return $profileId;
        }

        $ch = curl_init("https://a.klaviyo.com/api/v1/person/{$profileId}?api_key={$apiKey}");

        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        $result = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($result);

        // remove unnecessary fields
        $result = array_filter((array) $result);
        $result = array_diff_key($result, array_flip(['object', 'id', '$email', '$source', 'created', 'updated']));

        return $result;

    }

    private function createDataArray($email, $custom_fields, $api_key) {

        $data = new stdClass();
        $data->profiles = array(
            'email' => $email,
        );

        foreach ($custom_fields as $field_name => $field_value) {
            $data->profiles[$field_name] = $field_value;
        }

        $profile = $this->getProfileDetails($email, $api_key);

        if(!empty($profile)) {
            $profileData = array_merge($data->profiles, $profile);
            $data->profiles = array_unique($profileData, SORT_REGULAR);
        }

        return $data;
    }


    public function syncProfile() {

        $user = ORM::for_table("accounts")->find_one(CUR_ID_FRONT);

        $api_key = 'pk_a345558aa7ebf10b0f7b96f082ad8da60f';

        if($user->currencyID != 1) {
            $api_key = 'pk_f14889064dfe92fb68dc63a57a3ad28b9c';
        }


        if(!empty($user)) {
            $queryParams = "First Name={$user->firstname}&Last Name={$user->lastname}&Currency ID={$user->currencyID}".
                           "&Total Spend={$user->totalSpend}&Total Courses={$user->totalCourses}&Last Active=".
                           "{$user->lastActive}&Sub Active={$user->subActive}";

            $rewardsQueryData = $this->rewardHelpers->buildAccountRewardsQueryData($user->id);
            if ($rewardsQueryData) {
                $queryParams .= "&{$rewardsQueryData}";
            }
            $courseAssigned = ORM::for_table("coursesAssigned")->where("accountID", $user->id)->order_by_asc('id')->find_one();
            $latestCourseAssigned = ORM::for_table("coursesAssigned")->where("accountID", $user->id)->order_by_desc('id')->find_one();
           
            if(!empty($courseAssigned) && $courseAssigned->id != "") {
                
                // first course details
                $course = ORM::for_table("courses")->find_one($courseAssigned->courseID);
                $categories = array();
                $courseCategories = ORM::for_table("courseCategoryIDs")->where("course_id", $course->id)->find_many();

                foreach($courseCategories as $cat) {

                    $catData = ORM::for_table("courseCategories")->find_one($cat->category_id);
                    array_push($categories, $catData->title);
                }

                $imploded_cat_names = implode(',', $categories);

                $queryParams .= "&First Course Title={$course->title}";
                $queryParams .= "&Course Category={$imploded_cat_names}";

                // course progress details
                $latestCourse = ORM::for_table("courses")->find_one($latestCourseAssigned->courseID);
                $latestCourseCategoriesArray = array();
                $latestCourseCategories = ORM::for_table("courseCategoryIDs")->where("course_id", $latestCourse->id)->find_many();

                foreach ($latestCourseCategories as $cat) {

                    $catData = ORM::for_table("courseCategories")->find_one($cat->category_id);
                    array_push($latestCourseCategoriesArray, $catData->title);
                }

                $latest_imploded_cat_names = implode(',', $latestCourseCategoriesArray);

                $progress = number_format($latestCourseAssigned->percComplete ?? 0);

                $queryParams .= "&Course ID={$latestCourse->id}&Course Name={$latestCourse->title}&Course Category={$latest_imploded_cat_names}".
                                "&Course Progress={$progress}&User ID={$user->id}&Last Study Date={$latestCourseAssigned->lastAccessed}";


                $courseModules = ORM::for_table("courseModules")->order_by_asc('ord')->find_one($latestCourse->courseID);
                $quiz = ORM::for_table("quizzes")->where('moduleID', $courseModules->id)->order_by_desc('id')->find_one();

                $result = !empty($quiz) ? ORM::for_table("quizResults")->where('quizID', $quiz->id)->find_one() : [];
                $score = !empty($result) ? $result->percentage : 'N/A'; // N/A is the fallback when a user is yet to complete a quiz
                $queryParams .= "&Quiz Score Percentage={$score}";

                // latest course rating
                $rating = ORM::for_table("courseRatings")->where("userID", $user->id)->order_by_desc('id')->find_one();
                $starRating = !empty($rating) ? $rating->rating : 'N/A'; // N/A is the fallback when a user is yet to rate a course
                $queryParams .= "&Star Rating={$starRating}";
            }

            if($user->subActive == 1) {
                $sub = ORM::for_table('subscriptions')->where('accountID', $user->id)->order_by_desc('whenUpdated')->find_one();
                
                switch($sub->premiumSubPlanID) {
                    default: $subPlan = ''; break;
                    case 1: $subPlan = 'Monthly'; break;
                    case 2: $subPlan = 'Bi-Annual'; break;
                    case 3: $subPlan = 'Annual'; break;
                }

                $queryParams .= "&Subscription Plan ={$subPlan}";
                $queryParams .= "&Renewal Date ={$sub->nextPaymentDate}";

                $subSchedule = ORM::for_table('subscription_schedules')->where('subscriptionID', $sub->id)->find_one();
                $paymentStatus = $subSchedule->isPayed;

                switch($paymentStatus) {
                    default: $paymentStatus = ''; break;
                    case 0: $paymentStatus = 'Pending'; break;
                    case 1: $paymentStatus = 'Success'; break;
                    case 2: $paymentStatus = 'Failed'; break;
                }

                if($sub->status === 2) {
                    $paymentStatus = 'Expired Subscription';
                } else if($sub->status === 3) {
                    $paymentStatus = 'Cancelled Subscription';
                }

                $queryParams .= "&Payment Status={$paymentStatus}";
            }

            $queryParams = str_replace (' ', '%20', $queryParams);

            $profileId = $this->getProfileDetails($user->email, $api_key, true);

            $ch = curl_init( "https://a.klaviyo.com/api/v1/person/{$profileId}?api_key={$api_key}&{$queryParams}" );

            curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));

            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");

            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

            $result = curl_exec($ch);
            curl_close($ch);

            $result = json_decode($result);

            return $result;
        }

    }

    public function placedOrderEvent($order, $historicOrder = false)
    {
        $api_key = 'pk_a345558aa7ebf10b0f7b96f082ad8da60f';
        $time = Carbon::now();

        // anything other than GBP to go into the ROW tracked events
        $currency = $this->currentCurrency();
        $site = SITE_TYPE;

        // this event is used to sync older orders in which case we pull the relevant data from the order itself
        if($historicOrder) {
            $time = $order->whenCreated;
            $currency = ORM::for_table('currencies')->find_one($order->currencyID);
            $site = $order->site;
        }

        if($currency->code != "GBP" || $site != "uk") {
            $api_key = 'pk_f14889064dfe92fb68dc63a57a3ad28b9c';
        }
        
        $orders = new orderController();
        $orderItems = $orders->getOrderItemsByOrderID($order->id);
        $customerID = $order->accountID;

        if($order->couponID) $coupon = ORM::for_table('coupons')->find_one($order->couponID);

        if($customerID) $customer = ORM::for_table('accounts')->find_one($customerID);

        $items = $itemNames =  $ItemCategories = [];
        
        foreach($orderItems as $orderItem) {

            if($orderItem->courseID) {
                $course = new courseController();

                $productID = $orderItem->courseID;
                $product = ORM::for_table('courses')->find_one($productID);
                $productName = $product->title;
                $productUrl = 'start/'.$product->slug;
                $image = $course->getCourseImage($productID);
                $courseCategories = implode(',', $course->getCourseCategories($productID));

            } else if($orderItem->journeyID) {
                $journey = new journeyController();

                $productID = $orderItem->journeyID;
                $product = ORM::for_table('journeys')->find_one($productID);
                $productName = $product->title;
                $productUrl = 'journeys/'.$product->slug;
                $image = $journey->getJourneyImage($productID);
                $courseCategories = $product->careers;

            } else if($orderItem->premimumSubPlanID) {
                $productID = $orderItem->premimumSubPlanID;
                $product = ORM::for_table('premiumSubscriptionsPlans')->find_one($productID);
                $productName = $product->months . ' month Unlimited Learning Plan';
                $productURL = 'subscription';
            }

            $item = new stdClass();
            $item->ProductID = $productID;
            $item->SKU = $productID;
            $item->ProductName = $productName;
            $item->Quantity = 1;
            $item->ItemPrice = $orderItem->price;
            $item->RowTotal = $orderItem->price;
            $item->ProductURL = SITE_URL.$productUrl;
            $item->ImageURL = $image;
            $item->Categories = $courseCategories ?? [];
            $item->Brand = 'New Skills Academy';

            array_push($items, $item);
            array_push($itemNames, $productName);
            array_push($ItemCategories, $courseCategories ?? '');

            $this->orderedProductEvent($api_key, $order, $item, $time);
        }

        $data = new stdClass();
        $data->token = $api_key;
        $data->event = 'Placed Order';

        $data->customer_properties = new stdClass();
        $data->customer_properties->email = $order->email;
        $data->customer_properties->first_name = $order->firstname;
        $data->customer_properties->last_name = $order->lastname;
        $data->customer_properties->phone_number = $customer ? $customer->phone : '';
        $data->customer_properties->address1 = $order->address1 ?? '';
        $data->customer_properties->address2 = $order->address2 ?? '';
        $data->customer_properties->city = $order->city ?? '';
        $data->customer_properties->zip = $order->postcode ?? '';
        $data->customer_properties->region = $order->county ?? '';
        $data->customer_properties->country = $order->country ?? '';

        $data->properties = new stdClass();
        $data->properties->event_id = $order->id . '-' . $time;
        $data->properties->value = $order->total;
        $data->properties->OrderId = $order->id;
        $data->properties->Categories = implode(',', $ItemCategories);
        $data->properties->ItemNames = $itemNames;
        $data->properties->Brands = 'New Skills Academy';
        $data->properties->DiscountCode = $coupon ? $coupon->code : '';
        $data->properties->DiscountValue = $coupon ? $coupon->value : 0;

        $data->properties->Items = $items;

        $address = new stdClass();
        $address->FirstName = $order->firstname ?? '';
        $address->LastName = $order->lastname ?? '';
        $address->Company = '';
        $address->Address1 = $order->address1 ?? '';
        $address->Address2 = $order->address2 ?? '';
        $address->City = $order->city ?? '';
        $address->Region = $order->county ?? '';
        $address->RegionCode = '';
        $address->Country = $order->country ?? '';
        $address->CountryCode = '';
        $address->Zip = $order->postcode ?? '';
        $address->Phone = $customer ? $customer->phone : '';

        $data->properties->BillingAddress = $address;
        $data->properties->ShippingAddress = $address;
        
        $data->time = $time;

        $ch = curl_init( "https://a.klaviyo.com/api/track" );
        
        $payload = json_encode( $data );

        curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

        $result = curl_exec($ch);

        curl_close($ch);

        $result = json_decode($result);

        return $result;
    }

    public function orderedProductEvent($api_key, $order, $item, $time)
    {
        $data = new stdClass();
        $data->token = $api_key;
        $data->event = 'Ordered Product';

        $data->customer_properties = new stdClass();
        $data->customer_properties->{'$email'} = $order->email;
        $data->customer_properties->email = $order->email;
        $data->customer_properties->first_name = $order->firstname;
        $data->customer_properties->last_name = $order->lastname;

        $data->properties = new stdClass();
        $data->properties->event_id = $order->id . '-' . $time;
        $data->properties->value = $item->ItemPrice;
        $data->properties->OrderId = $order->id;
        $data->properties->ProductID = $item->ProductID;
        $data->properties->SKU = $item->ProductID;
        $data->properties->ProductName = $item->ProductName;
        $data->properties->Quantity = 1;
        $data->properties->ProductURL = (str_contains($item->ProductURL, 'start')) ? str_replace('start', 'course',$item->ProductURL) : $item->ProductURL;
        $data->properties->ImageURL = $item->ImageURL;
        $data->properties->Categories = $item->Categories;
        $data->properties->ProductBrand = 'New Skills Academy';

        $data->time = $time;

        $ch = curl_init( "https://a.klaviyo.com/api/track" );
        
        $payload = json_encode( $data );

        curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

        $result = curl_exec($ch);

        curl_close($ch);

        $result = json_decode($result);

        return $result;
    }


    public function addUserRatingField($userID, $rating)
    {
        $user = ORM::For_table("accounts")->find_one($userID);

        if($user) {

            $klaviyo_api_key = 'pk_a345558aa7ebf10b0f7b96f082ad8da60f';

            $currency = $this->currentCurrency();
            if($currency->code != "GBP" || SITE_TYPE != "uk") {
                $klaviyo_api_key = 'pk_f14889064dfe92fb68dc63a57a3ad28b9c';
            }

            $personID = $this->getProfileDetails($user->email, $klaviyo_api_key, true);
            if($personID) {

                $queryParams = "Star Rating={$rating}";
                $queryParams = str_replace (' ', '%20', $queryParams);

                $ch = curl_init( "https://a.klaviyo.com/api/v1/person/{$personID}?api_key={$klaviyo_api_key}&{$queryParams}" );

                curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

                $result = curl_exec($ch);
                curl_close($ch);
                $result = json_decode($result);
            }

        }
    }


}