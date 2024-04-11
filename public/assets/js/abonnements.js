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
            console.error("error");
        },
    });
}