// OMAR SALHI  IS THE OWNER OF THIS PIECE OF USELESS CODE

/*$(document).ready(function () {
    // DisplayListProducts();
});*/



function DisplayListProducts4Owner(movement_direction) {
    $.ajax({
        url: '/user/dashboard/',
        type: "post",
        data: {
            movement_direction: movement_direction,
        },
        async: true,
        success: function (response) {
            $("#sub-market-block").html(response);

            setTimeout(function() {
                launchSwiper();
            }, 1000);

            jQuery('html, body').animate({scrollTop: 10}, 550);

            var page_index_to_enable=$('#currentPage').val();
            var page_index_to_disable=$('#previousPage').val();

            document.getElementById("page-"+page_index_to_enable).classList.add("active")
            document.getElementById("page-" +page_index_to_disable ).classList.remove("active")
        },
        error: function (response) {
            console.log(response);
        },
    });
}