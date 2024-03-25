// OMAR SALHI  IS THE OWNER OF THIS PIECE OF USELESS CODE
// $(document).ready(function () {
// });


setTimeout(function () {
    adjustSelection();
    regex();
}, 1000);

function adjustSelection() {
    var element = document.getElementsByClassName("nice-select").item(0);
    element.classList.add("selectCategory");
}



function createProduct(e) {

    let name = $('#name').val();
    let description = $('#description').val();
    let price = $('#price').val();
    let quantity = $('#quantity').val();
    let category = $('#category').val();

    let form_data = new FormData();
    const list=$('#createinputfile').prop('files');
    for (let i=0;i<list.length;i++ ) {
        form_data.append('file-'+(i+1), list[i]);
    }
    form_data.append('name', name);
    form_data.append('description', description);
    form_data.append('price', price);
    form_data.append('quantity', quantity);
    form_data.append('category', category);

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

function consultProd(){
    const files=$('#createinputfile').prop('files');
    let html='<div class="swiper-wrapper" >';   
    for (let i = 0; i < files.length; i++) {
        console.log(i)
        html+='<div class="swiper-slide">\n' +
            '<img src="'+URL.createObjectURL(files[i])+'"'+
            ' alt="" class="fixedImagesSize"/>\n' +
            '</div>'
    }
    html+='<div class="swiper-pagination"></div></div>'
    $("#slider").html(html);
    $("#uploadModal").modal("show");
    launchSwiper()
}


function regex(){
    const regex=/^[a-zA-Z][a-zA-Z0-9\s]*$/

    const all_inputs={
        name:{
            input_name:'name',
            regex:/^[a-zA-Z][a-zA-Z0-9\\s]*$/,
            error_div:'error-message',
            error_text:'Please enter a valid name (letters and numbers only).'
        },
        price:{
            input_name:'price',
            regex:/\d+(\.\d{1,2})?/,
            error_div:'error-message-price',
            error_text:'Please enter a valid price (numbers only).'
        }
    }

    for(const key in all_inputs ) {
        document.getElementById(all_inputs[key].input_name).addEventListener('input', function () {
            const errorMessageElement = document.getElementById(all_inputs[key].error_div);

            if (this.value.match(all_inputs[key].regex)) {
                this.classList.remove('name_regex_f');
                this.classList.add('name_regex_t');
                errorMessageElement.textContent = '';
            } else {
                this.classList.remove('name_regex_t');
                this.classList.add('name_regex_f');
                errorMessageElement.textContent = all_inputs[key].error_text;
            }

            if (this.value.trim().length === 0) {
                this.classList.remove('name_regex_t');
                this.classList.remove('name_regex_f');
                errorMessageElement.textContent = "";
            }
        })
    }
}





