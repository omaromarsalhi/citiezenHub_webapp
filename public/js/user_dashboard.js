// OMAR SALHI  IS THE OWNER OF THIS PIECE OF USELESS CODE

/*$(document).ready(function () {
    // DisplayListProducts();
});*/




function DisplayListProducts4Owner(movement_direction, page) {
    $.ajax({
        url: '/user/dashboard/',
        type: "post",
        data: {
            movement_direction: movement_direction,
            page:page
        },
        async: true,
        success: function (response) {

            console.log(response)

            $("#sub-"+page+"-block").html(response.template);
            setTimeout(function() {
                launchSwiper();
            }, 1000);


            $("#"+page+"-page-"+response.previousPage).removeClass("active")
            $("#"+page+"-page-"+response.currentPage).addClass("active")
        },
        error: function (response) {
            console.log(response);
        },
    });
}