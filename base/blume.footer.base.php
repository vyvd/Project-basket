

</section>

<script type="text/javascript" src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>

<!-- -------------- /Main Wrapper -------------- -->
<div id="returnStatus"></div>
<!-- -------------- Sidebar Right -------------- -->
<aside id="sidebar_right" class="nano affix">

    <!-- -------------- Sidebar Right Content -------------- -->
    <div class="sidebar-right-wrapper nano-content">

        <div class="sidebar-block br-n p15">

            <h6 class="title-divider text-muted mb20"> Visitors Stats
                <span class="pull-right"> 2015
                  <i class="fa fa-caret-down ml5"></i>
                </span>
            </h6>

            <div class="progress mh5">
                <div class="progress-bar progress-bar-primary" role="progressbar" aria-valuenow="34"
                     aria-valuemin="0"
                     aria-valuemax="100" style="width: 34%">
                    <span class="fs11">New visitors</span>
                </div>
            </div>
            <div class="progress mh5">
                <div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="66"
                     aria-valuemin="0"
                     aria-valuemax="100" style="width: 66%">
                    <span class="fs11 text-left">Returnig visitors</span>
                </div>
            </div>
            <div class="progress mh5">
                <div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="45"
                     aria-valuemin="0"
                     aria-valuemax="100" style="width: 45%">
                    <span class="fs11 text-left">Orders</span>
                </div>
            </div>

            <h6 class="title-divider text-muted mt30 mb10">New visitors</h6>

            <div class="row">
                <div class="col-xs-5">
                    <h3 class="text-primary mn pl5">350</h3>
                </div>
                <div class="col-xs-7 text-right">
                    <h3 class="text-warning mn">
                        <i class="fa fa-caret-down"></i> 15.7% </h3>
                </div>
            </div>

            <h6 class="title-divider text-muted mt25 mb10">Returnig visitors</h6>

            <div class="row">
                <div class="col-xs-5">
                    <h3 class="text-primary mn pl5">660</h3>
                </div>
                <div class="col-xs-7 text-right">
                    <h3 class="text-success-dark mn">
                        <i class="fa fa-caret-up"></i> 20.2% </h3>
                </div>
            </div>

            <h6 class="title-divider text-muted mt25 mb10">Orders</h6>

            <div class="row">
                <div class="col-xs-5">
                    <h3 class="text-primary mn pl5">153</h3>
                </div>
                <div class="col-xs-7 text-right">
                    <h3 class="text-success mn">
                        <i class="fa fa-caret-up"></i> 5.3% </h3>
                </div>
            </div>

            <h6 class="title-divider text-muted mt40 mb20"> Site Statistics
                <span class="pull-right text-primary fw600">Today</span>
            </h6>
        </div>
    </div>
</aside>
<!-- -------------- /Sidebar Right -------------- -->

</div>
<!-- -------------- /Body Wrap  -------------- -->

<!-- -------------- Models -------------- -->
<?php  include __DIR__ . 'blume.models.base.php'; ?>



<!-- -------------- FooTable JS -------------- -->
<script src="<?= SITE_URL ?>assets/blume/js/plugins/footable/js/footable.all.min.js"></script>
<script src="<?= SITE_URL ?>assets/blume/js/plugins/footable/js/footable.filter.min.js"></script>

<!-- -------------- Dropzone JS -------------- -->
<script src="<?= SITE_URL ?>assets/blume/js/plugins/dropzone/dropzone.min.js"></script>

<!-- -------------- HighCharts Plugin -------------- -->
<script src="<?= SITE_URL ?>assets/blume/js/plugins/highcharts/highcharts.js"></script>

<!-- -------------- FileUpload JS -------------- -->
<script src="<?= SITE_URL ?>assets/blume/js/plugins/fileupload/fileupload.js"></script>
<script src="<?= SITE_URL ?>assets/blume/js/plugins/holder/holder.min.js"></script>

<!-- -------------- MonthPicker JS -------------- -->
<script src="<?= SITE_URL ?>assets/blume/allcp/forms/js/jquery-ui-monthpicker.min.js"></script>
<script src="<?= SITE_URL ?>assets/blume/allcp/forms/js/jquery-ui-datepicker.min.js"></script>
<script src="<?= SITE_URL ?>assets/blume/allcp/forms/js/jquery.spectrum.min.js"></script>
<script src="<?= SITE_URL ?>assets/blume/allcp/forms/js/jquery.stepper.min.js"></script>

<!-- -------------- Summernote -------------- -->
<script src="<?= SITE_URL ?>assets/blume/js/plugins/summernote/summernote.min.js"></script>

<!-- -------------- Theme Scripts -------------- -->
<script src="<?= SITE_URL ?>assets/blume/js/utility/utility.js"></script>
<script src="<?= SITE_URL ?>assets/blume/js/demo/demo.js"></script>
<script src="<?= SITE_URL ?>assets/blume/js/main.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.15.5/dist/sweetalert2.all.min.js"></script>
<script src="<?= SITE_URL ?>assets/blume/js/importJson.js"></script>
<script src="<?= SITE_URL ?>assets/blume/js/deleteItem.js"></script>
<script src="<?= SITE_URL ?>assets/blume/js/demo/widgets_sidebar.js"></script>
<script src="<?= SITE_URL ?>assets/blume/js/pages/user-forms-file-uploaders.js"></script>
<script src="<?= SITE_URL ?>assets/blume/js/pages/user-forms-editors.js"></script>
<script type="text/javascript">
    jQuery(document).ready(function () {

        "use strict";

        // Init Theme Core
        Core.init();

        // Init Demo JS
        Demo.init();


        // Init FooTable
        $('.footable').footable();

        $('.datetimepicker').datetimepicker({
            prevText: '<i class="fa fa-chevron-left"></i>',
            nextText: '<i class="fa fa-chevron-right"></i>',
            beforeShow: function(input, inst) {
                var newclass = 'allcp-form';
                var themeClass = $(this).parents('.allcp-form').attr('class');
                var smartpikr = inst.dpDiv.parent();
                if (!smartpikr.hasClass(themeClass)) {
                    inst.dpDiv.wrap('<div class="' + themeClass + '"></div>');
                }
            }
        });

    });
    $('body').removeClass('sb-l-m');
</script>
<script type="text/javascript">

    function googleApiAuthResponseHandler(responseData, callback) {
        if (!responseData.hasOwnProperty('code')) {
            return false;
        }
        switch (responseData.code) {
            case 'auth_url':
                if (responseData.hasOwnProperty('auth_url') && responseData.auth_url) {
                    const popup = window.open(responseData.auth_url)
                    window.addEventListener('message', event => {
                        if (event.origin === '<?php echo rtrim(SITE_URL, '/'); ?>') {
                            if (event.data !== 'success') {
                                console.error('Google auth error')
                            }
                            popup.close();
                            callback()
                        }
                    })
                    return true;
                }
                return false;
            default:
                return false;
        }
    }
    function googleApiAuth(action, callback, onError) {
        jQuery.ajax({
            url: `<?= SITE_URL ?>ajax?c=googleAuth&a=${action}`,
            type: "POST",
            // data: formData,
            async: true,
            success: function (msg) {
                let parseResponse = JSON.parse(msg);
                if (typeof parseResponse !== 'object') {
                    onError('There was an error')
                    return;
                }
                if (!parseResponse.hasOwnProperty('success')) {
                    onError('There was an error')
                    return;
                }
                if (
                    !parseResponse.success &&
                    parseResponse.hasOwnProperty('code')
                ) {
                    if (!googleApiAuthResponseHandler(parseResponse, callback)) {
                        onError('There was an error')
                    }
                    return;
                }
                if (parseResponse.success) {
                    callback()
                }
            }
        });
    }
</script>
<script type="text/javascript">

    function parseCopyCourseResult(msg) {
        var msgToObj = JSON.parse(msg)
        if (!typeof msgToObj === 'object') {
            return false;
        }
        return msgToObj;
    }
    function getCopyCourseConnectionLabel(data) {
        if (data.hasOwnProperty('CONNECTION_LABEL')) {
            return data.CONNECTION_LABEL;
        }
        if (data.hasOwnProperty('CONNECTION')) {
            return data.CONNECTION;
        }
        return 'Invalid connection label'
    }
    function buildCopyCourseResultList(data) {
        var sub_ul = $('<ul/>');
        $.each(data, function (index, value) {
            if (!value.hasOwnProperty('CONNECTION')) {
                return;
            }
            var siteUrlLink;
            if (value.hasOwnProperty('model') && value.model) {
                if (value.hasOwnProperty('SITE_URL')) {
                    siteUrlLink = $('<a/>').html(value.SITE_URL).attr('href', value.SITE_URL);
                } else {
                    siteUrlLink = 'Site url Error';
                }
            } else {
                siteUrlLink = 'Error copying course'
            }
            var sub_li = $('<li/>').html(`${getCopyCourseConnectionLabel(value)}: `).append(siteUrlLink);
            sub_ul.append(sub_li);
        });
        return sub_ul;
    }
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