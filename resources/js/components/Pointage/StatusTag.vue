<template>
   <transition 
        enter-active-class="animate__animated animate__fadeIn" 
        leave-active-class="animate__animated animate__fadeOut"
    >
        <span 
            class="tag" 
            :style="style" 
            :class="classes" 
            v-if="status!=''"
        >
            {{ status.toLowerCase() }}
        </span>
   </transition>

</template>

<script setup>

import { computed, onMounted, onBeforeMount, ref, watch } from 'vue'
import { useStore } from 'vuex'

import { 
    POINTAGE_LOAD_ORDER_STATES, 
    POINTAGE_STATUS_MODULE,
    POINTAGE_SET_LOADED
} from '../../store/types/types';

const props = defineProps({
    id: {
        required: true,
        type: Number
    },
    classes: {
        required: false,
        type: String
    },
    width: {
        required: false,
        type: String,
        default: '120px'
    }
})

const store = useStore()
const status = ref('')
const style = ref('')

const pointageStates = computed(() => store.getters[`${POINTAGE_STATUS_MODULE}pointageStates`])
const loaded = computed(() => store.getters[`${POINTAGE_STATUS_MODULE}loaded`])


watch(() => pointageStates, (current_val) => {
    const state = current_val.value?.find(obj => obj.id == props.id)
    if(typeof state != "undefined") {
        status.value = state.name
        style.value = `width:${props.width}; background-color: ${state.color};color: ${state.fontcolor}`
    }
}, {
    deep: true
})

watch(() => props.id, (current_val) => {
    const state=pointageStates.value?.find(obj => obj.id == current_val)
    if(typeof state != "undefined") {
        status.value = state.name
        style.value = `width:${props.width}; background-color: ${state.color}; color: ${state.fontcolor}`
    }
}, {
    deep:true
})

onMounted(async ()=> {
    console.log(loaded.value, " is the loaded value")
    if(loaded.value === false) {
        await store.dispatch(`${POINTAGE_STATUS_MODULE}${POINTAGE_LOAD_ORDER_STATES}`) 
    }
    if(pointageStates.value?.length > 0) {
        const state = pointageStates.value?.find(obj => obj.id == props.id)
        if(typeof state != "undefined") {
            status.value = state.name
            style.value = `width:${props.width};background-color: ${state.color};color: ${state.fontcolor || '#000'}`
        }
    }
    
})

onBeforeMount(() => {
    store.commit(`${POINTAGE_STATUS_MODULE}${POINTAGE_SET_LOADED}`, false)
})
          
           
</script>

<style scoped>
.tag {
    text-transform: capitalize;
    background: #DDD;
    border-radius: 70px;
    text-align: center;
    font-size: 12px;
    height: 24px;
    position: relative;
    display: inline-block;
    vertical-align: middle;
    line-height: 24px;
    transition: all 0.5s ease-in;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    padding:0 10px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;

}
  
</style>
