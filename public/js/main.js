$(document).ready(function () {
    // countBasket()
    new Splide('#image-slider').mount();
    launchSwiper()


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


function showInvalidPop(msg) {
    $('#error-message').html(msg);
    $('#statusErrorsModal').modal('show')
}

function hideInvalidPop() {
    $('#statusErrorsModal').modal('hide')
}

function showValidPop(msg) {
    $('#success-message').html(msg);
    $('#statusSuccessModal').modal('show')
}

function hideValidPop() {
    $('#statusSuccessModal').modal('hide')
}