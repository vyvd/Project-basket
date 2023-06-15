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
    $cpc = cpc_on();
    $start_date = $_GET['start_date'];
    $end_date = $_GET['end_date'];

    //$start_date = '2015-07-23';
    //$end_date = '2018-07-23';

    $start_date = date('Y-m-d h:i:s', strtotime($start_date));
    $end_date = date('Y-m-d h:i:s', strtotime($end_date));
    if(!empty($start_date) && !empty($end_date)) {
        if(strpos($start_date, '1970') === false && strpos($end_date, '1970') === false) {
            $where .= " WHERE datetime BETWEEN '$start_date' AND '$end_date'";
        }
    }
    //$sql = "SELECT SUM(cpc_earnings) as total_cpc, landing_page, datetime, affiliate_id, COUNT(*) as count FROM ap_referral_traffic WHERE datetime > '$start_date' AND datetime < '$end_date' GROUP BY affiliate_id ORDER BY count DESC LIMIT 0, 5";
   /* $sql = "SELECT Sum(cpc_earnings) as total_cpc, 
                   landing_page, 
                   datetime, 
                   affiliate_id, 
                   Count(*) AS count,
                   fullname,
                   email 
            FROM   ap_referral_traffic AS art 
                   INNER JOIN ap_members AS am 
                           ON art.affiliate_id = am.id 
            {$where}
            GROUP  BY art.affiliate_id 
            ORDER  BY count DESC 
            LIMIT  0, 5";*/
    $sql = "SELECT Sum(cpc_earnings) as total_cpc, 
                   landing_page, 
                   affiliate_id, 
                   Count(id) AS count
            FROM   ap_referral_traffic
            {$where}
            GROUP BY affiliate_id 
            ORDER BY count DESC 
            LIMIT  0, 5";
    if(!$cpc) {
        $sql = "SELECT 
               landing_page, 
               affiliate_id, 
               Count(id) AS count
        FROM ap_referral_traffic
        {$where}
        GROUP BY affiliate_id 
        LIMIT  0, 5";
    }
    //echo $sql;
    $query = mysqli_query($mysqli, $sql);
    $data = [];
    foreach ($query as $row) {
        $affiliate_id = $row['affiliate_id'];
        $row['affiliate_name'] = avatar($affiliate_id, true).'<a href="affiliate-stats?a='.$affiliate_id.'">'.profile_name($affiliate_id, true).'</a>';
        if($cpc){
            $row['total_cpc'] = $money_format->formatCurrency($row['total_cpc'], $currency_symbol);
        } else { 
            unset($row['total_cpc']);
        }
        //unset($row['count']);
        unset($row['datetime']);
        unset($row['email']);
        unset($row['affiliate_id']);
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
?>