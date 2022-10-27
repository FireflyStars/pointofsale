<template>
    <div class="attach-header">
        <div class="client-pic rounded-circle"></div>
        <h3 class="attach-header-title">Fichier attaché</h3>
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
.attach-header{
    display: flex;
    align-items: center;
}
.client-pic{
    width: 53px;
    height: 53px;
    background: #A1FA9F;
}
.attach-header-title{
    margin-left: 1rem;
    font-family: 'Mulish';
    font-style: normal;
    font-weight: 900;
    font-size: 17px;
    line-height: 21px;    
    opacity: 0.5;
}
</style>