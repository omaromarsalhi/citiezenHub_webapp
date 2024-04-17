var currentPage = 1;
var isLoading = false;
var totalPostsCount = 0;
var allPostsLoaded = false;
var postIdToModify = null;
var initialCaption = null;
var currentImageIndices = {};
var posts = [];
var currentImageIndexUpload = 0;

function createPostHTML(post, postUrl) {
    var bouttonImg = '';
    if (post.images.length > 1) {
        bouttonImg = `
            <button class="image-nav" style="position: absolute; background: none; border: none; top: 50%; left: 0; transform: translateY(-50%); font-size: 30px; width: 10px" onclick="changeImage(${post.id}, -1)">&#8592;</button>
            <button class="image-nav" style="position: absolute; background: none; border: none; top: 50%; right: 22px; transform: translateY(-50%); font-size: 30px; width: 10px"" onclick="changeImage(${post.id}, 1)">&#8594;</button>
        `;
    }
    var imageHTML = '';
    if (post.images.length > 0) {
        imageHTML = `
            <div class="thumbnail" style="position: relative;">
                <img id="post-image-${post.id}" src="images/blog/${post.images[0]}"
                     alt="Personal Portfolio Images" style="width: 800px; height: 500px; object-fit: contain;">
                ${bouttonImg}
            </div>
        `;
    }
    return `
        <div class="col-xl-8 col-lg-8" data-post-id="${post.id}">
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
                                <div class="dropdown">
                                    <button class="dropbtn"><i class="fas fa-cog"></i></button>
                                    <div class="dropdown-content">
                                        <button onclick="handleMenuAction(this, ${post.id}, '${post.caption}', '${post.images}', 'modifier')">Modifier</button>
                                        <button onclick="handleMenuAction(this, ${post.id}, '${post.caption}', '${post.images}', 'supprimer')">Supprimer</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <span>${post.datePost}</span>
                        <h4 class="title"><a href="${postUrl}">${post.caption} <i
                                    class="feather-arrow-up-right"></i></a></h4>
                    </div>
                    ${imageHTML}
                    
                    
                    <div class="meta" style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px">
        <div>${post.nbReactions} Reactions</div>
        <div>${post.nbComments} Comments</div>
    </div>
                    
            
                </div>
            </div>
            
            
            
            
        </div>
    `;
}

function changeImage(postId, direction) {
    console.log("aloioa")
    var images = posts.find(post => post.id === postId).images;
    if (!currentImageIndices[postId]) {
        currentImageIndices[postId] = 0;
    }
    currentImageIndices[postId] += direction;
    if (currentImageIndices[postId] < 0) {
        currentImageIndices[postId] = images.length - 1;
    } else if (currentImageIndices[postId] >= images.length) {
        currentImageIndices[postId] = 0;
    }
    document.getElementById(`post-image-${postId}`).src = `images/blog/${images[currentImageIndices[postId]]}`;
}

function loadPostsPage(page) {
    if (isLoading || allPostsLoaded) {
        return;
    }
    isLoading = true;
    document.getElementById('loadingIcon').style.display = 'block';
    $.ajax({
        url: '/blog/page/' + page,
        type: 'GET',
        success: function (response) {
            response.posts.forEach(function (post) {
                var newPostHTML = createPostHTML(post, post.url);
                $('#postsContainer').append(newPostHTML);
                posts.push(post);
            });
            if (response.posts.length < 5) {
                allPostsLoaded = true;
            } else {
                currentPage++;
            }
            isLoading = false;
            document.getElementById('loadingIcon').style.display = 'none';
        },
        error: function (xhr, status, error) {
            console.error(response.message);
            isLoading = false;
            document.getElementById('loadingIcon').style.display = 'none';
        }
    });
}

function getTotalPostsCount(callback) {
    $.ajax({
        url: '/blog/count',
        type: 'GET',
        success: function (response) {
            callback(response.count);
        },
        error: function (xhr, status, error) {
            console.error(error);
        }
    });
}

document.addEventListener('DOMContentLoaded', function () {
    getTotalPostsCount(function (count) {
        totalPostsCount = count;
        loadPostsPage(currentPage);
    });
});

window.onscroll = function () {
    var scrollPosition = window.pageYOffset;
    var windowSize = window.innerHeight;
    var bodyHeight = document.body.offsetHeight;

    // Charger plus de posts 500px avant d'arriver au bas de la page
    if (Math.max(bodyHeight - (scrollPosition + windowSize), 0) < 500) {
        loadPostsPage(currentPage);
    }
};

document.addEventListener('DOMContentLoaded', function () {
    loadPostsPage(currentPage);
});

window.onscroll = function () {
    var scrollPosition = window.pageYOffset;
    var windowSize = window.innerHeight;
    var bodyHeight = document.body.offsetHeight;

    // Charger plus de posts 500px avant d'arriver au bas de la page
    if (Math.max(bodyHeight - (scrollPosition + windowSize), 0) < 500) {
        loadPostsPage(currentPage);
    }
};

function addPost(event) {
    event.preventDefault();
    let formData = new FormData();
    let caption = $('#contact-message').val();
    let files = $('#nipa')[0].files;
    for (let i = 0; i < files.length; i++) {
        formData.append('images[]', files[i]);
    }
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
                var newPostHTML = createPostHTML(response.post, response.post.url);
                $('#postsContainer').prepend(newPostHTML);
                posts.unshift(response.post);
                $('html, body').animate({
                    scrollTop: 950
                }, 300);
                $('#contact-message').val('');
                $('#nipa').val('');
                document.getElementById("previousImage").style.display = "none";
                document.getElementById("nextImage").style.display = "none";
                $('#rbtinput2').attr('src', 'aucuneImg.png');
                $('#ajoutPost').prop('disabled', true);
                document.getElementById("delImage").style.display = "none";
                Swal.fire(
                    'Ajouté!',
                    'Votre post a été ajouté.',
                    'success'
                )
            } else {
                console.error('Failed to create post: ' + response.message);
            }
        },
        error: function (response) {
            console.error("error");
        },
    });
}

document.addEventListener('DOMContentLoaded', function () {
    var textarea = document.getElementById('contact-message');
    var fileInput = document.getElementById('nipa');
    var submitButton = document.getElementById('ajoutPost');

    function verifierEtatBouton() {
        var caption = textarea.value.trim();
        var image = fileInput.files.length > 0;
        submitButton.disabled = !(caption || image);
    }

    textarea.addEventListener('input', verifierEtatBouton);
    fileInput.addEventListener('change', verifierEtatBouton);

    verifierEtatBouton();
});

document.addEventListener('DOMContentLoaded', function () {
    var textarea = document.getElementById('captionModfier');
    var fileInput = document.getElementById('nipaUpload');
    var submitButton = document.getElementById('modifierButton');

    function verifierEtatBoutonModifier() {
        var caption = textarea.value.trim();
        var image = fileInput.files.length > 0;
        var sameCaption = caption === initialCaption;
        submitButton.disabled = !(caption || image) || sameCaption;
    }

    textarea.addEventListener('input', verifierEtatBoutonModifier);
    fileInput.addEventListener('change', verifierEtatBoutonModifier);

    verifierEtatBoutonModifier();
});

function handleMenuAction(button, postId, caption, image, action) {
    if (action === "modifier") {
        postIdToModify = postId;
        showModifierPopup(caption, image);
    } else if (action === "supprimer") {
        deletePost(postId);
    }
}

function deletePost(postId) {
    Swal.fire({
        title: 'Êtes-vous sûr?',
        text: "Vous ne pourrez pas revenir en arrière!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Oui, supprimez-le!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/blog/' + postId,
                type: 'DELETE',
                success: function (response) {
                    $("div[data-post-id='" + postId + "']").remove();
                    Swal.fire(
                        'Supprimé!',
                        'Votre post a été supprimé.',
                        'success'
                    )
                },
                error: function (xhr, status, error) {
                    // Gérer les erreurs en fonction de vos besoins
                    console.error(error);
                }
            });
        }
    });
}

function showModifierPopup(caption, images) {
    var modal = document.getElementById("modifierModal");
    let messageTextarea = document.getElementById("captionModfier");
    let imageModifier = document.getElementById("imageModifer");
    let nextButton = document.getElementById("nextImageUload"); // Assurez-vous d'avoir un bouton avec cet id dans votre HTML
    let prevButton = document.getElementById("previousImageUpload"); // Assurez-vous d'avoir un bouton avec cet id dans votre HTML

    messageTextarea.value = caption;
    initialCaption = caption;


    if (images !== "") {
        let imageArray = images.split(','); // Divisez la chaîne d'images en un tableau
        imageModifier.src = "images/blog/" + imageArray[currentImageIndexUpload]; // Utilisez l'index pour accéder à l'image

        // Vérifiez si le post a une seule image ou aucune image
        if (imageArray.length <= 1) {
            // Cachez les boutons si le post a une seule image ou aucune image
            nextButton.style.display = "none";
            prevButton.style.display = "none";
        } else {
            // Sinon, affichez les boutons et ajoutez des écouteurs d'événements pour naviguer entre les images
            nextButton.style.display = "block";
            prevButton.style.display = "block";

            nextButton.onclick = function () {
                currentImageIndexUpload = (currentImageIndexUpload + 1) % imageArray.length; // Utilisez le modulo pour boucler à travers les images
                imageModifier.src = "images/blog/" + imageArray[currentImageIndexUpload];
            }

            prevButton.onclick = function () {
                currentImageIndexUpload = (currentImageIndexUpload - 1 + imageArray.length) % imageArray.length; // Ajoutez la longueur avant le modulo pour éviter les indices négatifs
                imageModifier.src = "images/blog/" + imageArray[currentImageIndexUpload];
            }
        }

        document.getElementById("delImageUpdate").style.display = "block";
    } else {
        imageModifier.src = "aucuneImg.png";
        nextButton.style.display = "none";
        prevButton.style.display = "none";
        document.getElementById("delImageUpdate").style.display = "none";
    }
    modal.style.display = "block";
}

function closeModifierPopup() {
    var modal = document.getElementById("modifierModal");
    modal.style.display = "none";
}

function submitModifierForm(event) {
    event.preventDefault();
    let formData = new FormData();
    let caption = $('#captionModfier').val();
    let files = $('#nipaUpload')[0].files; // Récupérez les fichiers d'image
    for (let i = 0; i < files.length; i++) {
        formData.append('images[]', files[i]); // Ajoutez chaque fichier d'image à formData
    }
    formData.append('caption', caption);

    $.ajax({
        url: '/edit/' + postIdToModify,
        type: "POST",
        data: formData,
        async: true,
        processData: false,
        contentType: false,
        success: function (response) {

            $("div[data-post-id='" + postIdToModify + "']").remove();
            var newPostHTML = createPostHTML(response.post, response.post.url);
            $('#postsContainer').prepend(newPostHTML);
            closeModifierPopup();

            // Supprimez l'ancienne version du post du tableau posts
            posts = posts.filter(post => post.id !== postIdToModify);
            // Ajoutez la nouvelle version du post au début du tableau posts
            posts.unshift(response.post);

            $('html, body').animate({
                scrollTop: 890
            }, 500);
        },
        error: function (response) {
            console.log("error");
        },
    });
}

function windowOnClick(event) {
    var modal = document.getElementById('modifierModal');
    if (event.target === modal) {
        closeModifierPopup();
    }
}

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
            document.getElementById("delImageUpdate").style.display = "block";
        };
        reader.readAsDataURL(imageFile);
    });
}

function afficherIconDelImage() {
    var nipaInput = document.getElementById("nipa");
    document.getElementById("delImage").style.display = "block";
    nipaInput.addEventListener("change", function () {
        var files = this.files;
        selectedImages = [];
        currentImageIndex = 0;
        for (var i = 0; i < files.length; i++) {
            var reader = new FileReader(); // Créer un nouveau FileReader pour chaque fichier
            reader.onload = function (event) {
                selectedImages.push(event.target.result);
                if (selectedImages.length === files.length) {
                    document.getElementById("rbtinput2").src = selectedImages[currentImageIndex];
                    // Afficher les boutons si nécessaire
                    if (selectedImages.length >= 2) {
                        document.getElementById("previousImage").style.display = "block";
                        document.getElementById("nextImage").style.display = "block";
                    } else {
                        document.getElementById("previousImage").style.display = "none";
                        document.getElementById("nextImage").style.display = "none";
                    }
                }
            };
            reader.readAsDataURL(files[i]);
        }
    });
}

function changeImageUpload(direction) {
    // Vérifier si des images ont été sélectionnées
    if (selectedImages.length > 0) {
        // Mettre à jour l'index de l'image actuelle
        currentImageIndexUpload += direction;
        // Utiliser l'opérateur modulo pour créer un effet de boucle
        currentImageIndexUpload = (currentImageIndexUpload + selectedImages.length) % selectedImages.length;
        // Mettre à jour la source de l'image
        document.getElementById("rbtinput2").src = selectedImages[currentImageIndexUpload];
    }
}

document.getElementById("delImage").addEventListener("click", function () {
    var nipaInput = document.getElementById("nipa");
    nipaInput.value = "";
    var rbtinput2 = document.getElementById("rbtinput2");
    rbtinput2.src = "aucuneImg.png";
    this.style.display = "none";
    document.getElementById("previousImage").style.display = "none";
    document.getElementById("nextImage").style.display = "none";
    $('#ajoutPost').prop('disabled', true);
});

document.getElementById("delImageUpdate").addEventListener("click", function () {
    Swal.fire({
        title: 'Êtes-vous sûr?',
        text: "Vous ne pourrez pas revenir en arrière!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Oui, supprimez-le!'
    }).then((result) => {
        if (result.isConfirmed) {
            var nipaInput = document.getElementById("nipaUpload");
            nipaInput.value = "";
            var rbtinput2 = document.getElementById("imageModifer");
            rbtinput2.src = "aucuneImg.png";
            this.style.display = "none";

            // Envoyer une requête AJAX pour supprimer l'image du post
            $.ajax({
                url: '/edit/' + postIdToModify + '/remove-image', // Remplacez par la route appropriée
                type: 'POST',
                success: function (response) {
                    Swal.fire(
                        'Supprimé!',
                        'Votre image a été supprimée.',
                        'success'
                    )
                },
                error: function (xhr, status, error) {
                    // Gérer les erreurs
                    console.error(error);
                }
            });
        }
    })
});