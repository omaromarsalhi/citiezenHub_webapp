// OMAR SALHI  IS THE OWNER OF THIS PIECE OF USELESS CODE

/*$(document).ready(function () {
    // DisplayListProducts();
});*/


function generatePdf(index) {
    let value = "../../marketPlaceImages/basketAnimation.gif"
    $('#generatePdfBtn_' + index).removeClass('pdfBtn');
    $('#generatePdfBtn_' + index).addClass('pdfBtn_disabled');
    $('#generatePdfBtn_' + index).html('<img class="loaderPdf" src="' + value + '" />')

    setTimeout(function () {
        $('#generatePdfBtn_' + index).addClass('pdfBtn');
        $('#generatePdfBtn_' + index).removeClass('pdfBtn_disabled');
        $('#generatePdfBtn_' + index).html('<span class="text_pdf">Contract</span> <i class="fa-solid fa-download"></i>')
    }, 2000)
}


function DisplayListProducts4Owner(movement_direction, page) {
    $.ajax({
        url: '/user/dashboard/',
        type: "post",
        data: {
            movement_direction: movement_direction,
            page: page
        },
        async: true,
        success: function (response) {

            console.log(response)

            $("#sub-" + page + "-block").html(response.template);
            setTimeout(function () {
                launchSwiper();
            }, 1000);


            $("#" + page + "-page-" + response.previousPage).removeClass("active")
            $("#" + page + "-page-" + response.currentPage).addClass("active")
        },
        error: function (response) {
            console.log(response);
        },
    });
}