<template>
    <div class="chat-footer">
        <div class="message-form" v-if="!voiceRecording">
            <input type="text" v-model="message" class="message-input" placeholder="Envoyer" @keyup.enter="sendMessage">
            <span class="send-icon" @click="sendMessage">
                <svg width="17" height="19" viewBox="0 0 17 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M16.8418 0.175649C16.9167 0.259535 16.9679 0.366181 16.9891 0.482337C17.0103 0.598493 17.0004 0.719042 16.9608 0.829008L10.6622 18.4298C10.6067 18.5848 10.514 18.7197 10.3943 18.8196C10.2747 18.9195 10.1326 18.9806 9.98393 18.9961C9.83521 19.0116 9.68554 18.9809 9.5514 18.9075C9.41726 18.834 9.30385 18.7207 9.22365 18.5798L5.78369 12.5362L0.37696 8.6911C0.25066 8.60154 0.148948 8.47473 0.083048 8.32464C0.0171479 8.17456 -0.0103786 8.00705 0.00350588 7.84058C0.0173903 7.67412 0.072146 7.51518 0.161731 7.3813C0.251316 7.24742 0.372247 7.14381 0.511181 7.0819L16.2573 0.0437668C16.3556 -0.000501556 16.4635 -0.0114873 16.5674 0.0121742C16.6713 0.0358357 16.7667 0.0931017 16.8418 0.176858V0.175649ZM6.86395 12.1829L9.85254 17.4316L14.9757 3.11576L6.86395 12.1829ZM14.2104 2.26035L1.4031 7.98692L6.09976 11.3263L14.2115 2.26035H14.2104Z" fill="#C5BDBD"/>
                </svg>
            </span>
        </div>
        <div class="message-form" v-else>
            <div class="recording">
                <div class="circle_ripple"></div>
                <div class="circle_ripple-2"></div>
            </div>
            <span class="send-icon" @click="sendMessage">
                <svg width="17" height="19" viewBox="0 0 17 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M16.8418 0.175649C16.9167 0.259535 16.9679 0.366181 16.9891 0.482337C17.0103 0.598493 17.0004 0.719042 16.9608 0.829008L10.6622 18.4298C10.6067 18.5848 10.514 18.7197 10.3943 18.8196C10.2747 18.9195 10.1326 18.9806 9.98393 18.9961C9.83521 19.0116 9.68554 18.9809 9.5514 18.9075C9.41726 18.834 9.30385 18.7207 9.22365 18.5798L5.78369 12.5362L0.37696 8.6911C0.25066 8.60154 0.148948 8.47473 0.083048 8.32464C0.0171479 8.17456 -0.0103786 8.00705 0.00350588 7.84058C0.0173903 7.67412 0.072146 7.51518 0.161731 7.3813C0.251316 7.24742 0.372247 7.14381 0.511181 7.0819L16.2573 0.0437668C16.3556 -0.000501556 16.4635 -0.0114873 16.5674 0.0121742C16.6713 0.0358357 16.7667 0.0931017 16.8418 0.176858V0.175649ZM6.86395 12.1829L9.85254 17.4316L14.9757 3.11576L6.86395 12.1829ZM14.2104 2.26035L1.4031 7.98692L6.09976 11.3263L14.2115 2.26035H14.2104Z" fill="#C5BDBD"/>
                </svg>
            </span>
        </div>
        <div class="ms-auto record-icon rounded-circle" @click="audioRecord">
            <svg width="17" height="23" viewBox="0 0 17 23" fill="none" xmlns="http://www.w3.org/2000/svg" v-if="!voiceRecording">
                <path d="M8.49072 14.1904C6.47721 14.1904 4.86397 12.6058 4.86397 10.6428L4.85184 3.54759C4.85184 1.58459 6.47721 0 8.49072 0C10.5042 0 12.1296 1.58459 12.1296 3.54759V10.6428C12.1296 12.6058 10.5042 14.1904 8.49072 14.1904ZM2.06203 10.6428C2.06203 14.1904 5.14295 16.6737 8.49072 16.6737C11.8385 16.6737 14.9194 14.1904 14.9194 10.6428H16.9814C16.9814 14.687 13.6822 18.0099 9.70368 18.5894V22.4681H7.27776V18.5894C3.29925 18.0218 0 14.687 0 10.6428H2.06203Z" fill="white"/>
            </svg>
            <svg v-else width="24" height="24" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 122.87 122.87">
                <path d="M18,18A61.45,61.45,0,1,1,0,61.44,61.28,61.28,0,0,1,18,18ZM77.38,39l6.53,6.54a4,4,0,0,1,0,5.63L73.6,61.44,83.91,71.75a4,4,0,0,1,0,5.63l-6.53,6.53a4,4,0,0,1-5.63,0L61.44,73.6,51.13,83.91a4,4,0,0,1-5.63,0L39,77.38a4,4,0,0,1,0-5.63L49.28,61.44,39,51.13a4,4,0,0,1,0-5.63L45.5,39a4,4,0,0,1,5.63,0L61.44,49.28,71.75,39a4,4,0,0,1,5.63,0ZM61.44,10.54a50.91,50.91,0,1,0,36,14.91,50.83,50.83,0,0,0-36-14.91Z" fill="white"/>
            </svg>
        </div>
    </div>
</template>
<script>
    import { ref } from 'vue';
    export default{
        emits:['sendMessage', 'sendVoiceMessage'],
        setup(props, { emit }){
            const message = ref('');
            const voiceRecording = ref(false);
            const voiceRecordingCancel = ref(false);
            const mediaRecorder = ref(null);
            const audioURL = ref(null);
            const sendMessage = ()=>{
                if(voiceRecording.value == true){
                    if(mediaRecorder.value.state == 'recording'){
                        voiceRecording.value = false;
                        mediaRecorder.value.stop();
                    }
                }else{
                    if(message.value ==''){
                        return;
                    }else{
                        emit('sendMessage', message.value);
                        message.value = '';
                    }
                }
                document.getElementById('chat-body').scrollTop =  (document.getElementById('chat-body').scrollHeight);
            }
            const audioRecord = ()=>{
                if(voiceRecording.value == false){
                    if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                        navigator.mediaDevices.getUserMedia({ audio: true })
                        // Success callback
                        .then((stream) => {
                            mediaRecorder.value = new MediaRecorder(stream);
                            // start recording
                            mediaRecorder.value.start();
                            let chunks = [];
                            mediaRecorder.value.ondataavailable = (e) => {
                                chunks.push(e.data);
                            };
                            // stop recording
                            mediaRecorder.value.onstop = (e) => {
                                const blob = new Blob(chunks, { type: "audio/ogg; codecs=opus" });
                                chunks = [];
                                audioURL.value = window.URL.createObjectURL(blob);
                                if(!voiceRecordingCancel.value){
                                    emit('sendVoiceMessage', audioURL.value);
                                    voiceRecordingCancel.value = false;
                                }
                                audioURL.value = null;
                                mediaRecorder.value = null;
                            };
                        })
                        // Error callback
                        .catch((err) => {
                            console.error(`The following getUserMedia error occurred: ${err}`);
                        });
                    } else {
                        console.log("getUserMedia not supported on your browser!");
                    }
                    voiceRecording.value = true;
                }else{
                    voiceRecordingCancel.value = true;
                    voiceRecording.value = false;
                    //stop the recording feature
                    mediaRecorder.value.stop();
                    // Reset all the recording properties including the media recorder and stream being captured
                    mediaRecorder.value = null;
                }
            }
            return{
                message,
                audioURL,
                voiceRecording,
                sendMessage,
                audioRecord
            }
        }
    }
</script>
<style>
.chat-footer{
    /* position: absolute;
    bottom: 10px; */
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.message-form{
    display: flex;
    align-items: center;
    justify-content: space-between;
    min-width: 246px;
    height: 50px;
    padding: 13px 19px 16px;
    background: #F7F7F7;
    border-radius: 28px;
}
.message-input{
    border: none;
    background: transparent;
    font-family: 'Poppins';
    font-style: normal;
    font-weight: 400;
    font-size: 14px;
    line-height: 21px;
}
.message-input:focus{
    outline: none;
    box-shadow: none;
}
.send-icon{
    display: block;
    width: 17px;
    height: 19px;
    cursor: pointer;
}
.record-icon{
    width: 50px;
    height: 50px;
    background: linear-gradient(180deg, #FFB91C 0%, #F2994A 88.02%, #DB9F1A 100%);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}
.record-icon:hover svg path{
    fill: black;
    transition: ease-in all .3s;
}
.send-icon:hover svg path{
    fill: #DB9F1A;
    transition: ease-in all .3s;
}
.recording{
    position: relative;
    height: 40px;
    width: 40px;
}
.circle_ripple{
  height: 10px;
  width: 10px;
  background:#E8581B;
  border-radius:50%;
  -webkit-animation-name: ripple 2s infinite; 
  animation: ripple 2s infinite;
  position:absolute;
  left:10px;
  top: 15px;
  z-index:0
}
.circle_ripple-2{
  height:10px;
  width:10px;
  background:#E8581B;
  border-radius:50%;
  -webkit-animation-name: ripple 2s infinite; 
  animation: ripple-2 2s infinite;
  position:absolute;
  left:10px;
  top: 15px;
}
@keyframes ripple {
 0% {
transform:scale(1);
  }
  50% {
transform:scale(2);
    opacity:0.3;
  }
 100% {
transform:scale(1);
   
  }  
}
@keyframes ripple-2 {
 0% {
transform:scale(1);
  }
  50% {
transform:scale(2.7);
    opacity:0.3;
  }
 100% {
transform:scale(1);
  }  
}
</style>