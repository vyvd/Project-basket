jQuery(document).ready(function () {
    $(".importJson").on('click', function (){
        Swal.fire({
            title: 'Do you want to import data?',
            //showDenyButton: true,
            showCancelButton: true,
            confirmButtonText: 'Import',
            //denyButtonText: `Don't save`,
        }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            if (result.isConfirmed) {
                $("#importingModal").modal();

                var route = $(this).attr('data-url');

                jQuery.ajax({
                    url: route,
                    type: "GET",
                    data: "",
                    async: true,
                    success: function (msg) {
                        //jQuery('#returnStatusAddNew').html(msg);
                        $("#importingModal").modal("hide");
                        Swal.fire({
                            icon: 'success',
                            title: 'Import Successfully!',
                            //showDenyButton: true,
                            //showCancelButton: true,
                            confirmButtonText: 'OK',
                            //denyButtonText: `Don't save`,
                        }).then((result) => {
                            /* Read more about isConfirmed, isDenied below */
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        });
                    },
                    cache: false,
                    contentType: false,
                    processData: false
                });
            }
            // else if (result.isDenied) {
            //     Swal.fire('Changes are not saved', '', 'info')
            // }
        });
    });
});