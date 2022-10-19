<template>
    <div class="container-fluid h-100 bg-color">
        <main-header />

        <div class="row d-flex align-content-stretch align-items-stretch flex-row hmax main-view-wrap" style="z-index:100">
            <side-bar />
            <transition
                enter-active-class="animate__animated animate_fadeIn"
                leave-active-class="animate__animated animate_fadeOut"
            >
                <div class="col main-view container">
                    <page-title 
                        icon="statistique" 
                        name="SAISIE RAPIDE" 
                        class="almarai_extrabold_normal_normal"
                        style="height: 45px; width: 45px;"
                    />
                    <div class="row m-0 ml-5 mr-5">
                        <div class="col p-0">
                            <WeekController @date-selected="dateSelected"></WeekController>
                            <Pointage :date="date"></Pointage>
                        </div>
                    </div>
                </div>
            </transition>    
        </div>
    </div>
</template>
<script>
import { useStore } from 'vuex'
import { ref, watch, onMounted } from 'vue';
import WeekController from './WeekController.vue';
import Pointage from './Pointage.vue';

export default {
    components:{
        WeekController,
        Pointage,
    },
    setup(){
        const store = useStore();
        const date = ref('');
        const dateSelected = (dateSelected)=>{
            date.value = dateSelected;
        }
        return {
            date,
            dateSelected
        }
    }
}
</script>
<style scoped>
.main-view{
    margin: 64px 10px 0 72px;
}
.page-title{
    margin-left: 0 !important
}
.tp-tabs{
    padding-left: 0 !important;
}
</style>