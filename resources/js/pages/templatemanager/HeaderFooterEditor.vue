<template>
    
<simple-modal-popup v-model="showmodal" :title="modal_title" @modalconfirm="save" @modalclose="closemodal" :style="{width:'1000px'}">
    <div class="container-fluid">
        <div class="row mb-3 mt-3">
            <div class="col-6">Nom</div><div class="col-6">Height</div>
        </div>
         <div class="row mb-3">
            <div class="col-6"><input type="text" class="input-text" v-model="hfobj.name"/></div><div class="col-6"><input type="number" class="input-text"  v-model="hfobj.height"/></div>
        </div>
<div class="row mb-3">
    <div class="col">Html</div>
</div>
<div class="row mb-3">
<div class="col"><textarea rows="16" class="input-text input-text-area"  v-model="hfobj.html"/>
<div class="text-danger" v-if="hfobj.html.trim().length==0"><span  >Html/text requis</span>&nbsp;</div>
</div> 
</div>
<div class="row mb-3">
    <div class="col">Requête SQL</div>
</div>
<div class="row mb-3">
<div class="col"><textarea rows="5"  class="input-text input-text-area"  v-model="hfobj.sql"/>
<div class="hint">Si pas de requête, les champs de la requête global seront utilisée pour remplacer les varibales.</div>
</div>
</div>
    </div>
     
</simple-modal-popup>

</template>

<script>


import { ref, onMounted, nextTick, computed, watch } from 'vue';
import { useStore } from 'vuex';
import Swal from 'sweetalert2';
import { HTMLTEMPLATE_LOAD_ELEMENTS, HTMLTEMPLATE_MODULE, HTMLTEMPLATE_SAVE_HF } from '../../store/types/types';
import { displayLoader, hideLoader } from '../../components/helpers/helpers';


export default {

    name: "HeaderFooterEditor",
    props:{
         modelValue:{
            type:Boolean,
            default:false,
            required:true
        },
        type:{
            type:String,
            required:true,
            default:'header'
        },
        obj:{
            type:Object,
            required:true,
            default:{

            }
        },
        loadElements:{
            type:Boolean,
            default:true
        }
      
    },

 emits: ['update:modelValue','onSaved'],
    
    setup(props,context) {

     
        const store = useStore()
        const modal_title=ref('');

        const showmodal=ref(false);
        const hfobj=ref({
            id:null,
            name:'',
            height:100,
            html:'',
            sql:'',
            type:props.type
        });



        watch(() => props.modelValue, (current_val, previous_val) => {
             showmodal.value=current_val;
            if(current_val==true){
                if(props.obj.id>0){
                    modal_title.value=`Modifier ${props.type=='footer'?'le pied de page':'l\'en-tête de page'}`;
                    hfobj.value.id=props.obj.id;
                    hfobj.value.name=props.obj.name;
                    hfobj.value.height=props.obj.height;
                    hfobj.value.html=props.obj.html;
                    hfobj.value.sql=props.obj.sql;
                     hfobj.value.type=props.type;

                }else{
                    modal_title.value=`Ajouter ${props.type=='footer'?'un pied de page':'une en-tête de page'}`;
                        hfobj.value.type=props.type;
                }
                console.log(props.obj.id);
            }else{
                modal_title.value='';
            }
        });

        onMounted(() => {
         
        });
    
        const save=()=>{
            closemodal();
            displayLoader('Sauvgarde en cours...');
            let saved=false;
            store.dispatch(`${HTMLTEMPLATE_MODULE}${HTMLTEMPLATE_SAVE_HF}`,hfobj.value).then(()=>{
                        if(props.loadElements){
                        saved=true;
                        displayLoader('Rechargement des éléments en cours...');
                        store.dispatch(`${HTMLTEMPLATE_MODULE}${HTMLTEMPLATE_LOAD_ELEMENTS}`).then(()=>{
                            hideLoader();
                        
                   
                });}else{
                    context.emit("onSaved",hfobj.value);
                }
            }).finally(()=>{
                  if(!saved)
                  hideLoader();
            });
        }

        const closemodal=()=>{
              context.emit("update:modelValue",false);
        }
        return {
            modal_title,
            save,
            closemodal,
            showmodal,
            hfobj

      
        }

  }

}
</script>

<style lang="scss" scoped>

.input-text-area{
  min-height: 120px;
  height: auto;
}
</style>
