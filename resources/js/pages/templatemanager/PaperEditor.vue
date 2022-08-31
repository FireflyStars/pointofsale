<template>
<div class="row">
                        <div class=" paper-editor col p-2">
                          <tab-pane :tabs="tabs" :current="`${template_id>0?'page_elements':'general_config'}`" class="almarai_700_normal">
                               <template v-slot:page_elements>
                                   <div class="pane py-4 px-2" >
                                        <div class="row mb-2"><h3 class="almarai_700_normal col font-14">Éléments du template</h3></div>
                                        <div class="row mb-2" v-if="template_id>0"><div class="col"><button class="btn btn-default btn-success" @click="createElementmodal('html')">Ajouter un élément html</button></div><div class="col"><button class="btn btn-default btn-info" @click="createElementmodal('table')">Ajouter une table de données</button></div></div>
                                        <div class="row mb-2" v-if="template_id>0"><div class="col"><button class="btn btn-default btn-secondary" @click="createElementmodal('address')">Ajouter un élément adresse</button></div><div class="col"><button class="btn btn-default btn-secondary" @click="addPageBreak()">Ajouter un saut de page</button></div></div>
                                        <div class="row mb-2" v-else><div class="col"><span class="hint">Fonctionnalité non disponible tant que le template n'est pas sauvegardé</span></div></div>
                                   </div>
                                  
                                </template>
                              <template v-slot:general_config>
                                <div class="pane py-4 px-2">
                            <div class="row "><h3 class="almarai_700_normal col font-14">Configuration générale</h3></div>
                              <div class="row "><div class="col-6"><input type="text" v-model.lazy="generalconfig.name" class="input-text" placeholder="Nom du template"/><div class="hint">Une fois sauvegardée, cette valeur ne sera pas modifiable.</div></div>
                              <div class="col-6">
                                <switch-btn v-model="generalconfig.qrcode" labelRight="Code QR"></switch-btn>
                              <div class="hint">Afficher un code QR LCDT-XXX dans la première page en haut à droite.</div>
                              </div>
                              </div>
                             <div class="row">
                                <div class="col">
                                <select-box
                                        v-model="generalconfig.type" 
                                        placeholder="Choisir un type de template" 
                                        :options="typetemplateOptions" 
                                        name="typetemplate" 
                                        label="Type de template" 
                                        hint="Une fois sauvegardée, cette valeur ne sera pas modifiable."
                            
                                    />

                                
                                </div>
                                <div class="col">
                                <select-box
                                        v-model="generalconfig.measuringunit" 
                                        placeholder="Choisir une unité de mesure" 
                                        :options="measuringunitOptions" 
                                        name="typetemplate" 
                                        label="Unité de mesure" 
                                        hint="Une fois sauvegardée, cette valeur ne sera pas modifiable."
                            
                                    />

                                
                                </div>
                            </div>
                            <div class="row">
                            <div class="col">Marge de page</div>
                            </div>
                             <div class="row">
                            <div class="col"><input type="number" step=".01" v-model="generalconfig.pagemargin.top" class="input-text" placeholder="Haut"/><div class="hint">Marge haut</div></div>
                            <div class="col"><input type="number" step=".01" v-model="generalconfig.pagemargin.right" class="input-text" placeholder="Droite"/><div class="hint">Marge droite</div></div>
                            <div class="col"><input type="number" step=".01" v-model="generalconfig.pagemargin.bottom" class="input-text" placeholder="Bas"/><div class="hint">Marge bas</div></div>
                            <div class="col"><input type="number" step=".01" v-model="generalconfig.pagemargin.left" class="input-text" placeholder="Gauche"/><div class="hint">Marge Gauche</div></div>
                            </div>
                            <div class="row">
                              <div class="col">

                                      <select-box
                                          v-model="generalconfig.htmltemplate_header_id" 
                                          placeholder="Choisir un en-tête de page" 
                                          :options="headerOptions" 
                                          name="header" 
                                          label="En-tête de page" 
                                          hint="La marge du haut de la page sera ignorée et la hauteur de l'en-tête sera utilisée comme marge du haut."
                                      />

                              </div>
                              <div class="col">


                                      <select-box
                                          v-model="generalconfig.htmltemplate_footer_id" 
                                          placeholder="Choisir un pied de page" 
                                          :options="footerOptions" 
                                          name="header" 
                                          label="Pied de page" 
                                          hint="La marge du bas de la page sera ignorée et la hauteur du pied de page sera utilisée comme marge du bas."
                                    
                                      />
                              </div>
                            </div>
                                     <transition enter-active-class="animate__animated animate__fadeIn"  leave-active-class="animate__animated animate__fadeOut">
                                <div v-if="generalconfig.type=='pdf'" class="postion-absolute">
                            <div class="row  w-100" >
                              <div class="col-12" style="padding-right:5px;">
                                  <label class="select-label body_medium" >Format du nom de téléchargement</label>
                                  <input type="text" v-model.lazy="generalconfig.pdf_filename_format" class="input-text  p-2" placeholder="Ex: DEVIS-{order_id}"/>
                                  <div class="hint">Spécifiez un format de nom de téléchargement pour le fichier pdf. Ex : Devis-{order_id} générera un fichier dont le nom sera Devis-XX.pdf.</div>
                                </div>
                            </div>
                                </div>
                                     </transition>
                             <transition enter-active-class="animate__animated animate__fadeIn"  leave-active-class="animate__animated animate__fadeOut">
                              <div v-if="generalconfig.type=='email'" class="postion-absolute">
                            <div class="row  w-100" >
                              <div class="col-12"  style="padding-right:5px;">
                                  <label class="select-label body_medium" >Email test</label>
                                  <input type="text" v-model.lazy="generalconfig.test_email" class="input-text  p-2" />
                                  <div class="hint">Spécifiez une adresse e-mail pour les tests.</div>
                                </div>
                            </div>
                             <div class="row  w-100"  >
                              <div class="col-12"  style="padding-right:5px;">
                                  <label class="select-label body_medium" >Sujet email</label>
                                  <input type="text" v-model.lazy="generalconfig.email_subject_format" class="input-text  p-2" placeholder="Ex: Confirmation commande {order_id}"/>
                                  <div class="hint">Spécifiez un format pour le sujet du mail.</div>
                                </div>
                            </div>
                              </div>
                             </transition>
                            <div class="row " >
                              <div class="col">
                                  <label class="select-label body_medium" >Requête SQL globale</label>
                                  <textarea v-model.lazy="generalconfig.global_sql" class="input-text input-text-area p-2" placeholder="Ex: SELECT `name` FROM `user` WHERE id={user_id}"></textarea>
                                </div>
                            </div>
                            <div class="row">
                              <div class="col">
                                  <label class="select-label body_medium" >Variables globales de test</label>
                                  <textarea v-model.lazy="generalconfig.global_test_vars" class="input-text input-text-area p-2" :class="{bgdanger:isJsonString(generalconfig.global_test_vars)==false&&generalconfig.global_test_vars.length>0}" :placeholder="testjsonplaceholder"></textarea>
                                  <div class="hint">Variables globales est un objet JSON qui permet de tester le template avec des donnees réel.</div>
                                       <transition enter-active-class="animate__animated animate__fadeIn" >
                                  <div class="text-danger" v-if="isJsonString(generalconfig.global_test_vars)==false&&generalconfig.global_test_vars.length>0">Invalid JSON</div>
                                       </transition>
                                </div>
                            </div>
                                  <div class="d-flex justify-content-end ">
                                  <button class="btn btn-default btn-dark" @click="save">Enregistrer</button>
                                </div>
                                </div>
                          
                                
                              </template>
                                  <template v-slot:global_css>
                                   <div class="pane py-4 px-2" >
                                        <div class="row mb-2"><h3 class="almarai_700_normal col font-14"><span class="alert alert-danger">Attention!  Editeur CSS globale</span></h3></div>
                                             <div class="row">
                                          <div class="col">
                                              <textarea v-model="globalcss" class="input-text input-text-area p-2"  rows="25"></textarea>
                                              <div class="hint">Veuillez noter que le CSS suivant est utilisé par tous les templates. PDF et EMAIL</div>
                                         
                                            </div>
                                        </div>
                                              <div class="d-flex justify-content-end ">
                                              <button class="btn btn-default btn-dark" @click="saveCss">Enregistrer CSS</button>
                                            </div>
                                   </div>
                                  
                                </template>
                             
                          </tab-pane>
                        </div>
</div>

   <simple-modal-popup v-model="showmodal_table" :title="modal_table_title" @modalconfirm="saveEl" @modalclose="closemodal" :style="{width:'1000px'}">
    <div class="container-fluid">
<div class="row mb-3">
    <div class="col">Tableau de données <button class="btn btn-default btn-link" @click="insertExample">Insérer un exemple</button></div>
</div>
<div class="row mb-3">
<div class="col"><textarea rows="30" v-model="element.data" class="input-text input-text-area" />
<div class="text-danger"><span  v-if="isJsonString(element.data)==false&&element.data.length>0">JSON invalide</span>&nbsp;</div>
</div> 
</div>
    </div>
     
</simple-modal-popup>

   <simple-modal-popup v-model="showmodal_html" :title="modal_html_title" @modalconfirm="saveEl" @modalclose="closemodal" :style="{width:'1000px'}">
    <div class="container-fluid">
<div class="row mb-3">
    <div class="col">Html</div>
</div>
<div class="row mb-3">
<div class="col"><textarea rows="25" v-model="element.data" class="input-text input-text-area" />
<div class="text-danger"><span  v-if="element.data.trim().length==0">Html/text requis</span>&nbsp;</div>
</div>
</div>
<div class="row mb-3">
    <div class="col">Requête SQL</div>
</div>
<div class="row mb-3">
<div class="col"><textarea rows="5" v-model="element.sql" class="input-text input-text-area" />
<div class="hint">Si pas de requête, les champs de la requête global seront utilisée pour remplacer les varibales.</div>
</div>
</div>
    </div>
     
</simple-modal-popup>


   <simple-modal-popup v-model="showmodal_address" :title="modal_address_title" @modalconfirm="saveEl" @modalclose="closemodal" :style="{width:'1000px'}">
    <div class="container-fluid">
<div class="row mb-3">
    <div class="col">Adresse</div>
</div>
<div class="row mb-3">
<div class="col"><textarea rows="20" v-model="element.data" class="input-text input-text-area" />
<div class="text-danger"><span  v-if="element.data.trim().length==0">Adresse Html/text requis</span>&nbsp;</div>
</div> 
</div>
<div class="row mb-3">
    <div class="col">Requête SQL</div>
</div>
<div class="row mb-3">
<div class="col"><textarea rows="5" v-model="element.sql" class="input-text input-text-area" />
<div class="hint">Si pas de requête, les champs de la requête global seront utilisée pour remplacer les varibales.</div>
</div>
</div>
    </div>
     
</simple-modal-popup>
</template>

<script>
import { computed, onMounted, ref, watch } from 'vue';
import Swal from 'sweetalert2';
import { useStore } from 'vuex';
import { HTMLTEMPLATE_GET_FOOTERLIST, HTMLTEMPLATE_GET_GLOBALCONF, HTMLTEMPLATE_GET_GLOBAL_CSS, HTMLTEMPLATE_GET_HEADERLIST, HTMLTEMPLATE_GET_ID, HTMLTEMPLATE_LOAD_ELEMENTS, HTMLTEMPLATE_LOAD_GLOBALCONF, HTMLTEMPLATE_MODULE, HTMLTEMPLATE_SAVE_ELEMENT, HTMLTEMPLATE_SAVE_GLOBALCONF, HTMLTEMPLATE_SAVE_GLOBAL_CSS, HTMLTEMPLATE_SET_GLOBAL_CSS, TOASTER_MODULE } from '../../store/types/types';
import {clearMsg, displayError, displayLoader, hideLoader} from '../../components/helpers/helpers';
import { useRouter } from 'vue-router';
import headerSectionVue from '../../components/reports/header-section.vue';
import SwitchBtn from '../../components/miscellaneous/SwitchBtn.vue';
export default {
  name: "PaperEditor",

  components:{SwitchBtn},
  emits:['onUpdateGeneralConfig','onSave'],
   setup(props,context) {

    const showmodal_table=ref(false);
        const showmodal_html=ref(false);
        const showmodal_address=ref(false);
        const globalcss=ref('');
        
    const store=useStore();
    const global_css=computed(()=>store.getters[`${HTMLTEMPLATE_MODULE}${HTMLTEMPLATE_GET_GLOBAL_CSS}`]);
        watch(global_css,(current_value,previous_value)=>{
          globalcss.value=current_value;
        });

        watch(globalcss,(current_value,previous_value)=>{
          store.commit(`${HTMLTEMPLATE_MODULE}${HTMLTEMPLATE_SET_GLOBAL_CSS}`,current_value);
        })
    const modal_table_title=ref('Ajouter un tableau de données');
    const modal_html_title=ref('Ajouter un élément html ou text');
     const modal_address_title=ref('Ajouter un élément adresse');
    const element=ref({
      name:'',
      data:'',
      type:'',//table,html,address,breakpage
      id:null,
      pos:null,
      sql:null
    });
    const router=useRouter();
    const tabs=ref({
            page_elements:'Éléments du template',
            general_config:'Configuration générale',
             global_css:'CSS globale',
           
        });


        
        const generalconfig=computed(()=>store.getters[`${HTMLTEMPLATE_MODULE}${HTMLTEMPLATE_GET_GLOBALCONF}`]);

        const template_id=computed(()=>store.getters[`${HTMLTEMPLATE_MODULE}${HTMLTEMPLATE_GET_ID}`]);
        onMounted(()=>{
          if(template_id.value>0){
          displayLoader('chargement du template en cours...');
          store.dispatch(`${HTMLTEMPLATE_MODULE}${HTMLTEMPLATE_LOAD_GLOBALCONF}`).finally(()=>{
            hideLoader();
          })
          }
        })

     const testjsonplaceholder=ref("Ex: \n{\n\t\"user_id\":1\n}");


      const headerOptions=ref([]);
      const headerList=computed(()=>store.getters[`${HTMLTEMPLATE_MODULE}${HTMLTEMPLATE_GET_HEADERLIST}`]);
      watch(headerList,(current_value,previous_value)=>{
        headerOptions.value=[];
        headerOptions.value.push({ value:'0', display: 'Aucun' });
        for(const i in headerList.value){
            headerOptions.value.push({
                value:headerList.value[i].id,
                display:headerList.value[i].name
            });
        }
      },{deep:true});
        const footerOptions=ref([]);
           const footerList=computed(()=>store.getters[`${HTMLTEMPLATE_MODULE}${HTMLTEMPLATE_GET_FOOTERLIST}`]);
      watch(footerList,(current_value,previous_value)=>{
        footerOptions.value=[];
        footerOptions.value.push({ value:'0', display: 'Aucun' });
        for(const i in footerList.value){
            footerOptions.value.push({
                value:footerList.value[i].id,
                display:footerList.value[i].name
            });
        }
      },{deep:true});

          const typetemplateOptions=ref([
            { value:'email', display: 'Email' },
            { value:'pdf', display: 'PDF' }
        ]);

        const measuringunitOptions=ref([
            { value:'pt', display: 'pt' },
            { value:'px', display: 'px' },
            { value:'mm', display: 'mm' },
            { value:'cm', display: 'cm' }
        ]);
        watch(()=>generalconfig.value,(current_value,previous_value)=>{
          context.emit('onUpdateGeneralConfig',generalconfig.value);
    
        },{deep:true});
        
        const isJsonString=(str)=>{
            try {
                JSON.parse(str);
            } catch (e) {
                return false;
            }
            return true;
        }
        const createElementmodal=(type)=>{
           element.value.data='';
           element.value.name='',
           element.value.pos=null;
           element.value.sql=null;
           element.value.id=null;
          if(type=='table'){
            element.value.type='table';
          showmodal_table.value=true;
           
          }
          if(type=='html'){
            element.value.type='html';
          showmodal_html.value=true;
           
          }
               if(type=='address'){
            element.value.type='address';
          showmodal_address.value=true;
           
          }
        }
        const addPageBreak=()=>{
          console.log('bnag');
           element.value.data='';
           element.value.name='',
           element.value.pos=null;
           element.value.sql=null;
           element.value.id=null;
            element.value.type='pagebreak';
            displayLoader('Ajout saut de page en cours...')
              store.dispatch(`${HTMLTEMPLATE_MODULE}${HTMLTEMPLATE_SAVE_ELEMENT}`,element.value).finally(()=>{
                hideLoader();
             
               loadPageRender();
              });
        }
        const saveEl=()=>{
      
          if(element.value.type=='table'&&isJsonString(element.value.data)){
              displayLoader('Sauvegarde du tableau en cours...')
              store.dispatch(`${HTMLTEMPLATE_MODULE}${HTMLTEMPLATE_SAVE_ELEMENT}`,element.value).finally(()=>{
                hideLoader();
               showmodal_table.value=false;
               loadPageRender();
              });
               
          }
                   if(element.value.type=='html'&&element.value.data.trim().length>0){
              displayLoader('Sauvegarde du html en cours...')
              store.dispatch(`${HTMLTEMPLATE_MODULE}${HTMLTEMPLATE_SAVE_ELEMENT}`,element.value).finally(()=>{
                hideLoader();
               showmodal_html.value=false;
               loadPageRender();
              });
              }
                if(element.value.type=='address'&&element.value.data.trim().length>0){
              displayLoader('Sauvegarde du address en cours...')
              store.dispatch(`${HTMLTEMPLATE_MODULE}${HTMLTEMPLATE_SAVE_ELEMENT}`,element.value).finally(()=>{
                hideLoader();
               showmodal_address.value=false;
               loadPageRender();
              });
                }
          
        }
        const openElementModal=(e)=>{
           element.value.data=e.data;
           element.value.name=e.name,
           element.value.pos=e.position;
           element.value.sql=e.sql;
           element.value.id=e.id;
          if(e.type=='table'){
            element.value.type='table';
          showmodal_table.value=true;
           
          }
          if(e.type=='html'){
            element.value.type='html';
          showmodal_html.value=true;
           
          }
               if(e.type=='address'){
            element.value.type='address';
          showmodal_address.value=true;
           
          }
        }
        const loadPageRender=()=>{
            store.dispatch(`${HTMLTEMPLATE_MODULE}${HTMLTEMPLATE_LOAD_ELEMENTS}`);
        }
        const closemodal=()=>{

        }
        const save=()=>{
           clearMsg();
           let valid=true;
          if(generalconfig.value.name.trim()==''){
              displayError('Le nom du template est requis.');
              valid=false;
          }
          if(generalconfig.value.type.trim()==''){
              displayError('Le type du template est requis.');
                 valid=false;
          }
          if(valid){
             if(template_id.value>0){
                displayLoader('Mise à jour template en cours');
             }else{
                displayLoader('Création d\'un nouveau template en cours');
             }
            store.dispatch(`${HTMLTEMPLATE_MODULE}${HTMLTEMPLATE_SAVE_GLOBALCONF}`,generalconfig.value).then(response=>{
                if(template_id.value>0){
                 
                      store.dispatch(`${HTMLTEMPLATE_MODULE}${HTMLTEMPLATE_LOAD_GLOBALCONF}`);
                      loadPageRender();
                }else{
                  router.push({name:'HtmlTemplateEdit',params:{id:response.data.id}});
                }

                hideLoader();
            }).finally(()=>{
               hideLoader();
            });
          }
        }
        const insertExample=()=>{
          element.value.data=generalconfig.value.example;
        }
        const saveCss=()=>{
          store.dispatch(`${HTMLTEMPLATE_MODULE}${HTMLTEMPLATE_SAVE_GLOBAL_CSS}`,globalcss.value);
        }
      return {
            tabs,
            generalconfig,
            headerOptions,
            footerOptions,
            typetemplateOptions,
            measuringunitOptions,
            testjsonplaceholder,
            isJsonString,
            showmodal_table,
            saveEl,
            modal_table_title,
            element,
            template_id,
            closemodal,
            save,
            insertExample,
            createElementmodal,
            showmodal_html,
            modal_html_title,
            addPageBreak,
            showmodal_address,
            modal_address_title,
            openElementModal,
            globalcss,
            saveCss
      }
   },

};
</script>

<style lang="scss" scoped>
.paper-editor{
   width: 600px;
   
    background: #DDD;
    box-shadow:  0px 0px 6px rgb(0 0 0 / 25%);
    height: calc(100vh - 66px);
    position:fixed;
    right: 0;
    top:66px;
    overflow-y: auto;
    overflow-x: hidden;
}
.input-text-area{
  min-height: 120px;
  height: auto;
}

.bgdanger{
      --bs-bg-opacity: 1;
    background-color: rgba(var(--bs-danger-rgb), var(--bs-bg-opacity)) !important;
}
.hint {
    margin-bottom: 20px;
    font-size: 10px;
    color: #757575;
    font-weight: 300;
}
.pane{
  background: #FFF;
}
</style>