<?php
include_once '../auth/startup.php';
require '../data/data-functions.php';
$owner = $_POST['owner'];
$lp = $_POST['landing_page'];
ini_set('display_errors', true);
ini_set('display_startup_errors', true);
error_reporting(E_ALL);
//var_dump($lp, $owner);
$query_res = "SELECT `ip` FROM ap_referral_traffic WHERE affiliate_id = '$owner' AND landing_page LIKE '$lp%'";
$result_ips = $mysqli->query($query_res);
if($result_ips){
	$cols = 4;
	$num_results = mysqli_num_rows($result_ips);
	$end = $cols - 1;
	$i = 0;
?>
<!--<td colspan="4">-->
	<table style="width: 100%; font-size: 16px; text-align: left">
		<tbody>
			<?php
				while($row_ip = $result_ips->fetch_array()) {
					$ip = $row_ip['ip'];
					$country_flag = get_country_flag_by_ip($mysqli, $ip);
					//var_Dump($i % $cols);
					if ($i % $cols == 0) {
						echo '<tr>';
					}
					echo '<td style="border: 1px solid #000">'.$country_flag.' '.$ip.'</td>';
					if ($i % $cols == $end) {
						echo '</tr>';
					}
					$i++;
				}
			}
			?>
		</tbody>
	</table>
<!--</td>-->