

function openNav() {
    refreshCartSide();
    $("#basket").modal("toggle");
}

function closeNav() {
    $("#basket").modal("toggle");
}

function refreshCartSide() {

    $("#ajaxItems").load(SITE_URL+"ajax?c=cart&a=render-cart-side");

}

function refreshCartTop() {

    $("#header-basket").load(SITE_URL+"ajax?c=cart&a=render-cart-header");

}

function topCartSide() {

    $(".ajaxCartCount").load(SITE_URL+"ajax?c=cart&a=top-cart-count");

}
refreshCartTop();
topCartSide();

$( ".buy-course a, .start-course-button" ).click(function() {

    var courseID = $(this).data("course-id");

    $.post(SITE_URL+"ajax?c=cart&a=add-course",
        {
            courseID: courseID
        },
        function(data, status){
            refreshCartTop();
            openNav();
            topCartSide();
            $("#returnStatus").html(data);
        });

});

function saveCourse(id) {

    $.post(SITE_URL+"ajax?c=course&a=user-save-course",
        {
            id: id
        },
        function(data, status){

            $("#returnStatus").append(data);

        });
}

function getCookie(name, decode = false) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) {

            if(decode) {
                return decodeURIComponent(c.substring(nameEQ.length,c.length));
            } else {
                return c.substring(nameEQ.length,c.length);
            }

        }
    }
    return null;
}
function delete_cookie( name, path ) {
    if( getCookie( name ) ) {
        document.cookie = name + "=" +
            ((path) ? ";path="+path:"")+
            ";expires=Thu, 01 Jan 1970 00:00:01 GMT";
    }
}
function createCookie(name, value, days) {
    var expires;

    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toGMTString();
    } else {
        expires = "";
    }
    document.cookie = encodeURIComponent(name) + "=" + encodeURIComponent(value) + expires + "; path=/";
}

Array.prototype.unique = function() {
    var a = this.concat();
    for(var i=0; i<a.length; ++i) {
        for(var j=i+1; j<a.length; ++j) {
            if(a[i] === a[j])
                a.splice(j--, 1);
        }
    }

    return a;
};

$( ".right-icons.cur .dropdown li" ).click(function() {

    var currency = $(this).data("currency");

    $.post(SITE_URL+"ajax?c=account&a=set-currency",
        {
            id: currency
        },
        function(data, status){
            var currentLocation = window.location.href;
            console.log(currentLocation);
            if(currentLocation.indexOf("?") > -1) {
                parent.location=currentLocation+'&curChange=true';
            } else {
                parent.location=currentLocation+'?curChange=true';
            }
        });

});