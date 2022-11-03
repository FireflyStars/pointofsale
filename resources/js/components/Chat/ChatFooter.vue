<template>
    <div class="chat-footer">
        <div class="message-form">
            <input type="text" v-model="message" class="message-input" placeholder="Envoyer">
            <span class="send-icon" @click="sendMessage">
                <svg width="17" height="19" viewBox="0 0 17 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M16.8418 0.175649C16.9167 0.259535 16.9679 0.366181 16.9891 0.482337C17.0103 0.598493 17.0004 0.719042 16.9608 0.829008L10.6622 18.4298C10.6067 18.5848 10.514 18.7197 10.3943 18.8196C10.2747 18.9195 10.1326 18.9806 9.98393 18.9961C9.83521 19.0116 9.68554 18.9809 9.5514 18.9075C9.41726 18.834 9.30385 18.7207 9.22365 18.5798L5.78369 12.5362L0.37696 8.6911C0.25066 8.60154 0.148948 8.47473 0.083048 8.32464C0.0171479 8.17456 -0.0103786 8.00705 0.00350588 7.84058C0.0173903 7.67412 0.072146 7.51518 0.161731 7.3813C0.251316 7.24742 0.372247 7.14381 0.511181 7.0819L16.2573 0.0437668C16.3556 -0.000501556 16.4635 -0.0114873 16.5674 0.0121742C16.6713 0.0358357 16.7667 0.0931017 16.8418 0.176858V0.175649ZM6.86395 12.1829L9.85254 17.4316L14.9757 3.11576L6.86395 12.1829ZM14.2104 2.26035L1.4031 7.98692L6.09976 11.3263L14.2115 2.26035H14.2104Z" fill="#C5BDBD"/>
                </svg>
            </span>
        </div>
        <div class="ms-auto record-icon" @click="audioRecord">
            <svg width="50" height="50" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="25" cy="25" r="25" fill="url(#paint0_linear_4790_15317)"/>
                <path d="M25.4907 28.1904C23.4772 28.1904 21.864 26.6058 21.864 24.6428L21.8518 17.5476C21.8518 15.5846 23.4772 14 25.4907 14C27.5042 14 29.1296 15.5846 29.1296 17.5476V24.6428C29.1296 26.6058 27.5042 28.1904 25.4907 28.1904ZM19.062 24.6428C19.062 28.1904 22.143 30.6737 25.4907 30.6737C28.8385 30.6737 31.9194 28.1904 31.9194 24.6428H33.9814C33.9814 28.687 30.6822 32.0099 26.7037 32.5894V36.4681H24.2778V32.5894C20.2993 32.0218 17 28.687 17 24.6428H19.062Z" fill="white"/>
                <defs>
                <linearGradient id="paint0_linear_4790_15317" x1="25" y1="0" x2="25" y2="50" gradientUnits="userSpaceOnUse">
                <stop stop-color="#FFB91C"/>
                <stop offset="0.880208" stop-color="#F2994A"/>
                <stop offset="1" stop-color="#DB9F1A"/>
                </linearGradient>
                </defs>
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
            const mediaRecorder = ref(null);
            const audioURL = ref(null);
            const sendMessage = ()=>{
                if(voiceRecording.value == true){
                    if(mediaRecorder.value.state == 'active'){
                        mediaRecorder.value.stop();
                    }
                }else{
                    emit('sendMessage', message.value);
                    message.value = '';
                }
            }
            const audioRecord = ()=>{
                voiceRecording.value = true;
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
                            emit('sendVoiceMessage', audioURL.value);
                            audioURL.value = null;
                        };
                    })
                    // Error callback
                    .catch((err) => {
                        console.error(`The following getUserMedia error occurred: ${err}`);
                    });
                } else {
                    console.log("getUserMedia not supported on your browser!");
                }
            }
            return{
                message,
                audioURL,
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
    cursor: pointer;
}
</style>