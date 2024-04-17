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
function removeInputs() {
    const elementsSansStyle = document.querySelectorAll('.test');
    elementsSansStyle.forEach(element => {
        element.innerHTML='';
    });
}
function editProfile(event) {

    event.preventDefault();
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
                customAlert.alert('Successfully saved Your Notificationm setting');
                // addErrorMessage(" Profile edited with sucess",'success');

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
            addErrorMessage("you should fixed your errors",'error','message');




        },
    });
}


function addErrorMessage(message,classeStyle,inputId) {
    const conteneurErreurs = document.getElementById(inputId);
    console.log("ghhhhhhhhh");
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
const customAlert = {
    alertWithPromise: function (message) {
        return new Promise(function (resolve, reject) {
            if (confirm(message)) {
                resolve(); // Confirmer la promesse si l'utilisateur clique sur OK
            } else {
                reject(); // Rejeter la promesse si l'utilisateur annule
            }
        });
    }
};
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

                    window.location.href = '/logout';
                })
                .catch(function () {
                    console.log("L'utilisateur a annulé l'action.");
                });
        },
        error: function (response) {
            const messagesErreur = parserMessagesErreur(response.responseText);
            console.log(messagesErreur);
            afficherMessagesErreur(messagesErreur);
            // for (const champ in messagesErreur) {
            //     console.log(champ);
            //     const messageErreur = messagesErreur[champ];
            //   afficherMessagesErreur()
            //
            // }


        },
    });
}
function DeleteCustomer(event) {
    //
    // // const button = event.target;
    // // const email = button.dataset.email;
    // // console.log(email);
    // // // event.preventDefault();
    // // let formData = new FormData();
    // // formData.append('email',email);
    // // $.ajax({
    // //
    // //     url: '/Delete/'+ email,
    // //     type: "POST",
    // //     data:formData,
    // //     async: true,
    // //     processData: false,
    // //     contentType: false,
    // //     success: function (response) {
    // //         console.log(response.state);
    // //
    // //     },
    // //     error: function (response) {
    // //         console.log("error");
    // //     },
    // // });
    // const button = event.target;
    // const email = button.dataset.email;
    // console.log(email);
    //
    // // event.preventDefault(); // Empêche le comportement par défaut du bouton (par exemple, soumettre un formulaire)
    //
    // let formData = new FormData();
    // formData.append('email', email);
    //
    // // Configuration de la requête fetch
    // fetch('/Delete/' + email, {
    //     method: 'POST',
    //     body: formData,
    // })
    //     .then(response => {
    //         if (response.ok) {
    //
    //             button.closest('.customer-row').remove();
    //
    //             console.log('Utilisateur supprimé avec succès');
    //
    //         } else {
    //             throw new Error('Erreur lors de la suppression du client');
    //         }
    //     })
    //     .catch(error => {
    //         console.error('Erreur:', error);
    //
    //     });
}

