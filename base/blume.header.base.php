
<!DOCTYPE html>
<html>

<head>
    <!-- -------------- Meta and Title -------------- -->
    <meta charset="utf-8">
    <title><?= $metaTitle ?> | Blume</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- -------------- Fonts -------------- -->
    <link rel='stylesheet' type='text/css' href='https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700'>
    <link href='https://fonts.googleapis.com/css?family=Lato:400,300,300italic,400italic,700,700italic' rel='stylesheet'
          type='text/css'>

    <!-- -------------- Icomoon -------------- -->
    <link rel="stylesheet" type="text/css" href="<?= SITE_URL ?>assets/blume/fonts/icomoon/icomoon.css">

    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css">
    <!-- -------------- FullCalendar -------------- -->
    <link rel="stylesheet" type="text/css" href="<?= SITE_URL ?>assets/blume/js/plugins/fullcalendar/fullcalendar.min.css">
    <link rel="stylesheet" type="text/css" href="<?= SITE_URL ?>assets/blume/js/plugins/magnific/magnific-popup.css">
    <link rel="stylesheet" type="text/css" href="<?= SITE_URL ?>assets/blume/js/plugins/dropzone/css/dropzone.css">
    <link rel="stylesheet" type="text/css" href="<?= SITE_URL ?>assets/blume/js/plugins/summernote/summernote.css">

    <!-- -------------- Plugins -------------- -->
    <link rel="stylesheet" type="text/css" href="<?= SITE_URL ?>assets/blume/js/plugins/c3charts/c3.min.css">

    <!-- -------------- CSS - theme -------------- -->
    <link rel="stylesheet" type="text/css" href="<?= SITE_URL ?>assets/blume/skin/default_skin/css/theme.css?ver=1.1">

    <!-- -------------- CSS - allcp forms -------------- -->
    <link rel="stylesheet" type="text/css" href="<?= SITE_URL ?>assets/blume/allcp/forms/css/forms.css">
    <link rel="stylesheet" type="text/css" href="<?= SITE_URL ?>assets/blume/js/plugins/footable/css/footable.core.min.css">

    <!-- -------------- Favicon -------------- -->
    <link rel="icon" type="image/png" href="<?= SITE_URL ?>assets/images/favicon.png" />

    <script src="<?= SITE_URL ?>assets/blume/js/jquery/jquery-1.11.3.min.js"></script>
    <script src="<?= SITE_URL ?>assets/blume/js/jquery/jquery_ui/jquery-ui.min.js"></script>

    <!-- -------------- IE8 HTML5 support  -------------- -->
    <!--[if lt IE 9]>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.2/html5shiv.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- toasr -->
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <link href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" rel="stylesheet">

    <style>
        #customizer {
            display:none !important;
        }
        #content .panel .panel-heading + .panel-menu {
            margin-top: 12px;
        }
        .label {
            cursor:pointer;
        }
        .label-small {
            font-size: 70%;
            margin-left: 5px;
            position: relative;
            top: -1px;
            margin-right: 5px;
        }
        .btn-small {
            font-size: 10px;
            padding: 4px 12px;
        }
        .modal-dialog {
            margin:100px auto;
        }
        .dataTables_wrapper .dataTables_filter input {
            border: 2px solid #dadada;
            border-radius: 4px;
            margin-bottom: 13px;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current, .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            color: #fff !important;
            border: 0 !important;
            background-color: white !important;
            background: #67d3e0 !important;
        }

        .dataTables_wrapper .dataTables_length, .dataTables_wrapper .dataTables_filter, .dataTables_wrapper .dataTables_info, .dataTables_wrapper .dataTables_processing, .dataTables_wrapper .dataTables_paginate {
            color: #adadad !important
        }
        .dataTables_length select {
            border:0;
        }
        .table > thead > tr > th {
            text-transform:none;
        }
        .table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td {
            padding:11px;
        }
        .dataTables_wrapper .dataTables_processing {
            background: #67d3e0 !important;
            width: 200px !important;
            color: rgb(255, 255, 255) !important;
            line-height: 0px;
            border-radius: 8px;
            left: 0 !important;
            right: 0;
            margin: auto;
            top: 53px !important;
        }
        .ncfeUser {
            background: black;
            color: #fff;
            padding: 2px 10px;
            font-size: 10px;
            border-radius: 5px;
        }
    </style>
    <script>
        var SITE_URL_JS = '<?= SITE_URL?>';
    </script>
</head>

<body class="dashboard-page sb-l-o sb-r-c mobile-view chute-rescale onload-check sb-l-m sb-l-disable-animation">

<!-- -------------- Customizer -------------- -->
<div id="customizer" class="hidden-xs">
    <div class="panel">
        <div class="panel-heading">
        <span class="panel-icon">
          <i class="fa fa-cogs"></i>
        </span>
            <span class="panel-title"> Theme Options</span>
        </div>
        <div class="panel-body pn">
            <ul class="nav nav-list nav-list-sm" role="tablist">
                <li class="active">
                    <a href="#customizer-header" role="tab" data-toggle="tab">Navbar</a>
                </li>
                <li>
                    <a href="#customizer-sidebar" role="tab" data-toggle="tab">Sidebar</a>
                </li>
                <li>
                    <a href="#customizer-settings" role="tab" data-toggle="tab">Misc</a>
                </li>
            </ul>
            <div class="tab-content p20 ptn pb15">
                <div role="tabpanel" class="tab-pane active" id="customizer-header">
                    <form id="customizer-header-skin">
                        <h6 class="mv20">Header Skins</h6>

                        <div class="customizer-sample">
                            <table>
                                <tr>
                                    <td>
                                        <div class="checkbox-custom fill checkbox-dark mb10">
                                            <input type="radio" name="headerSkin" id="headerSkin5" checked
                                                   value="bg-dark">
                                            <label for="headerSkin5">Dark</label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="checkbox-custom fill checkbox-warning mb10">
                                            <input type="radio" name="headerSkin" id="headerSkin2" value="bg-warning">
                                            <label for="headerSkin2">Warning</label>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="checkbox-custom fill checkbox-danger mb10">
                                            <input type="radio" name="headerSkin" id="headerSkin3" value="bg-danger">
                                            <label for="headerSkin3">Danger</label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="checkbox-custom fill checkbox-success mb10">
                                            <input type="radio" name="headerSkin" id="headerSkin4" value="bg-success">
                                            <label for="headerSkin4">Success</label>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="checkbox-custom fill checkbox-primary mb10">
                                            <input type="radio" name="headerSkin" id="headerSkin6" value="bg-primary">
                                            <label for="headerSkin6">Primary</label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="checkbox-custom fill checkbox-info mb10">
                                            <input type="radio" name="headerSkin" id="headerSkin7" value="bg-info">
                                            <label for="headerSkin7">Info</label>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="checkbox-custom fill checkbox-alert mb10">
                                            <input type="radio" name="headerSkin" id="headerSkin8" value="bg-alert">
                                            <label for="headerSkin8">Alert</label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="checkbox-custom fill checkbox-system mb10">
                                            <input type="radio" name="headerSkin" id="headerSkin9" value="bg-system">
                                            <label for="headerSkin9">System</label>
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <div class="checkbox-custom checkbox-disabled fill mb10">
                                <input type="radio" name="headerSkin" id="headerSkin1" value="bgc-light">
                                <label for="headerSkin1">Light</label>
                            </div>
                        </div>
                    </form>
                    <form id="customizer-footer-skin">
                        <h6 class="mv20">Footer Skins</h6>

                        <div class="customizer-sample">
                            <table>
                                <tr>
                                    <td>
                                        <div class="checkbox-custom fill checkbox-dark mb10">
                                            <input type="radio" name="footerSkin" id="footerSkin1" checked value="">
                                            <label for="footerSkin1">Dark</label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="checkbox-custom checkbox-disabled fill mb10">
                                            <input type="radio" name="footerSkin" id="footerSkin2" value="footer-light">
                                            <label for="footerSkin2">Light</label>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </form>
                </div>
                <div role="tabpanel" class="tab-pane" id="customizer-sidebar">
                    <form id="customizer-sidebar-skin">
                        <h6 class="mv20">Sidebar Skins</h6>

                        <div class="customizer-sample">
                            <div class="checkbox-custom fill checkbox-dark mb10">
                                <input type="radio" name="sidebarSkin" checked id="sidebarSkin2" value="">
                                <label for="sidebarSkin2">Dark</label>
                            </div>
                            <div class="checkbox-custom fill checkbox-disabled mb10">
                                <input type="radio" name="sidebarSkin" id="sidebarSkin1" value="sidebar-light">
                                <label for="sidebarSkin1">Light</label>
                            </div>
                        </div>
                    </form>
                </div>
                <div role="tabpanel" class="tab-pane" id="customizer-settings">
                    <form id="customizer-settings-misc">
                        <h6 class="mv20 mtn">Layout Options</h6>

                        <div class="form-group">
                            <div class="checkbox-custom fill mb10">
                                <input type="checkbox" checked="" id="header-option">
                                <label for="header-option">Fixed Header</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="checkbox-custom fill mb10">
                                <input type="checkbox" checked="" id="sidebar-option">
                                <label for="sidebar-option">Fixed Sidebar</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="checkbox-custom fill mb10">
                                <input type="checkbox" id="breadcrumb-option">
                                <label for="breadcrumb-option">Fixed Breadcrumbs</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="checkbox-custom fill mb10">
                                <input type="checkbox" id="breadcrumb-hidden">
                                <label for="breadcrumb-hidden">Hide Breadcrumbs</label>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="form-group mn pb35 pt25 text-center">
                <a href="#" id="clearAll" class="btn btn-primary btn-bordered btn-sm">Clear All</a>
            </div>
        </div>
    </div>
</div>
<!-- -------------- /Customizer -------------- -->

<!-- -------------- Body Wrap  -------------- -->
<div id="main">

    <!-- -------------- Header  -------------- -->
    <header class="navbar navbar-fixed-top bg-dark">
        <div class="navbar-logo-wrapper">
            <a class="navbar-logo-text" href="dashboard1.html">
                <b><?= SITE_NAME ?></b>
            </a>
            <span id="sidebar_left_toggle" class="ad ad-lines"></span>
        </div>
        <ul class="nav navbar-nav navbar-left">
            <li class="dropdown dropdown-fuse hidden-xs">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Admin

                </a>
                <ul class="dropdown-menu" role="menu">
                    <li><a href="#">Action</a></li>
                    <li><a href="#">One more action</a></li>
                    <li><a href="#">More actions if needed</a></li>
                    <li class="divider"></li>
                    <li><a href="#">Separated action</a></li>
                </ul>
            </li>
            <li class="hidden-xs">
                <a class="navbar-fullscreen toggle-active" href="#">
                    <span class="glyphicon glyphicon-fullscreen"></span>
                </a>
            </li>
        </ul>
        <form class="navbar-form navbar-left search-form square" role="search" style="display:none;">
            <div class="input-group add-on">

                <input type="text" class="form-control" placeholder="Search..." onfocus="this.placeholder=''"
                       onblur="this.placeholder='Search...'">

                <div class="input-group-btn">
                    <button class="btn btn-default" type="submit"><i class="glyphicon glyphicon-search"></i></button>
                </div>

            </div>
        </form>
        <ul class="nav navbar-nav navbar-right">
            <li class="hidden-xs" style="display:none;">>
                <div class="navbar-btn btn-group">
                    <a href="#" class="topbar-dropmenu-toggle btn" data-toggle="button">
                        <span class="fa fa-magic fs20 text-info"></span>
                    </a>
                </div>
            </li>
            <li class="dropdown dropdown-fuse" style="display:none;">>
                <div class="navbar-btn btn-group">
                    <button data-toggle="dropdown" class="btn dropdown-toggle">
                        <span class="fa fa-envelope fs20 text-danger"></span>
                    </button>
                    <button data-toggle="dropdown" class="btn dropdown-toggle fs18 visible-xl">
                        3
                    </button>
                    <div class="dropdown-menu keep-dropdown w375 animated animated-shorter fadeIn" role="menu">
                        <div class="panel mbn">
                            <div class="panel-menu">
                                <div class="btn-group btn-group-justified btn-group-nav" role="tablist">
                                    <a href="#nav-tab1" data-toggle="tab"
                                       class="btn btn-primary btn-bordered btn-sm active">Activity</a>
                                    <a href="#nav-tab2" data-toggle="tab"
                                       class="btn btn-primary btn-bordered btn-sm br-l-n br-r-n">Messages</a>
                                    <a href="#nav-tab3" data-toggle="tab" class="btn btn-primary btn-bordered btn-sm">Notifications</a>
                                </div>
                            </div>
                            <div class="panel-body panel-scroller scroller-overlay scroller-navbar pn">
                                <div class="tab-content br-n pn">
                                    <div id="nav-tab1" class="tab-pane active" role="tabpanel">
                                        <ul class="media-list" role="menu">
                                            <li class="media">
                                                <a class="media-left" href="#"> <img src="<?= SITE_URL ?>assets/blume/img/avatars/5.jpg"
                                                                                     class="mw40 br2"
                                                                                     alt="avatar">
                                                </a>

                                                <div class="media-body">
                                                    <h5 class="media-heading">New post
                                                        <small class="text-muted">- 09/01/15</small>
                                                    </h5>
                                                    Last Updated 5 days ago by
                                                    <a class="" href="#"> John Doe </a>
                                                </div>
                                            </li>
                                            <li class="media">
                                                <a class="media-left" href="#"> <img src="<?= SITE_URL ?>assets/blume/img/avatars/2.jpg"
                                                                                     class="mw40 br2"
                                                                                     alt="avatar">
                                                </a>

                                                <div class="media-body">
                                                    <h5 class="media-heading">New post
                                                        <small> - 09/01/15</small>
                                                    </h5>
                                                    Last Updated 5 days ago by
                                                    <a class="" href="#"> John Doe </a>
                                                </div>
                                            </li>
                                            <li class="media">
                                                <a class="media-left" href="#"> <img src="<?= SITE_URL ?>assets/blume/img/avatars/3.jpg"
                                                                                     class="mw40 br2"
                                                                                     alt="avatar">
                                                </a>

                                                <div class="media-body">
                                                    <h5 class="media-heading">New post
                                                        <small class="text-muted">- 09/01/15</small>
                                                    </h5>
                                                    Last Updated 5 days ago by
                                                    <a class="" href="#"> John Doe </a>
                                                </div>
                                            </li>
                                            <li class="media">
                                                <a class="media-left" href="#"> <img src="<?= SITE_URL ?>assets/blume/img/avatars/4.jpg"
                                                                                     class="mw40 br2"
                                                                                     alt="avatar">
                                                </a>

                                                <div class="media-body">
                                                    <h5 class="media-heading">New post
                                                        <small class="text-muted">- 09/01/15</small>
                                                    </h5>
                                                    Last Updated 5 days ago by
                                                    <a class="" href="#"> John Doe </a>
                                                </div>
                                            </li>
                                            <li class="media">
                                                <a class="media-left" href="#"> <img src="<?= SITE_URL ?>assets/blume/img/avatars/5.jpg"
                                                                                     class="mw40 br2"
                                                                                     alt="avatar">
                                                </a>

                                                <div class="media-body">
                                                    <h5 class="media-heading">New post
                                                        <small class="text-muted">- 09/01/15</small>
                                                    </h5>
                                                    Last Updated 5 days ago by
                                                    <a class="" href="#"> John Doe </a>
                                                </div>
                                            </li>
                                            <li class="media">
                                                <a class="media-left" href="#"> <img src="<?= SITE_URL ?>assets/blume/img/avatars/2.jpg"
                                                                                     class="mw40 br2"
                                                                                     alt="avatar">
                                                </a>

                                                <div class="media-body">
                                                    <h5 class="media-heading">New post
                                                        <small> - 09/01/15</small>
                                                    </h5>
                                                    Last Updated 5 days ago by
                                                    <a class="" href="#"> John Doe </a>
                                                </div>
                                            </li>
                                            <li class="media">
                                                <a class="media-left" href="#"> <img src="<?= SITE_URL ?>assets/blume/img/avatars/3.jpg"
                                                                                     class="mw40 br2"
                                                                                     alt="avatar">
                                                </a>

                                                <div class="media-body">
                                                    <h5 class="media-heading">New post
                                                        <small class="text-muted">- 09/01/15</small>
                                                    </h5>
                                                    Last Updated 5 days ago by
                                                    <a class="" href="#"> John Doe </a>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                    <div id="nav-tab2" class="tab-pane chat-widget" role="tabpanel">
                                        <div class="media">
                                            <div class="media-left">
                                                <a href="#">
                                                    <img class="media-object" alt="64x64"
                                                         src="<?= SITE_URL ?>assets/blume/img/avatars/3.jpg">
                                                </a>
                                            </div>
                                            <div class="media-body">
                                                <span class="media-status online"></span>
                                                <h5 class="media-heading">Frank Hill
                                                    <small> - 14:10am</small>
                                                </h5>
                                                Lorem ipsum dolor sit amet, consectetuer adipiscing elit.
                                            </div>
                                        </div>
                                        <div class="media">
                                            <div class="media-body">
                                                <span class="media-status offline"></span>
                                                <h5 class="media-heading">George Kelly
                                                    <small> - 15:25am</small>
                                                </h5>
                                                Sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam
                                                erat volutpat.
                                            </div>
                                            <div class="media-right">
                                                <a href="#">
                                                    <img class="media-object" alt="64x64"
                                                         src="<?= SITE_URL ?>assets/blume/img/avatars/1.jpg">
                                                </a>
                                            </div>
                                        </div>
                                        <div class="media">
                                            <div class="media-left">
                                                <a href="#">
                                                    <img class="media-object" alt="64x64"
                                                         src="<?= SITE_URL ?>assets/blume/img/avatars/2.jpg">
                                                </a>
                                            </div>
                                            <div class="media-body">
                                                <span class="media-status online"></span>
                                                <h5 class="media-heading">Frank Hill
                                                    <small> - 15:33am</small>
                                                </h5>
                                                Lorem ipsum dolor sit amet, nonummy nibh euismod tinc consectetuer
                                                adipiscing elit.
                                            </div>
                                        </div>
                                        <div class="media">
                                            <div class="media-body">
                                                <span class="media-status offline"></span>
                                                <h5 class="media-heading">George Kelly
                                                    <small> - 15:43am</small>
                                                </h5>
                                                Euismod sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna
                                                aliquam erat volutpat.
                                            </div>
                                            <div class="media-right">
                                                <a href="#">
                                                    <img class="media-object" alt="64x64"
                                                         src="<?= SITE_URL ?>assets/blume/img/avatars/1.jpg">
                                                </a>
                                            </div>
                                        </div>
                                        <div class="media">
                                            <div class="media-left">
                                                <a href="#">
                                                    <img class="media-object" alt="64x64"
                                                         src="<?= SITE_URL ?>assets/blume/img/avatars/2.jpg">
                                                </a>
                                            </div>
                                            <div class="media-body">
                                                <span class="media-status online"></span>
                                                <h5 class="media-heading">Frank Hill
                                                    <small> - 16:30am</small>
                                                </h5>
                                                Corem ipsum dolor sit amet, nonummy nibh euismod tinc co.
                                            </div>
                                        </div>
                                        <div class="media">
                                            <div class="media-body">
                                                <span class="media-status offline"></span>
                                                <h5 class="media-heading">George Kelly
                                                    <small> - 12:30am</small>
                                                </h5>
                                                Ubh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
                                            </div>
                                            <div class="media-right">
                                                <a href="#">
                                                    <img class="media-object" alt="64x64"
                                                         src="<?= SITE_URL ?>assets/blume/img/avatars/1.jpg">
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="nav-tab3" class="tab-pane alerts-widget" role="tabpanel">
                                        <div class="media">
                                            <a class="media-left" href="#"> <span
                                                    class="fa fa-shopping-cart text-success"></span> </a>

                                            <div class="media-body">
                                                <h5 class="media-heading">New Product Order
                                                    <small class="text-muted"></small>
                                                </h5>
                                                <a href="#">iPad Air</a> - 3 hours ago
                                            </div>
                                            <div class="media-right">
                                                <div class="media-response"> Confirm?</div>
                                                <div class="btn-group">
                                                    <button type="button"
                                                            class="btn btn-default btn-sm btn-bordered light">
                                                        <i class="fa fa-check text-success"></i>
                                                    </button>
                                                    <button type="button"
                                                            class="btn btn-default btn-sm btn-bordered light">
                                                        <i class="fa fa-cog"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="media">
                                            <a class="media-left" href="#"> <span
                                                    class="fa fa-comment text-system"></span>
                                            </a>

                                            <div class="media-body">
                                                <h5 class="media-heading">New User Comment
                                                    <small class="text-muted"></small>
                                                </h5>
                                                Sam Fisher - I'd like to read more!
                                            </div>
                                            <div class="media-right">
                                                <div class="media-response text-right"> Moderate?</div>
                                                <div class="btn-group">
                                                    <button type="button"
                                                            class="btn btn-default btn-sm btn-bordered light">
                                                        <i class="fa fa-check text-success"></i>
                                                    </button>
                                                    <button type="button"
                                                            class="btn btn-default btn-sm btn-bordered light">
                                                        <i class="fa fa-pencil"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="media">
                                            <a class="media-left" href="#"> <span class="fa fa-eye text-warning"></span>
                                            </a>

                                            <div class="media-body">
                                                <h5 class="media-heading">New User Review
                                                    <small class="text-muted"></small>
                                                </h5>
                                                Sebastian Jones - 5 hours ago
                                            </div>
                                            <div class="media-right">
                                                <div class="media-response"> Approve?</div>
                                                <div class="btn-group">
                                                    <button type="button"
                                                            class="btn btn-default btn-sm btn-bordered light">
                                                        <i class="fa fa-check text-success"></i>
                                                    </button>
                                                    <button type="button"
                                                            class="btn btn-default btn-sm btn-bordered light">
                                                        <i class="fa fa-remove"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="media">
                                            <a class="media-left" href="#"> <span class="fa fa-user text-info"></span>
                                            </a>

                                            <div class="media-body">
                                                <h5 class="media-heading">New User Registration
                                                    <small class="text-muted"></small>
                                                </h5>
                                                Carlos Santiyago - 7 hours ago
                                            </div>
                                            <div class="media-right">
                                                <div class="media-response"> Approve?</div>
                                                <div class="btn-group">
                                                    <button type="button"
                                                            class="btn btn-default btn-sm btn-bordered light">
                                                        <i class="fa fa-check text-success"></i>
                                                    </button>
                                                    <button type="button"
                                                            class="btn btn-default btn-sm btn-bordered light">
                                                        <i class="fa fa-remove"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="media">
                                            <a class="media-left" href="#"> <span class="fa fa-user text-info"></span>
                                            </a>

                                            <div class="media-body">
                                                <h5 class="media-heading">New User Registration
                                                    <small class="text-muted"></small>
                                                </h5>
                                                Douglas Adams - 13 hours ago

                                            </div>
                                            <div class="media-right">
                                                <div class="media-response"> Approve?</div>
                                                <div class="btn-group">
                                                    <button type="button"
                                                            class="btn btn-default btn-sm btn-bordered light">
                                                        <i class="fa fa-check text-success"></i>
                                                    </button>
                                                    <button type="button"
                                                            class="btn btn-default btn-sm btn-bordered light">
                                                        <i class="fa fa-remove"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="media">
                                            <a class="media-left" href="#"> <span class="fa fa-info text-alert"></span>
                                            </a>

                                            <div class="media-body">
                                                <h5 class="media-heading">New Invoice
                                                    <small class="text-muted"></small>
                                                </h5>
                                                <a href="#">iPad Air</a> - 14 hours ago

                                            </div>
                                            <div class="media-right">
                                                <div class="media-response single">#1234567</div>
                                                <button type="button" class="btn btn-default btn-sm btn-bordered light">
                                                    <i class="fa fa-link"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="media">
                                            <a class="media-left" href="#"> <span
                                                    class="fa fa-shopping-cart text-success"></span> </a>

                                            <div class="media-body">
                                                <h5 class="media-heading">New Product Order
                                                    <small class="text-muted"></small>
                                                </h5>
                                                <a href="#">iPad Air</a> - 14 hours ago
                                            </div>
                                            <div class="media-right">
                                                <div class="media-response"> Confirm?</div>
                                                <div class="btn-group">
                                                    <button type="button"
                                                            class="btn btn-default btn-sm btn-bordered light">
                                                        <i class="fa fa-check text-success"></i>
                                                    </button>
                                                    <button type="button"
                                                            class="btn btn-default btn-sm btn-bordered light">
                                                        <i class="fa fa-cog"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-footer text-center">
                                <a href="#" class="btn btn-primary btn-sm btn-bordered"> View All </a>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
            <li class="dropdown dropdown-fuse" style="display:none;">>
                <div class="navbar-btn btn-group">
                    <button data-toggle="dropdown" class="btn dropdown-toggle">
                        <span class="fa fa-bell fs20 text-primary"></span>
                    </button>
                    <button data-toggle="dropdown" class="btn dropdown-toggle fs18 visible-xl">
                        8
                    </button>
                    <div class="dropdown-menu keep-dropdown w375 animated animated-shorter fadeIn" role="menu">
                        <div class="panel mbn">
                            <div class="panel-menu">
                                <span class="panel-icon"><i class="fa fa-tasks"></i></span>
                                <span class="panel-title fw600"> Activity reports</span>
                                <button class="btn btn-default light btn-xs btn-bordered pull-right" type="button"><i
                                        class="fa fa-refresh"></i>
                                </button>
                            </div>
                            <div class="panel-body panel-scroller scroller-navbar scroller-overlay scroller-pn pn">
                                <ol class="timeline-list">
                                    <li class="timeline-item">
                                        <div class="timeline-icon bg-dark light">
                                            <span class="fa fa-envelope"></span>
                                        </div>
                                        <div class="timeline-desc">
                                            <b>John Doe</b> Sent you a message.
                                            <a href="#">View now</a>
                                        </div>
                                        <div class="timeline-date">11:15am</div>
                                    </li>
                                    <li class="timeline-item">
                                        <div class="timeline-icon bg-success">
                                            <span class="fa fa-info"></span>
                                        </div>
                                        <div class="timeline-desc">
                                            <b>Admin</b> created invoice for:
                                            <a href="#">iPad Air</a>
                                        </div>
                                        <div class="timeline-date">6:26pm</div>
                                    </li>
                                    <li class="timeline-item">
                                        <div class="timeline-icon bg-success">
                                            <span class="fa fa-info"></span>
                                        </div>
                                        <div class="timeline-desc">
                                            <b>Admin</b> created invoice for:
                                            <a href="#">iPhone 5s</a>
                                        </div>
                                        <div class="timeline-date">11:45am</div>
                                    </li>
                                    <li class="timeline-item">
                                        <div class="timeline-icon bg-dark light">
                                            <span class="fa fa-envelope"></span>
                                        </div>
                                        <div class="timeline-desc">
                                            <b>Lara Johnes</b> Sent you a message.
                                            <a href="#">View now</a>
                                        </div>
                                        <div class="timeline-date">3:18pm</div>
                                    </li>
                                    <li class="timeline-item">
                                        <div class="timeline-icon bg-primary">
                                            <span class="fa fa-star"></span>
                                        </div>
                                        <div class="timeline-desc">
                                            <b>Richard Johnes</b> Added to wishlist:
                                            <a href="#">iPhone 5c</a>
                                        </div>
                                        <div class="timeline-date">8:15am</div>
                                    </li>
                                    <li class="timeline-item">
                                        <div class="timeline-icon bg-success">
                                            <span class="fa fa-info"></span>
                                        </div>
                                        <div class="timeline-desc">
                                            <b>Admin</b> created invoice for:
                                            <a href="#">Mac Pro</a>
                                        </div>
                                        <div class="timeline-date">9:29pm</div>
                                    </li>
                                    <li class="timeline-item">
                                        <div class="timeline-icon bg-primary">
                                            <span class="fa fa-star"></span>
                                        </div>
                                        <div class="timeline-desc">
                                            <b>Douglas Adams</b> Added to wishlist:
                                            <a href="#">iPad 4</a>
                                        </div>
                                        <div class="timeline-date">3:05am</div>
                                    </li>
                                </ol>

                            </div>
                            <div class="panel-footer text-center">
                                <a href="#" class="btn btn-primary btn-sm btn-bordered"> View All </a>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
            <li class="dropdown dropdown-fuse" style="display:none;">
                <div class="navbar-btn btn-group">
                    <button data-toggle="dropdown" class="btn btn-md dropdown-toggle">
                        EN
                    </button>
                    <ul class="dropdown-menu pv5 animated animated-short fadeIn" role="menu">
                        <li>
                            <a href="javascript:void(0);"> Spanish </a>
                        </li>
                        <li>
                            <a href="javascript:void(0);"> Italian </a>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="dropdown dropdown-fuse">
                <a href="#" class="dropdown-toggle fw600" data-toggle="dropdown">
                    <span class="hidden-xs"><name><?= $_SESSION['adminFirstname'].' '.$_SESSION['adminLastname'] ?></name> </span>
                    <span class="fa fa-caret-down hidden-xs mr15"></span>
                </a>
                <ul class="dropdown-menu list-group keep-dropdown w250" role="menu">
                    <li class="list-group-item">
                        <a href="<?= SITE_URL ?>" target="_blank" class="animated animated-short fadeInUp">
                            <span class="fa fa-eye"></span> View Site
                        </a>
                    </li>
                    <li class="list-group-item">
                        <a href="<?= SITE_URL ?>blume/account" target="_blank" class="animated animated-short fadeInUp">
                            <span class="fa fa-user"></span> Change Password
                        </a>
                    </li>
                    <li class="dropdown-footer text-center">
                        <a href="<?= SITE_URL ?>ajax?c=blumeNew&a=sign-out" class="btn btn-primary btn-sm btn-bordered">
                            <span class="fa fa-power-off pr5"></span> Logout </a>
                    </li>
                </ul>
            </li>
        </ul>
    </header>
    <!-- -------------- /Header  -------------- -->

    <!-- -------------- Sidebar  -------------- -->
    <aside id="sidebar_left" class="nano nano-light affix">

        <!-- -------------- Sidebar Left Wrapper  -------------- -->
        <div class="sidebar-left-content nano-content">

            <!-- -------------- Sidebar Header -------------- -->
            <header class="sidebar-header">

                <!-- -------------- Sidebar - Author -------------- -->
                <div class="sidebar-widget author-widget" style="display:none;">>
                    <div class="media">
                        <a class="media-left" href="#">
                            <img src="<?= SITE_URL ?>assets/blume/img/avatars/profile_avatar.jpg" class="img-responsive">
                        </a>

                        <div class="media-body">
                            <div class="media-links">
                                <a href="#" class="sidebar-menu-toggle">User Menu -</a> <a href="utility-login.html">Logout</a>
                            </div>
                            <div class="media-author">Douglas Adams</div>
                        </div>
                    </div>
                </div>

                <!-- -------------- Sidebar - Author Menu  -------------- -->
                <div class="sidebar-widget menu-widget">
                    <div class="row text-center mbn">
                        <div class="col-xs-2 pln prn">
                            <a href="dashboard1.html" class="text-primary" data-toggle="tooltip" data-placement="top"
                               title="Dashboard">
                                <span class="fa fa-dashboard"></span>
                            </a>
                        </div>
                        <div class="col-xs-4 col-sm-2 pln prn">
                            <a href="charts-highcharts.html" class="text-info" data-toggle="tooltip"
                               data-placement="top" title="Stats">
                                <span class="fa fa-bar-chart-o"></span>
                            </a>
                        </div>
                        <div class="col-xs-4 col-sm-2 pln prn">
                            <a href="sales-stats-products.html" class="text-system" data-toggle="tooltip"
                               data-placement="top" title="Orders">
                                <span class="fa fa-info-circle"></span>
                            </a>
                        </div>
                        <div class="col-xs-4 col-sm-2 pln prn">
                            <a href="sales-stats-purchases.html" class="text-warning" data-toggle="tooltip"
                               data-placement="top" title="Invoices">
                                <span class="fa fa-file"></span>
                            </a>
                        </div>
                        <div class="col-xs-4 col-sm-2 pln prn">
                            <a href="basic-profile.html" class="text-alert" data-toggle="tooltip" data-placement="top"
                               title="Users">
                                <span class="fa fa-users"></span>
                            </a>
                        </div>
                        <div class="col-xs-4 col-sm-2 pln prn">
                            <a href="management-tools-dock.html" class="text-danger" data-toggle="tooltip"
                               data-placement="top" title="Settings">
                                <span class="fa fa-cogs"></span>
                            </a>
                        </div>
                    </div>
                </div>

            </header>
            <!-- -------------- /Sidebar Header -------------- -->

            <!-- -------------- Sidebar Menu  -------------- -->
            <ul class="nav sidebar-menu">
                <?php
                $accountAdmin = ORM::for_table("blumeUsers")->find_one(CUR_ID);

                if($accountAdmin->role == "customer_service") {
                    ?>
                    <li>
                        <a href="<?= SITE_URL ?>blume/dashboard">
                            <span class="fa fa-dashboard"></span>
                            <span class="sidebar-title">Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= SITE_URL ?>customer-service-portal" target="_blank">
                            <span class="fa fa-envelope"></span>
                            <span class="sidebar-title">Customer Service Portal</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= SITE_URL ?>blume/accounts">
                            <span class="fa fa-users"></span>
                            <span class="sidebar-title">Accounts</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= SITE_URL ?>blume/marketing/coupons">
                            <span class="fa fa-file"></span>
                            <span class="sidebar-title">Discount Coupons</span>
                        </a>
                    </li>
                    <li class="toggle-sub-nav">
                        <a href="#">
                            <span class="fa fa-gbp"></span>
                            <span class="sidebar-title">Orders</span>
                            <span class="fa fa-caret-down" style="position: absolute;right: 53px;top: 11px;"></span>
                        </a>
                        <ul class="nav sub-nav">
                            <li>
                                <a href="<?= SITE_URL ?>blume/orders">
                                    <span class="fa fa-file-text-o"></span> Completed</a>
                            </li>
                            <li>
                                <a href="<?= SITE_URL ?>blume/orders/abandoned">
                                    <span class="fa fa-file-text-o"></span> Abandoned</a>
                            </li>
                            <li>
                                <a href="<?= SITE_URL ?>blume/orders/certificates">
                                    <span class="fa fa-file-text-o"></span> Certificates</a>
                            </li>
                            <li>
                                <a href="<?= SITE_URL ?>blume/orders/qualifications">
                                    <span class="fa fa-file-text-o"></span> Qualification Orders</a>
                            </li>
                            <li>
                                <a href="<?= SITE_URL ?>blume/orders/printed">
                                    <span class="fa fa-file-text-o"></span> Printed Courses</a>
                            </li>
                            <li>
                                <a href="<?= SITE_URL ?>blume/orders/gifted">
                                    <span class="fa fa-file-text-o"></span> Gifted Courses</a>
                            </li>
                            <li>
                                <a href="<?= SITE_URL ?>blume/orders/subscriptions">
                                    <span class="fa fa-file-text-o"></span> Instalments</a>
                            </li>
                            <li>
                                <a href="<?= SITE_URL ?>blume/orders/gifted-vouchers">
                                    <span class="fa fa-file-text-o"></span> Gifted Vouchers</a>
                            </li>
                        </ul>
                    </li>
                    <?php
                } else { ?>

                <li>
                    <a href="<?= SITE_URL ?>blume/dashboard">
                        <span class="fa fa-dashboard"></span>
                        <span class="sidebar-title">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="<?= SITE_URL ?>customer-service-portal" target="_blank">
                        <span class="fa fa-envelope"></span>
                        <span class="sidebar-title">Customer Service Portal</span>
                    </a>
                </li>
                <li>
                    <a href="<?= SITE_URL ?>blume/accounts">
                        <span class="fa fa-users"></span>
                        <span class="sidebar-title">Accounts</span>
                    </a>
                </li>
                <li class="toggle-sub-nav">
                    <a href="#">
                        <span class="fa fa-graduation-cap"></span>
                        <span class="sidebar-title">Courses (<?= ORM::for_table("courseReviews")->where("status", "p")->count() ?>)</span>
                        <span class="fa fa-caret-down" style="position: absolute;right: 53px;top: 11px;"></span>
                    </a>
                    <ul class="nav sub-nav">
                        <li>
                            <a href="<?= SITE_URL ?>blume/courses">
                                <span class="fa fa-file-text-o"></span> Manage</a>
                        </li>
                        <li>
                            <a href="<?= SITE_URL ?>blume/courses/categories">
                                <span class="fa fa-file-text-o"></span> Categories</a>
                        </li>
                        <li>
                            <a href="<?= SITE_URL ?>blume/courses/providers">
                                <span class="fa fa-file-text-o"></span> Providers</a>
                        </li>
                        <li>
                            <a href="<?= SITE_URL ?>blume/courses/reviews">
                                <span class="fa fa-file-text-o"></span> Reviews (<?= ORM::for_table("courseReviews")->where("status", "p")->count() ?>)</a>
                        </li>
                        <li>
                            <a href="<?= SITE_URL ?>blume/courses/ratings">
                                <span class="fa fa-file-text-o"></span> Ratings</a>
                        </li>
                        <li>
                            <a href="<?= SITE_URL ?>blume/courses/relations">
                                <span class="fa fa-file-text-o"></span> Relations</a>
                        </li>
                        <li>
                            <a href="<?= SITE_URL ?>blume/courses/modules/faqs">
                                <span class="fa fa-file-text-o"></span> Module FAQ's</a>
                        </li>
                    </ul>
                </li>
                <li class="toggle-sub-nav">
                    <a href="#">
                        <span class="fa fa-envelope"></span>
                        <span class="sidebar-title">Support</span>
                        <span class="fa fa-caret-down" style="position: absolute;right: 53px;top: 11px;"></span>
                    </a>
                    <ul class="nav sub-nav">
                        <li>
                            <a href="<?= SITE_URL ?>blume/support/help-articles">
                                <span class="fa fa-file-text-o"></span> Help Articles</a>
                        </li>
                        <li>
                            <a href="<?= SITE_URL ?>blume/support/resources">
                                <span class="fa fa-file-text-o"></span> Resources</a>
                        </li>
                        <li>
                            <a href="<?= SITE_URL ?>blume/support/blog">
                                <span class="fa fa-file-text-o"></span> Blog </a>
                        </li>
                        <li>
                            <a href="<?= SITE_URL ?>blume/support/blog/categories">
                                <span class="fa fa-file-text-o"></span> Blog Categories</a>
                        </li>
                        <li>
                            <a href="<?= SITE_URL ?>blume/support/faqs">
                                <span class="fa fa-file-text-o"></span> FAQ's </a>
                        </li>
                        <li>
                            <a href="<?= SITE_URL ?>blume/support/help-videos">
                                <span class="fa fa-file-text-o"></span> Help Videos </a>
                        </li>
                        <li>
                            <a href="<?= SITE_URL ?>blume/support/learning-request">
                                <span class="fa fa-file-text-o"></span> Learning Request </a>
                        </li>
                    </ul>
                </li>
                <li class="toggle-sub-nav">
                    <a href="#">
                        <span class="fa fa-edit"></span>
                        <span class="sidebar-title">Content</span>
                        <span class="fa fa-caret-down" style="position: absolute;right: 53px;top: 11px;"></span>
                    </a>
                    <ul class="nav sub-nav">
                        <li>
                            <a href="<?= SITE_URL ?>blume/content/faqs">
                                <span class="fa fa-file-text-o"></span> FAQ's</a>
                        </li>
                        <li>
                            <a href="<?= SITE_URL ?>blume/pages">
                                <span class="fa fa-file-text-o"></span> Static Pages</a>
                        </li>
                        <li>
                            <a href="<?= SITE_URL ?>blume/content/testimonials">
                                <span class="fa fa-file-text-o"></span> Testimonials</a>
                        </li>
                        <li>
                            <a href="<?= SITE_URL ?>blume/content/team">
                                <span class="fa fa-file-text-o"></span> Team / Staff</a>
                        </li>
                        <li>
                            <a href="<?= SITE_URL ?>blume/content/affiliate-faqs">
                                <span class="fa fa-file-text-o"></span> Affiliate FAQ's</a>
                        </li>
                        <li>
                            <a href="<?= SITE_URL ?>blume/content/email-templates">
                                <span class="fa fa-file-text-o"></span> Email Templates</a>
                        </li>
                    </ul>
                </li>
                    <li class="toggle-sub-nav">
                        <a href="#">
                            <span class="fa fa-gbp"></span>
                            <span class="sidebar-title">Orders</span>
                            <span class="fa fa-caret-down" style="position: absolute;right: 53px;top: 11px;"></span>
                        </a>
                        <ul class="nav sub-nav">
                            <li>
                                <a href="<?= SITE_URL ?>blume/orders">
                                    <span class="fa fa-file-text-o"></span> Completed</a>
                            </li>
                            <li>
                                <a href="<?= SITE_URL ?>blume/orders/abandoned">
                                    <span class="fa fa-file-text-o"></span> Abandoned</a>
                            </li>
                            <li>
                                <a href="<?= SITE_URL ?>blume/orders/certificates">
                                    <span class="fa fa-file-text-o"></span> Certificates</a>
                            </li>
                            <li>
                                <a href="<?= SITE_URL ?>blume/orders/qualifications">
                                    <span class="fa fa-file-text-o"></span> Qualification Orders</a>
                            </li>
                            <li>
                                <a href="<?= SITE_URL ?>blume/orders/printed">
                                    <span class="fa fa-file-text-o"></span> Printed Courses</a>
                            </li>
                            <li>
                                <a href="<?= SITE_URL ?>blume/orders/gifted">
                                    <span class="fa fa-file-text-o"></span> Gifted Courses</a>
                            </li>
                            <li>
                                <a href="<?= SITE_URL ?>blume/orders/subscriptions">
                                    <span class="fa fa-file-text-o"></span> Instalments</a>
                            </li>
                            <li>
                                <a href="<?= SITE_URL ?>blume/orders/gifted-vouchers">
                                    <span class="fa fa-file-text-o"></span> Gifted Vouchers</a>
                            </li>
                        </ul>
                    </li>
                    <li class="toggle-sub-nav">
                        <a href="#">
                            <span class="fa fa-table"></span>
                            <span class="sidebar-title">Reporting</span>
                            <span class="fa fa-caret-down" style="position: absolute;right: 53px;top: 11px;"></span>
                        </a>
                        <ul class="nav sub-nav">
                            <li>
                                <a href="<?= SITE_URL ?>blume/reporting/subscriptions">
                                    <span class="fa fa-file-text-o"></span> Subscriptions</a>
                            </li>
                            <li>
                                <a href="<?= SITE_URL ?>blume/reporting/leaderboard">
                                    <span class="fa fa-file-text-o"></span> Rewards Leaderboard</a>
                            </li>
                            <li>
                                <a href="<?= SITE_URL ?>blume/reporting/ncfe">
                                    <span class="fa fa-file-text-o"></span> NCFE</a>
                            </li>
                            <li>
                                <a href="<?= SITE_URL ?>blume/reporting/board-stats">
                                    <span class="fa fa-file-text-o"></span> Board Stats</a>
                            </li>
                        </ul>
                    </li>
                <li class="toggle-sub-nav">
                    <a href="#">
                        <span class="fa fa-newspaper-o"></span>
                        <span class="sidebar-title">Marketing</span>
                        <span class="fa fa-caret-down" style="position: absolute;right: 53px;top: 11px;"></span>
                    </a>
                    <ul class="nav sub-nav">
                        <li>
                            <a href="<?= SITE_URL ?>blume/marketing/coupons">
                                <span class="fa fa-file-text-o"></span> Discount Coupons</a>
                        </li>
                        <li>
                            <a href="<?= SITE_URL ?>blume/marketing/vouchers">
                                <span class="fa fa-file-text-o"></span> Vouchers</a>
                        </li>
                        <li>
                            <a href="<?= SITE_URL ?>blume/marketing/why">
                                <span class="fa fa-file-text-o"></span> Why New Skills</a>
                        </li>
                        <li>
                            <a href="<?= SITE_URL ?>blume/Banner">
                                <span class="fa fa-file-text-o"></span> Website Banner</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="<?= SITE_URL ?>blume/achievers">
                        <span class="fa fa-star"></span>
                        <span class="sidebar-title">Achiever Board  (<?= ORM::for_table("achievers")->where_not_equal("status", "a")->count() ?>)</span>
                    </a>
                </li>
                    <?php
                    $ncfeMessageCount = ORM::for_table("messages")->where_null("messageQueueID")->where("csApproved", "0")->count();
                    ?>
                    <li class="toggle-sub-nav">
                        <a href="#">
                            <span class="fa fa-folder"></span>
                            <span class="sidebar-title">NCFE (<?= $ncfeMessageCount ?>)</span>
                            <span class="fa fa-caret-down" style="position: absolute;right: 53px;top: 11px;"></span>
                        </a>
                        <ul class="nav sub-nav">
                            <li>
                                <a href="<?= SITE_URL ?>blume/ncfe/tutors">
                                    <span class="fa fa-file-text-o"></span> Tutors</a>
                            </li>
                            <li>
                                <a href="<?= SITE_URL ?>blume/ncfe/iqa">
                                    <span class="fa fa-file-text-o"></span> IQA Users</a>
                            </li>
                            <li>
                                <a href="<?= SITE_URL ?>blume/ncfe/messages">
                                    <span class="fa fa-file-text-o"></span> Messages (<?= $ncfeMessageCount ?>)</a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="<?= SITE_URL ?>blume/jobs">
                            <span class="fa fa-users"></span>
                            <span class="sidebar-title">Jobs</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= SITE_URL ?>blume/currencies">
                            <span class="fa fa-gbp"></span>
                            <span class="sidebar-title">Currencies</span>
                        </a>
                    </li>
                <li>
                    <a href="<?= SITE_URL ?>blume/offers">
                        <span class="fa fa-star"></span>
                        <span class="sidebar-title">Special Offer Pages</span>
                    </a>
                </li>
                <li>
                    <a href="<?= SITE_URL ?>blume/messages">
                        <span class="fa fa-envelope"></span>
                        <span class="sidebar-title">Messages</span>
                    </a>
                </li>
                <li>
                    <a href="<?= SITE_URL ?>blume/admins">
                        <span class="fa fa-users"></span>
                        <span class="sidebar-title">Admins</span>
                    </a>
                </li>
                <li>
                    <a href="<?= SITE_URL ?>blume/settings">
                        <span class="fa fa-cogs"></span>
                        <span class="sidebar-title">Settings</span>
                    </a>
                </li>
                <li>
                    <a href="<?= SITE_URL ?>blume/settings/redirects">
                        <span class="fa fa-exchange"></span>
                        <span class="sidebar-title">Redirects</span>
                    </a>
                </li>
                <?php } ?>
            </ul>
            <!-- -------------- /Sidebar Menu  -------------- -->
            <!-- -------------- Sidebar Hide Button -------------- -->
            <div class="sidebar-toggler">
                <a href="#">
                    <span class="fa fa-arrow-circle-o-left"></span>
                </a>
            </div>
            <!-- -------------- /Sidebar Hide Button -------------- -->

        </div>
        <!-- -------------- /Sidebar Left Wrapper  -------------- -->

    </aside>

    <script type="text/javascript">
        $( ".toggle-sub-nav" ).click(function() {
            $( this ).find( '.sub-nav' ).slideToggle();
        });
    </script>

    <!-- -------------- /Sidebar -------------- -->

    <!-- -------------- Main Wrapper -------------- -->
    <section id="content_wrapper">

        <!-- -------------- Topbar Menu Wrapper -------------- -->
        <div id="topbar-dropmenu-wrapper">
            <div class="topbar-menu row">
                <div class="col-xs-4 col-sm-2">
                    <a href="#" class="service-box bg-danger">
                        <span class="fa fa-music"></span>
                        <span class="service-title">Audio</span>
                    </a>
                </div>
                <div class="col-xs-4 col-sm-2">
                    <a href="#" class="service-box bg-success">
                        <span class="fa fa-picture-o"></span>
                        <span class="service-title">Images</span>
                    </a>
                </div>
                <div class="col-xs-4 col-sm-2">
                    <a href="#" class="service-box bg-primary">
                        <span class="fa fa-video-camera"></span>
                        <span class="service-title">Videos</span>
                    </a>
                </div>
                <div class="col-xs-4 col-sm-2">
                    <a href="#" class="service-box bg-alert">
                        <span class="fa fa-envelope"></span>
                        <span class="service-title">Messages</span>
                    </a>
                </div>
                <div class="col-xs-4 col-sm-2">
                    <a href="#" class="service-box bg-system">
                        <span class="fa fa-cog"></span>
                        <span class="service-title">Settings</span>
                    </a>
                </div>
                <div class="col-xs-4 col-sm-2">
                    <a href="#" class="service-box bg-dark">
                        <span class="fa fa-user"></span>
                        <span class="service-title">Users</span>
                    </a>
                </div>
            </div>
        </div>
        <!-- -------------- /Topbar Menu Wrapper -------------- -->

        <br />
        <br />

        <style>
            .progress {
                margin-bottom:0px;
            }
        </style>

