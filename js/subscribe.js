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
           // nextBtn.hide();
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

    var changeMenuBar = function (elt) {
        if (elt.hasClass('selected')) {
            return;
        }

        tabReset();

        elt.addClass('selected');
        $('.' + elt.data('contents')).show();

        showOrHideStepBtn(elt);
    };

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
        changeMenuBar($('.tab-menu').find('li.selected').prev('li'));
    });

    /**
     * 다음탭 이동
     */
    nextBtn.on('click', function () {
        var selectedTab = $('.tab-menu').find('li.selected');
        if (selectedTab.data('contents') === 'set-period') {
            var pet_idx = getBoxSelected('select-pet').data('pet_idx');
            var period = getBoxSelected('set-period').data('period');

            if (!pet_idx) {
                alert('아이를 선택해주세요.');
                changeMenuBar(tabMenu.eq(0));
                return false;
            }
            if (!period) {
                alert('구독 기간을 선택해주세요.');
                changeMenuBar(tabMenu.eq(1));
                return false;
            }

            location.href = '/subscribe/add?pet_idx=' + pet_idx + '&period=' + period;
        } else {
            changeMenuBar(selectedTab.next('li'));
        }
    });

    var getBoxSelected = function (type) {
        return $('.tab-contents.' + type).find('.box-selected');
    }

    changeMenuBar(tabMenu.eq(0));
})