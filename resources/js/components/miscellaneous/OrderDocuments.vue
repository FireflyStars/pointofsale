<template>
  <mini-panel title="Document/rapport">
  <div class="row mb-3">
    <div class="col-4"></div>
    <div class="col-3  almarai-light lcdtgrey d-flex font-12 align-items-center justify-content-center">Date</div>
    <div class="col-2  almarai-light lcdtgrey d-flex font-12 align-items-center justify-content-center">Auteur</div>
    <div class="col-1"></div>
    <div class="col-1"></div>
    <div class="col-1"></div>
  </div>
             <transition-group tag="div" class="list"  name="list" appear>
  <div class="row mb-3" v-for="document,index in documents" :key="index">
    <div class="col-4 documentline almarai_bold_normal font-14  d-flex align-items-center" :class="{report:document.template_id>0}">{{document.name}}</div>
    <div class="col-3 almarai-light d-flex font-14 align-items-center justify-content-center">{{document.formatted_date}}</div>
    <div class="col-2 almarai-light d-flex font-14 align-items-center justify-content-center">{{document.user==null?'--/--':document.user.name}}</div>
    <div class="col-1 d-flex align-items-center justify-content-center"><span v-if="document.template_id>0" @click="editReport(document)"><icon  name="file-outline" width="16px" height="16px" class="cursor-pointer" ></icon></span></div>
    <div class="col-1 d-flex align-items-center justify-content-center">
        <span @click="document.template_id>0?generatePdfById(document.id):downloadDoc(document.id)" > 
           <icon  name="download" width="16px" height="16px" class="cursor-pointer" ></icon>
        </span>
    </div>
    <div class="col-1 d-flex align-items-center justify-content-center" >
        <span @click="document.template_id>0?removeReport(document):removeDoc(document)">
            <icon  name="trash-x" width="20px" height="20px" class="cursor-pointer"  ></icon>
        </span>
    </div>
  </div>
             </transition-group>
    <div class="d-flex justify-content-evenly mt-4">
                            <span class="font-14 mulish_600_normal document_action noselect" @click="createReport" ><icon name="plus-circle" width="16px" height="16px" /> AJOUTER RAPPORT</span>
                            <span class="font-14 mulish_600_normal document_action  noselect"  @click="addfile" ><icon name="plus-circle" width="16px" height="16px"/> AJOUTER DOCUMENT</span>

                        </div>  
                        <input type="file" id="fileuploadEl" class="d-none" @change="fileElUpdated"/>
  </mini-panel>
</template>

<script>
import MiniPanel from './MiniPanel.vue'
import Icon from '../../components/miscellaneous/Icon.vue';
import { computed, onMounted } from '@vue/runtime-core';
import { useStore } from 'vuex';
import { DEVIS_DETAIL_GET_DOCUMENT_URL, DEVIS_DETAIL_GET_ORDER_DOCUMENTS, DEVIS_DETAIL_LOAD_ORDER_DOCUMENTS, DEVIS_DETAIL_MODULE, DEVIS_DETAIL_REMOVE_DOCUMENT, DEVIS_DETAIL_SET_ORDER_DOCUMENTS, DEVIS_DETAIL_UNSET_ORDER_DOCUMENT, DEVIS_DETAIL_UPLOAD_DOCUMENT, DISPLAY_LOADER, HIDE_LOADER, LOADER_MODULE } from '../../store/types/types';
import {formatDate} from '../../components/helpers/helpers';
import { useRouter } from 'vue-router';
import useReports from '../../composables/reports/useReports';

export default {
        name: "OrderDocuments",
        components:{MiniPanel,Icon},
        props:{
            order_id:{
                type:Number,
                required:true
            }
        },
        setup(props){
              const { generatePdfById,deleteReport } = useReports();
            const store=useStore();
            const router=useRouter();
            
            onMounted(()=>{
                store.commit(`${DEVIS_DETAIL_MODULE}${DEVIS_DETAIL_SET_ORDER_DOCUMENTS}`,[]);
                store.dispatch(`${DEVIS_DETAIL_MODULE}${DEVIS_DETAIL_LOAD_ORDER_DOCUMENTS}`,props.order_id);

            });
            const documents=computed(()=>store.getters[`${DEVIS_DETAIL_MODULE}${DEVIS_DETAIL_GET_ORDER_DOCUMENTS}`]);
            const editReport=(document)=>{
              
                router.push({ name: 'edit-report-page', params: { id: document.id } });
            }
            const  removeReport=async (document)=>{
                  store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [
                    true,
                    "Suppression rapport en cours..",
                ]);
                const data = await deleteReport(document.id);
                store.commit(`${DEVIS_DETAIL_MODULE}${DEVIS_DETAIL_UNSET_ORDER_DOCUMENT}`,document.id);
                    store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
            }

            const  removeDoc=async (document)=>{
                  store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [
                    true,
                    "Suppression document en cours..",
                ]);
                store.dispatch(`${DEVIS_DETAIL_MODULE}${DEVIS_DETAIL_REMOVE_DOCUMENT}`,document.id).then(response=>{
                        store.commit(`${DEVIS_DETAIL_MODULE}${DEVIS_DETAIL_UNSET_ORDER_DOCUMENT}`,document.id);
                         store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
                });
              
            }
               const  downloadDoc=async (document_id)=>{
                   store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [
                    true,
                    "Téléchargement document en cours..",
                ]);

                   store.dispatch(`${DEVIS_DETAIL_MODULE}${DEVIS_DETAIL_GET_DOCUMENT_URL}`,document_id).then(response=>{
            
                         store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
                });
               }
            const addfile=()=>{
                let fileEl=document.querySelector('#fileuploadEl');
                
                fileEl.click();
                
                console.log(fileEl);
            }
            const fileElUpdated=()=>{
                var input = document.querySelector('#fileuploadEl');

                var data = new FormData();
                for (const file of input.files) {
                data.append('files',file,file.name)
                }
                store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [
                    true,
                    "Chargement document en cours..",
                ]);
                store.dispatch(`${DEVIS_DETAIL_MODULE}${DEVIS_DETAIL_UPLOAD_DOCUMENT}`,data).then(resp=>{
                    store.dispatch(`${DEVIS_DETAIL_MODULE}${DEVIS_DETAIL_LOAD_ORDER_DOCUMENTS}`,props.order_id).then(resp2=>{
                        store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
                    });
                    
                });
              
              
            }
              const createReport=()=>{
                    document.getElementsByTagName( 'body' )[0].className='';
                    router.push( {name: 'create-report-page', params: { id: props.order_id }});
                }
            return{
                documents,
                formatDate,
                router,
                editReport,
                generatePdfById,
                removeReport,
                addfile,
                fileElUpdated,
                removeDoc,
                downloadDoc,
                createReport
            }
        }
}
</script>

<style lang="scss" scoped>
.documentline {
    padding-left: 48px;
    position: relative;
    word-break: break-word;
}
.documentline::before{
    content: "";
    width: 13px;
    height: 13px;
    background-color: var(--lcdtOrange) ;
    border-radius: 50%;
    display: block;
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    transition: background-color ease-in-out 0.3s;
}
.documentline.report::before{
    background-color: #C4C4C4;
}
.document_action{
    color:var(--lcdtOrange);
    cursor: pointer;
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