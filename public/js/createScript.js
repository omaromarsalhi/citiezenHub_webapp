// OMAR SALHI  IS THE OWNER OF THIS PIECE OF USELESS CODE
// $(document).ready(function () {
// });


setTimeout(function() {
    adjustSelection();
}, 1000);
function adjustSelection() {
    var element=document.getElementsByClassName("nice-select").item(0);
    element.classList.add("selectCategory");
}

// function createProduct(e){
//
//
//     let name=$('#name').val();
//     let description=$('#description').val();
//     let price=$('#price').val();
//     let quantity=$('#quantity').val();
//     let category=$('#category').val();
//     let image=$('#createinputfile').prop('files')[0];
//
//
//     $.ajax({
//         url: '/market/place/new',
//         type: "POST",
//         data: {
//             name: name,
//             description: description,
//             price: price,
//             quantity: quantity,
//             category: category,
//             image:image
//         },
//         async: true,
//         success: function (response) {
//             console.log(response.state);
//         },
//         error: function (response) {
//             console.log("error");
//         },
//     });
// }

function createProduct(e){

    let name=$('#name').val();
    let description=$('#description').val();
    let price=$('#price').val();
    let quantity=$('#quantity').val();
    let category=$('#category').val();

    let form_data = new FormData();

    form_data.append('image',$('#createinputfile').prop('files')[0]);
    form_data.append('name',name);
    form_data.append('description',description);
    form_data.append('price',price);
    form_data.append('quantity',quantity);
    form_data.append('category',category);

    $.ajax({
        url: '/market/place/new',
        type: "POST",
        data:form_data,
        async: true,
        processData: false,
        contentType: false,
        success: function (response) {
            console.log(response.state);
        },
        error: function (response) {
            console.log("error");
        },
    });
}