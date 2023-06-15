<?php
if(CUR_ID != "") {
    header('Location: '.SITE_URL.'blume/dashboard');
}
if($this->post["code"] != "") {
    $user = ORM::for_table("blumeUsers")->find_one($_SESSION['adminID']);

    if($this->post["code"] == $user->loginCode) {
        $newSignedID = $user->id;
        $_SESSION['adminFirstname'] = $user->name;
        $_SESSION['adminLastname'] = $user->surname;
        $_SESSION['id'] = $newSignedID;
        $_SESSION['idx'] = base64_encode("g4p3h9xfn8sq03hs2234$newSignedID");


        $encryptedID = base64_encode("g4enm2c0c4y3dn3727553$newSignedID");

        // set 2fa cookie, which expires in 30 days
        setcookie("blume2fa", $user->loginCode, time()+60*60*24*30, "/");

        ini_set("session.cookie_httponly", 1);
        setcookie("idCookie", $encryptedID, time()+60*60*24*100, "/");

        // record sign in
        // attempts to get the IP of the current user
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        $item = ORM::for_table("blumeLogs")->create();

        $item->userID = $newSignedID;
        $item->set_expr("dateTime", "NOW()");
        $item->ip = $ip;
        $item->action = "successfully signed into their account";

        $item->save();

        echo '<script type="text/javascript">setTimeout(location.reload(), 1500);</script>';


    } else {
        echo '<div class="alert alert-danger text-center">Incorrect code.</div>';
    }

    exit;

}
?>
<!DOCTYPE html>
<html>

<head>
    <!-- -------------- Meta and Title -------------- -->
    <meta charset="utf-8">
    <title>Confirm Sign In | Blume</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- -------------- Fonts -------------- -->
    <link rel='stylesheet' type='text/css' href='https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700'>
    <link href='https://fonts.googleapis.com/css?family=Lato:400,300,300italic,400italic,700,700italic' rel='stylesheet'
          type='text/css'>

    <!-- -------------- CSS - theme -------------- -->
    <link rel="stylesheet" type="text/css" href="<?= SITE_URL ?>assets/blume/skin/default_skin/css/theme.css">

    <!-- -------------- CSS - allcp forms -------------- -->
    <link rel="stylesheet" type="text/css" href="<?= SITE_URL ?>assets/blume/allcp/forms/css/forms.css">


    <!-- -------------- IE8 HTML5 support  -------------- -->
    <!--[if lt IE 9]>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.2/html5shiv.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body class="utility-page sb-l-c sb-r-c">

<!-- -------------- Body Wrap  -------------- -->
<div id="main" class="animated fadeIn">

    <!-- -------------- Main Wrapper -------------- -->
    <section id="content_wrapper">

        <div id="canvas-wrapper">
            <canvas id="demo-canvas"></canvas>
        </div>

        <!-- -------------- Content -------------- -->
        <section id="content">

            <!-- -------------- Login Form -------------- -->
            <div class="allcp-form theme-primary mw320" id="login">

                <div class="panel mw320">

                    <form name="signIn">
                        <div class="panel-body pn mv10">

                            <p>Please enter the code you've been emailed:</p>

                            <div class="section">
                                <label for="username" class="field prepend-icon">
                                    <input type="text" name="code" id="username" class="gui-input"
                                           placeholder="6 Digit Code">
                                    <label for="username" class="field-icon">
                                        <i class="fa fa-user"></i>
                                    </label>
                                </label>
                            </div>
                            <!-- -------------- /section -------------- -->


                            <!-- -------------- /section -------------- -->

                            <div class="section">
                                <div id="return_status"></div>
                                <button type="submit" class="btn btn-bordered btn-primary pull-right">Complete Sign In</button>
                            </div>
                            <!-- -------------- /section -------------- -->

                        </div>
                        <!-- -------------- /Form -------------- -->
                    </form>
                </div>
                <!-- -------------- /Panel -------------- -->
            </div>
            <!-- -------------- /Spec Form -------------- -->

        </section>
        <!-- -------------- /Content -------------- -->

    </section>
    <!-- -------------- /Main Wrapper -------------- -->

</div>
<!-- -------------- /Body Wrap  -------------- -->

<!-- -------------- Scripts -------------- -->

<!-- -------------- jQuery -------------- -->
<script src="<?= SITE_URL ?>assets/blume/js/jquery/jquery-1.11.3.min.js"></script>
<script src="<?= SITE_URL ?>assets/blume/js/jquery/jquery_ui/jquery-ui.min.js"></script>

<script type="text/javascript">
    $("form[name='signIn']").submit(function(e) {

        var formData = new FormData($(this)[0]);

        e.preventDefault();

        $( "#return_status" ).empty();


        $.ajax({
            url: "<?= SITE_URL ?>blume/login/confirm",
            type: "POST",
            data: formData,
            async: false,
            success: function (msg) {
                $('#return_status').append(msg);
            },
            cache: false,
            contentType: false,
            processData: false
        });

    });
</script>

<!-- -------------- CanvasBG JS -------------- -->
<script src="<?= SITE_URL ?>assets/blume/js/plugins/canvasbg/canvasbg.js"></script>

<!-- -------------- Theme Scripts -------------- -->
<script src="<?= SITE_URL ?>assets/blume/js/utility/utility.js"></script>
<script src="<?= SITE_URL ?>assets/blume/js/demo/demo.js"></script>
<script src="<?= SITE_URL ?>assets/blume/js/main.js"></script>

<!-- -------------- Page JS -------------- -->
<script type="text/javascript">
    jQuery(document).ready(function () {

        "use strict";

        // Init Theme Core
        Core.init();

        // Init Demo JS
        Demo.init();

        // Init CanvasBG
        CanvasBG.init({
            Loc: {
                x: window.innerWidth / 5,
                y: window.innerHeight / 10
            }
        });

    });
</script>

<!-- -------------- /Scripts -------------- -->
</body>

</html>
