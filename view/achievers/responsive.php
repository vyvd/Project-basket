<?php
$css = array("acheiver.css");
$pageTitle = "Achiever Board";

$breadcrumb = array(
    "Achiever Board" => '',
);
$itemsLimit = 20;
$items = $this->controller->getItems($itemsLimit);
$totalItems = $this->controller->getItemsTotal();

include BASE_PATH . 'header.php';
?>
<style>
    .acheiver-item{
        width: 245px;
        max-width: 100%;
    }
</style>

    <!-- Main Content Start-->
    <main role="main" class="regular">

        <!--Page Content-->
        <section class="">
            <div class="container wider-container">

                <div class="row align-items-center acheiver-board">
                    <div class="col-12 text-center">
                        <h1>Achiever's Board</h1>
                        <p>Here we celebrate our students' achievements. Send us a photo of you with your certificate to get added to our achiever's board.</p>
                    </div>
                </div>

                <div class="row" >
                    <div class="col-12 acheivers-list grid" style="opacity:0;">
                        <?php
                        $count = 1;
                        $reactions = array("clapping", "starstruck", "smiling", "usercap", "cap");
                        foreach($items as $item) {

                            ?>
                            <div class="acheiver-item">
                                <div class="acheiver-box text-center">
                                    <img src="<?= $this->controller->getAchieverImage($item->id, "medium") ?>" alt="acheivers" />
                                    <i class="reaction <?= $reactions[array_rand($reactions)] ?>"></i>
                                    <p class="acheiver-name"><?= $item->firstname.' '.$item->lastname ?></p>
                                </div>
                            </div>
                            <?php

                            if($count == 2 || $count == 19) {
                                ?>
                                <div class="acheiver-item" onclick="parent.location='<?= SITE_URL ?>achievers/submit'" style="cursor:pointer;">
                                    <div class="acheiver-box upload-img">
                                        <h1>Celebrate Your Success Here</h1>
                                        <p>Upload your <br/>image <i class="fas fa-long-arrow-alt-right"></i></p>
                                    </div>
                                </div>
                                <?php
                            }
                            $excludeIds[] = $item->id;
                            $count ++;

                        }
                        ?>
                    </div>
                    <?php
                    if($totalItems > $itemsLimit){
                        ?>

                        <div class="col-12 text-center mb-5">
                            <button type="button" id="getMoreItems" class="btn btn-primary btn-lg extra-radius">Load More</button>
                            <input type="hidden" name="currentPage" id="currentPage" value='1'>
                            <input type="hidden" name="excludeIds" id="excludeIds" value='<?= implode(',', $excludeIds)?>'>
                        </div>

                        <?php
                    }
                    ?>

                </div>

                <!-- implement an infinity load type thing -->

                <script src="https://cdnjs.cloudflare.com/ajax/libs/masonry/4.2.2/masonry.pkgd.min.js" integrity="sha512-JRlcvSZAXT8+5SQQAvklXGJuxXTouyq8oIMaYERZQasB8SBDHZaUbeASsJWpk0UUrf89DP3/aefPPrlMR1h1yQ==" crossorigin="anonymous"></script>

                <script>
                    $( document ).ready(function() {

                        setTimeout(function () {
                            $('.grid').masonry({
                                itemSelector: '.acheiver-item',
                                gutter: 10
                            });

                            $(".acheivers-list").css("opacity", "1");

                        }, 2000);

                        $("#getMoreItems").on('click', function (){
                            const url = "<?= SITE_URL ?>ajax?c=achieverBoard&a=getItems&action=json";
                            $("#getMoreItems").css('display', 'none');
                            $.ajax({
                                type: "POST",
                                url: url,
                                data: {
                                    page: parseInt($("#currentPage").val()) + 1,
                                    excludeIds: $("#excludeIds").val(),
                                },
                                success: function(response){
                                    response = JSON.parse(response);

                                    $(".acheivers-list.grid").append(response.items);

                                    setTimeout(function () {
                                        $('.acheivers-list.grid').masonry('reloadItems');

                                        $('.grid').masonry({
                                            itemSelector: '.acheiver-item',
                                            gutter: 10
                                        });

                                    }, 200);

                                    if(response.loadMore == true){
                                        $("#getMoreItems").css('display', 'inline-block');
                                    }
                                    $("#excludeIds").val(response.excludeIds);

                                    console.log(response);
                                },
                                error: function(xhr, status, error){
                                    console.error(xhr);
                                }
                            });
                        });
                    });
                </script>

            </div>
        </section>


        <?php include BASE_PATH . 'learn-confidence.php'; ?>

        <?php include BASE_PATH . 'newsletter.php'; ?>

        <?php include BASE_PATH . 'featured.php'; ?>

        <?php include BASE_PATH . 'success-stories.php'; ?>

    </main>
    <!-- Main Content End -->


<?php include BASE_PATH . 'footer.php';?>