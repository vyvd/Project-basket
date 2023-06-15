<?php

// capture utm details, if any, to later save to an order
if ($this->get["utm_source"] != "") {

    setcookie("utm_source", $this->get["utm_source"], time() + 60 * 60 * 24 * 30, "/");
    setcookie("utm_medium", $this->get["utm_medium"], time() + 60 * 60 * 24 * 30, "/");
    setcookie("utm_campaign", $this->get["utm_campaign"], time() + 60 * 60 * 24 * 30, "/");
    setcookie("utm_term", $this->get["utm_term"], time() + 60 * 60 * 24 * 30, "/");
}

// AWIN Tracking
if ($_GET['awc'] != "") {
    setcookie("awc", $_GET['awc'], time() + 60 * 60 * 24 * 365, "/");
}

/* affiliate tracking and discounting */
include(TO_PATH . '/affiliates/controller/affiliate-tracking.php');

// get currency
$currency = $this->currentCurrency();

// check for affiliate discount
if ($_GET["ref"] != "" && $_SESSION["refCodeInternal"] != $_GET["ref"]) {

    $_SESSION["refCodeInternal"] = $_GET["ref"];

    $affVoucher = ORM::For_table("ap_affiliate_voucher")->where("aff_id", $_GET["ref"])->find_one();

    if ($affVoucher != "") {

        $_SESSION["affiliateDiscount"] = $affVoucher->voucher_value;

        $_SESSION["affiliateDiscountType"] = $affVoucher->comission_type;

        $affCoupon = ORM::for_table("coupons")->where("code", $affVoucher->voucher_code)->find_one();

        // check for currency based discount
        $currencyPricing = ORM::for_table("couponCurrencyPricing")->where("couponID", $affCoupon->id)->where("currencyID", $currency->id)->find_one();

        if ($currencyPricing->value != "") {
            $_SESSION["affiliateDiscount"] = $currencyPricing->value;
        }

        if ($affCoupon->id != "") {

            if ($affCoupon->valueMax != "") {
                $_SESSION["affiliateDiscountMax"] = $affCoupon->valueMax;
            } else {
                $_SESSION["affiliateDiscountMax"] = "";
            }

            if ($affCoupon->valueMin != "") {
                $_SESSION["affiliateDiscountMin"] = $affCoupon->valueMin;
            } else {
                $_SESSION["affiliateDiscountMin"] = "";
            }

            $_SESSION["excludedCourses"] = $affCoupon->excludeCourses;
        }


        $getString = '';

        // as we are redirecting, we do not want to lose any other GET variables, so append these to the direct URL
        foreach ($_GET as $key => $value) {
            if ($key != "ref" && $key != "request") {
                $getString .= '&' . $key . '=' . $value;
            }
        }

        header('Location: ' . SITE_URL . REQUEST . '?ref=' . $_GET["ref"] . $getString);
        exit;
    } else {

        header('Location: ' . SITE_URL . REQUEST . '?ref=70');
        exit;
    }
}


// check to see if currency has a free trial enabled for subscriptions, we will perform a check against the current currency later in the process (on landing pages, etc)
if ($this->get["trial"] == "true") {
    setcookie("allowSubTrial", "true", time() + 60 * 60 * 24 * 30, "/"); // expires in 30 days
}

$should_exclude_GA = false;
$is_profile_page = false;
$is_staging_site = in_array(SITE_ENVIRONMENT, array('staging', 'stage')) ? true : false;
?>
<!doctype html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <?php if ($should_exclude_GA || $is_staging_site) : ?>
        <meta name="robots" content="noindex, nofollow">
        <meta name="googlebot" content="noindex, nofollow">
    <?php else : ?>
        <meta name="robots" content="index, follow">
    <?php endif; ?>


    <title><?= $pageTitle ?> | <?= SITE_NAME ?></title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/core-js/3.15.0/minified.js" integrity="sha512-Z/VQ/Kzx+AXxCyMLlA5UGJD2dcKAiVEaDa1rYwO5up8lv3DLy7U9n3LCpz/s1O/SydrXki2Ia5U7Ujwm5fINew==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="<?= SITE_URL ?>assets/js/jquery.min.js"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&family=Ubuntu:wght@700&display=swap" rel="stylesheet">


    <script>
        let site_type = '<?php echo SITE_TYPE; ?>';
        let domain_name = '<?php echo DOMAIN_NAME; ?>';
        let currency = '<?php echo SITE_TYPE == "us" ? "USD" : "GBP"; ?>';
        let current_user_id = '<?php echo CUR_ID_FRONT; ?>';
        let current_user_email = '<?php echo CUR_EMAIL_FRONT; ?>';
        let current_user_email_hased = '<?php echo hash("sha256", CUR_EMAIL_FRONT); ?>';


        window.SITE_TYPE = site_type == '' ? null : site_type;
        window.DOMAIN_NAME = domain_name == '' ? null : domain_name;
        window.CURRENCY = currency == '' ? null : currency;
        window.CUR_ID_FRONT = current_user_id == '' ? null : current_user_id;
        window.CUR_EMAIL_FRONT = current_user_email == '' ? null : current_user_email;
        window.CUR_EMAIL_FRONT_HASHED = current_user_email == '' ? null : current_user_email_hased;

        // declare klaviyo tracking
        if (typeof _learnq === 'undefined' || _learnq === null) {
            let _learnq = [];
        }

        // AWIN Tracking
        var iCookieLength = 30; // Cookie length in days
        var sCookieName = "Source"; // Name of the first party cookie to utilise for last click referrer de-duplication
        var sSourceParameterName = ["utm_source", "gclid", "fbclid"]; // The parameter used by networks and other marketing channels to tell you who drove the traffic
        var domain = domain_name ?? ''; // Top level domain

        var _getQueryStringValue = function(sParameterName) {

            var aQueryStringPairs = document.location.search.substring(1).split("&");
            for (var i = 0; i < aQueryStringPairs.length; i++) {
                var aQueryStringParts = aQueryStringPairs[i].split("=");
                if (sParameterName.includes(aQueryStringParts[0].toLowerCase())) {
                    if (aQueryStringParts[0].toLowerCase() == "utm_source") {
                        return aQueryStringParts[1].toLowerCase();
                    } else if (sSourceParameterName.includes(aQueryStringParts[0].toLowerCase())) {
                        return aQueryStringParts[0].toLowerCase();
                    } else {
                        return 'other';
                    }
                }
            }
        };

        var _setCookie = function(sCookieName, sCookieContents, iCookieLength) {
            var dCookieExpires = new Date();
            dCookieExpires.setTime(dCookieExpires.getTime() + (iCookieLength * 24 * 60 * 60 * 1000));
            document.cookie = sCookieName + "=" + sCookieContents + "; expires=" + dCookieExpires.toGMTString() + "; path=/; domain=" + domain;
        };

        if (_getQueryStringValue(sSourceParameterName)) {
            _setCookie(sCookieName, _getQueryStringValue(sSourceParameterName), iCookieLength);
        }
    </script>

    <!-- Facebook Pixel Code -->
    <script>
        ! function(f, b, e, v, n, t, s) {
            if (f.fbq) return;
            n = f.fbq = function() {
                n.callMethod ?
                    n.callMethod.apply(n, arguments) : n.queue.push(arguments)
            };
            if (!f._fbq) f._fbq = n;
            n.push = n;
            n.loaded = !0;
            n.version = '2.0';
            n.queue = [];
            t = b.createElement(e);
            t.async = !0;
            t.src = v;
            s = b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t, s)
        }(window, document, 'script',
            'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '<?= FB_BUSINESS_PIXEL_ID ?>');
        fbq('track', 'PageView');
    </script>

    <noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=<?= FB_BUSINESS_PIXEL_ID ?>&ev=PageView&noscript=1" /></noscript>
    <!-- End Facebook Pixel Code --><noscript><img height="1" width="1" style="display: none;" src="https://www.facebook.com/tr?id=<?= FB_BUSINESS_PIXEL_ID ?>&ev=PageView&noscript=1&eid" alt="facebook_pixel"></noscript>


    <?php
    if (TIKTOK_KEY_1 != "") {
    ?>
        <!-- TikTok Pixel Base Code -->
        <script>
            ! function(w, d, t) {
                w.TiktokAnalyticsObject = t;
                var ttq = w[t] = w[t] || [];
                ttq.methods = ["page", "track", "identify", "instances", "debug", "on", "off", "once", "ready", "alias", "group", "enableCookie", "disableCookie"], ttq.setAndDefer = function(t, e) {
                    t[e] = function() {
                        t.push([e].concat(Array.prototype.slice.call(arguments, 0)))
                    }
                };
                for (var i = 0; i < ttq.methods.length; i++) ttq.setAndDefer(ttq, ttq.methods[i]);
                ttq.instance = function(t) {
                    for (var e = ttq._i[t] || [], n = 0; n < ttq.methods.length; n++) ttq.setAndDefer(e, ttq.methods[n]);
                    return e
                }, ttq.load = function(e, n) {
                    var i = "https://analytics.tiktok.com/i18n/pixel/events.js";
                    ttq._i = ttq._i || {}, ttq._i[e] = [], ttq._i[e]._u = i, ttq._t = ttq._t || {}, ttq._t[e] = +new Date, ttq._o = ttq._o || {}, ttq._o[e] = n || {};
                    n = document.createElement("script");
                    n.type = "text/javascript", n.async = !0, n.src = i + "?sdkid=" + e + "&lib=" + t;
                    e = document.getElementsByTagName("script")[0];
                    e.parentNode.insertBefore(n, e)
                };

                if (CUR_ID_FRONT) {

                    ttq.identify({
                        external_id: CUR_ID_FRONT,
                        email: CUR_EMAIL_FRONT, // we will hash with sha-256
                        //phone_number: '+12133734253', // we will hash with sha-256
                    });

                }

                ttq.load('<?= TIKTOK_KEY_1 ?>');

                ttq.page();

            }(window, document, 'ttq');
        </script>
        <!-- End TikTok Pixel Base Code -->
    <?php } ?>


    <?php
    if (SNAP_KEY_1 != "") {
    ?>
        <!-- Snap Pixel Code -->
        <script type='text/javascript'>
            (function(e, t, n) {
                if (e.snaptr) return;
                var a = e.snaptr = function() {
                    a.handleRequest ? a.handleRequest.apply(a, arguments) : a.queue.push(arguments)
                };
                a.queue = [];
                var s = 'script';
                r = t.createElement(s);
                r.async = !0;
                r.src = n;
                var u = t.getElementsByTagName(s)[0];
                u.parentNode.insertBefore(r, u);
            })(window, document,
                'https://sc-static.net/scevent.min.js');

            /*
            snaptr('init', '6ec934d9-9045-45f8-84fc-e64af5b7174a', {
                'user_email': CUR_EMAIL_FRONT,
                'user_hashed_email': CUR_EMAIL_FRONT_HASHED
            });
             */

            if (CUR_EMAIL_FRONT_HASHED) {

                snaptr('init', '<?= SNAP_KEY_1 ?>', {
                    'user_email': CUR_EMAIL_FRONT,
                    'user_hashed_email': CUR_EMAIL_FRONT_HASHED
                });

            } else {

                snaptr('init', '<?= SNAP_KEY_1 ?>');

            }


            snaptr('track', 'PAGE_VIEW');
        </script>
        <!-- End Snap Pixel Code -->
    <?php } ?>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="<?= SITE_URL ?>assets/vendor/bootstrap/bootstrap.min.css">
    <!-- Css Style -->
    <link rel="stylesheet" href="<?= SITE_URL ?>assets/css/theme.css">
    <?php
    foreach ($css as $item) {
    ?>
        <link rel="stylesheet" href="<?= SITE_URL ?>assets/css/<?= $item ?>">
    <?php
    }
    if (is_array($js)) {
        foreach ($js as $item) {
    ?>
            <script src="<?= SITE_URL ?>assets/js/<?= $item ?>"></script>
    <?php
        }
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
    <meta property="og:url" content="<?= SITE_URL . REQUEST ?>" />
    <meta property="og:site_name" content="<?= SITE_NAME ?>" />

    <?php
    if (SITE_TYPE == "uk") {
    ?>
        <script type='application/ld+json'>
            {
                "@context": "http:\/\/schema.org",
                "@type": "WebSite",
                "@id": "#website",
                "url": "https:\/\/newskillsacademy.co.uk\/",
                "name": "New Skills Academy",
                "potentialAction": {
                    "@type": "SearchAction",
                    "target": "https:\/\/newskillsacademy.co.uk\/search?search={search_term_string}",
                    "query-input": "required name=search_term_string"
                }
            }
        </script>
    <?php
    } else {
    ?>
        <script type='application/ld+json'>
            {
                "@context": "http:\/\/schema.org",
                "@type": "WebSite",
                "@id": "#website",
                "url": "https:\/\/newskillsacademy.com\/",
                "name": "New Skills Academy",
                "potentialAction": {
                    "@type": "SearchAction",
                    "target": "https:\/\/newskillsacademy.com\/search?search={search_term_string}",
                    "query-input": "required name=search_term_string"
                }
            }
        </script>
    <?php
    }
    ?>

    <script type="application/ld+json">
        {
            "@context": "http://schema.org",
            "@type": "Organization",
            "name": "<?= SITE_NAME ?>",
            "url": "<?= SITE_URL ?>",
            "sameAs": [
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

    <link rel="stylesheet" href="<?= SITE_URL ?>assets/vendor/owlCarousel/assets/owl.carousel.min.css">
    <link rel="stylesheet" href="<?= SITE_URL ?>assets/vendor/owlCarousel/assets/owl.theme.default.min.css">
    <script src="<?= SITE_URL ?>assets/vendor/owlCarousel/owl.carousel.js"></script>

    <link rel="canonical" href="<?= SITE_URL . rtrim(REQUEST, '/') ?>" />

    <?php if (!empty(CUR_ID_FRONT) && !$should_exclude_GA) : ?>

        <script>
            window.dataLayer = window.dataLayer || [];
            window.dataLayer.push({
                'user_id': '<?php echo CUR_ID_FRONT; ?>',
                'user_email': '<?php echo CUR_EMAIL_FRONT; ?>'
            });
        </script>



    <?php endif; ?>

    <?php if (!$should_exclude_GA) : ?>

        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=<?= GA_KEY_1 ?>"></script>

        <?php if (!empty(CUR_ID_FRONT)) : ?>

            <script>
                window.dataLayer = window.dataLayer || [];

                function gtag() {
                    dataLayer.push(arguments);
                }
                gtag('js', new Date());
                gtag('config', '<?= GA_KEY_1 ?>', {
                    'user_id': '<?php echo CUR_ID_FRONT; ?>',
                    'user_email': '<?php echo CUR_EMAIL_FRONT; ?>'
                });
                gtag('config', '<?= GA_KEY_2 ?>');
            </script>



        <?php else : ?>

            <script>
                window.dataLayer = window.dataLayer || [];

                function gtag() {
                    dataLayer.push(arguments);
                }
                gtag('js', new Date());
                gtag('config', '<?= GA_KEY_1 ?>');
                gtag('config', '<?= GA_KEY_2 ?>');
            </script>

        <?php endif; ?>


        <!-- GTM snippet to go here-->

        <!-- Google Tag Manager -->

        <script>
            (function(w, d, s, l, i) {
                w[l] = w[l] || [];
                w[l].push({
                    'gtm.start': new Date().getTime(),
                    event: 'gtm.js'
                });
                var f = d.getElementsByTagName(s)[0],
                    j = d.createElement(s),
                    dl = l != 'dataLayer' ? '&l=' + l : '';
                j.async = true;
                j.src =
                    'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
                f.parentNode.insertBefore(j, f);
            })(window, document, 'script', 'dataLayer', '<?= GTM_KEY_1 ?>');
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



    <script data-obct type="text/javascript">
        /** DO NOT MODIFY THIS CODE**/ ! function(_window, _document) {
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
        window._tfa.push({
            notify: 'event',
            name: 'page_view',
            id: 1289858
        });
        ! function(t, f, a, x) {
            if (!document.getElementById(x)) {
                t.async = 1;
                t.src = a;
                t.id = x;
                f.parentNode.insertBefore(t, f);
            }
        }(document.createElement('script'),
            document.getElementsByTagName('script')[0],
            '//cdn.taboola.com/libtrc/unip/1289858/tfa.js',
            'tb_tfa_script');
    </script>
    <noscript>
        <img src='https://trc.taboola.com/1289858/log/3/unip?en=page_view' width='0' height='0' style='display:none' />
    </noscript>
    <!-- End of Taboola Pixel Code -->


    <?php
    if (BING_KEY_1 != "") {
    ?>
        <!-- UET Tag for Microsoft Ads Start -->
        <script>
            (function(w, d, t, r, u) {
                var f, n, i;
                w[u] = w[u] || [], f = function() {
                    var o = {
                        ti: "<?= BING_KEY_1 ?>"
                    };
                    o.q = w[u], w[u] = new UET(o), w[u].push("pageLoad")
                }, n = d.createElement(t), n.src = r, n.async = 1, n.onload = n.onreadystatechange = function() {
                    var s = this.readyState;
                    s && s !== "loaded" && s !== "complete" || (f(), n.onload = n.onreadystatechange = null)
                }, i = d.getElementsByTagName(t)[0], i.parentNode.insertBefore(n, i)
            })(window, document, "script", "//bat.bing.com/bat.js", "uetq");
        </script>
        <!-- UET Tag for Microsoft Ads Ends -->
    <?php
    }
    ?>

    <?php
    if (isset($headerHTML)) {
        echo $headerHTML;
    }
    ?>


</head>

<body>
<!-- start of banner code  -->
    <?php

    if(!strpos($_SERVER['REQUEST_URI'], 'gift') && !strpos($_SERVER['REQUEST_URI'], 'special-offer/') && !strpos($_SERVER['REQUEST_URI'], 'bankofengland')) {
        foreach (ORM::for_table('Banner')->where("bannerState", "on")->find_result_set() as $item) {
            
            if ($_SESSION['refCodeInternal'] == $item->bannerRef ) { 
                    if($item->bannerTimer != ""){
                        $currentTime = date('h:i A', time());
                        $endTime = $item->bannerTimer;
                        if((strtotime($currentTime) >= strtotime($endTime)) ){
                            $item->set(
                                array(
                                    'bannerState'  => "off",
                                )
                            );
                            $item->save();
                        }
                    }          
                ?>
                    <style>
                    .spacing { height: 40px;}
                    .topBanner p {margin-bottom: 0;font-size: 14px;}
                    </style>
                    <script>
                        const daterate='<?php echo $item->bannerTimer; ?>';
                        const newDate = daterate.replace("T"," ");
                        // // Set the date we're counting down to
                        var countDownDate = new Date(newDate).getTime();
                        // Update the count down every 1 second
                        var x = setInterval(function() {
                            // Get today's date and time
                            var now = new Date().getTime();
                            // Find the distance between now and the count down date
                            var distance = countDownDate - now;
                            // Time calculations for days, hours, minutes and seconds
                            var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                            var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                            var seconds = Math.floor((distance % (1000 * 60)) / 1000);
                            // Display the result in the element with id="demo"
                            var bannerTimerEle = document.getElementById("bannerTimer");
                            if (bannerTimerEle) {
                                document.getElementById("bannerTimer").innerHTML = days + "d " + hours + "h " +
                                    minutes + "m " + seconds + "s " + "left";
                                // If the count down is finished, write some text
                                if (distance < 0) {
                                    clearInterval(x);
                                    document.getElementById("bannerTimer").innerHTML = "";
                                }
                            }
                        }, 1000);
                    </script>
                    <?php
                        $bannerTEXTMAIN = "bannerText" .$currency->code;
                        if($item->$bannerTEXTMAIN != ""){
                            ?>
                            <div class="spacing"></div>
                            <style>.Header.fixed { top:51px; } @media(max-width:600px) {.Header.fixed {top: 98px;} .spacing { height:94px }.top-header .navbar-collapse { top:87px; }}</style>
                            <div class="topBanner" style="background:<?php echo $item->bannerColor ?>;color:<?php echo $item->bannerTextColor ?>;padding:10px;text-align:center;font-size:14px;position:fixed;width:100%;top:0;left:0;z-index:99;">                
                            <?php 

                            $item->$bannerTEXTMAIN = str_replace(['<p>', '</p>'], '', $item->$bannerTEXTMAIN);
                            echo $item->$bannerTEXTMAIN  ;
                                if($item->bannerTimer != ""){
                                    ?>
                                    <span style="font-weight: bold;margin-left: 10px;" id="bannerTimer"></span> 
                                    <?php 
                                }
                            }else {
                                break;
                            }
                        
                        
                }   
                    ?>
                            </div>
    <?php   } 
        }
    
    ?>
    
    <!-- End of banner code  -->
    <?php if (!$should_exclude_GA) : ?>
        <!-- Google Tag Manager (noscript) -->
        <noscript>
            <iframe src="https://www.googletagmanager.com/ns.html?id=<?= GTM_KEY_1 ?>" height="0" width="0" style="display:none;visibility:hidden"></iframe>
        </noscript>
        <!-- End Google Tag Manager (noscript) -->
    <?php endif; ?>

    <?php
    if (ADMIN_ACCESSED == true) {
    ?>
        <div style="position:fixed;top:0;left:0;width:100%;padding:5px 15px;color:#fff;z-index:9999999;background: #08586c;font-size:17px;">
            <?php
            if ($_SESSION["iqaAccessed"] == "yes") {
            ?>
                Accessed via IQA
                <a href="<?= SITE_URL ?>ajax?c=tutor&a=exit-iqa-access&id=<?= CUR_ID_FRONT ?>" style="    color: #fff;
    background: #17a9ce;
    float: right;
    padding: 0px 15px;
    border-radius: 5px;">
                    Exit
                </a>
            <?php
            } else {
            ?>
                Accessed via Admin
                <a href="<?= SITE_URL ?>ajax?c=blumeNew&a=exit-admin-access&id=<?= CUR_ID_FRONT ?>" style="    color: #fff;
    background: #17a9ce;
    float: right;
    padding: 0px 15px;
    border-radius: 5px;">
                    Exit
                </a>
            <?php
            }
            ?>
        </div>
        <div style="height:100px;"></div>

    <?php
    }
    ?>
    <?php
    if (REQUEST == "" || REQUEST == "teens-unite") {
    ?>
    <?php
    }
    ?>
    <!--Header Part-->

        <header  class="<?php if (REQUEST == "" || REQUEST == "teens-unite" || REQUEST == "subscription" || REQUEST == "subscription-old" || REQUEST == "subscription-sale" || REQUEST == "subscription-renew" || REQUEST == "subscription-sale-monthly") { ?>home<?php } ?>Header">

        <div class="top-header">

            <nav class="navbar navbar-expand-lg">

                <div class="container wider-container">


                    <a class="navbar-brand" href="<?= SITE_URL ?>">
                        <img class="logo" src="<?= SITE_URL ?>assets/images/logo-<?php if (REQUEST == "" || REQUEST == "teens-unite" || REQUEST == "subscription" || REQUEST == "subscription-old" || REQUEST == "subscription-sale" || REQUEST == "subscription-renew" || REQUEST == "subscription-sale-monthly") { ?>white<?php } else { ?>blue<?php } ?>.png" alt="New Skills Academy" />
                        <img class="logo sticky" src="<?= SITE_URL ?>assets/images/logo-blue.png" alt="New Skills Academy" />
                    </a>


                    <button class="navbar-toggler toggler-example" type="button" data-toggle="collapse" data-target="#navBar" aria-controls="navBar" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="dark-blue-text"><i class="fas fa-bars fa-1x"></i></span>
                    </button>

                    <a class="nav-link cart mobileBasketToggle" data-toggle="modal" data-target="#basket" onclick="refreshCartSide();" href="javascript:;">
                        <span class="ajaxCartCount"></span>
                        <i class="fas fa-shopping-cart"></i>
                    </a>
                    <div class="collapse navbar-collapse" id="navBar">

                        <ul class="nav navbar-nav ml-auto">

                            <li class="nav-item link <?php if (REQUEST == "") { ?>active<?php } ?>">
                                <a class="nav-link" href="<?= SITE_URL ?>">Home</a>
                            </li>
                            <li class="nav-item link <?php if (REQUEST == "courses") { ?>active<?php } ?>">
                                <a class="nav-link" href="<?= SITE_URL ?>courses">Online Courses</a>
                            </li>
                            <?php if ($currency->code == "GBP") { ?>
                                <li class="nav-item link <?php if (REQUEST == "staff-training") { ?>active<?php } ?>">
                                    <a class="nav-link" href="<?= SITE_URL ?>staff-training">Staff Training</a>
                                </li>
                            <?php } ?>
                            <li class="nav-item link <?php if (REQUEST == "support") { ?>active<?php } ?>">
                                <a class="nav-link" href="<?= SITE_URL ?>support">Support</a>
                            </li>
                            <li class="right-icons">

                                <?php
                                if (SIGNED_IN == false) {
                                ?>
                                    <a class="nav-link signIn" href="javascript:;" data-toggle="modal" data-target="#signIn" onclick='$(".close-menu").trigger("click");'>Sign In</a>
                                <?php
                                } else {
                                ?>
                                    <a class="nav-link signIn" href="<?= SITE_URL ?>dashboard">Dashboard</a>
                                <?php
                                }
                                ?>
                                
                            </li>
                            
                            <li class="right-icons">
                                <a class="nav-link search" href="javascript:searchFloat();"><i class="fas fa-search"></i></a>
                            </li>
                            <?php
                            $showSwitcher = true;

                            if (SITE_TYPE == "uk") {
                                $showSwitcher = false;
                            }

                            if ($this->get["switchCur"] == "true") {
                                $showSwitcher = true;
                            }


                            if ($showSwitcher == true) {
                            ?>
                                <li class="right-icons cur">
                                    <a class="nav-link currency" href="javascript:;">
                                        <?= $currency->code ?> (<?= $currency->short ?>)
                                        <i class="fas fa-caret-down"></i>
                                    </a>
                                    <ul class="dropdown">
                                        <?php
                                        foreach (ORM::For_table("currencies")->find_many() as $currencyDropdown) {
                                        ?>
                                            <li data-currency="<?= $currencyDropdown->id ?>"><?= $currencyDropdown->code ?> (<?= $currencyDropdown->short ?>)</li>
                                        <?php
                                        }
                                        ?>
                                    </ul>
                                </li>
                            <?php
                            }
                            ?>
                            <li class="right-icons">
                                
                                <a class="nav-link cart" data-toggle="modal" data-target="#basket" onclick="refreshCartSide();" href="javascript:;">
                                    <span class="ajaxCartCount"></span>
                                    <i class="fas fa-shopping-cart"></i>
                                </a>
                            </li>
                        </ul>
                        <div class="close-menu d-md-none"><i class="fas fa-times-circle"></i></div>
                    </div>
                </div>
            </nav>
        </div>

        <?php
        if (REQUEST == "") {

            $courseCount = ceil((ORM::for_table("courses")->where("usImport", "0")->count() - 5) / 10) * 10;
            $studentCount = ORM::for_table("accounts")->count();
            $studentCount = round($studentCount, -3);
        ?>
            <div class="container home-banner-section wider-container">
                <div class="home-banner">
                </div>
                <div class="row text-center">
                    <h2 class="home-banner-title">Change Your Life With A New Skill</h2>
                    <h3 class="home-banner-subtitle" <?php if (SITE_TYPE == "uk") { ?>style="color:#fff;" <?php } ?>>Join Over <?= number_format($studentCount) ?> students and study one of our <br /> <?= $courseCount ?>+ career enhancing, confidence boosting courses</h3>
                    <div class="col-12 search-box">
                        <form action="<?= SITE_URL ?>search" method="get" id="homeSearch">
                            <input type="text" name="search" placeholder="Find your perfect course..." class="search-field">
                            <a class="search-button" href="javascript:;" onclick="$('#homeSearch').submit();"><img src="<?= SITE_URL ?>assets/images/sarch-icon.png" alt="Search" /></a>
                        </form>
                    </div>
                    <div class="col-12 banner-buttons">
                        <a href="<?= SITE_URL ?>courses">See All Courses</a>
                        <a href="<?= SITE_URL ?>subscription" class="featured"><i class="fas fa-medal"></i> Unlimited Learning</a>
                        <?php
                        if ($_SESSION["refCodeInternal"] != "330") {
                        ?>
                            <a href="<?= SITE_URL ?>redeem">Redeem a Voucher</a>
                        <?php
                        }
                        ?>
                        <!--<a href="javascript:;" onclick="scrollValidate();">Get Unlimited Learning</a>-->
                    </div>

                    <?php
                    if ($_SESSION["refCodeInternal"] == "330") {
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
                                scrollTop: $("#validateFooter").offset().top - 100
                            }, 1000);

                        }
                    </script>

                    <div class="col-12 reviews-home">
                        <?php
                        if ($this->isMobileDevice()) {
                        ?>
                            <div class="trustpilot-widget" data-locale="en-GB" data-template-id="539ad0ffdec7e10e686debd7" data-businessunit-id="5b450fa2ad92290001bfac20" data-style-height="318px" data-style-width="100%" data-theme="dark" data-stars="4,5">
                                https://uk.trustpilot.com/review/newskillsacademy.co.uk
                            </div>
                            <script type='text/javascript' src='//widget.trustpilot.com/bootstrap/v5/tp.widget.bootstrap.min.js?ver=5.4'></script>
                        <?php
                        } else {
                        ?>
                            <script type='text/javascript' src='//widget.trustpilot.com/bootstrap/v5/tp.widget.bootstrap.min.js?ver=5.4'></script>

                            <div class="trustpilot-widget" data-locale="en-GB" data-template-id="53aa8912dec7e10d38f59f36" data-businessunit-id="5b450fa2ad92290001bfac20" data-style-height="160px" data-style-width="100%" data-theme="dark" data-stars="5" data-schema-type="Organization">
                            </div>
                        <?php
                        }
                        ?>

                    </div>
                </div>
            </div>
        <?php
        } else if (REQUEST == "teens-unite") {
        ?>
            <div class="container home-banner-section wider-container">
                <div class="teens-banner">
                </div>
                <div class="row text-center">
                    <div class="col-12 teens-main-logo">
                        <img src="<?= SITE_URL ?>assets/images/teens-logo.png" alt="Teens United">
                    </div>
                    <div class="col-12 teens-tagline reviews-home">
                        <h3>Proudly supporting Teens Unite Since 2018</h3>
                    </div>
                </div>
            </div>
        <?php
        } else if (REQUEST == "subscription-old") {
            $courseCount = 700;

            if ($currency->code != "GBP") {
                $courseCount = 300;
            }
        ?>
            <div class="container home-banner-section wider-container">
                <div class="subscription-banner">
                </div>
                <div id="hero">
                    <div class="wider-container">

                        <div class="row">
                            <div class="col-12 col-lg-7">

                                <h2 class="wow fadeInUp">Become an <span>Unlimited Learning Member</span> & get</h2>
                                <h1 class="wow fadeInUp" data-wow-delay="0.2s">Unlimited access <br />to <strong><?= $courseCount ?>+ courses</strong></h1>
                                <p class="wow fadeInUp" data-wow-delay="0.4s">Only <?= $this->price($currency->prem12) ?> for 12 months!</p>

                                <?php
                                if ($_COOKIE["allowSubTrial"] == "true" && $currency->trialStatus != "None") {
                                ?>
                                    <p class="trialPoint">
                                        This includes a FREE <?= $currency->trialDays ?> day trial!
                                        <small>Cancel anytime.</small>
                                    </p>
                                <?php
                                }
                                ?>

                                <a href="<?= SITE_URL ?>checkout?addSub=true" class="cta wow pulse" data-wow-delay="3s">
                                    Get Access Now
                                </a>

                            </div>
                        </div>

                    </div>
                </div>
            </div>
        <?php
        } else if (REQUEST == "subscription") {

            $courseCount = 700;

            if ($currency->code != "GBP") {
                $courseCount = 300;
            }

        ?>
            <div class="container home-banner-section wider-container subscription2">
                <div class="subscription-banner">
                </div>
                <div id="hero">
                    <div class="wider-container">

                        <div class="row">
                            <div class="col-12 col-lg-12 header_subscription2">

                                <!--                            <h2 class="wow fadeInUp">Become an <span>Unlimited Learning Member</span> & get</h2>-->
                                <h1 class="wow fadeInUp" data-wow-delay="0.2s">Unlimited Courses, <?php if ($currency->code == "GBP") { ?>free student card<?php } ?> and more.</h1>
                                <h1 class="wow fadeInUp mt-0" data-wow-delay="0.2s"> Study anywhere, cancel anytime.</h1>
                                <h2 class="wow fadeInUp mt-3"><span>Ready to learn?</span></h2>
                                <h2 class="wow fadeInUp mt-3"><span>Click below to create or restart your membership and discover over <?= $courseCount ?> courses.</span></h2>

                                <?php
                                if ($_COOKIE["allowSubTrial"] == "true" && $currency->trialStatus != "None") {
                                ?>
                                    <p class="trialPoint">
                                        This includes a FREE <?= $currency->trialDays ?> day trial!
                                        <small>Cancel anytime.</small>
                                    </p>
                                <?php
                                }
                                ?>

                                <a href="<?= SITE_URL ?>checkout?addSub=true" class="cta wow pulse" data-wow-delay="3s">
                                    Get Access Now
                                </a>
                                <p class="wow fadeInUp mt-3" style="color: #3E9DC0" data-wow-delay="0.4s">Only <?= $this->price($currency->prem12) ?> for 12 months!</p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        <?php
        } else if (REQUEST == "subscription-sale") {

            $courseCount = 700;

            if ($currency->code != "GBP") {
                $courseCount = 300;
            }

            $original = $currency->prem12;
            $currency->prem12 = 75;

            if($currency->code == "USD") {
                $currency->prem12 = 99;
            }

            ?>
            <div class="container home-banner-section wider-container subscription2">
                <div class="subscription-banner">
                </div>
                <div id="hero">
                    <div class="wider-container">

                        <div class="row">
                            <div class="col-12 col-lg-12 header_subscription2">

                                <!--                            <h2 class="wow fadeInUp">Become an <span>Unlimited Learning Member</span> & get</h2>-->
                                <h1 class="wow fadeInUp" data-wow-delay="0.2s">Unlimited Courses, <?php if ($currency->code == "GBP") { ?>free student card<?php } ?> and more.</h1>
                                <h1 class="wow fadeInUp mt-0" data-wow-delay="0.2s"> Study anywhere, cancel anytime.</h1>
                                <h2 class="wow fadeInUp mt-3"><span>Ready to learn?</span></h2>
                                <h2 class="wow fadeInUp mt-3"><span>Click below to create or restart your membership and discover over <?= $courseCount ?> courses.</span></h2>

                                <?php
                                if ($_COOKIE["allowSubTrial"] == "true" && $currency->trialStatus != "None") {
                                    ?>
                                    <p class="trialPoint">
                                        This includes a FREE <?= $currency->trialDays ?> day trial!
                                        <small>Cancel anytime.</small>
                                    </p>
                                    <?php
                                }
                                ?>

                                <a href="<?= SITE_URL ?>ajax?c=cart&a=add-renewal-discounted-subscription" class="cta wow pulse" data-wow-delay="3s">
                                    Get Access Now
                                </a>
                                <p class="wow fadeInUp mt-3" style="color: #3E9DC0;opacity:0.7;" data-wow-delay="0.4s"><s>Was <?= $this->price($original) ?></s></p>
                                <p class="wow fadeInUp mt-3" style="color: #3E9DC0" data-wow-delay="0.4s">Now only <?= $this->price($currency->prem12) ?> for 12 months!</p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <?php
        }else if (REQUEST == "subscription-sale-monthly") {

            $courseCount = 700;

            if ($currency->code != "GBP") {
                $courseCount = 300;
            }

            $original = $currency->prem1;
            $currency->prem1 = 8.25;

            ?>
            <div class="container home-banner-section wider-container subscription2">
                <div class="subscription-banner">
                </div>
                <div id="hero">
                    <div class="wider-container">

                        <div class="row">
                            <div class="col-12 col-lg-12 header_subscription2">

                                <!--                            <h2 class="wow fadeInUp">Become an <span>Unlimited Learning Member</span> & get</h2>-->
                                <h1 class="wow fadeInUp" data-wow-delay="0.2s">Unlimited Courses, <?php if ($currency->code == "GBP") { ?>free student card<?php } ?> and more.</h1>
                                <h1 class="wow fadeInUp mt-0" data-wow-delay="0.2s"> Study anywhere, cancel anytime.</h1>
                                <h2 class="wow fadeInUp mt-3"><span>Ready to learn?</span></h2>
                                <h2 class="wow fadeInUp mt-3"><span>Click below to create or restart your membership and discover over <?= $courseCount ?> courses.</span></h2>

                                <?php
                                if ($_COOKIE["allowSubTrial"] == "true" && $currency->trialStatus != "None") {
                                    ?>
                                    <p class="trialPoint">
                                        This includes a FREE <?= $currency->trialDays ?> day trial!
                                        <small>Cancel anytime.</small>
                                    </p>
                                    <?php
                                }
                                ?>

                                <a href="<?= SITE_URL ?>ajax?c=cart&a=add-Monthly-Discounted-Subscription" class="cta wow pulse" data-wow-delay="3s">
                                    Get Access Now
                                </a>
                                <p class="wow fadeInUp mt-3" style="color: #3E9DC0;opacity:0.7;" data-wow-delay="0.4s"><s>Was <?= $this->price($original) ?></s></p>
                                <p class="wow fadeInUp mt-3" style="color: #3E9DC0" data-wow-delay="0.4s">Now only <?= $this->price($currency->prem1) ?> for 1 month!</p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <?php
        } else if (REQUEST == "subscription-renew") {

            $courseCount = 700;

            if ($currency->code != "GBP") {
                $courseCount = 300;
            }

            $original = $currency->prem12;
            $currency->prem12 = 50;

            ?>
            <div class="container home-banner-section wider-container subscription2">
                <div class="subscription-banner">
                </div>
                <div id="hero">
                    <div class="wider-container">

                        <div class="row">
                            <div class="col-12 col-lg-12 header_subscription2">

                                <!--                            <h2 class="wow fadeInUp">Become an <span>Unlimited Learning Member</span> & get</h2>-->
                                <h1 class="wow fadeInUp" data-wow-delay="0.2s">Unlimited Courses, <?php if ($currency->code == "GBP") { ?>free student card<?php } ?> and more.</h1>
                                <h1 class="wow fadeInUp mt-0" data-wow-delay="0.2s"> Study anywhere, cancel anytime.</h1>
                                <h2 class="wow fadeInUp mt-3"><span>Ready to learn?</span></h2>
                                <h2 class="wow fadeInUp mt-3"><span>Click below to create or restart your membership and discover over <?= $courseCount ?> courses.</span></h2>

                                <?php
                                if ($_COOKIE["allowSubTrial"] == "true" && $currency->trialStatus != "None") {
                                    ?>
                                    <p class="trialPoint">
                                        This includes a FREE <?= $currency->trialDays ?> day trial!
                                        <small>Cancel anytime.</small>
                                    </p>
                                    <?php
                                }
                                ?>

                                <a href="<?= SITE_URL ?>ajax?c=cart&a=add-renewal-discounted-subscription" class="cta wow pulse" data-wow-delay="3s">
                                    Get Access Now
                                </a>
                                <p class="wow fadeInUp mt-3" style="color: #3E9DC0;opacity:0.7;" data-wow-delay="0.4s"><s>Was <?= $this->price($original) ?></s></p>
                                <p class="wow fadeInUp mt-3" style="color: #3E9DC0" data-wow-delay="0.4s">Now only <?= $this->price($currency->prem12) ?> for 12 months!</p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <?php
        }
        ?>

        <?php
        if ($breadcrumb != "") {
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
                        }
                        <?php
                        $count = 2;
                        foreach ($breadcrumb as $title => $link) {
                        ?>, {
                                "@type": "ListItem",
                                "position": <?= $count ?>,
                                "name": "<?= $title ?>",
                                "item": "<?= $link ?>"
                            }
                        <?php
                            $count++;
                        }
                        ?>
                    ]
                }
            </script>

            <nav aria-label="breadcrumb" class="breadcrumb-bar">
                <div class="container wider-container">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= SITE_URL ?>">Home</a></li>
                        <?php
                        foreach ($breadcrumb as $title => $link) {
                        ?>
                            <li class="breadcrumb-item <?php if ($link == "") { ?>active<?php } ?>"><a href="<?= $link ?>"><?= $title ?></a></li>
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

        $input.on('keyup', function() {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(doneTyping, doneTypingInterval);
        });

        $input.on('keydown', function() {
            clearTimeout(typingTimer);
        });

        function doneTyping() {
            var query = $("#ajaxSearch").val();
            console.log(query);
            $("#ajaxResultsSearch").load("<?= SITE_URL ?>ajax?c=course&a=header-search&q=" + encodeURIComponent(query));
        }

    </script>

    <div class="stickySpace"></div>