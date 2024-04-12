
function showFormLocation() {
    $('#hidden_form').toggleClass('expanded')
}


function add2Card(id,product_index) {
    $('#product_'+product_index).attr('onclick',"")
    let value="../../marketPlaceImages/basketAnimation.gif"
    $('#product_'+product_index).html('<img src="'+value+'" />')
    $.ajax({
        url: "/basket/new",
        type: "POST",
        data: {
            id:id
        },
        async: true,
        success: function (response) {
            setTimeout(function (){
                $('#product_'+product_index).html('<i class="mt-2 fi fi-rr-shopping-cart-add"></i>')
                $('#itmesNumber').html(response)
                $('#product_'+product_index).attr('onclick','add2Card('+id+','+product_index+')')
            }, 1300)
        },
    });
}

function removeItem(id,product_name,product_index){
    $.ajax({
        url: "/basket/remove",
        type: "POST",
        data: {
            id:id
        },
        async: true,
        success: function (response) {
            $('#itmesNumber').html(response)
            $('#product_in_basket_'+product_index).remove()
            $('#notification_box').html('<div class="woocommerce-message" id="notifDiv" role="alert">\n' +
                '<i class="notifIcon mt-6 pb-0 fa-solid fa-circle-check"></i>  “'+product_name+'” removed.\n' +
                '                                                        <a href=""\n' +
                '                                                           class="restore-item">Undo?</a>\n' +
                '                                                    </div>')

            $('#notifDiv').on('click', function() {
                $('#notifDiv').remove()
            });
        },
    });
}