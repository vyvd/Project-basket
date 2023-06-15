
<div class="modal fade" id="copyCourse" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Copy Course</h4>
            </div>
            <form name="copyCourseForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Select copy content destination/s</label>
                        <select name="copyDest" class="form-control" multiple>
                            <option value="current-website">This website</option>
                            <?php foreach (COURSE_COPY_CONNECTIONS as $courseCopyConnection) { ?>
                            <option value="<?= $courseCopyConnection['key'] ?>"><?= $courseCopyConnection['label'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="copyCourseResults" style="display: none">
                        <h5>Results</h5>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Copy</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">

    $('.select2').select2();

    function showCopyCourseToast(msg, status) {
        toastr.options.positionClass = "toast-bottom-left";
        toastr.options.closeDuration = 1000;
        toastr.options.timeOut = 5000;
        if (status === 'success') {
            toastr.success(msg, '')
            return''
        }
        toastr.error(msg || 'Error', 'Oops')
    }

    jQuery(document).on('click', '.copyCourse', function (e) {
       e.preventDefault();
        let courseID = jQuery(this).attr('data-course-id');
        copyCourseFormHandler(courseID);
       let copyCourseModal = jQuery('#copyCourse').modal();
       copyCourseModal.modal('show')

    });
    function copyCourseFormHandler(courseId) {
        jQuery("form[name='copyCourseForm']").submit(function (e) {
            e.preventDefault();
            var form = jQuery(this);
            var formData = new FormData($(this)[0]);
            let selectVal = jQuery(this).find('select[name="copyDest"]').val();
            formData.set('copyDest', selectVal);
            formData.set('courseID', courseId);
            jQuery.ajax({
                url: "<?= SITE_URL ?>ajax?c=blumeNew&a=copy-course",
                type: "POST",
                data: formData,
                async: true,
                success: function (msg) {
                    let errorMsg = 'There was an error copying the course contents';
                    let successMsg = 'Successfully copied course';
                    let parseResult = parseCopyCourseResult(msg);
                    if (!parseResult || !parseResult.hasOwnProperty('success')) {
                        showCopyCourseToast(errorMsg, 'error');
                        return;
                    }
                    var resultsBox = jQuery(form).find('.copyCourseResults');
                    if (!parseResult.success) {
                        if (parseResult.hasOwnProperty('errors')) {
                            if (Array.isArray(parseResult.errors) && parseResult.errors.length) {
                                console.error(parseResult.errors);
                            }
                        }
                        return;
                    }
                    if (parseResult.hasOwnProperty('redirect') && parseResult.redirect !== '') {
                        window.location = parseResult.redirect;
                        return;
                    }
                    if (parseResult.hasOwnProperty('results')) {
                        if (Array.isArray(parseResult.results) && parseResult.results.length) {
                            var resultsList = buildCopyCourseResultList(parseResult.results)
                            jQuery(resultsBox).append(resultsList).fadeIn();
                        }
                    }
                },
                cache: false,
                contentType: false,
                processData: false
            });
        });
    }
</script>