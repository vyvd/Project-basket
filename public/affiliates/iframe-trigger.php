<?php
ob_start();
include_once $_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/access-functions.php';
require $_SERVER['DOCUMENT_ROOT'].'/affiliates/data/data-functions.php';
require(__DIR__ . '/../../wp-load.php');
global $wpdb;
if(!isset($_GET['columns']) && (!isset($_GET['products_ids'])) && (!isset($_GET['aff_id']))) {
	die('Issue Loading Iframe because one of the following elements are missing: product_ids, columns, aff_id');
}
$affiliate_filter = $_GET['aff_id'];
$protocol = 'https';
if($_SERVER['REMOTE_ADDR'] == '127.0.0.1') {
	$protocol = 'http';
}
?>
<!DOCTYPE html">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>Iframe Generator</title>
	<meta name="robots" content="noindex,nofollow" />
	<meta name="viewport" content="width=device-width">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<script type="text/javascript">
		//MDN PolyFil for IE8 (This is not needed if you use the jQuery version)
		if (!Array.prototype.forEach){
			Array.prototype.forEach = function(fun /*, thisArg */){
			"use strict";
			if (this === void 0 || this === null || typeof fun !== "function") throw new TypeError();
			
			var
			t = Object(this),
			len = t.length >>> 0,
			thisArg = arguments.length >= 2 ? arguments[1] : void 0;

			for (var i = 0; i < len; i++)
			if (i in t)
				fun.call(thisArg, t[i], i, t);
			};
		}
	</script>
</head>
<body>
	<iframe allowtransparency="true" frameborder="0" width="100%" style="border:none; max-width: 1150px" scrolling="no" src="<?php echo site_url('', $protocol).'/affiliates/iframe.php?aff_id='.$affiliate_filter ?>"></iframe>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
	<script type="text/javascript" src="<?php echo site_url('', $protocol) ?>/affiliates/assets/js/iframeResizer.min.js"></script> 
	<p id="callback"></p>
	<script type="text/javascript">
		iFrameResize({
			log                     : true,                  // Enable console logging
			enablePublicMethods     : true,                 // Enable methods within iframe hosted page
			heightCalculationMethod : 'lowestElement',
			resizedCallback         : function(messageData){ // Callback fn when resize is received
				$('p#callback').html(
					'<b>Frame ID:</b> '    + messageData.iframe.id +
					' <b>Height:</b> '     + messageData.height +
					' <b>Width:</b> '      + messageData.width + 
					' <b>Event type:</b> ' + messageData.type
				);
			},
			messageCallback         : function(messageData){ // Callback fn when message is received
				$('p#callback').html(
					'<b>Frame ID:</b> '    + messageData.iframe.id +
					' <b>Message:</b> '    + messageData.message
				);
				alert(messageData.message);
			},
			closedCallback         : function(id){ // Callback fn when iFrame is closed
				$('p#callback').html(
					'<b>IFrame (</b>'    + id +
					'<b>) removed from page.</b>'
				);
			}
		});
	</script>
</body>
</html>