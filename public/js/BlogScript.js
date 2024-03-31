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

var currentPage = 1;
var isLoading = false;
var totalPostsCount = 0;

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

function loadPostsPage(page) {
    if (isLoading || page * 5 > totalPostsCount) {
        return;
    }
    isLoading = true;
    $.ajax({
        url: '/blog/page/' + page,
        type: 'GET',
        success: function(response) {
            response.posts.forEach(function(post) {
                var newPostHTML = createPostHTML(post);
                $('#postsContainer').append(newPostHTML);
            });
            currentPage++;
            isLoading = false;
        },
        error: function(xhr, status, error) {
            console.error(error);
            isLoading = false;
        }
    });
}

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
        submitButton.disabled = !(caption || image);
    }

    textarea.addEventListener('input', verifierEtatBoutonModifier);
    fileInput.addEventListener('change', verifierEtatBoutonModifier);

    verifierEtatBoutonModifier
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
                $("div[data-post-id='" + postIdToModify + "']").remove();
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

    $.ajax({
        url: '/edit/' + postIdToModify,
        type: "POST",
        data: formData,

        async: true,
        processData: false,
        contentType: false,
        success: function (response) {
            $("div[data-post-id='" + postIdToModify + "']").remove();
            var newPostHTML = createPostHTML(response.post);
            $('#postsContainer').prepend(newPostHTML);
            closeModifierPopup();
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
        };
        reader.readAsDataURL(imageFile);
    });
}