
<template>
    <router-view v-slot="{ Component }">
      
            <div class="container-fluid h-100 bg-color">
                <main-header />

                <div class="row d-flex align-content-stretch align-items-stretch flex-row hmax main-view-wrap" style="z-index:100" >
                    
                    <side-bar />
            <transition enter-active-class="animate__animated animate__fadeIn" >
                    <div class="col main-view container" v-if="showcontainer">
                            <page-title icon="pdf" name="Template Email et PDF" class="almarai_extrabold_normal_normal" style="width: 45px; height: 45px; " />

            <div class="row m-0 ml-5 mr-5">
                        <div class="col">
                            <tab-pane :tabs="tabs" current='template' class="almarai_700_normal">
                                <template v-slot:template>
                                   <item-list-table :table_def="all_templates" >
                                     <template v-slot:rowaction="{row}">
                                            <item-list-row-actions :item="row" :edit="true" :delete="true" :duplicate="true" @onRowEditClick="editRow" @onRowDeleteClick="deleteRow" @onRowDuplicateClick="duplicateRow"></item-list-row-actions>
                                        </template>
                                   </item-list-table>
                                </template>
                                <template v-slot:header>
                                    <button class="btn  quicklink body_medium newbtn" @click="addnew('header')">Ajouter une en-tête de page</button>
                                     <item-list-table :table_def="template_headers " @onRowClicked="rowHeaderEdit">
                                          <template v-slot:rowaction="{row}">
                                                <item-list-row-actions :item="row" :edit="true" :delete="true" :duplicate="true" @onRowEditClick="rowHeaderEdit({row:row})" @onRowDeleteClick="deleteHeaderRow" @onRowDuplicateClick="duplicateHeaderRow"></item-list-row-actions>
                                          </template>
                                   </item-list-table>
                                </template>
                               <template v-slot:footer>
                                  <button class="btn  quicklink body_medium newbtn" @click="addnew('footer')">Ajouter un pied de page</button>
                                          <item-list-table :table_def="template_footers " @onRowClicked="rowFooterEdit">
                                             <template v-slot:rowaction="{row}">
                                                <item-list-row-actions :item="row" :edit="true" :delete="true" :duplicate="true" @onRowEditClick="rowFooterEdit({row:row})" @onRowDeleteClick="deleteFooterRow" @onRowDuplicateClick="duplicateFooterRow"></item-list-row-actions>
                                          </template>
                                   </item-list-table>
                                </template>
                           
                                
                            </tab-pane>
                        </div>
                         
            </div>
            
                        <component :is="Component" />

                    </div>
            </transition>
                </div>
            </div>
 <header-footer-editor :obj="hfObj" v-model="showHeaderFooterEditor" :type="headerFooterEditorType" :loadElements="false" @onSaved="updateListRow"/>
    </router-view>
</template>

<script>

import MainHeader from '../../components/layout/MainHeader.vue';
import SideBar from '../../components/layout/SideBar.vue';
import ItemListTable from '../../components/miscellaneous/ItemListTable/ItemListTable.vue';

import { ref, onMounted, nextTick, computed } from 'vue';
import { useStore } from 'vuex';
import {  HTMLTEMPLATEFOOTERLIST_LIST_DEF, HTMLTEMPLATEFOOTERLIST_MODULE, HTMLTEMPLATEHEADERLIST_LIST_DEF, HTMLTEMPLATEHEADERLIST_MODULE, HTMLTEMPLATELIST_DELETE_ROW, HTMLTEMPLATELIST_DUPLICATE_ROW, HTMLTEMPLATELIST_LIST_DEF, HTMLTEMPLATELIST_MODULE, ITEM_LIST_MODULE, ITEM_LIST_REMOVE_ROW, ITEM_LIST_TABLE_RELOAD, ITEM_LIST_UPDATE_ROW } from '../../store/types/types';
import HeaderFooterEditor from './HeaderFooterEditor.vue'; 
import ItemListRowActions from '../../components/miscellaneous/ItemListTable/ItemListRowActions.vue'
import { useRouter } from 'vue-router';
import Swal from 'sweetalert2';
import { displayLoader, hideLoader } from '../../components/helpers/helpers';
export default {

    name: "GestionPDF",

    components: {
      MainHeader,
      SideBar,
      ItemListTable,
      ItemListRowActions,
      HeaderFooterEditor
    },

    setup() {

        const tabs = ref({})
        const store = useStore()
        const router=useRouter()

        const all_templates = computed(()=>store.getters[`${HTMLTEMPLATELIST_MODULE}${HTMLTEMPLATELIST_LIST_DEF}`]);
        const template_headers = computed(()=>store.getters[`${HTMLTEMPLATEHEADERLIST_MODULE}${HTMLTEMPLATEHEADERLIST_LIST_DEF}`]);
        const template_footers = computed(()=>store.getters[`${HTMLTEMPLATEFOOTERLIST_MODULE}${HTMLTEMPLATEFOOTERLIST_LIST_DEF}`]);
        
        const showHeaderFooterEditor=ref(false);
        const headerFooterEditorType=ref('header');
        const hfObj=ref({});
        tabs.value= {
            template:'Template',
            header:'En-tête de page',
            footer:'Pied de page',
       
        };

        const showcontainer = ref(false)

        onMounted(() => {
            nextTick(() => {
                showcontainer.value=true
            })

        })
        const rowHeaderEdit=(row)=>{
        
            headerFooterEditorType.value='header';
            showHeaderFooterEditor.value=true;
            hfObj.value=row.row;
        }
        
         const rowFooterEdit=(row)=>{
            headerFooterEditorType.value='footer';
            showHeaderFooterEditor.value=true;
            hfObj.value=row.row;
        }
        const updateListRow=(obj)=>{
          if(obj.id>0){
                store.commit(`${ITEM_LIST_MODULE}${ITEM_LIST_UPDATE_ROW}`,{id:'id',idValue:obj.id,colName:'html',colValue:obj.html});
                store.commit(`${ITEM_LIST_MODULE}${ITEM_LIST_UPDATE_ROW}`,{id:'id',idValue:obj.id,colName:'name',colValue:obj.name});
                store.commit(`${ITEM_LIST_MODULE}${ITEM_LIST_UPDATE_ROW}`,{id:'id',idValue:obj.id,colName:'height',colValue:obj.height});
                store.commit(`${ITEM_LIST_MODULE}${ITEM_LIST_UPDATE_ROW}`,{id:'id',idValue:obj.id,colName:'sql',colValue:obj.sql});
          }else{
            store.dispatch(`${ITEM_LIST_MODULE}${ITEM_LIST_TABLE_RELOAD}`,{fullreload:true});
          }
        }
        const addnew=(type)=>{
            const hf={
                id:null,
                name:'',
                sql:'',
                html:'',
                height:100
            }
              headerFooterEditorType.value=type;
            showHeaderFooterEditor.value=true;
            hfObj.value=hf;
        }
        const editRow=(row)=>{
            router.push({name: 'HtmlTemplateEdit', params: { id: row.id }});
        }
        const deleteRow= async(row)=>{
             
            try {
                
                const result = await Swal.fire({
                    title: 'Veuillez confirmer!',
                    html: `Voulez vous supprimer le template <b>"${row.name}"</b>?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#42A71E',
                    cancelButtonColor: 'var(--lcdtOrange)',
                    cancelButtonText: 'Annuler',
                    confirmButtonText: `Oui, s'il vous plaît.`
                })

                if(result.isConfirmed) {

                    displayLoader('Suppression template en cours...')
                    await store.dispatch(`${HTMLTEMPLATELIST_MODULE}${HTMLTEMPLATELIST_DELETE_ROW}`, { id: row.id, type: 'template'}).then(()=>{
                        store.commit(`${ITEM_LIST_MODULE}${ITEM_LIST_REMOVE_ROW}`,{id:'id',idValue:row.id});
                    });

                }

                
            }
        
            catch(e) {
                displayError('Une erreur est survenue')
                throw e
            }
            
            finally {
               hideLoader();
            }
        }

        const duplicateRow=async(row)=>{
           try {
                
            const result = await Swal.fire({
                title: 'Dupliquer le template',
                input: 'text',
                inputLabel: 'Nom du template',
                inputValue: `${row.name}_copie`,
                confirmButtonColor: '#42A71E',
                cancelButtonColor: 'var(--lcdtOrange)',
                showCancelButton: true,
                cancelButtonText: 'Annuler',
                confirmButtonText: `Dupliquer`,
                inputValidator: (value) => {
                    if (!value) {
                    return 'Veuillez préciser le nom du template!'
                    }
                    if (value==row.name) {
                    return 'Veuillez choisir un autre nom de template!'
                    }
                }
                })

    

                if(result.isConfirmed) {
            

                    displayLoader('Copie du template en cours...')
                     await store.dispatch(`${HTMLTEMPLATELIST_MODULE}${HTMLTEMPLATELIST_DUPLICATE_ROW}`, { id: row.id, type: 'template',name:result.value}).then(response=>{
                         router.push({name: 'HtmlTemplateEdit', params: { id: response.data.id }});
                     });

                }

                
            }
        
            catch(e) {
                displayError('Une erreur est survenue')
             
                throw e
            }
            
            finally {
               hideLoader();
            } 
        }

          const deleteHeaderRow= async(row)=>{
             
            try {
                
                const result = await Swal.fire({
                    title: 'Veuillez confirmer!',
                    html: `Voulez vous supprimer  l\'en-tête <b>"${row.name}"</b>?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#42A71E',
                    cancelButtonColor: 'var(--lcdtOrange)',
                    cancelButtonText: 'Annuler',
                    confirmButtonText: `Oui, s'il vous plaît.`
                })

                if(result.isConfirmed) {

                    displayLoader('Suppression de l\'en-tête en cours...')
                    await store.dispatch(`${HTMLTEMPLATELIST_MODULE}${HTMLTEMPLATELIST_DELETE_ROW}`, { id: row.id, type: 'header'}).then(()=>{
                        store.commit(`${ITEM_LIST_MODULE}${ITEM_LIST_REMOVE_ROW}`,{id:'id',idValue:row.id});
                    });

                }

                
            }
        
            catch(e) {
                displayError('Une erreur est survenue')
                throw e
            }
            
            finally {
               hideLoader();
            }
        }

        const duplicateHeaderRow=async(row)=>{
           try {
                
            const result = await Swal.fire({
                title: 'Dupliquer  l\'en-tête',
                input: 'text',
                inputLabel: 'Nom de l\'en-tête',
                inputValue: `${row.name}_copie`,
                confirmButtonColor: '#42A71E',
                cancelButtonColor: 'var(--lcdtOrange)',
                showCancelButton: true,
                cancelButtonText: 'Annuler',
                confirmButtonText: `Dupliquer`,
                inputValidator: (value) => {
                    if (!value) {
                    return 'Veuillez préciser le nom de l\'en-tête!'
                    }
                    if (value==row.name) {
                    return 'Veuillez choisir un autre nom pour l\'en-tête!'
                    }
                }
                })

    

                if(result.isConfirmed) {
            

                    displayLoader('Copie de l\'en-tête de page en cours...')
                     await store.dispatch(`${HTMLTEMPLATELIST_MODULE}${HTMLTEMPLATELIST_DUPLICATE_ROW}`, { id: row.id, type: 'header',name:result.value}).then(response=>{
                          displayLoader('Chargement en cours..')
                       store.dispatch(`${ITEM_LIST_MODULE}${ITEM_LIST_TABLE_RELOAD}`,{fullreload:true});
                     });

                }

                
            }
        
            catch(e) {
                displayError('Une erreur est survenue')
             
                throw e
            }
            
            finally {
               hideLoader();
            } 
        }

          const deleteFooterRow= async(row)=>{
             
            try {
                
                const result = await Swal.fire({
                    title: 'Veuillez confirmer!',
                    html: `Voulez vous supprimer le pied de page <b>"${row.name}"</b>?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#42A71E',
                    cancelButtonColor: 'var(--lcdtOrange)',
                    cancelButtonText: 'Annuler',
                    confirmButtonText: `Oui, s'il vous plaît.`
                })

                if(result.isConfirmed) {

                    displayLoader('Suppression pied de page en cours...')
                    await store.dispatch(`${HTMLTEMPLATELIST_MODULE}${HTMLTEMPLATELIST_DELETE_ROW}`, { id: row.id, type: 'footer'}).then(()=>{
                        store.commit(`${ITEM_LIST_MODULE}${ITEM_LIST_REMOVE_ROW}`,{id:'id',idValue:row.id});
                    });

                }

                
            }
        
            catch(e) {
                displayError('Une erreur est survenue')
                throw e
            }
            
            finally {
               hideLoader();
            }
        }

        const duplicateFooterRow=async(row)=>{
           try {
                
            const result = await Swal.fire({
                title: 'Dupliquer le pied de page',
                input: 'text',
                inputLabel: 'Nom du pied de page',
                inputValue: `${row.name}_copie`,
                confirmButtonColor: '#42A71E',
                cancelButtonColor: 'var(--lcdtOrange)',
                showCancelButton: true,
                cancelButtonText: 'Annuler',
                confirmButtonText: `Dupliquer`,
                inputValidator: (value) => {
                    if (!value) {
                    return 'Veuillez préciser le nom du pied de page!'
                    }
                    if (value==row.name) {
                    return 'Veuillez choisir un autre nom pour le pied de page!'
                    }
                }
                })

    

                if(result.isConfirmed) {
            

                    displayLoader('Copie du pied de page en cours...')
                     await store.dispatch(`${HTMLTEMPLATELIST_MODULE}${HTMLTEMPLATELIST_DUPLICATE_ROW}`, { id: row.id, type: 'footer',name:result.value}).then(response=>{
                               displayLoader('Chargement en cours..')
                       store.dispatch(`${ITEM_LIST_MODULE}${ITEM_LIST_TABLE_RELOAD}`,{fullreload:true});
                     });

                }

                
            }
        
            catch(e) {
                displayError('Une erreur est survenue')
             
                throw e
            }
            
            finally {
               hideLoader();
            } 
        }
        return {
            showcontainer,
            tabs,
            all_templates,
            template_headers,
            template_footers,
            rowHeaderEdit,
            rowFooterEdit,
            headerFooterEditorType,
            showHeaderFooterEditor,
            hfObj,
            updateListRow,
            addnew,
            editRow,
            deleteRow,
            duplicateRow,
            deleteHeaderRow,
            duplicateHeaderRow,
            deleteFooterRow,
            duplicateFooterRow
        }

  }

}
</script>

<style lang="scss" scoped>
.lcdt-logo {
    padding-left: 0
}

.newbtn{
        position: absolute;
        right: 333px;
        top: -62px;
        background-color: rgb(232, 111, 41);
        color: rgb(255, 255, 255);
        height: 38px!important;
        padding:7px 18px;
}
.newbtn:hover {
    opacity: 0.9;
}
</style>
