var currentPage = 1;
var isLoading = false;
var totalPostsCount = 0;
var allPostsLoaded = false;
var postIdToModify = null;
var initialCaption = null;

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
                                        <button onclick="handleMenuAction(this, ${post.id}, '${post.caption}', '${post.image}', 'modifier')">Modifier</button>
                                        <button onclick="handleMenuAction(this, ${post.id}, '${post.caption}', '${post.image}', 'supprimer')">Supprimer</button>
                                    </div>
                                </div>
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

function loadPostsPage(page) {
    if (isLoading || allPostsLoaded) {
        return;
    }
    isLoading = true;
    document.getElementById('loadingIcon').style.display = 'block';
    $.ajax({
        url: '/blog/page/' + page,
        type: 'GET',
        success: function(response) {
            response.posts.forEach(function(post) {
                var newPostHTML = createPostHTML(post);
                $('#postsContainer').append(newPostHTML);
            });
            if (response.posts.length < 5) {
                allPostsLoaded = true;
            } else {
                currentPage++;
            }
            isLoading = false;
            document.getElementById('loadingIcon').style.display = 'none';
        },
        error: function(xhr, status, error) {
            console.error(error);
            isLoading = false;
            document.getElementById('loadingIcon').style.display = 'none';
        }
    });
}

function getTotalPostsCount(callback) {
    $.ajax({
        url: '/blog/count',
        type: 'GET',
        success: function(response) {
            callback(response.count);
        },
        error: function(xhr, status, error) {
            console.error(error);
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    getTotalPostsCount(function(count) {
        totalPostsCount = count;
        loadPostsPage(currentPage);
    });
});

window.onscroll = function() {
    var scrollPosition = window.pageYOffset;
    var windowSize     = window.innerHeight;
    var bodyHeight     = document.body.offsetHeight;

    // Charger plus de posts 500px avant d'arriver au bas de la page
    if (Math.max(bodyHeight - (scrollPosition + windowSize), 0) < 500) {
        loadPostsPage(currentPage);
    }
};

document.addEventListener('DOMContentLoaded', function() {
    loadPostsPage(currentPage);
});

window.onscroll = function() {
    var scrollPosition = window.pageYOffset;
    var windowSize     = window.innerHeight;
    var bodyHeight     = document.body.offsetHeight;

    // Charger plus de posts 500px avant d'arriver au bas de la page
    if (Math.max(bodyHeight - (scrollPosition + windowSize), 0) < 500) {
        loadPostsPage(currentPage);
    }
};
function addPost(event) {
    event.preventDefault();
    let formData = new FormData();
    let caption = $('#contact-message').val();
    formData.append('image', $('#nipa').prop('files')[0]);
    formData.append('caption', caption);
    $.ajax({
        url: '/newPost',
        type: "POST",
        data: formData,
        async: true,
        processData: false,
        contentType: false,
        success: function (response) {
            if (response.success) {
                var newPostHTML = createPostHTML(response.post);
                $('#postsContainer').prepend(newPostHTML);
                $('html, body').animate({
                    scrollTop: 950
                }, 300);
                $('#contact-message').val('');
                $('#nipa').val('');
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
            console.error(response);
        },
    });
}

document.addEventListener('DOMContentLoaded', function() {
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

document.addEventListener('DOMContentLoaded', function() {
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
                    console.log(postId);
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

function showModifierPopup(caption, image) {
    var modal = document.getElementById("modifierModal");
    let messageTextarea = document.getElementById("captionModfier");
    let imageModifier = document.getElementById("imageModifer");
    messageTextarea.value = caption;
    initialCaption = caption;
    if (image !== "null") {
        imageModifier.src = "images/blog/" + image;
        document.getElementById("delImageUpdate").style.display = "block";
    } else {
        imageModifier.src = "aucuneImg.png";
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
    formData.append('caption', caption);
    formData.append('image', $('#nipaUpload').prop('files')[0]);
    console.log($('#nipaUpload').prop('files')[0]);


    $.ajax({
        url: '/edit/' + postIdToModify,
        type: "POST",
        data: formData,

        async: true,
        processData: false,
        contentType: false,
        success: function (response) {
            console.log(response);
            $("div[data-post-id='" + postIdToModify + "']").remove();
            var newPostHTML = createPostHTML(response.post);
            $('#postsContainer').prepend(newPostHTML);
            closeModifierPopup();

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
    document.getElementById("nipa").addEventListener("change", function () {
        var delImageLabel = document.getElementById("delImage");
        if (this.files.length > 0) {
            delImageLabel.style.display = "block";
        } else {
            delImageLabel.style.display = "none";
        }
    });
}

document.getElementById("delImage").addEventListener("click", function () {
    var nipaInput = document.getElementById("nipa");
    nipaInput.value = "";
    var rbtinput2 = document.getElementById("rbtinput2");
    rbtinput2.src = "aucuneImg.png";
    this.style.display = "none";
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
                success: function(response) {
                    // Gérer la réponse du serveur
                    console.log(response);
                    Swal.fire(
                        'Supprimé!',
                        'Votre image a été supprimée.',
                        'success'
                    )
                },
                error: function(xhr, status, error) {
                    // Gérer les erreurs
                    console.error(error);
                }
            });
        }
    })
});