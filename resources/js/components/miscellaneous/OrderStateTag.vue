<template>
   <transition enter-active-class="animate__animated animate__fadeIn" leave-active-class="animate__animated animate__fadeOut">
        <span class="tag" :style="style" :class="classes" v-if="status!=''">{{status.toLowerCase()}}</span>
   </transition>

</template>

<script>
    import {computed, onMounted, ref,watch} from 'vue';
import { useStore } from 'vuex';
import { ORDERSTATETAG_GET_LOADED, ORDERSTATETAG_GET_ORDER_STATES, ORDERSTATETAG_LOAD_ORDER_STATES, ORDERSTATETAG_MODULE } from '../../store/types/types';
    export default {
        name: "OrderStateTag",
        props:{
            order_state_id:{
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
            const order_states=computed(()=>store.getters[`${ORDERSTATETAG_MODULE}${ORDERSTATETAG_GET_ORDER_STATES}`]);
            const loaded=computed(()=>store.getters[`${ORDERSTATETAG_MODULE}${ORDERSTATETAG_GET_LOADED}`]);  
            onMounted(()=>{
                if(loaded.value===false)
                store.dispatch(`${ORDERSTATETAG_MODULE}${ORDERSTATETAG_LOAD_ORDER_STATES}`)
                if(order_states.value.length>0){
                    const order_state=order_states.value.filter(obj=>obj.id==props.order_state_id);
                    if(typeof order_state[0]!="undefined"){
                    status.value=order_state[0].name;
                    style.value=`width:${props.width};background-color: ${order_state[0].color};color: ${order_state[0].fontcolor}`;
                    }
                }
            })
            const status=ref('');
            const style=ref('');
            watch(() => order_states, (current_val, previous_val) => {
                const order_state=current_val.value.filter(obj=>obj.id==props.order_state_id);
                if(typeof order_state[0]!="undefined"){
                status.value=order_state[0].name;
                style.value=`width:${props.width}; background-color: ${order_state[0].color};color: ${order_state[0].fontcolor}`;
                }
            },{
                    deep:true
                });

            watch(() => props.order_state_id, (current_val, previous_val) => {
                const order_state=order_states.value.filter(obj=>obj.id==current_val);
                if(typeof order_state[0]!="undefined"){
                status.value=order_state[0].name;
                style.value=`width:${props.width}; background-color: ${order_state[0].color};color: ${order_state[0].fontcolor}`;
                }
            },{
                    deep:true
                });
            // const css_class=ref('');
            // const status=ref('');
            // status.value=props.tagname;
            // css_class.value=props.tagname;

            // const statuses={
            //     inprogress:'In process', // if we want to show  In progress as in process
            //     //donatedtocharity:'Donated'
            // };
            // if(css_class.value in statuses){
            //     status.value=statuses[css_class.value];

            // }
            // watch(() => props.tagname, (current_val, previous_val) => {
            //     status.value=current_val;
            //     css_class.value=current_val;
            //     if(css_class.value in statuses){
            //         status.value=statuses[css_class.value];

            //     }
            // });
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
