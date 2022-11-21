<template>
    <div class="chat-wrapper">
        <chat-header></chat-header>
        <chat-body :messages="messages"></chat-body>
        <chat-footer @send-message="sendMessage" @send-voice-message="sendVoiceMessage"></chat-footer>
    </div>
</template>
<script>
    import { ref } from 'vue';
    import ChatHeader from './ChatHeader.vue';
    import ChatBody from './ChatBody.vue';
    import ChatFooter from './ChatFooter.vue';
    import moment from 'moment';
    export default {
        components:{
            ChatHeader,
            ChatBody,
            ChatFooter
        },
        setup(){
            const messages = ref([]);
            const sendMessage = (message)=>{
                messages.value.push({
                    time: moment().locale('fr').format('DD MMM YYYY - HH:mm'),
                    content: message,
                    type: 'text',
                    dir: 1,
                });
            }
            const sendVoiceMessage = (audioUrlObject)=>{
                messages.value.push({
                    time: moment().locale('fr').format('DD MMM YYYY - HH:mm'),
                    content: audioUrlObject,
                    type: 'voice',
                    dir: 1,
                });
            }
            return{
                messages,
                sendMessage,
                sendVoiceMessage
            }
        }
    }
</script>
<style scoped>
.chat-wrapper{
    position: relative;
    background: white;
    max-width: 356px;
    max-height: 467px;
    padding: 20px 20px 10px 20px;
}
</style>