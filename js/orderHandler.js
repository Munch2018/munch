$(function () {
    var orderHandler = {};
    var doPayButton = $('#doPay');
    var form = $('#orderForm');
    var telphone = $('input[name="buyer_phone"]');

    orderHandler = {
        'constructor': function () {
            orderHandler.registerEvent();
        },

        'registerEvent': function () {
            doPayButton.on('click', function () {
                if (!orderHandler.validate()) {
                    return false;
                }

                form.submit();
            })


            $('.addr-search').on('click', function () {
                orderHandler.daumApi.open();
            })

            $('#ord_addr_chg').on('click', function () {
                window.open('/order/popupAddress', '', 'width=500,height=400');
            })

            telphone.on('keyup', function () {
                var num = telphone.val().replace(/[^0-9]/g, '');
                telphone.val(num);
            });
        },

        'daumApi': new daum.Postcode({
            oncomplete: function (data) {
                $('input[name="zipcode"]').val(data.zonecode);
                $('input[name="addr1st"]').val(data.address);
                $('input[name="address_idx"]').val('');
            }
        }),

        'validate': function () {
            if (!this.checkTelphone()) {
                return false;
            }

            var zipcode = $('input[name="zipcode"]');
            var addr1st = $('input[name="addr1st"]') ;
            var addr2nd = $('input[name="addr2nd"]') ;

            if (!zipcode.val()) {
                alert('주소를 선택해주세요.');
                zipcode.focus();
                return false;
            }
            if (!addr1st.val()) {
                alert('주소를 선택해주세요.');
                addr1st.focus();
                return false;
            }
            if (!addr2nd.val()) {
                alert('상세주소를 입력해주세요.');
                addr2nd.focus();
                return false;
            }
            if (!$('#customer_uid').val()) {
                alert('카드를 먼저 등록해주세요.');
                return false;
            }

            return true;
        },

        'doPay': function () {
            $.ajax({
                url: '/Order/requestPayment',
                type: "POST",
                data: {
                    customer_uid : $('#customer_uid').val(),
                    merchant_uid: '', // 새로 생성한 결제(재결제)용 주문 번호
                    amount: '',
                    name: ''
                },
                async: true,
                dataType: 'json',
                complete: function (result) {
                    console.log(result);
                    var data = result.responseJSON;
                    if (data.code === 'success') {
                    }
                }
            })
        },

        'checkTelphone' : function () {
            var telphoneVal = telphone.val();
            if (telphoneVal.length !== 11 && telphoneVal.length !== 10) {
                alert("유효하지 않은 전화번호 입니다.");
                telphone.val("");
                telphone.focus();
                return false;
            }

            // 유효성 체크
            var regExp_ctn = /^((01[1|6|7|8|9])[1-9]+[0-9]{6,7})|(010[1-9][0-9]{7})$/;
            if (!regExp_ctn.test(telphoneVal)) {
                alert("유효하지 않은 전화번호 입니다.");
                telphone.val("");
                telphone.focus();
                return false;
            }

            return true;
        }
    }

    orderHandler.constructor();
})
