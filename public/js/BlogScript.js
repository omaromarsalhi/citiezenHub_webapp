var currentPage = 1;
var isLoading = false;
var totalPostsCount = 0;
var allPostsLoaded = false;
var postIdToModify = null;
var initialCaption = null;
var currentImageIndices = {};
var posts = [];
var currentImageIndexUpload = 0;
var isSearching = false;
var idUser = 0;


//blog de creation de post
function createPostHTML(post, postUrl, idU) {
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
                <img id="post-image-${post.id}" src="../usersImg/${post.images[0]}"
                     alt="Personal Portfolio Images" style="width: 1300px; height: 500px; object-fit: contain;">
                ${bouttonImg}
            </div>
        `;
    }
    var captiontext = '';
    if (post.caption !== '') {
        captiontext = `
            <h4 class="title"><a href="${postUrl}">${post.caption} <i
                class="feather-arrow-up-right"></i></a></h4>
            <a class="translateBtn" data-post-id="${post.id}" data-original-caption="${post.caption}" onclick="translateText('${post.id}', '${post.caption}', 'fr', 'ar')">Translate</a>
        `;
    }

    var dropDown = '';
    if (idU === idUser) {
        dropDown = `
        <div class="meta">
            <div class="dropdown">
                                    <button class="dropbtn"><i class="fas fa-cog"></i></button>
                                    <div class="dropdown-content">
                                        <button onclick="handleMenuAction(this, ${post.id}, '${post.caption}', '${post.images}', 'modifier')">Modifier</button>
                                        <button onclick="handleMenuAction(this, ${post.id}, '${post.caption}', '${post.images}', 'supprimer')">Supprimer</button>
                                    </div>
                                </div>
                            </div>
        `
    }


    return `
        <div class="col-xl-12 col-lg-8" data-post-id="${post.id}">
            <div class="rn-blog single-column mb--30" data-toggle="modal" data-target="#exampleModalCenters">
                <div class="inner">
                    <div class="content mb-4">
                        <div class="category-info">                                                                                                                                  
                            <div class="category-list">
                                <img src=../usersImg/${post.userImage} height="50"
                                     width="50" style="margin-right: 10px">
                                <a href="blog-details.html">${post.userName} ${post.userSurname}</a>
                            </div>
                            ${dropDown}
                        </div>
                        <span>${post.datePost}</span>
                        ${captiontext}
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
                var newPostHTML = createPostHTML(post, post.url, post.userId);
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

    $.ajax({
        url: '/getUserId',
        type: 'GET',
        success: function (response) {
            idUser = response.userId;
            console.log(idUser);
        },
        error: function (xhr, status, error) {
            console.error(error);
        }
    });
});


window.onscroll = function () {
    var scrollPosition = window.pageYOffset;
    var windowSize = window.innerHeight;
    var bodyHeight = document.body.offsetHeight;

    if (!isSearching && Math.max(bodyHeight - (scrollPosition + windowSize), 0) < 500) {
        loadPostsPage(currentPage);
    }
};

function changeImage(postId, direction) {
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
    document.getElementById(`post-image-${postId}`).src = `../usersImg/${images[currentImageIndices[postId]]}`;
}

function addPost(event) {
    event.preventDefault();

    let imageFile = document.querySelector('#nipa').files[0];
    let formData = new FormData();
    formData.append('image', imageFile);

    if (imageFile != null) {

        $.ajax({
            url: '/checkImage',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (data) {
                console.log(data.microsoft.items[0].label);
                console.log(data.microsoft.items[0].likelihood_score);
                console.log(data.microsoft.items[0].category);

                var isPublic = true;
                var max = 0;
                var categorie = "";
                for (i = 0; i < data.microsoft.items.length; i++) {
                    if (data.microsoft.items[i].likelihood_score > 0.2) {
                        isPublic = false;
                        if (max < data.microsoft.items[i].likelihood_score) {
                            max = data.microsoft.items[i].likelihood_score;
                            categorie = data.microsoft.items[i].category;
                        }
                    }
                }

                if (isPublic) {
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

                                document.getElementById("delImage").style.display = "none";

                                rep = " ";
                                rep = "Votre post a été ajouté avec succès";
                                console.log(rep);
                                $('#sucess-message').text(rep);

                                $('#statusSuccessModal').modal('show');
                            } else {
                                console.error('Failed to create post: ' + response.message);
                            }
                        },
                        error: function (response) {
                            console.error("error");
                        },
                    });
                } else {
                    rep = " ";
                    rep = "Votre post a ete refuse a cause d'un contenu : " + categorie;
                    console.log(rep);
                    $('#error-message').text(rep);
                    $('#statusErrorsModal').modal('show');

                    $('#contact-message').val('');
                    $('#nipa').val('');
                    document.getElementById("previousImage").style.display = "none";
                    document.getElementById("nextImage").style.display = "none";
                    $('#rbtinput2').attr('src', 'aucuneImg.png');
                }
            },
            error: function () {
                console.log('Une erreur est survenue');
            }
        });
    } else {
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

                    document.getElementById("delImage").style.display = "none";

                    rep = " ";
                    rep = "Votre post a été ajouté avec succès";
                    console.log(rep);
                    $('#sucess-message').text(rep);

                    $('#statusSuccessModal').modal('show');
                } else {
                    console.error('Failed to create post: ' + response.message);
                }
            },
            error: function (response) {
                console.error("error");
            },
        });
    }

}

function handleMenuAction(button, postId, caption, image, action) {
    if (action === "modifier") {
        postIdToModify = postId;
        translateText(caption, 'fr', 'ar');
        showModifierPopup(caption, image);
    } else if (action === "supprimer") {
        deletePost(postId);
    }
}

function deletePost(postId) {
    $.ajax({
        url: '/blog/' + postId,
        type: 'DELETE',
        success: function (response) {
            $("div[data-post-id='" + postId + "']").remove();
        },
        error: function (xhr, status, error) {
            console.error(error);
        }
    });
}

function showModifierPopup(caption, images) {
    var modal = document.getElementById("modifierModal");
    let messageTextarea = document.getElementById("captionModfier");
    let imageModifier = document.getElementById("imageModifer");
    let nextButton = document.getElementById("nextImageUload");
    let prevButton = document.getElementById("previousImageUpload");

    messageTextarea.value = caption;
    initialCaption = caption;


    if (images !== "") {
        let imageArray = images.split(',');
        imageModifier.src = "../usersImg/" + imageArray[currentImageIndexUpload];

        if (imageArray.length <= 1) {
            nextButton.style.display = "none";
            prevButton.style.display = "none";
        } else {
            nextButton.style.display = "block";
            prevButton.style.display = "block";

            nextButton.onclick = function () {
                currentImageIndexUpload = (currentImageIndexUpload + 1) % imageArray.length;
                imageModifier.src = "../usersImg/" + imageArray[currentImageIndexUpload];
            }

            prevButton.onclick = function () {
                currentImageIndexUpload = (currentImageIndexUpload - 1 + imageArray.length) % imageArray.length;
                imageModifier.src = "../usersImg/" + imageArray[currentImageIndexUpload];
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
    let files = $('#nipaUpload')[0].files;
    for (let i = 0; i < files.length; i++) {
        formData.append('images[]', files[i]);
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

            posts = posts.filter(post => post.id !== postIdToModify);
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
            var reader = new FileReader();
            reader.onload = function (event) {
                selectedImages.push(event.target.result);
                if (selectedImages.length === files.length) {
                    document.getElementById("rbtinput2").src = selectedImages[currentImageIndex];
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
    if (selectedImages.length > 0) {
        currentImageIndexUpload += direction;
        currentImageIndexUpload = (currentImageIndexUpload + selectedImages.length) % selectedImages.length;
        document.getElementById("rbtinput2").src = selectedImages[currentImageIndexUpload];
    }
}


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
                    console.error(error);
                }
            });
        }
    })
});


let searchInput = document.querySelector('.search-form-wrapper input[type="search"]');
searchInput.addEventListener('input', function () {
    isSearching = searchInput.value !== '';
    if (isSearching) {
        $.ajax({
            url: '/blog/search/' + searchInput.value,
            type: 'GET',
            success: function (response) {
                $('#postsContainer').empty();

                response.posts.forEach(function (post) {
                    var newPostHTML = createPostHTML(post, post.url);
                    $('#postsContainer').append(newPostHTML);
                });
            },
            error: function (xhr, status, error) {
                console.error(error);
            }
        });
    } else {
        $('#postsContainer').empty();
        currentPage = 1;
        allPostsLoaded = false;
        loadPostsPage(currentPage);
    }
});

function translateText(postId, textToTranslate, sourceLanguage, targetLanguage) {
    const apiKey = "db017c40fad98dc5b9fc";
    const url = `https://api.mymemory.translated.net/get?q=${encodeURIComponent(textToTranslate)}&langpair=${sourceLanguage}|${targetLanguage}&key=${apiKey}`;

    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.responseData && data.responseData.translatedText) {
                let postElement = document.querySelector(`div[data-post-id="${postId}"] .title a`);
                let translateButton = document.querySelector(`div[data-post-id="${postId}"] .translateBtn`);

                if (translateButton.textContent === 'Translate') {
                    postElement.textContent = data.responseData.translatedText;
                    translateButton.textContent = 'Original version';
                } else {
                    postElement.textContent = translateButton.dataset.originalCaption;
                    translateButton.textContent = 'Translate';
                }
            } else {
                console.error("Erreur : champ 'translatedText' manquant dans responseData");
            }
        })
        .catch(error => console.error("Erreur lors de l'analyse de la réponse JSON : ", error));
}