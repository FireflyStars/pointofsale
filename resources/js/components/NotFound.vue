<template>
    <div class="container-fluid h-100">
    <div class="d-flex flex-column align-items-center justify-content-center h-100">
        <div class="text-center">
               <img :src="imageUrl" >
<br/>
        <i class="bi bi-emoji-frown h1"></i>

        <h1 class="h1"> Oups !</h1>
        <p>La page que vous recherchez n'existe pas.</p>
        </div>
    </div>
    </div>
</template>

<script>
    import { ref, onMounted } from 'vue';
    export default {
        name: "NotFound",
        setup(){
            // const imageUrl = ref('../images/logolcdt.png');
            const imageUrl = ref('');
            onMounted(()=>{
                axios.post('/get-404-setting').then((res)=>{
                    if(res.data.imageUrl != ''){
                        imageUrl.value = res.data.imageUrl;
                    }
                    if(res.data.faviconUrl != ''){
                        const favicon = document.querySelector("link[rel~='icon']")
                        favicon.href = res.data.faviconUrl;
                    }                    
                    if(res.data.title != ''){
                        const title = document.querySelector("title")
                        title.innerHTML = res.data.title;
                    }                    
                }).catch((error)=>{
                    console.log(error);
                }).finally(()=>{

                })        
            })    
            return {
                imageUrl
            }        
        }
    }
    
</script>

<style scoped>

</style>