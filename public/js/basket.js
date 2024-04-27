window.onload = function () {

}


function checkOut() {
    loader_start()
    let address=$('#City').val() + ', ' + $('#Postcode').val() + ', ' + $('#County').val()
    $.ajax({
        url: "/basket/proceedCheckOut",
        type: "POST",
        data: {
            address: address,
        },
        async: true,
        success: function (response) {
            console.log(response)

            //
            // $('#itmesNumber').html(0)
            // $('#product_in_basket_' + product_index).remove()
            // $('#notification_box').html('<div class="woocommerce-message" id="notifDiv" role="alert">\n' +
            //     '<i class="notifIcon mt-6 pb-0 fa-solid fa-circle-check"></i>  “' + product_name + '” removed.\n' +
            //     '<a href=""\n' +
            //     '   class="restore-item">Undo?</a>\n' +
            //     '</div>')
            //
            // $('#notifDiv').on('click', function () {
            //     $('#notifDiv').remove()
            // });

        },
    });

}

function updateBasket() {
    $('#updateBasketBtn').removeAttr('disabled')
}

function updateAddress() {
    $('#meetingPlace').html($('#City').val() + ', ' + $('#Postcode').val() + ', ' + $('#County').val())
}

function confirmUpdate(length) {
    const new_quantities = [];

    for (let i = 1; i <= length; i++) {
        new_quantities.push($('#itemQuantityInBasket_' + i).val())
    }

    $.ajax({
        url: "/basket/update",
        type: "POST",
        data: {
            new_quantities: new_quantities,
        },
        async: true,
        success: function (response) {
            let totalPrice = 0
            for (let i = 0; i < length; i++) {
                $('#itemPrice_' + (i + 1)).html(new_quantities[i] * $('#price_' + (i + 1)).data('value') + '<span class="woocommerce-Price-currencySymbol">Dt</span>')
                totalPrice += new_quantities[i] * $('#price_' + (i + 1)).data('value');
            }
            $('#notification_box').html('<div class="woocommerce-message" id="notifDiv" role="alert">\n' +
                '<i class="notifIcon mt-6 pb-0 fa-solid fa-circle-check"></i>  “ Basket has been updated ”\n' +
                '<a href=""\n' +
                '   class="restore-item">Undo?</a>\n' +
                '</div>')

            $('#notifDiv').on('click', function () {
                $('#notifDiv').remove()
            });

            $('#updateBasketBtn').attr('disabled', '')
            $('#subtotal_price').html(totalPrice + '<span class="woocommerce-Price-currencySymbol">Dt</span>')
            $('#total_price').html(totalPrice + '<span class="woocommerce-Price-currencySymbol">Dt</span>')

        },
    });
}

function showFormLocation() {
    $('#hidden_form').toggleClass('expanded')
}


function add2Card(id, product_index) {
    $('#product_' + product_index).attr('onclick', "")
    let value = "../../marketPlaceImages/basketAnimation.gif"
    $('#product_' + product_index).html('<img src="' + value + '" />')
    $.ajax({
        url: "/basket/new",
        type: "POST",
        data: {
            id: id
        },
        async: true,
        success: function (response) {
            setTimeout(function () {
                $('#product_' + product_index).html('<i class="mt-2 fi fi-rr-shopping-cart-add"></i>')
                $('#itmesNumber').html(response)
                $('#product_' + product_index).attr('onclick', 'add2Card(' + id + ',' + product_index + ')')
            }, 1300)
        },
    });
}

function removeItem(id, product_name, product_index,length) {
    $.ajax({
        url: "/basket/remove",
        type: "POST",
        data: {
            id: id
        },
        async: true,
        success: function (response) {
            $('#itmesNumber').html(response)
            $('#product_in_basket_' + product_index).remove()
            $('#notification_box').html('<div class="woocommerce-message" id="notifDiv" role="alert">\n' +
                '<i class="notifIcon mt-6 pb-0 fa-solid fa-circle-check"></i>  “' + product_name + '” removed.\n' +
                '<a href=""\n' +
                '   class="restore-item">Undo?</a>\n' +
                '</div>')

            length-=1
            const new_quantities = [];

            for (let i = 1; i <= length; i++) {
                new_quantities.push($('#itemQuantityInBasket_' + i).val())
            }

            let totalPrice = 0
            for (let i = 0; i < length; i++) {
                $('#itemPrice_' + (i + 1)).html(new_quantities[i] * $('#price_' + (i + 1)).data('value') + '<span class="woocommerce-Price-currencySymbol">Dt</span>')
                totalPrice += new_quantities[i] * $('#price_' + (i + 1)).data('value');
            }

            $('#subtotal_price').html(totalPrice + '<span class="woocommerce-Price-currencySymbol">Dt</span>')
            $('#total_price').html(totalPrice + '<span class="woocommerce-Price-currencySymbol">Dt</span>')


            $('#notifDiv').on('click', function () {
                $('#notifDiv').remove()
            });

        },
    });
}



