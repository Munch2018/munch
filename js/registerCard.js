$(function () {
    /**
     * 빌링키 발급
     */

    var issueBilling = function (params) {
        var resultCode = false;
        $.ajax({
            url: '/Order/issueBilling',
            type: "POST",
            async: false,
            data: {
                expiry: params.expiry,
                birth: params.birth,
                pwd_2digit: params.pwd_2digit,
                card_number: params.card_number,
                customer_uid: params.customer_uid,
                card_last_num: params.card_last_num
            },
            dataType: 'json',
            complete: function (result) {
                var data = result.responseJSON;
                if (data.code == 0) {
                    resultCode = true;

                    var card_info_html = '<div class="using_card">' +
                        '    <b>사용중인 카드 정보</b>' +
                        '    <br><span>' + data.response.card_name + ' (****-****-****-'+data.response.card_last_num+')</span>' +
                        '</div>' +
                        '<button type="button" class="btn-purple-square" id="changeCard">카드변경</button>';

                    $('#customer_uid').val(params.customer_uid);
                    $('#register_card_form').html('');
                    $('#card_info_form').html(card_info_html);

                    $('#changeCard').on('click', function () {
                        changeCard();
                    })
                    $('#register-card').hide();
                    $('#doPay').show();
                } else if (data.message) {
                    alert(data.message);
                }
            }
        })
        return resultCode;
    }

    var issuedBillingKey = function () {
        var formData = formValidation();
        if (!formData) {
            return false;
        }

        if (!issueBilling(formData)) {
            return false;
        }
    }

    var makeCustomerUid = function () {
        var default_data = [];
        default_data.push($('#member_idx').val());
        default_data.push($('input[name="card_num[]"]').eq(3).val());

        return default_data.join('_');
    }

    var formValidation = function () {
        var params = {};

        //카드번호
        var card_input = $('input[name="card_num[]"]');
        var card_num = [];
        for (var i = 0; i <= 3; i++) {
            if (!/^[0-9]*$/.test(card_input.eq(i).val())
                || !card_input.eq(i).val()
                || card_input.eq(i).val().length !== 4) {
                alert('카드번호를 정확하게 입력해주세요.');
                return false;
            }
            card_num[i] = card_input.eq(i).val();
        }
        params.card_last_num = card_num[3];

        if (card_num.length !== 4) {
            alert('카드번호가 정확하지 않습니다.');
            return false;
        }

        var month = $('#validity_month');
        var year = $('#validity_year');

        if (!year.val()) {
            alert('유효기간을 입력해주세요.');
            year.focus();
            return false;
        }
        if (!month.val()) {
            alert('유효기간을 입력해주세요.');
            month.focus();
            return false;
        }

        var pwd = $('input[name="pwd_2digit"]');
        if (!pwd.val() || !/^[0-9]*$/.test(pwd.val() || pwd.val().length !== 2)) {
            alert('비밀번호를 정확하게 입력해주세요.');
            pwd.focus();
            return false;
        }

        var birth = $('input[name="birthday"]');
        if (!birth.val() || !/^[0-9]*$/.test(birth.val() || birth.val().length !== 6)) {
            alert('생년월일을 정확하게 입력해주세요.');
            birth.focus();
            return false;
        }

        params.card_number = card_num.join('-');
        params.expiry = year.val() + '-' + month.val();
        params.birth = birth.val();
        params.pwd_2digit = pwd.val();
        params.customer_uid = makeCustomerUid();

        return params;
    }

    $('#register-card').on('click', function () {
        issuedBillingKey();
    });

    $('#changeCard').on('click', function () {
        changeCard();
    })

    var changeHtml = function () {
        $.ajax({
            type: 'get',
            url: '/Order/changeCardHtml',
            data:{
                'page' : $('#page').val()
            },
            dataType: 'json',
            complete: function (contents) {
                if (!!contents.responseText) {
                    $('#doPay').hide();
                    $('#changeCard').show();
                    $('#register-card').hide();
                    $('#customer_uid').val('');
                    $('#register_card_form').html(contents.responseText);
                    $('#card_info_form').html('');

                    $('#register-card').on('click', function () {
                        issuedBillingKey();
                    });
                } else {
                    alert('변경요청이 정상적이지 않습니다. 새로고침 후 재시도해주세요.');
                }
            }
        });
    }

    var changeCard = function () {
        $.ajax({
            url: '/Order/changeCard',
            type: "POST",
            data: {customer_uid: $('#customer_uid').val()},
            async: true,
            dataType: 'json',
            complete: function (result) {
                var data = result.responseJSON;
                if (data.code === 'success') {
                    changeHtml();
                }
            }
        })
    }



});