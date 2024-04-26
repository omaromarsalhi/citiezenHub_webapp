var running = false;
function send() {
  if (running == true) return;
  var msg = document.getElementById("message").value;
  if (msg == "") return;
  running = true;
  addMsg(msg);
  //DELEAY MESSAGE RESPOSE Echo
  window.setTimeout(addResponseMsg, 1000, msg);
}
function addMsg(msg) {
  var div = document.createElement("div");
  div.innerHTML =
    "<span style='flex-grow:1'></span><div class='chat-message-sent'>" +
    msg +
    "</div>";
  div.className = "chat-message-div";
  document.getElementById("message-box").appendChild(div);
  //SEND MESSAGE TO API
  document.getElementById("message").value = "";
  document.getElementById("message-box").scrollTop = document.getElementById(
    "message-box"
  ).scrollHeight;
}
function addResponseMsg(msg) {
  var div = document.createElement("div");
  div.innerHTML = "<div class='chat-message-received'>" + msg + "</div>";
  div.className = "chat-message-div";
  document.getElementById("message-box").appendChild(div);
  document.getElementById("message-box").scrollTop = document.getElementById(
    "message-box"
  ).scrollHeight;
  running = false;
}
document.getElementById("message").addEventListener("keyup", function (event) {
  if (event.keyCode === 13) {
    event.preventDefault();
    send();
  }
});
 document.getElementById("chatbot_toggle").onclick = function () {
    if (document.getElementById("chatbot").classList.contains("collapsed")) {
      document.getElementById("chatbot").classList.remove("collapsed")
      document.getElementById("chatbot_toggle").children[0].style.display = "none"
      document.getElementById("chatbot_toggle").children[1].style.display = ""
      setTimeout(addResponseMsg,1000,"Hi")
    }
    else {
      document.getElementById("chatbot").classList.add("collapsed")
      document.getElementById("chatbot_toggle").children[0].style.display = ""
      document.getElementById("chatbot_toggle").children[1].style.display = "none"
    }
  }
  let mediaRecorder;
  let audioChunks = [];
  let isRecording = false;
  
  function toggleRecording() {
      if (!isRecording) {
          startRecording();
          document.getElementById('start-voice-input').style.display = 'none';
          document.getElementById('stop-voice-input').style.display = 'inline-block';
      } else {
          stopRecording();
          document.getElementById('start-voice-input').style.display = 'inline-block';
          document.getElementById('stop-voice-input').style.display = 'none';
      }
      isRecording = !isRecording;
  }
  
  function startRecording() {
      navigator.mediaDevices.getUserMedia({ audio: true })
          .then(stream => {
              mediaRecorder = new MediaRecorder(stream);
              mediaRecorder.addEventListener("dataavailable", event => {
                  audioChunks.push(event.data);
              });
              console.log("salem");
              mediaRecorder.start();
          })
          .catch(error => console.error("Error accessing microphone:", error));
  }
  
  function stopRecording() {
      mediaRecorder.stop();
      mediaRecorder.addEventListener("stop", () => {
          const audioBlob = new Blob(audioChunks, { type: 'audio/wav' });
          const formData = new FormData();
          formData.append("audioFile", audioBlob);
          console.log(audioBlob);
  
          fetch('/save-audio', {
              method: 'POST',
              body: formData,
          }).then(response => {
              console.log("Audio file saved!");
          }).catch(error => console.error("Error saving audio:", error));
  
          // Reset recording state
          isRecording = false;
          audioChunks = [];
          console.log('a7chi');
      });
  }
  