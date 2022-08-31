<template>

      
            <div class="container-fluid h-100 bg-color">
                <main-header />

                <div class="row d-flex align-content-stretch align-items-stretch flex-row hmax main-view-wrap" style="z-index:100" >
                    
                    <side-bar />
            <transition enter-active-class="animate__animated animate__fadeIn" >
                    <div class="col main-view container" v-if="showcontainer">
                            <page-title icon="pdf" :name="page_title" class="almarai_extrabold_normal_normal" style="width: 45px; height: 45px; " />
<div class="row" v-if="generalconfig.name.trim()!=''"><span class="col mb-2 font-16 almarai_700_normal" >{{generalconfig.name}}</span></div>
<div class="row align-items-center">
    <div class="col-2 mb-2" ><input type="range"  v-model.lazy="zoom" min="50" max="150" step="1"> {{zoom}}%</div>
    <div class="col-2 mb-2" v-if="template_id>0"><switch-btn v-model="rendersql" labelLeft="Afficher données réelles"></switch-btn></div>
    <div class="col-1 mb-2"  v-if="template_id>0&&generalconfig.type=='pdf'"><button class="btn btn-default btn-animate-side btn-success" @click="testPdf()">Test PDF</button></div>
    <div class="col-1 mb-2" v-if="template_id>0&&generalconfig.type=='email'"><button class="btn btn-default btn-animate-side btn-success" @click="testEmail()">Test Email</button></div>
</div>
<div class="row align-items-center"></div>
<div v-html="style()"></div>
                    <div class="row m-0 ml-5 mr-5 mb-5">
                                <div class=" paper" :style="`transform:scale(${zoom/100});padding-top:${currentHeader!=null?currentHeader.height:papermargin.top}${papermargin.unit};padding-right:${papermargin.right}${papermargin.unit};padding-bottom:${currentFooter!=null?currentFooter.height:papermargin.bottom}${papermargin.unit};padding-left:${papermargin.left}${papermargin.unit};`" >
                                <div class="margin-line" v-if="papermargin.top>0&&currentHeader==null" :style="`border-bottom: 1px dashed #3c3c3c;width: 100%;left:0;top:0;height:${papermargin.top}${papermargin.unit};`"></div>
                                <div class="header-el" v-if="currentHeader!=null"  :style="`border-bottom: 1px dashed #3c3c3c;width: 100%;left:0;top:0;height:${currentHeader!=null?currentHeader.height:0}${papermargin.unit};padding-right:${papermargin.right}${papermargin.unit};padding-left:${papermargin.left}${papermargin.unit};`">
                                     <div class="el_actions" :style="`left:${papermargin.left}${papermargin.unit};`">
                                        <span @click="openHeaderFooterEditor('header')">&#128393;</span>
                                    </div>
                                <div v-html="currentHeader.rendered_data"></div>
                                </div> 

                                <div class="margin-line" v-if="papermargin.bottom>0&&currentFooter==null" :style="`border-top: 1px dashed #3c3c3c;width: 100%;left:0;bottom:0;height:${papermargin.bottom}${papermargin.unit};`"></div>
                                
                                <div class="footer-el" v-if="currentFooter!=null"  :style="`border-top: 1px dashed #3c3c3c;width: 100%;left:0;bottom:0;height:${currentFooter!=null?currentFooter.height:0}${papermargin.unit};padding-right:${papermargin.right}${papermargin.unit};padding-left:${papermargin.left}${papermargin.unit};`">
                                     <div class="el_actions" :style="`left:${papermargin.left}${papermargin.unit};`">
                                        <span @click="openHeaderFooterEditor('footer')">&#128393;</span>
                                    </div>
                                <div v-html="currentFooter.rendered_data"></div>
                                </div> 
                                
                                <div class="margin-line" v-if="papermargin.right>0" :style="`border-left: 1px dashed #3c3c3c;height: 100%;right:0;top:0;width:${papermargin.right}${papermargin.unit};`"></div>
                                <div class="margin-line" v-if="papermargin.left>0" :style="`border-right: 1px dashed #3c3c3c;height: 100%;left:0;top:0;width:${papermargin.left}${papermargin.unit};`"></div>
                                <div class="position-relative">
                                    <div v-html="generalconfig.qrcode_rendered"></div>
                                <div v-for="element,index in elements" :key="index" class="element" :class="elementClass(element)" :data-name="`${element.type.toLowerCase()} ${element.id}`">
                                    <div class="el_actions">
                                        <span v-if="element.type!='address'" @click="openRepositioningModal(element)">&#10303;</span>
                                        <span v-if="element.type!='pagebreak'" @click="openElementModal(element)">&#128393;</span>
                                        <span @click="removeElement(element)">&#10008;</span>
                                    </div>
                                  
                                    <div v-if="element.type=='html'" v-html="element.rendered_data"></div>
                                    <div class="addressblock" v-if="element.type=='address'" v-html="element.rendered_data"></div>
                                     <div v-if="element.type=='table'" v-html="element.rendered_data"></div>
                                </div>
                                </div>
                                </div>

                                
                    </div>
                    <paper-editor @onUpdateGeneralConfig="updatedConfig" ref="papereditor"/>
        

                    </div>
            </transition>
                </div>
            </div>
    <simple-modal-popup v-model="showmodal_reposition" title="Repositionner un élément" @modalconfirm="repositionEl" >
    <div class="container-fluid">
<div class="row mb-3">
    <div class="col">Repositionner l'élément <b class="text-capitalize">{{reposEl.el.type}} {{reposEl.el.id}}</b></div>
</div>
<div class="row mb-3">
<div class="col-4">
     <select-box
                                          v-model="reposEl.pos" 
                                          placeholder="Choisir" 
                                          :options="pos" 
                                          name="pos" 
                                         
                                    
                                      />

</div> 
</div>
<div class="row mb-3">
    <div class="row mb-3">
    <div class="col">l'élément</div>
</div>
<div class="col-4">
     <select-box
                                          v-model="reposEl.sibling_id" 
                                          placeholder="Choisir" 
                                          :options="siblings" 
                                          name="sibling_id" 
                                    
                                      />

</div> 
</div>
    </div>
     
</simple-modal-popup>

<header-footer-editor :obj="hfObj" v-model="showHeaderFooterEditor" :type="headerFooterEditorType"/>
</template>

<script>

import MainHeader from '../../components/layout/MainHeader.vue';
import SideBar from '../../components/layout/SideBar.vue';

import { ref, onMounted, nextTick, computed, watch } from 'vue';
import { useStore } from 'vuex';
import PaperEditor from './PaperEditor.vue';
import { useRoute } from 'vue-router';
import { HTMLTEMPLATE_GET_CURRENTFOOTER, HTMLTEMPLATE_GET_CURRENTHEADER, HTMLTEMPLATE_GET_ELEMENTS, HTMLTEMPLATE_GET_GLOBALCONF, HTMLTEMPLATE_GET_GLOBAL_CSS, HTMLTEMPLATE_GET_ID, HTMLTEMPLATE_LOAD_ELEMENTS, HTMLTEMPLATE_MODULE, HTMLTEMPLATE_REMOVE_ELEMENT, HTMLTEMPLATE_REPOSITION_ELEMENT, HTMLTEMPLATE_SET_ID, HTMLTEMPLATE_SET_RENDERSQL, HTMLTEMPLATE_TEST_EMAIL } from '../../store/types/types';
import SwitchBtn from '../../components/miscellaneous/SwitchBtn.vue'
import { displayError, displayLoader, hideLoader } from '../../components/helpers/helpers';
import Swal from 'sweetalert2';
import HeaderFooterEditor from './HeaderFooterEditor.vue';
export default {

    name: "HtmlTemplate",

    components: {
      MainHeader,
      SideBar,
      PaperEditor,
    
        SwitchBtn,
        HeaderFooterEditor
    },

    setup() {
        const papereditor=ref(null);
        const showmodal_reposition=ref(false);
        const zoom=ref(100);
        const store = useStore();
        const styles=ref('');
        const rendersql=ref(false);
        const papermargin=ref({
            top:10,
            right:10,
            bottom:10,
            left:10,
            unit:'pt'
        });

        
        const pos=ref([
            { value:'before', display: 'Avant' },
            { value:'after', display: 'Après' }
           
        ]);
        const siblings=ref([]);

  const generalconfig=computed(()=>store.getters[`${HTMLTEMPLATE_MODULE}${HTMLTEMPLATE_GET_GLOBALCONF}`]);
  const currentHeader=computed(()=>store.getters[`${HTMLTEMPLATE_MODULE}${HTMLTEMPLATE_GET_CURRENTHEADER}`]);
  const currentFooter=computed(()=>store.getters[`${HTMLTEMPLATE_MODULE}${HTMLTEMPLATE_GET_CURRENTFOOTER}`]); 
  const template_id=computed(()=>store.getters[`${HTMLTEMPLATE_MODULE}${HTMLTEMPLATE_GET_ID}`]);   
  const global_css=computed(()=>store.getters[`${HTMLTEMPLATE_MODULE}${HTMLTEMPLATE_GET_GLOBAL_CSS }`]);

        const showcontainer = ref(false);
        const route=useRoute();
        const page_title=ref('Création template');
        onMounted(() => {
 
            store.commit(`${HTMLTEMPLATE_MODULE}${HTMLTEMPLATE_SET_ID}`,route.params.id);
            nextTick(() => {
                showcontainer.value=true
            });
       
            if(route.params.id>0){
            store.dispatch(`${HTMLTEMPLATE_MODULE}${HTMLTEMPLATE_LOAD_ELEMENTS}`);
            page_title.value="Edit template"
            }

        })
        const elements=computed(()=>store.getters[`${HTMLTEMPLATE_MODULE}${HTMLTEMPLATE_GET_ELEMENTS}`]);

        const updatedConfig=(config)=>{
            
            updatePaperMargin(config.pagemargin.top,config.pagemargin.right,config.pagemargin.bottom,config.pagemargin.left,config.measuringunit);
        }

        const updatePaperMargin=(top,right,bottom,left,unit)=>{
                papermargin.value.top=top;
                papermargin.value.right=right;
                papermargin.value.bottom=bottom;
                papermargin.value.left=left;
                papermargin.value.unit=unit;
        }
        const openElementModal=(element)=>{
                papereditor.value.openElementModal(element);
        }
        const removeElement=(element)=>{
                Swal.fire({
          title: 'Etes-vous sûr de vouloir supprimer cet élément?',
          text: "Vous ne pourrez pas revenir en arrière !",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#42A71E',
          // cancelButtonColor: '#E8581B',
          cancelButtonColor: 'var(--lcdtOrange)',
          cancelButtonText: 'Annuler',        
          confirmButtonText: `Oui, s'il vous plaît.`
        }).then((result) => {
          if (result.isConfirmed) {
                displayLoader('Suppression de l\'élément en cours...');
                store.dispatch(`${HTMLTEMPLATE_MODULE}${HTMLTEMPLATE_REMOVE_ELEMENT}`,element).then(response=>{
                    if(response.data.delete=='ok')
                Swal.fire(
                        'Supprimé!',
                        'L\'élément a été supprimé',
                        'success'
                        )    
            }).finally(()=>{
                hideLoader();
            });
          
           
          }
        }) 
          
        }
        const reposEl=ref({
            el:null,
            pos:'after',
            sibling_id:null
        });
        const openRepositioningModal=(e)=>{
          
             reposEl.value.el=e;
             reposEl.value.pos='after';
             reposEl.value.sibling_id=null;
                    siblings.value=[];
                for(const i in elements.value){
                    if(elements.value[i].id!=reposEl.value.el.id&&elements.value[i].type!='address')
                    siblings.value.push( { value:elements.value[i].id, display: `${elements.value[i].type} ${elements.value[i].id}` });
                }

             showmodal_reposition.value=true;
        }
        const repositionEl=()=>{
            if(reposEl.value.sibling_id==null){
            displayError(`Choisissez l\'élément ${reposEl.value.pos=='before'?'avant':'après'} lequel vous voulez positionner l\'élément ${reposEl.value.el.type} ${reposEl.value.el.id}.`);
            }else{
                   displayLoader('Réarrangement des éléments...');
            store.dispatch(`${HTMLTEMPLATE_MODULE}${HTMLTEMPLATE_REPOSITION_ELEMENT}`,reposEl.value).then(response=>{
                if(response.data.reposition=='ok'){
                    hideLoader();
                    displayLoader('Rechargement des éléments...');
                     store.dispatch(`${HTMLTEMPLATE_MODULE}${HTMLTEMPLATE_LOAD_ELEMENTS}`).finally(()=>{
                        hideLoader();
                     });
                }
            }).finally(()=>{
                   showmodal_reposition.value=false;
            });
            }
        }

        watch(rendersql,(current_val,previous_val)=>{
            store.commit(`${HTMLTEMPLATE_MODULE}${HTMLTEMPLATE_SET_RENDERSQL}`,current_val);
            if(current_val==true){
                displayLoader('Chargement des éléments en cours. Cela peut prendre un moment, veuillez patienter...');
            }else{
                displayLoader('Chargement des éléments en cours...');
            }
                     store.dispatch(`${HTMLTEMPLATE_MODULE}${HTMLTEMPLATE_LOAD_ELEMENTS}`).finally(()=>{
                        hideLoader();
                     });
        })

        const elementClass=(el)=>{  
                let classes=el.type=='table'?'datatable':el.type;
                if(rendersql.value){
                    classes=`${classes} rendered`;
                }
                return classes;
        }
        const showHeaderFooterEditor=ref(false);
        const headerFooterEditorType=ref('header');
        const hfObj=ref({});
        const openHeaderFooterEditor=(type)=>{

            if(type=='header')
            hfObj.value=currentHeader.value;
             if(type=='footer')
            hfObj.value=currentFooter.value;


            showHeaderFooterEditor.value=true;
            headerFooterEditorType.value=type;
   
        }
        const testPdf=()=>{
            window.location=`/htmltemplate-generate-pdf-test/${template_id.value}`;
        }
        const testEmail=()=>{
            displayLoader('Envoi email de test en cours...');
            store.dispatch(`${HTMLTEMPLATE_MODULE}${HTMLTEMPLATE_TEST_EMAIL}`).then(()=>{
                hideLoader();
            });
        }

        const style=()=>{
            return `<style>${global_css.value}</style>`;
        }
        return {

            showcontainer,
            zoom,
            styles,
            updatedConfig,
            papermargin,
            generalconfig,
            elements,
            rendersql,
            page_title,
            openElementModal,
            papereditor,
            removeElement,
            openRepositioningModal,
            showmodal_reposition,
            repositionEl,
            reposEl,
            pos,
            siblings,
            elementClass,
            currentHeader,
            currentFooter,
            openHeaderFooterEditor,
            headerFooterEditorType,
            showHeaderFooterEditor,
            hfObj,
            template_id,
            testPdf,
            testEmail,
            global_css,
            style
        }

  }

}
</script>

<style lang="scss" scoped>
.paper{
    width: 21cm;
    min-height: 29.7cm;
    background: white;
    box-shadow:  0px 0px 6px rgb(0 0 0 / 25%);
    transform-origin: top left;
    transition: all 0.2s ease-in-out;
    position: relative;
}

.margin-line{

    position: absolute;
    background: #eee;
    opacity: 0.5;
    transition: all 0.2s ease-in-out;
}
.header-el,.footer-el{
    position:absolute;
     transition: all 0.2s ease-in-out;
}
.element{
    border: 1px dashed var(--lcdtOrange);
    position: relative;
    min-height: 30px;
    &::before{
        content:attr(data-name);
        background: var(--lcdtOrange);
        color:#FFF;
        font-size: 11px;
        padding:5px 3px;
        border-top-left-radius: 5px;
        border-top-right-radius: 5px;
        font-family: "Mulish Regular";
        top:-21px;
        position: absolute;
        left: 0;
        text-transform: capitalize;
        line-height: 11px;
        transition: opacity  500ms ease-in-out;
    }

    margin:30px 1px 0 1px;
    transition: margin  500ms ease-in-out;
    &.pagebreak{
        border-color: #6c757d;
         &::before{
            background:  #6c757d;
         }
    }
    &.html{
        border-color: #198754;
         &::before{
            background:  #198754;
         }
    }
    &.address{
        margin:0px;
        border-color: blueviolet;
        position: absolute;
        right: 14px;
        top: 126px;
         &::before{
            background:  blueviolet;
         }
    }

    &.rendered{
        border:none;
        margin:0;
        
          &::before{
            opacity: 0;
          }

          & .addressblock{
                background-color: rgba(138, 43, 226, $alpha: 0);
          }
    }
}
.el_actions{
    display: flex;
    width: auto;
    background: #DDD;
    justify-content: space-evenly;
    & span{
        font-size: 18px;
        cursor: pointer;
        padding:0 10px;
         background: #DDD;
         transition: 300ms background ease-in-out;
        &:hover{
            background: var(--lcdtOrange);
            color:#FFF;
        }
    }
    position: absolute;
    top:0;
    left: 0;
    opacity: 0;
    transition: opacity ease-in-out 0.2s;
}
.element:hover,.header-el:hover,.footer-el:hover{
    & .el_actions{
        opacity: 1;
    }
}

.addressblock{
    transition: all ease-in-out 500ms;
    background: rgba(138, 43, 226, $alpha: 0.6);
    height: 78px;
    width: 275px;
    display: block;
}
</style>
<style lang="scss" >
@import "../../../../resources/css/htmltemplate.scss"; //Here i add extra "./"(current directory)
</style>