
function parserMessagesErreur(reponseTexte) {
    // Rechercher la partie JSON contenant les messages d'erreur
    const startIndex = reponseTexte.indexOf('{"success":false,"errors":');
    if (startIndex === -1) {
        console.error("Format de réponse d'erreur invalide.");
        return {}; // Renvoyer un objet vide si le format est invalide
    }
    const erreurJSON = reponseTexte.substring(startIndex);
    try {
        const errorObj = JSON.parse(erreurJSON);
        return errorObj.errors || {};
    } catch (error) {
        console.error("Erreur d'analyse JSON :", error);
        return {};
    }
}
function showLoaderAndBlockUI() {
    const loader = document.getElementById('loader');
    const mainContent = document.getElementById('main-content');
    loader.style.display = 'flex';
    mainContent.style.pointerEvents = 'none';
    mainContent.style.filter = 'blur(5px)';
    setTimeout(() => {
        const form = document.getElementById("signup-form");
        form.submit();
    }, 100);
}
function afficherMessagesErreur(erreurs) {

    if (Object.keys(erreurs).length === 0) {
        return;
    }
    removeInputs();
    for (const champ in erreurs) {
        console.log(champ);
        const conteneurErreurs = document.getElementById(champ);
        const contientTexte = conteneurErreurs.textContent.trim().length > 0;
        const messageErreur = erreurs[champ];
        conteneurErreurs.classList.add('test');
        conteneurErreurs.textContent = messageErreur;

    }


}
const customAlert = {
    alertWithPromise: function (message) {
        return new Promise(function (resolve, reject) {
            if (confirm(message)) {
                resolve();
            } else {
                reject();
            }
        });
    }
};
function removeInputs() {
    const elementsSansStyle = document.querySelectorAll('.test');
    elementsSansStyle.forEach(element => {
        element.innerHTML='';
    });
}

function editProfile(event) {

    event.preventDefault();
    // showLoaderAndBlockUI("test");
    let formData = new FormData();
    let name=$('#firstnamee').val();
    let lastname=$('#lastnamee').val();
    let email=$('#email').val();
    let role=$('#role').val();
    let age=$('#agee').val();
    let gender=$('#gender').val();
    let status=$('#status').val();
    let cin=$('#cinn').val();
    let phoneNumber=$('#phoneNumberr').val();
    let date=$('#date').val();
    formData.append('image',$('#nipa').prop('files')[0]);
    formData.append('name',name);
    formData.append('lastname',lastname);
    formData.append('email',email);
    formData.append('role',role);
    formData.append('age',age);
    formData.append('gender',gender);
    formData.append('status',status);
    formData.append('cin',cin);
    formData.append('phoneNumber',phoneNumber);
    formData.append('date',date);
    $.ajax({
        url: '/editProfile',
        type: "POST",
        data:formData,
        async: true,
        processData: false,
        contentType: false,
        success: function (response) {
            if (response.success) {
                let user = response.user;
                console.log(user)
                removeInputs();
                $('#notification_box').html('<div class="woocommerce-message" id="notifDiv" role="alert">\n' +
                    '<i class="notifIcon mt-6 pb-0 fa-solid fa-circle-check"></i>  removed.\n' +
                    '<a href=""\n' +
                    '   class="restore-item">Undo?</a>\n' +
                    '</div>')
              //  addErrorMessage(" Profile edited with sucess",'success','message');
              //   alert('Profile edited with sucess.');
            } else  {
                let errors = response.errors;
                console.log(errors);
                alert('Il y a des erreurs dans le formulaire. Veuillez corriger et réessayer.');
            }

        },
        error: function (response) {
            const messagesErreur = parserMessagesErreur(response.responseText);
            console.log(messagesErreur);
            afficherMessagesErreur(messagesErreur);
           // addErrorMessage("you should fixed your errors",'error','message');
            alert('Il y a des erreurs dans le formulaire. Veuillez corriger et réessayer.');




        },

    });
}

function addErrorMessage(message,classeStyle,inputId) {
    const conteneurErreurs = document.getElementById(inputId);
    const elementErreur = document.createElement('div');
    conteneurErreurs.classList.add('message-container');
    elementErreur.classList.add(classeStyle);
    elementErreur.textContent=message;
    conteneurErreurs.appendChild(elementErreur);
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
    let email=$('#email').val();
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
            customAlert.alertWithPromise('Votre mot de passe a été modifié avec succès. Vous devez vous déconnecter et saisir vos nouvelles informations.')
                .then(function () {

                    $('#email').val('');
                    $('#oldPass').val('');
                    $('#NewPass').val('');
                    $('#rePass').val('');
                    // window.location.href = '/logout';
                })
                .catch(function () {
                    console.log("L'utilisateur a annulé l'action.");
                });
        },
        error: function (response) {
            const messagesErreur = parserMessagesErreur(response.responseText);
            console.log(messagesErreur);
            afficherMessagesErreur(messagesErreur);



        },
    });
}
function AddMunicipality(event) {
    event.preventDefault();
    let formData = new FormData();
    let name=$('#namee').val();
    let adresse=$('#adresse').val();
    formData.append('imagee',$('#createinputfile').prop('files')[0]);
    formData.append('name',name);
    formData.append('adresse',adresse);
    $.ajax({

        url: '/AddMunicipality',
        type: "POST",
        data:formData,
        async: true,
        processData: false,
        contentType: false,
        success: function (response) {
            console.log(response.state);
            removeInputs();
            Swal.fire(
                'Ajouté!',

                'success'
            )
        },
        error: function (response) {
            const messagesErreur = parserMessagesErreur(response.responseText);
            console.log(messagesErreur);
            afficherMessagesErreur(messagesErreur);
        },
    });
}


function DeleteCustomer(event) {
}

function afficherMessage() {
    $('#notification_box').html('<div class="woocommerce-message" id="notifDiv" role="alert">\n' +
        '<i class="notifIcon mt-6 pb-0 fa-solid fa-circle-check"></i>  removed.\n' +
        '<a href=""\n' +
        '   class="restore-item">Undo?</a>\n' +
        '</div>')
    console.log("kkkkk");
}
function editProfileAdmin(event) {

    event.preventDefault();

    // showLoaderAndBlockUI("test");
    let formData = new FormData();
    let name=$('#firstnamee').val();
    let lastname=$('#lastnamee').val();
    let email=$('#email').val();
    let address=$('#address').val();
    let role=$('#role').val();
    let age=$('#agee').val();
    let gender=$('#gender').val();
    let status=$('#status').val();
    let cin=$('#cinn').val();
    let phoneNumber=$('#phoneNumberr').val();
    let date=$('#dob').val();
    formData.append('image',$('#upload-settings-porfile-picture').prop('files')[0]);
    formData.append('name',name);
    formData.append('lastname',lastname);
    formData.append('email',email);
    formData.append('address',address);
    formData.append('role',role);
    formData.append('age',age);
    formData.append('gender',gender);
    formData.append('status',status);
    formData.append('cin',cin);
    formData.append('phoneNumber',phoneNumber);
    formData.append('date',date);
    $.ajax({
        url: '/editProfileAdmin',
        type: "POST",
        data:formData,
        async: true,
        processData: false,
        contentType: false,
        success: function (response) {
            if (response.success) {
                let user = response.user;
                console.log(user)
                removeInputs();
                 addErrorMessage(" Profile edited with sucess",'success','message');
                //   alert('Profile edited with sucess.');
            } else  {
                let errors = response.errors;
                console.log(errors);
                alert('Il y a des erreurs dans le formulaire. Veuillez corriger et réessayer.');
            }

        },
        error: function (response) {
            const messagesErreur = parserMessagesErreur(response.responseText);
            console.log(messagesErreur);
            afficherMessagesErreur(messagesErreur);
            // addErrorMessage("you should fixed your errors",'error','message');
            alert('Il y a des erreurs dans le formulaire. Veuillez corriger et réessayer.');

        },

    });
}


