<template>
<main class="main is-visible bg-white">
    <div class="container h-100">
        <div class="d-flex flex-column h-100 position-relative">
            <!-- Chat: Header -->
            <chat-header></chat-header>
            <!-- Chat: Header -->

            <!-- Chat: Content -->
            <chat-content></chat-content>
            <!-- Chat: Content -->

            <!-- Chat: Footer -->
            <chat-footer></chat-footer>
            <!-- Chat: Footer -->
        </div>
    </div>
</main>    
</template>
<script>
import ChatHeader from './ChatHeader.vue';
import ChatContent from './ChatContent.vue';
import ChatFooter from './ChatFooter.vue';
export default {
    components:{
        ChatHeader,
        ChatContent,
        ChatFooter,
    }
}
</script>
<style scoped>
.main{
    height: 100%;
    position: relative;
    -webkit-box-flex: 1;
    -ms-flex: 1;
    flex: 1;
    overflow: hidden;
}
</style>