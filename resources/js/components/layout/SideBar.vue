<template>

    <transition
        enter-active-class="animate__animated animate__fadeIn"
        leave-active-class="animate__animated animate__fadeOut"
    >

        <div 
            class="col-sm-1 side-bar-wrap d-flex flex-column align-items-start"
            style="padding-left: 0; margin-left: 0;width:max-content;" 
            :class="{ slidein:slidesidebar }" 
            v-if="$route.name != 'Login'"
        >

                
            <div 
                class="d-flex flex-column align-items-start side-bar position-fixed"
                :class="{ 'side-bar--visible': isSidebarVisible }"
                @mouseover="isSidebarVisible=true"
                @mouseleave="hideSidebar"
                style="padding-left: 1.2rem;"
            >

                <template v-for="item in items" :key="item.id">

                    <div
                        class="d-flex align-items-center justify-content-between cursor-pointer" 
                        :class="{ 'first-child': item.id == 1 }"
                        style="gap: .5rem; margin-bottom: 20px;"
                        @click.prevent="navigateOrOpenMenu(item)"
                    >

                        <Icon
                            :name="item.icon" 
                            width="24" 
                            height="24" 
                            class="side-icons"
                            :class="{ 'active': highlight_current(item.urlname) || isChildActivated(item) }"
                        />

                        <span class="list-title">{{ lodash.upperFirst(item.title) }}</span>

                        <Icon name="angle-down" width="8" height="16" v-if="item.children.length" class="angle-down" />
                        <Icon name="angle-down" width="8" height="16" style="opacity: 0; visibility: hidden;" v-else class="angle-down" />

                        
                    </div>

                    <template v-if="activeItem.id == item.id && activeItem.show == true">
                        
                        <template v-for="child in item.children" :key="child.id"> 
                            
                            <div
                                class="d-flex align-items-center justify-content-center cursor-pointer" 
                                style="gap: .5rem; margin-bottom: 20px; margin-left: 2rem;"
                                @click.prevent="navigateOrOpenMenu(child)"
                            >
                                <Icon 
                                    :name="child.icon" 
                                    width="24" 
                                    height="24" 
                                    class="side-icons"
                                    :class="{ 'active': highlight_current(child.urlname) }"
                                />

                                <span class="list-title">{{ lodash.upperFirst(child.title) }}</span>

                            </div>

                        </template>
                    
                    </template>


                </template>
                

            </div>

            <div class="user_initials body_bold" @click="showmenu">
                {{ initials }}
            </div>

            <transition name="usermenu">
                <div class="usermenu" v-if="dispmenu" v-click-away="close">
                    <button class="btn mb-3 btn-outline-success body_medium"  data-bs-toggle="tooltip" data-bs-placement="right" title="Réinitialiser toutes les listes" @click="reinit()">Réinitialiser liste</button>
                    <button class="btn mb-3 btn-outline-primary body_medium"  data-bs-toggle="tooltip" data-bs-placement="right" title="Librairie de composants pour développeurs" @click="router.push({name:'ComponentsTest'})">Développeur</button>
                    <button class="btn btn-outline-dark body_medium"  data-bs-toggle="tooltip" data-bs-placement="right" title="Déconnexion de l'utilisateur" @click="logout">Se déconnecter</button>
                </div>
            </transition>

        </div>

    </transition>

</template>

<script>
    export default {
        name: "SideBar",
    }
</script>

<script setup>
    
    import lodash from 'lodash'
    import { ref, reactive, computed, watch, onMounted } from 'vue'
    import { useRouter, useRoute } from 'vue-router'

    import axios from 'axios'
    import { useStore } from 'vuex'

    import {
        DISPLAY_LOADER,
        GET_MENU_ITEMS,
        HIDE_LOADER,
        LOADER_MODULE,
        MENU_ITEMS_MODULE,
        SIDEBAR_GET_SLIDEIN,
        SIDEBAR_MODULE
    } from "../../store/types/types"

    import Icon from '../miscellaneous/Icon.vue'

    const store=useStore();
    const uname=ref(window.sessionStorage.getItem('name'));
    const initials= ref((uname.value!=null?uname.value.substr(0,2):''));
    const router = useRouter();
    const route = useRoute();
    const dispmenu=ref(false);
    const route_name=ref(route.name);
    const full_route_path = ref('')
    const route_path = ref('')

    const isSidebarVisible = ref(false)

    const activeItem = reactive({
        id: null,
        show: false
    })


    watch(() =>route.name, (current_val, previous_val) => {
        route_name.value = current_val
    })

    const items = computed(() => store.getters[`${MENU_ITEMS_MODULE}items`])

    const isChildActivated = (item) => {
        const childRoutes = item.children?.map(child => child.urlname)
        return childRoutes.includes(route.name)
    }
    onMounted(()=>{
        // axios.post('/get-sidebar-setting').then((res)=>{
        //     if(res.data.sidebarActiveColor != ''){
        //         document.querySelector('.side-icons.active rect').style.fill = res.data.sidebarActiveColor + '! important';
        //     }
        // }).catch((error)=>{
        //     console.log(error);
        // }).finally(()=>{

        // })        
    })
    const navigateOrOpenMenu = (item) => {

        if(item?.children?.length) {

            if(item.id == activeItem.id) {
                activeItem.show = !activeItem.show
                return
            }

            activeItem.show = true
            activeItem.id = item.id
            return   
        }

        try {
            router.push({ name: item.urlname })
        }

        catch(e) {
            throw e
        }

    }

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

    const close = () => dispmenu.value = false

    function showmenu() {
        dispmenu.value = !dispmenu.value;
    }

    const slidesidebar=computed(()=>{
        return store.getters[`${SIDEBAR_MODULE}${SIDEBAR_GET_SLIDEIN}`];
    })

    function getEmailingParentPath(val){
        return val.substring(0,9)
    }

    const highlight_current=(page_route)=>{

        if(page_route==router.currentRoute.value.name)
        return true;

        let parent=router.currentRoute.value.matched.filter(obj=>obj.name==page_route);
        
        if(parent.length>0)
        return true;

        return false;
    }

    const getMenu = () => {
        try {
            store.dispatch(`${MENU_ITEMS_MODULE}${GET_MENU_ITEMS}`)
        }
        catch(e) {
            throw e
        }
    }

    const reinit=()=>{
        window.localStorage.clear()
        window.location.reload()
    }

    const hideSidebar = () => {
        isSidebarVisible.value=false
        activeItem.show = false
        activeItem.id = null
    }

    onMounted(() => {
        getMenu()
    })

    
</script>

<style lang="scss" scoped>

.first-child {
    margin-top: 90px;
}

.list-title {
    font-family: 'Almarai Regular';
    font-style: normal;
    font-weight: 800;
    font-size: 14px;
    line-height: 110%;
    display: flex;
    align-items: center;
    color: #000000;
    display: none;
}

.user_initials{
  z-index: 20;
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
  left: .6rem;
  &:hover{
    background-color: #333;
    cursor:pointer;
  }
}
.side-bar-wrap {
    //width: 72px;
    transition: left ease-in-out 0.5s;
}

.angle-down {
    display: none;
}

.side-bar {
    background:#FBFBFB;
    box-shadow: inset 0px 0px 6px rgba(0, 0, 0, 0.25);
    width: 72px;
    height: 100%;
    z-index: 9;
    &--visible {
        //transition: width .2s;
        width: 300px;
        .list-title {
            display: inline-block;
        }
        .angle-down {
            display: inline-block;
        }
    }
    &--inner {
        display: grid;
        grid-template-columns: 20% 70% 10%;
        justify-content: center;
    }
}
.usermenu{
    background: #FFFFFF;
    box-shadow: 0px 4px 16px rgba(0, 0, 0, 0.12);
    border-radius: 4px;
    min-width: 184px;
    position: fixed;
    left: 16px;
    bottom: 79px;
    z-index: 9999;
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

rect {
    transition: fill  .3s ease;
}

.side-icons.active rect {
    fill:#FFA500 !important;
}

.side-icons.active path,  .side-icons.active circle {
    fill: #A23E13 !important;
}

.side-icons:not(.active):hover path, .side-icons:not(.active):hover circle {
    fill: #fff !important;
}

.side-icons.stroke-able:not(.active):hover path {
    stroke: #fff !important;
}
.side-icons.stroke-able.active path,  .side-icons.stroke-able.active circle {
    fill: #A23E13 !important;
    stroke: #A23E13 !important;
}
.side-icons rect {
    fill:#FBFBFB !important;
}
.side-icons:not(.active):hover rect {
    fill: #47454B !important;
}

.list-enter-from {
    opacity: 0;
    transform: scale(0.6);
}
.list-enter-to {
    opacity: 1;
    transform: scale(1);
}
.list-enter-active {
    transition: all 1s ease;
}

.list-leave-from {
    transform-origin: right center;
    opacity: 1;
    transform: scale(1);

}
.list-leave-to {
    transform-origin: right center;
    opacity: 0;
    transform: scale(0.6);
}
.list-leave-active {
            transition: all 1s ease;
        transform-origin: right center;
    position:absolute;     
    width: 100%;
}
.list-move {
    transition:all 0.3s ease;
}

</style>
