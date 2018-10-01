function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    var expires = "expires=" + d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function getCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

function viewGoods() {
    setCookie('goods_view', 'y', 1);
}

function goodsViewAction() {
    var goods_view = getCookie('goods_view');

    if (goods_view == 'y') {
        $('html, body').scrollTop($('#goods').offset().top);
        setCookie('goods_view', 'n', 1);
    }
}


function commonShowLayer(layer) {
    var w = $(window).width();
    var h = $(window).height();
    var offsetY = window.pageYOffset;

    var layer_width = layer.width();
    var layer_height = layer.outerHeight();

    layer.css({
        'top': ((h + offsetY) - layer_height) / 2,
        'left': (w - layer_width) / 2
    });
}