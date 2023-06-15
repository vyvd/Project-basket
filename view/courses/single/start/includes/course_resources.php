<?php
    $downloadContent = $this->controller->downloadContent($course->id);
?>
<div class="form-row">
    <div class="col-12 col-md-12">
        <div class="loader_wrapper" style="display: none">
            <i class="fas fa-spin fa-spinner"></i>
            <p>Downloading</p>
        </div>
        <div class="row align-items-center white-rounded mt-5">
            <div class="col-12">
                <div class="userDownloadContent p-4">
                    <div class="row mb-3">
                        <div class="col-12 text-right">
                            <?php if($course->isNCFE != "1") {
                                ?>
                                <button style="max-width: 200px" class="btn btn-primary btn-lg downloadContent"><i class="fa fa-download" aria-hidden="true"></i> Download All</button>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <?php
                            if($course->resources != "") {
                                ?>
                                <h4>Resources</h4>
                                <?php echo $course->resources ?>
                                <br />
                                <hr />
                                <br />
                                <?php
                            }
                            ?>

                            <h4>Worksheets</h4>
                            <?php
                                if(count($downloadContent['worksheets']) >= 1){
                                    foreach ($downloadContent['worksheets'] as $worksheet){

                                        $url = $worksheet['url'];
                                        $url = str_replace("www.old.newskillsacademy.co.uk", "newskillsacademy.co.uk", $url); // to get static images from old site
                                        $url = str_replace("old.newskillsacademy.com/wp-content", "newskillsacademy.com/wp-content", $url); // to get static images from old site
                                        //$url = str_replace("http://www.newskillsacademy.com/wp-content", "https://old.newskillsacademy.com/wp-content", $url); // to get static images from old site
                                        //$url = str_replace("https://newskillsacademy.com/wp-content", "https://old.newskillsacademy.com/wp-content", $url); // to get static images from old site
                            ?>
                                        <p>
                                            <a  href="<?= $url ?>" target="_blank" style="  color: grey;
                                font-size: 17px;">
                                                <i class="fa fa-file"
                                                   style="margin-right:5px;    color: #a00303;"></i>
                                                <?= $worksheet['title'] ?>
                                            </a>
                                        </p>
                            <?php
                                    }
                                }else{
                            ?>
                                    <p><em>There are no worksheets for this course.</em></p>
                            <?php
                                }
                            ?>
                        </div>
                        <div class="col-md-6">
                            <h4>Module PDF's</h4>
                            <?php
                            if(count($downloadContent['moduleContents']) >= 1){
                                foreach ($downloadContent['moduleContents'] as $worksheet){
                                    ?>
                                    <p>
                                        <a  href="<?= $worksheet['url'] ?>" target="_blank" style="  color: grey;
                                font-size: 17px;">
                                            <i class="fa fa-file"
                                               style="margin-right:5px;    color: #a00303;"></i>
                                            <?= $worksheet['title'] ?>
                                        </a>
                                    </p>
                                    <?php
                                }
                            }else{
                                ?>
                                <p><em>There are no content for this course.</em></p>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <?php
        //        echo "<pre>";
        //        print_r($downloadContent);
        //        echo "</pre>";
                ?>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function (){
        $('.downloadContent').on('click', function (e) {
            $(".loader_wrapper").css('display', 'block');
            var url = '<?= SITE_URL ?>ajax?c=course&a=downloadContentZip&courseID=<?= $course->id?>'
            $.ajax({
                type: "GET",
                url: url,
                success: function(response){
                    $(".loader_wrapper").css('display', 'none');
                    window.location.href = '<?= SITE_URL ?>assets/cdn/courseContent/<?= CUR_ID_FRONT .'/'. $course->slug . '.zip'?>'
                    setTimeout(function (){
                        $.ajax({
                            type: "GET",
                            url: '<?= SITE_URL . 'ajax?c=media&a=unlinkCourseContent&courseID='. $course->id .'&courseSlug='. $course->slug?>',
                            success: function(response){

                            },
                            error: function(xhr, status, error){
                                console.error(xhr);
                            }
                        });
                    }, 5000);
                },
                error: function(xhr, status, error){
                    console.error(xhr);
                }
            });
        });
    });
</script>