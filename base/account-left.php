<ul>
    <li <?php if(REQUEST == "dashboard") { ?>class="active"<?php } ?>><a href="<?= SITE_URL ?>dashboard"><i class="far fa-home-lg"></i> Dashboard</a></li>
    <li <?php if(REQUEST == "dashboard/courses") { ?>class="active"<?php } ?>><a href="<?= SITE_URL ?>dashboard/courses"><i class="far fa-tv-alt"></i> My Courses</a></li>
    <li <?php if(REQUEST == "dashboard/courses/notes") { ?>class="active"<?php } ?>><a href="<?= SITE_URL ?>dashboard/courses/notes"><i class="far fa-sticky-note"></i> Course Notes</a></li>
    <li <?php if(REQUEST == "messages") { ?>class="active"<?php } ?>><a href="<?= SITE_URL ?>messages"><i class="far fa-envelope"></i> Messages (<?= ORM::for_table("messages")->where("recipientID", CUR_ID_FRONT)->where("seen", "0")->count() ?>)</a></li>
    <li <?php if(REQUEST == "rewards") { ?>class="active"<?php } ?>><a href="<?= SITE_URL ?>rewards"><i class="far fa-trophy-alt"></i> Rewards</a></li>
    <li <?php if(REQUEST == "profile") { ?>class="active"<?php } ?>><a href="<?= SITE_URL ?>profile"><i class="far fa-head-side"></i> Profile</a></li>
    <li <?php if(REQUEST == "dashboard/recommend") { ?>class="active"<?php } ?>><a href="<?= SITE_URL ?>dashboard/recommend"><i class="far fa-link"></i> Recommend Friends</a></li>
</ul>
<h4>Rewards Progress</h4>
<div class="reward-status">
    <i class="far fa-trophy-alt"></i> <div class="reward-100"><div class="reward-percent" style="width: 50%;"></div></div>
</div>
<p>So far you have earned 0/15 learning reward trophies.</p>
<a href="<?= SITE_URL ?>" class="white-button">Redeem</a>