<template>
    <div class="row main-logo" :style="{ 'background-color': headerColor }">
        <div class="col-12 p-0 d-flex align-items-center">
            <img @click="slideinMenu"
                :src="logoUrl"
                alt="Lcdt logo"
                class="logo img-fluid"
            
            />
           <div>
               
                <search></search>  
                <!-- <base-button
                    prepend
                    @click="router.push( { name: 'CreateCustomer' });"
                    class="btn btn-newcustomer body_medium"
                    kind=""
                    title="Nouveau Client"
                >
                    <icon name="user" color="white" />
                </base-button>
                <base-button
                    prepend
                    @click="router.push( { name: 'CreateAction' });"
                    class="btn btn-newrdv body_medium"
                    kind=""
                    title="Nouveau Rendez Vous"
                >
                    <icon name="user-star" color="white" />
                </base-button>

                <base-button
                    prepend
                    @click="createDevis"
                    class="btn btn-newdevis body_medium"
                    kind=""
                    title="Nouveau Devis"
                >
                    <icon name="clipboard" />
                </base-button> -->
           </div>
        </div>
    </div>
</template>

<script>

    import { onMounted, ref } from 'vue';
    import { useStore } from 'vuex'
    import { SIDEBAR_MODULE, SIDEBAR_SET_SLIDEIN, TOASTER_MESSAGE, TOASTER_MODULE } from "../../store/types/types";
    import Search from '../miscellaneous/Search.vue';
    import axios from 'axios';
    import { useRouter } from 'vue-router'
    export default {
        name: "MainHeader",
        components: { Search },
        setup() {

            const store=useStore();
            const router=useRouter();
            const logoUrl = ref('../../images/logolcdt.png');
            const headerColor = ref('#070113');
            const featureunavailable=((feature)=>{
                store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{message:feature+' feature not yet implemented.',ttl:5,type:'danger'});
            });

            const slideinMenu = ()=> {
                router.push({
                    name: 'LandingPage'
                })
                store.commit(`${SIDEBAR_MODULE}${SIDEBAR_SET_SLIDEIN}`);
            }
            const createDevis = ()=>{
                router.push( { name: 'CreateDevis' });
            }
            const neworder=()=>{
                router.push({
                    name: 'NewOrder',
                    params: {

                    },
                })
            }
            onMounted(()=>{
                axios.post('/get-header-setting').then((res)=>{
                    if(res.data.logoUrl != ''){
                        logoUrl.value = res.data.logoUrl;
                    }
                    if(res.data.faviconUrl != ''){
                        const favicon = document.querySelector("link[rel~='icon']")
                        favicon.href = res.data.faviconUrl;
                    }
                    if(res.data.faviconUrl != ''){
                        headerColor.value = res.data.headerColor;
                    }
                }).catch((error)=>{
                    console.log(error);
                }).finally(()=>{

                })
            });
           return {
               neworder,
               slideinMenu,
               featureunavailable,
               createDevis,
               router,
               logoUrl,
               headerColor
           }
        }
    }
</script>

<style scoped>
    .main-logo {
        position: fixed;
        width: 100%;
        z-index: 10;
        box-shadow: 0px 0px 4px rgba(80, 80, 80, 0.2);
    }
    .btn-newdevis {
        background: var(--lcdtOrange);
        margin-right:7px;
        margin-top: 9px;
        float: left;
        font-size: 12px;
        font-weight: 700;
        width: auto;
        color:#FFF;
        font-family: "Open Sans";
        height: 48px;
        border-radius: 10px;
    }
    .btn-newrdv {
        background: rgba(232, 88, 27, 0.7);
        margin-right:7px;
        margin-top: 9px;
        float: left;
        font-size: 12px;
        font-weight: 700;
        width: auto;
        color:#FFF;
        font-family: "Open Sans";
        height: 48px;
        border-radius: 10px;
    }
      .btn-newcustomer {
        background: rgba(232, 88, 27, 0.47);
        margin-right:7px;
        margin-top: 9px;
        float: left;
        font-size: 12px;
        font-weight: 700;
        width: auto;
        color:#FFF;
        font-family: "Open Sans";
        height: 48px;
        border-radius: 10px;
    }
     .btn-devis{
        background: #E8581B;
        margin-right:22px;
        margin-top: 9px;
        float: left;
        font-size: 12px;
        font-weight: 700;
        width: auto;
        font-family: "Open Sans";
        color:#FFF;
        height: 48px;
        border-radius: 10px;
    }
    .col-12 {
        flex-direction: row;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }

    .logo{
        cursor: pointer;
        height:64px;
    }
    button span{
        vertical-align: middle;
        margin-left: 8px;
    }
</style>