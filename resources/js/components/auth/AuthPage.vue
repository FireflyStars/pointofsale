<template>
    <router-view v-slot="{ Component }">
    <transition
            enter-active-class="animate__animated animate__fadeIn"
    >
    <div class="container-fluid h-100" v-if="showimg">
        <div class="row auth-logo">
            <div class="col-12 p-lg-0 d-flex align-items-center">
                <img :src="logoUrl" height="55">
            </div>
        </div>
        <div class="row d-flex align-content-stretch align-items-stretch flex-row hmax">
           <div class="col-lg-6 col-sm-3 auth-bg" :style="{ 'backgroundImage': 'url('+loginImageUrl+')' }"></div>
            <div class="col-lg-6 auth-form col-sm-9"><Component :is="Component" /></div>
        </div>
    </div>
    </transition>
    </router-view>
</template>

<script>
    import {ref,onMounted,nextTick} from 'vue';
    import axios from 'axios';
    export default {
        name: "AuthPage",
        setup(props,context){
            const showimg=ref(false);
            const logoUrl = ref('');
            // const logoUrl = ref('../../images/logolcdt.png');
            // const loginImageUrl = ref('../../images/login.jpg');
            const loginImageUrl = ref('');
            onMounted(()=>{
                axios.post('/get-login-setting').then((res)=>{
                    if(res.data.logoUrl != ''){
                        logoUrl.value = res.data.logoUrl;
                    }
                    if(res.data.loginImage != ''){
                        loginImageUrl.value = res.data.loginImage;
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

                });                
                 nextTick(()=>{
                    showimg.value=true;
                });
            });
            return {
                logoUrl,
                loginImageUrl,
                showimg
            }
        }
    }
</script>

<style scoped>

.hmax{
    height: calc(100% - var(--authlogoheight));
}

.auth-logo{
    height: var(--authlogoheight);
}
.auth-bg{
    background: #EEEEEE no-repeat  right bottom;
    background-size: cover;
}
</style>