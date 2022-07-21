<template>
   <transition enter-active-class="animate__animated animate__fadeIn" leave-active-class="animate__animated animate__fadeOut">
    <span class="tag" :style="style" :class="classes" v-if="status!=''">{{status.toLowerCase()}}</span>
   </transition>

</template>

<script>
    import { onMounted, ref,watch} from 'vue';

    export default {
        name: "StateTag",
        props:{
            id:{
                required:true,
                type:Number
            },
            states:{
                type:Object,
                required:true
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
       
      
     
            onMounted(()=>{
            
            
                if(props.states.length>0){
                    const state=props.states.filter(obj=>obj.id==props.id);
                    if(typeof state[0]!="undefined"){
                        status.value=state[0].name;
                        style.value=`width:${props.width};background-color: ${state[0].color};color: ${state[0].fontcolor}`;
                    }
                }
            })
            const status=ref('');
            const style=ref('');


            watch(() => props.id, (current_val, previous_val) => {
                const state=props.states.filter(obj=>obj.id==props.id);
                    if(typeof state[0]!="undefined"){
                        status.value=state[0].name;
                        style.value=`width:${props.width};background-color: ${state[0].color};color: ${state[0].fontcolor}`;
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
