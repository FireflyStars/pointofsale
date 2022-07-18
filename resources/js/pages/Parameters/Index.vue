<template>
    
    <router-view v-slot="{ Component }">
        
        <div class="container-fluid h-100 bg-color">

            <main-header />

            <div 
                class="row d-flex align-content-stretch align-items-stretch flex-row hmax main-view-wrap" style="z-index:100"
            >

                <side-bar />

                <transition
                    enter-active-class="animate__animated animate_fadeIn"
                    leave-active-class="animate__animated animate_fadeOut"
                >

                    <div class="col main-view container">
                        
                        <page-title 
                            icon="parameters" 
                            name="PARAMETERS" 
                            class="almarai_extrabold_normal_normal"
                            style="height: 45px; width: 45px;"
                        />

                        <div class="row m-0 ml-5 mr-5">
                            
                            <div class="col">

                            
                            </div>
                            
                        </div>

                        <component :is="Component" />

                    </div>

                </transition>    

            </div>

        </div>

    </router-view>


</template>

<script setup>

    import { useStore } from 'vuex'
    import { ref, computed } from 'vue'
    import ItemListTable from '../../components/miscellaneous/ItemListTable/ItemListTable.vue';

    import {
    }
    from '../../store/types/types'


    const store = useStore()


</script>

<style lang="scss" scoped>


</style>
