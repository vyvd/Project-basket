<div class="form-row">
    <div class="col-12 col-md-12">

        <div id="modules" class="row align-items-center mt20 reading">
            <?php
            foreach($blogs as $blog) {
                ?>
                <div class="card">
                    <div class="card-header">
                        <a href="<?= SITE_URL.$course->slug.'/additional-reading/'.$blog->slug;?>"><?= $blog->title;?></a>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>

    </div>
</div>