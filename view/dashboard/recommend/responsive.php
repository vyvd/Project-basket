<?php
$pageTitle = "Recommend Friends";
include BASE_PATH . 'account.header.php';

?>


    <section class="page-title">
        <div class="container">
            <h1>Recommend Friends</h1>
        </div>
    </section>

    <section class="page-content">
        <div class="container">
            <div class="row">
                <div class="col-12 regular-full pointsEarned recommend-friend">
                    <div class="row align-items-center">
                        <div class="col-12 col-md-4 text-center">
                            <img src="<?= SITE_URL ?>assets/user/images/recommend-friend.png" alt="recommend friend" />
                        </div>
                        <div class="col-12 col-md-8 text-center recommend-text">
                            <h1 class="text-uppercase">Tell your friends & save big on your next course</h1>
                            <p>Share your own personal link that's below with your friends, and on your social media. Whenever someone buys a course, we will give them an automatic 75% discount. Not only that, but we'll also give you a 75% discount code for every order made!</p>
                            <div class="link-box"><span>https://newskillsacademy.co.uk/refer/<?= $this->controller->rafLink(); ?></span></div>
                            <p class="refer-note">Applies to full price courses. Cannot be used in conjunction with any other offer. Excludes subscription and pay monthly products.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


<?php include BASE_PATH . 'account.footer.php';?>