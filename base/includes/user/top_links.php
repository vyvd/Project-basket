<ul class="navbar-nav align-items-center  ml-auto ml-md-0 ">
    <li class="nav-item dropdown">
        <a class="nav-link pr-0" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <div class="media align-items-center">
                <span class="profileName">
                    <?= $this->user->firstname ?>
                </span>

                                  <span class="avatar avatar-sm ">
                                    <img alt="Image placeholder" class="rounded-circle" src="<?= SITE_URL ?>assets/cdn/profileImg/<?= $this->user->profileImg ?>" style="width:55px;height:55px;">
                                  </span>
            </div>
        </a>
        <div class="dropdown-menu dropdown-menu-right">
            <a href="<?= SITE_URL ?>profile" class="dropdown-item">
                <i class="ni ni-single-02"></i>
                <span>My profile</span>
            </a>
            <div class="dropdown-divider"></div>
            <a href="<?= SITE_URL ?>dashboard/billing" class="dropdown-item">
                <i class="ni ni-single-02"></i>
                <span>Billing</span>
            </a>
            <div class="dropdown-divider"></div>
            <a href="<?= SITE_URL ?>ajax?c=account&a=sign-out" class="dropdown-item">
                <i class="ni ni-user-run"></i>
                <span>Logout</span>
            </a>
        </div>
    </li>
    <?php
    $account = ORM::for_table("accounts")->select("balance")->find_one(CUR_ID_FRONT);

    if($isActiveSubscription) {
        ?>
        <li class="nav-item" style="position:relative;top:-2px;">
            <a href="<?= SITE_URL ?>dashboard/premium" class="premium">
                <i class="fas fa-star"></i>
                Premium
            </a>
        </li>
        <?php
    }

    if($account->balance != "0.00") {
        ?>
        <li class="nav-item" style="position:relative;top:-2px;">
            <a href="<?= SITE_URL ?>dashboard/billing/balance" class="premium">
                <i class="fas fa-pound-sign"></i>
                <?= number_format($account->balance, 2) ?>
            </a>
        </li>
        <?php
    }
    ?>
</ul>