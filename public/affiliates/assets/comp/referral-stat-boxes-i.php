<?php $cpc_on = cpc_on();
if($cpc_on=='1'){$col='4';}else{$col='6';}?>
<div class="row">
    <div class="col-lg-<?php echo $col;?> col-md-<?php echo $col;?> col-sm-6 col-xs-12">
		<div class="stat-box">
			<a href="#">
				<div class="stat-icon stat-default hvr-bounce-in">
					<i class="fa-rocket"></i>
				</div>
				<div class="stat-data">
					<h2><span id="total_referrals_period"><?php //echo total_referrals_period_i($start_date, $end_date, $owner);?></span> <span class="stat-info"><?php echo $lang['VISITORS'];?> <span class="small-text">(selected period)</span></span></h2>
				</div>
			</a>
		</div>
	</div>
	<?php if($cpc_on=='1'){ ?>
	<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
		<div class="stat-box">
			<a href="my-traffic">
				<div class="stat-icon stat-default hvr-bounce-in">
					<i class="fa-money"></i>
				</div>
				<div class="stat-data">
					<h2><span id="my_total_cpc_earnings"><?php //echo my_total_cpc_earnings($owner);?></span> <span class="stat-info"><?php echo $lang['CPC_EARNINGS'];?></span></h2>
				</div>
			</a>
		</div>
	</div>
	<?php } ?>
	<div class="col-lg-<?php echo $col;?> col-md-<?php echo $col;?> col-sm-6 col-xs-12">
	<div class="stat-box">
		<a href="#">
			<div class="stat-icon stat-success hvr-bounce-in">
				<i class="fa-ok-circled2"></i>
			</div>
			<div class="stat-data">
				<h2><span id="my_conversion_period"><?php //echo my_conversion_period_i($start_date, $end_date, $owner);?></span> <span class="stat-info"><?php echo $lang['CONVERSION_RATE'];?> <span class="small-text">(selected period)</span></span></h2>
			</div>
		</a>
	</div>
</div>
</div>