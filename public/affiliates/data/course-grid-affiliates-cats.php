<div class="post_item post_item_courses post_item_courses_3 post_format_standard odd">
	<div class="course-preview -course post_content ih-item colored square effect_dir left_to_right">
		<div class="course-image post_featured img">
			<a target="_blank" href="<?php echo $course->url; ?>?ref=<?php echo $affiliate_filter ?>">
                <?php //echo get_the_post_thumbnail($the_id, array(400, 200), array('class' => 'attachment-homepage-thumb')); ?>
                <img class="attachment-homepage-thumb" src="<?php echo $course->imageUrl; ?>">
            </a>
		</div>
		<div class="course-meta">
			<header class="course-header">
				<h5 class="nomargin"><?php echo $course->title; ?></h5>
			</header>
		</div>
		<?php 
			if($coupon_data !== '') {  ?>
		<div class="discount-text-iframe">

				<?php
					if(strpos($coupon_data, 'fixed_price') !== false) {
						$coupon_data = str_replace('fixed_price', 'fixedprice', $coupon_data);
					}

					if(isset($_GET['iframe_test55'])) {
					    var_dump($coupon_data);
                    }

					$coupon_data = explode('_', $coupon_data);



                    $coupon = $coupon_data[0];
					$discount = $coupon_data[1];
					$coupon_type = $coupon_data[2];
//					$post_id = $the_id;
//					$product_id = get_post_meta($post_id, '_course_product', true);
//					$product = wc_get_product($product_id);

				    $was_price = $course->price;
                    $locale = 'en-GB';
//					if(isset($_SESSION['locale'])){
//						$locale = $_SESSION['locale'];
//					} else {
//						$mysqli = new mysqli(HOST, USER, PASSWORD, DATABASE);
//						$user_country = get_country_by_ip($_SERVER['REMOTE_ADDR'], $mysqli);
//						$locale = 'en-Us';
//						if ($user_country == 1) {
//							$locale = 'en-GB';
//						}
//					}

					$money_format = new \NumberFormatter($locale, \NumberFormatter::CURRENCY); 
					$currency_symbol = $money_format->getSymbol(\NumberFormatter::INTL_CURRENCY_SYMBOL);

					//var_Dump($coupon_type);
					if($coupon_type !== 'percentage') {
						if($coupon_type == 'fixedprice') {
							$now_only = 0;
							switch($was_price) {
								case 99:
									$now_only = 15;
									break;
								case 199:
									$now_only = 20;
									break;
								case 299:
									$now_only = 24;
									break;
								case ($was_price >= 399):
								//var_Dump(123);
									$now_only = 60;
									break;
							 }
							 $discount = $was_price - $now_only;
						} else {
							$now_only = $was_price - $discount;
							if($now_only < 1) {
								$now_only = 0;
							}
						}
					} else {
						$now_only = $was_price - ($discount * $was_price) / 100;
					}
				?>

				was <?php echo $money_format->formatCurrency($was_price,  $currency_symbol); ?>
				<br>
				<br>
				<strong style="font-size: 18px">
					<?php if($now_only > 0) { ?>
					Now Only <?php echo $money_format->formatCurrency($now_only,  $currency_symbol);?>
					<?php } else { ?>
					It's FREE
					<?php }  ?>
				</strong>
				<br><br>
				<?php
					if($coupon_type == 'percentage') { 
						echo $discount.'%';
					} else {
						echo $money_format->formatCurrency($discount,  $currency_symbol);
					} 
					?>
					Discount
					<br>
					Use Code: <?php echo strtoupper($coupon) ?>
		</div>
		<?php } ?>
		<section class="find-more-now">
			<a target="_blank" class="find-out-more" href="<?php echo $course->url; ?>?ref=<?php echo $affiliate_filter ?>">FIND OUT MORE</a>
		</section>
	</div>
</div>