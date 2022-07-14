<template>
  <router-view>
    <transition enter-active-class="animate__animated animate__fadeIn">
      <div class="container-fluid h-100 bg-color" id="container">
        <main-header />
        <div class="row d-flex align-content-stretch align-items-stretch flex-row hmax main-view-wrap reports-page" style="z-index:100">
            <side-bar />
            <div class="col main-view container">
                <h1 class="d-flex align-items-center m-0">
                  <span class="action-icon"></span>
                  <span class="ms-3 font-22 almarai_extrabold_normal_normal">ACTION COMMERCIAL CALENDAR VIEW</span>
                </h1>
                <div class="col-12 bg-white mt-3 rounded p-3">
                    <full-calendar :options="calendarOptions"></full-calendar>
                </div>
            </div>
        </div>
      </div>
    </transition>
  </router-view>
</template>
<script>
import { ref, onMounted } from 'vue';
import {     
  DISPLAY_LOADER,
  HIDE_LOADER,
  LOADER_MODULE,
  TOASTER_MESSAGE,
  TOASTER_MODULE, 
} from '../../store/types/types';
  
import axios from 'axios';
import { useStore } from 'vuex';
import { useRouter } from 'vue-router';
import '@fullcalendar/core/vdom' // solves problem with Vite
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin  from '@fullcalendar/daygrid'
import timeGridPlugin  from '@fullcalendar/timegrid'
import bootstrap5Plugin from '@fullcalendar/bootstrap5';
import frLocale from '@fullcalendar/core/locales/fr';

export default {
    name: 'ActionCommercialCalendar',
    components:{
        FullCalendar
    },
    setup() {
        const store = useStore();
        const router = useRouter();
        const calendarOptions = ref({
            themeSystem: 'bootstrap5',
            headerToolbar: {
                start: 'today prevYear,prev,next,nextYear', // will normally be on the left. if RTL, will be on the right
                center: 'title',
                end: 'dayGridMonth,timeGridWeek,timeGridDay' // will normally be on the right. if RTL, will be on the left
            },
            plugins: [ dayGridPlugin, timeGridPlugin, bootstrap5Plugin ],
            locale: frLocale, // the initial locale. of not specified, uses the first one
            initialView: 'dayGridMonth',
            views: {
                dayGridMonth: { // name of view
                    titleFormat: { year: 'numeric', month: 'long' }
                },
                timeGridWeek: { // name of view
                    titleFormat: { year: 'numeric', month: 'short', day: 'numeric' },
                }
            },            
            allDaySlot: false,
            buttonIcons:{
                prev: 'chevron-left',
                next: 'chevron-right',
                prevYear: 'chevron-double-left',
                nextYear: 'chevron-double-right'
            },
            eventSources:{
                url: '/get-actions-for-calendar',
                method: 'POST',
                extraParams:{
                    _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                failure: (error) => {
                    store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                        type: 'danger',
                        message: error.message,
                        ttl: 5,
                    });  
                },
                color: '#e86f29',
                textColor: 'white'
            },
            loading: (isLoading)=>{
                if(isLoading){
                    store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'chargement des publicités d\'action.']);
                }else{
                    store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
                }
            },            
        })
        onMounted(()=>{

        })
        return {
            calendarOptions
        }
  },
}
</script>
<style lang="scss" scoped>
.main-view{
    padding: 0;
    h1{
        padding: 60px 10px 0 0;
    }
}
</style>
<style lang="scss">
.btn-primary{
    background: #e86f29;
    border-color: #e86f29;
    &:focus{
        box-shadow: none;
    }
    &:hover{
        background: rgba(232, 88, 27, 1);;
        border-color: rgba(232, 88, 27, 1);;
    }
    &.active,
    &:active{
        background: rgba(232, 88, 27, 1);
        border-color: rgba(232, 88, 27, 1);
        &:focus{
            box-shadow: none;
        }
    }
    &:disabled{
        background: #e86f29;
        border-color: #e86f29;        
    }
    &:focus{
        background: rgba(232, 88, 27, 1);
        border-color: rgba(232, 88, 27, 1);
        box-shadow: none;
    }
}
// .fc .fc-daygrid-day.fc-day-today,
// .fc-timegrid-col.fc-day-today .fc-timegrid-col-frame {
//     background-color: #e86f29;
//     opacity: .6;
// }
</style>