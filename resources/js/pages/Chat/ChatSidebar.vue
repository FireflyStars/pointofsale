<template>
<aside class="sidebar bg-light">
    <div class="h-100">
        <!-- Chats -->
        <div class="h-100">
            <div class="d-flex flex-column h-100 position-relative">
                <div class="hide-scrollbar">
                    <div class="container py-3">
                        <!-- Title -->
                        <div class="mb-3">
                            <h2 class="fw-bold m-0">Chats</h2>
                        </div>

                        <!-- Chats -->
                        <div class="card-list">
                            <chat-user v-for="(user, index) in users" :key="index" 
                                :id="user.id" 
                                :imgUrl="user.imgUrl" 
                                :name="user.name"
                                :lastMessage="user.lastMessage"
                                :unReadCnt="user.unReadCnt"
                                :online="user.online"
                                :isTyping="user.isTyping"
                            ></chat-user>
                        </div>
                        <!-- Chats -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</aside>    
</template>
<script>
import { ref } from 'vue';
import ChatUser from './ChatUser';

export default {
    components:{
        ChatUser
    },
    setup(){
        const users = ref([
            {
                id: 1,
                name: 'William Wright',
                imgUrl: '../../images/user-img.jpg',
                lastMessage: 'Hey, Marshall! How are you? Can you please change the color theme of the website to pink and purple?',
                unReadCnt: 3,
                isTyping: false,
                online: false,
            },
            {
                id: 2,
                name: 'Ollie Chandler',
                imgUrl: '../../images/user-img2.jpg',
                lastMessage: 'Hey, Marshall! How are you? Can you please change the color theme of the website to pink and purple?',
                unReadCnt: 0,
                isTyping: true,
                online: true,
            },
        ])
        return{
            users
        }
    }
}
</script>
<style scoped>
@media (min-width: 1200px){
    .sidebar {
        width: 410px;
        height: 100%;
        overflow: hidden;
    }
    h2, .h2 {
        font-size: 1.375rem;
    }
}
.card-list .card+.card {
    margin-top: 1rem;
}
</style>