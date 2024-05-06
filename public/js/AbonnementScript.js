console.log('omar');

// document.get("createinputfile").addEventListener("change", function (event) {
//     console.log('omar');
//     var imageFile = this.files[0];
//     testImage(imageFile);
// })

$(document).ready(function () {

    $('#createinputfile').on('input', function() {
        console.log('omar');
        var imageFile = this.files[0];
        testImage(imageFile);
    });
    document.getElementById('name').addEventListener('input', validateName);
    document.getElementById('lastname').addEventListener('input', validateLastName);
});


let popup = document.getElementById("popup");

function openPopup() {
    popup.classList.add("open-popup")

}

function closePopup() {
    popup.classList.remove("open-popup");
    redirectToAnotherRoute();

}

function addPost(event) {
    event.preventDefault();
    const selectedOption = document.querySelector('.nice-select ul li.selected');
    const type_text = selectedOption.textContent.trim();

    let formData = new FormData();
    let name = $('#name').val();
    let lastname = $('#lastname').val();
    let type = type_text;
    const isNameValid = validateName();
    const isLastNameValid = validateLastName();

    if (isNameValid && isLastNameValid) {

        formData.append('image', $('#createinputfile').prop('files')[0]);
        formData.append('name', name);
        formData.append('lastname', lastname);
        formData.append('type', type);
        $.ajax({
            url: '/addAbonnement',
            type: "POST",
            data: formData,
            async: true,
            processData: false,
            contentType: false,
            success: function (response) {
                $('#statusSuccessModal').modal('show');
                redirectToAnotherRoute();
            },
            error: function (response) {
                console.error("error");
                $('#statusErrorsModal').modal('show');

            },
        });
    } else {
        alert("fields arent write §§§§§§§§!");
    }
}


function redirectToAnotherRoute() {
    // Wait for 3 seconds (3000 milliseconds)
    setTimeout(function () {
        // Redirect to another route
        window.location.href = '/showAbonnement'; // Replace '/your-new-route' with the actual route
    }, 2000); // 3000 milliseconds = 3 seconds
}


const nameRegex = /^[A-Za-z\s]+$/;
const lastNameRegex = /^[A-Za-z\s]+$/;

function validateName() {
    const nameInput = document.getElementById('name');
    const name = nameInput.value.trim();
    if (nameRegex.test(name)) {
        nameInput.style.borderColor = 'green';
        return true;
    } else {
        nameInput.style.borderColor = 'red';
        return false;
    }
}

function validateLastName() {
    const lastNameInput = document.getElementById('lastname');
    const lastName = lastNameInput.value.trim();
    if (lastNameRegex.test(lastName)) {
        lastNameInput.style.borderColor = 'green';
        return true;
    } else {
        lastNameInput.style.borderColor = 'red';
        return false;
    }
}// Add event listeners for input fields


function testImage(filePath) {
    var critere = 0;
    let rep = "";
    let formData = new FormData();
    formData.append('file', filePath);
    $.ajax({
        url: '/AbonnementScan',
        type: "POST", // Change the request type to POST
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {

            for (i = 0; i < 10; i++) {
                if (response.result.tags[i].confidence > 0 && response.result.tags[i].tag.en == "person") {
                    critere++;
                    rep = rep + "person avec une confiance de " + response.result.tags[i].confidence + " \n";
                }
                if (response.result.tags[i].confidence > 30 , response.result.tags[i].tag.en == "portrait") {
                    critere++;
                    rep = rep + "Image portrait avec une confiance de " + response.result.tags[i].confidence + "  \n";

                }
            }
            console.log(response);
            console.log(rep);


            if (critere == 2) {
                $('#sucess-message').text(rep);
                $('#statusSuccessModal').modal('show');

                /*
                                                $('#add-takeout-messages').html('<div class="alert alert-success">' +
                                                '<button type="button" class="close" data-dismiss="alert">&times;</button>' +
                                                '<strong><i class="glyphicon glyphicon-ok-sign"></i></strong> ' + response +
                                                '</div>');
                */
            } else {
                rep = " ";
                rep = " Les critere ne sont pas respecté : \n L'image doit etre de type portrait et d'un etre humain   ";
                console.log(rep);
                $('#error-message').text(rep);

                $('#statusErrorsModal').modal('show');

            }
        },
        error: function (xhr, status, error) {
            $('#statusErrorsModal').modal('show');
        }
    });
}


// Add an event listener for when a file is selected

/*
var input = document.getElementById('createinputfile');
  input.addEventListener('change', function() {
    var fullPath = input.value;
    console.log(fullPath);
    testImage(fullPath);

  });*/

//$('#createinputfile').change(function (e) {
// Prints out the file path
//console.log(imagePath);
//  testImage("C:\Users\azeez\Downloads\images (3).jpg");
//});



  
  