
function addTransport(event) {
    event.preventDefault();
    const selectedOption = document.querySelector('.form-select option:checked');
    const type_text = selectedOption.textContent.trim();
    let formData = new FormData();
    let reference = $('#Reference').val();  
    let Time = $('#Time').val();
    let type = type_text;
    let prix = $('#Prix').val();
     

    formData.append('image', $('#createinputfile').prop('files')[0]);
    formData.append('reference', reference);
    formData.append('time', Time);
    formData.append('type_vehicule', type);
    formData.append('prix', prix);
    formData.append('adressStation', adress);
    formData.append('type_vehicule', type);
    $.ajax({
        url: '/addStation',
        type: "POST",
        data: formData,
        async: true,
        processData: false,
        contentType: false,
        beforeSend: function() {
            // You can add any pre-processing logic here
        },
        success: function(response) {
                  /*if (response.error == 'VALIDATION_ERROR') {
                // Handle validation errors
                alert("Validation failed: " + response.messages.join(', '));
                console.log("c bon")
            }
           else  if (response.error === 'DUPLICATE_ENTRY') {
                // Handle duplicate entry error
                alert("Error: " + response.message);
            } 
             else */ 
              if (response.message === "Transport added successfully.") {
                // Handle success
                alert("Added successfully");
                $('#addDealModal').modal('hide');
                $('#name').val('');
                $('#adressStation').val('');
                $('#createinputfile').val('');
                $('#createinputfile').closest('form').get(0).reset();
                updateStationList(response.stations); 
            } 
        },
        error: function(response) {
       alert(response.error);
       console.log(response.error);
            // Handle AJAX errors
        },
    });
}
