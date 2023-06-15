<?php
/**
 * Slightly Updated by Zubaer
 * More updates pending...
 */

include(TO_PATH . '/affiliates/auth/startup.php');
include(TO_PATH . '/affiliates/data/data-functions.php');

error_reporting(E_ALL);
ini_set('display_errors', '1');

$time_start = microtime(true);

print "Starting report generation...\n";
//exit;


//ALTER TABLE `ap_reports` DROP ` payment_amount `

define('PUBLIC_PATH', dirname(__DIR__));

//print "PUBLIC_PATH: ".PUBLIC_PATH;

require PUBLIC_PATH."/wp-load.php";

class GenerateReportsAffiliates {
    //public function __construct($wpdb) {
    public function __construct($mysqli) {
        global $wpdb;

        global $mysqli;

        $sql = "SELECT id FROM ap_members WHERE admin_user = 0";
        //$affiliates = $wpdb->get_col($sql);
        //$affiliates = [7];

        $affiliates = [];

        //$affiliates = mysqli_fetch_assoc($mysqli->query($sql));
        $affiliate_results = mysqli_fetch_all($mysqli->query($sql), MYSQLI_ASSOC);

        //var_dump($affiliate_results);

        //die();

        foreach ($affiliate_results as $key => $affiliate_result) {

            $affiliates[] = $affiliate_result['id'];

        }


        //var_dump($affiliates);


        $get_dates = [];

        foreach($affiliates as $affiliate_id) {
            $new_report = false;

            //var_dump($affiliate_id);

            //by Zubaer
            //$sql = "SELECT net_earnings FROM ap_earnings WHERE affiliate_id = {$affiliate_id} AND void = 0 and refund = 0";
            //$payment_amount = array_sum($wpdb->get_col($sql));

            $payment_amount = 0;

            try {

                $sql = "SELECT SUM(net_earnings) as payment_amount FROM ap_earnings WHERE affiliate_id = {$affiliate_id} AND void = 0 and refund = 0";
                //$result = $wpdb->get_results($sql);
                $result = mysqli_fetch_assoc($mysqli->query($sql));

                //var_dump($result);
                //die();


                //$payment_amount = floatval( $result[0]->payment_amount );
                $payment_amount = floatval( $result['payment_amount'] );
                //var_dump($payment_amount);
                //var_dump(gettype($payment_amount));

                //die();

            } catch (Exception $e) {
                $e->getMessage();
            }

            print "Affiliate ID: ".$affiliate_id." -> Total Earnings (Lifetime): ".$payment_amount."\n";

            if($payment_amount > 0) {
                $sql = "SELECT datetime FROM ap_earnings WHERE affiliate_id = {$affiliate_id} AND void = 0 and refund = 0";
                //$dates = $wpdb->get_col($sql);
                $date_results = mysqli_fetch_all($mysqli->query($sql), MYSQLI_ASSOC);

                $dates = [];

                foreach ($date_results as $key => $date_result) {

                    $dates[] = $date_result['datetime'];

                }

                //var_dump($dates);
                //die();


                foreach($dates as $date) {
                    $get_dates[] = date('Y-m', strtotime($date));
                }
                $get_dates = array_unique($get_dates);

                //var_dump($get_dates);

                foreach($get_dates as $get_date) {

                    $start_gen = date('Y-m-d', strtotime("{$get_date}-01"));
                    $last_day = date('t', strtotime("{$get_date}-01"));
                    $end_gen = date('Y-m-d', strtotime("{$get_date}-".$last_day));
                    $sql = "SELECT 1 FROM ap_reports WHERE aff_id = {$affiliate_id} AND date_gen = '{$start_gen}'";


                    //var_dump($sql);

                    //$report_exists = $wpdb->get_var($sql);
                    $report_exist_result = mysqli_fetch_row($mysqli->query($sql));

                    $report_exists = false;

                    if(!empty($report_exist_result)) {
                        $report_exists = $report_exist_result[0];

                        if($report_exists == '1') {
                            $report_exists = true;
                        }

                    }

                    //var_dump($report_exists);

                    //die();

                    if($affiliate_id == 57) {
                        print "Does report exists with date: ".$get_date." : ".$report_exists."\n";
                    }

                    if(!$report_exists) {

                        //by Zubaer
                        //$sql_nem = "SELECT net_earnings FROM ap_earnings WHERE affiliate_id = {$affiliate_id} AND datetime >= '{$start_gen}' AND datetime <= '{$end_gen}' AND void = 0 and refund = 0";
                        //$net_earnings = $wpdb->get_col($sql_nem);
                        //$payment_amount_month = array_sum($net_earnings);

                        $sql_nem = "SELECT SUM(net_earnings) as payment_amount_month FROM ap_earnings WHERE affiliate_id = {$affiliate_id} AND datetime >= '{$start_gen}' AND datetime <= '{$end_gen}' AND void = 0 and refund = 0";

                        //$result = $wpdb->get_results($sql_nem);
                        $result = mysqli_fetch_assoc($mysqli->query($sql_nem));


                        //$payment_amount_month = floatval( $result[0]->payment_amount_month );
                        $payment_amount_month = floatval( $result['payment_amount_month'] );

                        //var_dump($payment_amount_month);
                        //die();

                        if($payment_amount_month > 0) {

                            print "Earnings for ".$get_date." : ".$payment_amount_month."\n";
                            print "Create report for : ".$get_date."\n";

                            $sql = "INSERT INTO ap_reports (aff_id, payment_amount, date_gen) VALUES ('$affiliate_id', $payment_amount_month, '$start_gen')";
                            //$wpdb->query($sql);
                            $mysqli->query($sql);

                            $new_report = true;
                        }
                    }
                }
                $day = date('d');
                $day = '01';
                if($new_report && $day == '01') {
                    if($_SERVER['REMOTE_ADDR'] !== '127.0.0.1') {
                        $this->send_email($affiliate_id, $start_gen);
                    }
                }
            }

            //Zubaer - Show each iteration realtime
            while (@ob_end_flush());
            ob_implicit_flush(true);

        }
        /*$vars = get_defined_vars();
        unset($vars['wpdb']);
        unset($vars['dates']);
        var_dump($vars);*/
    }
    public function send_email($affiliate_id, $start_gen) {
        global $wpdb;

        global $mysqli;

        $sql = "SELECT email FROM ap_members WHERE id = '{$affiliate_id}'";
        //$aff_email = $wpdb->get_var($sql);

        $aff_email_result = mysqli_fetch_assoc($mysqli->query($sql));
        $aff_email = $aff_email_result['email'];

        //var_dump($aff_email);
        //die();

        //$aff_email = 'info@seoadsem.com';
        $subject = 'Invoice '.$start_gen;
        $email_heading = date('F Y');
        $message = '
				There had been a new invoice added to your account on '.site_url('affiliates/my-reports').'
			';
        //send_email_woocommerce_style($aff_email, $subject, $email_heading, $message);

        $headers = "";
        //$headers = "From: " . strip_tags($_POST['req-email']) . "\r\n";
        //$headers .= "Reply-To: ". strip_tags($_POST['req-email']) . "\r\n";
        //$headers .= "CC: susan@example.com\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

        mail($aff_email, $subject, $message, $headers);

    }
}
//new GenerateReportsAffiliates($wpdb);

global $mysqli;

new GenerateReportsAffiliates($mysqli);

$time_end = microtime(true);
$execution_time = ($time_end - $time_start);

printf('Report creation process took %.5f sec', $execution_time);