

function addReclamation() {
    let subject=$('#subject').val()
    let message=$('#contact-message').val()


    if($('#condition').prop('checked')===false || subject==='' || message===''){
        showInvalidPop('you have to agree to the terms and conditions');
        return
    }




    $.ajax({
        url: "/reclamation/new",
        type: "POST",
        data: {
            subject: subject,
            message: message,
        },
        async: true,
        success: function (response) {
            $('#subject').val('')
            $('#contact-message').val('')
            $('#condition').prop('checked',false)
            showValidPop('reclamation made successfully')
        },
    });
}


function deleteReclamation(idReclamation){

}

function deletee(index) {
    let value = "../../marketPlaceImages/basketAnimation.gif"
    $('#deleteReclamation' + index).removeClass('pdfBtn');
    $('#deleteReclamation' + index).addClass('pdfBtn_disabled');
    $('#deleteReclamation' + index).html('<img class="loaderPdf" src="' + value + '" />')

    setTimeout(function () {
        $('#deleteReclamation' + index).addClass('pdfBtn');
        $('#deleteReclamation' + index).removeClass('pdfBtn_disabled');
        $('#deleteReclamation' + index).html('<span class="text_pdf">Delete</span> <i class="fa-solid fa-download"></i>')
    }, 2000)
}