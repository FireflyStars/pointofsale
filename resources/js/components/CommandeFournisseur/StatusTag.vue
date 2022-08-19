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
    ORDERSTATETAG_LOAD_ORDER_STATES, 
    COMMANDE_FOURNISSEUR_STATUS_MODULE 
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

const order_states = computed(() => store.getters[`${COMMANDE_FOURNISSEUR_STATUS_MODULE}orderStates`])
const loaded = computed(() => store.getters[`${COMMANDE_FOURNISSEUR_STATUS_MODULE}loaded`])


watch(() => order_states, (current_val) => {
    const order_state = current_val.value.find(obj => obj.id == props.id)
    if(typeof order_state != "undefined") {
        status.value = order_state.name
        style.value = `width:${props.width}; background-color: ${order_state.color};color: ${order_state.fontcolor}`
    }
}, {
    deep: true
})

watch(() => props.id, (current_val) => {
    const order_state=order_states.value.find(obj => obj.id == current_val)
    if(typeof order_state != "undefined") {
        status.value = order_state.name
        style.value = `width:${props.width}; background-color: ${order_state.color}; color: ${order_state.fontcolor}`
    }
}, {
    deep:true
})

onMounted(async ()=> {
    if(loaded.value === false) {
        await store.dispatch(`${COMMANDE_FOURNISSEUR_STATUS_MODULE}${ORDERSTATETAG_LOAD_ORDER_STATES}`) 
    }
    if(order_states.value.length > 0) {
        const order_state = order_states.value.find(obj => obj.id == props.id)
        if(typeof order_state != "undefined") {
            status.value = order_state.name
            style.value = `width:${props.width};background-color: ${order_state.color};color: ${order_state.fontcolor || '#000'}`
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
