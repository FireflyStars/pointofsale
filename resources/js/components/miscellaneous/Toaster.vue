<template>

        <transition-group tag="div" class="toast-wrap" name="list" appear>
        <div class="toaster" v-for="(toast,index) in toasts" :key="index" :class="toast.type" @click="dismiss(toast.id)">
           {{toast.message}}
        </div>
        </transition-group>


</template>

<script>
    import {TOASTER_GET_ALL, TOASTER_MODULE, TOASTER_REMOVE_TOAST} from '../../store/types/types';
    import {computed} from 'vue';
    import {useStore} from 'vuex';
    export default {
        name: "Toaster",
        setup(){
            const dismiss=((id)=>{
                store.commit(`${TOASTER_MODULE}${TOASTER_REMOVE_TOAST}`,id);
            });
            const store=useStore();
            const toasts=computed(()=>store.getters[`${TOASTER_MODULE}${TOASTER_GET_ALL}`]);
             return {
                 dismiss,
                 toasts
             }
        }
    }
</script>

<style scoped>
    .toast-wrap{
        position: fixed;top: 75px; left:50%;transform: translate(-50%); height: auto;width: 100%;max-width:480px; z-index: 10003;display: flex;align-items: center;justify-content: center;
        flex-direction: column;
    }
 .toaster{
        width: 100%;
     padding: 10px 20px;
     color: white;
     position: relative;
     border-radius: 10px;
     box-shadow: 1px 3px 5px rgba(0,0,0,0.2);
     margin-bottom: 10px;
     text-align: center;
    cursor: pointer;
 }
    .toaster.danger{
        background:var(--lcdtRed);
    }
    .toaster.success{
        background:rgba(66, 167, 30, 0.95);
    }
    .toaster.warn{
        background:rgba(250,222,179,0.97);
        color:#000;
    }

    .list-enter-from{
        opacity: 0;
    
        transform: translateY(-60px) scale(0.6);
    }
    .list-enter-to{
        opacity: 1;
        transform: scale(1);
        transform: translateY(0);
    }
    .list-enter-active{
        transition: all 0.3s ease-in-out;
    }

    .list-leave-from{
        opacity: 1; 
        transform: translateY(0) scale(1);
    }
    .list-leave-to{
        opacity: 0;
  
        transform: translateY(-60px) scale(0.6);
    }
    .list-leave-active{
        transition: all 0.3s ease-in-out;
       position: absolute;
        width: 100%;
    }
    .list-move{
        transition:all 0.5s ease-in-out;
    }
</style>