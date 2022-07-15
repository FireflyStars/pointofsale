<template>
<transition enter-active-class="animate__animated animate__fadeIn"
            leave-active-class="animate__animated animate__fadeOut">
        <div class="simple-modal-overlay"  v-if="showmodalpopupoverlay" @click.self="closeSimpleModal">
            <transition enter-active-class="sm-show"
            leave-active-class="sm-hide">
            <div v-if="showmodalpopup" class="simple-modal-popup" :style="style">
                <div class="container-fluid">
                  <div class="row mt-4">
                    <div class="col d-flex align-items-center gap-2 justify-content-center">
                      <icon :name="icon" v-show="!!icon" :style="iconStyles" />
                      <h2 class="simple-modal-title" v-if="title!=''">{{title}}</h2>
                    </div>
                  </div>
                </div>
                <slot></slot>
                   <div class="container-fluid">
                      <div class="d-flex justify-content-center gap-4">
                         
                            <button type="button" @click="confirm" class="swal2-confirm swal2-styled swal2-default-outline" aria-label="" style="display: inline-block; background-color: rgb(66, 167, 30);">{{ confirmButtonTitle }}</button>
                          
                        
                            <button type="button" @click="annuler" class="swal2-cancel swal2-styled swal2-default-outline" aria-label="" style="display: inline-block; background-color: var(--lcdtOrange);">Annuler</button>
                         
                      </div>
                   </div>
            </div>
            </transition>
        </div>
</transition>
</template>

<script>
import { ref } from '@vue/reactivity';
import { nextTick, watch } from '@vue/runtime-core';
export default {
    name: 'SimpleModalPopup',
    props:{
        modelValue:{
            type:Boolean,
            default:false,
            required:true
        },
        style:{
            type:Object,
            default:{width:'620px'},
            required:false
        },
        title:{
            type:String,
            default:'',
            required:false
        },
        confirmButtonTitle: {
          required: false,
          default: 'Enregistrer',
          type: String,
        },
        icon: {
          required: false,
          default: null,
          type: String,
        },
        iconStyles: {
          required: false,
          default: null,
          type: String,
        },
    },
    
    emits: ['update:modelValue','modalconfirm','modalclose'],
    
    setup(props,context){
        const showmodalpopup=ref(false);
        const showmodalpopupoverlay=ref(false);
        watch(() => props.modelValue, (current_val, previous_val) => {
            if(current_val==false){
              closeSimpleModal();
            }else{
            showmodalpopupoverlay.value=current_val;
            nextTick(()=>{
                showmodalpopup.value=current_val;
            })  
            } 
        });

        const closeSimpleModal=()=>{
          showmodalpopup.value=false;
            nextTick(()=>{
                  showmodalpopupoverlay.value=false;

                  context.emit("update:modelValue",false);
                  context.emit("modalclose");
            });   
        }
        const annuler=()=>{
          closeSimpleModal();
        }
        const confirm=()=>{
          context.emit("modalconfirm");
        }
        return {
            showmodalpopupoverlay,
            showmodalpopup,
            closeSimpleModal,
            annuler,
            confirm
        }
    }
}
</script>

<style scoped>
.swal2-styled:hover {
    background-image: linear-gradient(rgba(0,0,0,.1),rgba(0,0,0,.1));
}
.simple-modal-title{
  
    position: relative;
    max-width: 100%;
    margin: 0;
  
    color: inherit;
    font-size: 1.875em;
    font-weight: 600;
    text-align: center;
    text-transform: none;
    word-wrap: break-word;
}

.simple-modal-popup {
    color:#545454;
    grid-column: 2;
    grid-row: 2;
    align-self: center;
    justify-self: center;
    display: grid;
    position: relative;
    z-index: 1060;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    box-sizing: border-box;
    /*grid-template-areas: "top-start     top            top-end" "center-start  center         center-end" "bottom-start  bottom-center  bottom-end";
    grid-template-rows: minmax(-webkit-min-content,auto) minmax(-webkit-min-content,auto) minmax(-webkit-min-content,auto);
    grid-template-rows: minmax(min-content,auto) minmax(min-content,auto) minmax(min-content,auto);*/
    min-height: 100%;
    padding: .625em;
   /* overflow-x: hidden;*/
    transition: background-color .1s;
    -webkit-overflow-scrolling: touch;
    background-color: #FFFFFF;
    border-radius: 5px;
}



.simple-modal-overlay{

    background:rgba(224, 224, 224,0.6);
    display: grid;
    position: fixed;
    z-index: 10002;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    box-sizing: border-box;
    grid-template-areas: "top-start     top            top-end" "center-start  center         center-end" "bottom-start  bottom-center  bottom-end";
    grid-template-rows: minmax(-webkit-min-content,auto) minmax(-webkit-min-content,auto) minmax(-webkit-min-content,auto);
    grid-template-rows: minmax(min-content,auto) minmax(min-content,auto) minmax(min-content,auto);
    height: 100%;
    padding: .625em;
    overflow-x: hidden;
    transition: background-color .1s;
    -webkit-overflow-scrolling: touch;
}
.animate__animated.animate__fadeIn,.animate__animated.animate__fadeOut {
  --animate-duration: .2s;
}


.sm-show {
  -webkit-animation: sm-show 0.3s;
          animation: sm-show 0.3s;
}

.sm-hide {
  -webkit-animation: sm-hide 0.15s forwards;
          animation: sm-hide 0.15s forwards;
}

@-webkit-keyframes sm-show {
  0% {
    transform: scale(0.7);
  }
  45% {
    transform: scale(1.05);
  }
  80% {
    transform: scale(0.95);
  }
  100% {
    transform: scale(1);
  }
}
@keyframes sm-show {
  0% {
    transform: scale(0.7);
  }
  45% {
    transform: scale(1.05);
  }
  80% {
    transform: scale(0.95);
  }
  100% {
    transform: scale(1);
  }
}
@-webkit-keyframes sm-hide {
  0% {
    transform: scale(1);
    opacity: 1;
  }
  100% {
    transform: scale(0.5);
    opacity: 0;
  }
}
@keyframes sm-hide {
  0% {
    transform: scale(1);
    opacity: 1;
  }
  100% {
    transform: scale(0.5);
    opacity: 0;
  }
}

</style>