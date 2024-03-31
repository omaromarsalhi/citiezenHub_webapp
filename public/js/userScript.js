// function DeleteProfile() {
//     if (confirm("Êtes-vous sûr de vouloir supprimer ton compte ?")) {
//         $.ajax({
//             url: '/delete',
//             type: 'DELETE',
//             success: function(response) {
//             },
//             error: function(xhr, status, error) {
//                 console.error(error);
//             }
//         });
//     }
// }
function editProfile(event) {
    event.preventDefault();
    let formData = new FormData();
    let name=$('#firstname').val();
    let lastname=$('#lastname').val();
    let email=$('#email').val();
    let role=$('#role').val();
    let age=$('#age').val();
    let gender=$('#gender').val();
    let status=$('#status').val();
    let cin=$('#cin').val();
    let phoneNumber=$('#phoneNumber').val();

    // let image=$('#image');
    formData.append('image',$('#nipa').prop('files')[0]);
    console.log($('#nipa').prop('files')[0])
    formData.append('name',name);
    formData.append('lastname',lastname);
    formData.append('email',email);
    formData.append('role',role);
    formData.append('age',age);
    formData.append('gender',gender);
    formData.append('status',status);
    formData.append('cin',cin);
    formData.append('phoneNumber',phoneNumber);
    $.ajax({
        url: '/editProfile',
        type: "POST",
        data:formData,
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
function editImage(event) {
    event.preventDefault();
    let formData = new FormData();
    let email=$('#email').val();
    formData.append('imagee',$('#nipa').prop('files')[0]);
    console.log($('#nipa').prop('files')[0])
    $.ajax({

        url: '/editImage',
        type: "POST",
        data:formData,
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
function editPassword(event) {
    event.preventDefault();
    let formData = new FormData();
    let oldPass=$('#oldPass').val();
    let NewPass=$('#NewPass').val();
    let rePass=$('#rePass').val();
    formData.append('oldPassword',oldPass);
    formData.append('newPassword',NewPass);
    formData.append('confirmPassword',rePass);

    $.ajax({

        url: '/changePassword',
        type: "POST",
        data:formData,
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
