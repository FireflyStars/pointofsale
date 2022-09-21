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
           <div class="col-lg-6 col-sm-3 auth-bg" ></div>
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
            const logoUrl = ref('../../images/logolcdt.png');
            onMounted(()=>{
                // axios.post('/get-logo').then((res)=>{
                //     if(res.data != '')
                //         logoUrl.value = res.data;
                // }).catch((error)=>{
                //     console.log(error);
                // }).finally(()=>{

                // });                
                 nextTick(()=>{
                    showimg.value=true;
                });
            });
            return {
                logoUrl,
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
    background: #EEEEEE url('/images/login.jpg') no-repeat  right bottom;
    background-size: cover ;
}
.auth-form{

}
</style>