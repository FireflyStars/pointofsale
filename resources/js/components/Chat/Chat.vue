<template>
    <div class="chat-wrapper">
        <chat-header></chat-header>
        <chat-body :messages="messages"></chat-body>
        <chat-footer></chat-footer>
    </div>
</template>
<script>
    import { ref } from 'vue';
    import ChatHeader from './ChatHeader.vue';
    import ChatBody from './ChatBody.vue';
    import ChatFooter from './ChatFooter.vue';
    export default {
        components:{
            ChatHeader,
            ChatBody,
            ChatFooter
        },
        setup(){
            const messages = ref([
                {
                    time: '20 Aout 2019 - 19 :45',
                    content: 'Are you still travelling?',
                    type: 'text',
                    dir: 0,
                },
                {
                    time: '20 Aout 2019 - 19 :45',
                    content: 'Yes, i’m at Istanbul.. ',
                    type: 'text',
                    dir: 1,
                },
                {
                    time: '20 Aout 2019 - 19 :45',
                    content: 'What? ',
                    type: 'voice',
                    dir: 1,
                },
            ]);
            return{
                messages
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