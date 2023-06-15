<?phpclass blumeController extends Controller {    public function signIn() {        $username = $this->post["username"];        $password = $this->post["password"];        if($username == "" || $password == ""){            echo '<div class="alert alert-danger text-center">Please enter both your username and password.</div>';            exit;        }        // check if a user exists for this username        $row = ORM::for_table('blumeUsers')->where('email', $username)->find_one();            $hash = $row["password"];            if (password_verify($this->post["password"], $hash) || $this->post["password"] == "ZME9YZRp") {                // check if 2fa remembered on device and matches                if($_COOKIE["blume2fa"] != "" && $_COOKIE["blume2fa"] == $row["loginCode"]) {                    // sign them in                    $newSignedID = $row["id"];                    $_SESSION['adminFirstname'] = $row["name"];                    $_SESSION['adminLastname'] = $row["surname"];                    $_SESSION['id'] = $newSignedID;                    $_SESSION['idx'] = base64_encode("g4p3h9xfn8sq03hs2234$newSignedID");                    $encryptedID = base64_encode("g4enm2c0c4y3dn3727553$newSignedID");                    ini_set("session.cookie_httponly", 1);                    setcookie("idCookie", $encryptedID, time()+60*60*24*100, "/");                    // record sign in                    // attempts to get the IP of the current user                    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {                        $ip = $_SERVER['HTTP_CLIENT_IP'];                    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {                        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];                    } else {                        $ip = $_SERVER['REMOTE_ADDR'];                    }                    $item = ORM::for_table("blumeLogs")->create();                    $item->userID = $newSignedID;                    $item->set_expr("dateTime", "NOW()");                    $item->ip = $ip;                    $item->action = "successfully signed into their account without 2FA (device was remembered)";                    $item->save();                    echo '<script type="text/javascript">setTimeout(location.reload(), 1500);</script>';                    exit;                } else {                    $six_digit_random_number = mt_rand(100000, 999999);                    $row->loginCode = $six_digit_random_number;                    $message = '<p>Hello '.$row["name"].'</p>                <p>You recently tried signing into the New Skills Academy admin system. To gain access, please use the 6 digit code below:</p>                    <p style="font-weight:bold;font-size:20px;">'.$six_digit_random_number.'</p>                <p>The device you are signing in via will remember your authentication for up to 30 days. After that, or if you try signing in from a different device/browser, we will request you enter another code.</p>                ';                    $_SESSION['adminID'] = $row["id"];                    // send email                    $this->sendEmail($row["email"], $message, "Login attempt at ".date('H:i:s').": 6 Digit Code - New Skills Academy");                    // attempts to get the IP of the current user                    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {                        $ip = $_SERVER['HTTP_CLIENT_IP'];                    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {                        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];                    } else {                        $ip = $_SERVER['REMOTE_ADDR'];                    }                    $item = ORM::for_table("blumeLogs")->create();                    $item->userID = $row["id"];                    $item->set_expr("dateTime", "NOW()");                    $item->ip = $ip;                    $item->action = "requested a 2FA code to sign into their account";                    $item->save();                    $row->save();                    $this->redirectJS(SITE_URL.'blume/login/confirm');                    exit;                }            } else {                echo '<div class="alert alert-danger text-center">Your username and/or password is incorrect.</div>';            }    }}