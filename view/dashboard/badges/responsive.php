<?php
$css = array("dashboard.css");
$pageTitle = "My Badges";
include BASE_PATH . 'account.header.php';
//echo "<pre>";
//print_r($certificateCoupon);
//die;

$badges = ORM::for_table("digitalBadges")->where("accountID", CUR_ID_FRONT)->find_many();
$socialText = 'I just earned my 1 years Unlimited Learning Membership Badge through New Skills Academy. Get yours by signing up at';
$socialUrl = 'https://newskillsacademy.co.uk/subscription';
?>
    <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
    <script src="https://platform.linkedin.com/in.js" type="text/javascript">lang: en_US</script>
    <!-- Load Facebook SDK for JavaScript -->
    <div id="fb-root"></div>
    <script>(function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) return;
            js = d.createElement(s); js.id = id;
            js.src = "https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v3.0";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));</script>

    <section class="page-title">
        <div class="container">
            <h1>
                <?= $pageTitle ?>
            </h1>
        </div>
    </section>

    <section class="page-content">
        <div class="container">

            <?php
            include BASE_PATH . 'subscribe-upsell.php'
            ?>

            <div class="row">

                <div class="col-12 regular-full">
                    <div class="row">
                        <div class="col-12 col-md-12 white-rounded notification certificate_lists">
                            <?php
                            if(count($badges) == 0){
                                ?>
                                <div class="alert alert-danger text-center p-4">
                                    <h5 class="m-0">You don't have any badges yet. You can earn a badge for every year you're an Unlimited Learning Member.<br />You'll soon be able to earn them for every course you complete as well.</h5>
                                </div>
                                <?php
                            }

                            foreach($badges as $badge) {
                                ?>
                                <div style="max-width:400px;margin:auto;width:100%;">
                                    <img src="<?= $badge->imgUrl ?>" style="width:100%;" />
                                    <p style="font-weight:bold;text-align:center;">Awarded on <?= date('jS M Y', strtotime($badge->whenIssued)) ?></p>

                                    <p style="text-align:center;">
                                        <a class="badge_download_link" href="<?= SITE_URL . 'dashboard/badges/download?img_url=' . $badge->imgUrl ?>">Click here to download</a>
                                    </p>

                                    <div class="mb-3 d-block text-center d-sm-flex justify-content-sm-center align-content-sm-end">
                                        <p class="mb-1 mb-sm-0 mr-2" style="font-weight:bold;text-align:center;">Share: </p>
                                        <div class="badge-fb-share-button fb-share-button mr-sm-1"
                                             data-href="<?= $socialUrl ?>"
                                             data-layout="button">
                                        </div>
                                        <div class=" mr-sm-1">
                                            <a href="https://twitter.com/share?ref_src=twsrc%5Etfw"
                                               data-url="<?= $socialUrl ?>" data-text="<?= $socialText ?>" class="twitter-share-button" data-show-count="false">
                                                Tweet
                                            </a>
                                        </div>
                                        <div class="badge-linkedin-share-button">
                                            <script type="IN/Share" data-url="<?= $socialUrl ?>"></script>
                                        </div>
                                    </div>
                                    <p style="text-align:center;font-size:12px;"><strong>What can I do with my badge?</strong> This digital badge follows the official standards of the Open Badges 2.0 specification. This means you can download this image (or copy its URL), and share it on other compatible websites such as your LinkedIn profile, or digital backpack websites like <a href="https://badgr.com/" target="_blank">badgr.com</a>. Yourself, potential employers, and others can check the authenticity of your badge at <a href="https://badgecheck.io/" target="_blank">badgecheck.io</a></p>

                                </div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

<?php include BASE_PATH . 'account.footer.php';?>