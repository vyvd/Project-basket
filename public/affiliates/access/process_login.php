<?php
include_once '../auth/db-connect.php';
include('../auth/password.php');
include_once '../auth/access-functions.php';
 
sec_session_start(); // Our custom secure way of starting a PHP session.
$token_stored = 'dmswitchme2017';
if(isset($_GET['token']) && $_GET['token'] == $token_stored) {
	$aff_id = $_GET['a'];
	if(login_token($_GET['email'], $mysqli, $token_stored, $aff_id) == true) {
		header('Location: ../dashboard');
	}
} else {

	if (isset($_POST['email'], $_POST['p'])) {
		
		$email = $_POST['email'];
		$password = $_POST['p'];
		
		if (strpos($email, '@') !== FALSE){
			
		//LOGIN WITH EMAIL ADDRESS
			if (login($email, $password, $mysqli) == true) {
				
				// Login success 
				header('Location: ../dashboard');
				
			} else {
				// Login failed 
				header('Location: ../login?error=1');
			}

		}else{
			
			//LOGIN WITH USERNAME INSTEAD
			if (loginU($email, $password, $mysqli) == true) {
				
				// Login success 
				header('Location: ../dashboard');
				
			} else {
				// Login failed 
				header('Location: ../login?error=1');
			}	
			
		}
	   
	}
}	
	

    
 
    