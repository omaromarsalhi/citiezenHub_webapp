
$(document).ready(function () {
    setTimeout(function (){
        loadChatInfo()
    },500)
});



const socket = new WebSocket("ws://localhost:8090?userId=" + currentUser);

socket.addEventListener("open", function () {
    console.log("CONNECTED");
});

socket.addEventListener('message', (event) => {
    const messageData = JSON.parse(event.data);
    const {senderId, message, recipientId} = messageData;

    let reciverId = $('#currentUserInChat').attr('data-value');

    if (reciverId == senderId && recipientId == currentUser) {
        $('#chatContainer').append('<div class="row no-gutters ">\n' +
            '                        <div class="dynamic-resizing">\n' +
            '                            <div class="chat-bubble chat-bubble--left">\n' +
            '                                ' + message + ' ' +
            '                            </div>\n' +
            '                        </div>\n' +
            '                    </div>')
        $("#chatContainer").scrollTop($("#chatContainer")[0].scrollHeight);
    } else {
        let out = false
        let numberOfJumps = 0
        let count = 0

        const today = new Date();
        const hours = today.getHours();
        const minutes = today.getMinutes();

        const currentTime = `${hours}:${minutes}`;
        console.log(currentTime)
        while (!out) {
            let user = document.getElementById("list_user_" + count)
            if (user) {
                let userId=$('#list_user_' + count).attr('data-value').split(':')[1]
                if(userId==senderId) {
                    $('#userLastMessage_' + count).html(message)
                    $('#userLastMessage_' + count).addClass("show")
                    $('#userLastMessageTime_' + count).html(currentTime)
                    $('#userLastMessageTime_' + count).addClass("show")
                    out = true
                }
            } else if (numberOfJumps > 1)
                out = true
            else
                numberOfJumps++
            count++
        }
    }

});


function sendMsg() {
    let msg = $('#msgToSend').val();
    $('#chatContainer').append('<div class="row no-gutters ">\n' +
        '                        <div class="dynamic-resizing-reverse right">\n' +
        '                            <div class="chat-bubble chat-bubble--right">\n' +
        '                                ' + msg + ' ' +
        '                            </div>\n' +
        '                        </div>\n' +
        '                    </div>')
    $("#chatContainer").scrollTop($("#chatContainer")[0].scrollHeight);
    $('#msgToSend').val('');
    let reciverId = $('#currentUserInChat').attr('data-value');


    msg = {
        'message': msg,
        'senderId': currentUser,
        'recipientId': reciverId
    }

    socket.send(JSON.stringify(msg));
    $.ajax({
        url: "/chat/new",
        type: "POST",
        data: {
            reciverId: reciverId,
            msg: msg,
        },
        async: true,
        success: function (response) {

        },
    });


}

function loadChatInfo() {
    let index=document.getElementById('#list_user_0')
    let idReciver=0
    if(index){
        console.log('here')
         idReciver = $('#list_user_0').data('value').split(':')[1]
    }else{
        console.log('here2')
         idReciver = $('#list_user_0').data('value').split(':')[1]
    }

    $.ajax({
        url: "/chat/getData",
        type: "POST",
        data: {
            idReciver: parseInt(idReciver),
            idSender: currentUser
        },
        async: true,
        success: function (response) {
            $('#chatContainer').html('')
            for (let i = 0; i < response.messages.length; i++) {
                if (response.messages[i][2]) {
                    $('#chatContainer').append('<div class="row no-gutters ">\n' +
                        '                        <div class="dynamic-resizing-reverse right">\n' +
                        '                            <div class="chat-bubble chat-bubble--right">\n' +
                        '                                ' + response.messages[i][0] + ' ' +
                        '                            </div>\n' +
                        '                        </div>\n' +
                        '                    </div>')
                    $("#chatContainer").scrollTop($("#chatContainer")[0].scrollHeight);
                } else {
                    $('#chatContainer').append('<div class="row no-gutters ">\n' +
                        '                        <div class="dynamic-resizing">\n' +
                        '                            <div class="chat-bubble chat-bubble--left">\n' +
                        '                                ' + response.messages[i][0] + ' ' +
                        '                            </div>\n' +
                        '                        </div>\n' +
                        '                    </div>')
                    $("#chatContainer").scrollTop($("#chatContainer")[0].scrollHeight);
                }
            }

        },
    });
}



$('.friend-drawer--onhover').on('click', function () {
    $('.chat-bubble').hide('slow').show('slow');

    let index = $(this).data('value').split(':')[0]
    let idReciver = $(this).data('value').split(':')[1]
    let imageUser = $('#userImg_' + index).attr("src");
    let userName = $('#userFullName_' + index).html();

    $('#currentUserInChatImg').attr('src', imageUser)
    $('#currentUserInChatName').html(userName)
    $('#currentUserInChat').attr('data-value', $(this).data('value').split(':')[1]);
    $('#userLastMessageTime_'+index).removeClass("show")
    $('#userLastMessage_'+index).removeClass("show")

    $.ajax({
        url: "/chat/getData",
        type: "POST",
        data: {
            idReciver: parseInt(idReciver),
            idSender: currentUser
        },
        async: true,
        success: function (response) {
            $('#chatContainer').html('')
            for (let i = 0; i < response.messages.length; i++) {
                if (response.messages[i][2]) {
                    $('#chatContainer').append('<div class="row no-gutters ">\n' +
                        '                        <div class="dynamic-resizing-reverse right">\n' +
                        '                            <div class="chat-bubble chat-bubble--right">\n' +
                        '                                ' + response.messages[i][0] + ' ' +
                        '                            </div>\n' +
                        '                        </div>\n' +
                        '                    </div>')
                    $("#chatContainer").scrollTop($("#chatContainer")[0].scrollHeight);
                } else {
                    $('#chatContainer').append('<div class="row no-gutters ">\n' +
                        '                        <div class="dynamic-resizing">\n' +
                        '                            <div class="chat-bubble chat-bubble--left">\n' +
                        '                                ' + response.messages[i][0] + ' ' +
                        '                            </div>\n' +
                        '                        </div>\n' +
                        '                    </div>')
                    $("#chatContainer").scrollTop($("#chatContainer")[0].scrollHeight);
                }
            }
        },
    });

});