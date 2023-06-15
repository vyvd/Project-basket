<?php

require_once(__DIR__ . '/courseController.php');
require_once(__DIR__ . '/mediaController.php');
require_once(__DIR__ . '/rewardsAssignedController.php');
require_once(__DIR__ . '/moosendController.php');
require_once(__DIR__ . '/emailTemplateController.php');
require_once(__DIR__ . '/../services/SocialiteService.php');

use SocialiteService;

class accountController extends Controller
{
    /**
     * @var mediaController
     */
    protected $medias;

    /**
     * @var rewardsAssignedController
     */
    protected $rewardsAssigned;

    /**
     * @var courseController
     */
    protected $courses;

    /**
     * @var emailTemplateController
     */
    protected $emailTemplates;

    /**
     * @var moosendController
     */
    protected $moosend;

    /**
     * @var SocialiteService
     */
    protected $socialiteService;

    public function __construct()
    {
        $this->medias = new mediaController();
        $this->courses = new courseController();
        $this->rewardsAssigned = new rewardsAssignedController();
        $this->emailTemplates = new emailTemplateController();
        $this->moosend = new moosendController();
        $this->socialiteService = new SocialiteService();
        $this->post = $_POST;
        $this->get = $_GET;
    }

    public function social_login($provider = null)
    {
        $provider = $_GET['provider'] ?? $provider;

        if ($provider == 'google') {
            $redirectUrl = $this->socialiteService->googleAuthorize();
        } else {
            $redirectUrl = $this->socialiteService->authorize($provider);
        }

        header('Location: ' . $redirectUrl);
        die;
    }

    public function social_login_callback($provider = null)
    {
        $provider = $_GET['provider'] ?? $provider;

        if ($provider == 'google') {
            $user = $this->socialiteService->googleCallback();
        } else {
            $user = $this->socialiteService->callback($provider);
        }

        $accountData = [];

        if (@$user->email) {
            $account = $this->getAccountByEmail($user->email);
        } else {
            if ($provider == 'facebook') {
                $account = ORM::for_table('accounts')->where('fbID', $user->id)->find_one();
                if (empty($account) || (@$account && ($account->email == $user->id))) {
                    $name = @$user->fullname ? explode(' ', $user->fullname) : [$user->id, $user->id];

                    $providerLogin = [
                        'fbID' => $user->id,
                        'firstname' => @$user->firstname ? $user->firstname : ($name[0] ?? null),
                        'lastname' => @$user->lastname ? $user->lastname : ($name[1] ?? null),
                        'imageUrl' => $user->pictureURL ?? null
                    ];

                    $_SESSION["providerLogin"] = $providerLogin;
                    $url = $_SESSION["currentPageUrl"] ?? SITE_URL;
                    header('Location: ' . $url);
                }
            }
        }

        if ($provider == 'facebook') {
            $accountData['fbID'] = $user->id;

            if (empty($account)) {
                $account = ORM::for_table('accounts')
                    ->where('fbID', $user->id)
                    ->find_one();
                $accountData['email'] = $user->email ?? $user->id;
            }

            if (empty($account)) {
                $name = @$user->fullname ? explode(' ', $user->fullname) : [$user->id, $user->id];
                $accountData['firstname'] = $name[0];
                $accountData['lastname'] = $name[1] ?? null;
                if (@$name[1]) {
                    $accountData['lastname'] = $name[1];
                }
                if (@$name[2]) {
                    $accountData['lastname'] = $name[1] . " " . $name[2];
                }
                $accountData['password'] = password_hash($this->randomString(), PASSWORD_BCRYPT);
                $accountData['whenCreated'] = date("Y-m-d H:i:s");
                $accountData['whenUpdated'] = $accountData['whenCreated'];
                if (@$user->pictureURL) {
                    $accountData['wpImage']['full'] = $user->pictureURL;
                }
            }
        } elseif ($provider == 'google') {
            if (empty($account)) {
                $accountData['email'] = $user->email ?? $user->id;
                $accountData['firstname'] = $user->firstname;
                $accountData['lastname'] = $user->lastname;
                $accountData['password'] = password_hash($this->randomString(), PASSWORD_BCRYPT);
                $accountData['whenCreated'] = date("Y-m-d H:i:s");
                $accountData['whenUpdated'] = $accountData['whenCreated'];
                if (@$user->pictureURL) {
                    $accountData['wpImage']['full'] = $user->pictureURL;
                }
            }
        }
        // echo "<pre>";
        // print_r($accountData);
        // die;

        if (@$account->id) {
            $accountData['id'] = $account->id;
        }
//        echo "<pre>";
//        print_r($name);
//        print_r($accountData);
//        die;
        $account = $this->saveAccount($accountData);
        $response = $this->accountSignIn($account, true);
        if ($response['error'] == true) {
            $_SESSION["loginErrorMessage"] = $response['message'];
        }
        $url = $response['redirect'] ?? ($_SESSION["currentPageUrl"] ?? SITE_URL);
        header('Location: ' . $url);
    }

    public function providerLogin()
    {
        $request = $this->post;
        $providerLogin = $_SESSION['providerLogin'];

        // validate email
        $existingEmail = ORM::for_table("accounts")->where("email", $request["email"])->find_one();
        if (!filter_var($request["email"], FILTER_VALIDATE_EMAIL) || $existingEmail->email != "") {
            ?>
            <script type="text/javascript">
                $("input[name='email']").addClass("form-error");
            </script>
            <?php
            $this->setToastDanger('Please make sure your email address is properly formatted and not in use on another account.');
            exit;
        }


        $accountData['fbID'] = $providerLogin['fbID'];

        if (empty($account)) {
            $account = ORM::for_table('accounts')
                ->where('fbID', $accountData['fbID'])
                ->find_one();
            $accountData['email'] = $request["email"] ?? $accountData['fbID'];
        }

        if (empty($account)) {
            $accountData['firstname'] = $providerLogin['firstname'];
            $accountData['lastname'] = $providerLogin['lastname'] ?? null;


            $accountData['password'] = password_hash($this->randomString(), PASSWORD_BCRYPT);
            $accountData['whenCreated'] = date("Y-m-d H:i:s");
            $accountData['whenUpdated'] = $accountData['whenCreated'];
            if (@$providerLogin['imageUrl']) {
                $accountData['wpImage']['full'] = $providerLogin['imageUrl'];
            }
        }

        if (@$account->id) {
            $accountData['id'] = $account->id;
        }

        $account = $this->saveAccount($accountData);
        $response = $this->accountSignIn($account, true);
        unset($_SESSION['providerLogin']);
        //
//        if($response['error'] == true) {
//            $_SESSION["loginErrorMessage"] = $response['message'];
//        }
//        $url = $response['redirect'] ?? ($_SESSION["currentPageUrl"] ?? SITE_URL);
        exit;
    }

    private function checkCredentialsOldSite($email, $password)
    {

        $url = IMPORT_BASE_URL . 'check_login.php';
        $fields = array(
            'email' => urlencode($email),
            'password' => urlencode($password)
        );

        foreach ($fields as $key => $value) {
            $fields_string .= $key . '=' . $value . '&';
        }
        rtrim($fields_string, '&');

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, count($fields));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);

        curl_close($ch);

        return $result; // yes or no

    }

    private function checkAccountOldSite($email)
    {

        $url = IMPORT_BASE_URL . 'check_user.php-invalidate';
        $fields = array(
            'email' => urlencode($email)
        );

        foreach ($fields as $key => $value) {
            $fields_string .= $key . '=' . $value . '&';
        }
        rtrim($fields_string, '&');

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, count($fields));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);

        curl_close($ch);

        return json_decode($result); // yes or no

    }

    public function signIn()
    {
        $account = ORM::for_table('accounts')
            ->where('email', $this->post["email"])->order_by_desc("id")
            ->find_one();


        if ($account->email == "") {


            // check for customer service
            $agent = ORM::for_table("blumeUsers")->where("role", "customer_service")->where("email", $this->post["email"])->find_one();

            if ($agent->email == "") {

                $this->setToastDanger("Oops. Invalid email or password.");
                exit;

                // Check account on old site
                $item = $this->checkAccountOldSite($this->post["email"]);
                if (@$item->id) {

                    $data = [];
                    $data['oldID'] = $item->id;
                    $data['firstname'] = $item->first_name;
                    $data['lastname'] = $item->last_name;
                    $data['email'] = $item->email;
                    $data['password'] = $item->password;
                    $data['whenCreated'] = $item->when_created;
                    $data['whenUpdated'] = $item->when_updated;
                    $data['lastActive'] = $item->last_active;

                    $currentAccount = ORM::for_table('accounts')
                        ->where('oldId', $item->id)
                        ->find_one();

                    if (@$currentAccount->id) {
                        $data['id'] = $currentAccount->id;
                    }

                    if (@$item->profile_image) {
                        $data['wpImage']['full'] = $item->profile_image;
                    }
                    $account = $this->saveAccount($data);
                    $this->accountSignIn($account);


                } else {

                    $this->setToastDanger("Oops. Invalid email or password.");

                }

                exit;

            } else {
                $hash = $agent["password"];
                if (password_verify($this->post["password"], $hash)) {

                    $newSignedID = $agent["id"];

                    $_SESSION['adminFirstname'] = $agent["name"];

                    $_SESSION['adminLastname'] = $agent["surname"];

                    $_SESSION['id'] = $newSignedID;

                    $_SESSION['idx'] = base64_encode("g4p3h9xfn8sq03hs2234$newSignedID");

                    $encryptedID = base64_encode("g4enm2c0c4y3dn3727553$newSignedID");


                    ini_set("session.cookie_httponly", 1);

                    setcookie("idCookie", $encryptedID, time() + 60 * 60 * 24 * 100, "/");

                    setcookie("passCookie", $hash, time() + 60 * 60 * 24 * 100, "/");


                    if (SITE_TYPE != "uk") {
                        $this->redirectJS('https://newskillsacademy.co.uk/customer-service-portal');
                        exit;
                    } else {
                        $this->redirectJS(SITE_URL . 'customer-service-portal');
                    }
                    exit;

                } else {
                    $this->setToastDanger("Oops. Invalid email or password.");
                    exit;
                }
            }

        }

        $this->accountSignIn($account);


    }

    protected function verifyRecaptcha()
    {
        require_once(__DIR__ . '/../helpers/CurlRequestHelpers.php');
        $results = (new CurlRequestHelpers())->sendRequest(
            'https://www.google.com/recaptcha/api/siteverify',
            [
                'response' => $this->post['token'],
                'secret' => RECAPTCHA_SECRET_KEY
            ]
        );
        return array_merge(['type' => 'recaptcha'], $results);
    }

    protected function accountSignIn($account, $socialLogin = false)
    {
        $signInSuccess = false;

        $ipAddress = $this->getUserIP(); // used for sign in logs and checking failed sign in attempts
        $signInTime = date('Y-m-d H:i:s', time() - 3660); // to count sign in attempts within a 60 second period / we have to minus an hour as well because of server timezone weirdness

        $attempts = ORM::for_table("accountSignInLogs")
            ->where("ipAddress", $ipAddress)
            ->where_gt("dateTime", $signInTime)
            ->where("success", "0")
            ->count();

        // limit login attempts to 10 in 60 seconds
        /*if($attempts > 9) {
            $this->setToastDanger("You have had too many login attempts. Please try again in 1 minute.");
            exit;
        }*/


        // STANDARD USER LOGIN
        if (@$account['oldID'] && $account["checkedPassword"] == 0 && $account["usImport"] == '1') {

            // then we need to check the old site
            $oldSite = $this->checkCredentialsOldSite($this->post["email"], $this->post["password"]);

            if ($oldSite == "yes") {
                // login is valid, update account with new password and proceed
                //$update = ORM::for_table("accounts")->find_one($account["id"]);
                $account->password = password_hash($this->post["password"], PASSWORD_BCRYPT);

                $signInSuccess = true;

            } else {

                // check current site
                if (password_verify($this->post["password"], $account["password"])) {
                    $signInSuccess = true;
                }

            }
            $account->checkedPassword = 1;
            $account->save();

        } else if ($socialLogin == true) {
            $signInSuccess = true;
        } else {

            // check current site
            if (password_verify($this->post["password"], $account["password"])) {
                $signInSuccess = true;
            }

        }

        // sign in or not sign in
        if ($signInSuccess == true) {

            if ($account["twoFactor"] == "1") {

                if ($_COOKIE["user2fa"] != "" && $_COOKIE["user2fa"] == $account["loginCode"]) {

                } else {
                    // account has 2fa enabled, so email them the code and redirect to confirmation page where code must be entered
                    $update = ORM::for_table("accounts")->find_one($account["id"]);

                    $six_digit_random_number = mt_rand(100000, 999999);

                    $update->loginCode = $six_digit_random_number;

                    $update->save();

                    $message = '<p>Hello ' . $account["firstname"] . '</p>
        
        <p>You recently tried signing into your New Skills Academy account. To gain access, please use the 6 digit code below:</p>
       
     
        <p style="font-weight:bold;font-size:20px;">' . $six_digit_random_number . '</p>
        
        <p>The device you are signing in via will remember your authentication for up to 30 days. After that, or if you try signing in from a different device/browser, we will request you enter another code.</p>';


                    $_SESSION['userID'] = $account["id"];

                    // send email
                    $this->sendEmail($account["email"], $message, "Login attempt at " . date('H:i:s') . ": 6 Digit Code - New Skills Academy");
                    if ($socialLogin == true) {
                        $_SESSION["currentPageUrl"] = SITE_URL . 'account/confirm';
                        $loginResponse = [
                            'error' => false,
                            'message' => null,
                            'redirect' => SITE_URL . 'account/confirm'
                        ];
                        return $loginResponse;
                    }

                    $this->redirectJS(SITE_URL . 'account/confirm');
                    exit;
                }


            }

            if ($account["lockSignIn"] == "1") {
                $errorMessage = "It seems your account has been locked or closed. Contact support to find out more.";
                if ($socialLogin == true) {
                    $loginResponse = [
                        'error' => true,
                        'message' => $errorMessage
                    ];
                    return $loginResponse;
                }
                $this->setToastDanger($errorMessage);
                exit;
            }

            $newSignedID = $account["id"];
            $_SESSION['id_front'] = $newSignedID;

            $_SESSION['idx_front']
                = base64_encode("g4p3h9xfn8sq03hs2234$newSignedID");

            //Added by Zubaer
            $_SESSION['nsa_email_front'] = $account["email"];

            $_SESSION['csrftoken']
                = substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 40);

            // set currency cookie based on account
            setcookie("currency", $account["currencyID"], time() + (86400 * 30) * 90, "/");

            $log = ORM::for_table("accountSignInLogs")->create();

            $log->accountID = $newSignedID;
            $log->ipAddress = $ipAddress;
            $log->set_expr("dateTime", "NOW()");

            $log->save();


            ?>
            <script>
                window.dataLayer = window.dataLayer || [];

                window.dataLayer.push({
                    'event': 'login',
                    'user_id': '<?php echo $newSignedID; ?>',
                    'user_email': '<?php echo $account["email"]; ?>'
                });

                gtag('event', 'log in', {
                    'event_label': 'success',
                    'event_category': 'registration',
                    'non_interaction': true
                });
            </script>
            <?php

            // reach tracking code
            $reachCodes = array("370", "369", "368");
            if (in_array($_SESSION["refCodeInternal"], $reachCodes)) {
                ?>
                <!-- Conversion Pixel - Conversion - New Skills Academy - Sign In - DO NOT MODIFY -->
                <script src="https://secure.adnxs.com/px?id=1490336&seg=26796659&t=1" type="text/javascript"></script>
                <!-- End of Conversion Pixel -->
                <?php
            }

            if ($this->post["return"] != "") {
                $this->redirectJS(SITE_URL . $this->post["return"]);
                exit;
            } else {
                if ($socialLogin == true) {
                    $loginResponse = [
                        'error' => false,
                        'message' => null
                    ];
                    return $loginResponse;
                }
                $this->redirectJS(SITE_URL . 'dashboard');
            }


        } else {

            // record failed sign in attempt and allow for blank account ID
            $accountID = $account["id"];

            if ($accountID == "") {
                $accountID = "";
            }

            $log = ORM::for_table("accountSignInLogs")->create();

            $log->accountID = $accountID;
            $log->ipAddress = $ipAddress;
            $log->set_expr("dateTime", "NOW()");
            $log->success = "0";

            $log->save();
            $errorMessage = "Your username or password was incorrect. Please try again.";
            if ($socialLogin == true) {
                $loginResponse = [
                    'error' => true,
                    'message' => $errorMessage
                ];
                return $loginResponse;
            } else {
                $this->setToastDanger($errorMessage);
            }

        }
    }

    public function confirmSignIn()
    {

        if ($this->post["code"] != "" && $_SESSION['userID'] != "") {

            $ipAddress = $this->getUserIP();

            $account = ORM::for_table("accounts")->find_one($_SESSION['userID']);

            if ($this->post["code"] == $account["loginCode"]) {
                $newSignedID = $account["id"];
                $_SESSION['id_front'] = $newSignedID;

                $_SESSION['idx_front']
                    = base64_encode("g4p3h9xfn8sq03hs2234$newSignedID");

                //Added by Zubaer
                $_SESSION['nsa_email_front'] = $account["email"];

                $_SESSION['csrftoken']
                    = substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 40);

                // set 2fa cookie, which expires in 30 days
                setcookie("user2fa", $account["loginCode"], time() + 60 * 60 * 24 * 30, "/");

                $log = ORM::for_table("accountSignInLogs")->create();

                $log->accountID = $newSignedID;
                $log->ipAddress = $ipAddress;
                $log->set_expr("dateTime", "NOW()");

                $log->save();

                $this->redirectJS(SITE_URL . 'dashboard');


            } else {
                $this->setToastDanger("That code is incorrect. Please make sure you are using the latest code we have sent to you.");
            }

            exit;

        }

    }

    public function signInLog($input)
    {
        $logItem = ORM::for_table("accountSignInLogs")
            ->where('accountID', $input['accountID'])
            ->where('dateTime', $input['dateTime'])
            ->find_one();
        if (empty($logItem)) {
            $logItem = ORM::for_table("accountSignInLogs")->create();
            $logItem->accountID = $input['accountID'];
            $logItem->ipAddress = $input['ipAddress'];
            $logItem->dateTime = $input['dateTime'];
            $logItem->save();
        }
    }

    private function checkSignInReward($account, $loginDays = null)
    {
        $signInRewards = ORM::forTable('rewards')
            ->where('category', 'signin')
            ->orderByAsc('rorder')
            ->find_many();
        foreach ($signInRewards as $signInReward) {
            $days[] = str_replace("signed_", "", $signInReward->short);
        }

        if (@$loginDays) {
            $signInLimit = $this->getSetting('reward_signin_limit');
            if ($loginDays > $signInLimit) {
                // only allow claim if one not claimed today
                $today = ORM::for_table("rewardsAssigned")
                    ->where("userID", $account)->where("signIn", "1")
                    ->where_like("whenAssigned", "%" . date('Y-m-d') . "%")
                    ->find_one();

                if ($today->id == "") {
                    $signInRewardPoints = $this->getSetting('reward_signin_points_after_limit');
                    // there have been none, lets assign
                    $this->rewardsAssigned->assignReward($account, "signed " . $loginDays . " days", true, true, $signInRewardPoints);
                }
            } else {
                if (in_array($loginDays, $days)) {

                    // only allow claim if one not claimed today
                    $today = ORM::for_table("rewardsAssigned")
                        ->where("userID", $account)->where("signIn", "1")
                        ->where_like("whenAssigned", "%" . date('Y-m-d') . "%")
                        ->find_one();

                    if ($today->id == "") {
                        // there have been none, lets assign
                        $this->rewardsAssigned->assignReward($account, "signed_" . $loginDays);
                    }
                }


            }

        } else {

//        $days = array(
//            2,
//            5,
//            7,
//            14,
//            21
//        ); // theres a reward for each of these consecutive days
            $limitDay = 0;
            foreach ($days as $day) {

                $reward = $this->checkRewardStatus($day, $account);
                if ($reward == true) {
                    $limitDay = $day;
                    // only allow claim if one not claimed today
                    $today = ORM::for_table("rewardsAssigned")
                        ->where("userID", $account)->where("signIn", "1")
                        ->where_like("whenAssigned", "%" . date('Y-m-d') . "%")
                        ->find_one();

                    if ($today->id == "") {
                        // there have been none, lets assign
                        $this->rewardsAssigned->assignReward($account, "signed_" . $day);
                    }
                }

                // For more than Limit
                $signInLimit = $this->getSetting('reward_signin_limit');
                if ($limitDay == $signInLimit) {
                    $signInRewardPoints = $this->getSetting('reward_signin_points_after_limit');
                    for ($i = $signInLimit + 1; $i <= 1000; $i++) {
                        $reward = $this->checkRewardStatus($i, $account);
                        if ($reward == true) {
                            // only allow claim if one not claimed today
                            $today = ORM::for_table("rewardsAssigned")
                                ->where("userID", $account)->where("signIn", "1")
                                ->where_like("whenAssigned", "%" . date('Y-m-d') . "%")
                                ->find_one();

                            if ($today->id == "") {
                                // there have been none, lets assign
                                $this->rewardsAssigned->assignReward($account, "signed " . $i . " days", true, true, $signInRewardPoints);
                            }
                        } else {
                            break;
                        }
                    }
                }
//            if($rewardCount == count($days)){
//                $signInLimit = $this->getSetting('reward_signin_limit');
//                $signInRewardPoints = $this->getSetting('reward_signin_points_after_limit');

//            }


            }
        }

    }

    protected function checkRewardStatus($day, $account)
    {
        $start = 0;
        $end = $day - 1;

        $reward = true;

        // check for consecutive sign ins
        while ($start <= $end) {
            $log = ORM::for_table("accountSignInLogs")
                ->where("accountID", $account)->where_like("dateTime",
                    "%" . date("Y-m-d", strtotime("-$start day")) . "%")
                ->find_one();
            if ($log->id == "") { // if login doesnt exist, then break the loop because theres a day in the cycle the user did not sign in
                $reward = false;
                $start = 999;
            }
            $start++;
        }

        return $reward;
    }

    public function consecutiveSignIns()
    {

        $days = 250;
        $start = 0;

        $count = 0;

        $echoFinal = true;

        while ($start <= $days) {

            $log = ORM::for_table("accountSignInLogs")
                ->where("accountID", CUR_ID_FRONT)->where_like("dateTime",
                    "%" . date("Y-m-d", strtotime("-$start day")) . "%")
                ->find_one();

            if ($log->id == "") {
                echo $start;
                // $start = 999;
                $count = 0;
                $echoFinal = false;
                break;
            } else {
                $count++;
            }

            $start++;

        }

        if ($echoFinal == true) {
            echo $count;
        }

    }

    public function register()
    {

        $fields = array("email", "password", "username");

        $this->validateValues($fields);

        // Validate password strength
        $password = $this->post["password"];

        if (strlen($password) < 6) {
            ?>
            <script type="text/javascript">
                $("input[name='password']").addClass("form-error");
            </script>
            <?php
            $this->setToastDanger('Your password should be at least 6 characters in length .');
            exit;
        }

        $_SESSION['csrftoken'] = substr(base_convert(sha1(uniqid(mt_rand())),
            16, 36), 0, 40);

        // validate username
        $existingUsername = ORM::for_table("accounts")
            ->where("username", $this->post["username"])->find_one();
        if (strlen($this->post["username"]) < 4
            || $existingUsername->username != ""
        ) {
            ?>
            <script type="text/javascript">
                $("input[name='username']").addClass("form-error");
            </script>
            <?php
            $this->setToastDanger('Your username must be at least 4 characters long and not be already in use.');
            exit;
        }

        $allowed = array("-", "_");
        if (ctype_alnum(str_replace($allowed, '', $this->post["username"]))) {

        } else {
            $this->setToastDanger('Your username must only contain letters, numbers, - and _.');
            exit;
        }


        // validate email
        $existingEmail = ORM::for_table("accounts")
            ->where("email", $this->post["email"])->find_one();
        if (!filter_var($this->post["email"], FILTER_VALIDATE_EMAIL)
            || $existingEmail->email != ""
        ) {
            ?>
            <script type="text/javascript">
                $("input[name='email']").addClass("form-error");
            </script>
            <?php
            $this->setToastDanger('Please make sure your email address is properly formatted and not in use on another account.');
            exit;
        }

        // checks are passed, lets create account
        $account = ORM::for_table("accounts")->create();

        foreach ($fields as $field) {

            if ($field == "password") {
                $account->password = password_hash($this->post["password"],
                    PASSWORD_BCRYPT);
            } else {
                $account->$field = $this->post[$field];
            }
        }

        $account->set_expr("whenCreated", "NOW()");

        $account->save();

        // sign them in
        $newSignedID = $account->id();
        $_SESSION['id_front'] = $newSignedID;

        $_SESSION['idx_front']
            = base64_encode("g4p3h9xfn8sq03hs2234$newSignedID");

        //Added by Zubaer
        $_SESSION['nsa_email_front'] = $this->post["email"];

        $encryptedID = base64_encode("g4enm2c0c4y3dn3727553$newSignedID");
        ini_set("session.cookie_httponly", 1);
        setcookie("idCookieFront", $encryptedID, time() + 60 * 60 * 24 * 100,
            "/");

    }

    public function editProfile()
    {

        $this->validateValues(array("firstname", "lastname", "email"));

        $account = ORM::for_table("accounts")->find_one(CUR_ID_FRONT);

        // if email has changed, check if it's valid and not in use on another account
        if ($account->email != $this->post["email"]) {

            $existingEmail = ORM::for_table("accounts")
                ->where("email", $this->post["email"])->find_one();

            if (!filter_var($this->post["email"], FILTER_VALIDATE_EMAIL)
                || $existingEmail->email != ""
            ) {
                $this->setToastDanger('Please make sure your email address is properly formatted and not in use on another account.');
                exit;
            }

            // send email change to helpscout
            $message = '
<p>The following user has changed their email address:</p>
        <p>
        <strong>Name:</strong> ' . $account->firstname . ' ' . $account->lastname . '<br />
        <strong>Original Email:</strong> ' . $account->email . '<br />
        <strong>New Email:</strong> ' . $this->post["email"] . '<br />
        <strong>Account ID:</strong> ' . $account->id . '<br />
</p>
        

';

            $this->sendEmail("support@newskillsacademy.co.uk", $message,
                "User changed email from " . $account->email . " to " . $this->post["email"]);

        }

        $account->set(
            array(
                'firstname' => strip_tags($this->post["firstname"]),
                'lastname' => strip_tags($this->post["lastname"]),
                'email' => strip_tags($this->post["email"])
            )
        );

        $account->set_expr("whenUpdated", "NOW()");

        $account->save();

        $this->setToastSuccess("Your profile was successfully updated.");


    }

    public function editImage()
    {

        $account = ORM::for_table("accounts")->find_one(CUR_ID_FRONT);

        if ($this->checkFileUploadSelected() == true) {
            $account->profileImg = $this->uploadImage("profileImg");
        }

        $account->save();

        $this->setToastSuccess("Your image was successfully updated.");

    }

    public function editPassword()
    {

        $account = ORM::for_table("accounts")->find_one(CUR_ID_FRONT);


        // change password if password is entered
        if ($this->post["password"] != "") {

            // check that current password is correct
            $hash = $account["password"];
            if (password_verify($this->post["passwordCurrent"], $hash)) {

                if ($this->post["password"] != $this->post["passwordConfirm"]) {
                    $this->setToastDanger('The passwords you entered do not match.');
                    exit;
                }

                $this->validatePassword($this->post["password"]);

                $account->password = password_hash($this->post["password"],
                    PASSWORD_BCRYPT);

            } else {

                $this->setToastDanger("Your current password is not correct.");
                exit;

            }


        }

        $account->save();

        $this->setToastSuccess("Your password was successfully updated.");


    }

    public function updatePreferences()
    {

        $account = ORM::for_table("accounts")->find_one(CUR_ID_FRONT);

        $account->rewardNotification = @$this->post["rewardNotification"] ? 1 : 0;

        if ($this->post["twoFactor"] == "1") {
            $account->twoFactor = "1";
        } else {
            $account->twoFactor = "0";
        }

        $account->save();

        $this->setToastSuccess("Your Preferences was successfully updated.");


    }

    public function signOut()
    {

        unset($_SESSION["adminAccessed"]);
        unset($_SESSION['idx_front']);
        //Added by Zubaer
        unset($_SESSION['nsa_email_front']);

        session_destroy();
        header('Location: ' . SITE_URL);

    }

    public function forgotPassword()
    {

        $account = ORM::for_table("accounts")
            ->where("email", $this->post["email"])->find_one();

        if ($account->email != "") {

            $token = openssl_random_pseudo_bytes(20);
            $token = bin2hex($token);

            $account->pwToken = $token;
            $account->setPassword = "1";

            $resetPasswordButton = $this->renderHtmlEmailButton("Reset Password",
                SITE_URL . '?pass=true&token=' . $token . '&id='
                . $account->id);
            $resetPasswordLink = SITE_URL . '?pass=true&token=' . $token . '&id=' . $account->id;

            $emailTemplate = $this->emailTemplates->getTemplateByTitle('forgot_password');

            if (@$emailTemplate->id) {
                $variables = [
                    '[FIRST_NAME]' => $account->firstname,
                    '[LAST_NAME]' => $account->lastname,
                    '#[RESET_LINK]' => $resetPasswordLink,
                    '[RESET_LINK]' => $resetPasswordLink,
                ];

                $message = $emailTemplate->description;
                $subject = $emailTemplate->subject;

                foreach ($variables as $k => $v) {
                    $message = str_replace($k, $v, $message);
                    $subject = str_replace($k, $v, $subject);
                }

                $this->sendEmail($this->post["email"], $message, $subject);
            }

            $account->save();

        }

        ?>
        <script>
            $("#forgot").modal("toggle");
            $("#forgotSuccess").modal("toggle");
        </script>
        <?php

    }

    public function resetPassword()
    {

        $account = ORM::for_table("accounts")
            ->where("email", $this->post["email"])
            ->where("pwToken", $this->post["pwToken"])
            ->where("id", $this->post["id"])
            ->find_one();

        if ($account->id == "") {
            $this->setToastDanger("We could not confirm your account. Please make sure you clicked the correct link and that your email is correct.");
            exit;
        }

        // Validate password strength
        $password = $this->post["password"];
        $uppercase = preg_match('@[A-Z]@', $password);
        $lowercase = preg_match('@[a-z]@', $password);
        $number = preg_match('@[0-9]@', $password);

        if (!$uppercase || !$lowercase || !$number || strlen($password) < 6) {
            $this->setToastDanger('Your password should be at least 6 characters in length and should include at least one upper case letter and one number.');
            exit;
        }

        if ($this->post["password"] != $this->post["passwordConfirm"]) {
            $this->setToastDanger("The passwords you have entered do not match.");
            exit;
        }

        $account->password = password_hash($this->post["password"], PASSWORD_BCRYPT);
        $account->pwToken = "";

        $account->save();

        $this->setToastSuccess("Your password was successfully reset, please sign back into your account.");

        ?>
        <script>
            $("#reset").modal("toggle");
            $("#signIn").modal("toggle");
        </script>
        <?php


    }

    public function rafLink()
    {

        // recommend a friend link

        $account = ORM::for_table("accounts")->find_one(CUR_ID_FRONT);

        if ($account->rafCode == "") {

            $code = str_replace(" ", "", $account->firstname);
            $code = urlencode($code);
            $code = strtoupper($code) . '75-' . rand(999, 9999);

            $account->rafCode = $code;

        }

        echo $account->rafCode;

        $account->save();

    }

    public function getMyOrders()
    {

        return ORM::for_table("orders")
            ->where("accountID", CUR_ID_FRONT)
            ->where("status", "completed")
            ->order_by_desc("id")
            ->find_many();

    }

    public function getMySubscriptions()
    {
        return ORM::for_table("subscriptions")
            ->where("accountID", CUR_ID_FRONT)
            ->where("status", 1)
            ->order_by_desc("id")
            ->find_many();
    }

    public function getOrderItems($orderID)
    {

        return ORM::for_table("orderItems")
            ->where("orderID", $orderID)
            ->find_many();

    }

    public function createAccount()
    {

        // check if account exists
        $existing = ORM::for_table("accounts")
            ->where("email", $this->post["email"])->find_one();

        if ($existing->id != "") {
            ?>
            <script type="text/javascript">
                $("input[name='email']").addClass("form-error");
            </script>
            <?php
            $this->setToastDanger("An account already exists for this email address. Please sign in before proceeding or use a different email address.");
            exit;
        }
        if ($this->post["email"] == "") {
            ?>
            <script type="text/javascript">
                $("input[name='email']").addClass("form-error");
            </script>
            <?php
            $this->setToastDanger("Please enter an email to create an account.");
            exit;
        }
        if ($this->post["firstname"] == "") {
            ?>
            <script type="text/javascript">
                $("input[name='firstname']").addClass("form-error");
            </script>
            <?php
            $this->setToastDanger("Please enter a First name to create an account.");
            exit;
        }
        if ($this->post["lastname"] == "") {
            ?>
            <script type="text/javascript">
                $("input[name='lastname']").addClass("form-error");
            </script>
            <?php
            $this->setToastDanger("Please enter a last name to create an account.");
            exit;
        }

        // Validate password strength
        $password = $this->post["password"];
        $uppercase = preg_match('@[A-Z]@', $password);
        $lowercase = preg_match('@[a-z]@', $password);
        $number = preg_match('@[0-9]@', $password);

        if (!$uppercase || !$lowercase || !$number || strlen($password) < 6) {
            ?>
            <script type="text/javascript">
                $("input[name='password']").addClass("form-error");
            </script>
            <?php
            $this->setToastDanger('Your password should be at least 6 characters in length and should include at least one upper case letter and one number.');
            exit;
        }


        // actually create account
        $account = ORM::for_table("accounts")->create();

        $account->set(
            array(
                'firstname' => strip_tags($this->post["firstname"]),
                'lastname' => strip_tags($this->post["lastname"]),
                'email' => strip_tags($this->post["email"]),
                'password' => password_hash($this->post["password"],
                    PASSWORD_BCRYPT)
            )
        );


        $currency = $this->currentCurrency();

        $account->set_expr("whenCreated", "NOW()");
        $account->set_expr("whenUpdated", "NOW()");
        $account->currencyID = $currency->id;

        $account->save();

        $accountID = $account->id();

        // Assign Default Register Reward
        $this->rewardsAssigned->assignReward($accountID, 'register', false, false);


        //add a reward for "signing in" when the user creates their account
        $newSignedID = $account["id"];
        $ipAddress = $this->getUserIP();

        $log = ORM::for_table("accountSignInLogs")->create();

        $log->accountID = $newSignedID;
        $log->ipAddress = $ipAddress;
        $log->set_expr("dateTime", "NOW()");

        $log->save();

        // send email confirming new account
        $message = '<p>Hi ' . $this->post["firstname"] . ',</p>
        <p>Thank you for joining New Skills Academy. We hope you enjoy your course and get the chance to learn much more from our wide range of available courses.</p>
        
        <p>You can sign into your account at any time using your email address - ' . $this->post["email"] . ' - and the password you set; so you are able to continue with your course(s), view progress, make notes, enroll onto other courses, and much more.</p>
       ';

        $message .= $this->renderHtmlEmailButton("My Courses", SITE_URL . 'dashboard/courses');

        $this->sendEmail($this->post["email"], $message, "Welcome to New Skills Academy");

        // sign them in
        $_SESSION['id_front'] = $accountID;


        $_SESSION['idx_front']
            = base64_encode("g4p3h9xfn8sq03hs2234$accountID");

        //Added by Zubaer
        $_SESSION['nsa_email_front'] = $this->post["email"];

        $encryptedID = base64_encode("g4enm2c0c4y3dn3727553$accountID");
        ini_set("session.cookie_httponly", 1);
        setcookie("idCookieFront", $encryptedID, time() + 60 * 60 * 24 * 100,
            "/");

        // return the new account ID
        return $accountID;


    }

    public function assignCourse($course, $user, $moreColumns = null)
    {
        $item = ORM::for_table("coursesAssigned")
            ->where('accountID', $user)
            ->where('courseID', $course)
            ->find_one();

        if (empty($item)) {
            $item = ORM::for_table("coursesAssigned")->create();

            /* Update Moosend Newsletter */
            $course_data = ORM::for_table("courses")->find_one($course);
            $user_data = ORM::For_table("accounts")->where("id", $user)->find_one();

            if (!empty($course_data) && !empty($user_data)) {

                $categories = array();

                $courseCategories = ORM::for_table("courseCategoryIDs")->where("course_id", $course_data->id)->find_many();

                foreach ($courseCategories as $cat) {

                    $catData = ORM::for_table("courseCategories")->find_one($cat->category_id);

                    array_push($categories, $catData->title);

                }

                $imploded_cat_names = implode(',', $categories);

                $custom_fields = array(
                    'First course Title' => $course_data->title,
                    'Course Category' => $imploded_cat_names,
                    'user_id' => $user_data->id,
                    'First name' => $user_data->firstname,
                    'Last name' => $user_data->lastname
                );

                $this->moosend->updateNewsletterSubscriber($user_data->firstname, $user_data->email, $custom_fields);

            }


        }

        $data = [
            'courseID' => $course,
            'accountID' => $user
        ];

        if (count($moreColumns) >= 1) {
            foreach ($moreColumns as $k => $v) {
                $data[$k] = $v;
            }
        }
        $subStatus = $item->sub;
        $item->set($data);

        // Check for Qualification Courses
        if ($this->courses->isQualificationCourse($course)) {
            $item->activated = 0;
        }

        $item->set_expr("whenAssigned", "NOW()");
        
        if ($subStatus == "1"){
            $item->sub = "0";
        }
        
        $item->save();
        return $item;

    }

    public function claimGiftCreateAccount()
    {

        $item = ORM::for_table("orderItems")
            //->where("giftEmail", $this->post["email"])
            ->where("giftToken", $this->post["token"])
            ->where("giftClaimed", "0")
            ->find_one();

        if ($item->id == "") {
            $this->setToastDanger("This gift cannot be claimed.");
            exit;
        }

        // create account
        $accountID = $this->createAccount();

        $this->assignCourse($item->courseID, $accountID);

        $item->giftClaimed = "1";

        $item->save();

        $this->redirectJS(SITE_URL . 'dashboard');

    }

    public function claimGiftSignIn()
    {

        $item = ORM::for_table("orderItems")
            //->where("giftEmail", $this->post["email"])
            ->where("giftToken", $this->post["token"])
            ->where("giftClaimed", "0")
            ->find_one();

        if ($item->id == "") {
            $this->setToastDanger("This gift cannot be claimed.");
            exit;
        }

        // sign in
        $account = ORM::for_table('accounts')
            ->where('username', $this->post["email"])->order_by_desc("id")
            ->find_one();

        if ($account->email == "") {
            $account = ORM::for_table('accounts')
                ->where('email', $this->post["email"])->order_by_desc("id")
                ->find_one();
        }

        if ($account->email == "") {

            $this->setToastDanger("Oops. Invalid email or password.");
            exit;
        }


        $hash = $account["password"];
        if (password_verify($this->post["password"], $hash)) {

            if ($account["lockSignIn"] == "1") {
                $this->setToastDanger("It seems your account has been locked or closed. Contact support to find out more.");
                exit;
            }

            $newSignedID = $account["id"];
            $_SESSION['id_front'] = $newSignedID;

            $_SESSION['idx_front']
                = base64_encode("g4p3h9xfn8sq03hs2234$newSignedID");

            //Added by Zubaer
            $_SESSION['nsa_user_email'] = $account['email'];

            $_SESSION['csrftoken']
                = substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 40);

            $log = ORM::for_table("accountSignInLogs")->create();

            $log->accountID = $newSignedID;
            $log->ipAddress = $this->getUserIP();
            $log->set_expr("dateTime", "NOW()");

            $log->save();


            $this->assignCourse($item->courseID, $newSignedID);

            $item->giftClaimed = "1";

            $item->save();

            $this->redirectJS(SITE_URL . 'dashboard');


        } else {

            $this->setToastDanger("Oops. Invalid email or password.");

        }


    }

    public function getMyCertificates()
    {

        $items = ORM::For_table("coursesAssigned")
            ->where("accountID", CUR_ID_FRONT)
            ->where("completed", "1")
            ->order_by_desc("id")
            ->find_many();

        return $items;

    }

    public function joinNewsletter()
    {

        $this->validateValues(array("firstname", "email"));

        $newsletterCourse = $this->getNewsletterCourse();

        // generate random token
        $characters
            = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-_';
        $charactersLength = strlen($characters);
        $token = '';
        for ($i = 0; $i < 20; $i++) {
            $token .= $characters[rand(0, $charactersLength - 1)];
        }

        $item = ORM::for_table("orderItems")->create();

        $item->set(
            array(
                'orderID' => "1",
                'courseID' => $newsletterCourse->id,
                'price' => '0.00'
            )
        );

        $item->giftEmail = $this->post["email"];
        $item->giftToken = $token;
        $item->gift = "1";
        $item->giftSent = "1";

        $item->set_expr("whenCreated", "NOW()");

        $item->save();

        $item = ORM::for_table("orderItems")->find_one($item->id());

        // now send the gift email
        $course = ORM::for_table("courses")->find_one($item->courseID);
        $link = SITE_URL . '?claim=gift&token=' . $token . '&email='
            . urlencode($this->post["email"]);

        $message = '<p>Hi ' . $this->post["firstname"] . ',</p>
        <p>As you have signed up to our newsletter, you can now claim the <strong>'
            . $course->title . '</strong> course for FREE!</p>
        
        <p>All you need to do is click the Claim Course button below and follow the steps.</p>
       ';

        $message .= $this->renderHtmlEmailButton("Claim Course", $link);

        $this->sendEmail($this->post["email"], $message,
            "Welcome to our newsletter, here's your free course...");

        $this->moosend->addSubscriber();

        if ($this->post["freeCourseLeave"] == "true") {
            // set cookie on frontend so we do not show the modal again
            ?>
            <script>
                setCookieFreeCourse("freeCourseLeave", "shown", 30);
            </script>
            <?php
        }

        $this->redirectJS(SITE_URL . 'newsletter/thank-you');

    }

    public function hasReward($id)
    {

        $item = ORM::for_table("rewardsAssigned")->where("userID", CUR_ID_FRONT)
            ->where("rewardID", $id)->count();

        if ($item == 0) {
            return false;
        } else {
            return true;
        }

    }


    public function leaderboardItems($count, $offset)
    {

        return ORM::for_table("accounts")->order_by_desc("rewardPoints")
            ->limit($count)->offset($offset)->find_many();

    }

    public function mySpecialOffers()
    {

        return ORM::for_table("offerPages")
            ->where("showInAccounts", "1")
            ->order_by_desc("id")
            ->find_many();

    }

    public function getAccountByID($id)
    {

        return ORM::for_table("accounts")->find_one($id);

    }

    public function getAccountByOldID($id, $usImport = '0')
    {

        return ORM::for_table("accounts")
            ->where('oldID', $id)
            ->where('usImport', $usImport)
            ->find_one();

    }

    public function getAccountByEmail($email, $usImport = '0')
    {

        return ORM::for_table("accounts")
            ->where('email', $email)
            ->where('usImport', $usImport)
            ->find_one();
    }

    public function saveAccount(array $input)
    {

        if (isset($input['id'])) {  //For Update
            $item = ORM::for_table("accounts")->find_one($input['id']);
        } else if (isset($input['email'])) {  //For Update
            $item = ORM::for_table("accounts")->where('email', $input['email'])->find_one();
            if (empty($item)) {  //For Create
                $item = ORM::for_table("accounts")->create();
            }
        }

        $data = [];
        if (isset($input["firstname"])) { //For Importing Data
            $data['firstname'] = $input["firstname"];
        }
        if (isset($input["lastname"])) {
            $data['lastname'] = $input["lastname"];
        }
        if (isset($input["email"])) {
            $data['email'] = $input["email"];
        }
        if (isset($input["password"])) {
            $data['password'] = $input["password"];
        }

        if (isset($input["oldID"])) { //For Importing Data
            $data['oldID'] = $input["oldID"];
        }
        if (isset($input["whenCreated"])) {
            $data['whenCreated'] = $input["whenCreated"];
        }
        if (isset($input["whenUpdated"])) {
            $data['whenUpdated'] = $input["whenUpdated"];
        }
        if (isset($input["usImport"])) {
            $data['usImport'] = $input["usImport"];
        }
        if (isset($input["forcePasswordReset"])) {
            $data['forcePasswordReset'] = $input["forcePasswordReset"];
        }
        if (isset($input["lastActive"])) {
            $data['lastActive'] = $input["lastActive"];
        }
        if (isset($input["fbID"])) {
            $data['fbID'] = $input["fbID"];
        }

        $item->set($data);
        $item->save();
        // Assign Default Register Reward
        $this->rewardsAssigned->assignReward($item->id, 'register', false, false);

        if (@$input['wpImage'] && ($this->medias->hasMedia(accountController::class, $item->id) === false)) {
            $this->medias->saveWPImage(
                $input['wpImage'],
                array('type' => accountController::class, 'id' => $item->id)
            );
        }

        // redirect to edit course so modules, etc can be added
        return $item;
    }

    public function saveCourseNote(array $input)
    {
        if (isset($input['id'])) {  //For Update
            $item = ORM::for_table("courseNotes")->find_one($input['id']);
        } else { //For Create
            $item = ORM::for_table("courseNotes")->create();
        }

        $data = $input;

        $item->set($data);
        $item->save();

        return $item;

    }

    public function getIdsByOlds(array $oldIDs, $usImport = '0')
    {
        return ORM::for_table('accounts')
            ->select('id')
            ->where_in('oldID', $oldIDs)
            ->where_in('usImport', $usImport)
            ->find_array();
    }

    public function newAccount()
    {
        if (isset($this->post['auth']) && $this->post['auth'] === 'recaptcha') {
            if (isset($this->post['token'])) {
                $verifyCaptcha = $this->verifyRecaptcha();
                if (!isset($verifyCaptcha['success']) || !$verifyCaptcha['success']) {
                    $this->setToastDanger('Recaptcha error');
                    return;
                }
            } else {
                $this->setToastDanger('Recaptcha token error');
                return;
            }
        }

        $accountID = $this->createAccount();

        if ($this->post["savedCourse"] != "") {
            // add
            $item = ORM::for_table("coursesSaved")->create();

            $item->userID = $accountID;
            $item->courseID = $this->post["savedCourse"];
            $item->set_expr("whenAdded", "NOW()");

            $item->save();

            $this->redirectJS(SITE_URL . 'dashboard/courses/saved');

        } else {
            $this->redirectJS(SITE_URL . 'dashboard');
        }

    }

    public function subscribeNewsletter()
    {
        $response = '<div class="alert alert-danger">Something went wrong, Please try again!</div>';
        $emailResponse = $this->moosend->addSubscriber();
        if (empty($emailResponse->Error)) {
            if (CUR_ID_FRONT) {
                // Assign Newsletter Reward
                $this->rewardsAssigned->assignReward(CUR_ID_FRONT, 'newsletter', false);
            }

            $response = "<div class='alert alert-success'>Success! You've been added to the list.</div>";
        }
        echo $response;
        exit();
    }

    public function getCertificateCoupon($accountID)
    {
        return ORM::for_table('coupons')
            ->where('type', 'c')
            ->where('isReward', 1)
            ->where('forUser', $accountID)
            ->find_one();
    }

    public function keepAlive()
    {

        // runs regularly via ajax to keep session alive for longer
        if (isset($_SESSION['idx_front'])) {
            $_SESSION['idx_front'] = $_SESSION['idx_front'];
            $_SESSION['nsa_email_front'] = $_SESSION['nsa_email_front'];
        }

    }

    public function checkUserType($type)
    {

        // function to check if user is of a particular type so we can decide whether to render specific pages/functions for them

        $account = ORM::for_table("accounts")
            ->select("isIQA")
            ->select("isTutor")
            ->select("isNCFE")
            ->find_one(CUR_ID_FRONT);

        if ($type == "IQA") {

            if ($account->isIQA == "1") {
                return true;
            } else {
                return false;
            }

        } else if ($type == "tutor") {

            if ($account->isTutor == "1") {
                return true;
            } else {
                return false;
            }

        } else if ($type == "NCFE") {

            if ($account->isNCFE == "1") {
                return true;
            } else {
                return false;
            }

        }

    }

    public function restrictUserAccessTo($type, $type2 = "")
    {

        // redirects a user away from a page if they are not the right user type
        if ($type2 != "") {

            if ($this->checkUserType($type) == false && $this->checkUserType($type2) == false) {
                $this->force404();
                exit;
            }

        } else {

            if ($this->checkUserType($type) == false) {
                $this->force404();
                exit;
            }

        }

    }

    public function setCurrency()
    {

        $currency = ORM::for_table("currencies")->find_one($this->post["id"]);

        if ($currency->id != "") {

            setcookie("currency", $currency->id, time() + (86400 * 30) * 90, "/");

            // update order currency, if they have one
            if (ORDER_ID != "") {

                $order = ORM::for_table("orders")->find_one(ORDER_ID);

                if ($order->id != "") {

                    $order->currencyID = $currency->id;

                    $order->save();


                }


            }

            if (CUR_ID_FRONT != "") {

                $account = ORM::for_table("accounts")->find_one(CUR_ID_FRONT);
                $account->currencyID = $currency->id;
                $account->save();

            }

            if ($_SESSION["refCodeInternal"] != "") {
                // update affiliate discount
                $affVoucher = ORM::For_table("ap_affiliate_voucher")->where("aff_id", $_SESSION["refCodeInternal"])->find_one();

                if ($affVoucher != "") {

                    $_SESSION["affiliateDiscount"] = $affVoucher->voucher_value;

                    $_SESSION["affiliateDiscountType"] = $affVoucher->comission_type;

                    $affCoupon = ORM::for_table("coupons")->where("code", $affVoucher->voucher_code)->find_one();

                    // check for currency based discount
                    $currencyPricing = ORM::for_table("couponCurrencyPricing")->where("couponID", $affCoupon->id)->where("currencyID", $currency->id)->find_one();

                    if ($currencyPricing->value != "") {
                        $_SESSION["affiliateDiscount"] = $currencyPricing->value;
                    }

                    if ($affCoupon->id != "") {

                        if ($affCoupon->valueMax != "") {
                            $_SESSION["affiliateDiscountMax"] = $affCoupon->valueMax;
                        } else {
                            $_SESSION["affiliateDiscountMax"] = "";
                        }

                        if ($affCoupon->valueMin != "") {
                            $_SESSION["affiliateDiscountMin"] = $affCoupon->valueMin;
                        } else {
                            $_SESSION["affiliateDiscountMin"] = "";
                        }

                        $_SESSION["excludedCourses"] = $affCoupon->excludeCourses;

                    }


                }
            }

        }


    }

    public function addBalance($amount, $description, $accountID)
    {

        $account = ORM::for_table("accounts")->find_one($accountID);

        $existingBalance = $account->balance;

        $account->balance = $existingBalance + $amount;

        // send email to user
        $message = '
<p>Hi ' . $account->firstname . ',</p>
        <p><strong>£' . $amount . '</strong> was just added to your account balance. This can be used to purchase new courses and/or certificates directly at ' . SITE_NAME . '.</p>
        
        <p>The reason for us adding this balance is ' . $description . '.</p>
        
  
' . $this->renderHtmlEmailButton("My Account", SITE_URL);

        $this->sendEmail($account->email, $message, "£" . $amount . " was just added to your balance on " . SITE_NAME, $account->email);

        $account->save();

        // record transaction
        $transaction = ORM::for_table("accountBalanceTransactions")->create();

        $transaction->credit = $amount;
        $transaction->description = $description;
        $transaction->accountID = $accountID;
        $transaction->set_expr("dateTime", "NOW()");

        $transaction->save();

    }

    public function removeBalance($amount, $description, $accountID)
    {

        $account = ORM::for_table("accounts")->find_one($accountID);

        $existingBalance = $account->balance;

        // check user has enough balance first
        if ($amount > $existingBalance) {
            return false;
        } else {

            // send email to user
            $message = '
<p>Hi ' . $account->firstname . ',</p>
        <p><strong>£' . $amount . '</strong> was just removed from your account balance. This can be used to purchase new courses and/or certificates directly at ' . SITE_NAME . '.</p>
        
        <p>The reason for us adding this balance is ' . $description . '.</p>
        
  
' . $this->renderHtmlEmailButton("My Account", SITE_URL);

            $this->sendEmail($account->email, $message, "£" . $amount . " was just removed from your balance on " . SITE_NAME, $account->email);

            $account->balance = $existingBalance - $amount;

            $account->save();

            // record transaction
            $transaction = ORM::for_table("accountBalanceTransactions")->create();

            $transaction->debit = $amount;
            $transaction->description = $description;
            $transaction->accountID = $accountID;
            $transaction->set_expr("dateTime", "NOW()");

            $transaction->save();

            return true;
        }
    }


    public function assignJourney($journeyID, $accountID)
    {

        // assigns a new journey to a users account
        $item = ORM::for_table("journeysAssigned")->create();

        $item->accountID = $accountID;
        $item->journeyID = $journeyID;
        $item->set_expr("whenAssigned", "NOW()");

        $item->save();

        $assignedID = $item->id();

        // now add each individual course to coursesAssigned
        $journey = ORM::for_table("journeys")->find_one($journeyID);
        $courseIDs = explode(",", $journey->courses);

        foreach ($courseIDs as $courseID) {

            if ($courseID != "") {

                $item = ORM::for_table("coursesAssigned")->create();

                $item->accountID = $accountID;
                $item->assignedJourneyID = $assignedID;
                $item->courseID = $courseID;
                $item->set_expr("whenAssigned", "NOW()");

                $item->save();

            }

        }


    }

    public function downloadBadge()
    {
        $imgUrl = false;
        if (isset($this->get['img_url']) && $this->get['img_url'] !== '') {
            $imgUrl = $this->get['img_url'];
        }
        $file = fopen($imgUrl, 'r');
        if (!$file) {
            header('Location: ' . SITE_URL . 'dashboard/badges');
        }
        header("Content-Disposition: attachment; filename=\"nsa-digital-badge.png\"");
        header('Content-type: image/png');
        fpassthru($file);
    }

    /**
     * Exports accounts with non-common emails to a downloadable csv
     *
     * @return void
     */
    public function exportBusinessAccountsCsv()
    {
        require_once(__DIR__ . '/../helpers/AccountsHelpers.php');
        require_once(__DIR__ . '/../helpers/ConsoleHelpers.php');

        if (!isset($this->post['emails'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Email/s not set'
            ]);
            return;
        }
        $emails = trim($this->post['emails']);
        $toEmails = array_filter(explode(',', trim($emails)), function ($email) {
            return filter_var($email, FILTER_VALIDATE_EMAIL);
        }, ARRAY_FILTER_USE_BOTH);
        $consoleHelper = new ConsoleHelpers();
        $consoleHelper->setCommand('ExportBusinessAccounts');
        $consoleHelper->setWebFilesPath(APP_ROOT_PATH);
        $consoleHelper->setEnvFileDirPath(APP_ROOT_PATH);
        $consoleHelper->setEnvFileName('.newskills-env');
        $consoleHelper->addArgument('--emails', $toEmails);
        $runCommand = $consoleHelper->runConsoleCommand();
        if ((int)$runCommand > 0) {
            echo json_encode([
                'success' => true,
                'message' => 'Your export has been queued and will be sent to your email'
            ]);
            return;
        }
        echo json_encode([
            'success' => false,
            'message' => 'There was an error adding your export to the queue.'
        ]);
    }
}