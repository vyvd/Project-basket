<?php
/* ===========================================
	TOP NAV FUNCTION 
	========================================= */
/*
Username is either – fish4 or john.goff@trinitymirror.com

Password - fishaff?!
*/
$end_date = date('Y-m-d');
$time = strtotime($end_date);
$start_date = date("Y-m-d", strtotime("-3 month", $time));

function get_column($query, $colName) {

    $toReturn = array();

    $dbData = ORM::for_table('table')->raw_query($query, array())->find_many();

    foreach($dbData as $dbRecord) {

        array_push($toReturn, $dbRecord->$colName);

    }

    return $toReturn;

}

function get_template_directory() {

    return $_SERVER['DOCUMENT_ROOT'].'/affiliates/';

}

function site_url($route = "") {

    if($route == "") {
        return SITE_URL;
    } else {
        return SITE_URL.$route;
    }

}

function repairSerializeString($value) {

	$regex = '/s:([0-9]+):"(.*?)"/';

	return preg_replace_callback(
		$regex, function($match) {
			return "s:".mb_strlen($match[2]).":\"".$match[2]."\""; 
		},
		$value
	);
}

function convert_to_csv_download($input_array, $output_file_name, $delimiter = ','){
	$temp_memory = fopen('php://memory', 'w');
	foreach ($input_array as $line) {
		fputcsv($temp_memory, $line, $delimiter);
	}
	fseek($temp_memory, 0);
	header('Content-Type: application/csv');
	header('Content-Disposition: attachement; filename="' . $output_file_name . '";');
	fpassthru($temp_memory);
	exit;
	//require($_SERVER['DOCUMENT_ROOT'].'/affiliates/inc/vendor/autoload.php');
}

function dt_getdata($sql) {
	global $mysqli;//we use connection already opened
    $query = mysqli_query($connection, $sql) OR DIE ("Can't get Data from DB , check your SQL Query " );
    $data = array();
    foreach ($query as $row ) {
        $data[] = $row ;
    }
    return $data;
}

function get_product_bought($order_id) {
	//require __DIR__.'/../../../wp-load.php';
	//global $wpdb;

    $is_old_order = strpos($order_id, 'WC') !== false ? true : false;
	$the_order_id_data = explode('#', $order_id);
	$the_order_id = str_replace(':', '', trim($the_order_id_data[1]));
    $the_order_id = trim($the_order_id);

	try {

	    //var_dump($order_id);
	    //var_dump($is_old_order);
	    //var_dump($the_order_id);

	    //$order_id = str_replace("WC Order #: ", "", $order_id);
	    //$order_id = str_replace("NSA Order #: ", "", $order_id);

	    //$order = ORM::for_table("orders")->where("oldID", $the_order_id)->find_one();

        if($is_old_order) {
            $order = ORM::for_table("orders")->where("oldID", $the_order_id)->find_one();
        } else {
            $order = ORM::for_table("orders")->where("id", $the_order_id)->find_one();
        }

	    $orderItem = ORM::for_table("orderItems")->where("orderID", $order->id)->find_one();

        //var_dump($orderItem->courseID);

        if($is_old_order) {
            $course = ORM::for_table("courses")->where("oldID", $orderItem->courseID)->find_one();
        } else {
            $course = ORM::for_table("courses")->where("id", $orderItem->courseID)->find_one();
        }


        return '<a href="'.SITE_URL.'course/'.$course->slug.'">'.$course->title.'</a>';

       /* $order = new WC_Order( $the_order_id );
        $the_order_id = intval($the_order_id);
        $items = $order->get_items();
        if(!empty($items)) {
            foreach ($items as $key => $product ) {
                $pid = $product['product_id'];
                $resultz = $wpdb->get_results("SELECT post_id FROM ".$wpdb->postmeta." WHERE meta_key = '_course_product' AND meta_value = '$pid'");
                return '<a href="'.get_permalink($resultz[0]->post_id).'">'.$items[$key]['name'].'</a>';
            }
        }*/

    } catch (Exception $e) {

    }

	return 'N/A';
}

function get_customer_name($order_id) {
    require __DIR__.'/../../../wp-load.php';
    global $wpdb;
    $the_order_id_data = explode('#', $order_id);
    $is_old_order = strpos($order_id, 'WC') !== false ? true : false;
    $the_order_id = str_replace(':', '', trim($the_order_id_data[1]));
    $the_order_id = trim($the_order_id);

    try {


        //$order = ORM::for_table("orders")->where("oldID", $the_order_id)->find_one();

        if($is_old_order) {
            $order = ORM::for_table("orders")->where("oldID", $the_order_id)->find_one();
        } else {
            $order = ORM::for_table("orders")->where("id", $the_order_id)->find_one();
        }

        $account = ORM::for_table("accounts")->find_one($order->accountID);

        /*
        $order = new WC_Order( $the_order_id );
        $the_order_id = intval($the_order_id);

//        $items = $order->get_items();
//        if(!empty($items)) {
//            foreach ($items as $key => $product ) {
//                $pid = $product['product_id'];
//                $resultz = $wpdb->get_results("SELECT post_id FROM ".$wpdb->postmeta." WHERE meta_key = '_course_product' AND meta_value = '$pid'");
//                return '<a href="'.get_permalink($resultz[0]->post_id).'">'.$items[$key]['name'].'</a>';
//            }
//        }

        //var_dump($order->get_billing_email());
        //var_dump($order->get_billing_first_name());
        //var_dump($order->get_billing_last_name());

        $name = $order->get_billing_first_name().' '.$order->get_billing_last_name();

        */

        $name = $account->firstname.' '.$account->lastname;

        if(empty($name)) {
            $name = 'N/A';
        }

        return $name;


    } catch (Exception $e) {

        //var_dump($e);

    }

    return 'N/A';
}

function get_customer_email($order_id) {
    require __DIR__.'/../../../wp-load.php';
    global $wpdb;
    $the_order_id_data = explode('#', $order_id);
    $is_old_order = strpos($order_id, 'WC') !== false ? true : false;

    $the_order_id = str_replace(':', '', trim($the_order_id_data[1]));
    $the_order_id = trim($the_order_id);


    try {

        if($is_old_order) {
            $order = ORM::for_table("orders")->where("oldID", $the_order_id)->find_one();
        } else {
            $order = ORM::for_table("orders")->where("id", $the_order_id)->find_one();
        }


        //var_dump($order);

        $account = ORM::for_table("accounts")->find_one($order->accountID);

        /*
        $order = new WC_Order( $the_order_id );
        $the_order_id = intval($the_order_id);

//        $items = $order->get_items();
//        if(!empty($items)) {
//            foreach ($items as $key => $product ) {
//                $pid = $product['product_id'];
//                $resultz = $wpdb->get_results("SELECT post_id FROM ".$wpdb->postmeta." WHERE meta_key = '_course_product' AND meta_value = '$pid'");
//                return '<a href="'.get_permalink($resultz[0]->post_id).'">'.$items[$key]['name'].'</a>';
//            }
//        }

//        var_dump($order->get_billing_email());
//        var_dump($order->get_billing_first_name());
//        var_dump($order->get_billing_last_name());

        $email = $order->get_billing_email();

        */

        $email = $account->email;

        if(empty($email)) {
            $email = 'N/A';
        }
        return $email;


    } catch (Exception $e) {

        //var_dump($e);

    }

    return 'N/A';
}

function get_country_flag_by_ip($mysqli, $ip) {
	//include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
	$sql = "SELECT country_code FROM ip2location WHERE INET_ATON('$ip') BETWEEN ip_from and ip_to and country_code <> 'ZZ'";
	$res = $mysqli->query($sql);
	$data = mysqli_fetch_assoc($res);
	//var_dump($res);
	if(!empty($data['country_code'])) {
		$country_code = strtolower($data['country_code']);
		$img_path = 'http://'.$_SERVER['HTTP_HOST'].'/affiliates/assets/img/png/'.$country_code.'.png';
		return '<img src="'.$img_path.'">';
	}
	return false;
}

function get_country_code_by_ip($mysqli, $ip) {
	//include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
	$sql = "SELECT country_code FROM ip2location WHERE INET_ATON('$ip') BETWEEN ip_from and ip_to and country_code <> 'ZZ'";
	$res = $mysqli->query($sql);
	$data = mysqli_fetch_assoc($res);
	//var_dump($res);
	if(!empty($data['country_code'])) {
		$country_code = strtolower($data['country_code']);
		return $country_code;
	}
	return false;
}

function get_country_name($country_code) {
	//include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
	$country_code = strtoupper($country_code);
	$countries = array( 'AF' => 'Afghanistan', 'AX' => 'Åland Islands', 'AL' => 'Albania', 'DZ' => 'Algeria', 'AS' => 'American Samoa', 'AD' => 'Andorra', 'AO' => 'Angola', 'AI' => 'Anguilla', 'AQ' => 'Antarctica', 'AG' => 'Antigua And Barbuda', 'AR' => 'Argentina', 'AM' => 'Armenia', 'AW' => 'Aruba', 'AU' => 'Australia', 'AT' => 'Austria', 'AZ' => 'Azerbaijan', 'BS' => 'Bahamas', 'BH' => 'Bahrain', 'BD' => 'Bangladesh', 'BB' => 'Barbados', 'BY' => 'Belarus', 'BE' => 'Belgium', 'BZ' => 'Belize', 'BJ' => 'Benin', 'BM' => 'Bermuda', 'BT' => 'Bhutan', 'BO' => 'Bolivia, Plurinational State Of', 'BQ' => 'Bonaire, Sint Eustatius And Saba', 'BA' => 'Bosnia And Herzegovina', 'BW' => 'Botswana', 'BV' => 'Bouvet Island', 'BR' => 'Brazil', 'IO' => 'British Indian Ocean Territory', 'BN' => 'Brunei Darussalam', 'BG' => 'Bulgaria', 'BF' => 'Burkina Faso', 'BI' => 'Burundi', 'KH' => 'Cambodia', 'CM' => 'Cameroon', 'CA' => 'Canada', 'CV' => 'Cape Verde', 'KY' => 'Cayman Islands', 'CF' => 'Central African Republic', 'TD' => 'Chad', 'CL' => 'Chile', 'CN' => 'China', 'CX' => 'Christmas Island', 'CC' => 'Cocos (keeling) Islands', 'CO' => 'Colombia', 'KM' => 'Comoros', 'CG' => 'Congo', 'CD' => 'Congo, The Democratic Republic Of The', 'CK' => 'Cook Islands', 'CR' => 'Costa Rica', 'CI' => 'Côte D\'ivoire', 'HR' => 'Croatia', 'CU' => 'Cuba', 'CW' => 'Curaçao', 'CY' => 'Cyprus', 'CZ' => 'Czech Republic', 'DK' => 'Denmark', 'DJ' => 'Djibouti', 'DM' => 'Dominica', 'DO' => 'Dominican Republic', 'EC' => 'Ecuador', 'EG' => 'Egypt', 'SV' => 'El Salvador', 'GQ' => 'Equatorial Guinea', 'ER' => 'Eritrea', 'EE' => 'Estonia', 'ET' => 'Ethiopia', 'FK' => 'Falkland Islands (malvinas)', 'FO' => 'Faroe Islands', 'FJ' => 'Fiji', 'FI' => 'Finland', 'FR' => 'France', 'GF' => 'French Guiana', 'PF' => 'French Polynesia', 'TF' => 'French Southern Territories', 'GA' => 'Gabon', 'GM' => 'Gambia', 'GE' => 'Georgia', 'DE' => 'Germany', 'GH' => 'Ghana', 'GI' => 'Gibraltar', 'GR' => 'Greece', 'GL' => 'Greenland', 'GD' => 'Grenada', 'GP' => 'Guadeloupe', 'GU' => 'Guam', 'GT' => 'Guatemala', 'GG' => 'Guernsey', 'GN' => 'Guinea', 'GW' => 'Guinea-bissau', 'GY' => 'Guyana', 'HT' => 'Haiti', 'HM' => 'Heard Island And Mcdonald Islands', 'VA' => 'Holy See (vatican City State)', 'HN' => 'Honduras', 'HK' => 'Hong Kong', 'HU' => 'Hungary', 'IS' => 'Iceland', 'IN' => 'India', 'ID' => 'Indonesia', 'IR' => 'Iran, Islamic Republic Of', 'IQ' => 'Iraq', 'IE' => 'Ireland', 'IM' => 'Isle Of Man', 'IL' => 'Israel', 'IT' => 'Italy', 'JM' => 'Jamaica', 'JP' => 'Japan', 'JE' => 'Jersey', 'JO' => 'Jordan', 'KZ' => 'Kazakhstan', 'KE' => 'Kenya', 'KI' => 'Kiribati', 'KP' => 'Korea, Democratic People\'s Republic Of', 'KR' => 'Korea, Republic Of', 'KW' => 'Kuwait', 'KG' => 'Kyrgyzstan', 'LA' => 'Lao People\'s Democratic Republic', 'LV' => 'Latvia', 'LB' => 'Lebanon', 'LS' => 'Lesotho', 'LR' => 'Liberia', 'LY' => 'Libya', 'LI' => 'Liechtenstein', 'LT' => 'Lithuania', 'LU' => 'Luxembourg', 'MO' => 'Macao', 'MK' => 'Macedonia, The Former Yugoslav Republic Of', 'MG' => 'Madagascar', 'MW' => 'Malawi', 'MY' => 'Malaysia', 'MV' => 'Maldives', 'ML' => 'Mali', 'MT' => 'Malta', 'MH' => 'Marshall Islands', 'MQ' => 'Martinique', 'MR' => 'Mauritania', 'MU' => 'Mauritius', 'YT' => 'Mayotte', 'MX' => 'Mexico', 'FM' => 'Micronesia, Federated States Of', 'MD' => 'Moldova, Republic Of', 'MC' => 'Monaco', 'MN' => 'Mongolia', 'ME' => 'Montenegro', 'MS' => 'Montserrat', 'MA' => 'Morocco', 'MZ' => 'Mozambique', 'MM' => 'Myanmar', 'NA' => 'Namibia', 'NR' => 'Nauru', 'NP' => 'Nepal', 'NL' => 'Netherlands', 'NC' => 'New Caledonia', 'NZ' => 'New Zealand', 'NI' => 'Nicaragua', 'NE' => 'Niger', 'NG' => 'Nigeria', 'NU' => 'Niue', 'NF' => 'Norfolk Island', 'MP' => 'Northern Mariana Islands', 'NO' => 'Norway', 'OM' => 'Oman', 'PK' => 'Pakistan', 'PW' => 'Palau', 'PS' => 'Palestinian Territory, Occupied', 'PA' => 'Panama', 'PG' => 'Papua New Guinea', 'PY' => 'Paraguay', 'PE' => 'Peru', 'PH' => 'Philippines', 'PN' => 'Pitcairn', 'PL' => 'Poland', 'PT' => 'Portugal', 'PR' => 'Puerto Rico', 'QA' => 'Qatar', 'RE' => 'Réunion', 'RO' => 'Romania', 'RU' => 'Russian Federation', 'RW' => 'Rwanda', 'BL' => 'Saint Barthélemy', 'SH' => 'Saint Helena, Ascension And Tristan Da Cunha', 'KN' => 'Saint Kitts And Nevis', 'LC' => 'Saint Lucia', 'MF' => 'Saint Martin (french Part)', 'PM' => 'Saint Pierre And Miquelon', 'VC' => 'Saint Vincent And The Grenadines', 'WS' => 'Samoa', 'SM' => 'San Marino', 'ST' => 'Sao Tome And Principe', 'SA' => 'Saudi Arabia', 'SN' => 'Senegal', 'RS' => 'Serbia', 'SC' => 'Seychelles', 'SL' => 'Sierra Leone', 'SG' => 'Singapore', 'SX' => 'Sint Maarten (dutch Part)', 'SK' => 'Slovakia', 'SI' => 'Slovenia', 'SB' => 'Solomon Islands', 'SO' => 'Somalia', 'ZA' => 'South Africa', 'GS' => 'South Georgia And The South Sandwich Islands', 'SS' => 'South Sudan', 'ES' => 'Spain', 'LK' => 'Sri Lanka', 'SD' => 'Sudan', 'SR' => 'Suriname', 'SJ' => 'Svalbard And Jan Mayen', 'SZ' => 'Swaziland', 'SE' => 'Sweden', 'CH' => 'Switzerland', 'SY' => 'Syrian Arab Republic', 'TW' => 'Taiwan, Province Of China', 'TJ' => 'Tajikistan', 'TZ' => 'Tanzania, United Republic Of', 'TH' => 'Thailand', 'TL' => 'Timor-leste', 'TG' => 'Togo', 'TK' => 'Tokelau', 'TO' => 'Tonga', 'TT' => 'Trinidad And Tobago', 'TN' => 'Tunisia', 'TR' => 'Turkey', 'TM' => 'Turkmenistan', 'TC' => 'Turks And Caicos Islands', 'TV' => 'Tuvalu', 'UG' => 'Uganda', 'UA' => 'Ukraine', 'AE' => 'United Arab Emirates', 'GB' => 'United Kingdom', 'US' => 'United States', 'UM' => 'United States Minor Outlying Islands', 'UY' => 'Uruguay', 'UZ' => 'Uzbekistan', 'VU' => 'Vanuatu', 'VE' => 'Venezuela, Bolivarian Republic Of', 'VN' => 'Viet Nam', 'VG' => 'Virgin Islands, British', 'VI' => 'Virgin Islands, U.s.', 'WF' => 'Wallis And Futuna', 'EH' => 'Western Sahara', 'YE' => 'Yemen', 'ZM' => 'Zambia', 'ZW' => 'Zimbabwe' );
	$country = $countries[$country_code];
	return $country;
}

function calculate_affiliate_commission($affiliate_id, $date) {
	//require __DIR__.'/../../../wp-load.php';
	//global $wpdb;
	$get_date = date('Y-m', strtotime($date));
	$start_gen = date('Y-m-d', strtotime("{$get_date}-01"));
	$last_day = date('t', strtotime("{$get_date}-01"));
	$end_gen = date('Y-m-d', strtotime("{$get_date}-".$last_day));
	$sql_nem = "SELECT net_earnings FROM ap_earnings WHERE affiliate_id = '{$affiliate_id}' AND datetime >= '{$start_gen} 00:00:00' AND datetime <= '{$end_gen} 23:59:59' AND void = 0 and refund = 0";
	//var_dump($sql_nem);
	$net_earnings = get_column($sql_nem, "net_earnings");
	$payment_amount_month = array_sum($net_earnings);
	return $payment_amount_month;
}

function get_user_email_for_bought_product($order_id, $require = true) {
	if($require) {
		//require __DIR__.'/../../../wp-load.php';
	}
	//global $wpdb, $post;

    $the_order_id_data = explode('#', $order_id);
    $is_old_order = strpos($order_id, 'WC') !== false ? true : false;

    $the_order_id = str_replace(':', '', trim($the_order_id_data[1]));
    $the_order_id = trim($the_order_id);

    //$order_id = str_replace("WC Order #: ", "", $order_id);
    //$order_id = str_replace("NSA Order #: ", "", $order_id);

    if($is_old_order) {
        $order = ORM::for_table("orders")->where("oldID", $the_order_id)->find_one();
    } else {
        $order = ORM::for_table("orders")->where("id", $the_order_id)->find_one();
    }


    $account = ORM::for_table("accounts")->find_one($order->accountID);

	$user_email = 'N/A';
	$the_order_id_data = explode('#', $order_id);
	$the_order_id = str_replace(':', '', trim($the_order_id_data[1]));
	$the_order_id = intval($the_order_id);
	//$user_id = get_post_meta($the_order_id, '_customer_user', true);
	//$user_data = get_user_by('ID', intval($user_id));
	//if(!empty($user_data)) {
	//	$user_email = $user_data->data->user_email;
	//}
	return $account->email;
}

function get_affiliate_type_comission($aff_id, $outside_wordpress = true, $return_both = false) {
    // get_data("SELECT comission FROM ap_members WHERE id = '$aff_id'");
    return ORM::for_table('ap_members')->whereIdIs($aff_id)->findOne();
}

function checked_status_banner_iframe($value, $columns) {
	if($value == $columns) {
		echo 'checked="checked"';
	}
}


function get_categories_courses($terms = []) {
	if(empty($terms)) {
		$terms = get_terms(array(
			'taxonomy' => 'course_category',
			'hide_empty' => true,
		));
	}
	$cats = [];
	$except = [
		'all',
		'international',
		'mega-course',
		'short-courses',
		'mini-courses'
	];
	foreach($terms as $term) {
		//var_dump($term);
		if(!in_array($term->slug, $except)) {
			$cats[] = $term;
		}
	}
	return $cats;
}

function get_courses_by_category($category) {
	remove_all_actions('pre_get_posts');
	$courses = new Wp_Query(array(
		'post_type' => 'course',
		'posts_per_page' => -1,
		'tax_query' => array(
			array(
				'taxonomy' => 'course_category',
				'field' => 'term_id',
		  'terms' => $category, // Where term_id of Term 1 is "1".
		)
		)
	));
	//var_dump($courses->posts);
	return $courses->posts;
}

function get_categories_by_courses($courses) {
	foreach($courses as $course) {
		$terms[] = get_the_terms($course, 'course_category');
	}
	//var_dump($terms);
	$cats = [];
	$except = [
		'all',
		'international',
		'mega-course',
		'short-courses',
		'mini-courses'
	];
	var_dump($terms);
	foreach($terms as $termz) {
		foreach($termz as $term) {
			if(!in_array($term->slug, $except)) {
				$cats[] = $term;
			}
		}
	}
	$cats = array_unique(array_column($cats, 'term_id'));
	return $cats;
}

function queryCoursesAjaxfuncAffiliates($checked_checkboxes) {
	global $wpdb, $affiliate_filter;
	remove_action('pre_get_posts', array('ThemexCourse', 'filterLessonColumns'));
	remove_action('pre_get_posts', array('ThemexCourse', 'filterCourses'));
	remove_action('pre_get_posts', 'hide_member_content');
	remove_action('pre_get_posts', 'changeCourseCountry');
	add_filter( 'posts_groupby', function( $groupby ) {
		return '';
	});
	if(isset($_POST['update_sql']) && $_POST['update_sql'] == 0) {
		$sql = "SELECT aff_id FROM ap_iframe_generator";
		$affiliates = $wpdb->get_col($sql);
		if(in_array($affiliate_filter, $affiliates)) {
			$sql = "SELECT courses FROM ap_iframe_generator WHERE aff_id = '{$affiliate_filter}'";
			$checked_checkboxes = json_decode($wpdb->get_var($sql));
			$course_ids = [];
			foreach($checked_checkboxes as $chk_checkbox) {
				if(strpos($chk_checkbox, '_') !== false) {
					$data = explode('_', $chk_checkbox);
					$course_ids[] = $data[0];
				}
			}
			if(!empty($course_ids)) {
				$checked_checkboxes = array_unique($course_ids);
			}
		}
	}
	//var_dump($checked_checkboxes);
	$args=array(
		'post_type' => 'course',
		'posts_per_page' => ThemexCore::getOption('courses_per_page', '12'),
		'paged' => themex_paged(),
		'meta_query' => array(
			array(
				'key' => '_thumbnail_id',
			),
		),
		'post__in' => $checked_checkboxes,
		'order' => 'none',
		'orderby' => 'none',
	);
	if (!empty($checked_checkboxes)) {
		$args['posts_per_page'] = 200;
	}
	define('CHECKED_CHECKBOXES', $checked_checkboxes);
	add_filter( 'posts_orderby', function( $groupby ) {
		global $wpdb;
		return ' FIELD('.$wpdb->posts.'.ID, '.implode(', ', CHECKED_CHECKBOXES).') ';
	});
	query_posts($args);
	//var_Dump($GLOBALS['wp_query']->request);
}

function get_location_by_product($order_id) {

    $the_order_id_data = explode('#', $order_id);
    $is_old_order = strpos($order_id, 'WC') !== false ? true : false;

    $the_order_id = str_replace(':', '', trim($the_order_id_data[1]));
    $the_order_id = trim($the_order_id);

    //$order_id = str_replace("WC Order #: ", "", $order_id);
    //$order_id = str_replace("NSA Order #: ", "", $order_id);
    //$order = ORM::for_table("orders")->where("oldID", $order_id)->find_one();

    if($is_old_order) {
        $order = ORM::for_table("orders")->where("oldID", $the_order_id)->find_one();
    } else {
        $order = ORM::for_table("orders")->where("id", $the_order_id)->find_one();
    }


    if($order->customerIP != "") {

        $ip = $order->customerIP;

        $result = ORM::for_table('table')->raw_query("SELECT city_name FROM ip_city_country_location_gb WHERE INET_ATON('$ip') BETWEEN ip_from and ip_to", array())->find_many();

        if ($result) {
            return 'GB - '.$result[0]->city_name;
        } else {
            //echo $sql;
            $result = ORM::for_table('table')->raw_query("SELECT country_code FROM ip2location WHERE INET_ATON('$ip') BETWEEN ip_from and ip_to", array())->find_many();
            if ($result) {
                return $result[0]->country_code;
            }
        }

    } else {
        return 'N/A';
    }

/*
	require __DIR__.'/../../../wp-load.php';
	global $wpdb;
	$the_order_id_data = explode('#', $order_id);
	$the_order_id = str_replace(':', '', trim($the_order_id_data[1]));
	$ip = get_post_meta($the_order_id, '_customer_ip_address', true);
	if ($ip) {
		$sql = "SELECT city_name FROM ip_city_country_location_gb WHERE INET_ATON('$ip') BETWEEN ip_from and ip_to";
		$result = $wpdb->get_results($sql);
		if ($result) {
			return 'GB - '.$result[0]->city_name;
		} else {
			$sql = "SELECT country_code FROM ip2location WHERE INET_ATON('$ip') BETWEEN ip_from and ip_to";
			//echo $sql;
			$result = $wpdb->get_results($sql);
			if ($result) {
				return $result[0]->country_code;
			}
		}
	} else {
		return 'N/A';
	}
*/
}

function get_all_affiliates() {
	include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
	ini_set('display_errors', true);
	ini_set('display_startup_errors', true);
	error_reporting(E_ALL);
	$query = "SELECT * FROM ap_members WHERE admin_user!=1 ORDER BY id DESC";
	$query = $mysqli->real_escape_string($query);
	$affiliates_ids = [];
	if($result = $mysqli->query($query)){
		$num_results = mysqli_num_rows($result);
		while($row = $result->fetch_array()) {
			$affiliates_ids[$row['email']] = $row['id'];
		}
	}
	return $affiliates_ids;
}

function admin_control($admin_user, $lang_u, $lang_a){
	if($admin_user=='1'){
		echo '<li>
		<a href="user-management"><i class="icon-default fa fa-fw fa-user"></i> '.$lang_u.'</a>
		</li>
		<li>
		<a href="settings"><i class="icon-default fa fa-fw fa-cog"></i> '.$lang_a.'</a>
		</li>';}
	}

	function avatar($userid, $return = false){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		$get_email = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT email FROM ap_members WHERE id=$userid"));
		$email = $get_email['email'];
	//GRAVATAR IMAGE URL
		$size = 40;
		$grav_url = "http://www.gravatar.com/avatar/" . md5( strtolower( trim( $email ) ) ) . "?d=" . urlencode( $default ) . "&s=" . $size;
		if($return) {
			return '<img src="'.$grav_url.'">';
		} else {
			echo '<img src="'.$grav_url.'">';
		}
	}

	function balance($owner){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		$balance = calculate_affiliate_commission($owner, date('Y-m-d'));
		$money_format = new \NumberFormatter($locale, \NumberFormatter::CURRENCY); 
		$currency_symbol = $money_format->getSymbol(\NumberFormatter::INTL_CURRENCY_SYMBOL); 
		echo $money_format->formatCurrency($balance,  $currency_symbol); 
	}

/* ===========================================
	PROFILE FUNCTION 
	========================================= */

	function profile_name($owner, $return = false){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		$get_profile = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT fullname FROM ap_members WHERE id=$owner"));
		$name = $get_profile['fullname'];
		if($return) {
			return $name;
		} else {
			echo $name;
		}
	}

	function profile_img($owner){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		$get_profile = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT avatar FROM ap_members WHERE id=$owner"));
		if($get_profile['avatar']==''){
		//GRAVATAR IMAGE URL
			$email = $get_profile['email'];
			$size = 180;
			$grav_url = "http://www.gravatar.com/avatar/" . md5( strtolower( trim( $email ) ) ) . "?d=" . urlencode( $default ) . "&s=" . $size;
			echo '<img src="'.$grav_url.'">';
		}
	}

	function profile_details($owner){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		include('lang/'.$_SESSION['language'].'.php');
		$get_profile = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT fullname, email, username FROM ap_members WHERE id=$owner"));
		$profile_name = $get_profile['fullname'];
		$profile_email = $get_profile['email'];
		$profile_username = $get_profile['username'];
		echo '
		<form method="post" action="data/update-user">
		<input type="text" name="f" value="'.$profile_name.'">
		<input type="text" name="e" value="'.$profile_email.'">
		<input type="submit" class="btn btn-success" value="Save">
		</form>';

	}
/* ===========================================
	AFFILIATES FUNCTION 
	========================================= */
	function affiliate_name($affiliate_filter){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		$get_profile = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT fullname FROM ap_members WHERE id=$affiliate_filter"));
		$name = $get_profile['fullname'];
		echo ucwords($name);
	}

	function get_current_affiliate_commission($affiliate_filter) {
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		$comission = get_affiliate_type_comission($affiliate_filter);
		if(!$comission) {
			$get_profile = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT comission FROM ap_members WHERE id=$affiliate_filter"));
			$comission = $get_profile['comission'];
		}
		echo $comission;
	}

	function affiliates_table(){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		$query = "SELECT * FROM ap_members WHERE admin_user!=1 ORDER BY id DESC";
		$query = $mysqli->real_escape_string($query);
		if($result = $mysqli->query($query)){
			$num_results = mysqli_num_rows($result);
			while($row = $result->fetch_array())
			{
				$member = $row['id'];
				//CALC AFFILIATE SALES
				$get_affiliate = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT SUM(sale_amount) as affiliate_sales FROM ap_earnings WHERE affiliate_id='$member'"));
				$affiliate_sales = $get_affiliate['affiliate_sales'];
				if($affiliate_sales==''){$affiliate_sales = '0.00';}
				//CALC AFFILIATE REFERRALS
				$get_affiliate = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT COUNT(id) as affiliate_referrals FROM ap_referral_traffic WHERE affiliate_id='$member'"));
				$affiliate_referrals = $get_affiliate['affiliate_referrals'];
				if($affiliate_referrals==''){$affiliate_referrals = '0.00';}
				//MULTI CURRENCY
				$money_format = new \NumberFormatter($locale, \NumberFormatter::CURRENCY); 
				$currency_symbol = $money_format->getSymbol(\NumberFormatter::INTL_CURRENCY_SYMBOL); 
				$token = 'dmswitchme2017';
				echo '<tr>
				<td>?ref='.$member.'</td>
				<td><a href="affiliate-stats?a='.$row['id'].'">'.$row['fullname'].'</a></td>
				<td>'.$row['username'].'</td>
				<td>'.$row['email'].'</td>
				<td>'.$affiliate_referrals.'</td>
				<td>'.$money_format->formatCurrency($affiliate_sales, $currency_symbol).'</td>
				<td>'.$money_format->formatCurrency($row['balance'], $currency_symbol).'</td>
				<td>'; if($row['terms']=='1'){echo 'Yes';} echo '</td>
				<td>
				<form method="get" action="affiliate-stats" class="pull-left">
				<input type="hidden" name="a" value="'.$row['id'].'">
				<input type="submit" class="btn btn-sm btn-primary" value="View Stats">
				</form>
				<form method="post" action="data/delete-affiliate" class="pull-left">
				<input type="hidden" name="m" value="'.$row['id'].'">
				<input type="submit" class="delete-affiliate btn btn-sm btn-danger" value="Delete">
				</form>
				</td>
				<td><a href="access/process_login.php?a='.$member.'&email='.$get_affiliate['email'].'&token='.$token.'">Switch To</a></td>
				</tr>';
			}
		}
	}

	function top_affiliates_table(){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		$limit = '4';
		$i = '0';
		$query = "SELECT affiliate_id, COUNT(*) as count FROM ap_earnings GROUP BY affiliate_id ORDER BY count DESC;";
		$query = $mysqli->real_escape_string($query);
		if($result = $mysqli->query($query)){
			$num_results = mysqli_num_rows($result);
			while($row = $result->fetch_array())
			{
				if($i < $limit){
					$affiliate_id = $row['affiliate_id'];
					$get_affiliate = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT fullname, email FROM ap_members WHERE id=$affiliate_id"));
					$fullname = $get_affiliate['fullname'];
					$email = $get_affiliate['email'];
					//CALC AFFILIATE SALES
					$get_affiliate = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT SUM(sale_amount) as affiliate_sales, COUNT(id) as ts FROM ap_earnings WHERE affiliate_id=$affiliate_id"));
					$affiliate_sales = $get_affiliate['affiliate_sales'];
					if($affiliate_sales==''){$affiliate_sales = '0.00';}
					$sales_count = $get_affiliate['ts'];
					//CALC AFFILIATE REFERRALS
					$get_affiliate = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT COUNT(id) as affiliate_referrals, COUNT(id) as tr FROM ap_referral_traffic WHERE affiliate_id=$affiliate_id"));
					$affiliate_referrals = $get_affiliate['affiliate_referrals'];
					if($affiliate_referrals==''){$affiliate_referrals = '0.00';}
					$referral_count = $get_affiliate['tr'];
					//MULTI CURRENCY
					$money_format = new \NumberFormatter($locale, \NumberFormatter::CURRENCY); 
					$currency_symbol = $money_format->getSymbol(\NumberFormatter::INTL_CURRENCY_SYMBOL); 
					echo '<tr class="top-list">
					<td>'; avatar($affiliate_id); echo '<a href="affiliate-stats?a='.$affiliate_id.'">'.$fullname.'</a></td>
					<td>'.$affiliate_referrals.'</td>
					<td>'.$money_format->formatCurrency($affiliate_sales, $currency_symbol).'</td>
					<td>'; 
						$conv = 0;
						if($referral_count > 0) {
							$conv = $sales_count / $referral_count  * 100;
						}
						echo number_format((float)$conv, 2, '.', '').'%';
					 echo '</td>
					</tr>';
				}
				$i++;
			}	
		}
	}

	function top_affiliates_list(){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		$limit = '5';
		$i = '0';
		$query = "SELECT affiliate_id, COUNT(*) as count FROM ap_earnings GROUP BY affiliate_id ORDER BY count DESC";
		$query = $mysqli->real_escape_string($query);
		if($result = $mysqli->query($query)){
			$num_results = mysqli_num_rows($result);
			while($row = $result->fetch_array())
			{
				if($i < $limit){
					$affiliate_id = $row['affiliate_id'];
					$get_affiliate = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT fullname, email FROM ap_members WHERE id=$affiliate_id"));
					$fullname = $get_affiliate['fullname'];
					$email = $get_affiliate['email'];
					$size = 40;
					$grav_url = "http://www.gravatar.com/avatar/" . md5( strtolower( trim( $email ) ) ) . "?d=" . urlencode( $default ) . "&s=" . $size;
					echo '
					<li id="'.$affiliate_id.'" class="side-danger">
					<a href="affiliate-stats?a='.$affiliate_id.'">
					<img src="'.$grav_url.'">
					'.$fullname.'
					</a>
					</li>';
				}
				$i++;
			}	
		}
	}

/* ===========================================
	REFERRAL TRAFFIC FUNCTIONS
	========================================= */
	function referral_table($start_date, $end_date, $affiliate_filter){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		$start_date = $start_date.'000000';
		$end_date = $end_date.'235959';
		$start_date = str_replace("-", "", $start_date);
		$end_date = str_replace("-", "", $end_date);
		if(isset($affiliate_filter)){$show = ' AND affiliate_id='.$affiliate_filter.'';}
		$query = "SELECT * FROM ap_referral_traffic WHERE datetime > $start_date AND datetime < $end_date $show ORDER BY datetime DESC";
		$query = $mysqli->real_escape_string($query);
		if($result = $mysqli->query($query)){
			$num_results = mysqli_num_rows($result);
			while($row = $result->fetch_array())
			{
				$affiliate_id = $row['affiliate_id'];
				$get_affiliate = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT * FROM ap_members WHERE id=$affiliate_id"));
				$affiliate_user = $get_affiliate['fullname'];
				//CHECK IF CPC ENABLED
				$get_cpc_on = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT cpc_on FROM ap_other_commissions WHERE id=1"));
				$cpc_on = $get_cpc_on['cpc_on'];
				//MULTI CURRENCY
				$money_format = new \NumberFormatter($locale, \NumberFormatter::CURRENCY); 
				$currency_symbol = $money_format->getSymbol(\NumberFormatter::INTL_CURRENCY_SYMBOL); 
				numfmt_set_attribute($money_format, NumberFormatter::MAX_FRACTION_DIGITS, 6);
				echo '<tr>
				<td><a href="affiliate-stats?a='.$affiliate_id.'">'; if($affiliate_user!=''){echo $affiliate_user;}else{echo 'No Affiliate';} echo '</a></td>
				<td>'.$row['ip'].'</td>
				<td>'.$row['agent'].'</td>
				<td>'.$row['host_name'].'</td>
				<td>'.$row['landing_page'].'</td>';
				if($cpc_on=='1'){
					echo '<td>';
					if($row['void']=='1'){ echo '<span class="red">'.$money_format->formatCurrency('0.00', $currency_symbol).' (VOID)</span>';} else {								 
						if($row['cpc_earnings']=='0'){echo $money_format->formatCurrency('0.00', $currency_symbol);}
						else { echo $money_format->formatCurrency($row['cpc_earnings'], $currency_symbol); }
					}
					echo '</td>';}
					echo '<td>'.$row['datetime'].'</td>
					<td>
					<form method="post" action="data/void-referral" class="pull-left">
					<input type="hidden" name="m" value="'.$row['id'].'">
					<input type="hidden" name="a" value="'.$row['affiliate_id'].'">
					<input type="hidden" name="r" value="'.pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME).'">
					<input type="submit" class="btn btn-xs btn-inverse" value="Void">
					</form>
					<form method="post" action="data/delete-referral" class="pull-left">
					<input type="hidden" name="m" value="'.$row['id'].'">
					<input type="hidden" name="a" value="'.$row['affiliate_id'].'">
					<input type="hidden" name="r" value="'.pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME).'">
					<input type="submit" class="btn btn-xs btn-danger" value="Delete">
					</form>
					</td>
					</tr>';
				}
			}
		}

		function top_url_referral_table($start_date, $end_date, $affiliate_filter){
			include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
			$start_date = $start_date.'000000';
			$end_date = $end_date.'235959';
			$start_date = str_replace("-", "", $start_date);
			$end_date = str_replace("-", "", $end_date);
			if(isset($affiliate_filter)){$show = ' AND affiliate_id='.$affiliate_filter.'';}
			$query = "SELECT landing_page, datetime, affiliate_id, COUNT(*) as count FROM ap_referral_traffic WHERE datetime > $start_date AND datetime < $end_date $show GROUP BY landing_page ORDER BY count DESC LIMIT 0, 5";
			$query = $mysqli->real_escape_string($query);
			if($result = $mysqli->query($query)){
				$num_results = mysqli_num_rows($result);
				while($row = $result->fetch_array())
				{
					$affiliate_id = $row['affiliate_id'];
					$get_affiliate = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT * FROM ap_members WHERE id=$affiliate_id"));
					$affiliate_user = $get_affiliate['fullname'];
					echo '<tr>
					<td><a href="affiliate-stats?a='.$affiliate_id.'">'; if($affiliate_user!=''){echo $affiliate_user;}else{echo 'No Affiliate';} echo '</a></td>
					<td>'.$row['landing_page'].'</td>
					</tr>';
				}
			}
		}

		function top_referring_affiliates($start_date, $end_date, $affiliate_filter){
			include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
			$start_date = $start_date.'000000';
			$end_date = $end_date.'235959';
			$start_date = str_replace("-", "", $start_date);
			$end_date = str_replace("-", "", $end_date);
			if(isset($affiliate_filter)){$show = ' AND affiliate_id='.$affiliate_filter.'';}
			$query = "SELECT SUM(cpc_earnings) as total_cpc, landing_page, datetime, affiliate_id, COUNT(*) as count FROM ap_referral_traffic WHERE datetime > $start_date AND datetime < $end_date $show GROUP BY affiliate_id ORDER BY count DESC LIMIT 0, 5";
			$query = $mysqli->real_escape_string($query);
			if($result = $mysqli->query($query)){
				$num_results = mysqli_num_rows($result);
				while($row = $result->fetch_array()) {
					$affiliate_id = $row['affiliate_id'];
					$get_affiliate = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT fullname, email FROM ap_members WHERE id=$affiliate_id"));
					$affiliate_user = $get_affiliate['fullname'];
					$email = $get_affiliate['email'];
					$get_cpc_on = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT cpc_on FROM ap_other_commissions WHERE id=1"));
					$cpc_on = $get_cpc_on['cpc_on'];
					$money_format = new \NumberFormatter($locale, \NumberFormatter::CURRENCY); 
					$currency_symbol = $money_format->getSymbol(\NumberFormatter::INTL_CURRENCY_SYMBOL); 
					numfmt_set_attribute($money_format, NumberFormatter::MAX_FRACTION_DIGITS, 6);
					echo '<tr class="top-list">
					<td>'; avatar($affiliate_id); echo '<a href="affiliate-stats?a='.$affiliate_id.'">'.$affiliate_user.'</a></td>
					<td>'.$row['count'].'</td>';
					if($cpc_on=='1'){echo '<td>'; 
					if($row['total_cpc']=='0'){echo $money_format->formatCurrency($row['total_cpc'], $currency_symbol);;}
					else { echo $money_format->formatCurrency($row['total_cpc'], $currency_symbol); }
					echo '</td>';}
					echo '</tr>';
				}
			}
		}
		function the_course_bought($landing_page) {
			$endofurl = parse_url($landing_page, PHP_URL_PATH);
			$link_parts = explode('/', $endofurl);

            $course = '';

            //var_dump($link_parts);

			if(isset($link_parts[2])) {

                $course = ucwords(str_replace('-', ' ', $link_parts[2]));

            } else if(isset($link_parts[1])) {

                $course = ucwords(str_replace('-', ' ', $link_parts[1]));

            }

            $link = 'http://'.$_SERVER['HTTP_HOST'].$endofurl;
            if(strpos($_SERVER['REQUEST_URI'], 'dashboard') !== false) {
                $link = $landing_page;
            }

			return array(
				'course_name' => $course,
				'course_link' => $link
			);
		}
		function my_referral_table($start_date, $end_date, $owner){
			include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
			$start_date = $start_date.'000000';
			$end_date = $end_date.'235959';
			$start_date = str_replace("-", "", $start_date);
			$end_date = str_replace("-", "", $end_date);
			$query = "SELECT * FROM ap_referral_traffic WHERE affiliate_id=$owner AND datetime > $start_date AND datetime < $end_date ORDER BY datetime DESC";
			$query = $mysqli->real_escape_string($query);
			if($result = $mysqli->query($query)){
				$num_results = mysqli_num_rows($result);
				while($row = $result->fetch_array())
				{
					$affiliate_id = $row['affiliate_id'];
					$get_affiliate = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT * FROM ap_members WHERE id=$affiliate_id"));
					$affiliate_user = $get_affiliate['fullname'];
				//CHECK IF CPC ENABLED
					$get_cpc_on = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT cpc_on FROM ap_other_commissions WHERE id=1"));
					$cpc_on = $get_cpc_on['cpc_on'];
				//MULTI CURRENCY
					$money_format = new \NumberFormatter($locale, \NumberFormatter::CURRENCY); 
					$currency_symbol = $money_format->getSymbol(\NumberFormatter::INTL_CURRENCY_SYMBOL); 
					numfmt_set_attribute($money_format, NumberFormatter::MAX_FRACTION_DIGITS, 6); 
					echo '<tr>
					<td>'.$row['ip'].'</td>
					<td>'.$row['agent'].'</td>
					<td>'.$row['host_name'].'</td>
					<td>'.$row['landing_page'].'</td>';
					echo '<td><a href="'.the_course_bought($row['landing_page'])['course_link'].'" target="_blank">'.the_course_bought($row['landing_page'])['course_name'].'</a></td>'; 
					if($cpc_on=='1'){echo '<td>'; 
					if($row['cpc_earnings']=='0'){echo $money_format->formatCurrency('0.00', $currency_symbol);;}
					else { echo $money_format->formatCurrency($row['cpc_earnings'], $currency_symbol); }
					echo '</td>';}
					echo '<td>'.$row['datetime'].'</td>
					</tr>';
				}
			}
		}

		function recent_referrals($owner){
			include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');

			$query = "SELECT * FROM ap_referral_traffic WHERE affiliate_id=$owner ORDER BY datetime DESC LIMIT 0, 4";
			$query = $mysqli->real_escape_string($query);
			if($result = $mysqli->query($query)){
				$num_results = mysqli_num_rows($result);
				while($row = $result->fetch_array()) {
					$affiliate_id = $row['affiliate_id'];
					$get_affiliate = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT * FROM ap_members WHERE id=$affiliate_id"));
					$affiliate_user = $get_affiliate['fullname'];
			//CHECK IF CPC ENABLED
					$get_cpc_on = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT cpc_on FROM ap_other_commissions WHERE id=1"));
					$cpc_on = $get_cpc_on['cpc_on'];
			//MULTI CURRENCY
					$money_format = new \NumberFormatter($locale, \NumberFormatter::CURRENCY); 
					$currency_symbol = $money_format->getSymbol(\NumberFormatter::INTL_CURRENCY_SYMBOL); 
					numfmt_set_attribute($money_format, NumberFormatter::MAX_FRACTION_DIGITS, 6);
					echo '<tr>
					<td>'.$row['ip'].'</td>';
					echo '<td><a href="'.the_course_bought($row['landing_page'])['course_link'].'" target="_blank">'.the_course_bought($row['landing_page'])['course_name'].'</a></td>'; 
					if($cpc_on=='1'){echo '<td>'; 
					if($row['cpc_earnings']=='0'){echo $money_format->formatCurrency('0.00', $currency_symbol);}
					else { echo $money_format->formatCurrency($row['cpc_earnings'], $currency_symbol); }
					echo '</td>';}
					echo '<td>'.$row['datetime'].'</td>
					</tr>';
				}
			}
		}

		function most_popular($owner) {
			include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
			ini_set('display_errors', true);
			ini_set('display_startup_errors', true);
			error_reporting(E_ALL);
			$query = "
			SELECT `affiliate_id`,
			`ip`, 
			landing_page, 
			Count(landing_page) as lpc, 
			`cpc_earnings`,
			SUM(`cpc_earnings`) as earnings,
			`datetime`
			FROM   ap_referral_traffic 
			WHERE  affiliate_id = '$owner'
			GROUP  BY landing_page DESC 
			ORDER  BY Count(landing_page) DESC
			LIMIT  0, 7
			";
	//$query = $mysqli->real_escape_string($query);
			$result = $mysqli->query($query);
			$j = 0;
			if($result){
				$num_results = mysqli_num_rows($result);
				while($row = $result->fetch_array()) {
					$affiliate_id = $row['affiliate_id'];
					$lpc = $row['lpc'];
					$lp = $row['landing_page'];
					$earnings = $row['earnings'];
					$get_affiliate = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT * FROM ap_members WHERE id=$affiliate_id"));
					$affiliate_user = $get_affiliate['fullname'];
			//CHECK IF CPC ENABLED
					$get_cpc_on = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT cpc_on FROM ap_other_commissions WHERE id=1"));
					$cpc_on = $get_cpc_on['cpc_on'];
			//MULTI CURRENCY
					$money_format = new \NumberFormatter($locale, \NumberFormatter::CURRENCY); 
					$currency_symbol = $money_format->getSymbol(\NumberFormatter::INTL_CURRENCY_SYMBOL);
					numfmt_set_attribute($money_format, NumberFormatter::MAX_FRACTION_DIGITS, 6);
					echo '<tr id="'.$j.'" class="table-ip-'.$j.'">';
					echo '<td><a href="'.the_course_bought($lp)['course_link'].'" target="_blank">'.the_course_bought($lp)['course_name'].'</a></td>'; 
					if($cpc_on=='1'){
						echo '<td>'; 
					}
					if($earnings=='0') {
						echo $money_format->formatCurrency('0.00', $currency_symbol);
					} else { 
						echo $money_format->formatCurrency($earnings, $currency_symbol); 
					}
					if($cpc_on=='1'){
						echo '</td>';
					}
					echo '<td>'.$row['datetime'].'</td>';
					$lp = parse_url($lp, PHP_URL_PATH);
					$lp = str_replace(['/course/', '/'], '', $lp);
					echo '<td><a class="nr-hits" data-lp="'.$lp.'" data-owner="'.$owner.'" style="cursor:pointer">'.$lpc.'</a></td>';
					echo '</tr>';
					$j++;
				}
			}
		}
/* ===========================================
	SALES FUNCTIONS
	========================================= */

	function voucher_table($affiliate_filter){
/*
	CREATE TABLE ap_affiliate_voucher(
	   ID int AUTO_INCREMENT NOT NULL,
	   aff_id int NOT NULL,
	   coupon_id_wc int NOT NULL,
	   voucher_code varchar(10) NOT NULL,
	   voucher_value int(10) NOT NULL,
	   expire_date datetime NOT NULL,
	   PRIMARY KEY( ID )
	);
*/


    $current_page_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";


	include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
	$query = "SELECT * FROM ap_affiliate_voucher WHERE aff_id = {$affiliate_filter} ORDER BY expire_date";
	$query = $mysqli->real_escape_string($query);
	if($result = $mysqli->query($query)){
		$num_results = mysqli_num_rows($result);
		while($row = $result->fetch_array()) {
			$affiliate_id = $row['aff_id'];
			$money_format = new \NumberFormatter($locale, \NumberFormatter::CURRENCY); 
			$currency_symbol = $money_format->getSymbol(\NumberFormatter::INTL_CURRENCY_SYMBOL);
			$the_date = date('Y-m-d h:i:s', strtotime($row['expire_date']));
			$current_date = strtotime(date('Y-m-d h:i:s'));
			if ($current_date > strtotime($the_date)) {
				$the_date = 'Expired Coupon';
			}
			$comission_type = $row['comission_type'];
			$options_ct = [
				'fixed' => false,
				'percentage' => false,
				'fixed_price' => false,
			];
			$options_ct[$comission_type] = true;
			//var_Dump($options_ct);
			$opt_string = '';
			foreach($options_ct as $option=>$selected) {
				$option_name = str_replace('_', ' ', $option);
				if($selected) {
					$opt_string .= '<option value="'.$option.'" selected="selected">'.ucwords($option_name).'</option>';
				} else {
					$opt_string .= '<option value="'.$option.'">'.ucwords($option_name).'</option>';
				}
			}



			if($comission_type !== 'percentage' ) {
				$disabled = '';
				$voucher_value = $money_format->formatCurrency($row['voucher_value'], $currency_symbol);
				if($comission_type == 'fixed_price') {
					$disabled = 'disabled="disabled"';
					$voucher_value = 'Fixed Prices';
				}

				echo '<tr>
				<form method="post" action="data/delete-update-transaction" class="pull-left">'.
				'<td>'.$row['voucher_code'].'</td>'.
				'<td><input type="text" name="voucher_value" value="'.$voucher_value.'" maxlength="10" '.$disabled.'><span style="display:none">'.$row['voucher_value'].'</span></td>'.
				'<td><select name="comission_type">
				'.$opt_string.'
				</select></td>'.
				'<td>'.$the_date.'</td>'.
				'<td>
				<input type="hidden" name="m" value="'.$row['ID'].'">
				<input type="hidden" name="a" value="'.$row['aff_id'].'">
				<input type="hidden" name="c" value="'.$row['coupon_id_wc'].'">
				<input type="hidden" name="voucher_code" value="'.$row['voucher_code'].'">
				<input type="hidden" name="r" value="'.$current_page_link.'">
				<input type="submit" name="update" class="btn btn-sm btn-inverse" value="Update">
				<input type="submit" name="delete"" class="btn btn-sm btn-danger" value="Delete">
				</td>
				</form>
				</tr>';
			} else {
				echo '<tr>
				<form method="post" action="data/delete-update-transaction" class="pull-left">'.
				'<td>'.$row['voucher_code'].'</td>'.
				'<td><input type="text" name="voucher_value" value="'.$row['voucher_value'].'" maxlength="10"><span style="display:none">'.$row['voucher_value'].'</span></td>'.
				'<td><select name="comission_type">
				'.$opt_string.'
				</select></td>'.
				'<td>'.$the_date.'</td>'.
				'<td>
				<input type="hidden" name="m" value="'.$row['ID'].'">
				<input type="hidden" name="a" value="'.$row['aff_id'].'">
				<input type="hidden" name="c" value="'.$row['coupon_id_wc'].'">
				<input type="hidden" name="voucher_code" value="'.$row['voucher_code'].'">
				<input type="hidden" name="r" value="'.$current_page_link.'">
				<input type="submit" name="update" class="btn btn-sm btn-inverse" value="Update">
				<input type="submit" name="delete"" class="btn btn-sm btn-danger" value="Delete">
				</td>
				</form>
				</tr>';
			}
		}
	}
}   

function the_selected_vouchers_aff($affiliate_filter, $voucher_code) {
	global $mysqli;
	$query = "SELECT selected_voucher_id FROM ap_iframe_generator WHERE aff_id = {$affiliate_filter} AND selected_voucher_id = '$voucher_code'";
	if ($result = $mysqli->query($query)) {
		if ($row = $result->fetch_assoc()) {
			if($row['selected_voucher_id'] == $voucher_code) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	} else {
		return false;
	}
}

function select_vouchers_aff($affiliate_filter){
	include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
	/*ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);*/
	$query = "SELECT * FROM ap_affiliate_voucher WHERE aff_id = {$affiliate_filter} AND expire_date > NOW() ORDER BY expire_date";
	$query = $mysqli->real_escape_string($query);
	$opt_string = '';
	if($result = $mysqli->query($query)){
		$num_results = mysqli_num_rows($result);
		while($row = $result->fetch_array()) {
			$voucher_code = $row['voucher_code'];
			$voucher_value = $row['voucher_value'];
			$comission_type = $row['comission_type'];
			$selected = the_selected_vouchers_aff($affiliate_filter, $voucher_code);
			if($selected) {
				$opt_string .= '<option value="'.$voucher_code.'_'.$voucher_value.'_'.$comission_type.'" selected="selected">'.ucwords($voucher_code).'</option>';
			} else {
				$opt_string .= '<option value="'.$voucher_code.'_'.$voucher_value.'_'.$comission_type.'">'.ucwords($voucher_code).'</option>';
			}
		}
		return $opt_string;
	} else {
		return false;
	}
}   

function add_coupon_to_woocommerce_dbs($voucher_code, $affiliate_id, $aff_coupon_value, $discount_type, $date) {
	global $mysqli;

	// Type: fixed_cart, percent, fixed_product, percent_product
	if($discount_type == 'percentage') {
		$discount_type = 'p';
	} else {
		if($discount_type !== 'fixed_price') {
			$discount_type = 'v';
		} else {
			$discount_type = 'v';
			$voucher_value = 0;
		}
	}

	$date =  @$date  ? date("Y-m-d 23:59:59",strtotime($date)) : null;
	$whenAdded = date("Y-m-d H:i:s");

	$sql = "INSERT INTO coupons (code, type, value, expiry, whenAdded) VALUES ('".$voucher_code."', '".$discount_type."', '".$aff_coupon_value."', '".$date."', '".$whenAdded."')";
	$mysqli->query($sql);
	$new_coupon_id = $mysqli->insert_id;
	
	if($new_coupon_id) {
		return $new_coupon_id;
	}
	return false;

	// require __DIR__.'/../../../wp-load.php';
	// // Type: fixed_cart, percent, fixed_product, percent_product
	// if($discount_type == 'percentage') {
	// 	$discount_type = 'percent';
	// } else {
	// 	if($discount_type !== 'fixed_price') {
	// 		$discount_type = 'fixed_cart';
	// 	} else {
	// 		$discount_type = 'fixed_price';
	// 		$voucher_value = 0;
	// 	}
	// }
	// $coupon = array(
	// 	'post_title' => $voucher_code,
	// 	'post_content' => 'affiliate_type_insert',
	// 	'post_excerpt' => $affiliate_id,
	// 	'post_status' => 'publish',
	// 	'post_type'		=> 'shop_coupon',
	// 	'post_author' => 134
	// );

	// $new_coupon_id = wp_insert_post( $coupon );
	// //var_Dump($new_coupon_id);				
	// // Add meta
	// update_post_meta( $new_coupon_id, 'discount_type', $discount_type );
	// update_post_meta( $new_coupon_id, 'coupon_amount', $aff_coupon_value );
	// update_post_meta( $new_coupon_id, 'individual_use', 'yes' );
	// update_post_meta( $new_coupon_id, 'product_ids', '' );
	// update_post_meta( $new_coupon_id, 'exclude_product_ids', '' );
	// update_post_meta( $new_coupon_id, 'usage_limit', '' );
	// update_post_meta( $new_coupon_id, 'expiry_date', $date );
	// update_post_meta( $new_coupon_id, 'apply_before_tax', 'yes' );
	// update_post_meta( $new_coupon_id, 'free_shipping', 'no' );
	
	// if($new_coupon_id) {
	// 	return $new_coupon_id;
	// }
	// return false;
}

function add_coupon_to_dbs($voucher_code, $affiliate_id, $aff_coupon_value, $discount_type, $date) {
	global $mysqli;

	// Type: fixed_cart, percent, fixed_product, percent_product
	if($discount_type == 'percentage') {
		$discount_type = 'p';
	} else {
		if($discount_type !== 'fixed_price') {
			$discount_type = 'v';
		} else {
			$discount_type = 'v';
			$voucher_value = 0;
		}
	}

	$date =  @$date  ? date("Y-m-d 23:59:59",strtotime($date)) : null;
	$whenAdded = date("Y-m-d H:i:s");

	$sql = "INSERT INTO coupons (code, type, value, expiry, whenAdded) VALUES ('".$voucher_code."', '".$discount_type."', '".$aff_coupon_value."', '".$date."', '".$whenAdded."')";
	$mysqli->query($sql);
	$new_coupon_id = $mysqli->insert_id;
	
	if($new_coupon_id) {
		return $new_coupon_id;
	}
	return false;
}

function check_coupon_wc_dbs_exists($voucher_code, $affiliate_id) {
	global $mysqli;
	//$sql = "SELECT ID, post_title FROM $wpdb->posts WHERE post_title = '$voucher_code' AND post_status = 'publish' AND post_type = 'shop_coupon' ";
	$coupon_titles = [];
	$coupon_ids = [];
	$the_coupons = [];

	$query = "SELECT id, code FROM coupons WHERE code = '$voucher_code'";
	//$query = $mysqli->real_escape_string($query);
	$result = $mysqli->query($query);
	$num_results = $result->num_rows;

	if($num_results >= 1){
		while($row = $result->fetch_array()) {
			$coupon_titles[] = $row['code'];
			$coupon_ids[] = $row['id'];
		}
		$the_coupons = array_combine($coupon_ids, $coupon_titles);
		if(in_array($voucher_code, $the_coupons)) {
			$arr = array_search($voucher_code, $the_coupons);
			return $arr;
		} else {
			return false;
		}

	}
	return false;
}

function update_coupon_wc($coupon_id_wc, $voucher_value, $discount_type) {

	global $mysqli;

	if($discount_type == 'percentage') {
		$discount_type = 'p';
	} else {
		if($discount_type !== 'fixed_price') {
			$discount_type = 'v';
		} else {
			$discount_type = 'v';
			$voucher_value = 0;
		}
	}

	$sql = "UPDATE coupons SET type='".$discount_type."', value='".$voucher_value."' WHERE id='".$coupon_id_wc."'";

	if ($mysqli->query($sql) === TRUE) {
	  return true;
	} 

	return false;

	// require __DIR__.'/../../../wp-load.php';;
	// global $wpdb;
	// if($discount_type == 'percentage') {
	// 	$discount_type = 'percent';
	// } else {
	// 	if($discount_type !== 'fixed_price') {
	// 		$discount_type = 'fixed_cart';
	// 	} else {
	// 		$discount_type = 'fixed_price';
	// 		$voucher_value = 0;
	// 	}
	// }
	// $update_price = update_post_meta( $coupon_id_wc, 'coupon_amount', $voucher_value );
	// $update_dicount_type = update_post_meta( $coupon_id_wc, 'discount_type', $discount_type );
	// if (!$update_price && !$update_dicount_type) {
	// 	return false;
	// } else {
	// 	return true;
	// }
	// return false;
}

function delete_coupon_wc($coupon_id_wc) {
	global $mysqli;

	$sql = "DELETE FROM coupons WHERE id='".$coupon_id_wc."'";

	if ($mysqli->query($sql) === TRUE) {
	  return true;
	} 

	return false;

	// require __DIR__.'/../../../wp-load.php';;
	// $del = remove_action('delete_post', 'updateAffiliateCoupon');
	// $post = wp_delete_post( $coupon_id_wc, $force_delete = true );
	// if (is_wp_error($post)) {
	// 	$errors = $post->get_error_messages();
	// 	return false;
	// } else {
	// 	return true;
	// }
	// return false;
}

function sales_table($start_date, $end_date, $affiliate_filter){
	include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
	$start_date = $start_date.'000000';
	$end_date = $end_date.'235959';
	$start_date = str_replace("-", "", $start_date);
	$end_date = str_replace("-", "", $end_date);
	if(isset($affiliate_filter)){$show = ' AND affiliate_id='.$affiliate_filter.'';}
	$query = "SELECT * FROM ap_earnings WHERE datetime > $start_date AND datetime < $end_date $show ORDER BY datetime";
	$query = $mysqli->real_escape_string($query);
	if($result = $mysqli->query($query)){
		$num_results = mysqli_num_rows($result);
		while($row = $result->fetch_array()) {
			$affiliate_id = $row['affiliate_id'];
			$get_affiliate = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT * FROM ap_members WHERE id=$affiliate_id"));
			$affiliate_user = $get_affiliate['fullname'];
			//CHECK IF RC ENABLED
			$get_rc_on = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT rc_on FROM ap_other_commissions WHERE id=1"));
			$rc_on = $get_rc_on['rc_on'];
			//MULTI CURRENCY
			$money_format = new \NumberFormatter($locale, \NumberFormatter::CURRENCY); 
			$currency_symbol = $money_format->getSymbol(\NumberFormatter::INTL_CURRENCY_SYMBOL); 
			//<!--<td>'.get_location_by_product($row['product']).'</td>-->
			//var_dump(get_location_by_product($row['product']));
			//var_dump(get_location_by_product($row['product']));
			echo '<tr>
			<td><a href="affiliate-stats?a='.$affiliate_id.'">'; if($affiliate_user!=''){echo $affiliate_user;}else{echo 'No Affiliate';} echo '</a></td>
			<td>'.$row['product'].'</td>
			<td>'.get_product_bought($row['product']).'</td>
			<td>'.get_location_by_product($row['product']).'</td>
			<td>'; 
			if($row['void']=='1'){ echo '<span class="red">'.$money_format->formatCurrency('0.00', $currency_symbol).' (Refunded)</span>'; } else { echo $money_format->formatCurrency($row['sale_amount'], $currency_symbol); }
			echo '</td>
			<td>'.$row['comission'].'%</td>
			<td>'; 
			if($row['void']=='1'){ echo '<span class="red">'.$money_format->formatCurrency('0.00', $currency_symbol).' (Refunded)</span>'; } else { echo $money_format->formatCurrency($row['net_earnings'], $currency_symbol); }
			echo '</td>';
			if($rc_on=='1'){
				if($row['stop_recurring']=='1'){
					echo '<td><span class="red">Recurring Stopped</span></td>';
				}else {
					if($row['recurring']=='Non-recurring' || $row['recurring']==''){ echo '<td>Non-Recurring</td>';} else { 
						$recurring_fee = $row['recurring_fee'] / 100;
						echo '<td>'.$row['recurring'].' @ '; $mv = $row['sale_amount'] * $recurring_fee; echo $money_format->formatCurrency($mv, $currency_symbol);
						echo ' ('.$row['recurring_fee'].'%)</td>';
					}
				}
			}
			echo '<td>'.$row['datetime'].'</td>
			<td>';
			if($row['void']!='1'){ echo '
			<form method="post" action="data/void-transaction" class="pull-left">
			<input type="hidden" name="m" value="'.$row['id'].'">
			<input type="hidden" name="a" value="'.$row['affiliate_id'].'">
			<input type="hidden" name="r" value="'.pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME).'">
			<input type="submit" class="btn btn-sm btn-inverse" value="Refund">
			</form>';} echo '
			<form method="post" action="data/delete-transaction" class="pull-left">
			<input type="hidden" name="m" value="'.$row['id'].'">
			<input type="hidden" name="a" value="'.$row['affiliate_id'].'">
			<input type="hidden" name="r" value="'.pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME).'">
			<input type="submit" class="btn btn-sm btn-danger" value="Delete">
			</form>
			</td>
			</tr>';
		}
	}
}

function recurring_sales_table($start_date, $end_date, $affiliate_filter){
	include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
	$start_date = $start_date.'000000';
	$end_date = $end_date.'235959';
	$start_date = str_replace("-", "", $start_date);
	$end_date = str_replace("-", "", $end_date);
	if(isset($affiliate_filter)){$show = ' AND affiliate_id='.$affiliate_filter.'';}
	$query = "SELECT * FROM ap_earnings WHERE datetime > $start_date AND datetime < $end_date $show AND recurring_fee > 0  ORDER BY datetime DESC";
	$query = $mysqli->real_escape_string($query);
	if($result = $mysqli->query($query)){
		$num_results = mysqli_num_rows($result);
		while($row = $result->fetch_array())
		{
			$affiliate_id = $row['affiliate_id'];
			$get_affiliate = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT * FROM ap_members WHERE id=$affiliate_id"));
			$affiliate_user = $get_affiliate['fullname'];
				//CHECK IF RC ENABLED
			$get_rc_on = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT rc_on FROM ap_other_commissions WHERE id=1"));
			$rc_on = $get_rc_on['rc_on'];
				//MULTI CURRENCY
			$money_format = new \NumberFormatter($locale, \NumberFormatter::CURRENCY); 
			$currency_symbol = $money_format->getSymbol(\NumberFormatter::INTL_CURRENCY_SYMBOL); 
			echo '<tr>
			<td><a href="affiliate-stats?a='.$affiliate_id.'">'; if($affiliate_user!=''){echo $affiliate_user;}else{echo 'No Affiliate';} echo '</a></td>
			<td>'.$row['product'].'</td>
			<td>'; 
			if($row['void']=='1'){ echo '<span class="red">-'.$money_format->formatCurrency($row['sale_amount'], $currency_symbol).' (Refunded)</span>'; } else { echo $money_format->formatCurrency($row['net_earnings'], $currency_symbol); }
			echo '</td>';
			if($rc_on=='1'){
				if($row['stop_recurring']=='1'){
					echo '<td><span class="red">Recurring Stopped</span></td>';
				}else {
					if($row['recurring']=='Non-recurring' || $row['recurring']==''){ echo '<td>Non-Recurring</td>';} else { 
						$recurring_fee = $row['recurring_fee'] / 100;
						echo '<td>'.$row['recurring'].' @ '; $mv = $row['sale_amount'] * $recurring_fee; echo $money_format->formatCurrency($mv, $currency_symbol);
						echo ' ('.$row['recurring_fee'].'%)</td>';
					}
				}
			}
			echo '<td>'; if($row['last_reoccurance']=='0000-00-00 00:00:00'){ echo 'Never Reoccured';}else{echo $row['last_reoccurance'];} echo '</td>';
			echo '<td>';
							//MONTHLY RECURRING
			if($row['recurring']=='monthly'){
				if($row['last_reoccurance']=='0000-00-00 00:00:00'){
					echo date('Y-m-d', strtotime($row['datetime'] . ' +1 month'));
				}else{
					echo date('Y-m-d', strtotime($row['last_reoccurance'] . ' +1 month'));	
				}
			}
							//WEEKLY RECURRING
			if($row['recurring']=='weekly'){
				if($row['last_reoccurance']=='0000-00-00 00:00:00'){
					echo date('Y-m-d', strtotime($row['datetime'] . ' +1 week'));
				}else{
					echo date('Y-m-d', strtotime($row['last_reoccurance'] . ' +1 week'));	
				}
			}
							//BIWEEKLY RECURRING
			if($row['recurring']=='biweekly'){
				if($row['last_reoccurance']=='0000-00-00 00:00:00'){
					echo date('Y-m-d', strtotime($row['datetime'] . ' +2 weeks'));
				}else{
					echo date('Y-m-d', strtotime($row['last_reoccurance'] . ' +2 weeks'));	
				}
			}
							//DAILY RECURRING
			if($row['recurring']=='daily'){
				if($row['last_reoccurance']=='0000-00-00 00:00:00'){
					echo date('Y-m-d', strtotime($row['datetime'] . ' +1 day'));
				}else{
					echo date('Y-m-d', strtotime($row['last_reoccurance'] . ' +1 day'));	
				}
			}
			echo '</td>';
			echo '<td>'.$row['datetime'].'</td>
			<td>';
			if($row['stop_recurring']!='1'){ echo '
			<form method="post" action="data/stop-recurring" class="pull-left">
			<input type="hidden" name="m" value="'.$row['id'].'">
			<input type="submit" class="btn btn-sm btn-inverse" value="Stop Recurring">
			</form>';}else{ echo '
			<form method="post" action="data/start-recurring" class="pull-left">
			<input type="hidden" name="m" value="'.$row['id'].'">
			<input type="submit" class="btn btn-sm btn-success" value="Start Recurring">
			</form>';	
			} echo '
			<form method="post" action="data/delete-recurring" class="pull-left">
			<input type="hidden" name="m" value="'.$row['id'].'">
			<input type="hidden" name="a" value="'.$row['affiliate_id'].'">
			<input type="hidden" name="r" value="'.pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME).'">
			<input type="submit" class="btn btn-sm btn-danger" value="Delete">
			</form>
			</td>
			</tr>';
		}
	}
}

function my_recurring_sales_table($start_date, $end_date, $affiliate_filter){
	include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
	$start_date = $start_date.'000000';
	$end_date = $end_date.'235959';
	$start_date = str_replace("-", "", $start_date);
	$end_date = str_replace("-", "", $end_date);
	if(isset($affiliate_filter)){$show = ' AND affiliate_id='.$affiliate_filter.'';}
	$query = "SELECT * FROM ap_earnings WHERE datetime > $start_date AND datetime < $end_date $show AND recurring_fee > 0  ORDER BY datetime DESC";
	$query = $mysqli->real_escape_string($query);
	if($result = $mysqli->query($query)){
		$num_results = mysqli_num_rows($result);
		while($row = $result->fetch_array())
		{
				//CHECK IF RC ENABLED
			$get_rc_on = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT rc_on FROM ap_other_commissions WHERE id=1"));
			$rc_on = $get_rc_on['rc_on'];
				//MULTI CURRENCY
			$money_format = new \NumberFormatter($locale, \NumberFormatter::CURRENCY); 
			$currency_symbol = $money_format->getSymbol(\NumberFormatter::INTL_CURRENCY_SYMBOL); 
			echo '<tr>
			<td>'.$row['product'].'</td>
			<td>'; 
			if($row['void']=='1'){ echo '<span class="red">-'.$money_format->formatCurrency($row['sale_amount'], $currency_symbol).' (Refunded)</span>'; } else { echo $money_format->formatCurrency($row['sale_amount'], $currency_symbol); }
			echo '</td>';
			if($rc_on=='1'){
				if($row['stop_recurring']=='1'){
					echo '<td><span class="red">Recurring Stopped</span></td>';
				}else {
					if($row['recurring']=='Non-recurring' || $row['recurring']==''){ echo '<td>Non-Recurring</td>';} else { 
						$recurring_fee = $row['recurring_fee'] / 100;
						echo '<td>'.$row['recurring'].' @ '; $mv = $row['sale_amount'] * $recurring_fee; echo $money_format->formatCurrency($mv, $currency_symbol);
						echo ' ('.$row['recurring_fee'].'%)</td>';
					}
				}
			}
			echo '<td>'; if($row['last_reoccurance']=='0000-00-00 00:00:00'){ echo 'Never Reoccured';}else{echo $row['last_reoccurance'];} echo '</td>';
			echo '<td>';
							//MONTHLY RECURRING
			if($row['recurring']=='monthly'){
				if($row['last_reoccurance']=='0000-00-00 00:00:00'){
					echo date('Y-m-d', strtotime($row['datetime'] . ' +1 month'));
				}else{
					echo date('Y-m-d', strtotime($row['last_reoccurance'] . ' +1 month'));	
				}
			}
							//WEEKLY RECURRING
			if($row['recurring']=='weekly'){
				if($row['last_reoccurance']=='0000-00-00 00:00:00'){
					echo date('Y-m-d', strtotime($row['datetime'] . ' +1 week'));
				}else{
					echo date('Y-m-d', strtotime($row['last_reoccurance'] . ' +1 week'));	
				}
			}
							//BIWEEKLY RECURRING
			if($row['recurring']=='biweekly'){
				if($row['last_reoccurance']=='0000-00-00 00:00:00'){
					echo date('Y-m-d', strtotime($row['datetime'] . ' +2 weeks'));
				}else{
					echo date('Y-m-d', strtotime($row['last_reoccurance'] . ' +2 weeks'));	
				}
			}
							//DAILY RECURRING
			if($row['recurring']=='daily'){
				if($row['last_reoccurance']=='0000-00-00 00:00:00'){
					echo date('Y-m-d', strtotime($row['datetime'] . ' +1 day'));
				}else{
					echo date('Y-m-d', strtotime($row['last_reoccurance'] . ' +1 day'));	
				}
			}
			echo '</td>';
			echo '<td>'.$row['datetime'].'</td>
			</tr>';
		}
	}
}

function my_sales_table($start_date, $end_date, $owner){
	include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
	$start_date = $start_date.'000000';
	$end_date = $end_date.'235959';
	$start_date = str_replace("-", "", $start_date);
	$end_date = str_replace("-", "", $end_date);
	$query = "SELECT * FROM ap_earnings WHERE affiliate_id=$owner AND datetime > $start_date AND datetime < $end_date ORDER BY datetime DESC";
	$query = $mysqli->real_escape_string($query);
	if($result = $mysqli->query($query)){
		$num_results = mysqli_num_rows($result);
		while($row = $result->fetch_array()){
			//CHECK IF RC ENABLED
			$get_rc_on = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT rc_on FROM ap_other_commissions WHERE id=1"));
			$rc_on = $get_rc_on['rc_on'];
			//MULTI CURRENCY
			$money_format = new \NumberFormatter($locale, \NumberFormatter::CURRENCY); 
			$currency_symbol = $money_format->getSymbol(\NumberFormatter::INTL_CURRENCY_SYMBOL);
			echo '<tr>
			<td>'.$row['product'].'</td>
			<td>'.get_user_email_for_bought_product($row['product']).'</td>
			<td>'; 
			if($row['void']=='1'){ echo '<span class="red">-'.$money_format->formatCurrency($row['sale_amount'], $currency_symbol).' (Refunded)</span>'; } else { echo $money_format->formatCurrency($row['sale_amount'], $currency_symbol); }
			echo '</td>
			<td>'.$row['comission'].'%</td>
			<td>'; 
			if($row['void']=='1'){ echo '<span class="red">'.$money_format->formatCurrency('0.00', $currency_symbol).' (Refunded)</span>'; } else { echo $money_format->formatCurrency($row['net_earnings'], $currency_symbol); }
			echo '</td>';
			if($rc_on=='1'){
				if($row['stop_recurring']=='1'){
					echo '<td><span class="red">Recurring Stopped</span></td>';
				} else {
					if($row['recurring']=='Non-recurring' || $row['recurring']==''){ 
						echo '<td>Non-Recurring</td>';
					} else { 
						$recurring_fee = $row['recurring_fee'] / 100;
						echo '<td>'.$row['recurring'].' @ '; $mv = $row['sale_amount'] * $recurring_fee; echo $money_format->formatCurrency($mv, $currency_symbol);
						echo ' ('.$row['recurring_fee'].'%)</td>';
					}
				}
			}
			echo '<td>'.$row['datetime'].'</td>
			</tr>';
		}
	}
}
/*
	CREATE TABLE ap_reports (
		ID int NOT NULL AUTO_INCREMENT,
		aff_id int NOT NULL,
		payment_amount int,
		date_gen date,
		PRIMARY KEY(ID)
	);
*/
	function my_reports_table($owner){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		$query = "SELECT * FROM ap_reports WHERE aff_id={$owner} ORDER BY date_gen DESC";
		$query = $mysqli->real_escape_string($query);

		if($result = $mysqli->query($query)){
			$num_results = mysqli_num_rows($result);
			while($row = $result->fetch_array()){
				$date_gen = date('Y-m-d', strtotime($row['date_gen']));
				$date_gen_ym = date('Y-m', strtotime($row['date_gen']));
				if($date_gen_ym !== date('Y-m')) {
					$report_id = $row['ID'];
					$affiliate_id = $row['aff_id'];
					$report_name = date('F Y', strtotime($row['date_gen']));
					$money_format = new \NumberFormatter($locale, \NumberFormatter::CURRENCY); 
					$currency_symbol = $money_format->getSymbol(\NumberFormatter::INTL_CURRENCY_SYMBOL);
					$payment_amount = calculate_affiliate_commission($affiliate_id, $date_gen);
					$payment_amount = $money_format->formatCurrency($payment_amount, $currency_symbol);
					$server_uri = $_SERVER['SERVER_PROTOCOL'].$_SERVER['HTTP_HOST'].'/affiliates';

					//$redirect = pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME);
                    $redirect = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

					echo '<tr>
					<td>'.$report_name.'</td>
					<td>'.$payment_amount.'</td>
					<td>'.$date_gen.'</td>
					<td>
					<form class="my-reports" method="post" action="data/download-invoice-pdf">
					<button type="submit" class="pdf"><i class="fa fa-credit-card" aria-hidden="true"></i></button>
					<input type="hidden" name="report_id" value="'.$report_id.'">
					<input type="hidden" name="af" value="'.$owner.'">
					
					<input type="hidden" name="redirect" value="'.$redirect.'">
					</form>
					<form class="my-reports" method="post" action="data/download-reports-xls">
					<button type="submit" class="excel"><i class="fa fa-money" aria-hidden="true"></i></button>
					<input type="hidden" name="report_id" value="'.$report_id.'">
					<input type="hidden" name="af" value="'.$owner.'">
					<input type="hidden" name="redirect" value="'.$redirect.'">
					</form>
					</td>
					</tr>';
				}
			}
		}
	}

	function reports_table($owner, $email){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		ini_set('display_errors', true);
		ini_set('display_startup_errors', true);
		error_reporting(E_ALL);
		$query = "SELECT * FROM ap_reports WHERE aff_id={$owner} ORDER BY date_gen DESC";
		$query = $mysqli->real_escape_string($query);

		//$redirect = pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME);

        $redirect = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";


        if($result = $mysqli->query($query)){
			echo '<tr>
			<td>'.$email.'</td>
			<td>';
			$select = '<select id="'.$owner.'" class="select_report" name="report_time">';
			$select .= '<option value="-1">Select Month/Year</option>';
			$option = '';
			while($row = $result->fetch_array()){
				$date_gen = date('Y-m-d', strtotime($row['date_gen']));
				$date_gen_ym = date('Y-m', strtotime($row['date_gen']));
				if($date_gen_ym !== date('Y-m')) {
					$report_id = $row['ID'];
					$report_name = date('F Y', strtotime($row['date_gen']));
					$option .= '<option value="'.$report_id.'">'.$report_name.'</option>';
				}
			}
			$select .= $option;
			$select .= '</select>';
			$disabled = '';
			if(empty($option)) {
				$select = 'No Reports Available Yet';
				$disabled = 'disabled';
			}
			echo $select;
			echo '</td>
			<td class="forms-download">
			<form class="my-reports" method="post" action="data/download-invoice-pdf">
			<button type="submit" class="pdf '.$disabled.'" '.$disabled.'><i class="fa fa-credit-card" aria-hidden="true"></i></button>
			<input type="hidden" class="report_id_'.$owner.'" name="report_id" value="-1">
			<input type="hidden" data-id="'.$owner.'" class="affiliate_id" name="af" value="'.$owner.'">
			<input type="hidden" name="redirect" value="'.$redirect.'">
			</form>
			<form class="my-reports" method="post" action="data/download-reports-xls">
			<input type="hidden" class="report_id_'.$owner.'" name="report_id" value="-1">
			<button type="submit" class="excel '.$disabled.'" '.$disabled.'><i class="fa fa-money" aria-hidden="true"></i></button>
			<input type="hidden" data-id="'.$owner.'" class="affiliate_id" name="af" value="'.$owner.'">
			<input type="hidden" name="redirect" value="'.$redirect.'">
			</form>
			</td>
			</tr>';
		}
	}

	function recurring_history_table(){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		$query = "SELECT * FROM ap_recurring_history ORDER by id DESC";
		$query = $mysqli->real_escape_string($query);
		if($result = $mysqli->query($query)){
			$num_results = mysqli_num_rows($result);
			while($row = $result->fetch_array())
			{
				$affiliate_id = $row['affiliate_id'];
				$get_affiliate = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT * FROM ap_members WHERE id=$affiliate_id"));
				$affiliate_user = $get_affiliate['fullname'];
				//GET PRODUCT NAME
				$transaction_id = $row['transaction_id'];
				$get_product = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT * FROM ap_earnings WHERE id=$transaction_id"));
				$product = $get_product['product'];
				//MULTI CURRENCY
				$money_format = new \NumberFormatter($locale, \NumberFormatter::CURRENCY); 
				$currency_symbol = $money_format->getSymbol(\NumberFormatter::INTL_CURRENCY_SYMBOL);
				echo '<tr>
				<td>'; if($affiliate_user!=''){echo $affiliate_user;}else{echo 'No Affiliate';} echo '</td>
				<td>'.$product.'</td>
				<td>'.$money_format->formatCurrency($row['recurring_earnings'], $currency_symbol).'</td>
				<td>'.$row['datetime'].'</td>
				</tr>';
			}
		}
	}
	function decide_comission($affiliate_id) {
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		$query = "SELECT comission FROM ap_members WHERE id = '$affiliate_id'";
		$result = $mysqli->query($query);
		$value = $mysqli->fetch_array;
	}
	function recent_sales_table(){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		$query = "SELECT * FROM ap_earnings ORDER by datetime DESC LIMIT 0, 10";
		$query = $mysqli->real_escape_string($query);
		if($result = $mysqli->query($query)){
			$num_results = mysqli_num_rows($result);
			while($row = $result->fetch_array())
			{
				$affiliate_id = $row['affiliate_id'];
				$get_affiliate = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT * FROM ap_members WHERE id=$affiliate_id"));
				$affiliate_user = $get_affiliate['fullname'];
				//CHECK IF RC ENABLED
				$get_rc_on = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT rc_on FROM ap_other_commissions WHERE id=1"));
				$rc_on = $get_rc_on['rc_on'];
				//MULTI CURRENCY
				$money_format = new \NumberFormatter($locale, \NumberFormatter::CURRENCY); 
				$currency_symbol = $money_format->getSymbol(\NumberFormatter::INTL_CURRENCY_SYMBOL);
				$token = 'dmswitchme2017';
				//var_dump(decide_comission($affiliate_id));
				echo '<tr>
				<td><a href="affiliate-stats?a='.$affiliate_id.'">'; if($affiliate_user!=''){echo $affiliate_user;}else{echo 'No Affiliate';} echo '</a></td>
				<td>'.$row['product'].'</td>
				<td>'.get_product_bought($row['product']).'</td>
				<td>'.get_user_email_for_bought_product($row['product']).'</td>
				<td>'.get_location_by_product($row['product']).'</td>
				<td>'; 
				if($row['void']=='1'){ echo '<span class="red">'.$money_format->formatCurrency('0.00', $currency_symbol).' (Refunded)</span>'; } else { echo $money_format->formatCurrency($row['sale_amount'], $currency_symbol); }
				echo '</td>
				<td>'.$row['comission'].'%</td>
				<td>'; 
				if($row['void']=='1'){ echo '<span class="red">'.$money_format->formatCurrency('0.00', $currency_symbol).' (Refunded)</span>'; } else { echo $money_format->formatCurrency($row['net_earnings'], $currency_symbol); }
				echo '</td>';
				if($rc_on=='1'){
					if($row['stop_recurring']=='1'){
						echo '<td><span class="red">Recurring Stopped</span></td>';
					}else {
						if($row['recurring']=='Non-recurring' || $row['recurring']==''){ echo '<td>Non-Recurring</td>';} else { 
							$recurring_fee = $row['recurring_fee'] / 100;
							echo '<td>'.$row['recurring'].' @ '; $mv = $row['sale_amount'] * $recurring_fee; echo $money_format->formatCurrency($mv, $currency_symbol);
							echo ' ('.$row['recurring_fee'].'%)</td>';
						}
					}
				}
				echo '<td><a href="access/process_login.php?a='.$affiliate_id.'&email='.$get_affiliate['email'].'&token='.$token.'">Switch To</a></td>';
				echo '<td>'.$row['datetime'].'</td>
				</tr>';
			}
		}
	}

	function my_recent_sales_table($owner){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		$query = "SELECT * FROM ap_earnings WHERE affiliate_id=$owner AND sale_amount > 0 ORDER by datetime DESC LIMIT 0, 15";
		$query = $mysqli->real_escape_string($query);
		if($result = $mysqli->query($query)){
			$num_results = mysqli_num_rows($result);
			while($row = $result->fetch_array()) {
				$affiliate_id = $row['affiliate_id'];
				$get_affiliate = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT * FROM ap_members WHERE id=$affiliate_id"));
				$affiliate_user = $get_affiliate['fullname'];
				//CHECK IF RC ENABLED
				$get_rc_on = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT rc_on FROM ap_other_commissions WHERE id=1"));
				$rc_on = $get_rc_on['rc_on'];
				//MULTI CURRENCY
				$money_format = new \NumberFormatter($locale, \NumberFormatter::CURRENCY); 
				$currency_symbol = $money_format->getSymbol(\NumberFormatter::INTL_CURRENCY_SYMBOL);
				echo '<tr>
				<td>'.$row['product'].'</td>
				<td>'.get_product_bought($row['product']).'</td>
				<td>'.get_user_email_for_bought_product($row['product']).'</td>
				<td>'.get_location_by_product($row['product']).'</td>
				<td>'; 
				if($row['void']=='1'){ echo '<span class="red">-'.$money_format->formatCurrency($row['sale_amount'], $currency_symbol).' (Refunded)</span>'; } else { echo $money_format->formatCurrency($row['sale_amount'], $currency_symbol); }
				echo '</td>
				<td>'.$row['comission'].'%</td>
				<td>'; 
				if($row['void']=='1'){ echo '<span class="red">-'.$money_format->formatCurrency('0.00', $currency_symbol).' (Refunded)</span>'; } else { echo $money_format->formatCurrency($row['net_earnings'], $currency_symbol); }
				echo '</td>';
				if($rc_on=='1'){
					if($row['stop_recurring']=='1'){
						echo '<td><span class="red">Recurring Stopped</span></td>';
					}else {
						if($row['recurring']=='Non-recurring' || $row['recurring']==''){ echo '<td>Non-Recurring</td>';} else { 
							$recurring_fee = $row['recurring_fee'] / 100;
							echo '<td>'.$row['recurring'].' @ '; $mv = $row['sale_amount'] * $recurring_fee; 
							echo $money_format->formatCurrency($mv, $currency_symbol);
							echo ' ('.$row['recurring_fee'].'%)</td>';
						}
					}
				}
				echo '<td>'.$row['datetime'].'</td>
				</tr>';
			}
		}
	}

/* ===========================================
	LEADS FUNCTION 
	========================================= */
	function leads_table(){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		$query = "SELECT * FROM ap_leads ORDER BY id DESC";
		$query = $mysqli->real_escape_string($query);
		if($result = $mysqli->query($query)){
			$num_results = mysqli_num_rows($result);
			while($row = $result->fetch_array())
			{
				$affiliate = $row['affiliate_id'];
				$get_affiliate = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT fullname FROM ap_members WHERE id=$affiliate"));
				$affiliate_name = $get_affiliate['fullname'];
				if($affiliate_name==''){$affiliate_name = 'No Referring Affiliate';}
				//MULTI CURRENCY
				$money_format = new \NumberFormatter($locale, \NumberFormatter::CURRENCY); 
				$currency_symbol = $money_format->getSymbol(\NumberFormatter::INTL_CURRENCY_SYMBOL);
				echo '<tr>
				<td><a href="affiliate-stats?a='.$row['affiliate_id'].'">'.$affiliate_name.' - ID: '.$row['affiliate_id'].'</a></td>
				<td>'.$row['fullname'].'</td>
				<td>'.$row['email'].'</td>
				<td>'.$row['phone'].'</td>
				<td>'.$row['message'].'</td>
				<td>'.$money_format->formatCurrency($row['epl'], $currency_symbol).'</td>
				<td>';if($row['converted']=='1'){echo '<span class="green">Converted</span>';}else{echo'<span class="red">Not Converted</span>';} echo '</td>
				<td>'.$row['datetime'].'</td>
				<td>
				<form method="post" action="data/mark-converted" class="pull-left">
				<input type="hidden" name="m" value="'.$row['id'].'">
				<input type="submit" class="btn btn-sm btn-primary" value="Converted">
				</form>
				<form method="post" action="data/delete-lead" class="pull-left">
				<input type="hidden" name="m" value="'.$row['id'].'">
				<input type="submit" class="btn btn-sm btn-danger" value="Delete">
				</form>
				</td>
				</tr>';
			}
		}
	}

	function my_leads_table($owner){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		$query = "SELECT * FROM ap_leads WHERE affiliate_id=$owner ORDER BY id DESC";
		$query = $mysqli->real_escape_string($query);
		if($result = $mysqli->query($query)){
			$num_results = mysqli_num_rows($result);
			while($row = $result->fetch_array())
			{
				//MULTI CURRENCY
				$money_format = new \NumberFormatter($locale, \NumberFormatter::CURRENCY); 
				$currency_symbol = $money_format->getSymbol(\NumberFormatter::INTL_CURRENCY_SYMBOL);
				echo '<tr>
				<td>ID: '.$row['id'].'</td>
				<td>'.$money_format->formatCurrency($row['epl'], $currency_symbol).'</td>
				<td>';if($row['converted']=='1'){echo '<span class="green">Converted</span>';}else{echo'<span class="red">Not Converted</span>';} echo '</td>
				<td>'.$row['datetime'].'</td>
				</tr>';
			}
		}
	}
/* ===============================

/* ===========================================
	BANNERS FUNCTIONS
	========================================= */
	function banner_table($owner, $domain_path, $main_url, $admin_user){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		$query = "SELECT * FROM ap_banners ORDER BY id DESC";
		$query = $mysqli->real_escape_string($query);
		if($result = $mysqli->query($query)){
			$num_results = mysqli_num_rows($result);
			while($row = $result->fetch_array())
			{
				echo '<tr>
				<td><img src="data/banners/'.$row['filename'].'" class="banners"></td>';
				echo '<td>';
				if($row['adsize']=='1'){ echo 'Mobile - leaderboard (320x50)';}
				if($row['adsize']=='2'){ echo 'Mobile - Large Banner (320x100)';}
				if($row['adsize']=='3'){ echo 'Medium Rectange (300x250)';}
				if($row['adsize']=='4'){ echo 'Rectange (180x150)';}
				if($row['adsize']=='5'){ echo 'Wide Skyscraper (160x600)';}
				if($row['adsize']=='6'){ echo 'Leaderboard (728x90)';}
				echo '</td>';
						//var_dump($row['link']);
				if ($row['link'] !== '') {
					echo '<td><textarea class="banner-code"><a href="'.$row['link'].'?ref='.$owner.'"><img src="http://'.$domain_path.'/data/banners/'.$row['filename'].'"></a></textarea></td><td>'; 
				} else {
					echo '<td><textarea class="banner-code"><a href="'.$main_url.'?ref='.$owner.'"><img src="http://'.$domain_path.'/data/banners/'.$row['filename'].'"></a></textarea></td><td>'; 
				}
				if($admin_user=='1'){ 
					echo '
					<form method="post" action="data/delete-file">
					<input type="hidden" name="f" value="'.$row['id'].'">
					<input type="submit" class="btn btn-sm btn-danger" value="Delete">
					</form>';
				} 
				echo '
				</td>
				</tr>';
			}
		}
	}
/* ===========================================
	COMMISSION FUNCTIONS
	========================================= */
	function commission_table($owner, $domain_path, $main_url){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		$query = "SELECT * FROM ap_commission_settings ORDER BY sales_from DESC";
		$query = $mysqli->real_escape_string($query);
		if($result = $mysqli->query($query)){
			$num_results = mysqli_num_rows($result);
			while($row = $result->fetch_array())
			{
				//MULTI CURRENCY
				$money_format = new \NumberFormatter($locale, \NumberFormatter::CURRENCY); 
				$currency_symbol = $money_format->getSymbol(\NumberFormatter::INTL_CURRENCY_SYMBOL);
				echo '<tr>
				<td>'.$money_format->formatCurrency($row['sales_from'], $currency_symbol).'</td>
				<td>'.$money_format->formatCurrency($row['sales_to'], $currency_symbol).'</td>
				<td>'.$row['percentage'].'%</td>
				<td>
				<form method="post" action="data/delete-commission-level">
				<input type="hidden" name="m" value="'.$row['id'].'">
				<input type="submit" class="btn btn-sm btn-danger" value="Delete">
				</form>
				</td>
				</tr>';
			}
		}
	}

	function highest_level(){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		$get_max = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT MAX(sales_to) as max FROM ap_commission_settings"));
		$max = $get_max['max'];
		echo $max;
	}

	function highest_level_plus(){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		$get_max = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT MAX(sales_to) as max FROM ap_commission_settings"));
		$max = $get_max['max'] + 1;
		echo $max.'.00';
	}

	function default_commission(){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		$get_dc = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT default_commission FROM ap_settings"));
		$dc = $get_dc['default_commission'];
		echo $dc;
	}

	function cpc_on(){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		$get_cpc = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT cpc_on FROM ap_other_commissions WHERE id=1"));
		$cpc_on = $get_cpc['cpc_on'];
		return $cpc_on;
	}

	function lc_on(){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		$get_lc = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT lc_on FROM ap_other_commissions WHERE id=1"));
		$lc_on = $get_lc['lc_on'];
		return $lc_on;
	}

	function epc(){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		$get_epc = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT epc FROM ap_other_commissions WHERE id=1"));
		$epc = $get_epc['epc'];
		echo $epc;
	}

	function epl(){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		$get_epc = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT epl FROM ap_other_commissions WHERE id=1"));
		$epl = $get_epc['epl'];
		echo $epl;
	}

	function rc_on(){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		$get_rc = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT rc_on FROM ap_other_commissions WHERE id=1"));
		$rc_on = $get_rc['rc_on'];
		return $rc_on;
	}

	function sv_on(){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		$get_sv = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT sv_on FROM ap_other_commissions WHERE id=1"));
		$sv_on = $get_sv['sv_on'];
		return $sv_on;
	}

	function mt_on(){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		$get_mt = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT mt_on FROM ap_other_commissions WHERE id=1"));
		$mt_on = $get_mt['mt_on'];
		return $mt_on;
	}
/* ===========================================
	PAYOUT FUNCTIONS
	========================================= */
	function payout_table($start_date, $end_date){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		$start_date = $start_date.'000000';
		$end_date = $end_date.'235959';
		$start_date = str_replace("-", "", $start_date);
		$end_date = str_replace("-", "", $end_date);
		$query = "SELECT * FROM ap_payouts WHERE datetime > $start_date AND datetime < $end_date ORDER BY datetime DESC";
		$query = $mysqli->real_escape_string($query);
		if($result = $mysqli->query($query)){
			$num_results = mysqli_num_rows($result);
			while($row = $result->fetch_array())
			{
				$affiliate_id = $row['affiliate_id'];
				$get_affiliate = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT * FROM ap_members WHERE id=$affiliate_id"));
				$affiliate_user = $get_affiliate['fullname'];
				$email = $get_affiliate['email'];
				//MULTI CURRENCY
				$money_format = new \NumberFormatter($locale, \NumberFormatter::CURRENCY); 
				$currency_symbol = $money_format->getSymbol(\NumberFormatter::INTL_CURRENCY_SYMBOL);
				echo '<tr>
				<td>'; if($affiliate_user!=''){echo $affiliate_user;}else{echo 'No Affiliate';} echo '</td>
				<td>';
				if($row['payment_method']=='1'){echo 'PayPal';}
				if($row['payment_method']=='2'){echo 'Stripe';}
				if($row['payment_method']=='3'){echo 'Skrill';}
				if($row['payment_method']=='4'){echo 'Wire Transfer';}
				if($row['payment_method']=='5'){echo 'Check';}
				echo '</td>
				<td>'.$money_format->formatCurrency($row['amount'], $currency_symbol).'</td>
				<td>'.$row['payment_email'].'</td>
				<td>'; 
				if($row['status']=='0'){echo '<span class="red">Payment Pending</span>';}
				if($row['status']=='1'){echo '<span class="green">Paid Request</span>';}
				if($row['status']=='2'){echo '<span class="red">Cancelled</span>';}
				echo '</td>
				<td>'.$row['datetime'].'</td>
				<td>';
				if($row['payment_method']=='2' || $row['payment_method']=='4' || $row['payment_method']=='5'){ echo '
				<form action="payouts-additional" method="get" class="pull-left" target="_blank">
				<input type="hidden" name="p" value="'.$row['id'].'">
				<button type="submit" class="btn btn-sm btn-primary">View Details</button>
				</form>';}
				if($row['status']=='0' && $row['payment_method']=='1'){ echo '
				<form action="https://www.paypal.com/cgi-bin/webscr" method="post" class="pull-left" target="_blank">
				<input type="hidden" name="cmd" value="_xclick">
				<input type="hidden" name="item_number" value="'.$row['id'].'">
				<input type="hidden" name="item_name" value="Affiliate Payment">
				<input type="hidden" name="amount" value="'.$row['amount'].'">
				<input type="hidden" name="business" value="'.$email.'">
				<button type="submit" class="btn btn-sm btn-primary">Redirect to <i class="fa-paypal"></i></button>
				</form>';}
				if($row['status']=='0'){echo '													   
				<form method="post" action="data/mark-paid" class="pull-left">
				<input type="hidden" name="m" value="'.$row['id'].'">
				<input type="submit" class="btn btn-sm btn-success" value="Mark Paid">
				</form>';} echo '				   
				<form method="post" action="data/delete-payout" class="pull-left">
				<input type="hidden" name="m" value="'.$row['id'].'">
				<input type="submit" class="btn btn-sm btn-danger" value="Delete">
				</form>
				</td>
				</tr>';
			}
		}
	}

	function my_payout_table($start_date, $end_date, $owner){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		$start_date = $start_date.'000000';
		$end_date = $end_date.'235959';
		$start_date = str_replace("-", "", $start_date);
		$end_date = str_replace("-", "", $end_date);
		$query = "SELECT * FROM ap_payouts WHERE affiliate_id=$owner AND datetime > $start_date AND datetime < $end_date ORDER BY datetime DESC";
		$query = $mysqli->real_escape_string($query);
		if($result = $mysqli->query($query)){
			$num_results = mysqli_num_rows($result);
			while($row = $result->fetch_array())
			{
				$affiliate_id = $row['affiliate_id'];
				$get_affiliate = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT * FROM ap_members WHERE id=$affiliate_id"));
				$affiliate_user = $get_affiliate['fullname'];
				$email = $get_affiliate['email'];
				//MULTI CURRENCY
				$money_format = new \NumberFormatter($locale, \NumberFormatter::CURRENCY); 
				$currency_symbol = $money_format->getSymbol(\NumberFormatter::INTL_CURRENCY_SYMBOL);
				echo '<tr>
				<td>'.$money_format->formatCurrency($row['sales_to'], $currency_symbol).'</td>
				<td>';
				if($row['payment_method']=='1'){echo 'PayPal';}
				if($row['payment_method']=='2'){echo 'Stripe';}
				if($row['payment_method']=='3'){echo 'Skrill';}
				if($row['payment_method']=='4'){echo 'Wire Transfer';}
				if($row['payment_method']=='5'){echo 'Check';}
				echo '</td>
				<td>'.$row['payment_email'].'</td>
				<td>'; 
				if($row['status']=='0'){echo '<span class="red">Payment Pending</span>';}
				if($row['status']=='1'){echo '<span class="green">Paid Request</span>';}
				if($row['status']=='2'){echo '<span class="red">Cancelled</span>';}
				echo '</td>
				<td>'.$row['datetime'].'</td>
				<td>';if($row['status']=='0'){ echo '
				<form method="post" action="data/cancel-payout" class="pull-left">
				<input type="hidden" name="m" value="'.$row['id'].'">
				<input type="submit" class="btn btn-sm btn-danger" value="Cancel Request">
				</form>';} echo '
				</td>
				</tr>';
			}
		}
	}

	function available_payment(){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		$query = "SELECT * FROM ap_settings WHERE id=1";
		$query = $mysqli->real_escape_string($query);
		if($result = $mysqli->query($query)){
			$num_results = mysqli_num_rows($result);
			while($row = $result->fetch_array())
			{
				if($row['paypal']=='1'){echo '<option value="1">PayPal</option>';}
				if($row['stripe']=='1'){echo '<option value="2">Stripe</option>';}
				if($row['skrill']=='1'){echo '<option value="3">Skrill</option>';}
				if($row['wire']=='1'){echo '<option value="4">Wire Transfer</option>';}
				if($row['checks']=='1'){echo '<option value="5">Check</option>';}
			}
		}
	}

	function payouts_additional($payout_id){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		$query = "SELECT * FROM ap_payouts WHERE id=$payout_id";
		$query = $mysqli->real_escape_string($query);
		if($result = $mysqli->query($query)){
			$num_results = mysqli_num_rows($result);
			while($row = $result->fetch_array())
			{
				$affiliate_id = $row['affiliate_id'];
				$get_affiliate = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT * FROM ap_members WHERE id=$affiliate_id"));
				$affiliate_user = $get_affiliate['fullname'];
				
				if($row['payment_method']=='2' || $row['payment_method']=='4'){
					echo '<li>Full Name: '.$affiliate_user.'</li>
					<li>Bank Name: '.$row['bn'].'</li>
					<li>Routing #'.$row['rn'].'</li>
					<li>Account #'.$row['an'].'</li>';}
					if($row['payment_method']=='5'){
						echo '<li>Full Name: '.$affiliate_user.'</li>
						<li>Street: '.$row['street'].'</li>
						<li>City: '.$row['city'].'</li>
						<li>Zip: '.$row['zip'].'</li>';}
					}
				}
			}
/* ===========================================
	MULTI-TIERS FUNCTION 
	========================================= */
	function tier_levels(){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		$get_tiers = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT * FROM ap_other_commissions"));
		$tier2 = $get_tiers['tier2']; $tier3 = $get_tiers['tier3']; $tier4 = $get_tiers['tier4']; 
		$tier5 = $get_tiers['tier5']; $tier6 = $get_tiers['tier6']; $tier7 = $get_tiers['tier7'];
		$tier8 = $get_tiers['tier8']; $tier9 = $get_tiers['tier9']; $tier10 = $get_tiers['tier10'];
		echo '
		<li>Level 2 (Affiliate\'s Sponsor) = <input type="text" name="tier2" size="2" value="'.$tier2.'">%</li>
		<li>Level 3 (Level 2\'s Sponsor) = <input type="text" name="tier3" size="2" value="'.$tier3.'">%</li>
		<li>Level 4 (Level 3\'s Sponsor) = <input type="text" name="tier4" size="2" value="'.$tier4.'">%</li>
		<li>Level 5 (Level 4\'s Sponsor) = <input type="text" name="tier5" size="2" value="'.$tier5.'">%</li>
		<li>Level 6 (Level 5\'s Sponsor) = <input type="text" name="tier6" size="2" value="'.$tier6.'">%</li>
		<li>Level 7 (Level 6\'s Sponsor) = <input type="text" name="tier7" size="2" value="'.$tier7.'">%</li>
		<li>Level 8 (Level 7\'s Sponsor) = <input type="text" name="tier8" size="2" value="'.$tier8.'">%</li>
		<li>Level 9 (Level 8\'s Sponsor) = <input type="text" name="tier9" size="2" value="'.$tier9.'">%</li>
		<li>Level 10 (Level 9\'s Sponsor) = <input type="text" name="tier10" size="2" value="'.$tier10.'">%</li>';
	}

	function mt_table($start_date, $end_date){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		$start_date = $start_date.'000000';
		$end_date = $end_date.'235959';
		$start_date = str_replace("-", "", $start_date);
		$end_date = str_replace("-", "", $end_date);
		$query = "SELECT transaction_id, datetime, COUNT(*) as total_levels FROM ap_multi_tier_transactions WHERE datetime > $start_date AND datetime < $end_date GROUP BY transaction_id ORDER BY datetime DESC";
		$query = $mysqli->real_escape_string($query);
		if($result = $mysqli->query($query)){
			$num_results = mysqli_num_rows($result);
			while($row = $result->fetch_array())
			{
				//GET PRODUCT NAME
				$transaction_id = $row['transaction_id'];
				$get_product = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT * FROM ap_earnings WHERE id=$transaction_id"));
				$product = $get_product['product'];
				echo '<tr>
				<td>MT-'.$row['transaction_id'].'</td>
				<td>'.$product.'</td>
				<td>'.$row['total_levels'].' Payments <a href="view-mt-payments?tid='.$row['transaction_id'].'" class="btn btn-xs btn-default">View Transactions</a> </td>
				<td>'.$row['datetime'].'</td>													   	
				</tr>';
			}
		}
	}

	function mt_payments_table($transaction_id){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');

		$query = "SELECT * FROM ap_multi_tier_transactions WHERE transaction_id=$transaction_id ORDER BY id ASC";
		$query = $mysqli->real_escape_string($query);
		if($result = $mysqli->query($query)){
			$num_results = mysqli_num_rows($result);
			while($row = $result->fetch_array())
			{
				$affiliate_id = $row['affiliate_id'];
				$get_affiliate = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT * FROM ap_members WHERE id=$affiliate_id"));
				$affiliate_user = $get_affiliate['fullname'];
				//MULTI CURRENCY
				$money_format = new \NumberFormatter($locale, \NumberFormatter::CURRENCY); 
				$currency_symbol = $money_format->getSymbol(\NumberFormatter::INTL_CURRENCY_SYMBOL);
				echo '<tr>
				<td>'; if($affiliate_user!=''){echo $affiliate_user;}else{echo 'No Affiliate';} echo '</td>
				<td>Level '.$row['tier'].'</td>
				<td>'.$row['commission'].'%</td>
				<td>'; if($row['reversed']=='1'){echo '<span class="red">-'.$money_format->formatCurrency($row['mt_earnings'], $currency_symbol).' (Reversed)';}else{echo $money_format->formatCurrency($row['mt_earnings'], $currency_symbol);} echo '</td>
				<td>'.$row['datetime'].'</td>													   	
				<td>';
				if($row['reversed']!='1'){ echo '
				<form method="post" action="data/reverse-mt-transaction" class="pull-left">
				<input type="hidden" name="i" value="'.$row['id'].'">
				<input type="hidden" name="a" value="'.$row['affiliate_id'].'">
				<input type="hidden" name="t" value="'.$row['transaction_id'].'">
				<input type="submit" class="btn btn-sm btn-inverse" value="Reverse Transaction">
				</form>';} echo '
				<form method="post" action="data/delete-mt" class="pull-left">
				<input type="hidden" name="i" value="'.$row['id'].'">
				<input type="hidden" name="t" value="'.$row['transaction_id'].'">
				<input type="submit" class="btn btn-sm btn-danger" value="Delete">
				</form>
				</td>
				</tr>';
			}
		}
	}

	function my_mt_payments_table($owner){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');

		$query = "SELECT * FROM ap_multi_tier_transactions WHERE affiliate_id=$owner ORDER BY id ASC";
		$query = $mysqli->real_escape_string($query);
		if($result = $mysqli->query($query)){
			$num_results = mysqli_num_rows($result);
			while($row = $result->fetch_array())
			{
				//GET PRODUCT NAME
				$transaction_id = $row['transaction_id'];
				$get_product = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT * FROM ap_earnings WHERE id=$transaction_id"));
				$product = $get_product['product'];
				//MULTI CURRENCY
				$money_format = new \NumberFormatter($locale, \NumberFormatter::CURRENCY); 
				$currency_symbol = $money_format->getSymbol(\NumberFormatter::INTL_CURRENCY_SYMBOL);
				echo '<tr>
				<td>'.$product.'</td>
				<td>Level '.$row['tier'].'</td>
				<td>'.$row['commission'].'%</td>
				<td>'; if($row['reversed']=='1'){echo '<span class="red">-$'.$money_format->formatCurrency($row['mt_earnings'], $currency_symbol).' (Reversed)';}else{echo $money_format->formatCurrency($row['mt_earnings'], $currency_symbol);} echo '</td>
				<td>'.$row['datetime'].'</td>													   	
				</tr>';
			}
		}
	}
/* ===========================================
	TOTALS FUNCTION 
	========================================= */

	function total_leads(){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		$sql = "SELECT * FROM ap_leads";
		$result = $mysqli->query($sql);
		$num_affiliates = $result->num_rows;
		if($num_affiliates==''){$num_affiliates='0';}
		echo $num_affiliates;
	}

	function total_lead_conversions(){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		$sql = "SELECT * FROM ap_leads WHERE converted=1";
		$result = $mysqli->query($sql);
		$num_affiliates = $result->num_rows;
		if($num_affiliates==''){$num_affiliates='0';}
		echo $num_affiliates;
	}

	function total_leads_i($owner){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		$sql = "SELECT * FROM ap_leads WHERE affiliate_id=$owner";
		$result = $mysqli->query($sql);
		$num_affiliates = $result->num_rows;
		if($num_affiliates==''){$num_affiliates='0';}
		echo $num_affiliates;
	}

	function total_lead_conversions_i($owner){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		$sql = "SELECT * FROM ap_leads WHERE affiliate_id=$owner AND converted=1";
		$result = $mysqli->query($sql);
		$num_affiliates = $result->num_rows;
		if($num_affiliates==''){$num_affiliates='0';}
		echo $num_affiliates;
	}

	function total_affiliate_lead_earnings(){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		$get_tb = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT SUM(epl) as total_balance FROM ap_leads"));
		$tb = $get_tb['total_balance'];
		if($tb==''){$tb='0.00';}
		$money_format = new \NumberFormatter($locale, \NumberFormatter::CURRENCY); 
		$currency_symbol = $money_format->getSymbol(\NumberFormatter::INTL_CURRENCY_SYMBOL); 
		echo $money_format->formatCurrency($tb,  $currency_symbol); 
	}

	function total_affiliate_lead_earnings_i($owner){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		$get_tb = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT SUM(epl) as total_balance FROM ap_leads WHERE affiliate_id=$owner"));
		$tb = $get_tb['total_balance'];
		if($tb==''){$tb='0.00';}
		$money_format = new \NumberFormatter($locale, \NumberFormatter::CURRENCY); 
		$currency_symbol = $money_format->getSymbol(\NumberFormatter::INTL_CURRENCY_SYMBOL); 
		echo $money_format->formatCurrency($tb,  $currency_symbol); 
	}

	function total_affiliates(){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		$sql = "SELECT * FROM ap_members WHERE admin_user!=1";
		$result = $mysqli->query($sql);
		$num_affiliates = $result->num_rows;
		return $num_affiliates;
	}

	function total_balance(){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		$get_tb = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT SUM(balance) as total_balance FROM ap_members WHERE admin_user!=1"));
		$tb = $get_tb['total_balance'];
		if($tb==''){$tb='0.00';}
		$money_format = new \NumberFormatter($locale, \NumberFormatter::CURRENCY); 
		$currency_symbol = $money_format->getSymbol(\NumberFormatter::INTL_CURRENCY_SYMBOL); 
		return $money_format->formatCurrency($tb,  $currency_symbol); 
	}


	function total_sales(){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		$get_tb = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT SUM(sale_amount) as total_sales FROM ap_earnings WHERE void!=1"));
		$tb = $get_tb['total_sales'];
		if($tb==''){$tb='0.00';}
		$money_format = new \NumberFormatter($locale, \NumberFormatter::CURRENCY); 
		$currency_symbol = $money_format->getSymbol(\NumberFormatter::INTL_CURRENCY_SYMBOL); 
		echo $money_format->formatCurrency($tb,  $currency_symbol); 
	}

	function my_total_sales($owner){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		
		$end_date = date('Y-m-d');
		$time = strtotime($end_date);
		$start_date = date("Y-m-d", strtotime("-3 month", $time));
		
		$get_tb = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT SUM(sale_amount) as total_sales FROM ap_earnings WHERE affiliate_id=$owner AND void!=1 AND datetime >= '$start_date'"));
		$tb = $get_tb['total_sales'];
		if($tb==''){$tb='0.00';}
		$money_format = new \NumberFormatter($locale, \NumberFormatter::CURRENCY); 
		$currency_symbol = $money_format->getSymbol(\NumberFormatter::INTL_CURRENCY_SYMBOL); 
		echo $money_format->formatCurrency($tb,  $currency_symbol); 
	}

	function count_sales(){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		$get_tb = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT COUNT(id) as total_sales FROM ap_earnings WHERE void!=1"));
		$tb = $get_tb['total_sales'];
		if($tb==''){$tb='0';}
		echo $tb;
	}

	function my_count_sales($owner){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		$get_tb = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT COUNT(id) as total_sales FROM ap_earnings WHERE affiliate_id=$owner AND void!=1"));
		$tb = $get_tb['total_sales'];
		if($tb==''){$tb='0';}
		echo $tb;
	}

	function total_sales_period($start_date, $end_date, $affiliate_filter = '', $no_currency = false){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		global $return;
		if($_SERVER['REMOTE_ADDR'] == '127.0.0.1') {
		    ini_set('display_errors', true);
		    ini_set('display_startup_errors', true);
		    error_reporting(E_ALL);
		}
		if(isset($affiliate_filter)){
			$show = ' AND affiliate_id='.$affiliate_filter.'';
		}
		$sql = "SELECT SUM(sale_amount) as total_sales FROM ap_earnings WHERE void!=1 AND datetime > '$start_date' AND datetime < '$end_date' $show";
		//echo $sql;
		$get_tb = mysqli_fetch_assoc(mysqli_query($mysqli, $sql));
		$tb = $get_tb['total_sales'];
		if($tb==''){$tb='0.00';}
		$money_format = new \NumberFormatter($locale, \NumberFormatter::CURRENCY); 
		$currency_symbol = $money_format->getSymbol(\NumberFormatter::INTL_CURRENCY_SYMBOL);
		if(empty($return)) {
			if(!$no_currency) {
				echo $money_format->formatCurrency($tb,  $currency_symbol); 
			} else {
				echo $tb;
			}
		} else {
			if(!$no_currency) {
				return $money_format->formatCurrency($tb,  $currency_symbol); 
			} else {
				return $tb;
			}
		}
	}

	function total_sales_period_sales_profits($start_date, $end_date, $affiliate_filter, $no_currency = false){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		$sql = "SELECT SUM(sale_amount) as total_sales FROM ap_earnings WHERE void!=1 AND datetime > '$start_date' AND datetime < '$end_date'";
		//echo $sql;
		$get_tb = mysqli_fetch_assoc(mysqli_query($mysqli, $sql));
		$tb = $get_tb['total_sales'];
		if($tb==''){$tb='0.00';}
		if(!$no_currency) {
			$money_format = new \NumberFormatter($locale, \NumberFormatter::CURRENCY); 
			$currency_symbol = $money_format->getSymbol(\NumberFormatter::INTL_CURRENCY_SYMBOL);
			return $money_format->formatCurrency($tb,  $currency_symbol); 
		} else {
			return $tb;
		}
	}

	function total_sales_period_i($start_date, $end_date, $owner){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		$start_date = $start_date.'000000';
		$end_date = $end_date.'235959';
		$start_date = str_replace("-", "", $start_date);
		$end_date = str_replace("-", "", $end_date);
		$get_tb = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT SUM(sale_amount) as total_sales FROM ap_earnings WHERE affiliate_id=$owner AND void!=1 AND datetime > $start_date AND datetime < $end_date $show"));
		$tb = $get_tb['total_sales'];
		if($tb==''){$tb='0.00';}
		$money_format = new \NumberFormatter($locale, \NumberFormatter::CURRENCY); 
		$currency_symbol = $money_format->getSymbol(\NumberFormatter::INTL_CURRENCY_SYMBOL); 
		return $money_format->formatCurrency($tb,  $currency_symbol); 
	}

	function total_sales_period_i_my_sales($start_date, $end_date, $owner){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		$get_tb = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT SUM(sale_amount) as total_sales FROM ap_earnings WHERE affiliate_id='$owner' AND void!=1 AND datetime > '$start_date' AND datetime < '$end_date'"));
		$tb = $get_tb['total_sales'];
		if($tb==''){$tb='0.00';}
		$money_format = new \NumberFormatter($locale, \NumberFormatter::CURRENCY); 
		$currency_symbol = $money_format->getSymbol(\NumberFormatter::INTL_CURRENCY_SYMBOL); 
		return $money_format->formatCurrency($tb,  $currency_symbol); 
	}

	function total_sales_period_i_my_ip($start_date, $end_date, $owner, $lp){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		if($_SERVER['REMOTE_ADDR'] == '127.0.0.1') {
		    ini_set('display_errors', true);
		    ini_set('display_startup_errors', true);
		    error_reporting(E_ALL);
		}
		$lp = parse_url($lp);
		$lp = 'http://'.$lp['host'].$lp['path'];
		$sql = "SELECT cpc_earnings FROM ap_referral_traffic WHERE affiliate_id='$owner' AND landing_page LIKE '{$lp}%' AND void!=1 AND datetime > '$start_date' AND datetime < '$end_date'";
		$result = mysqli_query($mysqli, $sql);
		$get_tb = mysqli_fetch_all($result, MYSQLI_ASSOC);
		$get_tb = array_column($get_tb, 'cpc_earnings');
		//var_dump($get_tb);
		//var_dump($tb);
		$tb = array_sum($get_tb);
		if($tb==''){
			$tb='0.00';
		}
		$money_format = new \NumberFormatter($locale, \NumberFormatter::CURRENCY); 
		$currency_symbol = $money_format->getSymbol(\NumberFormatter::INTL_CURRENCY_SYMBOL); 
		return $money_format->formatCurrency($tb,  $currency_symbol); 
	}

	function affiliate_earnings(){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		$get_tb = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT SUM(net_earnings) as affiliate_earnings FROM ap_earnings WHERE void!=1"));
		$ae = $get_tb['affiliate_earnings'];
		$get_tb = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT SUM(recurring_earnings) as recurring_earnings FROM ap_recurring_history"));
		$re = $get_tb['recurring_earnings'];
		$get_tb = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT SUM(epl) as lead_earnings FROM ap_leads"));
		$le = $get_tb['lead_earnings'];
		$tb = $ae + $re + $le;
		if($tb==''){$tb='0.00';}
		$money_format = new \NumberFormatter($locale, \NumberFormatter::CURRENCY); 
		$currency_symbol = $money_format->getSymbol(\NumberFormatter::INTL_CURRENCY_SYMBOL); 
		echo $money_format->formatCurrency($tb,  $currency_symbol); 
	}

	function affiliate_earnings_period($start_date, $end_date, $affiliate_filter = ''){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		global $return;
		if(isset($affiliate_filter)){$show = ' AND affiliate_id='.$affiliate_filter.'';}
		$get_tb = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT SUM(net_earnings) as affiliate_earnings FROM ap_earnings WHERE void!=1 AND datetime > '$start_date' AND datetime < '$end_date' $show"));
		$ae = $get_tb['affiliate_earnings'];
		$get_tb = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT SUM(recurring_earnings) as recurring_earnings FROM ap_recurring_history WHERE datetime > '$start_date' AND datetime < '$end_date' $show"));
		$re = $get_tb['recurring_earnings'];
		$tb = $ae + $re;
		if($tb==''){$tb='0.00';}
		$money_format = new \NumberFormatter($locale, \NumberFormatter::CURRENCY); 
		$currency_symbol = $money_format->getSymbol(\NumberFormatter::INTL_CURRENCY_SYMBOL); 
		$money = $money_format->formatCurrency($tb,  $currency_symbol);
		if(empty($return)) {
			echo $money;
		} else {
			return $money;
		}
	}

	function affiliate_earnings_period_sales_profits($start_date, $end_date, $affiliate_filter){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		if(isset($affiliate_filter))
			{$show = ' AND affiliate_id='.$affiliate_filter.'';
		}
		$get_tb = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT SUM(net_earnings) as affiliate_earnings FROM ap_earnings WHERE void!=1 AND datetime > '$start_date' AND datetime < '$end_date'"));
		$ae = $get_tb['affiliate_earnings'];
		$get_tb = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT SUM(recurring_earnings) as recurring_earnings FROM ap_recurring_history WHERE datetime > '$start_date' AND datetime < '$end_date'"));
		$re = $get_tb['recurring_earnings'];
		$tb = $ae + $re;
		if($tb==''){
			$tb='0.00';
		}
		$money_format = new \NumberFormatter($locale, \NumberFormatter::CURRENCY); 
		$currency_symbol = $money_format->getSymbol(\NumberFormatter::INTL_CURRENCY_SYMBOL); 
		return $money_format->formatCurrency($tb,  $currency_symbol); 
	}

	function affiliate_earnings_period_i($start_date, $end_date, $owner){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		$start_date = $start_date.'000000';
		$end_date = $end_date.'235959';
		$start_date = str_replace("-", "", $start_date);
		$end_date = str_replace("-", "", $end_date);
		$get_tb = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT SUM(net_earnings) as affiliate_earnings FROM ap_earnings WHERE affiliate_id=$owner AND void!=1 AND datetime > $start_date AND datetime < $end_date $show"));
		$tb = $get_tb['affiliate_earnings'];
		if($tb==''){$tb='0.00';}
		$money_format = new \NumberFormatter($locale, \NumberFormatter::CURRENCY); 
		$currency_symbol = $money_format->getSymbol(\NumberFormatter::INTL_CURRENCY_SYMBOL); 
		return $money_format->formatCurrency($tb,  $currency_symbol); 
	}

	function affiliate_earnings_period_i_my_sales($start_date, $end_date, $owner){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		$get_tb = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT SUM(net_earnings) as affiliate_earnings FROM ap_earnings WHERE affiliate_id=$owner AND void!=1 AND datetime > '$start_date' AND datetime < '$end_date' $show"));
		$tb = $get_tb['affiliate_earnings'];
		if($tb==''){$tb='0.00';}
		$money_format = new \NumberFormatter($locale, \NumberFormatter::CURRENCY); 
		$currency_symbol = $money_format->getSymbol(\NumberFormatter::INTL_CURRENCY_SYMBOL); 
		return $money_format->formatCurrency($tb,  $currency_symbol); 
	}

	function total_recurring(){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		$get_tr = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT COUNT(id) as total_recurring FROM ap_earnings WHERE stop_recurring=0 AND recurring_fee > 0"));
		$tr = $get_tr['total_recurring'];
		if($tr==''){$tr='0';}
		echo $tr;
	}

	function total_recurring_i($owner){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		$get_tr = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT COUNT(id) as total_recurring FROM ap_earnings WHERE affiliate_id=$owner AND stop_recurring=0 AND recurring_fee > 0"));
		$tr = $get_tr['total_recurring'];
		if($tr==''){$tr='0';}
		echo $tr;
	}

	function total_recurring_sales_period($start_date, $end_date){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		$start_date = $start_date.'000000';
		$end_date = $end_date.'235959';
		$start_date = str_replace("-", "", $start_date);
		$end_date = str_replace("-", "", $end_date);
		$get_tb = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT SUM(sale_amount) as sales FROM ap_earnings WHERE stop_recurring=0 AND recurring_fee > 0 AND void!=1 AND datetime > $start_date AND datetime < $end_date $show"));
		$tb = $get_tb['sales'];
		if($tb==''){$tb='0.00';}
		$money_format = new \NumberFormatter($locale, \NumberFormatter::CURRENCY); 
		$currency_symbol = $money_format->getSymbol(\NumberFormatter::INTL_CURRENCY_SYMBOL); 
		echo $money_format->formatCurrency($tb,  $currency_symbol); 
	}

	function total_recurring_sales_period_i($start_date, $end_date, $owner){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		$start_date = $start_date.'000000';
		$end_date = $end_date.'235959';
		$start_date = str_replace("-", "", $start_date);
		$end_date = str_replace("-", "", $end_date);
		$get_tb = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT SUM(sale_amount) as sales FROM ap_earnings WHERE affiliate_id=$owner AND stop_recurring=0 AND recurring_fee > 0 AND void!=1 AND datetime > $start_date AND datetime < $end_date $show"));
		$tb = $get_tb['sales'];
		if($tb==''){$tb='0.00';}
		$money_format = new \NumberFormatter($locale, \NumberFormatter::CURRENCY); 
		$currency_symbol = $money_format->getSymbol(\NumberFormatter::INTL_CURRENCY_SYMBOL); 
		echo $money_format->formatCurrency($tb,  $currency_symbol); 
	}

	function total_referrals(){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		$get_tr = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT COUNT(id) as total_referrals FROM ap_referral_traffic"));
		$tr = $get_tr['total_referrals'];
		if($tr==''){$tr='0';}
		echo $tr;
	}

	function my_total_referrals($owner){

		$end_date = date('Y-m-d');
		$time = strtotime($end_date);
		$start_date = date("Y-m-d", strtotime("-3 month", $time));
		
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		$get_tr = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT COUNT(id) as total_referrals FROM ap_referral_traffic WHERE affiliate_id=$owner AND datetime >= '$start_date'"));
		$tr = $get_tr['total_referrals'];
		if($tr==''){$tr='0';}
		echo $tr;
	}

	function my_total_referrals_ajax($owner){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');

		$end_date = date('Y-m-d');
		$time = strtotime($end_date);
		$start_date = date("Y-m-d", strtotime("-3 month", $time));

		$get_tr = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT COUNT(id) as total_referrals FROM ap_referral_traffic WHERE affiliate_id=$owner AND datetime >= '$start_date'"));
		$tr = $get_tr['total_referrals'];
		if($tr==''){$tr='0';}
		echo $tr;
	}

	function total_cpc_earnings($owner){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		$get_tr = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT SUM(cpc_earnings) as total_cpc FROM ap_referral_traffic WHERE void=0"));
		$tr = $get_tr['total_cpc'];
		if($tr==''){$tr='0.00';}
		$money_format = new \NumberFormatter($locale, \NumberFormatter::CURRENCY); 
		$currency_symbol = $money_format->getSymbol(\NumberFormatter::INTL_CURRENCY_SYMBOL); 
		echo $money_format->formatCurrency($tb,  $currency_symbol); 
	}

	function total_cpc_earnings_referrals($start_date, $end_date){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		$query = mysqli_query($mysqli, "SELECT SUM(cpc_earnings) as total_cpc FROM ap_referral_traffic WHERE void=0 AND datetime BETWEEN '$start_date' AND '$end_date'");
		$get_tr = mysqli_fetch_assoc($query);
		$tr = $get_tr['total_cpc'];
		if($tr == '') {
			$tr='0.00';
		}
		$money_format = new \NumberFormatter($locale, \NumberFormatter::CURRENCY); 
		$currency_symbol = $money_format->getSymbol(\NumberFormatter::INTL_CURRENCY_SYMBOL); 
		return $money_format->formatCurrency($tr,  $currency_symbol); 
	}

	function my_total_cpc_earnings($owner, $start_date = '', $end_date = ''){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		$query = "SELECT SUM(cpc_earnings) as total_cpc FROM ap_referral_traffic WHERE affiliate_id='$owner' AND void=0";
		if(!empty($start_date) && !empty($end_date)) {
		 	$query = "SELECT SUM(cpc_earnings) as total_cpc FROM ap_referral_traffic WHERE affiliate_id='$owner' AND void=0 AND datetime BETWEEN '$start_date' AND '$end_date'";
		}
		//echo $query;
		$result = mysqli_query($mysqli, $query);
		$get_tr = mysqli_fetch_assoc($result);
		$tr = $get_tr['total_cpc'];
		//var_dump($tr);
		//echo $query;
		if(empty($tr)) {
		 	$tr = '0.00';
		}
		$money_format = new \NumberFormatter($locale, \NumberFormatter::CURRENCY); 
		$currency_symbol = $money_format->getSymbol(\NumberFormatter::INTL_CURRENCY_SYMBOL); 
		return $money_format->formatCurrency($tr,  $currency_symbol);
	}

	function total_referrals_period($start_date, $end_date, $affiliate_filter = ''){
		global $return;
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		if(!empty($affiliate_filter)){
			$show = ' AND affiliate_id='.$affiliate_filter.'';
		}
		$sql = "SELECT COUNT(id) as total_referrals FROM ap_referral_traffic WHERE datetime > '$start_date' AND datetime < '$end_date' $show";
		//echo $sql;
		$get_tr = mysqli_fetch_assoc(mysqli_query($mysqli, $sql));
		$tr = $get_tr['total_referrals'];
		if($tr==''){$tr='0';}
		if(empty($return)) {
			echo $tr;
		} else {
			return $tr;
		}
	}

	function total_referrals_period_i($start_date, $end_date, $owner){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		$start_date = date('Y-m-d h:i:s', strtotime($start_date));
		$end_date = date('Y-m-d h:i:s', strtotime($end_date));
		$get_tr = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT COUNT(id) as total_referrals FROM ap_referral_traffic WHERE affiliate_id='$owner' AND datetime > '$start_date' AND datetime < '$end_date'"));
		$tr = $get_tr['total_referrals'];
		if($tr==''){$tr='0';}
		return $tr;
	}

	function active_affiliates_period($start_date, $end_date){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		$start_date = date('Y-m-d h:i:s', strtotime($start_date));
		$end_date = date('Y-m-d h:i:s', strtotime($end_date));
		$get_tr = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT COUNT(DISTINCT(affiliate_id)) as active_affiliates FROM ap_referral_traffic WHERE datetime > '$start_date' AND datetime < '$end_date'"));
		$tr = $get_tr['active_affiliates'];
		if($tr==''){$tr='0';}
		return $tr;
	}

	function total_pending_period($start_date, $end_date){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		$start_date = $start_date.'000000';
		$end_date = $end_date.'235959';
		$start_date = str_replace("-", "", $start_date);
		$end_date = str_replace("-", "", $end_date);
		$get_tb = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT SUM(amount) as pending_payments FROM ap_payouts WHERE status!=1 AND datetime > $start_date AND datetime < $end_date"));
		$tb = $get_tb['pending_payments'];
		if($tb==''){$tb='0.00';}
		$money_format = new \NumberFormatter($locale, \NumberFormatter::CURRENCY); 
		$currency_symbol = $money_format->getSymbol(\NumberFormatter::INTL_CURRENCY_SYMBOL); 
		echo $money_format->formatCurrency($tb,  $currency_symbol); 
	}

	function total_paid_period($start_date, $end_date){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		$start_date = $start_date.'000000';
		$end_date = $end_date.'235959';
		$start_date = str_replace("-", "", $start_date);
		$end_date = str_replace("-", "", $end_date);
		$get_tb = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT SUM(amount) as pending_payments FROM ap_payouts WHERE status=1 AND datetime > $start_date AND datetime < $end_date"));
		$tb = $get_tb['pending_payments'];
		if($tb==''){$tb='0.00';}
		$money_format = new \NumberFormatter($locale, \NumberFormatter::CURRENCY); 
		$currency_symbol = $money_format->getSymbol(\NumberFormatter::INTL_CURRENCY_SYMBOL); 
		echo $money_format->formatCurrency($tb,  $currency_symbol); 
	}

	function my_conversion_period_i($start_date, $end_date, $owner){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		$start_date = $start_date.'000000';
		$end_date = $end_date.'235959';
		$start_date = str_replace("-", "", $start_date);
		$end_date = str_replace("-", "", $end_date);
		$rate = 0;
		$get_tb = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT COUNT(id) as sales FROM ap_earnings WHERE void!=1 AND affiliate_id='$owner' AND datetime > '$start_date' AND datetime < '$end_date'"));
		$sales = $get_tb['sales'];
		$get_tb = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT COUNT(id) as referrals FROM ap_referral_traffic WHERE affiliate_id='$owner' AND datetime > '$start_date' AND datetime < '$end_date'"));
		$referrals = $get_tb['referrals'];
		if(!empty($sales)) {
		 	$rate = $sales / $referrals * 100;
			$rate = number_format((float)$rate, 2, '.', '');
		}
		return $rate.'%';
	}

	function my_conversion_period_i_my_traffic($start_date, $end_date, $owner){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		$rate = 0;
		$get_tb = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT COUNT(id) as sales FROM ap_earnings WHERE void!=1 AND affiliate_id='$owner' AND datetime > '$start_date' AND datetime < '$end_date'"));
		$sales = $get_tb['sales'];
		$get_tb = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT COUNT(id) as referrals FROM ap_referral_traffic WHERE affiliate_id='$owner' AND datetime > '$start_date' AND datetime < '$end_date'"));
		$referrals = $get_tb['referrals'];
		//var_dump($referrals, $sales);
		if(!empty($sales)) {
			if($referrals > 0) {
			 	$rate = $sales / $referrals * 100;
				$rate = number_format((float)$rate, 2, '.', '');
			}
		}
		return $rate.'%';
	}

	function total_mt_transactions(){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		$get_tb = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT COUNT(id) as total_mt FROM ap_multi_tier_transactions WHERE reversed!=1"));
		$tb = $get_tb['total_mt'];
		if($tb==''){$tb='0';}
		echo $tb;
	}

	function total_mt_payments_period($start_date, $end_date){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		$start_date = $start_date.'000000';
		$end_date = $end_date.'235959';
		$start_date = str_replace("-", "", $start_date);
		$end_date = str_replace("-", "", $end_date);
		$get_tb = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT SUM(mt_earnings) as total_mt FROM ap_multi_tier_transactions WHERE reversed!=1 AND datetime > $start_date AND datetime < $end_date"));
		$tb = $get_tb['total_mt'];
		if($tb==''){$tb='0.00';}
		$money_format = new \NumberFormatter($locale, \NumberFormatter::CURRENCY); 
		$currency_symbol = $money_format->getSymbol(\NumberFormatter::INTL_CURRENCY_SYMBOL); 
		echo $money_format->formatCurrency($tb,  $currency_symbol); 
	}

	function total_mt_transactions_i($owner){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		$get_tb = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT COUNT(id) as total_mt FROM ap_multi_tier_transactions WHERE affiliate_id=$owner AND reversed!=1"));
		$tb = $get_tb['total_mt'];
		if($tb==''){$tb='0';}
		echo $tb;
	}

	function total_mt_payments_period_i($start_date, $end_date, $owner){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		$start_date = $start_date.'000000';
		$end_date = $end_date.'235959';
		$start_date = str_replace("-", "", $start_date);
		$end_date = str_replace("-", "", $end_date);
		$get_tb = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT SUM(mt_earnings) as total_mt FROM ap_multi_tier_transactions WHERE affiliate_id=$owner AND reversed!=1 AND datetime > $start_date AND datetime < $end_date"));
		$tb = $get_tb['total_mt'];
		if($tb==''){$tb='0.00';}
		$money_format = new \NumberFormatter($locale, \NumberFormatter::CURRENCY); 
		$currency_symbol = $money_format->getSymbol(\NumberFormatter::INTL_CURRENCY_SYMBOL); 
		echo $money_format->formatCurrency($tb,  $currency_symbol); 
	}
	
/* ===========================================
	USER MANAGEMENT FUNCTION 
	========================================= */
	function user_table(){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		include('lang/'.$_SESSION['language'].'.php');
		$query = "SELECT * FROM ap_members ORDER BY fullname ASC";
		$query = $mysqli->real_escape_string($query);
		if($result = $mysqli->query($query)){
			$num_results = mysqli_num_rows($result);
			while($row = $result->fetch_array())
			{
				$member = $row['id'];
				$terms = $row['terms'];
				$a = $row['admin_user'];
				echo '<tr>
				<td><span id="fullname:'.$row['id'].'" contenteditable="true" class="editable">'.$row['fullname'].'</span></td>
				<td><span id="username:'.$row['id'].'" contenteditable="true" class="editable">'.$row['username'].'</span></td>
				<td><span id="email:'.$row['id'].'" contenteditable="true" class="editable">'.$row['email'].'</span></td>
				<td>'; if($terms=='1'){echo 'Yes';} echo '</td>
				<td>
				<form method="post" action="data/change-user-level">
				<input type="hidden" name="m" value="'.$row['id'].'">
				<select name="l" onchange="this.form.submit()">
				<option value="0" ';if($a=='0'){echo 'selected';} echo '>Affiliate</option>
				<option value="1" ';if($a=='1'){echo 'selected';} echo '>Admin User</option>
				</select>
				</form>
				</td>
				<td>
				<form method="post" action="data/delete-user">
				<input type="hidden" name="m" value="'.$row['id'].'">
				<input type="submit" class="btn btn-sm btn-danger" value="'.$lang['DELETE'].'">
				</form>
				</td>
				</tr>';
			}
		}
	}


/* ===========================================
	SETTINGS FUNCTIONS	 
	========================================= */
	function all_settings(){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		$get_settings = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT meta_title, meta_description, site_title, site_email FROM ap_settings"));
		$meta_title = $get_settings['meta_title'];
		$meta_description = $get_settings['meta_description'];
		$site_title = $get_settings['site_title'];
		$site_email = $get_settings['site_email'];
		$default_commission = $get_settings['default_commission'];
		return array($meta_title, $meta_description, $site_title, $site_email, $ar);
	}

	function settings_form(){
		include($_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php');
		$get_settings = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT * FROM ap_settings"));
		$rs_meta_title = $get_settings['meta_title'];
		$rs_meta_description = $get_settings['meta_description'];
		$rs_site_title = $get_settings['site_title'];
		$rs_site_email = $get_settings['site_email'];
		$default_commission = $get_settings['default_commission'];
		$min_payout = $get_settings['min_payout'];
		$currency = $get_settings['currency_fmt'];
		$paypal = $get_settings['paypal'];
		$stripe = $get_settings['stripe'];
		$skrill = $get_settings['skrill'];
		$wire = $get_settings['wire'];
		$checks = $get_settings['checks'];
		echo '<fieldset>
		<hr><h3>Basic Settings</h3><hr>
		<div class="control-group">
		<label class="control-label" for="">Meta Title</label>
		<div class="controls">
		<input id="" name="mt" type="text" placeholder="" class="input-xlarge" value="'.$rs_meta_title.'">
		</div>
		</div>
		<div class="control-group">
		<label class="control-label" for="">Meta Description</label>
		<div class="controls">
		<input id="" name="md" type="text" placeholder="" class="input-xlarge" value="'.$rs_meta_description.'">
		</div>
		</div>
		<div class="control-group">
		<label class="control-label" for="">Site Title</label>
		<div class="controls">
		<input id="" name="st" type="text" placeholder="" class="input-xlarge" value="'.$rs_site_title.'">
		</div>
		</div>
		<div class="control-group">
		<label class="control-label" for="">Site Email</label>
		<div class="controls">
		<input id="" name="se" type="text" placeholder="" class="input-xlarge" value="'.$rs_site_email.'">
		</div>
		</div>
		<hr><h3>Payout Settings</h3><hr>
		<div class="col-lg-3">
		<div class="control-group">
		<label class="control-label" for="">Default Commission</label>
		<div class="controls">
		<input id="" name="dc" type="text" placeholder="" class="input-large" value="'.$default_commission.'">%
		</div>
		</div>
		<div class="control-group">
		<label class="control-label" for="">Min Required for Payout</label>
		<div class="controls">
		<input id="" name="mp" type="text" placeholder="" class="input-large" value="'.$min_payout.'">
		</div>
		</div>
		</div>
		<div class="col-lg-3">
		<div class="control-group">
		<label class="control-label" for="">Payout Options Available</label>
		<div class="controls">
		<input id="" name="paypal" type="checkbox" value="1" ';if($paypal=='1'){echo 'checked';}echo'><i class="fa-paypal"></i> PayPal<br>
		<input id="" name="stripe" type="checkbox" value="1" ';if($stripe=='1'){echo 'checked';}echo'><i class="fa-cc-stripe"></i> Stripe<br>
		<input id="" name="skrill" type="checkbox" value="1" ';if($skrill=='1'){echo 'checked';}echo'> Skrill<br>
		<input id="" name="wire" type="checkbox" value="1" ';if($wire=='1'){echo 'checked';}echo'> Wire Transfer<br>
		<input id="" name="checks" type="checkbox" value="1" ';if($checks=='1'){echo 'checked';}echo'> Check <br>
		</div>
		</div>
		</div>
		</div>
		<div class="control-group">
		<label class="control-label" for=""></label>
		<div class="controls">
		<button type="submit" class="btn btn-success">Save</button>
		</div>
		</div>
		</fieldset>';	
	}

function load_and_process($path, $arr = array()) {
    if(file_exists($path)) {
        ob_start();
        extract($arr);
        require($path);
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    }
    return false;
}