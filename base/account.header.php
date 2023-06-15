<?php
if($this->userType("NCFE")) {
    include BASE_PATH . 'account-ncfe.header.php';
} else if($this->userType("tutor")) {
    include BASE_PATH . 'account-tutor.header.php';
} else if($this->userType("IQA")) {
    include BASE_PATH . 'account-iqa.header.php';
} else {

$should_exclude_GA = false;
$is_profile_page = false;

// get currency
$currency = $this->currentCurrency();
$isActiveSubscription = $this->isActiveSubscription(CUR_ID_FRONT);
?>
<!doctype html>
<html lang="en">
<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="robots" content="index,follow">

    <?php if($should_exclude_GA): ?>
        <meta name="robots" content="noindex, nofollow">
        <meta name="googlebot" content="noindex, nofollow">
    <?php else: ?>
        <meta name="robots" content="index, follow">
    <?php endif; ?>

    <title><?= $pageTitle ?> | <?= SITE_NAME ?></title>

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Catamaran:wght@100;200;300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/core-js/3.15.0/minified.js" integrity="sha512-Z/VQ/Kzx+AXxCyMLlA5UGJD2dcKAiVEaDa1rYwO5up8lv3DLy7U9n3LCpz/s1O/SydrXki2Ia5U7Ujwm5fINew==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

    <!-- Css Style -->
    <link rel="stylesheet" href="<?= SITE_URL ?>assets/user/css/theme.css">
    <?php
    foreach($css as $item) {
        ?>
        <link rel="stylesheet" href="<?= SITE_URL ?>assets/css/<?= $item ?>">
        <?php
    }
    ?>
    <link rel="stylesheet" href="<?= SITE_URL ?>assets/user/css/media.css">

    <!--Favicon-->
    <link rel="icon" type="image/png" href="<?= SITE_URL ?>assets/images/favicon.png" />

    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <link href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.11.2/css/all.css" integrity="sha384-zrnmn8R8KkWl12rAZFt4yKjxplaDaT7/EUkKm7AovijfrQItFWR7O/JJn4DAa/gx" crossorigin="anonymous">

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-51304979-13"></script>

    <?php if(!empty(CUR_ID_FRONT)): ?>

        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', 'UA-51304979-13',{'user_id': '<?php echo CUR_ID_FRONT; ?>', 'user_email' : '<?php echo CUR_EMAIL_FRONT; ?>'});
            gtag('config', 'AW-643632521');
        </script>

    <?php else: ?>

        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', 'UA-51304979-13');
            gtag('config', 'AW-643632521');
        </script>

    <?php endif; ?>


    <!-- GTM snippet to go here-->

    <!-- Google Tag Manager -->

    <script>
        (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
            new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-T9QGDDG');
    </script>

    <!-- End Google Tag Manager -->


</head>
<body class="">

<?php
if(ADMIN_ACCESSED == true) {
    ?>
    <div style="position:fixed;top:0;left:0;width:100%;padding:5px 15px;color:#fff;z-index:9999999;background: #08586c;font-size:17px;">
        <?php
        if($_SESSION["iqaAccessed"] == "yes") {
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
    <div style="height:35px;"></div>
    <?php
}
?>

<nav class="navbar navbar-expand-lg sidenav navbar-vertical">
    <div class="sidenav-header  align-items-center">
        <a class="navbar-brand" href="<?= SITE_URL ?>">
            <img class="logo" src="<?= SITE_URL ?>assets/images/logo-white.png" alt="New Skills Academy" />
            <img class="logo collapse" src="<?= SITE_URL ?>assets/images/logo-white-collapsed.png" alt="New Skills Academy" />
        </a>
    </div>

    <div class="top-links d-md-none">
        <?php include('includes/user/top_links.php');?>
    </div>

    <button class="navbar-toggler toggler-example" type="button" data-toggle="collapse" data-target="#navBar" aria-controls="navBar" aria-expanded="false" aria-label="Toggle navigation">
        <span class="dark-blue-text"><i class="fas fa-bars fa-1x"></i></span>
    </button>

    <div class="collapse navbar-collapse" id="navBar">
        <ul class="nav navbar-nav ml-auto">
            <li class="nav-item link <?php if(REQUEST == "dashboard") { ?>active<?php } ?>">
                <a class="nav-link" href="<?= SITE_URL ?>dashboard">
                    <span class="icon"><i class="fas fa-tachometer-alt"></i></span>
                    <span class="menu-title">Dashboard</span>
                </a>
            </li>
            <li class="nav-item link <?php if(REQUEST == "dashboard/courses") { ?>active<?php } ?>">
                <a class="nav-link" href="<?= SITE_URL ?>dashboard/courses">
                    <span class="icon"><i class="fas fa-graduation-cap"></i></span>
                    <span class="menu-title">My Courses</span>
                </a>
            </li>
            <li class="nav-item link <?php if(REQUEST == "dashboard/certificates") { ?>active<?php } ?>">
                <a class="nav-link" href="<?= SITE_URL ?>dashboard/certificates">
                    <span class="icon"><i class="fas fa-chalkboard"></i></span>
                    <span class="menu-title">My Certificates</span>
                </a>
            </li>
            <li class="nav-item link <?php if(REQUEST == "dashboard/rewards") { ?>active<?php } ?>">
                <a class="nav-link" href="<?= SITE_URL ?>dashboard/rewards">
                    <span class="icon"><i class="fas fa-trophy"></i></span>
                    <span class="menu-title">My Rewards</span>
                </a>
            </li>
            <li class="nav-item link <?php if(REQUEST == "dashboard/badges") { ?>active<?php } ?>">
                <a class="nav-link" href="<?= SITE_URL ?>dashboard/badges">
                    <span class="icon"><i class="fas fa-badge-check"></i></span>
                    <span class="menu-title">My Badges</span>
                </a>
            </li>
            
            <li class="nav-item link <?php if(REQUEST == "dashboard/jobs") { ?>active<?php } ?>">
                <a class="nav-link" href="<?= SITE_URL ?>dashboard/jobs">
                    <span class="icon"><i class="fas fa-users"></i></span>
                    <span class="menu-title">Jobs</span>
                </a>
            </li>
            <?php
            if($isActiveSubscription === false) {
                ?>
                <li class="nav-item link <?php if(REQUEST == "dashboard/subscribe") { ?>active<?php } ?>">
                    <a class="nav-link" href="<?= SITE_URL ?>dashboard/subscribe">
                        <span class="icon"><i class="fas fa-medal"></i></span>
                        <span class="menu-title">Unlimited Learning</span>
                    </a>
                </li>
                <?php
            }
            ?>
            <li class="nav-item link <?php if(REQUEST == "dashboard/courses/saved") { ?>active<?php } ?>">
                <a class="nav-link" href="<?= SITE_URL ?>dashboard/courses/saved">
                    <span class="icon"><i class="fas fa-heart"></i></span>
                    <span class="menu-title">Saved Courses</span>
                </a>
            </li>
            <li class="nav-item link <?php if(REQUEST == "messages") { ?>active<?php } ?>">
                <a class="nav-link" href="<?= SITE_URL ?>messages">
                    <span class="icon"><i class="far fa-envelope"></i></span>
                    <span class="menu-title">Messages</span>
                </a>
            </li>
            <?php
            if(($isActiveSubscription === false) && $currency->code == "GBP") {
                ?>
                <li class="nav-item link <?php if(REQUEST == "dashboard/student-card") { ?>active<?php } ?>">
                    <a class="nav-link" href="<?= SITE_URL ?>dashboard/student-card">
                        <span class="icon"><i class="fas fa-id-card"></i></span>
                        <span class="menu-title">Student Card</span>
                    </a>
                </li>
                <?php
            }
            ?>
            <li class="nav-item link <?php if(REQUEST == "dashboard/recommend") { ?>active<?php } ?>">
                <a class="nav-link" href="<?= SITE_URL ?>dashboard/recommend">
                    <span class="icon"><i class="fas fa-users"></i></span>
                    <span class="menu-title">Recommend Friends</span>
                </a>
            </li>
            <?php
            if($isActiveSubscription === false) {
                ?>
                <li class="nav-item link <?php if(REQUEST == "dashboard/offers") { ?>active<?php } ?>">
                    <a class="nav-link" href="<?= SITE_URL ?>dashboard/offers">
                        <span class="icon"><i class="fas fa-percentage"></i></span>
                        <span class="menu-title">Special Offers</span>
                    </a>
                </li>
                <?php
            }
            ?>
            <li class="nav-item link <?php if(REQUEST == "profile") { ?>active<?php } ?>">
                <a class="nav-link" href="<?= SITE_URL ?>profile">
                    <span class="icon"><i class="fas fa-user"></i></span>
                    <span class="menu-title">My Profile</span>
                </a>
            </li>
            <li class="nav-item link <?php if(REQUEST == "dashboard/billing") { ?>active<?php } ?>">
                <a class="nav-link" href="<?= SITE_URL ?>dashboard/billing">
                    <span class="icon"><i class="far fa-pound-sign"></i></span>
                    <span class="menu-title">My Orders</span>
                </a>
            </li>
            <li class="nav-item link <?php if(REQUEST == "dashboard/support") { ?>active<?php } ?>">
                <a class="nav-link" href="<?= SITE_URL ?>dashboard/support">
                    <span class="icon"><i class="far fa-comments"></i></span>
                    <span class="menu-title">Support</span>
                </a>
            </li>
        </ul>
    </div>

    <div class="sidenav-footer  align-items-center">
        <div class="sidenav-footer-items">
            <a href="<?= SITE_URL ?>privacy-notice">Privacy & Security</a>
        </div>
        <div class="sidenav-footer-items">
            <a href="<?= SITE_URL ?>terms-website-use">Terms of Use</a>
        </div>
        <div class="sidenav-footer-items">
            <p><strong>Learning Portal Powered by Be-a Education Ltd</strong></p>
        </div>
        <div class="sidenav-footer-items">
            <p>&copy; New Skills Academy <?= date('Y') ?></p>
        </div>
    </div>
</nav>

<!-- Main Content -->
<main role="main" class="main" id="main">
    <div class="user-menu-collapse">
        <i class="fas fa-angle-left"></i>
        <i class="fas fa-angle-right"></i>
    </div>
    <section class="dashboard-top">
        <div class="container">
            <div class="row align-items-center">
                <div class="top-text col-12 col-md-6 text-left">
                    <?php
                    if(SITE_TYPE == "uk") {
                        ?>
                        Meet fellow New Skills Academy students in our study group. <a href="https://www.facebook.com/groups/353686723019972/" target="_blank">Join now!</a>
                        <?php
                    } else {
                        ?>
                        Welcome to your new look learning dashboard! <a href="<?php echo SITE_URL ?>blog/a-new-look-for-new-skills" target="_blank">Find out more!</a>
                        <?php
                    }
                    ?>
                </div>
                <div class="top-links col-12 col-md-6 text-right d-none d-md-block">
                    <?php include('includes/user/top_links.php');?>
                </div>
            </div>
        </div>
    </section>
<?php } ?>