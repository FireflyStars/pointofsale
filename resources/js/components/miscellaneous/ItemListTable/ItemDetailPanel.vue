<template>

     <transition enter-active-class="animate__animated animate__fadeIn" leave-active-class="animate__animated animate__fadeOut">
<div class="back-layer" v-if="show"></div> 
     </transition>
      <transition enter-active-class="animate__animated animate__slideInRight" leave-active-class="animate__animated animate__slideOutRight"  >
        <div class="od" :style="{width:width}" v-if="show">
            <div class="position-sticky"  :style="{width:`calc(100% + 40px)`,top:'0px',margin:'0 -20px'}">
                <div class="miniloader" v-if="showloader"></div>
            </div>
                 <transition enter-active-class="animate__animated animate__fadeIn" leave-active-class="animate__animated animate__fadeOut">
                <div class="close-wrapper" v-if="showclose">
                    <i class="icon-close" @click="close"></i>
                     
                </div>
               
                 </transition>
           <slot></slot>
        </div>
    </transition>
</template>

<script>
import { computed, nextTick, onMounted, ref, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useStore } from 'vuex';
import { ITEM_LIST_GET_CURRENT, ITEM_LIST_GET_IDENTIFIER, ITEM_LIST_MODULE, ITEM_LIST_SELECT_CURRENT } from '../../../store/types/types';
   
    export default {
        name: "ItemDetailPanel",
        
    props: { 
        width: {
            required: false,
            type: String,
            default:'680px'
            
        },
        closebtn:{
            required: false,
            type:Boolean,
            default:true
        },
        showloader:{
            required: false,
            type:Boolean,
            default:false
        }
    },
        setup(props){
            const store=useStore();
            const router=useRouter();
            const route=useRoute();
            const show=ref(false);
            const showclose=ref(false);
            onMounted(()=>{
               store.dispatch(`${ITEM_LIST_MODULE}${ITEM_LIST_SELECT_CURRENT}`,{current:route.params.id});
                document.getElementsByTagName( 'body' )[0].className='hide-overflowY';
                window.scrollTo({ left: 0, behavior: "smooth" });

                show.value=true;
                 setTimeout(()=>{
                showclose.value=true;
                },1000)
            })
            const identifier=computed(()=>store.getters[`${ITEM_LIST_MODULE}${ITEM_LIST_GET_IDENTIFIER}`])
            const current_sel=computed(()=>store.getters[`${ITEM_LIST_MODULE}${ITEM_LIST_GET_CURRENT}`]);
            watch(()=>current_sel,(currentValue,oldValue)=>{
                    if(currentValue.value[identifier.value]==''){
                        show.value=false;
                    setTimeout(()=>{
                        router.push({name:router.currentRoute.value.matched[0].name});
                    },1000);
                    }
            },{
                deep:true
            });

           
                const close=()=>{
                    store.dispatch(`${ITEM_LIST_MODULE}${ITEM_LIST_SELECT_CURRENT}`,{current:''});   
                }
             return {
                 show,
                 close,
                 width:props.width,
                 current_sel,
                 identifier,
                 showclose
             }
        }
    }
</script>

<style scoped>
.close-wrapper{
    position: fixed;
    right: 30px;
    width: 30px;
    height: 30px;
    top: 80px;
    background-color: rgb(221 221 221 / 70%);
    border-radius: 5px;
}
.od{
      
        background: #FFF;
        height: calc(100% - var(--mainlogoheight));
        position: fixed;
        top: var(--mainlogoheight);
        overflow-y: auto;
        right: 0;
        z-index: 10001;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.18);
        padding: 0 20px;
    }
     .icon-close{
        top:50%;
        left: 50%;
        transform: translate(-50%,-50%);
        transform-origin: center;
    }
.icon-close:hover {
  transform:translate(-50%,-50%) scale(1.5);
}
    .back-layer {
        background: rgba(224, 224, 224,0.6);
        position: fixed;
        top: 0;
        left: 0;
        height: 100%;
        width: 100%;
        z-index: 9999;
    }


 .miniloader{
    width: 100%;
    height: 4px;
    background: #EEEEEE;
    position: absolute;
    margin:0 -20px;
    display: block;
}
.miniloader:after{
    content: " ";
    background: linear-gradient(
            270deg
            , var(--lcdtOrange) 0%, #F7CDB5 51.08%, var(--lcdtOrange) 100%);
    width: 225px;
    position: absolute;
    left: 50%;
    height: 4px;
    z-index: 7;
    top: 0;
    transform: translate(-50%);
    animation: w 2s linear 0s infinite alternate-reverse;

}

@keyframes w {
    0%   {width: 325px;}
    25%  {width: 400px;}
    50%  {width: 280px;}
    100% {width: 100%;}
}
</style>