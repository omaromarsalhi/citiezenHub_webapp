var running = false;

// Event listener for keyup to handle Enter key submissions
document.getElementById("messagebot").addEventListener("keyup", function (event) {
    if (event.keyCode === 13) {  // Enter key code
        event.preventDefault();
        send();
    }
});

// Toggle the chatbot view on clicking the toggle button
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

// Function to send message
function send() {
    if (running) return;
    var msg = document.getElementById("messagebot").value;
    if (msg === "") return;
    running = true;
    addMsg(msg);
    sendTextForProcessing(msg);
}

// Function to add user's message to the chat area
function addMsg(msg) {
    var div = document.createElement("div");
    div.innerHTML = "<span style='flex-grow:1'></span><div class='chat-message-sent'>" + msg + "</div>";
    div.className = "chat-message-div";
    document.getElementById("message-box").appendChild(div);
    document.getElementById("messagebot").value = "";
    document.getElementById("message-box").scrollTop = document.getElementById("message-box").scrollHeight;
}

// Function to add response message to the chat area
function addResponseMsg(msg) {
    var div = document.createElement("div");
    div.innerHTML = "<div class='chat-message-received'>" + msg + "</div>";
    div.className = "chat-message-div";
    document.getElementById("message-box").appendChild(div);
    document.getElementById("message-box").scrollTop = document.getElementById("message-box").scrollHeight;
    running = false;
}

// Function to send text for processing and get a response
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

// Function to start voice recording and handle speech recognition
function startRecording1() {
    const recognition = new window.webkitSpeechRecognition(); // Initialize speech recognition
    recognition.lang = 'en-US'; // Set recognition language

    recognition.onresult = function(event) {
        const transcript = event.results[0][0].transcript; // Get the transcription
        document.getElementById('messagebot').value = transcript; // Fill the chat input field with the transcription
        send(); // Send the message immediately after setting the text
    }

    recognition.start(); // Start recording
}

// Add event listener to start recording button
document.getElementById('startRecording1').addEventListener('click', startRecording1);
