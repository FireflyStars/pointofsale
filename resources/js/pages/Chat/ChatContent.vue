<template>
    <div class="chat-body hide-scrollbar flex-1 h-100">
        <div class="chat-body-inner" style="padding-bottom: 87px">
            <div class="py-3 py-lg-5">
                <message v-for="(message, index) in messages" :key="index"
                    :direction="message.direction"
                    :userImage="message.userImage"
                    :message="message.message"
                    :time="message.time"
                    :type="message.type"
                ></message>
            </div>
        </div>
    </div>    
</template>
<script>
import { ref } from 'vue';
import Message from './Message';

export default {
    components:{
        Message
    },
    setup(){
        const messages = ref([
            {
                userId: 1,
                direction: 'In',
                userImage: '../../images/user-img.jpg',
                message: 'Hey, Marshall! How are you? Can you please change the color theme of the website to pink and purple?',
                time: '08:45 PM',
                type: 'text',
            },
            {
                userId: 2,
                direction: 'Out',
                userImage: '../../images/user-img2.jpg',
                message: 'Hey, Marshall! How are you? Can you please change the color theme of the website to pink and purple?',
                time: '08:45 PM',
                type: 'text',
            },
            {
                userId: 2,
                direction: 'Out',
                userImage: '../../images/user-img2.jpg',
                message: 'Hey, Marshall! How are you? Can you please change the color theme of the website to pink and purple?',
                time: '08:45 PM',
                type: 'text',
            },
            {
                userId: 2,
                direction: 'Out',
                userImage: '../../images/user-img2.jpg',
                message: 'Hey, Marshall! How are you? Can you please change the color theme of the website to pink and purple?',
                time: '08:45 PM',
                type: 'text',
            },
        ]);
        return {
            messages
        }
    }
}
</script>
<style scoped>
.hide-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
    overflow-y: auto;
    overflow-x: hidden;
}
.hide-scrollbar::-webkit-scrollbar {
    display: none;
}
.avatar {
    position: relative;
    display: flex;    
    display: -webkit-box;
    display: -ms-flexbox;
    height: 44px;
    width: 44px;
}
.avatar-sm {
    height: 34px;
    width: 34px;
}
.avatar, .avatar .avatar-img, .avatar .avatar-text {
    border-radius: 50%;
}
.avatar .avatar-img {
    max-width: 100%;
    height: auto;
}
.message-divider {
    margin-top: 1.5rem;
    margin-bottom: 1.5rem;
    text-align: center;
}
@media (min-width: 992px){
    .message-divider {
        margin-top: 2rem;
        margin-bottom: 2rem;
    }
}
</style>