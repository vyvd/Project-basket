<div class="row">
    <div class="col-xs-12">

        <form name="uploadSupportingDoc" autocomplete="off">
            <h4>Add Supporting Document:</h4>
            <div class="row">
                <div class="col-xs-4">
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" class="form-control" name="title" required />
                    </div>
                </div>
                <div class="col-xs-4">
                    <div class="form-group">
                        <label>Upload File</label>
                        <input type="file" class="form-control" name="uploaded_file" accept="application/pdf" required/>
                    </div>
                </div>
                <div class="col-xs-4">
                    <div class="form-group">
                        <label>Who is this file for?</label>
                        <select name="audience" class="form-control">
                            <option value="s">Student</option>
                            <option value="t">Tutor</option>
                        </select>
                    </div>
                </div>
                <div class="col-xs-1">
                    <input type="submit" class="btn btn-success" value="Upload" style="margin-top:32px;" />
                </div>
            </div>
            <input type="hidden" name="id" value="<?= $course->id ?>" />
        </form>

        <br />

        <div id="returnSupportingDoc"></div>

        <script type="text/javascript">
            jQuery("form[name='uploadSupportingDoc']").submit(function(e) {

                e.preventDefault();
                var formData = new FormData($(this)[0]);

                jQuery.ajax({
                    url: "<?= SITE_URL ?>ajax?c=blumeNcfe&a=upload-supporting-doc",
                    type: "POST",
                    data: formData,
                    async: true,
                    success: function (msg) {
                        jQuery('#returnSupportingDoc').html(msg);
                        jQuery("form[name='uploadSupportingDoc']").trigger('reset');;
                    },
                    cache: false,
                    contentType: false,
                    processData: false
                });

            });
        </script>

        <hr />

        <br />

        <h4>Supporting Documents</h4>

        <div id="supportingDocsAjax"></div>

        <script>
            jQuery("#supportingDocsAjax").load('<?= SITE_URL ?>ajax?c=blumeNcfe&a=render-supporting-docs&id=<?= $course->id ?>');
        </script>


    </div>
</div>