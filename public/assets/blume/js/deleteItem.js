jQuery(document).ready(function () {
    $(".deleteItem").on('click', function () {
        Swal.fire({
            title: 'Are you sure want to delete?',
            //showDenyButton: true,
            showCancelButton: true,
            confirmButtonText: 'Delete',
            confirmButtonColor: '#f5393d',
            //denyButtonText: `Don't save`,
        }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            if (result.isConfirmed) {
                var id = $(this).data('id');
                var table = $(this).data('table');
                var reload = $(this).data('reload');

                var route = SITE_URL_JS + 'ajax?c=blumeNew&a=deleteItem';

                $.ajax({
                    type: "POST",
                    url: route,
                    data: {
                        table: table,
                        id: id
                    },
                    success: function (msg) {
                        //jQuery('#returnStatusAddNew').html(msg);
                        Swal.fire({
                            icon: 'success',
                            title: 'Delete Successfully!',
                            //showDenyButton: true,
                            //showCancelButton: true,
                            confirmButtonText: 'OK',
                            //denyButtonText: `Don't save`,
                        }).then((result) => {
                            /* Read more about isConfirmed, isDenied below */
                            if (result.isConfirmed) {
                                $("#" + table + id).remove();
                                if (reload === true) {
                                    location.reload();
                                }
                            }
                        });
                    },
                    // cache: false,
                    // contentType: false,
                    // processData: false
                });
            }
            // else if (result.isDenied) {
            //     Swal.fire('Changes are not saved', '', 'info')
            // }
        });
    });
});

function deleteModelItem(id, table, reload = false) {
    Swal.fire({
        title: 'Are you sure want to delete?',
        //showDenyButton: true,
        showCancelButton: true,
        confirmButtonText: 'Delete',
        confirmButtonColor: '#f5393d',
        //denyButtonText: `Don't save`,
    }).then((result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {

            var route = SITE_URL_JS + 'ajax?c=blumeNew&a=deleteItem';

            $.ajax({
                type: "POST",
                url: route,
                data: {
                    table: table,
                    id: id
                },
                success: function (msg) {
                    //jQuery('#returnStatusAddNew').html(msg);
                    Swal.fire({
                        icon: 'success',
                        title: 'Delete Successfully!',
                        //showDenyButton: true,
                        //showCancelButton: true,
                        confirmButtonText: 'OK',
                        //denyButtonText: `Don't save`,
                    }).then((result) => {
                        /* Read more about isConfirmed, isDenied below */
                        if (result.isConfirmed) {
                            $("#" + table + id).remove();
                            if (reload === true) {
                                location.reload();
                            }
                        }
                    });
                },
            });
        }
    });
}