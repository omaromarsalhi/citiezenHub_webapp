


function addTransport(event) {
    event.preventDefault();
    const TypeVehicule = document.getElementById("typeSelect").value;
    let depart =document.getElementById("station-depart").value;
    let arrive=document.getElementById("station-arrive").value;
    let formData = new FormData();
    let reference = $('#Reference').val();  
    let Time = $('#Time').val();
    let prix = $('#Prix').val();
 
alert("aaaa");
    formData.append('image', $('#createinputfile').prop('files')[0]);
    formData.append('reference', reference);
    formData.append('time', Time);
    formData.append('type_vehicule', TypeVehicule);
    if(prix===''){
    prix=prix;
    formData.append('prix',0);
    }
    else 
    formData.append('prix', prix);

    formData.append('depart', depart);
    formData.append('arrive', arrive);
    console.log(Time);

    $.ajax({
        
        url: '/addTransport',
        type: "POST",
        data: formData,
        async: true,
        processData: false,
        contentType: false,
        beforeSend: function() {

        },
        success: function(response) {
          
              if (response.message === "Transport added successfully.") {
                alert("Added successfully");
                $('#addDealModal').modal('hide');
                $('#reference').val('');
                $('#time').val('');
                $('#prix').val('');
                $('#createinputfile').closest('form').get(0).reset();
               window.location.reload();
            } 
        },
        error: function(response) {
        if (response.message == "aaa") {
  alert("duplicate in database")
  } 
          else if (response.responseJSON && response.responseJSON.error === 'VALIDATION_ERROR') {
        const errorMessage = response.responseJSON.messages.join(', '); // Join error messages with ','
        const errorMessagesArray = errorMessage.split(','); // Split the error messages on ','
        let errorMessagesHTML = ''; // Initialize an empty string to store HTML for error messages

        // Loop through each error message and create HTML for it
        errorMessagesArray.forEach((message) => {
            errorMessagesHTML += `<div>${message.trim()}</div>`; // Trim whitespace and wrap each message in a <div>
        });
        $('.error-label').html(errorMessagesHTML);
    } else if (response.responseJSON && response.responseJSON.error === 'DUPLICATE_ENTRY') {
        // Handle duplicate entry error
        alert("Error: " + response.responseJSON.message);
        console.log("Duplicate entry");
    } else {
        // Handle other errors
        alert("An error occurred while inserting the subscription: " + response.responseJSON.message);
        console.log("Database error");
    }
}
        
    });
}


    

function updateTransport(event) {
    event.preventDefault();
    const TypeVehicule = document.getElementById("typeSelectUpd").value;
    let depart =document.getElementById("station-departUpd").value;
    let arrive=document.getElementById("station-arriveUpd").value;
    let formData = new FormData();
    let reference = $('#ReferenceUpd').val();  
    let Time = $('#TimeUpd').val();
    let prix = $('#PrixUpd').val();
 


    formData.append('image', $('#createinputfileUpd').prop('files')[0]);
    formData.append('reference', reference);
    formData.append('time', Time);
    formData.append('type_vehicule', TypeVehicule);
    formData.append('prix', prix);
    formData.append('depart', depart);
    formData.append('arrive', arrive);
        if(prix===''){
    prix=prix;
    formData.append('prix',0);
    }
    else 
    formData.append('prix', prix);

    $.ajax({
        url: '/updateTransport/' + maVariableGlobale,
        type: "POST",
        data: formData,
        async: true,
        processData: false,
        contentType: false,
        beforeSend: function() {
            // Add any pre-request logic here
        },
        success: function(response) {
            if (response.message == "Transport added successfully.") {
                $('#updateDealModal').modal('hide');
                $('#ReferenceUpd').val('');
                $('#PrixUpd').val('');
                $('#createinputfileUpd').closest('form').get(0).reset();
                 window.location.reload();
                
            } 
        },
        error: function (response) {
       if (response.error == "DUPLICATE_ENTRY") {
  alert(duplicate in database)
  } 
        else if (response.responseJSON && response.responseJSON.error === 'VALIDATION_ERROR') {
        const errorMessage = response.responseJSON.messages.join(', '); // Join error messages with ','
        const errorMessagesArray = errorMessage.split(','); // Split the error messages on ','
        let errorMessagesHTML = ''; // Initialize an empty string to store HTML for error messages

        // Loop through each error message and create HTML for it
        errorMessagesArray.forEach((message) => {
            errorMessagesHTML += `<div>${message.trim()}</div>`; // Trim whitespace and wrap each message in a <div>
        });
        $('.error-label').html(errorMessagesHTML);
    } else if (response.responseJSON && response.responseJSON.error === 'DUPLICATE_ENTRY') {
        // Handle duplicate entry error
        alert("Error: " + response.responseJSON.message);
        console.log("Duplicate entry");
    } else {
        // Handle other errors
        alert( response.responseJSON.message);
        console.log("Database error");
    }
        },
    });
}



    const inputFields = document.querySelectorAll('.updateDealModal');
    const prixregex = document.querySelectorAll('.PrixUpd');
    const referenceRegex = document.querySelectorAll('.ReferenceUpd');
    
 

    
    prixregex.forEach(inputField => {
        inputField.addEventListener('keyup', function() {
            // Check if the input value contains a number
            if (/\d/.test(this.value)) {
                this.classList.add('input-modified');
                this.classList.add('input-valid');
            } else {
                this.classList.remove('input-modified');
                this.classList.remove('input-invalid');
                this.classList.add('input-typing');
            }
        });
    });

    
    referenceRegex.forEach(inputField => {
        inputField.addEventListener('keyup', function() {
            // Check if the input value contains a number
            if (/\d/.test(this.value)) {
                this.classList.add('input-modified');
                this.classList.add('input-valid');
            } else {
                this.classList.remove('input-modified');
                this.classList.remove('input-invalid');
                this.classList.add('input-typing');
            }
        });
    });


    const fileInputs = document.querySelectorAll('input[type="file"]');

    fileInputs.forEach(fileInput => {
        fileInput.addEventListener('change', function() {
            // Check if files have been selected
            if (this.files.length > 0) {
                console.log('No file has been selected.');
                this.classList.remove('input-modified');
                this.classList.remove('input-valid');
                this.classList.add('input-typing');
            } else {
                console.log('A file has been selected.');
                this.classList.add('input-modified');
                this.classList.add('input-invalid');
            }
        });
    });

    document.getElementById('createDealBtn').addEventListener('click', function() {
        // Validate all fields
        if (validateFields()) {
            // All fields are valid, proceed with the action
            console.log('All fields are valid. Proceeding with the action...');
            // Add your code to perform the action (e.g., submit the form)
        } else {
            // Some fields are not valid, display an error message
            alert('Error: Please fill out all fields correctly.');
        }
    });

    function validateFields() {
        // Check if all fields are valid
        const inputFields = document.querySelectorAll('.input-target');
        let allFieldsValid = true;

        inputFields.forEach(inputField => {
            if (inputField.value.trim() === '') {
                // Field is empty
                allFieldsValid = false;
                return;
            }

            // Check if the field color is #50C878
            if (getComputedStyle(inputField).border-color !== 'rgb(80, 200, 120)') {
                // Field color is not #50C878
                allFieldsValid = false;
                return;
            }
                if (getComputedStyle(inputField).border-color == 'rgb(80, 200, 120)') {
                // Field color is not #50C878
                allFieldsValid = true;
                return;
            }
        });

        // Return whether all fields are valid
        return allFieldsValid;
    }


 
function deleteTransport(transportid) {
    if (confirm("Êtes-vous sûr de vouloir supprimer cette transport ?")) {
        $.ajax({
            url: '/transport/' + transportid,
            type: 'DELETE',
            success: function(response) {
              alert(response.message);
              window.location.reload();

            },
            error: function(xhr, status, error) {
                              window.location.reload();

            }
        });
        
    }
}


function updateTransportList(lisTransport) {
    // Clear the existing station list
    $('#table-regions-by-revenue').empty();

    // Append the new station list
    lisTransport.forEach(function(transport) {
        let stationHTML = `   
    
                      <tr>
                      <td class="white-space-nowrap ps-0 country" style="width:32%">
                        <div class="d-flex align-items-center">
                           <a href="#!">
                            <div class="d-flex align-items-center"><img src="assetsAdmin/assets/img/country/india.png" alt="" width="24" />
                              <p class="mb-0 ps-3 text-primary fw-bold fs-9" >India</p>
                            </div>
                          </a>
                        </div>
                      </td>
                       <td class="white-space-nowrap ps-0 country" style="width:32%">
                        <div class="d-flex align-items-center">
                           <a href="#!">
                            <div class="d-flex align-items-center"><img src="assetsAdmin/assets/img/country/india.png" alt="" width="24" />
                              <p class="mb-0 ps-3 text-primary fw-bold fs-9" >India</p>
                            </div>
                          </a>
                        </div>
                      </td>
                      <td class="align-middle users" style="width:17%">
                        <h6 class="mb-0">92896(41.6%)</span></h6>
                      </td>
                      <td class="align-middle text-end transactions" style="width:17%">
                        <h6 class="mb-0">67(34.3%)</span></h6>
                      </td>
                           <td class="align-middle text-end transactions" style="width:17%">
                        <h6 class="mb-0">67(34.3%)</span></h6>
                      </td>
                           <td class="align-middle text-end transactions" style="width:17%">
                        <h6 class="mb-0">67(34.3%)</span></h6>
                      </td>
                           <td class="align-middle text-end transactions" style="width:17%">
                        <h6 class="mb-0">67(34.3%)</span></h6>
                      </td>
                             <td class="align-middle white-space-nowrap text-end pe-0">
                      <div class="position-relative">
                        <div class="hover-actions"> <button class="btn btn-sm btn-phoenix-secondary me-1 fs-10" onclick=""></span></button><button class="btn btn-sm btn-phoenix-secondary fs-10" onclick=""><span class="fas fa-trash"></span></button></div> 
                        </div>
                      <div class="btn-reveal-trigger position-static"><button class="btn btn-sm dropdown-toggle dropdown-caret-none transition-none btn-reveal fs-10" type="button" data-bs-toggle="dropdown" data-boundary="window" aria-haspopup="true" aria-expanded="false" data-bs-reference="parent"><span class="fas fa-ellipsis-h fs-10"></span></button>
                        <div class="dropdown-menu dropdown-menu-end py-2"><a class="dropdown-item" href="#!">View</a><a class="dropdown-item" href="#!">Update</a>
                          <div class="dropdown-divider"></div><a class="dropdown-item text-danger"   onclick="deleteTransport(${transport.idTransport})">Remove</a>
                        </div>
                      </div>
                    </td>
                 
                    </tr>

        `;
        $('#table-regions-by-revenue').append(stationHTML);
    });
}


function showModifierPopup(id,prix,Reference,Time) {
    maVariableGlobale=id;
    var modal = document.getElementById("updateDealModal");
    let transportPrixInput = document.getElementById("PrixUpd");
    let stationReferenceInput = document.getElementById("ReferenceUpd");
    let stationTimeInput = document.getElementById("TimeUpd");
   // let stationImageInput =document.getElementById("createinputfileUpd");

    // Assuming stationData is an object with properties like name, location, capacity
   transportPrixInput.value = prix;
   stationReferenceInput.value=Reference;
   stationTimeInput.value=Time;

    $('#updateDealModal').modal('show');}