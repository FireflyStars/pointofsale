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
  <div class="row mb-3" v-for="document,index in documents" :key="index">
    <div class="col-4 documentline almarai_bold_normal font-14  d-flex align-items-center" :class="{report:document.template_id>0}">{{document.name}}</div>
    <div class="col-3 almarai-light d-flex font-14 align-items-center justify-content-center">{{document.formatted_date}}</div>
    <div class="col-2 almarai-light d-flex font-14 align-items-center justify-content-center">{{document.user==null?'--/--':document.user.name}}</div>
    <div class="col-1 d-flex align-items-center justify-content-center"><span @click="editReport(document)"><icon  name="file-outline" width="16px" height="16px" class="cursor-pointer" ></icon></span></div>
    <div class="col-1 d-flex align-items-center justify-content-center">
        <span @click="generatePdfById(document.id)"> 
           <icon  name="download" width="16px" height="16px" class="cursor-pointer" ></icon>
        </span>
    </div>
    <div class="col-1 d-flex align-items-center justify-content-center" >
            <icon  name="trash-x" width="20px" height="20px" class="cursor-pointer"  ></icon>
    </div>
  </div>
    <div class="d-flex justify-content-evenly mt-4">
                            <span class="font-14 mulish_600_normal document_action noselect" @click="router.push( {name: 'create-report-page', params: { id: order_id }})" ><icon name="plus-circle" width="16px" height="16px" /> AJOUTER RAPPORT</span>
                            <span class="font-14 mulish_600_normal document_action  noselect" ><icon name="plus-circle" width="16px" height="16px"/> AJOUTER DOCUMENT</span>

                        </div>  
  </mini-panel>
</template>

<script>
import MiniPanel from './MiniPanel.vue'
import Icon from '../../components/miscellaneous/Icon.vue';
import { computed, onMounted } from '@vue/runtime-core';
import { useStore } from 'vuex';
import { DEVIS_DETAIL_GET_ORDER_DOCUMENTS, DEVIS_DETAIL_LOAD_ORDER_DOCUMENTS, DEVIS_DETAIL_MODULE } from '../../store/types/types';
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
              const { generatePdfById } = useReports();
            const store=useStore();
            const router=useRouter();
            
            onMounted(()=>{
                store.dispatch(`${DEVIS_DETAIL_MODULE}${DEVIS_DETAIL_LOAD_ORDER_DOCUMENTS}`,props.order_id);

            });
            const documents=computed(()=>store.getters[`${DEVIS_DETAIL_MODULE}${DEVIS_DETAIL_GET_ORDER_DOCUMENTS}`]);
            const editReport=(document)=>{
              
                router.push({ name: 'edit-report-page', params: { id: document.id } });
            }
            return{
                documents,
                formatDate,
                router,
                editReport,
                generatePdfById
            }
        }
}
</script>

<style lang="scss" scoped>
.documentline {
    padding-left: 48px;
    position: relative;
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
</style>