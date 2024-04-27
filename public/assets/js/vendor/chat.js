var running = false;

document.getElementById("message").addEventListener("keyup", function (event) {
    if (event.keyCode === 13) {
        event.preventDefault();
        send();
    }
});

document.getElementById("chatbot_toggle").onclick = function () {
    var chatbot = document.getElementById("chatbot");
    if (chatbot.classList.contains("collapsed")) {
        chatbot.classList.remove("collapsed");
        document.getElementById("chatbot_toggle").children[0].style.display = "none";
        document.getElementById("chatbot_toggle").children[1].style.display = "";
    } else {
        chatbot.classList.add("collapsed");
        document.getElementById("chatbot_toggle").children[0].style.display = "";
        document.getElementById("chatbot_toggle").children[1].style.display = "none";
    }
};

function send() {
    if (running) return;
    var msg = document.getElementById("message").value;
    if (msg === "") return;
    running = true;
    addMsg(msg);
    sendTextForProcessing(msg);
}

function addMsg(msg) {
    var div = document.createElement("div");
    div.innerHTML = "<span style='flex-grow:1'></span><div class='chat-message-sent'>" + msg + "</div>";
    div.className = "chat-message-div";
    document.getElementById("message-box").appendChild(div);
    document.getElementById("message").value = "";
    document.getElementById("message-box").scrollTop = document.getElementById("message-box").scrollHeight;
}

function addResponseMsg(msg) {
    var div = document.createElement("div");
    div.innerHTML = "<div class='chat-message-received'>" + msg + "</div>";
    div.className = "chat-message-div";
    document.getElementById("message-box").appendChild(div);
    document.getElementById("message-box").scrollTop = document.getElementById("message-box").scrollHeight;
    running = false;
}

function sendTextForProcessing(msg) {
    fetch('/process-text', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ text: msg })
    })
    .then(response => response.json())
    .then(data => {
        addResponseMsg(data.text);
        if (data.audioUrl) {
            const audio = new Audio(data.audioUrl);
            audio.play();
        }
    })
    .catch(error => console.error('Error processing text:', error));
}
