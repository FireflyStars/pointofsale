<template>
   <transition enter-active-class="animate__animated animate__fadeIn" leave-active-class="animate__animated animate__fadeOut">
    <span class="tag" :style="style" :class="classes" v-if="status!=''">{{status.toLowerCase()}}</span>
   </transition>

</template>

<script>
    import {computed, onMounted, ref,watch} from 'vue';
import { useStore } from 'vuex';
import { INVOICESTATETAG_GET_LOADED, INVOICESTATETAG_GET_INVOICE_STATES, INVOICESTATETAG_LOAD_INVOICE_STATES, INVOICESTATETAG_MODULE } from '../../store/types/types';
    export default {
        name: "InvoiceStateTag",
        props:{
            invoice_state_id:{
                required:true,
                type:Number
            },
            classes:{
                required:false,
                type:String
            },
            width:{
                required:false,
                type:String,
                default:'120px'
            }
        },
        setup(props){
            const store=useStore();
            const invoice_states=computed(()=>store.getters[`${INVOICESTATETAG_MODULE}${INVOICESTATETAG_GET_INVOICE_STATES}`]);
            const loaded=computed(()=>store.getters[`${INVOICESTATETAG_MODULE}${INVOICESTATETAG_GET_LOADED}`]);  
            onMounted(()=>{
                if(loaded.value===false)
                store.dispatch(`${INVOICESTATETAG_MODULE}${INVOICESTATETAG_LOAD_INVOICE_STATES}`)
                if(invoice_states.value.length>0){
                    const invoice_state=invoice_states.value.filter(obj=>obj.id==props.invoice_state_id);
                    if(typeof invoice_state[0]!="undefined"){
                    status.value=invoice_state[0].name;
                    style.value=`width:${props.width};background-color: ${invoice_state[0].color};color: ${invoice_state[0].fontcolor}`;
                    }
                }
            })
            const status=ref('');
            const style=ref('');
            watch(() => invoice_states, (current_val, previous_val) => {
                const invoice_state=current_val.value.filter(obj=>obj.id==props.invoice_state_id);
                if(typeof invoice_state[0]!="undefined"){
                status.value=invoice_state[0].name;
                style.value=`width:${props.width}; background-color: ${invoice_state[0].color};color: ${invoice_state[0].fontcolor}`;
                }
            },{
                    deep:true
                });

            watch(() => props.invoice_state_id, (current_val, previous_val) => {
                const invoice_state=invoice_states.value.filter(obj=>obj.id==current_val);
                if(typeof invoice_state[0]!="undefined"){
                status.value=invoice_state[0].name;
                style.value=`width:${props.width}; background-color: ${invoice_state[0].color};color: ${invoice_state[0].fontcolor}`;
                }
            },{
                    deep:true
                });
       
            return {
                style,
                status
            }
        }
    }
</script>

<style scoped>
.tag{
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
}
  
</style>
