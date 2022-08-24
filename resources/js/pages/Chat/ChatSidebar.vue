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
                            <chat-user v-for="(user, index) in userList" :key="index" 
                                :id="user.id" 
                                :imgUrl="user.imgUrl" 
                                :name="user.name"
                                :lastMessage="user.lastMessage"
                                :lastMessageTime="user.lastMessageTime"
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
import { computed } from 'vue';
import ChatUser from './ChatUser';
import {
    CHAT_MODULE,
    GET_USERS,
} from '../../store/types/types';
import { useStore } from 'vuex';

export default {
    components:{
        ChatUser
    },
    setup(){
        const store = useStore();
        const userList = computed(()=>{
            return store.getters[`${CHAT_MODULE}${GET_USERS}`];
        })
        return{
            userList
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