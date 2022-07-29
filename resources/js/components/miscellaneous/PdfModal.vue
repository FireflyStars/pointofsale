<template>
    <Teleport to="body">
        <div class="pdfviewer-layer d-flex align-items-center justify-content-center position-fixed" v-if="showModal" style="z-index: 9999999999;">
            <transition name="list" appear>
                <div class="pdfviewer-panel m-auto bg-white">
                    <div class="pdfviewer-header text-capitalize d-flex align-items-center justify-content-center position-relative almarai-extrabold font-22">
                        <svg @click="closeModal" class="close-icon cursor-pointer" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M6.78812 5.2973C6.3976 4.90481 5.76444 4.90481 5.37392 5.2973C4.98339 5.6898 4.98339 6.32616 5.37392 6.71865L10.5883 11.9594L5.29289 17.2816C4.90237 17.6741 4.90237 18.3105 5.29289 18.703C5.68341 19.0955 6.31657 19.0955 6.7071 18.703L12.0025 13.3808L17.293 18.6979C17.6835 19.0904 18.3166 19.0904 18.7072 18.6979C19.0977 18.3054 19.0977 17.6691 18.7072 17.2766L13.4167 11.9594L18.6261 6.7237C19.0167 6.33121 19.0167 5.69485 18.6261 5.30235C18.2356 4.90986 17.6025 4.90986 17.2119 5.30235L12.0025 10.5381L6.78812 5.2973Z" fill="black"/>
                        </svg>
                    </div>
                    <div class="pdfviewer-content">
                        <vue-pdf-embed :rotation="angle" :source="pdfData" />
                    </div>
                    <div class="pdfview-footer d-flex justify-content-center mt-2">
                        <svg @click="rotateLeft" class="me-3 cursor-pointer" width="24px" height="24px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3 2V8M3 8H9M3 8L5.64033 5.63067C7.02134 4.25209 8.81301 3.35964 10.7454 3.08779C12.6777 2.81593 14.6461 3.17941 16.3539 4.12343C18.0617 5.06746 19.4165 6.54091 20.214 8.32177C21.0115 10.1026 21.2086 12.0944 20.7756 13.997C20.3426 15.8996 19.303 17.61 17.8133 18.8704C16.3237 20.1308 14.4647 20.873 12.5165 20.9851C10.5684 21.0972 8.63652 20.5732 7.01208 19.492C5.38765 18.4108 4.15862 16.831 3.51018 14.9907" stroke="#001A72" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <svg @click="rotateRight" class="cursor-pointer" width="24px" height="24px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M20.9999 2V8M20.9999 8H14.9999M20.9999 8L18.3597 5.63067C16.9787 4.25209 15.187 3.35964 13.2546 3.08779C11.3223 2.81593 9.35389 3.17941 7.64608 4.12343C5.93827 5.06746 4.58354 6.54091 3.78602 8.32177C2.98849 10.1026 2.79138 12.0944 3.22438 13.997C3.65738 15.8996 4.69703 17.61 6.18668 18.8704C7.67634 20.1308 9.5353 20.873 11.4835 20.9851C13.4316 21.0972 15.3635 20.5732 16.9879 19.492C18.6124 18.4108 19.8414 16.831 20.4898 14.9907" stroke="#001A72" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                </div>
            </transition>
        </div>
    </Teleport>
</template>
<script>

import {onMounted, ref} from 'vue';
import VuePdfEmbed from 'vue-pdf-embed'
export default {
    name: 'PdfModal',
    components:{
        VuePdfEmbed
    },
    setup(){
        const pdfData = ref();
        const angle = ref(0);
        const showModal = ref(false);
        const openModal = (base64)=>{
            showModal.value = true;
            pdfData.value = base64;
        }
        const closeModal = ()=>{
            showModal.value = false;
        }
        const rotateRight = ()=>{
            if(angle.value < 360){
                angle.value += 90;
            }
        }
        const rotateLeft = ()=>{
            if(angle.value != 0){
                angle.value -= 90;
            }
        }
        return {
            pdfData,
            showModal,
            angle,
            openModal,
            closeModal,
            rotateLeft,
            rotateRight,
        }
    }

}
</script>
<style lang="scss" scoped>
/* width */
::-webkit-scrollbar {
  width: 9px;
}
/* Track */
::-webkit-scrollbar-track {
  background: #E0E0E0; 
}
/* Handle */
::-webkit-scrollbar-thumb {
  background: #47454B; 
  border-radius: 6px;
}

.list-enter-from{
    opacity: 0;
    transform: scale(0.6);
}
.list-enter-to{
    opacity: 1;
    transform: scale(1);
}
.list-enter-active{
    transition: all 1s ease;
}

.list-leave-from{
    opacity: 1;
    transform: scale(1);
}
.list-leave-to{
    opacity: 0;
    transform: scale(0.6);
}
.list-leave-active{
    transition: all 1s ease;
    position: absolute;
    width: 100%;
}
.list-move{
    transition:all 0.9s ease;
}
.pdfviewer-layer{
    width: 100%;
    height: 100%;
    top: 0;
    z-index: 11;
    background: rgba(0, 0, 0, 0.3);
    .pdfviewer-panel{
        width: 800px;
        height: 700px;
        padding: 40px 40px;
        .pdfviewer-header{
            .close-icon{
                position: absolute;
                top: -25px;
                right: -20px;
            }
        }
        .pdfviewer-content{
            height: 620px;
            overflow-y: auto;
            overflow-x: hidden;
        }
    }
}
</style>