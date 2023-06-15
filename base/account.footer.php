
</main>

<div id="returnStatus"></div>
<script type="text/javascript">
    var SITE_URL = '<?= SITE_URL ?>';
</script>
<script src="<?= SITE_URL ?>assets/js/global.js?ver=4.1" type="text/javascript"></script>

<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

<!-- Progress Circle JavaScript -->
<script type="text/javascript">
    $(document).ready(function (){
        function getCookie(cname) {
            var name = cname + "=";
            var decodedCookie = decodeURIComponent(document.cookie);
            var ca = decodedCookie.split(';');
            for(var i = 0; i <ca.length; i++) {
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
        
        if (getCookie('user-sidebar-menu') == 'collapsed'){
            $('body').addClass('collapse-side-menu');
        }

        $(".user-menu-collapse .fa-angle-left").on('click', function(){
            $('body').addClass('collapse-side-menu');
            document.cookie = "user-sidebar-menu=collapsed";
        });
        $(".user-menu-collapse .fa-angle-right").on('click', function(){
            $('body').removeClass('collapse-side-menu');
            document.cookie = "user-sidebar-menu=";
        });
    });
    $(function() {

        $(".progress-circle").each(function() {

            var value = $(this).attr('data-value');
            var left = $(this).find('.progress-left .progress-bar');
            var right = $(this).find('.progress-right .progress-bar');

            if (value > 0) {
                if (value <= 50) {
                    right.css('transform', 'rotate(' + percentageToDegrees(value) + 'deg)')
                } else {
                    right.css('transform', 'rotate(180deg)')
                    left.css('transform', 'rotate(' + percentageToDegrees(value - 50) + 'deg)')
                }
            }

        })

        function percentageToDegrees(percentage) {

            return percentage / 100 * 360

        }

    });
</script>

<!-- add subscription course -->
<script>
    $( ".subAddCourse" ).click(function() {

        var courseID = $(this).data("course-id");

        $.get( "<?= SITE_URL ?>ajax?c=sub&a=add-course&courseID="+courseID, function( data ) {
            if(data == "complete") {

                $(".subCourse"+courseID).addClass("hasAccess");

            } else {
                $("#returnStatus").html(data);
            }
        });

    });

    $( ".subRemoveCourse" ).click(function(e) {

        var courseID = $(this).data("course-id");

        e.preventDefault();
        if (window.confirm("Are you sure you want to remove this course from your account? You will lose any progress and notes.")) {
            $.get( "<?= SITE_URL ?>ajax?c=sub&a=remove-course&courseID="+courseID, function( data ) {
                if(data == "complete") {

                    $(".subCourse"+courseID).removeClass("hasAccess");

                } else {
                    $("#returnStatus").html(data);
                }
            });
        }


    });
</script>

<?php
if($hasTooltips == true) {
    ?>
    <script>
        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        });
    </script>
    <?php
}
?>


<script>
    setInterval(function(){$.post('<?= SITE_URL ?>ajax?c=account&a=keep-alive');},20000);
    
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
