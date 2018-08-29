$(function () {
    var orderHandler = {};
    var doPayButton = $('#doPay');
    var form = $('#orderForm');

    orderHandler = {
        'constructor': function () {
            orderHandler.registerEvent();
        },

        'registerEvent': function () {
            doPayButton.on('click', function () {
                if (!orderHandler.validate()) {
                    return false;
                }

                orderHandler.doPay();
            })


            $('.addr-search').on('click', function () {
                orderHandler.daumApi.open();
            })

            $('#ord-info-same').on('click', function () {
                var elt = $(this);
                if (elt.is(':checked')) {

                } else {

                }
            })
        },

        'daumApi': new daum.Postcode({
            oncomplete: function (data) {
                $('input[name="zipcode"]').val(data.zonecode);
                $('input[name="addr1st"]').val(data.address);
                $('input[name="address_idx"]').val('');
            }
        }),

        'validate': function () {
            return true;
        },

        'doPay': function () {
            form.submit();
        }
    }

    orderHandler.constructor();
})
