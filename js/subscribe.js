jQuery(document).ready(function () {
    var $ = jQuery;

    var tabMenu = $('.tab-menu li');
    var petImgBox = $('.select-pet .box');
    var periodBox = $('.set-period .box');
    var prevBtn = $('.btn-prev input[type="button"]');
    var nextBtn = $('.btn-next input[type="button"]');

    var tabReset = function () {
        tabMenu.removeClass('selected');
        $('.tab-contents').hide();
    }

    var boxReset = function (elements) {
        elements.removeClass('box-selected').addClass('box');
    }

    var showOrHideStepBtn = function (elt) {
        if (elt.data('contents') === 'select-pet') {
            prevBtn.hide();
        } else {
            prevBtn.show();
        }
        if (elt.data('contents') === 'pay-info') {
            nextBtn.hide();
        } else {
            nextBtn.show();
        }
    }

    var onClickBox = function (elt, elements) {
        if (elt.hasClass('box-selected')) {
            return;
        }

        boxReset(elements);
        elt.removeClass('box').addClass('box-selected');
    }

    tabMenu.on('click', function () {
        var elt = $(this);

        if (elt.hasClass('selected')) {
            return;
        }

        tabReset();

        elt.addClass('selected');
        $('.' + elt.data('contents')).show();

        showOrHideStepBtn(elt);
    });

    periodBox.on('click', function () {
        onClickBox($(this), periodBox);
    });
    petImgBox.on('click', function () {
        onClickBox($(this), petImgBox);
    });

    /**
     * 이전탭 이동
     */
    prevBtn.on('click', function () {
        $('.tab-menu').find('li.selected').prev('li').click();
    });
    /**
     * 다음탭 이동
     */
    nextBtn.on('click', function () {
        $('.tab-menu').find('li.selected').next('li').click();
    });
})