<template>
  <router-view>
    <transition enter-active-class="animate__animated animate__fadeIn">
      <div class="container-fluid h-100 bg-color" id="container">
        <main-header />
        <div class="row d-flex align-content-stretch align-items-stretch flex-row hmax main-view-wrap reports-page" style="z-index:100" >
            <side-bar />
            <div class="col main-view container d-flex">
                <chat-sidebar></chat-sidebar>
                <chat-form></chat-form>
            </div>
        </div>
      </div>
    </transition>
  </router-view>
</template>
<script>
import { ref, onMounted, watchEffect } from 'vue';

import axios from 'axios';
import { useStore } from 'vuex';
import { useRouter, useRoute } from 'vue-router';
import ChatSidebar from './ChatSidebar.vue';
import ChatForm from './ChatForm.vue';
export default {
    components:{
        ChatSidebar,
        ChatForm
    },
    setup() {
        const store = useStore();
        const router = useRouter();
        const route = useRoute();
        return {
        }
  },
}
</script>
<style>
/* width */
::-webkit-scrollbar {
    width: 9px;
}
/* Track */
::-webkit-scrollbar-track {
    background: #E0E0E0; 
}
/* Handle */
::-webkit-scrollbar-thumb {
    background: #47454B; 
    border-radius: 6px;
}
</style>
<style lang="scss" scoped>
.main-view{
    padding: 0;
    margin: 64px 50px 0 72px;
}
</style>