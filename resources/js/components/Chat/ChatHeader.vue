<template>
    <div class="chat-header">
        <div class="client-pic rounded-circle"></div>
        <h3 class="chat-header-title">CHAT Client</h3>
    </div>
</template>
<script>
export default{
    setup(){
        return{

        }
    }
}
</script>
<style scoped>
.chat-header{
    display: flex;
    align-items: center;
}
.client-pic{
    width: 53px;
    height: 53px;
    background: #FFB91C;
}
.chat-header-title{
    margin-left: 1rem;
    font-family: 'Mulish';
    font-style: normal;
    font-weight: 900;
    font-size: 17px;
    line-height: 21px;    
    opacity: 0.5;
}
</style>