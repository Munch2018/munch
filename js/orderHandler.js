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
                if (!this.validate()) {
                    return false;
                }

                this.doPay();
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
                $('input[name="buyer_zipcode"]').val(data.zonecode);
                $('input[name="buyer_addr1st"]').val(data.address);
            }
        }),

        'validate': function () {

        },

        'doPay': function () {
            form.submit();
        }
    }

    orderHandler.constructor();
})
