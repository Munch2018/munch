jQuery(document).ready(function () {
    var $ = jQuery;

    var tabMenu = $('.tab-menu li');
    var petImgBox = $('.select-pet .box');
    var periodBox = $('.set-period .box');

    var tabReset = function () {
        tabMenu.removeClass('selected');
        $('.tab-contents').hide();
    }

    var boxReset = function (elements) {
        elements.removeClass('box-selected').addClass('box');
    }

    var showStepBtn = function (elt) {
        console.log(elt);
    }

    var onClickBox = function (elt, elements) {
        if (elt.hasClass('box-selected')) {
            return;
        }

        boxReset(elements);
        elt.removeClass('box').addClass('box-selected');
    }

    tabMenu.on('click',function () {
        var elt = $(this);

        if (elt.hasClass('selected')) {
            return;
        }

        tabReset();

        elt.addClass('selected');
        $('.' + elt.data('contents')).show();

        showStepBtn(elt);
    });

    periodBox.on('click', function () {
        onClickBox($(this), periodBox);
    });
    petImgBox.on('click', function () {
        onClickBox($(this), petImgBox);
    });
})