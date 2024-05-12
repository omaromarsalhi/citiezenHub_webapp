function addReclamation() {
    let subject = $('#subject').val().toLowerCase();
    let message = $('#contact-message').val().toLowerCase();
    let privatekey = $('#private-key').val();

    if($('#condition').prop('checked') === false || subject === '' || message === ''){
        showInvalidPop('You have to agree to the terms and conditions');
        return;
    }


    let form_data = new FormData();
    const image = $('#reclamation_imageFile_file').prop('files')[0];
    console.log(image);
    form_data.append('image', image);
    form_data.append('subject', subject);
    form_data.append('message', message);
    form_data.append('privatekey', privatekey);


    const badWords = ['bad', 'khalil', 'rmila', '3A4'];
    let foundBadWordInSubject = null;
    let foundBadWordInMessage = null;

    // Check each bad word to find a match and store which word was found
    badWords.forEach(badWord => {
        if (subject.includes(badWord) && !foundBadWordInSubject) {
            foundBadWordInSubject = badWord;
        }
        if (message.includes(badWord) && !foundBadWordInMessage) {
            foundBadWordInMessage = badWord;
        }
    });

    if (foundBadWordInSubject || foundBadWordInMessage) {
        let alertMessage = "Please avoid using inappropriate language. Found: ";
        if (foundBadWordInSubject) {
            alertMessage += `"${foundBadWordInSubject[0]}${'*'.repeat(foundBadWordInSubject.length - 1)}" in Subject`;
        }
        if (foundBadWordInMessage) {
            if (foundBadWordInSubject) {
                alertMessage += " and ";
            }
            alertMessage += `"${foundBadWordInMessage[0]}${'*'.repeat(foundBadWordInMessage.length - 1)}" in Message`;
        }
        showInvalidPop(alertMessage);
        return;
    }

    $.ajax({
        url: "/reclamation/new",
        type: "POST",
        data: form_data,
        async: true,
        processData: false,
        contentType: false,
        success: function (response) {
            $('#subject').val('');
            $('#contact-message').val('');
            $('#condition').prop('checked', false);
            showValidPop('Reclamation made successfully');
        }
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
document.addEventListener('DOMContentLoaded', function() {
    function startRecording() {
        const recognition = new window.webkitSpeechRecognition();
        recognition.lang = 'en-US';

        recognition.onresult = function(event) {
            const transcript = event.results[0][0].transcript;
            document.getElementById('contact-message').value = transcript;
        }

        recognition.start();
    }

    document.getElementById('startRecording').addEventListener('click', startRecording);
});
