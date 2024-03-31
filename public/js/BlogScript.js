function createPostHTML(post) {
    var imageHTML = '';
    if (post.image) {
        imageHTML = `
            <div class="thumbnail">
                <a href="blog-details.html">
                    <img src="images/blog/${post.image}"
                         alt="Personal Portfolio Images">
                </a>
            </div>
        `;
    }
    return `
        <div class="col-xl-8 col-lg-8">
            <div class="rn-blog single-column mb--30" data-toggle="modal" data-target="#exampleModalCenters">
                <div class="inner">
                    <div class="content mb-4">
                        <div class="category-info">
                            <div class="category-list">
                                <img src="assets/images/activity/activity-01.jpg" height="50"
                                     width="50" style="margin-right: 10px">
                                <a href="blog-details.html">Omar marrakchi</a>
                            </div>
                            <div class="meta">
                                <select onchange="handleMenuAction(this, ${post.id}, '${post.caption}', '${post.image}')">
                                    <option value="" disabled selected hidden><i class="fas fa-cogs"></i>
                                    </option>
                                    <option value="modifier">Modifier</option>
                                    <option value="supprimer">Supprimer</option>
                                </select>
                            </div>
                        </div>
                        <span>${post.datePost}</span>
                        <h4 class="title"><a href="blog-details.html">${post.caption} <i
                                    class="feather-arrow-up-right"></i></a></h4>
                    </div>
                    ${imageHTML}
                </div>
            </div>
        </div>
    `;
}

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
            if (response.success) {
                var newPostHTML = createPostHTML(response.post);
                $('#postsContainer').prepend(newPostHTML);
                $('#contact-message').val('');
                $('#nipa').val('');
                $('#rbtinput2').attr('src', 'aucuneImg.png');
                $('#ajoutPost').prop('disabled', true);
            } else {
                console.error('Failed to create post: ' + response.message);
            }
        },
        error: function (response) {
            console.error("error");
        },
    });
}

document.addEventListener('DOMContentLoaded', function() {
    var textarea = document.getElementById('contact-message');
    var fileInput = document.getElementById('nipa');
    var submitButton = document.getElementById('ajoutPost');

    // Fonction pour vérifier l'état du bouton
    function verifierEtatBouton() {
        var caption = textarea.value.trim();
        var image = fileInput.files.length > 0;
        // Le bouton est activé si le caption ou l'image est présent, mais pas les deux vides
        submitButton.disabled = !(caption || image);
    }

    // Écouteur d'événements pour la textarea
    textarea.addEventListener('input', verifierEtatBouton);

    // Écouteur d'événements pour l'input de fichier
    fileInput.addEventListener('change', verifierEtatBouton);

    // Vérification initiale au chargement de la page
    verifierEtatBouton();
});



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