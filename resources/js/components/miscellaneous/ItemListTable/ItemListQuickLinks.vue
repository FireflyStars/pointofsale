<template>
  <div class="item-list-quick-links d-flex gap-2">
  
    
             <transition-group tag="div" class="d-flex gap-2"  name="list" appear>
            <template v-for="link,index in links" :key="index" >
            <base-button v-if="displayableOnCurrentRoute(link.page_route)" :title="link.name" @contextmenu="showdelete($event,link)" :btnStyle="`background-color:${link.background_color};color:${link.font_color};`" @click="goto(link)"  prepend  :classList="`btn quicklink body_medium ${link.icon_name!=''&&link.icon_name!=null?'':' nogap'}`">
            <icon v-if="link.icon_name!=''&&link.icon_name!=null" :name="link.icon_name" width="24px" height="24px" :color="link.font_color"/>
            </base-button>   
            </template> 
             </transition-group>
      


         <transition enter-active-class="animate__animated animate__fadeIn" >
            <base-button v-if="is_admin" classList="nogap" @click="showaddbtn" title="" ><icon name="link" width="22px" height="22px"/></base-button>
         </transition>
  </div>


  <simple-modal-popup v-model="showmodal_quicklink" title="Créer un lien" @modalconfirm="newquicklink" @modalclose="closemodal" icon="link" iconStyles="width:24px;height:24px;">
    <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-6">Nom</div><div class="col-6"><input type="text" v-model="link.name" class="input-text"/></div>
            </div>
            <div class="row mb-3">
                <div class="col-6">Route</div><div class="col-6"><input type="text" v-model="link.route" class="input-text"/></div>
            </div>
            <div class="row mb-3">
                <div class="col-6">Icône</div><div class="col-6"><input type="text" v-model="link.icon_name" class="input-text"/></div>
            </div>
          <div class="row mb-3">
                <div class="col-6">Couleur Fond</div><div class="col-6"><input type="color" v-model="link.background_color" class="input-text p-2"/></div>
            </div>
          <div class="row mb-3">
                <div class="col-6">Couleur texte</div><div class="col-6"><input type="color" v-model="link.font_color" class="input-text p-2"/></div>
         </div>
          <div class="row mb-3">
                <div class="col-6">Type</div><div class="col-6"> <select-box
                                        v-model="link.type" 
                                        placeholder="Chosir un type" 
                                        :options="typeOptions" 
                                        name="linktype" /></div>
         </div>

    </div>
     
</simple-modal-popup>
</template>

<script>
import { ref } from '@vue/reactivity'
import Swal from 'sweetalert2';
import { ITEMLISTQUICKLINK_ADD_LINK, ITEMLISTQUICKLINK_GET_ISSUPERADMIN, ITEMLISTQUICKLINK_GET_LINKS, ITEMLISTQUICKLINK_LOAD_LINKS, ITEMLISTQUICKLINK_MODULE, ITEMLISTQUICKLINK_REMOVE_LINK, TOASTER_CLEAR_TOASTS, TOASTER_MESSAGE, TOASTER_MODULE } from '../../../store/types/types';
import { useStore } from 'vuex';
import { useRouter } from 'vue-router';
import { computed, onMounted } from '@vue/runtime-core';
export default {
        name: "ItemListQuickLinks",
        components:{
            Swal
        },
    
        setup(props){
            const showmodal_quicklink=ref(false);
            const store=useStore();
            const router=useRouter();
            
            onMounted(()=>{
                store.dispatch(`${ITEMLISTQUICKLINK_MODULE}${ITEMLISTQUICKLINK_LOAD_LINKS}`);
            })
            const links=computed(()=>store.getters[`${ITEMLISTQUICKLINK_MODULE}${ITEMLISTQUICKLINK_GET_LINKS}`]);
            const is_admin=computed(()=>store.getters[`${ITEMLISTQUICKLINK_MODULE}${ITEMLISTQUICKLINK_GET_ISSUPERADMIN}`]);
            const link=ref({
                name:'',
                route:'',
                background_color:'#e86f29',
                font_color:'#FFFFFF',
                icon_name:'',
                type:0,
                page_route:router.currentRoute.value.name

            })
            const typeOptions = ref([
            { value:'0', display:'Lien interne' },
            { value:'1', display:'Lien externe' }
            ])
            const showaddbtn=()=>{
                showmodal_quicklink.value=true;
            }
            const closemodal=()=>{

            }
            const newquicklink=()=>{
            let valide=true;
            store.commit(`${TOASTER_MODULE}${TOASTER_CLEAR_TOASTS}`);
                if(link.value.name.trim()==''){
                    store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                                    type: 'danger',
                                    message: 'Veuillez saisir un nom.',
                                    ttl: 8,
                                });
                                valide=false;
                }
                if(link.value.route.trim()==''){
                    store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                                    type: 'danger',
                                    message: 'Veuillez saisir une route.',
                                    ttl: 8,
                                });
                                valide=false;
                }
                 if(valide){
                 store.dispatch(`${ITEMLISTQUICKLINK_MODULE}${ITEMLISTQUICKLINK_ADD_LINK}`,link.value);
                 showmodal_quicklink.value=false;
                 }
             
            }
                const goto=(link)=>{
                        if(link.type==0){
                            router.push({ path: link.route})
                        }else{
                            window.location=link.route;
                        }
                 }
                 const showdelete=(e,link)=>{
                    if(is_admin){
                        Swal.fire({
                                title: 'Veuillez confirmer!',
                                text: `Voulez-vous le bouton "${link.name}". Cette action est irréversible.`,
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#42A71E',
                                cancelButtonColor: 'var(--lcdtOrange)',
                                cancelButtonText: 'Annuler',
                                confirmButtonText: `Oui, s'il vous plaît.`
                            }).then((result) => {
                                
                                if(result.isConfirmed)
                                  store.dispatch(`${ITEMLISTQUICKLINK_MODULE}${ITEMLISTQUICKLINK_REMOVE_LINK}`,link.id);
                                
                            });

                    e.preventDefault();
                    }
                    
                 }

                 const displayableOnCurrentRoute=(page_route)=>{
                  
                        if(page_route==router.currentRoute.value.name)
                        return true;

                        let parent=router.currentRoute.value.matched.filter(obj=>obj.name==page_route);
                        if(parent.length>0)
                        return true;

                        return false;
                 }
             return {
                showmodal_quicklink,
                showaddbtn,
                newquicklink,
                closemodal,
                link,
                typeOptions,
                is_admin,
                links,
                goto,
                router,
                showdelete,
                displayableOnCurrentRoute

             }
        }
    }

</script>

<style lang="scss" scoped>
.item-list-quick-links{
    position: absolute;
    top:-140px;
    right: 0;
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
        transform-origin: right center;
        opacity: 1;
        transform: scale(1);
   
    }
    .list-leave-to{
        transform-origin: right center;
        opacity: 0;
        transform: scale(0.6);
    }
    .list-leave-active{
               transition: all 1s ease;
         transform-origin: right center;
        position:absolute;     
        width: 100%;
    }
    .list-move{
        transition:all 0.3s ease;
    }
</style>