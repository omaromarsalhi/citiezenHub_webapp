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
    if(!check_all_inputs()){
        console.log('error')
    }else {
        loader_start()
        let name = $('#name').val();
        let description = $('#description').val();
        let price = $('#price').val();
        let quantity = $('#quantity').val();
        let category = $('#category').val();

        let form_data = new FormData();
        const list = $('#createinputfile').prop('files');
        for (let i = 0; i < list.length; i++) {
            form_data.append('file-' + (i + 1), list[i]);
        }
        form_data.append('name', name);
        form_data.append('description', description);
        form_data.append('price', price);
        form_data.append('quantity', quantity);
        form_data.append('category', category);

        $.ajax({
            url: '/product/new',
            type: "POST",
            data: form_data,
            async: true,
            processData: false,
            contentType: false,
            success: function (response) {
                loader_stop(4000)
                setTimeout(function (){
                    handle_success('the product has been added successfully')
                    $('#createinputfile').val(null)
                    $('#createfileImage').attr('src', '/assets/images/portfolio/portfolio-05.jpg')
                    $('#name').val('');
                    $('#description').val('');
                    $('#price').val('');
                    $('#quantity').val('');
                    $('#category').val('');
                },4100)

            },
            error: function (response) {
                console.log("error");
            },
        });
    }
}

function consultProd() {

    let name = $('#name').val();
    let price = $('#price').val();
    let quantity = $('#quantity').val();
    let category = $('#category').val();

    $("#name_model").html(name.charAt(0).toUpperCase()+name.slice(1).toLowerCase())
    $("#price_model").html(price+' DT')
    $("#quantity_model").html(quantity+' Pieces/ ')
    $("#category_model").html(category.charAt(0).toUpperCase()+category.slice(1).toLowerCase())

    const files = $('#createinputfile').prop('files');
    let html = '<div class="swiper-wrapper" >';
    for (let i = 0; i < files.length; i++) {
        console.log(i)
        html += '<div class="swiper-slide">\n' +
            '<img src="' + URL.createObjectURL(files[i]) + '"' +
            ' alt="" class="fixedImagesSize"/>\n' +
            '</div>'
    }
    html += '<div class="swiper-pagination"></div></div>'
    $("#slider").html(html);
    $("#uploadModal").modal("show");

    launchSwiper()
}

function check_all_inputs(){
    let errors = [];
    $('#error-message-image').html('')

    if(! $('#name').val().match(/^[a-zA-Z][a-zA-Z0-9\s]*$/))
        errors.push({text: "name", el: $('#error-message')});
    if(! $('#price').val().match(/^[1-9]\d{0,10}(,\d{3})*(\.\d{1,2})?$/))
        errors.push({text: "price", el: $('#error-message-price')});
    if(! $('#quantity').val().match(/^[1-9]\d{0,10}(,\d{3})*(\.\d{1,2})?$/))
        errors.push({text: "quantity", el: $('#error-message-quantity')});
    if( $('#description').val().trim() === '')
        errors.push({text: "description", el: $('#error-message-desc')});
    if($('#createinputfile').prop('files').length===0)
        errors.push({text: "image", el: $('#error-message-image')});

    if (errors.length > 0) {
        handle_errors(errors);
        return false
    }
    return true
}
function regex() {

    const all_inputs = {
        name: {
            input_name: 'name',
            regex: /^[a-zA-Z][a-zA-Z0-9\s]*$/,
            error_div: 'error-message',
            error_text: 'Please enter a valid name (letters and numbers only).'
        },
        price: {
            input_name: 'price',
            regex: /^[1-9]\d{0,10}(,\d{3})*(\.\d{1,2})?$/,
            error_div: 'error-message-price',
            error_text: 'Please enter a valid Price (numbers only).'
        },
        quantity: {
            input_name: 'quantity',
            regex: /^[1-9]\d{0,10}(,\d{3})*(\.\d{1,2})?$/,
            error_div: 'error-message-quantity',
            error_text: 'Please enter a valid Quantity (numbers only).'
        }
    }

    for (const key in all_inputs) {
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


function generateDescreption() {
    const errorMessageElement = document.getElementById('error-message-desc');
    let title = $('#name').val();
    console.log(title)
    if (title === '') {
        errorMessageElement.textContent = 'Please enter a valid product name so that Ai can help you generating a description';
    } else {
        loader_start_desc()
        errorMessageElement.textContent=''
        $.ajax({
            url: '/product/generate_description',
            type: "POST",
            data: {
                title: title,
            },
            async: true,
            success: function (response) {
                $('#description').val(response.description)
                loader_stop_desc()
            },
            error: function (response) {
                console.log("error")
            },
        });
    }
}







