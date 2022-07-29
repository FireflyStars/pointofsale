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
    INTERVENTION_LOAD_ORDER_STATES, 
    INTERVENTION_STATUS_MODULE,
    INTERVENTION_SET_LOADED
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

const interventionStates = computed(() => store.getters[`${INTERVENTION_STATUS_MODULE}interventionStates`])
const loaded = computed(() => store.getters[`${INTERVENTION_STATUS_MODULE}loaded`])


watch(() => interventionStates, (current_val) => {
    const state = current_val.value?.find(obj => obj.id == props.id)
    if(typeof state != "undefined") {
        status.value = state.name
        style.value = `width:${props.width}; background-color: ${state.color};color: ${state.fontcolor}`
    }
}, {
    deep: true
})

watch(() => props.id, (current_val) => {
    const state=interventionStates.value?.find(obj => obj.id == current_val)
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
        await store.dispatch(`${INTERVENTION_STATUS_MODULE}${INTERVENTION_LOAD_ORDER_STATES}`) 
    }
    if(interventionStates.value?.length > 0) {
        const state = interventionStates.value?.find(obj => obj.id == props.id)
        if(typeof state != "undefined") {
            status.value = state.name
            style.value = `width:${props.width};background-color: ${state.color};color: ${state.fontcolor || '#000'}`
        }
    }
    
})

onBeforeMount(() => {
    store.commit(`${INTERVENTION_STATUS_MODULE}${INTERVENTION_SET_LOADED}`, false)
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
