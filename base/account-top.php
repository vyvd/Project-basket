<div class="container-fluid" id="site-header">
    <div class="container">
        <div class="row">
            <div class="col dash-header" id="site-header-container">
                <h1>My Account</h1>
                <div id="user-account">
                    <div class="user-icon" style="background-image: url('<?= SITE_URL ?>assets/cdn/profileImg/<?= $this->user->profileImg ?>')"></div>
                    <h4><?= $this->user->firstname.' '.$this->user->lastname ?></h4><br/>
                    <a href="<?= SITE_URL ?>profile"><?= $this->user->email ?> <i class="fas fa-pencil"></i></a>
                </div>
                <div id="notifications">
                    <a href="<?= SITE_URL ?>messages"><i class="far fa-envelope <?php if(ORM::for_table("messages")->where("recipientID", CUR_ID_FRONT)->where("seen", "0")->count() > 0) { ?>notify<?php } ?>"></i></a>
                    <a href="<?= SITE_URL ?>rewards"><i class="far fa-trophy-alt"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>