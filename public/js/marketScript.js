// OMAR SALHI  IS THE OWNER OF THIS PIECE OF USELESS CODE


$(document).ready(function () {
    $('input[type="checkbox"]').click(function () {
        let currentState=this.checked
        let keyValue = this.id.split('_')
        Object.keys(filterBy[keyValue[0]]).forEach(function (key) {
            let id= keyValue[0] +'_'+key
            console.log(id)
            $('#'+id).prop('checked',false)
            filterBy[keyValue[0]][key] = false
            }
        )

        $(this).prop('checked',currentState)
        filterBy[keyValue[0]][keyValue[1]] = currentState
        console.log(filterBy);
        filterByPrice()
    })
});


function filterByPrice() {
    console.log(filterBy);
    $.ajax({
        url: '/market/place/filtered',
        type: "post",
        data: {
            filterBy: filterBy,
        },
        async: true,
        success: function (response) {

            $("#sub-market-block").html(response.subMarket);
            $("#navPages").html(response.nav);

            setTimeout(function () {
                showProducts();
                launchSwiper();
            }, 1000);

            jQuery('html, body').animate({scrollTop: 10}, 550);

            var page_index_to_enable = $('#currentPage').val();
            var page_index_to_disable = $('#previousPage').val();

            document.getElementById("page-" + page_index_to_enable).classList.add("active")
            document.getElementById("page-" + page_index_to_disable).classList.remove("active")
        },
        error: function (response) {
            console.log(response);
        },
    });
}


function DisplayListProducts(movement_direction) {
    $.ajax({
        url: '/market/place/',
        type: "post",
        data: {
            movement_direction: movement_direction,
        },
        async: true,
        success: function (response) {
            $("#sub-market-block").html(response);

            setTimeout(function () {
                showProducts();
                launchSwiper();
            }, 1000);

            jQuery('html, body').animate({scrollTop: 10}, 550);

            var page_index_to_enable = $('#currentPage').val();
            var page_index_to_disable = $('#previousPage').val();

            document.getElementById("page-" + page_index_to_enable).classList.add("active")
            document.getElementById("page-" + page_index_to_disable).classList.remove("active")
        },
        error: function (response) {
            console.log(response);
        },
    });
}

function showProducts() {
    var children = document.getElementById("sub-market-block").children;
    for (var i = 0; i < children.length; i++) {
        var child = children[i];
        child.classList.add("sal-animate");
    }
}

// function executeTasks() {
//     var currentTask = tasks.shift(); // Get the first function
//
//     if (currentTask) {
//         currentTask(); // Execute the current task
//
//         setTimeout(function() {
//             executeTasks(); // Call itself recursively after delay
//         }, 2000); // Delay in milliseconds (adjust as needed)
//     }
// }

function deletUser(id) {
    $.ajax({
        url: "/user/delete",
        type: "POST",
        data: {
            id: id,
        },
        async: true,
        success: function (response) {
            if (response == "success") {
                DisplayTableMenu();
            } else {
                alert("something went wrong");
            }
        },
    });
}

// function createUser() {
//   $.ajax({
//     url: "/user/create",
//     type: "POST",
//     data: {
//       id: id,
//     },
//     success: function (response) {
//       if (response == "success") {
//         DisplayTableMenu();
//       } else {
//         alert("something went wrong");
//       }
//     },
//   });
// }
