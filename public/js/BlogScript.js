function addPost(event) {
    event.preventDefault();
    let formData = new FormData();
    let caption = $('#contact-message').val();
    formData.append('image', $('#nipa').prop('files')[0]);
    formData.append('caption', caption);
    $.ajax({

        url: '/new',
        type: "POST",
        data: formData,

        async: true,
        processData: false,
        contentType: false,
        success: function (response) {
            console.log(response.state);
            $('#contact-message').val('');
            $('#nipa').val('');
        },
        error: function (response) {
            console.log("error");
        },
    });
}

var postIdToModify = null;

function handleMenuAction(select, postId, caption, image) {
    var selectedValue = select.value;
    if (selectedValue === "modifier") {
        postIdToModify = postId;
        showModifierPopup(caption, image);
    } else if (selectedValue === "supprimer") {
        deletePost(postId);
    }
}

function deletePost(postId) {
    if (confirm("Êtes-vous sûr de vouloir supprimer ce post ?" + postId)) {
        $.ajax({
            url: '/blog/' + postId,
            type: 'DELETE',
            success: function (response) {
                // Rafraîchir la page ou mettre à jour la liste des posts
                location.reload();
            },
            error: function (xhr, status, error) {
                // Gérer les erreurs en fonction de vos besoins
                console.error(error);
            }
        });
    }
}

function showModifierPopup(caption, image) {
    var modal = document.getElementById("modifierModal");
    var form = document.getElementById("modifierForm");
    let messageTextarea = document.getElementById("captionModfier");
    let imageModifier = document.getElementById("imageModifer");
    messageTextarea.value = caption;
    if (image !== "") {
        imageModifier.src = "images/blog/" + image;
    }
    else {
        imageModifier.src = "aucuneImg.png";
    }
    modal.style.display = "block";
    form.reset();
}


function closeModifierPopup() {
    // Récupérer la fenêtre modale
    var modal = document.getElementById("modifierModal");
    modal.style.display = "none";
}

// Fonction appelée lorsque l'utilisateur soumet le formulaire
function submitModifierForm(event) {
    event.preventDefault();
    let formData = new FormData();
    let caption = $('#captionModfier').val();
    formData.append('caption', caption);
    formData.append('image', $('#nipaUpload').prop('files')[0]);
    console.log('image', $('#nipaUpload').prop('files')[0]);


    $.ajax({
        url: '/edit/' + postIdToModify,
        type: "POST",
        data: formData,

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
    closeModifierPopup();
}

// Cette fonction vérifie si le clic a été effectué en dehors de la fenêtre modale
function windowOnClick(event) {
    var modal = document.getElementById('modifierModal');
    if (event.target === modal) {
        closeModifierPopup();
    }
}

// Ajoutez cet écouteur d'événements à la fenêtre
document.addEventListener('DOMContentLoaded', function () {
    window.addEventListener('click', windowOnClick);
});


function changerImage() {
    document.getElementById("nipaUpload").addEventListener("change", function () {
        var imageFile = this.files[0];
        var reader = new FileReader();
        reader.onload = function (event) {
            var imageModifier = document.getElementById("imageModifer");
            imageModifier.src = event.target.result;
        };
        reader.readAsDataURL(imageFile);
    });
}