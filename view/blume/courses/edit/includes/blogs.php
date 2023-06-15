<?php
    $blogs = $this->course->getAllCourseBlogs($course);
    ?>
    <div class="row mb20">
        <div class="col-12">
            <button data-type="single" data-url="<?= SITE_URL?>ajax?c=import&a=course_blogs&course_id=<?= $course->id?>" class="importJson btn btn-dark pull-right">
                <i class="fa fa-download"></i> Import Blogs
            </button>
            <button href="#" data-toggle="modal" data-target="#addBlog" class="btn btn-success pull-right mr10" style="margin-left:5px;">
                <i class="fa fa-plus"></i>
                New Blog
            </button>
        </div>
    </div>
<?php
    if (count($blogs) >= 1) {
        foreach ($blogs as $blog){
        ?>
            <div class="module" id="blog<?= $blog['id']?>">
                <form name="editBlog<?= $blog['id']?>">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="form-group">
                                <label>Title</label>
                                <input type="text" class="form-control" name="title" value="<?= $blog['title']?>">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="form-group">
                                <label>Edit Contents <a href="javascript:;" onclick="$('#editBlogContents<?= $blog['id'] ?>').slideToggle();">Show / Hide</a></label>
                                <div id="editBlogContents<?= $blog['id'] ?>" style="display: none;">
                                    <textarea name="contents" class="tinymce"><?= $blog['contents'] ?></textarea>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <button type="submit" class="btn btn-success btn-small">
                                <i class="fa fa-check"></i>
                                Update
                            </button>
                            <button data-id="<?= $blog['id'] ?>" data-table="blog" type="button" class="btn btn-danger btn-small ml10 deleteItem">
                                <i class="fa fa-trash"></i>
                                Delete
                            </button>
                        </div>
                    </div>
                    <input type="hidden" name="courseID" value="<?= $blog['courseID'] ?>">
                    <input type="hidden" name="id" value="<?= $blog['id'] ?>" />
                </form>
                <div id="returnStatus<?= $blog['id'] ?>"></div>
                <script type="text/javascript">
                    jQuery("form[name='editBlog<?= $blog['id'] ?>']").submit(function(e) {
                        tinyMCE.triggerSave();
                        e.preventDefault();
                        var formData = new FormData($(this)[0]);

                        jQuery.ajax({
                            url: "<?= SITE_URL ?>ajax?c=blumeNew&a=edit-course-blog",
                            type: "POST",
                            data: formData,
                            async: true,
                            success: function (msg) {
                                jQuery('#returnStatus<?= $blog['id'] ?>').html(msg);
                            },
                            cache: false,
                            contentType: false,
                            processData: false
                        });

                    });
                </script>
            </div>
        <?php
        }
    } else {
        ?>
        <p><em>There are no blog for this course.</em></p>
        <?php
    }
?>
<div class="modal fade" id="addBlog" tabindex="-1" role="dialog" aria-labelledby="myBlogLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Add Course Blog</h4>
            </div>
            <form name="addNewBlog">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" name="title" class="form-control" />
                    </div>

                    <input type="hidden" name="courseID" value="<?= $course->id ?>" />
                    <p><em>Slug/URL is generated automatically.</em></p>
                    <div id="returnStatusAddNew"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Publish</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">

    jQuery("form[name='addNewBlog']").submit(function(e) {
        e.preventDefault();
        // $('textarea[name="text"]').html($('.summernote').code());
        var formData = new FormData($(this)[0]);

        jQuery.ajax({
            url: "<?= SITE_URL ?>ajax?c=blumeNew&a=create-course-blog",
            type: "POST",
            data: formData,
            async: true,
            success: function (msg) {
                //jQuery('#returnStatusAddNew').html(msg);
                Swal.fire({
                    icon: 'success',
                    title: 'Added Successfully!',
                    //showDenyButton: true,
                    //showCancelButton: true,
                    confirmButtonText: 'OK',
                    //denyButtonText: `Don't save`,
                }).then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed) {
                        //$("#"+table+id).remove();
                        location.reload();
                    }
                });
            },
            cache: false,
            contentType: false,
            processData: false
        });
    });
</script>

