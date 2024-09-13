<?php
session_start();
$user_name = $_SESSION['user_name'];
$user_type= $_SESSION['user_type'];
 if ((!isset($_SESSION['user_name'])) && (!isset($_SESSION['user_type']))) {
  header('Location: index.php');
 exit;
 }
include("connections.php");



// Get username from database
$sql = "SELECT user_name FROM userss WHERE user_name = '$user_name'";
$result = mysqli_query($con, $sql);

if ($result && mysqli_num_rows($result) > 0) {
  $user_data = mysqli_fetch_assoc($result);
  $username = $user_data['user_name'];
} else {
  // Handle case where username retrieval fails (e.g., display error message)
  echo "Error: Unable to retrieve username";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="page.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/annyang/2.6.1/annyang.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.5.3/jspdf.min.js"></script>
</head>
<body>
    
<div id="nav-bar">
  <input id="nav-toggle" type="checkbox"/>
  <div id="nav-header"><a id="nav-title" href="#" target="_blank">AI Educator</a>
    <label for="nav-toggle"><span id="nav-toggle-burger"></span></label>
    <hr/>
  </div>
  <div id="nav-content">
    <div class="nav-button"><i class="fas fa-palette"></i><span><a href="page.php">Educator</a></span></div>
    <div id="nav-content-highlight"></div>
  </div>
  <div id="nav-content">
    <div class="nav-button"><i class="fas fa-palette"></i><span><a href="multimodalinput2.php">Image Educator</a></span></div>
    <div id="nav-content-highlight"></div>
  </div>
  <input id="nav-footer-toggle" type="checkbox"/>
  <div id="nav-footer">
    <div id="nav-footer-heading">
      <div id="nav-footer-avatar"><img src="https://gravatar.com/avatar/4474ca42d303761c2901fa819c4f2547"/></div>
      <div id="nav-footer-titlebox"><a id="nav-footer-title" href="#" target="_blank"></a><span id="nav-footer-subtitle">Admin</span></div>
      <label for="nav-footer-toggle"><i class="fas fa-caret-up"></i></label>
    </div>
    <div id="nav-footer-content">
    </div>
  </div>
</div>
<div class="seperate"  >
  <div  class="card">
    <p id="transcript"></p>
  </div>
  <div class="card1">
    <p id="gemini-response"></p>
  </div>
  <button class="savebookmarkBtn" onclick="generatePdf()">
  <span class="saveIconContainer">
    <svg viewBox="0 0 384 512" height="0.9em" class="saveicon">
      <path
        d="M0 48V487.7C0 501.1 10.9 512 24.3 512c5 0 9.9-1.5 14-4.4L192 400 345.7 507.6c4.1 2.9 9 4.4 14 4.4c13.4 0 24.3-10.9 24.3-24.3V48c0-26.5-21.5-48-48-48H48C21.5 0 0 21.5 0 48z"
      ></path>
    </svg>
  </span>
  <p class="savetext">Save</p>
</button>


<button class="button777" id="speak-button">
  <div class="button77-overlay"></div>
  <span id="spanid">Speak</span>
  
</button>


    <button class="button1" id="start-record">
      <svg xmlns="http://www.w3.org/2000/svg" width="24" viewBox="0 0 24 24" height="24" fill="none" class="svg-icon"><g stroke-width="2" stroke-linecap="round" stroke="#ff342b"><rect y="3" x="9" width="6" rx="3" height="11"></rect><path d="m12 18v3"></path><path d="m8 21h8"></path><path d="m19 11c0 3.866-3.134 7-7 7-3.86599 0-7-3.134-7-7"></path></g></svg>
      <span class="lable">Record</span>
    </button>

    <button class="button2" id="stop-record">
      <svg xmlns="http://www.w3.org/2000/svg" width="24" viewBox="0 0 24 24" height="24" fill="none" class="svg-icon"><g stroke-width="2" stroke-linecap="round" stroke="#ff342b"><rect y="3" x="9" width="6" rx="3" height="11"></rect><path d="m12 18v3"></path><path d="m8 21h8"></path><path d="m19 11c0 3.866-3.134 7-7 7-3.86599 0-7-3.134-7-7"></path></g></svg>
      <span class="lable">Stop</span>
    </button>
    <button class="button99" type=button
    id="send-to-gemini" onclick="sendPrompt()">
      <div class="svg-wrapper-1">
        <div class="svg-wrapper">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            width="24"
            height="24"
          >
            <path fill="none" d="M0 0h24v24H0z"></path>
            <path
              fill="currentColor"
              d="M1.946 9.315c-.522-.174-.527-.455.01-.634l19.087-6.362c.529-.176.832.12.684.638l-5.454 19.086c-.15.529-.455.547-.679.045L12 14l6-8-8 6-8.054-2.685z"
            ></path>
          </svg>
        </div>
      </div>
      <span>Send</span>
    </button>
    
</div>
<script>
    const startButton = document.getElementById("start-record");
    const stopButton = document.getElementById("stop-record");
    const transcriptDiv = document.getElementById("transcript");
    const geminiResponseDiv = document.getElementById("gemini-response");
    const sendToGeminiButton = document.getElementById("send-to-gemini");

    let recognition;
    let recordedText = "";

    startButton.addEventListener("click", function() {
      recognition = new webkitSpeechRecognition();
      recognition.interimResults = true;

      recognition.onstart = function() {
        startButton.disabled = true;
        stopButton.disabled = false;
        transcriptDiv.textContent = "";
      };

      recognition.onerror = function(event) {
        console.error(event.error);
      };

      recognition.onresult = function(event) {
        let transcript = "";
        for (let i = event.resultIndex; i < event.results.length; ++i) {
          if (event.results[i].isFinal) {
            transcript += event.results[i][0].transcript;
          }
        }
        transcriptDiv.textContent = transcript;
        recordedText = transcript; // Store the final transcript
        sendToGeminiButton.disabled = false; // Enable send button
      };

      recognition.start();
    });

    stopButton.addEventListener("click", function() {
      recognition.stop();
      startButton.disabled = false;
      stopButton.disabled = true;
    });
  function sendPrompt() {
  const formData = new FormData();
  formData.append('text', recordedText);
  
  fetch('response.php', {
    method: 'POST',
    body: formData
  })
  .then(response => response.text())
  .then(data => {
    document.getElementById("gemini-response").innerHTML = data;
  })
  .catch(error => {
    document.getElementById("gemini-response").innerHTML = "Error: " + error;
  });
}
    const textInput = document.getElementById('gemini-rsponse');
    //const speakButton = document.getElementById('speak-button');
    const voiceSelect = document.getElementById('voice-select');
    const speechSynthesis = window.speechSynthesis;

    function populateVoices() {
      const voices = speechSynthesis.getVoices();
      voices.forEach(voice => {
        const option = document.createElement('option');
        option.textContent = `${voice.name} (${voice.lang})`;
        option.value = voice.name;
        voiceSelect.appendChild(option);
      });
    }

    speakButton.addEventListener('click', () => {
      const text = textInput.value;
      if (text.trim() === '') {
        return;
      }

      const utterance = new SpeechSynthesisUtterance(text);
      const selectedVoice = voiceSelect.value;

      if (selectedVoice) {
        for (const voice of speechSynthesis.getVoices()) {
          if (voice.name === selectedVoice) {
            utterance.voice = voice;
            break;
          }
        }
      }

      speechSynthesis.speak(utterance);
    });

    populateVoices();
  </script>

    <script>
      const textContainer = document.getElementById('gemini-response');
const speakButton = document.getElementById('speak-button');
let utterance;

speakButton.addEventListener('click', () => {
  if (!utterance) {
    // If utterance is not defined, create a new one
    utterance = new SpeechSynthesisUtterance(textContainer.textContent);
    utterance.onend = () => {
      // Reset utterance after it finishes speaking
      utterance = null;
      
    };
    speechSynthesis.speak(utterance);
    
  } else {
    // If utterance is defined, stop speaking and reset
    speechSynthesis.cancel();
    utterance = null;
    
  }
});
document.getElementById("speak-button").addEventListener("click", function() {
  var button = document.getElementById("speak-button");
  var text = document.getElementById("spanid");
  if (text.textContent === "Speak") {
    text.textContent = "Mute";
  } else {
    text.textContent = "Speak";
  }
});
</script>
<script>     
 function generatePdf() {
  const pdf = new jsPDF();
  const text1 = document.getElementById('transcript').textContent;
  const text2 = document.getElementById('gemini-response').textContent;
  const lines = [text1, text2].join('\n');

  pdf.setFontSize(12);
  pdf.text(10, 10, lines);

  pdf.output('dataurlnewwindow');
}
</script>
</body>
</html>