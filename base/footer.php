<?php include BASE_PATH . 'common-scripts.php'; ?>
<?php include BASE_PATH . 'includes/ga_footer_scripts.php';?>


<div id="returnStatus"></div>

<!--Footer Part-->
<footer>

    <!--Multi Awards-->
    <section class="multi-awards padded-sec">
        <div class="container wider-container">
            <div class="row align-items-center">
                <h2 class="section-title">Multi-Award Winning Courses...</h2>
                <div id="multiAwardsSlider" class="multiAwardsSlider owl-carousel">
                    <div class="award-item">
                        <img src="<?= SITE_URL ?>assets/images/natwest.png" alt="winner" />
                    </div>
                    <div class="award-item">
                        <img src="<?= SITE_URL ?>assets/images/sme.png" alt="winner" />
                    </div>
                    <div class="award-item">
                        <img src="<?= SITE_URL ?>assets/images/herts.png" alt="winner" />
                    </div>
                    <div class="award-item">
                        <img src="<?= SITE_URL ?>assets/images/best.png" alt="winner" />
                    </div>
                    <div class="award-item">
                        <img src="<?= SITE_URL ?>assets/images/global.png" alt="winner" />
                    </div>
                    <div class="award-item">
                        <img src="<?= SITE_URL ?>assets/images/small.png" alt="winner" />
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!--Validate Student-->
        <section class="validate-students" id="validateFooter">
            <div class="container wider-container">
                <div class="row align-items-center">
                    <div class="col-12 col-md-8 text-right">
                        Validate a student's qualification. Enter their certificate ID to begin.
                    </div>
                    <div class="col-12 col-md-4 text-left">
                        <div class="input-group mb-3">
                                <input type="text" class="form-control" name="cert" id="cert1" placeholder="Certificate ID" aria-label="Certificate ID" aria-describedby="basic-addon2">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" data-toggle="modal" data-target="#validateStudent" onclick="$('.validateStudent-boxes').css('display','none'); $('#validateFooterForm').css('display','block');"><i class="fas fa-check"></i></button>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    <section class="ab-preview">
        <a href="<?= SITE_URL ?>achievers">
            <img src="<?= SITE_URL ?>assets/images/abMobile.png" class="mobile" alt="achiever board" />
            <img src="<?= SITE_URL ?>assets/images/ab.png" class="desktop" alt="achiever board" />
        </a>
    </section>


    <!--Main Footer-->
    <section class="main-footer padded-sec">
        <div class="container wider-container">
            <div class="row footer-up">
                <div class="col-12 col-md-4 text-left">
                    <a href="<?= SITE_URL ?>">
                        <img src="<?= SITE_URL ?>assets/images/logo-white.png" alt="NSA Logo" class="footer-logo" />
                    </a>
                    <p>Part of the Be-a Education Ltd Family</p>
                    <p>Reg no: 08761384</p>
                    <p>Vat no: 382819269</p>
                    <p class="copy">&copy; New Skills Academy <?= date('Y') ?></p>
                    <p class="copy small">
                        All prices are in <?= $currency->code ?>.
                    </p>
                </div>
                <div class="col-12 col-md-4 text-center">
                    <span><img src="<?= SITE_URL ?>assets/images/trustpilot.png" alt="trust pilot" style="max-width:140px;" /></span>
                    <span><img src="<?= SITE_URL ?>assets/images/stars-image.png" alt="stars" style="width:190px;" /></span>
                    <a href="<?= SITE_URL ?>teens-unite">
                        <span class="teens-logo"><img src="<?= SITE_URL ?>assets/images/teens.png" alt="teens" /></span>
                    </a>
                </div>
                <div class="col-12 col-md-4">
                    <ul class="footer-right-menu">
                        <li><a href="<?= SITE_URL ?>terms-conditions-supply-products" rel="nofollow">Terms & Conditions of Supply of products</a></li>
                        <li><a href="<?= SITE_URL ?>terms-website-use" rel="nofollow">Terms of Website Use</a></li>
                        <li><a href="<?= SITE_URL ?>website-acceptable-use-policy" rel="nofollow">Website Acceptable Use Policy</a></li>
                        <li><a href="<?= SITE_URL ?>privacy-notice" rel="nofollow">Privacy Notice</a></li>
                        <li><a href="<?= SITE_URL ?>cookie-policy" rel="nofollow">Cookie Policy</a></li>
                        <li><a href="<?= SITE_URL ?>your-information" rel="nofollow">Your Data</a></li>
                        <li><a href="<?= SITE_URL ?>assets/cdn/Be-A Healthcare Modern Slavery Policy v1.0.docx.pdf" rel="nofollow">Modern Slavery Policy</a></li>
                    </ul>
                </div>
            </div>
            <div class="row footer-down align-items-center">
                <div class="col-12 col-md-12 col-lg-9">
                    <ul class="footer-menu">
                        <li><a href="<?= SITE_URL ?>about">About</a></li>
                        <li><a href="<?= SITE_URL ?>contact">Contact</a></li>
                        <li><a href="<?= SITE_URL ?>blog">Blog</a></li>
                        <?php if($currency->code == "GBP") { ?>
                        <li><a href="https://secure.tesco.com/clubcard/vouchers/new-skills-academy/UK-010027.prd" rel="noreferrer" target="_blank" rel="nofollow">Tesco Clubcard</a></li>
                        <?php } ?>
                        <li><a href="<?= SITE_URL ?>become-affiliate">Become an Affiliate</a></li>
                        <li><a href="<?= SITE_URL ?>testimonials">Testimonials</a></li>
                        <li><a href="<?= SITE_URL ?>achievers">Achiever Board</a></li>
                        <li><a href="<?= SITE_URL ?>gift">Gift Card</a></li>
                    </ul>
                </div>
                <div class="col-12 col-md-12 col-lg-3 social-icons">
                    <a href="<?= $this->getSetting("youtube"); ?>" rel="noreferrer" target="_blank" aria-label="Youtube"><i class="fab fa-youtube"></i></a>
                    <a href="<?= $this->getSetting("facebook"); ?>" rel="noreferrer" target="_blank" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="<?= $this->getSetting("twitter"); ?>" rel="noreferrer" target="_blank" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="<?= $this->getSetting("instagram"); ?>" rel="noreferrer" target="_blank" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>
    </section>


</footer>

<div class="modal fade basket" id="basket" tabindex="-1" role="dialog" aria-labelledby="basketTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div id="ajaxItems">
                <p class="text-center">
                    <i class="fa fa-spin fa-spinner"></i>
                </p>
            </div>
        </div>
    </div>
</div>

<div class="modal fade basket signIn" id="signIn" tabindex="-1" role="dialog" aria-labelledby="basketTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <a class="btn-close" data-dismiss="modal">X</a>
                <p class="popup-title text-center">Sign In</p>

                <p class="text-center signInNotice">
                    <i class="fa fa-globe"></i> Our students from all countries can now sign in from here.
                </p>

            </div>
            <div class="modal-body coupon">

                <?php
                if($this->get["voucher"] == "redeemed") {
                    $this->setAlertSuccess("Your voucher was redeemed. Please sign into your account to view your new course(s).");
                }
                ?>

                <form name="signIn">
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" id="passwordShow">

                        <label class="showPassword"><input type="checkbox" onclick="showPassword()"> Show Password</label>

                        <hr />

                        <script>
                            function showPassword() {
                                var x = document.getElementById("passwordShow");
                                if (x.type === "password") {
                                    x.type = "text";
                                } else {
                                    x.type = "password";
                                }

                            }
                        </script>
                    </div>

                    <p>
                        <input type="checkbox" name="remember" value="1" />
                        Remember Me
                    </p>

                    <div class="totals">
                        <button
                                type="submit"
                                class="btn btn-primary extra-radius">
                            Let's Go
                        </button>
                    </div>
                </form>

                <?php
                $this->renderFormAjax("account", "sign-in", "signIn");
                ?>

            </div>
            <div class="modal-body totals text-center">
                <a href="javascript:;" class="forgotToggle" data-dismiss="modal" data-toggle="modal" data-target="#forgot">
                    <i class="far fa-frown"></i>
                    I cannot sign in -
                </a>
                <a href="javascript:;" data-dismiss="modal" data-toggle="modal" data-target="#forgot" style="color:#000;text-decoration:underline;">Forgot Password?</a>
                <br />
                <a href="javascript:;" class="forgotToggle" data-dismiss="modal" data-toggle="modal" data-target="#forgot">
                    <i class="far fa-user-plus"></i>
                    I need an account -
                </a>
                <a href="javascript:;" data-dismiss="modal" data-toggle="modal" data-target="#newAccount" style="color:#000;text-decoration:underline;">Create Account</a>
            </div>
            <div class="modal-footer text-center">
                <a class="social-login-icons" href="<?= SITE_URL.'ajax?c=account&a=social_login&provider=facebook'?>">
                    <img src="<?= SITE_URL.'assets/images/fblogin.png' ?>">
                </a>
                <a class="social-login-icons" href="<?= SITE_URL.'ajax?c=account&a=social_login&provider=google'?>">
                    <img src="<?= SITE_URL.'assets/images/googlelogin.png' ?>">
                </a>
            </div>
        </div>
    </div>
</div>

<?php
if($this->get["voucher"] == "redeemed" || $this->get["signIn"] == "true") {
    ?>
    <script>
        $( document ).ready(function() {
            $("#signIn").modal("toggle");
        });
    </script>
    <?php
}
?>

<?php
if($this->get["curChange"] == "true") {
    ?>
    <div class="modal fade basket signIn" id="curChange" tabindex="-1" role="dialog" aria-labelledby="basketTitle" aria-hidden="true">
        <div class="modal-dialog" role="document" style="height:auto;">
            <div class="modal-content" style="border-bottom:0;">
                <div class="modal-body">
                    <a class="btn-close" data-dismiss="modal">X</a>
                    <p class="popup-title text-center" style="font-size:22px;">Currency updated to <?= $currency->code ?></p>

                    <p class="text-center signInNotice">
                        <i class="fa fa-info-circle" style="margin-right:3px;"></i> We are now displaying content relevant to your location, based on your selected currency.
                    </p>

                </div>
                <div class="modal-body" style="padding-top:0;">

                    <div class="totals">
                        <button type="button" data-dismiss="modal" class="btn btn-primary extra-radius" style="font-size: 17px;padding: 14px 9px;"><i class="fa fa-check" style="margin-right:5px;"></i> Great, continue browsing...</button>
                    </div>


                </div>
            </div>
        </div>
    </div>
    <script>
        $( document ).ready(function() {
            $("#curChange").modal("toggle");
        });
    </script>
    <?php
}
?>

<?php
if(CUR_ID_FRONT == "") {
    ?>
    <div class="modal fade basket signIn" id="newAccount" tabindex="-1" role="dialog" aria-labelledby="basketTitle" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <a class="btn-close" data-dismiss="modal">X</a>
                    <p class="popup-title text-center">Create Account</p>

                    <p class="text-center">
                        <a href="javascript:;" data-dismiss="modal" data-target="#signIn">I already have one, sign me in</a>
                    </p>

                </div>
                <div class="modal-body coupon">
                    <form name="newAccount">
                        <div class="form-group">
                            <label>Firstname</label>
                            <input type="text" name="firstname" class="form-control">
                        </div>

                        <div class="form-group">
                            <label>Lastname</label>
                            <input type="text" name="lastname" class="form-control">
                        </div>

                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" name="email" class="form-control">
                        </div>

                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control">

                        </div>

                        <input type="hidden" name="savedCourse" id="newAccountSavedCourse" value="" />


                        <div class="totals">
                            <button type="submit" class="btn btn-primary extra-radius">Let's Go</button>
                        </div>
                    </form>

                    <?php
                    $this->renderFormAjax("account", "new-account", "newAccount");
                    ?>

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade basket signIn" id="forgot" tabindex="-1" role="dialog" aria-labelledby="basketTitle" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <a class="btn-close" data-dismiss="modal">X</a>
                    <p class="popup-title text-center">Password Reset</p>

                    <p class="text-center signInNotice">
                        <i class="fa fa-info-circle"></i> If you can't sign into your account, we'll send you password reset instructions straight to your email address
                    </p>

                </div>
                <div class="modal-body coupon">
                    <form name="forgotPassword">
                        <div class="form-group">
                            <label>Your Email Address</label>
                            <input type="email" name="email" class="form-control">
                        </div>
                        <div class="totals">
                            <button type="submit" class="btn btn-primary extra-radius resetPwdBtn">Reset Password</button>
                        </div>
                    </form>
                    <?php
                    $this->renderFormAjax("account", "forgot-password", "forgotPassword");
                    ?>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade basket signIn" id="forgotSuccess" tabindex="-1" role="dialog" aria-labelledby="basketTitle" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <a class="btn-close" data-dismiss="modal">X</a>
                    <p class="popup-title text-center">Your Password Was Reset</p>

                </div>
                <div class="modal-body coupon">
                    Thank you. If that account exists, then password reset instructions are being sent to the email specified.
                </div>
            </div>
        </div>
    </div>
    <?php

    if(@$_SESSION['socialLogin']){
    ?>
        <div class="modal fade basket signIn" id="socialLoginEmail" tabindex="-1" role="dialog" aria-labelledby="basketTitle" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <a class="btn-close" data-dismiss="modal">X</a>
                        <p class="popup-title text-center">Sign In</p>

                        <p class="text-center signInNotice">
<!--                            <i class="fa fa-info-circle"></i> If you can't sign into your account, we'll send you password reset instructions straight to your email address-->
                        </p>

                    </div>
                    <div class="modal-body coupon">
                        <form name="providerLogin">
                            <div class="form-group">
                                <label>Your Email Address</label>
                                <input type="email" name="email" class="form-control">
                            </div>
                            <div class="totals">
                                <button type="submit" class="btn btn-primary extra-radius resetPwdBtn">Sign In</button>
                            </div>
                        </form>
                        <?php
                        $this->renderFormAjax("account", "provider-login", "providerLogin");
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <script>
            $( document ).ready(function() {
                $("#socialLoginEmail").modal("toggle");
            });
        </script>
        <?php
    }

}
?>


<?php
if($this->get["claim"] == "gift") {

    $item = ORM::for_table("orderItems")
            ->where("giftEmail", urldecode($this->get["email"]))
            ->where("giftToken", $this->get["token"])
            ->where("giftClaimed", "0")
            ->find_one();


    if($item->id != "") {

        $course = ORM::for_table("courses")->find_one($item->courseID);
        $courseGiftImg = ORM::For_table("media")->where("modelType", courseController::class)->where("modelId", $course->id)->find_one();

        ?>
        <div class="modal fade basket signIn" id="claimGift" tabindex="-1" role="dialog" aria-labelledby="basketTitle"
             aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <a class="btn-close" data-dismiss="modal">X</a>
                        <h1 class="text-center" style="margin-bottom: 0;font-size: 26px;padding-top: 11px;">Claim Gift</h1>

                    </div>
                    <div class="modal-body coupon">

                        <div class="cart-items d-flex align-items-center">
                            <div class="product-img"><img src="<?= $courseGiftImg->url ?>" /></div>
                            <div class="product-title align-self-md-center">
                                <p><strong>You're claiming: </strong><em><?= $course->title ?></em></p>
                                <p><strong><?= $this->price($item->price) ?></strong></p>
                            </div>
                        </div>

                        <hr />

                        <p style="font-size:18px;"><strong>I need an account...</strong></p>

                        <form name="createAccountGift">
                            <input type="hidden" name="token" value="<?= $this->get["token"] ?>" />
                            <input type="hidden" name="email" value="<?= urldecode($this->get["email"]) ?>" />
                            <div class="form-group">
                                <label>Firstname</label>
                                <input type="text" name="firstname" class="form-control" >
                            </div>

                            <div class="form-group">
                                <label>Lastname</label>
                                <input type="text" name="lastname" class="form-control" >
                            </div>

                            <div class="form-group">
                                <label>Email Address</label>
                                <input type="email" name="email" class="form-control" value="<?= urldecode($this->get["email"]) ?>">
                            </div>

                            <div class="form-group">
                                <label>Set Password</label>
                                <input type="password" name="password" class="form-control">
                            </div>

                            <div class="totals">
                                <button type="submit" class="btn btn-primary extra-radius">Create Account</button>
                            </div>
                        </form>
                        <?php
                        $this->renderFormAjax("account", "claim-gift-create-account", "createAccountGift");
                        ?>

                        <hr />

                        <p style="font-size:18px;"><strong>I have an account...</strong></p>

                        <form name="signInGift">
                            <input type="hidden" name="token" value="<?= $this->get["token"] ?>" />
                            <input type="hidden" name="email" value="<?= urldecode($this->get["email"]) ?>" />
                            <div class="form-group">
                                <label>Email Address</label>
                                <input type="email" name="email" class="form-control" value="<?= urldecode($this->get["email"]) ?>">
                            </div>

                            <div class="form-group">
                                <label>Password</label>
                                <input type="password" name="password" class="form-control">
                            </div>

                            <div class="totals">
                                <button type="submit" class="btn btn-primary extra-radius">Sign In</button>
                            </div>
                        </form>
                        <?php
                        $this->renderFormAjax("account", "claim-gift-sign-in", "signInGift");
                        ?>



                    </div>
                </div>
            </div>
        </div>

        <script>
            $( document ).ready(function() {
                $("#claimGift").modal("toggle");
            });
        </script>
        <?php
    }
}
?>

<div class="modal fade validateStudent" id="validateStudent" tabindex="-1" role="dialog" aria-labelledby="validateStudentTitle" aria-hidden="true">
    <div class="modal-dialog" role="document" style="    background: #259cc0;">
        <div class="modal-content">

            <div class="modal-body">
                <form name="validateFooter" id="validateFooterForm">
                    <h3><span>Important Message</span></h3>
                    <p>If a person has a valid certificate issued by us, this means only that they have taken our detailed on-line course and understood the material well enough to pass a final test by answering at least 70% of the test questions correctly.</p>
                    <p>We have not performed any additional tests or checks on such person (whether prior to or after issuing them with a certificate) including, without limitation, any identity, background, criminal record or any other security checks. It is your responsibility to undertake such tests or checks on such person as would be considered reasonably necessary, including, without limitation, enquiry as to their skills, expertise & experience, practical tests as to the quality of their work, and the obtaining of references in this regard.</p>
                    <p>While nothing in this disclaimer excludes or limits our liability for death or personal injury arising from our negligence, or our fraud or fraudulent misrepresentation, or any other liability that cannot be excluded or limited by English law, we will not be liable to any third party for any loss or damage, whether in contract, tort (including negligence), breach of statutory duty, or otherwise, even if foreseeable, arising under or in connection with any use of or reliance on any certificate issued by us.</p>
                    <div class="popular-overlay-btn">
                        <button type="submit" class="btn btn-outline-primary btn-lg extra-radius">I understand. Please validate now.</button>
                    </div>
                    <div class="popular-overlay-btn">
                        <button type="button" class="btn btn-outline-primary btn-lg extra-radius" data-dismiss="modal">I disagree. Please take me back.</button>
                    </div>
                    <input type="hidden" name="cert" id="cert2" value="" />
                    <script>
                        $('#cert1').on('keyup', function() {
                            $('#cert2').val($(this).val());
                        });
                    </script>
                </form>

                <div id="returnCert"></div>

                <?php
                $this->renderFormAjax("course", "validate-cert", "validateFooter", "#returnCert");
                ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php
if($hideNewsletterModal != true) {
?>
<div class="modal fade basket signIn" id="freeCourseLeave" tabindex="-1" role="dialog" aria-labelledby="basketTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <a class="btn-close" data-dismiss="modal">X</a>
                <p class="popup-title text-center">Have a FREE Course on us!</p>

            </div>
            <div class="modal-body coupon">

                <img src="<?= SITE_URL ?>assets/images/freeCourseLeave.png" alt="Free course" />
                <!--<img src="https://i.gyazo.com/79b7c986b7249fdbe0f871c20ac1daf5.png" alt="Free course" />-->

                <form name="freeCourseLeave">

                    <p class="subTitle">
                        Sign-up to our newsletter and receive a <u>free course</u>
                    </p>

                    <div class="form-group">
                        <input type="text" placeholder="First Name" name="firstname" class="form-control">
                    </div>

                    <div class="form-group">
                        <input type="email" placeholder="Email" name="email" class="form-control">
                    </div>

                    <div class="totals">
                        <button type="submit" class="btn btn-primary extra-radius">GET MY FREE COURSE</button>
                    </div>

                    <input type="hidden" name="freeCourseLeave" value="true" />
                </form>

                <?php
                $this->renderFormAjax("account", "join-newsletter", "freeCourseLeave")
                ?>

            </div>
        </div>
    </div>
</div>

<script>
    function setCookieFreeCourse(cname,cvalue,exdays) {
        var d = new Date();
        d.setTime(d.getTime() + (exdays*24*60*60*1000));
        var expires = "expires=" + d.toGMTString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    }

    function getCookieFreeCourse(cname) {
        var name = cname + "=";
        var decodedCookie = decodeURIComponent(document.cookie);
        var ca = decodedCookie.split(';');
        for(var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
                return c.substring(name.length, c.length);
            }
        }
        return "";
    }

    <?php if(!isset($request) || ($request != 'subscription')){?>
    $(document).bind("mouseleave", function(e) {
        if (e.pageY - $(window).scrollTop() <= 1) {
            if(getCookieFreeCourse("freeCourseLeave") != "shown") {
                $('#freeCourseLeave').modal("show");
            }
        }
    });
    <?php }?>

    $('#freeCourseLeave').on('hidden.bs.modal', function () {
        setCookieFreeCourse("freeCourseLeave", "shown", 30);
    });
</script>
<?php } ?>




<script>
    $(document).ready(function (){


        $(document).ready(function(){
            $('[data-toggle="tooltip"]').tooltip();
        });

        $(".close-menu").click(function (event) {
            //$(".navbar-toggler").click();
            $(".navbar-collapse").collapse('hide');
        });

        $('.multiAwardsSlider.owl-carousel').owlCarousel({
            loop:false,
            autoplay: true,
            margin:10,
            nav:true,
            responsive:{
                0:{
                    items:2
                },
                600:{
                    items:3
                },
                1000:{
                    items:6
                }
            }
        })
    });

    $(window).scroll(function() {
        var scroll = $(window).scrollTop();

        if (scroll >= 112) {
            $(".Header").addClass("fixed");
            $(".stickySpace").css("display", "block");
            $(".stickySpace").css("height", "112px");
            //$(".secondHeader").css("display", "block");
            $(".top-header").addClass('sticky');
            $(".breadcrumb-bar").css("display", "none");
        } else {
            $(".Header").removeClass("fixed");
            $(".stickySpace").css("display", "none");
            //$(".secondHeader").css("display", "none");
            $(".top-header").removeClass('sticky');
            $(".breadcrumb-bar").css("display", "block");
        }
    });

    function searchFloat() {

        $(".searchFloat").slideDown();
        $("#ajaxSearch").focus();
        $(".searchOverlay").css("display", "block");

    }

    function closeSearch() {

        $(".searchFloat").slideUp();
        $(".searchOverlay").css("display", "none");

    }
</script>


<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="<?= SITE_URL ?>assets/vendor/bootstrap/bootstrap.min.js"></script>

<script type="text/javascript">
    var SITE_URL = '<?= SITE_URL ?>';
</script>
<script src="<?= SITE_URL ?>assets/js/global.js?ver=4.2" type="text/javascript"></script>

<script type="text/javascript" src="https://s.skimresources.com/js/191807X1663369.skimlinks.js"></script>
<script src="//rum-static.pingdom.net/pa-60bf6085a406840011000183.js" async></script>

<?php
$_SESSION["currentPageUrl"] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
unset($_SESSION["loginErrorMessage"]);
$reachCodes = array("370", "369", "368");
if(in_array($_SESSION["refCodeInternal"], $reachCodes)) {
    // Reach tracking code
    ?>
    <!-- Quantcast Choice. Consent Manager Tag v2.0 (for TCF 2.0) -->
    <script type="text/javascript" async=true>
        (function() {
            var host = window.location.hostname;
            var element = document.createElement('script');
            var firstScript = document.getElementsByTagName('script')[0];
            var url = 'https://quantcast.mgr.consensu.org'
                .concat('/choice/', 'JYWDqeLS64fbt', '/', host, '/choice.js')
            var uspTries = 0;
            var uspTriesLimit = 3;
            element.async = true;
            element.type = 'text/javascript';
            element.src = url;

            firstScript.parentNode.insertBefore(element, firstScript);

            function makeStub() {
                var TCF_LOCATOR_NAME = '__tcfapiLocator';
                var queue = [];
                var win = window;
                var cmpFrame;

                function addFrame() {
                    var doc = win.document;
                    var otherCMP = !!(win.frames[TCF_LOCATOR_NAME]);

                    if (!otherCMP) {
                        if (doc.body) {
                            var iframe = doc.createElement('iframe');

                            iframe.style.cssText = 'display:none';
                            iframe.name = TCF_LOCATOR_NAME;
                            doc.body.appendChild(iframe);
                        } else {
                            setTimeout(addFrame, 5);
                        }
                    }
                    return !otherCMP;
                }

                function tcfAPIHandler() {
                    var gdprApplies;
                    var args = arguments;

                    if (!args.length) {
                        return queue;
                    } else if (args[0] === 'setGdprApplies') {
                        if (
                            args.length > 3 &&
                            args[2] === 2 &&
                            typeof args[3] === 'boolean'
                        ) {
                            gdprApplies = args[3];
                            if (typeof args[2] === 'function') {
                                args[2]('set', true);
                            }
                        }
                    } else if (args[0] === 'ping') {
                        var retr = {
                            gdprApplies: gdprApplies,
                            cmpLoaded: false,
                            cmpStatus: 'stub'
                        };

                        if (typeof args[2] === 'function') {
                            args[2](retr);
                        }
                    } else {
                        queue.push(args);
                    }
                }

                function postMessageEventHandler(event) {
                    var msgIsString = typeof event.data === 'string';
                    var json = {};

                    try {
                        if (msgIsString) {
                            json = JSON.parse(event.data);
                        } else {
                            json = event.data;
                        }
                    } catch (ignore) {}

                    var payload = json.__tcfapiCall;

                    if (payload) {
                        window.__tcfapi(
                            payload.command,
                            payload.version,
                            function(retValue, success) {
                                var returnMsg = {
                                    __tcfapiReturn: {
                                        returnValue: retValue,
                                        success: success,
                                        callId: payload.callId
                                    }
                                };
                                if (msgIsString) {
                                    returnMsg = JSON.stringify(returnMsg);
                                }
                                if (event && event.source && event.source.postMessage) {
                                    event.source.postMessage(returnMsg, '*');
                                }
                            },
                            payload.parameter
                        );
                    }
                }

                while (win) {
                    try {
                        if (win.frames[TCF_LOCATOR_NAME]) {
                            cmpFrame = win;
                            break;
                        }
                    } catch (ignore) {}

                    if (win === window.top) {
                        break;
                    }
                    win = win.parent;
                }
                if (!cmpFrame) {
                    addFrame();
                    win.__tcfapi = tcfAPIHandler;
                    win.addEventListener('message', postMessageEventHandler, false);
                }
            };

            makeStub();

            var uspStubFunction = function() {
                var arg = arguments;
                if (typeof window.__uspapi !== uspStubFunction) {
                    setTimeout(function() {
                        if (typeof window.__uspapi !== 'undefined') {
                            window.__uspapi.apply(window.__uspapi, arg);
                        }
                    }, 500);
                }
            };

            var checkIfUspIsReady = function() {
                uspTries++;
                if (window.__uspapi === uspStubFunction && uspTries < uspTriesLimit) {
                    console.warn('USP is not accessible');
                } else {
                    clearInterval(uspInterval);
                }
            };

            if (typeof window.__uspapi === 'undefined') {
                window.__uspapi = uspStubFunction;
                var uspInterval = setInterval(checkIfUspIsReady, 6000);
            }
        })();
    </script>
    <!-- End Quantcast Choice. Consent Manager Tag v2.0 (for TCF 2.0) -->

    <script>
        ! function() {
            var lotameClientId = '9790';
            var lotameTagInput = {
                data: {},
                config: {
                    clientId: Number(lotameClientId)
                }
            };

            // Lotame initialization
            var lotameConfig = lotameTagInput.config || {};
            var namespace = window['lotame_' + lotameConfig.clientId] = {};
            namespace.config = lotameConfig;
            namespace.data = lotameTagInput.data || {};
            namespace.cmd = namespace.cmd || [];
        } ();
    </script>

    <script async src="https://tags.crwdcntrl.net/lt/c/9790/lt.min.js"></script>

    <!-- Segment Pixel - Intent - New Skills Academy - All Users - DO NOT MODIFY -->
    <script src="https://secure.adnxs.com/seg?add=26796638&t=1" type="text/javascript"></script>
    <!-- End of Segment Pixel -->

    <?php
}
?>
<link rel="stylesheet" type="text/css" href="https://cdn.wpcc.io/lib/1.0.2/cookieconsent.min.css"/><script src="https://cdn.wpcc.io/lib/1.0.2/cookieconsent.min.js" defer></script><script>window.addEventListener("load", function(){window.wpcc.init({"border":"thin","corners":"small","colors":{"popup":{"background":"#f6f6f6","text":"#000000","border":"#555555"},"button":{"background":"#555555","text":"#ffffff"}}})});</script>
<script type="text/javascript" src="//static.klaviyo.com/onsite/js/klaviyo.js?company_id=WDrNnS" ></script>
<script type="application/javascript" async src="https://static.klaviyo.com/onsite/js/klaviyo.js?company_id=WDrNnS"></script>
<script src="https://www.dwin1.com/31125.js" type="text/javascript" defer="defer"></script>
<script src="https://www.google.com/recaptcha/api.js?render=<?= RECAPTCHA_SITE_KEY ?>"></script>
<script type="text/javascript">
    function parseRecaptchaResult(msg) {
        var msgToObj = JSON.parse(msg)
        if (!typeof msgToObj === 'object') {
            return false;
        }
        return msgToObj;
    }
    function validateRecaptchaResult(results) {
        if (!results.hasOwnProperty('success')) {
            showRecaptchaToast('Recaptcha error');
            return false;
        }
        if (results.success) {
            return true;
        }
        showRecaptchaToast('Recaptcha failed');
        return false;
    }

    function showRecaptchaToast(msg) {
        toastr.options.positionClass = "toast-bottom-left";
        toastr.options.closeDuration = 1000;
        toastr.options.timeOut = 5000;
        toastr.error(msg || 'Error', 'Oops')
    }

    function runFormAjax(formName, callback) {
        if (formName === 'newAccount') {
            grecaptcha.ready(function () {
                grecaptcha.execute('<?= RECAPTCHA_SITE_KEY ?>', {action: 'submit'})
                    .then(function (token) {
                        callback('recaptcha', token)
                    })
            })
        } else {
            callback()
        }
    }
</script>
</body>
</html>
