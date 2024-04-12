
function addStation(event) {
    event.preventDefault();
    const selectedOption = document.querySelector('.form-select option:checked');
    const type_text = selectedOption.textContent.trim();
    let formData = new FormData();
    let name = $('#name').val();  
    let adress = $('#adressStation').val();
    let type = type_text;

    formData.append('image', $('#createinputfile').prop('files')[0]);
    formData.append('nomStation', name);
    formData.append('adressStation', adress);
    formData.append('type_vehicule', type);
    console.log( $('#createinputfile').prop('files')[0]);
    $.ajax({
        url: '/addStation',
        type: "POST",
        data: formData,
        async: true,
        processData: false,
        contentType: false,
        beforeSend: function() {
        },
    
    success: function(response) {

    if (response.message == "Station added successfully .") {
        alert("added successfully");
        fetchUpdatedStationList();
        $('#addDealModal').modal('hide');
        $('#name').val('');
        $('#adressStation').val('');
        $('#createinputfile').val('');
        $('#createinputfile').closest('form').get(0).reset();

 
    } else if (response.errorCode === 'DUPLICATE_ENTRY') {
        alert(response.message);
    }
}
    ,
        error: function (response) {
            
        },
    });
}

    




    const inputFields = document.querySelectorAll('.input-target');

    inputFields.forEach(inputField => {
        inputField.addEventListener('keyup', function() {
            // Check if the input value contains a number
            if (/\d/.test(this.value)) {
                this.classList.add('input-modified');
                this.classList.add('input-invalid');
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
                this.classList.remove('input-invalid');
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


 
function deleteStation(stationId) {
    if (confirm("Êtes-vous sûr de vouloir supprimer cette station ?")) {
        $.ajax({
            url: '/station/' + stationId,
            type: 'DELETE',
            success: function(response) {
                // Once the station is deleted successfully, fetch the updated station list
                fetchUpdatedStationList();
            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    }
}

function fetchUpdatedStationList() {
    $.ajax({
        url: '/stations', // Endpoint to fetch the updated station list
        type: 'GET',
        success: function(updatedStationList) {
            updateStationList(updatedStationList);console.log("aaaaaa");
        },
        error: function(xhr, status, error) {
            console.error(error);
        }
    });
}

function updateStationList(stationList) {
    // Clear the existing station list
    $('#table-latest-review-body').empty();

    // Append the new station list
    stationList.forEach(function(station) {
        console.log(station.NomStation);
        // Generate HTML for each station and append it to the list
        let stationHTML = `           
    <tr class="hover-actions-trigger btn-reveal-trigger position-static">
                    <td class="fs-9 align-middle ps-0">
                      <div class="form-check mb-0 fs-8"><input class="form-check-input" type="checkbox" data-bulk-select-row='{"product":"Apple MacBook Pro 13 inch-M1-8/256GB-space","productImage":"/products/60x60/3.png","customer":{"name":"Woodrow Burton","avatar":"/team/40x40/58.webp"},"rating":4.5,"review":"It&#39;s a Mac, after all. Once you&#39;ve gone Mac, there&#39;s no going back. My first Mac lasted over nine years, and this is my second.","status":{"title":"Pending","badge":"warning","icon":"clock"},"time":"Just now"}' /></div>
                    </td>
                    <td class="align-middle product white-space-nowrap py-0"><a class="d-block rounded-2 border border-translucent" href="apps/e-commerce/landing/product-details.html"><img src=" assetsAdmin/assets/img/products/60x60/3.png" alt="" width="53" /></a></td>
                    <td class="align-middle product white-space-nowrap"><a class="fw-semibold" href="apps/e-commerce/landing/product-details.html">${station.NomStation}</a></td>
                    <td class="align-middle customer white-space-nowrap"><a class="d-flex align-items-center text-body" href="apps/e-commerce/landing/profile.html">
                        <div class="avatar avatar-l"><img class="rounded-circle"  "/images/station/thumbku-66193aabca6b6654721467.png"   alt="" /></div>
                        <h6 class="mb-0 ms-3 text-body"> ${station.TypeVehicule}</h6>
                      </a></td>
                    <td class="align-middle rating white-space-nowrap fs-10"><span class="fa fa-star text-warning"></span><span class="fa fa-star text-warning"></span><span class="fa fa-star text-warning"></span><span class="fa fa-star text-warning"></span><span class="fa fa-star-half-alt star-icon text-warning"></span></td>
                    <td class="align-middle review" style="min-width:350px;">
                      <p class="fs-9 fw-semibold text-body-highlight mb-0">${station.AddressStation}</p>
                    </td>
                    <td class="align-middle text-start ps-5 status"><span class="badge badge-phoenix fs-10 badge-phoenix-warning"><span class="badge-label">Pending</span><span class="ms-1" data-feather="clock" style="height:12.8px;width:12.8px;"></span></span></td>
                    <td class="align-middle text-end time white-space-nowrap">
                      <div class="hover-hide">
                        <h6 class="text-body-highlight mb-0">Just now</h6>
                      </div>
                    </td>
                    <td class="align-middle white-space-nowrap text-end pe-0">
                      <div class="position-relative">
                        <div class="hover-actions"><button class="btn btn-sm btn-phoenix-secondary me-1 fs-10"><span class="fas fa-check"></span></button><button class="btn btn-sm btn-phoenix-secondary fs-10" onclick="deleteStation(${station.id})"><span class="fas fa-trash"></span></button></div>
                      </div>
                      <div class="btn-reveal-trigger position-static"><button class="btn btn-sm dropdown-toggle dropdown-caret-none transition-none btn-reveal fs-10" type="button" data-bs-toggle="dropdown" data-boundary="window" aria-haspopup="true" aria-expanded="false" data-bs-reference="parent"><span class="fas fa-ellipsis-h fs-10"></span></button>
                        <div class="dropdown-menu dropdown-menu-end py-2"><a class="dropdown-item" href="#!">View</a><a class="dropdown-item" href="#!">Export</a>
                          <div class="dropdown-divider"></div><a class="dropdown-item text-danger" href="#!">Remove</a>
                        </div>
                      </div>
                    </td>
                  </tr> 

        `;
        $('#table-latest-review-body').append(stationHTML);
    });
}

function showModifierPopup(stationData) {
    var modal = document.getElementById("addDealModal");
    let stationNameInput = document.getElementById("name");
    let stationLocationInput = document.getElementById("adressStation");

    // Assuming stationData is an object with properties like name, location, capacity
    stationNameInput.value = stationData.NomStation;
    stationLocationInput.value = stationData.AddressStation;

    modal.style.display = "block";
}