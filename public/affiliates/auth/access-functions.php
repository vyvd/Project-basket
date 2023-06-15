<?php
include_once 'config.inc.php';
 if($_SERVER['REMOTE_ADDR'] == '127.0.0.1') {
    ini_set('display_errors', true);
    ini_set('display_startup_errors', true);
    error_reporting(E_ALL);
}
function sec_session_start() {
    $session_name = 'sec_session_id';   // Set a custom session name
    $secure = SECURE;
    // This stops JavaScript being able to access the session id.
    $httponly = true;
    // Forces sessions to only use cookies.
    if (ini_set('session.use_only_cookies', 1) === FALSE) {
        header("Location: ../error?err=Could not initiate a safe session (ini_set)");
        exit();
    }
    // Gets current cookies params.
    $cookieParams = session_get_cookie_params();
    session_set_cookie_params($cookieParams["lifetime"],
        $cookieParams["path"], 
        $cookieParams["domain"], 
        $secure,
        $httponly);
    // Sets the session name to the one set above.
    session_name($session_name);
    session_start();            // Start the PHP session 
    session_regenerate_id();    // regenerated the session, delete the old one. 
}


function login($email, $password, $mysqli) {
    // Using prepared statements means that SQL injection is not possible. 
    if ($stmt = $mysqli->prepare("SELECT id, fullname, username, password, admin_user
        FROM ap_members
       WHERE email = ?
        LIMIT 1")) {
        $stmt->bind_param('s', $email);  // Bind "$email" to parameter.
        $stmt->execute();    // Execute the prepared query.
        $stmt->store_result();
 
        // get variables from result.
        $stmt->bind_result($user_id, $fullname, $username, $db_password, $admin_user);
        $stmt->fetch();
 
        // hash the password with the unique salt.
        //$password = hash('sha512', $password . $salt);
        if ($stmt->num_rows == 1) {
            // If the user exists we check if the account is locked
            // from too many login attempts 
 
            if (checkbrute($user_id, $mysqli) == true) {
                // Account is locked 
                // Send an email to user saying their account is locked
                return false;
            } else {
                // Check if the password in the database matches
                // the password the user submitted.
				
                if (password_verify($password, $db_password)) {
                    // Password is correct!
                    // Get the user-agent string of the user.
                    $user_browser = $_SERVER['HTTP_USER_AGENT'];
                    // XSS protection as we might print this value
                    $user_id = preg_replace("/[^0-9]+/", "", $user_id);
                    $_SESSION['user_id'] = $user_id;
					$_SESSION['fullname'] = $fullname;
					$_SESSION['emailaddress'] = $email;
					$_SESSION['admin_user'] = $admin_user;
                    // XSS protection as we might print this value
                    $username = preg_replace("/[^a-zA-Z0-9_\-]+/", 
                                                                "", 
                                                                $username);
                    $_SESSION['username'] = $username;
                  	$stmtB = $mysqli->prepare("UPDATE ap_members SET browser = ? WHERE id=$user_id"); 
					$stmtB->bind_param('s', $user_browser);
					$stmtB->execute();
					$stmtB->close();
                    // Login successful.
					
					
                    return true;
                } else {
                    // Password is not correct
                    // We record this attempt in the database
                    $now = time();
                    $mysqli->query("INSERT INTO login_attempts(user_id, time)
                                    VALUES ('$user_id', '$now')");
                    return false;
                }
            }
        } else {
            // No user exists.
            return false;
        }
    }
}

function login_token($email,$mysqli, $token, $id) {
    if ($stmt = $mysqli->prepare("SELECT username, fullname, email, password, admin_user 
			FROM ap_members
		   WHERE id = ?
			LIMIT 1")) {
		$stmt->bind_param('i', $id);  // Bind "$email" to parameter.
		$stmt->execute();    // Execute the prepared query.
		$stmt->store_result();
 
		// get variables from result.
		$stmt->bind_result($username, $fullname, $email, $db_password, $admin_user);
		$stmt->fetch();
		// XSS protection as we might print this value
		$user_id = preg_replace("/[^0-9]+/", "", $id);
		$_SESSION['user_id'] = $user_id;
		$_SESSION['fullname'] = $fullname;
		$_SESSION['emailaddress'] = $email;
		$_SESSION['admin_user'] = $admin_user;
		// XSS protection as we might print this value
		$username = preg_replace("/[^a-zA-Z0-9_\-]+/", "", $username);
		$_SESSION['username'] = $username;
		$_SESSION['token'] = $token;
		return true;
	}
}

function loginU($username, $password, $mysqli) {
	
    // Using prepared statements means that SQL injection is not possible. 
    if ($stmt = $mysqli->prepare("SELECT id, fullname, email, password, admin_user 
        FROM ap_members
       WHERE username = ?
        LIMIT 1")) {
        $stmt->bind_param('s', $username);  // Bind "$email" to parameter.
        $stmt->execute();    // Execute the prepared query.
        $stmt->store_result();
 
        // get variables from result.
        $stmt->bind_result($user_id, $fullname, $email, $db_password, $admin_user);
        $stmt->fetch();
 		
        // hash the password with the unique salt.
        //$password = hash('sha512', $password . $salt);
        if ($stmt->num_rows == 1) {
            // If the user exists we check if the account is locked
            // from too many login attempts 
 
            if (checkbrute($user_id, $mysqli) == true) {
                // Account is locked 
                // Send an email to user saying their account is locked
                return false;
            } else {
                // Check if the password in the database matches
                // the password the user submitted.
				
                if (password_verify($password, $db_password)) {
					
                    // Password is correct!
                    // Get the user-agent string of the user.
                    $user_browser = $_SERVER['HTTP_USER_AGENT'];
                    // XSS protection as we might print this value
                    $user_id = preg_replace("/[^0-9]+/", "", $user_id);
                    $_SESSION['user_id'] = $user_id;
					$_SESSION['fullname'] = $fullname;
					$_SESSION['emailaddress'] = $email;
					$_SESSION['admin_user'] = $admin_user;
                    // XSS protection as we might print this value
                    $username = preg_replace("/[^a-zA-Z0-9_\-]+/", 
                                                                "", 
                                                                $username);
                    $_SESSION['username'] = $username;
					$stmtB = $mysqli->prepare("UPDATE ap_members SET browser = ? WHERE id=$user_id"); 
					$stmtB->bind_param('s', $user_browser);
					$stmtB->execute();
					$stmtB->close();
				
                    // Login successful.
                    return true;
                } else {
					
                    // Password is not correct
                    // We record this attempt in the database
                    $now = time();
                    $mysqli->query("INSERT INTO login_attempts(user_id, time)
                                    VALUES ('$user_id', '$now')");
                    return false;
                }
            }
        } else {
            // No user exists.
            return false;
        }
    }
}

function checkbrute($user_id, $mysqli) {
    // Get timestamp of current time 
    $now = time();
 
    // All login attempts are counted from the past 2 hours. 
    $valid_attempts = $now - (2 * 60 * 60);
 
    if ($stmt = $mysqli->prepare("SELECT time 
                             FROM login_attempts <code><pre>
                             WHERE user_id = ? 
                            AND time > '$valid_attempts'")) {
        $stmt->bind_param('i', $user_id);
 
        // Execute the prepared query. 
        $stmt->execute();
        $stmt->store_result();
 
        // If there have been more than 5 failed logins 
        if ($stmt->num_rows > 5) {
            return true;
        } else {
            return false;
        }
    }
}


function login_check($mysqli) {
    // Check if all session variables are set 
    if (isset($_SESSION['user_id'], $_SESSION['username'])) {
 
        $user_id = $_SESSION['user_id'];
        $username = $_SESSION['username'];
 
        // Get the user-agent string of the user.
        $user_browser = $_SERVER['HTTP_USER_AGENT'];

        if ($stmt = $mysqli->prepare("SELECT browser 
                                      FROM ap_members 
                                      WHERE id = ? LIMIT 1")) {
            // Bind "$user_id" to parameter. 
            $stmt->bind_param('i', $user_id);
            $stmt->execute();   // Execute the prepared query.
            $stmt->store_result();
			
 
            if ($stmt->num_rows == 1) {
                // If the user exists get variables from result.
                $stmt->bind_result($browser);
       			$stmt->fetch();
				

                if ($browser == $user_browser) {
                    
                    return true;
                } else {
                    // Not logged in 
					$warning = '1';
                    return true;
                }
            } else {
                // Not logged in 
               // return false;
            }
        } else {
            // Not logged in 
            //return false;
        }
    } else {
        // Not logged in 
        return false;
    }
}


/*function esc_url($url) {
 
    if ('' == $url) {
        return $url;
    }
 
    $url = preg_replace('|[^a-z0-9-~+_.?#=!&;,/:%@$\|*\'()\\x80-\\xff]|i', '', $url);
 
    $strip = array('%0d', '%0a', '%0D', '%0A');
    $url = (string) $url;
 
    $count = 1;
    while ($count) {
        $url = str_replace($strip, '', $url, $count);
    }
 
    $url = str_replace(';//', '://', $url);
 
    $url = htmlentities($url);
 
    $url = str_replace('&amp;', '&#038;', $url);
    $url = str_replace("'", '&#039;', $url);
 
    if ($url[0] !== '/') {
        // We're only interested in relative links from $_SERVER['PHP_SELF']
        return '';
    } else {
        return $url;
    }
}*/