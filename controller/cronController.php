<?php

require_once(__DIR__ . '/moosendController.php');

class cronController extends Controller {

    /**
     * @var moosendController
     */
    protected $moosend;

    public function __construct()
    {
        $this->moosend = new moosendController();
    }

    public function courseAverageRating() {

        $courses = ORM::for_table("courses")->find_many();

        foreach($courses as $course) {

            $total = 0;
            $ratings = ORM::for_table("courseRatings")->where("courseID", $course->id)->find_many();

            foreach($ratings as $rating) {

                $total = $total+$rating->rating;

            }

            $average = number_format($total/count($ratings), 2);

            $update = ORM::for_table("courses")->find_one($course->id);

            $update->averageRating = $average;
            $update->totalRatings = count($ratings);

            $update->save();

        }

        echo "Rating averages successfully updated";

    }

    public function courseEnrollmentCount() {

        $courses = ORM::for_table("courses")->find_many();

        foreach($courses as $course) {

            $enrollments = ORM::for_table("coursesAssigned")->where("courseID", $course->id)->count();

            $update = ORM::for_table("courses")->find_one($course->id);

            $update->enrollmentCount = $enrollments;

            $update->save();

        }

        echo "Counts successfully updated";

    }

    public function checkTodaysGiftSends() {

        $items = ORM::for_table("orderItems")
            ->where("gift", "1")
            ->where("giftSent", "0")
            ->where("giftDate", date('Y-m-d'))
            ->find_many();

        foreach($items as $item) {

            $order = ORM::for_table("orders")->find_one($item->orderID);

            $course = ORM::for_table("courses")->find_one($item->courseID);

            $link = SITE_URL.'?claim=gift&token='.$item->giftToken.'&email='.urlencode($item->giftEmail);

            $message = '<p>Hi there,</p>
        <p>You have been gifted the <strong>'.$course->title.'</strong> course by <strong>'.$order->firstname.' '.$order->lastname.'</strong>.</p>
        <p>This course is already paid for, so you can take it at any time via New Skills Academy. All you need to do is click the Claim Gift button below and follow the steps.</p>
       ';

            $message .= $this->renderHtmlEmailButton("Claim Gift", $link);

            $this->sendEmail($item->giftEmail, $message, "You've been gifted a course from ".$order->firstname." ".$order->lastname);

            // update record
            $update = ORM::for_table("orderItems")->find_one($item->id);
            $update->giftSent = "1";
            $update->save();

        }

        echo count($items).' sent';


    }

    public function initialUserReportingData() {

        // generates initial data for totalSpend and totalCourses

        $accounts = ORM::for_table("accounts")
            ->where("initialReportingData", "0")
            ->limit(5000)
            ->find_many();

        foreach($accounts as $account) {

            $totalCourses = ORM::for_table("coursesAssigned")
                ->where_null("bundleID")
                ->where("sub", "0")
                ->where_not_equal("courseID", "342")
                ->where("accountID", $account->id)
                ->count();

            $totalSpend = 0;

            $orders = ORM::for_table("orders")->select("total")->where("accountID", $account->id)->where("status", "completed")->find_many();

            foreach($orders as $order) {

                $totalSpend = $totalSpend+$order->total;

            }

            $update = ORM::For_Table("accounts")->find_one($account->id);

            $update->totalCourses = $totalCourses;
            $update->totalSpend = number_format($totalSpend, 2);
            $update->initialReportingData = "1";

            $update->save();


        }

        echo "complete";

    }

    public function currencyConversionRates() {

        // gets updated conversion rates every 5 mins
        $currencies = ORM::for_table("currencies")->where_not_equal("id", "1")->find_many();

        foreach($currencies as $currency) {

            $data = unserialize(file_get_contents('http://www.geoplugin.net/currency/php.gp?from=GBP&to='.$currency->code.'&amount=1'));

            $this->debug($data);

        }

    }

    public function updateOrderGbpValues() {

        // updates GBP order values for orders before the US/int. site launch

        $orders = ORM::for_table("orders")
            ->where("status", "completed")
            ->where_not_equal("total", "0")
            ->where("totalGBP", "0")
            ->order_by_desc("id")
            ->limit(10000)
            ->find_many();

        foreach($orders as $order) {

            if($order->usImport == "1") {
                $currency = ORM::for_table("currencies")->find_one(2);
            } else {
                $currency = ORM::for_table("currencies")->find_one($order->currencyID);
            }

            $update = ORM::for_table("orders")->find_one($order->id);

            $update->totalGBP = number_format($order->total*$currency->convRate, 2);

            $update->save();

        }

    }

    public function getConversionRate() {

        // cron run regularly to get latest conversion rates for GBP

        $currencies = ORM::for_table("currencies")->where_not_equal("code", "GBP")->find_many();

        foreach($currencies as $currency) {

            // Fetching JSON
            $req_url = 'https://v6.exchangerate-api.com/v6/658f1e3b9e641adb9088c1ef/latest/'.$currency->code;
            $response_json = file_get_contents($req_url);

            // Continuing if we got a result
            if(false !== $response_json) {

                // Try/catch for json_decode operation
                try {

                    // Decoding
                    $response = json_decode($response_json);

                    // Check for success
                    if('success' === $response->result) {

                        $base_price = 1; // Your price in USD
                        $price = round(($base_price * $response->conversion_rates->GBP), 2);

                        // update currency with new price
                        $update = ORM::for_table("currencies")->find_one($currency->id);

                        $update->convRate = $price;

                        $update->save();

                    }

                }
                catch(Exception $e) {
                    // Handle JSON parse error...
                    echo "error";
                }

            }

        }

        echo "Conversion rates pulled successfully";

    }

    public function setUkOnlyPricing() {


        $courses = ORM::for_table("courses")->where("usImport", "0")->find_many();

        foreach($courses as $course) {

            $pricing = ORM::for_table("coursePricing")->where_not_equal("currencyID", "1")->where("courseID", $course->id)->find_many();

            foreach($pricing as $price) {

                $update = ORM::for_table("coursePricing")->find_one($price->id);

                $update->available = "0";

                $update->save();

            }

        }

    }

    public function divideUsCourseDuration() {

        // resets course duration

        $courses = ORM::for_table("courses")->where("usImport", "1")->find_many();

        foreach($courses as $course) {

            $update = ORM::for_table("courses")->find_one($course->id);

            $update->duration = $course->duration/60;

            $update->save();

        }

    }

    public function duplicateModuleSlugs() {

        exit;

        $items = ORM::for_table("courseModules")->find_many();

        foreach($items as $item) {

            $existing = ORM::for_table("courseModules")->where("slug", $item->slug)->count();

            if($existing > 0) {

                $update = ORM::for_table("courseModules")->find_one($item->id);

                $update->slug = $item->slug.'-'.rand(5,555);

                $update->save();

            }

        }


    }

    public function normaliseCourseCompletionPercentages() {

        $assigned = ORM::for_table("coursesAssigned")->where_gt("lastAccessed", date('Y-m-d H:i:s', strtotime("-15 minutes")))->where("completed", "0")->find_many();

        echo count($assigned);

        foreach($assigned as $assign) {

            $totalModules = ORM::for_table("courseModules")->where("courseID", $assign->courseID)->count();

            $percentage = number_format(($assign->currentModuleKey / $totalModules) * 100, 2);

            $update = ORM::for_table("coursesAssigned")->find_one($assign->id);
            $update->percComplete = $percentage;
            $update->save();

            ?>
            <p><?= $assign->id ?> = <?= $percentage ?></p>
            <?php

        }

    }

    public function syncKlaviyoProfiles()
    {
        require_once(APP_ROOT_PATH . 'helpers/RewardHelpers.php');
        $rewardHelper = new RewardHelpers();

        $api_key = 'pk_a345558aa7ebf10b0f7b96f082ad8da60f';
        $list_id = 'ScERE5';
        $course_progress_list_id = 'XLMsBN';

        // anything other than GBP to go into a different list
        $currency = $this->currentCurrency();

        if ($currency->code != "GBP") {
            $api_key = 'pk_f14889064dfe92fb68dc63a57a3ad28b9c';
            $list_id = 'XbKJti';
            $course_progress_list_id = 'UFTwzg';
        }

        $klaviyo_custom_fields = [];

        $activeSubscriptions = ORM::for_table('accounts')->where_not_equal('subActive', '0')->limit(200)->order_by_expr('RAND()')->find_many();

        foreach ($activeSubscriptions as $user) {

            $queryParams = "First Name={$user->firstname}&Last Name={$user->lastname}&Currency ID={$user->currencyID}" .
                "&Total Spend={$user->totalSpend}&Total Courses={$user->totalCourses}&Last Active=" .
                "{$user->lastActive}&Sub Active={$user->subActive}";

            $rewardsQueryData = $rewardHelper->buildAccountRewardsQueryData($user->id);
            if ($rewardsQueryData) {
                $queryParams .= "&{$rewardsQueryData}";
            }
            $courseAssigned = ORM::for_table("coursesAssigned")->where("accountID", $user->id)->order_by_asc('id')->find_one();
            $latestCourseAssigned = ORM::for_table("coursesAssigned")->where("accountID", $user->id)->order_by_desc('id')->find_one();

            if (!empty($courseAssigned) && $courseAssigned->id != "") {

                // first course details
                $firstCourse = ORM::for_table("courses")->find_one($courseAssigned->courseID);
                $categories = array();
                $courseCategories = ORM::for_table("courseCategoryIDs")->where("course_id", $firstCourse->id)->find_many();

                foreach ($courseCategories as $cat) {

                    $catData = ORM::for_table("courseCategories")->find_one($cat->category_id);
                    array_push($categories, $catData->title);
                }

                $imploded_cat_names = implode(',', $categories);

                $queryParams .= "&First Course Title={$firstCourse->title}";
                $queryParams .= "&Course Category={$imploded_cat_names}";

                $klaviyo_custom_fields['First Course Title'] = $firstCourse->title;
                $klaviyo_custom_fields['Course Category'] = $imploded_cat_names;

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

                $klaviyo_custom_fields['Course ID'] = $latestCourse->id;
                $klaviyo_custom_fields['Course Name'] = $latestCourse->title;
                $klaviyo_custom_fields['Course Category'] = $latest_imploded_cat_names;
                $klaviyo_custom_fields['Course Progress'] = $progress;
                $klaviyo_custom_fields['User ID'] = $user->id;
                $klaviyo_custom_fields['Last Study Date'] = $latestCourseAssigned->lastAccessed;

                $courseModules = ORM::for_table("courseModules")->order_by_asc('ord')->find_one($latestCourse->id);
                $quiz = ORM::for_table("quizzes")->where('moduleID', $courseModules->id)->order_by_desc('id')->find_one();

                $result = !empty($quiz) ? ORM::for_table("quizResults")->where('quizID', $quiz->id)->find_one() : [];
                $score = !empty($result) ? $result->percentage : 'N/A'; // N/A is the fallback when a user is yet to complete a quiz
                $queryParams .= "&Quiz Score Percentage={$score}";
                $klaviyo_custom_fields['Quiz Score Percentage'] = $score;

                // latest course rating
                $rating = ORM::for_table("courseRatings")->where("userID", $user->id)->order_by_desc('id')->find_one();
                $starRating = !empty($rating) ? $rating->rating : 'N/A'; // N/A is the fallback when a user is yet to rate a course
                $queryParams .= "&Star Rating={$starRating}";
                $klaviyo_custom_fields['Star Rating'] = $starRating;
            }

            $sub = ORM::for_table('subscriptions')->where('accountID', $user->id)->order_by_desc('whenUpdated')->find_one();


            if ($sub->id != "") {

                switch ($sub->premiumSubPlanID) {
                    default:
                        $subPlan = '';
                        break;
                    case 1:
                        $subPlan = 'Monthly';
                        break;
                    case 2:
                        $subPlan = 'Bi-Annual';
                        break;
                    case 3:
                        $subPlan = 'Annual';
                        break;
                }

                $queryParams .= "&Subscription Plan ={$subPlan}";
                $queryParams .= "&Renewal Date ={$sub->nextPaymentDate}";

                $paymentStatus = $sub->status ?? 2;

                switch ($sub->status) {
                    default:
                        $paymentStatus = 'Success';
                        break;
                    case 0:
                        $paymentStatus = 'Success';
                        break;
                    case 1:
                        $paymentStatus = 'Success';
                        break;
                    case 2:
                        $paymentStatus = 'Expired';
                        break;
                    case 3:
                        $paymentStatus = 'Cancelled';
                }

                $queryParams .= "&Payment Status={$paymentStatus}";

                $klaviyo_custom_fields['Subscription Plan'] = $subPlan;
                $klaviyo_custom_fields['Renewal Date'] = $sub->nextPaymentDate;
                $klaviyo_custom_fields['Payment Status'] = $paymentStatus;

            }

            $profileId = $this->moosend->getProfileDetails($user->email, $api_key, true);

            if ($profileId) {


                $queryParams = str_replace (' ', '%20', $queryParams);

                $ch = curl_init( "https://a.klaviyo.com/api/v1/person/{$profileId}?api_key={$api_key}&{$queryParams}" );

                curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

                $result = curl_exec($ch);
                curl_close($ch);

                $klaviyo_data = new stdClass();

                $klaviyo_data->profiles = array(
                    "email" => $user->email,
                    "First Name" => $user->firstname,
                    "Last Name" => $user->lastname,
                    "Currency ID" => $currency->id,
                    "Total Spend" => $user->totalSpend,
                    "Total Courses" => $user->totalCourses,
                    "Last Active" => $user->lastActive,
                    "Sub Active" => $user->subActive,
                );

                if (!empty($klaviyo_custom_fields)) {
                    $klaviyo_data->profiles["First Course Title"] = $klaviyo_custom_fields['First Course Title'] ?? '';
                    $klaviyo_data->profiles["Course Category"] = $klaviyo_custom_fields['Course Category'] ?? '';

                    $klaviyo_data->profiles["Course ID"] = $klaviyo_custom_fields['Course ID'] ?? '';
                    $klaviyo_data->profiles["Course Name"] = $klaviyo_custom_fields['Course Name'] ?? '';
                    $klaviyo_data->profiles["Course Category"] = $klaviyo_custom_fields['Course Category'] ?? '';
                    $klaviyo_data->profiles["Course Progress"] = $klaviyo_custom_fields['Course Progress'] ?? '';
                    $klaviyo_data->profiles["User ID"] = $klaviyo_custom_fields['User ID'] ?? '';
                    $klaviyo_data->profiles["Last Study Date"] = $klaviyo_custom_fields['Last Study Date'] ?? '';
                    $klaviyo_data->profiles['Quiz Score Percentage'] = $klaviyo_custom_fields['Quiz Score Percentage'] ?? '';

                    $klaviyo_data->profiles['Star Rating'] = $klaviyo_custom_fields['Star Rating'] ?? '';

                    $klaviyo_data->profiles['Subscription Plan'] = $klaviyo_custom_fields['Subscription Plan'] ?? '';
                    $klaviyo_data->profiles['Renewal Date'] = $klaviyo_custom_fields['Renewal Date'] ?? '';
                    $klaviyo_data->profiles['Payment Status'] = $klaviyo_custom_fields['Payment Status'] ?? '';

                }

                $ch = curl_init("https://a.klaviyo.com/api/v2/list/{$list_id}/subscribe?api_key={$api_key}");

                $payload = json_encode($klaviyo_data);

                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                $result = curl_exec($ch);
                curl_close($ch);


                // sub to course progress list too
                $ch = curl_init("https://a.klaviyo.com/api/v2/list/{$course_progress_list_id}/subscribe?api_key={$api_key}");

                $payload = json_encode($klaviyo_data);

                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                $result = curl_exec($ch);
                curl_close($ch);
            } // create new profile & add to subscribers list if it doesn't already exist
            else {
                $klaviyo_data = new stdClass();
                $klaviyo_data->profiles = array(
                    "email" => $user->email,
                    "First Name" => $user->firstname,
                    "Last Name" => $user->lastname,
                    "Currency ID" => $currency->id,
                    "Total Spend" => $user->totalSpend,
                    "Total Courses" => $user->totalCourses,
                    "Last Active" => $user->lastActive,
                    "Sub Active" => $user->subActive,
                );

                if (!empty($klaviyo_custom_fields)) {
                    $klaviyo_data->profiles["First Course Title"] = $klaviyo_custom_fields['First Course Title'] ?? '';
                    $klaviyo_data->profiles["Course Category"] = $klaviyo_custom_fields['Course Category'] ?? '';

                    $klaviyo_data->profiles["Course ID"] = $klaviyo_custom_fields['Course ID'] ?? '';
                    $klaviyo_data->profiles["Course Name"] = $klaviyo_custom_fields['Course Name'] ?? '';
                    $klaviyo_data->profiles["Course Category"] = $klaviyo_custom_fields['Course Category'] ?? '';
                    $klaviyo_data->profiles["Course Progress"] = $klaviyo_custom_fields['Course Progress'] ?? '';
                    $klaviyo_data->profiles["User ID"] = $klaviyo_custom_fields['User ID'] ?? '';
                    $klaviyo_data->profiles["Last Study Date"] = $klaviyo_custom_fields['Last Study Date'] ?? '';
                    $klaviyo_data->profiles['Quiz Score Percentage'] = $klaviyo_custom_fields['Quiz Score Percentage'] ?? '';

                    $klaviyo_data->profiles['Star Rating'] = $klaviyo_custom_fields['Star Rating'] ?? '';

                    if ($user->subActive == '1') {
                        $klaviyo_data->profiles['Subscription Plan'] = $klaviyo_custom_fields['Subscription Plan'] ?? '';
                        $klaviyo_data->profiles['Renewal Date'] = $klaviyo_custom_fields['Renewal Date'] ?? '';
                        $klaviyo_data->profiles['Payment Status'] = $klaviyo_custom_fields['Payment Status'] ?? '';
                    }
                }

                $ch = curl_init("https://a.klaviyo.com/api/v2/list/{$list_id}/subscribe?api_key={$api_key}");

                $payload = json_encode($klaviyo_data);


                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                // sub to course progress list too
                $ch = curl_init("https://a.klaviyo.com/api/v2/list/{$course_progress_list_id}/subscribe?api_key={$api_key}");

                $payload = json_encode($klaviyo_data);

                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                $result = curl_exec($ch);
                curl_close($ch);
            }

        }

    }


        public function courseBoardStats() {

        $courses = ORM::for_table("courses")->find_many();

        $dateFrom = $this->get["dateFrom"];

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=COURSE_STATS_FOR_BOARD_'
            .time().'.csv');

        $output = fopen('php://output', 'w');


        fputcsv($output, array(
            'COURSE', 'NON_SUB_ENROLMENTS', 'SUB_ENROLMENTS', 'REV', 'ENROLMENT_PURCHASES'
        ));

        foreach($courses as $course) {

            $nonSub = ORM::for_table("coursesAssigned")
                ->where("courseID", $course->id)
                ->where_gt("whenAssigned", $dateFrom.' 00:00:00')
                ->where("sub", "0")
                ->count();

            $sub = ORM::for_table("coursesAssigned")
                ->where("courseID", $course->id)
                ->where_gt("whenAssigned", $dateFrom.' 00:00:00')
                ->where("sub", "1")
                ->count();

            $orderItems = ORM::for_table("orderItems")->where("courseID", $course->id)->where_gt("whenCreated", $dateFrom.' 00:00:00')->find_many();

            $rev = 0;
            $purchases = 0;

            foreach($orderItems as $item) {

                $order = ORM::for_table("orders")->find_one($item->orderID);

                if($order->status == "completed"){

                    $rev = $rev+$item->price;
                    $purchases ++;

                }

            }

            if($rev < 0) {
                $rev = 0;
            }

            fputcsv($output, array(
                $course->title,
                $nonSub,
                $sub,
                number_format($rev, 2),
                $purchases
            ));

        }

    }

    public function courseCompletionsReport() {


        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=COURSE_STATS_'
            .time().'.csv');

        $courses = ORM::for_table("coursesAssigned")->where_gt("whenAssigned", '2022-08-09 00:00:00')->where_lt("whenAssigned", '2022-09-09 00:00:00')->find_many();

        $output = fopen('php://output', 'w');


        fputcsv($output, array(
            'COURSE', 'STUDENT', 'COMPLETED', 'COMPLETION_RATE', 'DATE_ADDED'
        ));

        foreach($courses as $assigned) {

            $course = ORM::for_table("courses")->find_one($assigned->courseID);

            $user = ORM::for_table("accounts")->find_one($assigned->accountID);

            fputcsv($output, array(
                $course->title,
                $user->email,
                $assigned->completed,
                $assigned->percComplete,
                $assigned->whenAssigned
            ));

        }

    }


    public function syncOrdersToKlaviyo()
    {
        $orders = ORM::for_table('orders')->limit(200)->order_by_expr('RAND()')->find_many();

        foreach ($orders as $order) {

            $this->moosend->placedOrderEvent($order, true);

        }

    }
    
     public function average() {
        
        $subscriptions = ORM::for_table("subscriptions")->find_many();
        
        $averageDays = 0;
        
        foreach($subscriptions as $sub) {
            
            $endDate = date('Y-m-d');
            
            if($sub->elapsedDate != "") {
                
                $endDate = $sub->elapsedDate;
        
            }
            
            $now = strtotime($endDate); 
            $your_date = strtotime($sub->whenAdded);
            $datediff = $now - $your_date;
            
            $averageDays = $averageDays+round($datediff / (60 * 60 * 24));
            
            
        }
        
        
        ?>
        <p>Overall average of <?= $averageDays/count($subscriptions). ' days' ?></p>
        <?php
        
        $subscriptions = ORM::for_table("subscriptions")->where("premiumSubPlanID", "1")->find_many();
        
        $averageDays = 0;
        
        foreach($subscriptions as $sub) {
            
            $endDate = date('Y-m-d');
            
            if($sub->elapsedDate != "") {
                
                $endDate = $sub->elapsedDate;
        
            }
            
            $now = strtotime($endDate); 
            $your_date = strtotime($sub->whenAdded);
            $datediff = $now - $your_date;
            
            $averageDays = $averageDays+round($datediff / (60 * 60 * 24));
            
            
        }
        
        
        ?>
        <p>Monthly sub average of <?= $averageDays/count($subscriptions). ' days' ?></p>
        <?php
        
        $subscriptions = ORM::for_table("subscriptions")->where("premiumSubPlanID", "3")->find_many();
        
        $averageDays = 0;
        
        foreach($subscriptions as $sub) {
            
            $endDate = date('Y-m-d');
            
            if($sub->elapsedDate != "") {
                
                $endDate = $sub->elapsedDate;
        
            }
            
            $now = strtotime($endDate); 
            $your_date = strtotime($sub->whenAdded);
            $datediff = $now - $your_date;
            
            $averageDays = $averageDays+round($datediff / (60 * 60 * 24));
            
            
        }
        
        
        ?>
        <p>Yearly sub average of <?= $averageDays/count($subscriptions). ' days' ?></p>
        <?php
        
    }



}