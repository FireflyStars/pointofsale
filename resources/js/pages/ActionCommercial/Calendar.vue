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
                  <span class="ms-3 font-22 almarai_extrabold_normal_normal">ACTION COMMERCIAL AGENDA</span>
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
import { ref } from 'vue';
import {     
  DISPLAY_LOADER,
  HIDE_LOADER,
  LOADER_MODULE,
  TOASTER_MESSAGE,
  TOASTER_MODULE, 
} from '../../store/types/types';
  
import { useStore } from 'vuex';
import { useRouter } from 'vue-router';
// import '@fullcalendar/core/vdom' // solves problem with Vite
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin  from '@fullcalendar/daygrid'
import timeGridPlugin  from '@fullcalendar/timegrid'
import bootstrap5Plugin from '@fullcalendar/bootstrap5';
import interactionPlugin from '@fullcalendar/interaction';
import frLocale from '@fullcalendar/core/locales/fr';
import tippy from 'tippy.js';
import 'tippy.js/dist/tippy.css'; // optional for styling

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
                start: 'today prevYear,prev,next,nextYear',
                center: 'title',
                end: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            plugins: [ dayGridPlugin, timeGridPlugin, bootstrap5Plugin, interactionPlugin ],
            locale: frLocale, // Franch
            initialView: 'dayGridMonth',
            selectable: true,
            views: {
                dayGridMonth: {
                    titleFormat: { year: 'numeric', month: 'long' },
                    dayMaxEventRows: 6
                },
                timeGridWeek: {
                    titleFormat: { year: 'numeric', month: 'short', day: 'numeric' },
                    dayMaxEventRows: 6,
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
            eventContent: function(info) {
              let italicEl = document.createElement('div')
              if(info.view.type == 'dayGridMonth'){
                italicEl.classList.add('w-100');
                var eventContent = '<div class="d-flex w-100">';
                    eventContent  +='<div class="d-flex align-items-center">';
                        eventContent  +=('<div class="fc-daygrid-event-dot" style="border-color: ' + info.event.backgroundColor +';">');
                        eventContent  +='</div>';
                    eventContent  +='</div>';
                    eventContent  +='<div class="w-100">';
                      eventContent  +='<div class="d-flex">';
                        eventContent  +='<div class="fc-event-time">';
                            eventContent  += info.timeText;
                        eventContent  +='</div>';
                        eventContent  +='<div class="fc-event-title">';
                            eventContent  += info.event.title;
                        eventContent  +='</div>';
                      eventContent  +='</div>';
                      eventContent  +='<div class="d-flex"><b>';
                          eventContent  += info.event.extendedProps.assignedUserName;
                      eventContent  +='</b></div>';
                    eventContent  +='</div>';
                  eventContent  +='</div>';
                italicEl.innerHTML = eventContent;
              }else{
                italicEl.classList.add('fc-event-main-frame');
                let eventContent = '<div class="fc-event-time fc-sticky">'+ info.timeText +'</div>';
                eventContent += '<div class="fc-event-title-container"><div class="fc-event-title fc-sticky">'+ info.event.title +'</div>';
                eventContent += '<div class="fc-event-title fc-sticky fw-bold">'+ info.event.extendedProps.assignedUserName +'</div></div>';
                italicEl.innerHTML = eventContent;
              }
              let arrayOfDomNodes = [ italicEl ]
              return { domNodes: arrayOfDomNodes }              
            },            
            eventClick: (eventClickInfo)=>{
              /**
               * eventClickInfo is a plain object with the following properties:
               * 
               * event(The associated Event Object.)
               * 
               * el(The HTML element for this event.)
               * 
               * jsEvent(The native JavaScript event with low-level information such as click coordinates.)
               * 
               * view (The current View Object.)
               *  */ 
              router.push({
                name: 'action-commercial-details',
                params: { id: eventClickInfo.event.extendedProps.dbId },
              })
            },
            eventDidMount: (info)=>{
              tippy(info.el, {
                content: info.event.extendedProps.description,
                theme: 'tomato',
                allowHTML: true,
                placement: 'top',
                trigger: 'mouseenter',
                duration: [250, 0]
              });
              info.el
            },   
            dateClick: function(info){
              
              alert('Clicked on: ' + info.dateStr);
            }
                       
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
.fc-daygrid-dot-event{
    background: rgba(0, 0, 0, 0.04);
}
.fc-daygrid-dot-event:hover{
    cursor: pointer;
    background: rgba(0, 0, 0, 0.2);
}
.tippy-box[data-theme~='tomato'] {
  background-color: #e86f29;
  color: white;
}
.tippy-box[data-theme~='tomato'][data-placement^='top'] > .tippy-arrow::before {
  border-top-color: #e86f29;
}
</style>