<template>
    <div class="chat-body" id="chat-body">
      <div class="message-list">
        <message v-for="(message, index) in messages" :key="index" :message="message"></message>
      </div>
    </div>
</template>
<script>
import Message from './Message.vue';
export default{
    props:{
        messages:{
            type: Array,
        }
    },
    components:{
        Message,
    },
    setup(){
        return{

        }
    }
}
</script>
<style scoped>
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
.chat-body{
    height: 332px;
    width: 100%;
    overflow-y: auto;
    padding-bottom: 50px;
}
</style>