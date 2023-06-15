<?php
    include_once '../auth/startup.php';
    require '../data/data-functions.php';
    if($_SERVER['REMOTE_ADDR'] == '127.0.0.1') {
        ini_set('display_errors', true);
        ini_set('display_startup_errors', true);
        error_reporting(E_ALL);
    }
    $money_format = new \NumberFormatter($locale, \NumberFormatter::CURRENCY); 
    $currency_symbol = $money_format->getSymbol(\NumberFormatter::INTL_CURRENCY_SYMBOL); 
    numfmt_set_attribute($money_format, NumberFormatter::MAX_FRACTION_DIGITS, 6);
    $where = "";
    $start_date = $_GET['start_date'];
    $end_date = $_GET['end_date'];

    //$start_date = '2015-07-23';
    //$end_date = '2018-07-23';

    $start_date = date('Y-m-d h:i:s', strtotime($start_date));
    $end_date = date('Y-m-d h:i:s', strtotime($end_date));
    if(!empty($start_date) && !empty($end_date)) {
        if(strpos($start_date, '1970') === false && strpos($end_date, '1970') === false) {
            $where .= " AND datetime BETWEEN '$start_date' AND '$end_date'";
        }
    }
    $sql = "SELECT   landing_page, 
                     affiliate_id, 
                     Count(id) AS count 
            FROM     ap_referral_traffic 
            WHERE    datetime > '$start_date'
            AND      datetime < '$end_date'
            GROUP BY landing_page 
            ORDER BY count DESC limit 0, 5";
    //echo $sql;
    $query = mysqli_query($mysqli, $sql);
    $data = [];
    foreach ($query as $row) {
        $affiliate_id = $row['affiliate_id'];
        $row['affiliate_id'] = avatar($affiliate_id, true).'<a href="affiliate-stats?a='.$affiliate_id.'">'.profile_name($affiliate_id, true).'</a>';
        unset($row['count']);
        unset($row['fullname']);
        unset($row['email']);
        $data[] = $row;
    }
    //var_dump($data);
    $totaldata = $totalfiltered = count($data);
    $data = [
        "draw"            => intval( $_GET['draw'] ),
        "recordsTotal"    => intval( $totaldata ),
        "recordsFiltered" => intval( $totalfiltered ),
        "data"            => $data
    ];
    echo json_encode($data);
