$(document).ready(function () {
    countBasket()
});

function countBasket() {
    $.ajax({
        url: '/basket/count',
        type: "POST",
        async: true,
        success: function (response) {
            $('#itmesNumber').html(response)
        },
        error: function (response) {
            alert(response)
        },
    })
}