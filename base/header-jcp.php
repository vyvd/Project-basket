<?php
/* affiliate tracking and discounting */
include(TO_PATH.'/affiliates/controller/affiliate-tracking.php');

if($_GET["ref"] != "" && $_SESSION["refCodeInternal"] != $_GET["ref"]) {

    $_SESSION["refCodeInternal"] = $_GET["ref"];

    $affVoucher = ORM::For_table("ap_affiliate_voucher")->where("aff_id", $_GET["ref"])->find_one();

    if($affVoucher != "") {

        $_SESSION["affiliateDiscount"] = $affVoucher->voucher_value;
        $_SESSION["affiliateDiscountType"] = $affVoucher->comission_type;

        $affCoupon = ORM::for_table("coupons")->where("code", $affVoucher->voucher_code)->find_one();

        if($affCoupon->id != "") {

            if($affCoupon->valueMax != "") {
                $_SESSION["affiliateDiscountMax"] = $affCoupon->valueMax;
            } else {
                $_SESSION["affiliateDiscountMax"] = "";
            }

            if($affCoupon->valueMin != "") {
                $_SESSION["affiliateDiscountMin"] = $affCoupon->valueMin;
            } else {
                $_SESSION["affiliateDiscountMin"] = "";
            }

            $_SESSION["excludedCourses"] = $affCoupon->excludeCourses;

        }


        $getString = '';

        foreach($_GET as $key => $value) {
            if($key != "ref" && $key != "request") {
                $getString .= '&'.$key.'='.$value;
            }
        }

        header('Location: '.SITE_URL.REQUEST.'?ref='.$_GET["ref"].$getString);
        exit;
    }

}


$should_exclude_GA = false;
$is_profile_page = false;
$is_staging_site = in_array(SITE_ENVIRONMENT, array('staging', 'stage')) ? true : false;

/*
$is_profile_page = is_author();
$endpoint = WC()->query->get_current_endpoint();
//var_dump($endpoint);

if( $is_profile_page || $endpoint == 'lost-password' ) {
    $should_exclude_GA = true;
}
*/

?>
<!doctype html>
    <html lang="en">
    <head>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <?php if($should_exclude_GA || $is_staging_site): ?>
            <meta name="robots" content="noindex, nofollow">
            <meta name="googlebot" content="noindex, nofollow">
        <?php else: ?>
            <meta name="robots" content="index, follow">
        <?php endif; ?>


        <title><?= $pageTitle ?> | <?= SITE_NAME ?></title>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/core-js/3.15.0/minified.js" integrity="sha512-Z/VQ/Kzx+AXxCyMLlA5UGJD2dcKAiVEaDa1rYwO5up8lv3DLy7U9n3LCpz/s1O/SydrXki2Ia5U7Ujwm5fINew==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script src="<?= SITE_URL ?>assets/js/jquery.min.js"></script>

        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?family=Catamaran:wght@100;200;300;400;500;600;700&display=swap" rel="stylesheet">


        <script>

            let site_type = '<?php echo SITE_TYPE; ?>';
            let domain_name = '<?php echo DOMAIN_NAME; ?>';
            let current_user_id = '<?php echo CUR_ID_FRONT; ?>';
            let current_user_email = '<?php echo CUR_EMAIL_FRONT; ?>';
            let current_user_email_hased = '<?php echo hash("sha256", CUR_EMAIL_FRONT); ?>';

            window.SITE_TYPE = site_type == '' ? null : site_type;
            window.DOMAIN_NAME = domain_name == '' ? null : domain_name;
            window.CUR_ID_FRONT = current_user_id == '' ? null : current_user_id;
            window.CUR_EMAIL_FRONT = current_user_email == '' ? null : current_user_email;
            window.CUR_EMAIL_FRONT_HASHED = current_user_email == '' ? null : current_user_email_hased;


            // AWIN Tracking
            var iCookieLength = 30; // Cookie length in days
            var sCookieName = "Source"; // Name of the first party cookie to utilise for last click referrer de-duplication
            var sSourceParameterName = ["utm_source","gclid","fbclid","source"]; // The parameter used by networks and other marketing channels to tell you who drove the traffic
            var domain = domain_name ?? ''; // Top level domain

            var _getQueryStringValue = function (sParameterName) {
                var aQueryStringPairs = document.location.search.substring(1).split("&");
                for (var i = 0; i < aQueryStringPairs.length; i++) {
                    var aQueryStringParts = aQueryStringPairs[i].split("=");
                    if (sParameterName.includes(aQueryStringParts[0].toLowerCase())) {
                        if(aQueryStringParts[0].toLowerCase()=="utm_source"){
                                return aQueryStringParts[1].toLowerCase();
                        }
                        else if(sSourceParameterName.includes(aQueryStringParts[0].toLowerCase())){
                                return aQueryStringParts[0].toLowerCase();
                        }
                    }
                }
            };
            
            var _setCookie = function (sCookieName, sCookieContents, iCookieLength, tlDomain) {
                var dCookieExpires = new Date();
                dCookieExpires.setTime(dCookieExpires.getTime() + (iCookieLength * 24 * 60 * 60 * 1000));
                document.cookie = sCookieName + "=" + sCookieContents + "; expires=" + dCookieExpires.toGMTString() +";domain=" + tlDomain + "; path=/;";
            };
            
            if (_getQueryStringValue(sSourceParameterName)) {
                _setCookie(sCookieName, _getQueryStringValue(sSourceParameterName), iCookieLength, domain);
            }

        </script>

        <!-- Facebook Pixel Code -->
        <script>
            !function(f,b,e,v,n,t,s)
            {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
                n.callMethod.apply(n,arguments):n.queue.push(arguments)};
                if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
                n.queue=[];t=b.createElement(e);t.async=!0;
                t.src=v;s=b.getElementsByTagName(e)[0];
                s.parentNode.insertBefore(t,s)}(window, document,'script',
                'https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', '<?= FB_BUSINESS_PIXEL_ID ?>');
            fbq('track', 'PageView');
        </script>

        <noscript><img height="1" width="1" style="display:none"
                       src="https://www.facebook.com/tr?id=<?= FB_BUSINESS_PIXEL_ID ?>&ev=PageView&noscript=1"
            /></noscript>
        <!-- End Facebook Pixel Code --><noscript><img height="1" width="1" style="display: none;" src="https://www.facebook.com/tr?id=<?= FB_BUSINESS_PIXEL_ID ?>&ev=PageView&noscript=1&eid" alt="facebook_pixel"></noscript>

        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="<?= SITE_URL ?>assets/vendor/bootstrap/bootstrap.min.css" >
        <!-- Css Style -->
        <link rel="stylesheet" href="<?= SITE_URL ?>assets/css/theme.css">
        <?php
        foreach($css as $item) {
            ?>
            <link rel="stylesheet" href="<?= SITE_URL ?>assets/css/<?= $item ?>">
            <?php
        }

        foreach($js as $item) {
            ?>
            <script src="<?= SITE_URL ?>assets/js/<?= $item ?>"></script>
            <?php
        }
        ?>
        <link rel="stylesheet" href="<?= SITE_URL ?>assets/css/media.css">

        <!--Favicon-->
        <link rel="icon" type="image/png" href="<?= SITE_URL ?>assets/images/favicon.png" />

        <meta name="description" content="<?= $metaDesc ?>">
        <meta name="keywords" content="<?= $metaTags ?>">
        <meta property="og:locale" content="en_US" />
        <meta property="og:type" content="website" />
        <meta property="og:title" content="<?= $pageTitle ?> | <?= SITE_NAME ?>" />
        <meta property="og:description" content="<?= $metaDesc ?>" />
        <meta property="og:url" content="<?= SITE_URL.REQUEST ?>" />
        <meta property="og:site_name" content="<?= SITE_NAME ?>" />
        <script type='application/ld+json'>{"@context":"http:\/\/schema.org","@type":"WebSite","@id":"#website","url":"https:\/\/newskillsacademy.co.uk\/","name":"New Skills Academy","potentialAction":{"@type":"SearchAction","target":"https:\/\/newskillsacademy.co.uk\/search?search={search_term_string}","query-input":"required name=search_term_string"}}</script>

        <script type="application/ld+json">
        {
          "@context" : "http://schema.org",
          "@type" : "Organization",
          "name" : "<?= SITE_NAME ?>",
          "url" : "<?= SITE_URL ?>",
          "sameAs" : [
            "https://www.facebook.com/newskillsacademyUK/",
            "https://twitter.com/NewSkillsAcad",
            "https://www.instagram.com/newskillsacademy/",
            "https://www.youtube.com/channel/UCaAEqYg-mA-3obmB5Z18Xhw"
          ]
        }
        </script>

        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.11.2/css/all.css" integrity="sha384-zrnmn8R8KkWl12rAZFt4yKjxplaDaT7/EUkKm7AovijfrQItFWR7O/JJn4DAa/gx" crossorigin="anonymous">

        <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
        <link href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" rel="stylesheet">

        <style>
            .form-error {
                border: 1px solid #c91b1b !important;
            }
        </style>

        <link rel="stylesheet" href="<?= SITE_URL ?>assets/vendor/owlCarousel/assets/owl.carousel.min.css">
        <link rel="stylesheet" href="<?= SITE_URL ?>assets/vendor/owlCarousel/assets/owl.theme.default.min.css">
        <script src="<?= SITE_URL ?>assets/vendor/owlCarousel/owl.carousel.js"></script>

        <link rel="canonical" href="<?= SITE_URL.REQUEST ?>" />

        <?php if(!empty(CUR_ID_FRONT) && !$should_exclude_GA): ?>

            <script>
                window.dataLayer = window.dataLayer || [];
                window.dataLayer.push({
                    'user_id' : '<?php echo CUR_ID_FRONT; ?>',
                    'user_email' : '<?php echo CUR_EMAIL_FRONT; ?>'
                });
            </script>

        <?php endif; ?>

        <?php if(!$should_exclude_GA): ?>

            <!-- Global site tag (gtag.js) - Google Analytics -->
            <script async src="https://www.googletagmanager.com/gtag/js?id=UA-51304979-13"></script>

        <?php if(!empty(CUR_ID_FRONT)): ?>

            <script>
                window.dataLayer = window.dataLayer || [];
                function gtag(){dataLayer.push(arguments);}
                gtag('js', new Date());
                gtag('config', '<?= GA_KEY_1 ?>',{'user_id': '<?php echo CUR_ID_FRONT; ?>', 'user_email' : '<?php echo CUR_EMAIL_FRONT; ?>'});
                gtag('config', '<?= GA_KEY_2 ?>');
            </script>

        <?php else: ?>

            <script>
                window.dataLayer = window.dataLayer || [];
                function gtag(){dataLayer.push(arguments);}
                gtag('js', new Date());
                gtag('config', '<?= GA_KEY_1 ?>');
                gtag('config', '<?= GA_KEY_2 ?>');
            </script>

        <?php endif; ?>


            <!-- GTM snippet to go here-->

            <!-- Google Tag Manager -->

            <script>
                (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
                    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
                    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
                })(window,document,'script','dataLayer','<?= GTM_KEY_1 ?>');
            </script>

            <!-- End Google Tag Manager -->

        <?php endif; ?>


        <!-- Pinterest Pixel Base Code -->
<!--        <script type="text/javascript">-->
<!--            !function(e){if(!window.pintrk){window.pintrk=function(){window.pintrk.queue.push(-->
<!--                Array.prototype.slice.call(arguments))};var-->
<!--                n=window.pintrk;n.queue=[],n.version="3.0";var-->
<!--                t=document.createElement("script");t.async=!0,t.src=e;var-->
<!--                r=document.getElementsByTagName("script")[0];r.parentNode.insertBefore(t,r)}}("https://s.pinimg.com/ct/core.js");-->
<!--            pintrk('load', '2612410700005');-->
<!--            pintrk('page');-->
<!--        </script>-->
<!--        <noscript>-->
<!--            <img height="1" width="1" style="display:none;" alt="" src="https://ct.pinterest.com/v3/?tid=2612410700005&event=init&noscript=1" />-->
<!--        </noscript>-->
        <!-- End Pinterest Pixel Base Code -->

        <script data-obct type = "text/javascript">
            /** DO NOT MODIFY THIS CODE**/
            !function(_window, _document) {
                var OB_ADV_ID = '00862bd4c85dd1cd3ca53df38745430549';
                if (_window.obApi) {
                    var toArray = function(object) {
                        return Object.prototype.toString.call(object) === '[object Array]' ? object : [object];
                    };
                    _window.obApi.marketerId = toArray(_window.obApi.marketerId).concat(toArray(OB_ADV_ID));
                    return;
                }
                var api = _window.obApi = function() {
                    api.dispatch ? api.dispatch.apply(api, arguments) : api.queue.push(arguments);
                };
                api.version = '1.1';
                api.loaded = true;
                api.marketerId = OB_ADV_ID;
                api.queue = [];
                var tag = _document.createElement('script');
                tag.async = true;
                tag.src = '//amplify.outbrain.com/cp/obtp.js';
                tag.type = 'text/javascript';
                var script = _document.getElementsByTagName('script')[0];
                script.parentNode.insertBefore(tag, script);
            }(window, document);
            obApi('track', 'PAGE_VIEW');
        </script>


        <!-- Taboola Pixel Code -->
        <script type='text/javascript'>
            window._tfa = window._tfa || [];
            window._tfa.push({notify: 'event', name: 'page_view', id: 1289858});
            !function (t, f, a, x) {
                if (!document.getElementById(x)) {
                    t.async = 1;t.src = a;t.id=x;f.parentNode.insertBefore(t, f);
                }
            }(document.createElement('script'),
                document.getElementsByTagName('script')[0],
                '//cdn.taboola.com/libtrc/unip/1289858/tfa.js',
                'tb_tfa_script');
        </script>
        <noscript>
            <img src='https://trc.taboola.com/1289858/log/3/unip?en=page_view'
                 width='0' height='0' style='display:none'/>
        </noscript>
        <!-- End of Taboola Pixel Code -->


    </head>
    <body class="jcp-page">

    <?php if(!$should_exclude_GA): ?>
        <!-- Google Tag Manager (noscript) -->
        <noscript>
            <iframe src="https://www.googletagmanager.com/ns.html?id=<?= GTM_KEY_1 ?>"
                    height="0" width="0" style="display:none;visibility:hidden"></iframe>
        </noscript>
        <!-- End Google Tag Manager (noscript) -->
    <?php endif; ?>

    <?php
    if(ADMIN_ACCESSED == true) {
        ?>
        <div style="position:fixed;top:0;left:0;width:100%;padding:5px 15px;color:#fff;z-index:9999999;background: #08586c;font-size:17px;">
            Accessed via Admin
            <a href="<?= SITE_URL ?>ajax?c=blumeNew&a=exit-admin-access&id=<?= CUR_ID_FRONT ?>" style="    color: #fff;
    background: #17a9ce;
    float: right;
    padding: 0px 15px;
    border-radius: 5px;">
                Exit
            </a>
        </div>
        <div style="height:35px;"></div>
        <?php
    }
    ?>
    <?php
    if(REQUEST == "" || REQUEST == "teens-unite") {
        ?>
        <?php
    }
    ?>
    <!--Header Part-->
    <header class="<?php if(REQUEST == "" || REQUEST == "teens-unite") { ?>home<?php } ?>Header">
        <div class="top-header">
            <div class="container wider-container">
                <a class="navbar-brand" href="<?= SITE_URL ?>">
                    <img class="logo" src="<?= SITE_URL ?>assets/images/logo-<?php if(REQUEST == "" || REQUEST == "teens-unite") { ?>white<?php } else { ?>blue<?php } ?>.png" alt="New Skills Academy" />
                    <img class="logo sticky" src="<?= SITE_URL ?>assets/images/logo-blue.png" alt="New Skills Academy" />
                </a>
                <a class="navbar-jcp" href="<?= SITE_URL ?>">
                    <img src="<?= SITE_URL ?>assets/images/JCP-logo-new.png" alt="">
                </a>


            </div>
        </div>

        <?php
        if(REQUEST == "") {

            $courseCount = ceil((ORM::for_table("courses")->count()-5) / 10) * 10;
            $studentCount = ORM::for_table("accounts")->count()+244000;
            $studentCount = round ($studentCount, -3);
            ?>
            <div class="container home-banner-section wider-container">
                <div class="home-banner">
                </div>
                <div class="row text-center">
                    <h2 class="home-banner-title">Change Your Life With A New Skill</h2>
                    <h3 class="home-banner-subtitle">Join Over <?= number_format($studentCount) ?> students and study one of our <br/> <?= $courseCount ?>+ career enhancing, confidence boosting courses</h3>
                    <div class="col-12 search-box">
                        <form action="<?= SITE_URL ?>search" method="get" id="homeSearch">
                            <input type="text" name="search" placeholder="Find your perfect course..." class="search-field">
                            <a class="search-button" href="javascript:;" onclick="$('#homeSearch').submit();"><img src="<?= SITE_URL ?>assets/images/sarch-icon.png" alt="Search" /></a>
                        </form>
                    </div>
                    <div class="col-12 banner-buttons">
                        <a href="<?= SITE_URL ?>courses">See All Courses</a>
                        <a href="<?= SITE_URL ?>redeem">Redeem a Voucher</a>
                        <a href="javascript:;" onclick="scrollValidate();">Validate a Qualification</a>
                    </div>

                    <?php
                    if($_SESSION["refCodeInternal"] == "330") {
                        ?>
                        <style>
                            .refLogoClubcard {
                                background: #259cc0;
                                padding: 16px;
                                border-radius: 10px;
                                margin: auto;
                                margin-top: 34px;
                            }
                        </style>
                        <img src="<?= SITE_URL ?>assets/images/tescoClubcard.png" alt="Tesco" class="refLogoClubcard" />
                        <?php
                    }
                    ?>

                    <script>
                        function scrollValidate() {

                            $([document.documentElement, document.body]).animate({
                                scrollTop: $("#validateFooter").offset().top-100
                            }, 1000);

                        }
                    </script>

                    <div class="col-12 reviews-home">
                        <?php
                        if($this->isMobileDevice()){
                            ?>
                            <div class="trustpilot-widget" data-locale="en-GB" data-template-id="539ad0ffdec7e10e686debd7" data-businessunit-id="5b450fa2ad92290001bfac20" data-style-height="318px" data-style-width="100%" data-theme="light" data-stars="4,5">
                                https://uk.trustpilot.com/review/newskillsacademy.co.uk
                            </div>
                            <script type='text/javascript' src='//widget.trustpilot.com/bootstrap/v5/tp.widget.bootstrap.min.js?ver=5.4'></script>
                            <?php
                        }
                        else {
                            ?>
                            <script type='text/javascript' src='//widget.trustpilot.com/bootstrap/v5/tp.widget.bootstrap.min.js?ver=5.4'></script>
                            <!-- TrustBox widget - Carousel -->
                            <div class="trustpilot-widget" data-locale="en-GB" data-template-id="53aa8912dec7e10d38f59f36" data-businessunit-id="5b450fa2ad92290001bfac20" data-style-height="160px" data-style-width="100%" data-theme="dark" data-stars="5" data-schema-type="Organization">
                            </div>
                            <?php
                        }
                        ?>

                    </div>
                </div>
            </div>
            <?php
        }
        ?>
        <?php
        if(REQUEST == "teens-unite") {
            ?>
            <div class="container home-banner-section wider-container">
                <div class="teens-banner">
                </div>
                <div class="row text-center">
                    <div class="col-12 teens-main-logo">
                        <img src="<?= SITE_URL ?>assets/images/teens-logo.png" alt="Teens United" >
                    </div>
                    <div class="col-12 teens-tagline reviews-home">
                        <h3>Proudly supporting Teens Unite Since 2018</h3>
                    </div>
                </div>
            </div>
            <?php
        }
        ?>

        <?php
        if($breadcrumb != "") {
            ?>
            <script type="application/ld+json">
            {
              "@context": "https://schema.org",
              "@type": "BreadcrumbList",
              "itemListElement": [{
                "@type": "ListItem",
                "position": 1,
                "name": "Home",
                "item": "<?= SITE_URL ?>"
              }<?php
                $count = 2;
                foreach($breadcrumb as $title => $link) {
                    ?>,{
                "@type": "ListItem",
                "position": <?= $count ?>,
                "name": "<?= $title ?>",
                "item": "<?= $link ?>"
              }<?php
                    $count ++;
                }
                ?>]
            }
            </script>

            <nav aria-label="breadcrumb" class="breadcrumb-bar">
                <div class="container wider-container">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= SITE_URL ?>">Home</a></li>
                        <?php
                        foreach($breadcrumb as $title => $link) {
                            ?>
                            <li class="breadcrumb-item <?php if($link == "") { ?>active<?php } ?>"><a href="<?= $link ?>"><?= $title ?></a></li>
                            <?php
                        }
                        ?>
                    </ol>
                </div>
            </nav>
            <?php
        }
        ?>
    </header>

    <div class="searchOverlay" onclick="closeSearch();"></div>

    <div class="searchFloat">
        <form name="search" action="<?= SITE_URL ?>search">
            <input type="text" id="ajaxSearch" class="form-control" name="search" placeholder="Find your perfect course..." />
            <i class="fa fa-search"></i>
            <i class="fa fa-times" onclick="closeSearch();"></i>
            <div id="ajaxResultsSearch"></div>
        </form>
    </div>

    <script type="text/javascript">
        var typingTimer;
        var doneTypingInterval = 300;
        var $input = $('#ajaxSearch');

        $input.on('keyup', function () {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(doneTyping, doneTypingInterval);
        });

        $input.on('keydown', function () {
            clearTimeout(typingTimer);
        });

        function doneTyping () {
            var query = $("#ajaxSearch").val();
            console.log(query);
            $("#ajaxResultsSearch").load("<?= SITE_URL ?>ajax?c=course&a=header-search&q="+encodeURIComponent(query));
        }
    </script>

    <div class="stickySpace"></div>
