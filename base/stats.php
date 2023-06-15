<!--Totals-->
<?php
    $usaUsersCount = 251475;
?>
<section class="totals-section">
    <div class="container wider-container padded-sec learn-with-confidence">
        <div class="row text-center">
            <div class="col-4">
                 <span class="totals-icon">
                   <img src="<?= SITE_URL ?>assets/images/online-education.png" alt="totals" />
                 </span>
                <label class="counter" data-count="<?= ORM::for_table("courses")->count() ?>">0</label>
                <p>TOTAL COURSES</p>
            </div>
            <div class="col-4">
                <span class="totals-icon">
                  <img src="<?= SITE_URL ?>assets/images/group.png" alt="totals" />
                </span>
                <label class="counter" data-count="<?= ORM::for_table("accounts")->count() ?>">0</label>
                <p>TOTAL STUDENTS</p>
            </div>
            <div class="col-4">
                <span class="totals-icon">
                  <img src="<?= SITE_URL ?>assets/images/video.png" alt="totals" />
                </span>
                <label class="counter" data-count="<?= ORM::for_table("courseModules")->count() ?>">0</label>
                <p>TOTAL LESSONS</p>
            </div>
        </div>
    </div>
</section>

<script>

    $.fn.isOnScreen = function(){

        var win = $(window);

        var viewport = {
            top : win.scrollTop(),
            left : win.scrollLeft()
        };
        viewport.right = viewport.left + win.width();
        viewport.bottom = viewport.top + win.height();

        var bounds = this.offset();
        bounds.right = bounds.left + this.outerWidth();
        bounds.bottom = bounds.top + this.outerHeight();

        return (!(viewport.right < bounds.left || viewport.left > bounds.right || viewport.bottom < bounds.top || viewport.top > bounds.bottom));

    };

    $(document).ready(function(){
        $(window).scroll(function(){
            if ($('.totals-section').isOnScreen()) {
                // The element is visible, do something
                $('.counter').each(function() {
                    var $this = $(this),
                        countTo = $this.attr('data-count');

                    $({ countNum: $this.text()}).animate({
                            countNum: countTo
                        },

                        {

                            duration: 1000,
                            easing:'linear',
                            step: function() {
                                $this.text(Math.floor(this.countNum));
                            },
                            complete: function() {
                                $this.text(this.countNum);
                                //alert('finished');
                            }

                        });



                });
            } else {
                // The element is NOT visible, do something else
            }
        });
    });


</script>