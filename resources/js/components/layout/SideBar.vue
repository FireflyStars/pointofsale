<template>
    <div 
        class="col-sm-3 side-bar-wrap d-flex flex-column align-items-center" 
        :class="{slidein:slidesidebar}" 
        v-if="$route.name!='Login'"
    >
            
        <div class="d-flex flex-column side-bar align-items-center position-fixed">

        <!-- :class="{active:route_name=='/'}" -->
        <!-- :class="{isActive :route_name=='LandingPage'}" -->
        <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" class="side-icons"  @click="router.push({name:'LandingPage'})" :class="route_name =='LandingPage' ? 'active' :''">
        <rect width="32" height="32" rx="8" />
        <path transform="translate(4.585,5.145)" d="M12 0.5L16.226 6.683L23.413 8.792L18.838 14.722L19.053 22.208L12 19.69L4.94698 22.208L5.16198 14.722L0.586975 8.792L7.77398 6.683L12 0.5ZM9.99998 12H7.99998C7.99996 13.0436 8.40776 14.0458 9.13638 14.7929C9.86499 15.54 10.8567 15.9727 11.8999 15.9988C12.9432 16.0249 13.9553 15.6423 14.7203 14.9326C15.4854 14.2228 15.9428 13.2422 15.995 12.2L16 12H14C14.0023 12.519 13.8028 13.0185 13.4436 13.3932C13.0844 13.7678 12.5937 13.9881 12.075 14.0075C11.5564 14.027 11.0505 13.8441 10.6643 13.4975C10.278 13.1509 10.0416 12.6677 10.005 12.15L9.99998 12Z" fill="black"/>
            </svg>

        <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" class="side-icons">
        <rect width="32" height="32" rx="8"/>
        <path transform="translate(6.975,4.975)" d="M8 13V15C6.4087 15 4.88258 15.6321 3.75736 16.7574C2.63214 17.8826 2 19.4087 2 21H0C0 18.8783 0.842855 16.8434 2.34315 15.3431C3.84344 13.8429 5.87827 13 8 13ZM8 12C4.685 12 2 9.315 2 6C2 2.685 4.685 0 8 0C11.315 0 14 2.685 14 6C14 9.315 11.315 12 8 12ZM8 10C10.21 10 12 8.21 12 6C12 3.79 10.21 2 8 2C5.79 2 4 3.79 4 6C4 8.21 5.79 10 8 10ZM14 20.5L11.061 22.045L11.622 18.773L9.245 16.455L12.531 15.977L14 13L15.47 15.977L18.755 16.455L16.378 18.773L16.938 22.045L14 20.5Z" fill="black"/>
        </svg>

        <svg 
            width="32" 
            height="32" 
            viewBox="0 0 32 32" 
            fill="none" 
            xmlns="http://www.w3.org/2000/svg" 
            class="side-icons" 
            @click="router.push({ name: 'entite' })"
            :class="route_name =='entite' ? 'active' :''"
        >
            <rect width="32" height="32" rx="8" />
            <path transform="translate(8,5.5)" d="M7 13.062V19H9V13.062C12.946 13.554 16 16.92 16 21H0C3.30739e-05 19.0514 0.711187 17.1698 2 15.7083C3.28882 14.2467 5.0667 13.3058 7 13.062ZM8 12C4.685 12 2 9.315 2 6C2 2.685 4.685 0 8 0C11.315 0 14 2.685 14 6C14 9.315 11.315 12 8 12Z" fill="black"/>
        </svg>

        <svg 
            @click="router.push({ name: 'templates' })"
            width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" class="side-icons" 
            :class="route_name =='templates' ? 'active' :''"
            >
            <rect width="32" height="32" rx="8" />
            <path transform="translate(6.515,6.515)" d="M5.05002 12.1209L2.93002 14.2429L5.75802 17.0709L17.071 5.75695L14.243 2.92995L12.12 5.04995L13.535 6.46395L12.12 7.87995L10.706 6.46495L9.29202 7.87995L10.706 9.29395L9.29202 10.7079L7.87802 9.29395L6.46302 10.7079L7.87802 12.1219L6.46302 13.5369L5.05002 12.1199V12.1209ZM14.95 0.807947L19.192 5.04995C19.3795 5.23747 19.4848 5.49178 19.4848 5.75695C19.4848 6.02211 19.3795 6.27642 19.192 6.46395L6.46402 19.1919C6.27649 19.3794 6.02218 19.4847 5.75702 19.4847C5.49186 19.4847 5.23755 19.3794 5.05002 19.1919L0.80802 14.9499C0.620549 14.7624 0.515233 14.5081 0.515233 14.2429C0.515233 13.9778 0.620549 13.7235 0.80802 13.5359L13.536 0.807947C13.7235 0.620476 13.9779 0.51516 14.243 0.51516C14.5082 0.51516 14.7625 0.620476 14.95 0.807947ZM12.12 16.3629L13.535 14.9489L15.777 17.1919H17.191V15.7779L14.949 13.5349L16.363 12.1209L19 14.7569V18.9999H14.758L12.121 16.3629H12.12ZM3.63602 7.87795L0.807019 5.04995C0.714043 4.95707 0.640284 4.84679 0.58996 4.72539C0.539636 4.60399 0.513733 4.47386 0.513733 4.34245C0.513733 4.21103 0.539636 4.0809 0.58996 3.95951C0.640284 3.83811 0.714043 3.72782 0.807019 3.63495L3.63602 0.806947C3.82355 0.619475 4.07786 0.51416 4.34302 0.51416C4.60818 0.51416 4.86249 0.619475 5.05002 0.806947L7.88002 3.63495L6.46402 5.04995L4.34302 2.92795L2.92902 4.34295L5.05002 6.46295L3.63602 7.87795Z" fill="black" />
        </svg>

        <svg 
            @click="router.push({ name: 'reports' })"
            width="32" 
            height="32" 
            viewBox="0 0 24 24" 
            fill="none" xmlns="http://www.w3.org/2000/svg"
            class="side-icons"
            :class="route_name =='reports' ? 'active' :''"
        >
            <path d="M4 7.80985C4 7.05613 4 6.67926 4.10029 6.37461C4.29878 5.77169 4.77169 5.29878 5.37461 5.10029C5.67926 5 6.05613 5 6.80985 5C7.13404 5 7.29614 5 7.43364 5.04022C7.70521 5.11967 7.93042 5.31045 8.05343 5.56527C8.11571 5.69429 8.14236 5.85418 8.19566 6.17396L8.25 6.5C8.35527 7.13161 8.4079 7.44741 8.59795 7.66237C8.66164 7.73441 8.7354 7.79689 8.81694 7.84788C9.06021 8 9.38037 8 10.0207 8H13.9793C14.6196 8 14.9398 8 15.1831 7.84788C15.2646 7.79689 15.3384 7.73441 15.4021 7.66237C15.5921 7.44741 15.6447 7.13161 15.75 6.5L15.8043 6.17396C15.8576 5.85418 15.8843 5.69429 15.9466 5.56527C16.0696 5.31045 16.2948 5.11967 16.5664 5.04022C16.7039 5 16.866 5 17.1902 5C17.9439 5 18.3207 5 18.6254 5.10029C19.2283 5.29878 19.7012 5.77169 19.8997 6.37461C20 6.67926 20 7.05613 20 7.80985V17C20 18.8856 20 19.8284 19.4142 20.4142C18.8284 21 17.8856 21 16 21H8C6.11438 21 5.17157 21 4.58579 20.4142C4 19.8284 4 18.8856 4 17V7.80985Z" 
            fill="#000" fill-opacity="0.25"/>
            <rect x="4" y="5" width="16" height="16" rx="2" stroke="black" stroke-width="1.2"/>
            <path d="M16 2L16 6C16 6.94281 16 7.41421 15.7071 7.7071C15.4142 8 14.9428 8 14 8L10 8C9.05719 8 8.58579 8 8.29289 7.70711C8 7.41421 8 6.94281 8 6L8 2" stroke="black" stroke-width="1.2" stroke-linecap="round"/>
            <path d="M9 14L15 14" stroke="black" stroke-width="1.2" stroke-linecap="round"/>
        </svg>

        <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg"  class="side-icons">
        <rect width="32" height="32" rx="8" />
        <path transform="translate(6,7.22)" d="M13.366 0.437988L16.577 5.99999H20V7.99999H18.833L18.076 17.083C18.0552 17.3329 17.9413 17.5658 17.7568 17.7357C17.5723 17.9055 17.3308 17.9999 17.08 18H2.92C2.66925 17.9999 2.4277 17.9055 2.24322 17.7357C2.05875 17.5658 1.94481 17.3329 1.924 17.083L1.166 7.99999H0V5.99999H3.422L6.634 0.437988L8.366 1.43799L5.732 5.99999H14.267L11.634 1.43799L13.366 0.437988ZM11 9.99999H9V14H11V9.99999ZM7 9.99999H5V14H7V9.99999ZM15 9.99999H13V14H15V9.99999Z" fill="black"/>
        </svg>

        <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" class="side-icons">
        <rect width="32" height="32" rx="8" />
        <path transform="translate(5.75,7)" d="M1 18C0.734784 18 0.48043 17.8946 0.292893 17.7071C0.105357 17.5196 0 17.2652 0 17V1C0 0.734784 0.105357 0.48043 0.292893 0.292893C0.48043 0.105357 0.734784 0 1 0H8.414L10.414 2H18C18.2652 2 18.5196 2.10536 18.7071 2.29289C18.8946 2.48043 19 2.73478 19 3V6H17V4H9.586L7.586 2H2V13.998L3.5 8H20.5L18.19 17.243C18.1358 17.4592 18.011 17.6512 17.8352 17.7883C17.6595 17.9255 17.4429 18 17.22 18H1ZM17.938 10H5.062L3.562 16H16.438L17.938 10Z" fill="black"/>
        </svg>

        <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" class="side-icons" @click="router.push({name:'emailing'})" :class="route_name =='emailing' || getEmailingParentPath($route.path)=='/emailing' ? 'active' :''">

        <rect width="32" height="32" rx="8" />
        <path transform="translate(5,7)"  d="M2 2.5V0.993C2.00183 0.730378 2.1069 0.479017 2.29251 0.293218C2.47813 0.107418 2.72938 0.00209465 2.992 0H21.008C21.556 0 22 0.445 22 0.993V17.007C21.9982 17.2696 21.8931 17.521 21.7075 17.7068C21.5219 17.8926 21.2706 17.9979 21.008 18H2.992C2.72881 17.9997 2.4765 17.895 2.29049 17.7088C2.10448 17.5226 2 17.2702 2 17.007V16H20V4.3L12 11.5L2 2.5ZM0 7H5V9H0V7ZM0 12H8V14H0V12Z" fill="black"/>
        </svg>

        <!-- -            <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" class="side-icons" :class="{active:route_name=='LandingPage'||route_name=='OrderDetails'}" @click="router.push({name:'LandingPage'})">
        -            <rect width="32" height="32" rx="8" fill="#42A71E"/>
        -            <path fill-rule="evenodd" clip-rule="evenodd" d="M8.71045 11H23.1138C23.731 11 24.2009 11.5537 24.1005 12.1627L23.0843 18.3254C22.9251 19.2913 22.09 20 21.111 20H11.8917C10.9127 20 10.0776 19.2913 9.9183 18.3254L8.71045 11Z" stroke="white" stroke-linecap="round" stroke-linejoin="round"/>
        -            <path fill-rule="evenodd" clip-rule="evenodd" d="M12 25C12.5523 25 13 24.5523 13 24C13 23.4477 12.5523 23 12 23C11.4477 23 11 23.4477 11 24C11 24.5523 11.4477 25 12 25Z" stroke="white"/>
        -            <path fill-rule="evenodd" clip-rule="evenodd" d="M21 25C21.5523 25 22 24.5523 22 24C22 23.4477 21.5523 23 21 23C20.4477 23 20 23.4477 20 24C20 24.5523 20.4477 25 21 25Z" stroke="white"/>
        -            <path fill-rule="evenodd" clip-rule="evenodd" d="M10 11H6H10Z" fill="white"/>
        -            <path d="M10 11H6" stroke="white" stroke-linecap="round"/>
        -        </svg> -->

        <!--
            <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" class="side-icons">
        -                <rect width="32" height="32" rx="8" fill="white"/>
        -                <path fill-rule="evenodd" clip-rule="evenodd" d="M7 16C6.44772 16 6 16.4477 6 17L6 24C6 24.5523 6.44772 25 7 25H9C9.55228 25 10 24.5523 10 24V17C10 16.4477 9.55228 16 9 16H7Z" stroke="#868686" stroke-linecap="round"/>
        -                <path fill-rule="evenodd" clip-rule="evenodd" d="M15 7C14.4477 7 14 7.44772 14 8L14 24C14 24.5523 14.4477 25 15 25H17C17.5523 25 18 24.5523 18 24V8C18 7.44772 17.5523 7 17 7H15Z" stroke="#868686" stroke-linecap="round"/>
        -                <path fill-rule="evenodd" clip-rule="evenodd" d="M23 11C22.4477 11 22 11.4477 22 12V24C22 24.5523 22.4477 25 23 25H25C25.5523 25 26 24.5523 26 24V12C26 11.4477 25.5523 11 25 11H23Z" stroke="#868686" stroke-linecap="round"/>
        -            </svg>
        -
        -
        -
        -            <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" class="side-icons" :class="{active:route_name=='LandingPage'||route_name=='OrderDetails'}" @click="router.push({name:'LandingPage'})">
        -            <rect width="32" height="32" rx="8" fill="#42A71E"/>
        -            <path fill-rule="evenodd" clip-rule="evenodd" d="M8.71045 11H23.1138C23.731 11 24.2009 11.5537 24.1005 12.1627L23.0843 18.3254C22.9251 19.2913 22.09 20 21.111 20H11.8917C10.9127 20 10.0776 19.2913 9.9183 18.3254L8.71045 11Z" stroke="white" stroke-linecap="round" stroke-linejoin="round"/>
        -            <path fill-rule="evenodd" clip-rule="evenodd" d="M12 25C12.5523 25 13 24.5523 13 24C13 23.4477 12.5523 23 12 23C11.4477 23 11 23.4477 11 24C11 24.5523 11.4477 25 12 25Z" stroke="white"/>
        -            <path fill-rule="evenodd" clip-rule="evenodd" d="M21 25C21.5523 25 22 24.5523 22 24C22 23.4477 21.5523 23 21 23C20.4477 23 20 23.4477 20 24C20 24.5523 20.4477 25 21 25Z" stroke="white"/>
        -            <path fill-rule="evenodd" clip-rule="evenodd" d="M10 11H6H10Z" fill="white"/>
        -            <path d="M10 11H6" stroke="white" stroke-linecap="round"/>
        -        </svg>
        -
        -        <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" class="side-icons">
        -            <rect width="32" height="32" rx="8" fill="white"/>
        -            <path d="M12 9V25" stroke="#868686" stroke-linecap="round"/>
        -            <path fill-rule="evenodd" clip-rule="evenodd" d="M24 7V25H10C8.89543 25 8 24.1046 8 23V9C8 7.89543 8.89543 7 10 7H24Z" stroke="#868686" stroke-linecap="round" stroke-linejoin="round"/>
        -            <path d="M12 21.0001C12 19.0001 15.3333 19.3334 16.6667 18.0001C17.3333 17.3334 15.3333 17.3334 15.3333 14.0001C15.3333 11.7781 16.222 10.6667 18 10.6667C19.778 10.6667 20.6667 11.7781 20.6667 14.0001C20.6667 17.3334 18.6667 17.3334 19.3333 18.0001C20.6667 19.3334 24 19.0001 24 21.0001" stroke="#868686" stroke-linecap="round"/>
        -        </svg>
        -
        -
        -        <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" class="side-icons">
        -            <rect width="32" height="32" rx="8" fill="white"/>
        -            <path fill-rule="evenodd" clip-rule="evenodd" d="M10.1827 18.003C9.96548 18.003 9.88583 17.8529 10.0052 17.6673L16.7844 7.12177C16.9036 6.93637 17.0003 6.96432 17.0003 7.19442V13.5886C17.0003 13.8141 17.1828 13.997 17.3937 13.997H21.4141C21.6314 13.997 21.711 14.1471 21.5917 14.3327L14.8124 24.8782C14.6932 25.0636 14.5966 25.0357 14.5966 24.8056V18.4114C14.5966 18.1859 14.414 18.003 14.2032 18.003H10.1827Z" stroke="#868686" stroke-linecap="round" stroke-linejoin="round"/>
        -        </svg>
        -
        -
        -        <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" class="side-icons">
        -                <rect width="32" height="32" rx="8" fill="white"/>
        -                <path d="M8 21H7C6.44772 21 6 20.5523 6 20V12C6 11.4477 6.44772 11 7 11H19C19.5523 11 20 11.4477 20 12V15C20 15.5523 20.4477 16 21 16H26M19 21H12" stroke="#868686" stroke-linecap="round"/>
        -                <path d="M19 21H20M20 12H23.382C23.7607 12 24.107 12.214 24.2764 12.5528L26 16V20C26 20.5523 25.5523 21 25 21H24" stroke="#868686" stroke-linecap="round"/>
        -                <circle cx="10" cy="21" r="2" stroke="#868686"/>
        -                <circle cx="22" cy="21" r="2" stroke="#868686"/>
        -            </svg> -->
        </div>

        <div class="user_initials body_bold" @click="showmenu">
            {{initials}}
        </div>

        <transition name="usermenu" >
            <div class="usermenu" v-if="dispmenu" >
                <button class="btn mb-3 btn-outline-primary body_medium"  data-bs-toggle="tooltip" data-bs-placement="right" title="Developer Components Library" @click="router.push({name:'ComponentsTest'})">Developer</button>
                <button class="btn btn-outline-dark body_medium"  data-bs-toggle="tooltip" data-bs-placement="right" title="Logout user" @click="logout">Sign out</button>
            </div>
        </transition>

    </div>

</template>

<script>

    import {ref,computed,watch} from 'vue';
    import { useRouter, useRoute } from 'vue-router'

    import axios from 'axios';
    import {useStore} from 'vuex';
    import {
        DISPLAY_LOADER,
        HIDE_LOADER,
        LOADER_MODULE,
        SIDEBAR_GET_SLIDEIN,
        SIDEBAR_MODULE
    } from "../../store/types/types";

    export default {
        name: "SideBar",
        components:{},
        setup(){
            const store=useStore();
            const uname=ref(window.sessionStorage.getItem('name'));
            const initials= ref((uname.value!=null?uname.value.substr(0,2):''));
            const router = useRouter();
            const route = useRoute();
            const dispmenu=ref(false);
            const route_name=ref(route.name);
            const full_route_path = ref('');
            const route_path = ref('');


            watch(() =>route.name, (current_val, previous_val) => {

                route_name.value=current_val;
                // emit('checkbox-clicked', current_val,props.id,props.name)


            });
            function logout(){
                showmenu();
                store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`,[true,'Logging out, please wait...']);

                axios.get('/logout', {

                })
                    .then(function (response) {
                        if(response.data.ok==1) {
                            sessionStorage.clear();
                           // router.push({
                            //    name:'Login',

                            //})
                            window.location="/";
                        }
                    })
                    .catch(function (error) {
                        console.log(error);
                    }).finally(()=>{
                    store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
                });
            }
            function showmenu() {
                dispmenu.value=!dispmenu.value;
            }
            const slidesidebar=computed(()=>{
                return store.getters[`${SIDEBAR_MODULE}${SIDEBAR_GET_SLIDEIN}`];
            });

            function getEmailingParentPath(val){
                return val.substring(0,9);
            }


            return {
                uname,
                initials,
                logout,
                showmenu,
                dispmenu,
                slidesidebar,
                route_name,
                router,
                route_path,
                getEmailingParentPath,
            }
        }
    }
</script>

<style lang="scss" scoped>

.user_initials{
  z-index: 2;
  text-transform: uppercase;
  background-color:#868686;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  text-align: center;
  line-height: 40px;
  vertical-align: middle;
  font-size: 16px;
  margin: auto 0 16px 0;
  position: fixed;
  bottom:16px;
  color:#FFF;
  &:hover{
    background-color: #333;
    cursor:pointer;
  }
}
    .side-bar-wrap{
        width: 72px;
        transition: left ease-in-out 0.5s;
    }

.side-bar{
    background:#FBFBFB;
    box-shadow: inset 0px 0px 6px rgba(0, 0, 0, 0.25);
    width: 72px;
    height: 100%;
    z-index: 1;
}
.side-icons:first-child{
     margin-top:114px;

}
.side-icons{
    margin-bottom: 32px;
    cursor: pointer !important

}
    .usermenu{
        background: #FFFFFF;

        /* Drop shadow */
        box-shadow: 0px 4px 16px rgba(0, 0, 0, 0.12);
        border-radius: 4px;
        min-width: 184px;
        position: fixed;
        left: 16px;
        bottom: 79px;
        z-index: 2;
        padding:45px 1rem 37px 1rem;
        transform-origin: left bottom;
    }
    .usermenu .btn{
        min-width: 154px;
        margin: 0 auto;
        display: block;

    }
    .usermenu-enter-from{
        opacity: 0;
        transform: scale(0.6);
    }
    .usermenu-enter-to{
        opacity: 1;
        transform: scale(1);
    }
    .usermenu-enter-active{
        transition: all ease 0.2s;
    }
    .usermenu-leave-from{
        opacity: 1;
        transform: scale(1);
    }
    .usermenu-leave-to{
        opacity: 0;
        transform: scale(0.6);
    }
    .usermenu-leave-active{
        transition: all ease 0.2s;
    }
    rect{
        transition: fill  .3s ease;

    }

    .side-icons.active rect{
     fill:#FFA500 !important;
    }

    .side-icons.active path,  .side-icons.active circle {
        fill: #A23E13 !important;
    }

    .side-icons:not(.active):hover path, .side-icons:not(.active):hover circle {
        fill: #fff !important;
    }
    .side-icons rect{
        fill:#FBFBFB !important;
    }
    .side-icons:not(.active):hover rect {
        fill: #47454B !important;
    }
    </style>
