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

import { computed, onMounted, ref, watch } from 'vue'
import { useStore } from 'vuex'

import { 
    OUVRAGE_LOAD_TAG_STATES, 
    OUVRAGE_STATE_MODULE 
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

const states = computed(() => store.getters[`${OUVRAGE_STATE_MODULE}states`])
const loaded = computed(() => store.getters[`${OUVRAGE_STATE_MODULE}loaded`])


watch(() => states, (current_val) => {
    const state = current_val.value.find(obj => obj.id == props.id)
    if(typeof state != "undefined") {
        status.value = state.name
        style.value = `width:${props.width}; background-color: ${state.color};color: ${state.fontcolor}`
    }
}, {
    deep: true
})

watch(() => props.id, (current_val) => {
    const state=states.value.find(obj => obj.id == current_val)
    if(typeof state != "undefined") {
        status.value = state.name
        style.value = `width:${props.width}; background-color: ${state.color}; color: ${state.fontcolor}`
    }
}, {
    deep:true
})

onMounted(async ()=> {
    if(loaded.value === false) {
        await store.dispatch(`${OUVRAGE_STATE_MODULE}${OUVRAGE_LOAD_TAG_STATES}`, { route: '/get-unit-states', params: {} }) 
    }
    if(states.value.length > 0) {
        const state = states.value.find(obj => obj.id == props.id)
        if(typeof state != "undefined") {
            status.value = state.name
            style.value = `width:${props.width};background-color: ${state.color};color: ${state.fontcolor || '#000'}`
        }
    }
    
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
