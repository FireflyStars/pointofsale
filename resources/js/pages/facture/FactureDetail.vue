<template>
<item-detail-panel :showloader="showloader" ref="detailpanel">
     <transition enter-active-class="animate__animated animate__fadeIn" leave-active-class="animate__animated animate__fadeOut">
    <div class="row" v-if="show">
        <div class="col-7">
            <page-title icon="facture" :name="`N° ${invoice.reference}`" class="almarai_extrabold_normal_normal" style="width: 45px; height: 45px;" />
        </div>
        <div class="col-5" style="padding-top:33px">
            <invoice-state-tag :invoice_state_id="invoice.invoice_state_id" classes="almarai_700_normal" width="auto"></invoice-state-tag>
        </div>
    </div>
     </transition>
 <transition enter-active-class="animate__animated animate__fadeIn" leave-active-class="animate__animated animate__fadeOut">
    <div class="row"  v-if="show" style="margin:40px 0 0 8px;">
        <div class="col-3" v-if="invoice.order!=null&&invoice.order_id!=0" ><h2 class="almarai_700_normal">Commande<br/>N° <span class="btn-link cursor-pointer" @click=" router.push({name:'commande-details', params: { id:invoice.order.id }});">{{invoice.order.id}}</span></h2></div>
        <div class="col-3" v-if="invoice.order!=null&&invoice.order_id!=0" ><h2 class="almarai_700_normal">Date Commande<br/>{{formatDate(invoice.order.datecommande)}}</h2></div>
        <div class="col-3"  ><h2 class="almarai_700_normal">Date Facture<br/>{{formatDate(invoice.dateecheance)}}</h2></div>
        <div class="col-3"><state-tag :id="invoice.invoice_type_id" :states="invoice.invoiceTypes" classes="almarai_700_normal" width="auto"></state-tag></div>
    </div>
 </transition>
    <hr v-if="show"/>
     <transition enter-active-class="animate__animated animate__fadeIn" leave-active-class="animate__animated animate__fadeOut">
    <div class="row" v-if="show"><div class="col"><h3 class="almarai_700_normal" v-if="typeof invoice.order.customer!='undefined'">{{invoice.order.customer.company}}</h3></div></div>
     </transition>
      <transition enter-active-class="animate__animated animate__fadeIn" leave-active-class="animate__animated animate__fadeOut">
    <div class="row" v-if="show">
        <div class="col"><span class="subtitle almarai_700_normal">Adresse du chantier</span><br><span v-html="invoice.order.formatted_chantier_address"></span></div>
        <div class="col"><span class="subtitle almarai_700_normal">Adresse facturation</span><br><span v-html="invoice.order.formatted_facturation_address"></span></div>
    </div>
      </transition>
       <transition enter-active-class="animate__animated animate__fadeIn" leave-active-class="animate__animated animate__fadeOut">
    <div class="row" v-if="show">
        <div class="col"><span class="subtitle almarai_700_normal">Contact</span><br><span v-if="invoice.order.contact!=null" v-html="`${typeof invoice.order.contact.firstname !='undefined'? invoice.order.contact.firstname:''} ${typeof invoice.order.contact.name !='undefined'?invoice.order.contact.name:''}${br(invoice.order.contact.mobile)}${br(invoice.order.contact.telephone)}`"></span><span v-else>Pas de contact</span></div>
        <div class="col"><span class="subtitle almarai_700_normal">Mode de paiement</span><br><span>{{invoice.mode_paiements.join(", ")}}</span></div>
    </div>
       </transition>
     <hr v-if="show"/>
           <transition enter-active-class="animate__animated animate__fadeIn" leave-active-class="animate__animated animate__fadeOut">
   <mini-panel title="" v-if="typeof(invoice.details) !='undefined'&&invoice.details.length>0&&show">
           <div class="row mt-3 mb-2">
   
            <div class="col-5"><h3 class="almarai_700_normal">Description</h3></div>
            <div class="col-3"><h3 class="almarai_700_normal">Date</h3></div>
            <div class="col-2"><h3 class="almarai_700_normal">TVA</h3></div>
            <div class="col-2"><h3 class="almarai_700_normal">Montant</h3></div>
        </div>
       <div class="row mb-3 align-items-center" v-for="detail in invoice.details" :key="detail.id">
            <div class="col-5">{{detail.description}}<span class="detail-comment" v-if="detail.comment!=null&&detail.comment.trim()!=''">{{detail.comment}}</span></div>
            <div class="col-3">{{formatDate(detail.created_at)}}</div>
            <div class="col-2">{{parseFloat(detail.tax.taux)*100}}%</div>
            <div class="col-2">{{formatPrice(detail.montant)}}</div>
        </div>

   </mini-panel>
       </transition>
      <!--<transition enter-active-class="animate__animated animate__fadeIn" leave-active-class="animate__animated animate__fadeOut">
   <mini-panel title="" v-if="invoice.order_invoice!=null&&show">
           <div class="row mt-3 mb-2">
            <div class="col-4"></div>
            <div class="col-3"><h3 class="almarai_700_normal">% Commande</h3></div>
            <div class="col-3"><h3 class="almarai_700_normal">Date échéance</h3></div>
            <div class="col-2"><h3 class="almarai_700_normal">Montant</h3></div>
        </div>
       <div class="row mb-3">
            <div class="col-4">{{invoice.order_invoice.description}}</div>
            <div class="col-3">{{invoice.order_invoice.pourcentage}}%</div>
            <div class="col-3">{{formatDate(invoice.order_invoice.dateinvoice)}}</div>
            <div class="col-2">{{formatPrice(invoice.order_invoice.montant)}}</div>
        </div>

   </mini-panel>
       </transition>-->
      <!--<transition enter-active-class="animate__animated animate__fadeIn" leave-active-class="animate__animated animate__fadeOut">
        
    <mini-panel title=""  v-if="show">
        <div class="row my-3">
            <div class="col-8"> 
                <div class="row mb-2">
                    <div class="col"><span class="factions lcdtOrange font-14 d-flex align-items-center gap-1"><icon name="plus-circle"/><span class=" noselect">AJOUTER LIGNE</span></span></div>
                </div>
                <div class="row mb-2">
                    <div class="col"><span class="factions lcdtOrange font-14 d-flex align-items-center gap-1"><icon name="plus-circle"/><span class=" noselect">AJOUTER COMMENTAIRE CLIENT/INTERNE</span></span></div>
                </div>
                 <div class="row mb-2" v-if="invoice.invoice_state_id==1">
                    <div class="col"><span class="factions lcdtOrange font-14 d-flex align-items-center gap-1"><icon name="plus-circle"/><span class=" noselect">MODIFIER ECHEANCE</span></span></div>
                </div>
            </div>
            <div class="col-4" >
                <div class="row mb-2 "  v-if="invoice.invoice_state_id==1">
                    <div class="col "><span class="factions lcdtOrange font-14 d-flex align-items-center gap-1"><icon name="plus-circle"/><span class=" noselect">CHANGER CONTACT</span></span></div>
                </div>
            </div>
        </div>
    </mini-panel>  
      </transition>-->
       <transition enter-active-class="animate__animated animate__fadeIn" leave-active-class="animate__animated animate__fadeOut">

           <payment v-if="show&&[1,4].includes(invoice.invoice_type_id)&&(invoice.invoice_state_id==6||invoice.invoice_state_id==2||invoice.invoice_state_id==3||invoice.invoice_state_id==5)" :invoice_id="invoice.id" @showloader="showloader=true" @hideloader="showloader=false"></payment>

       </transition>
     <div class="od_actions mb-3" v-if="show">
        <button class="btn btn-outline-dark almarai_700_normal" v-if="invoice.invoice_state_id==1" @click="updateInvoiceState(6)">VALIDE</button>
     
        <button class="btn btn-outline-info almarai_700_normal" v-if="invoice.invoice_state_id==6"  @click="updateInvoiceState(2)">ENVOYE</button>
        <button class="btn btn-outline-secondary almarai_700_normal" v-if="invoice.invoice_state_id==1&&invoice.invoice_type_id!=3" @click="removeInvoice()">EFFACER</button>  
           <button class="btn btn-outline-warning almarai_700_normal">PDF</button>
        <button class="btn btn-outline-secondary almarai_700_normal"  @click="detailpanel.close()">FERMER</button>  
        
     </div>


</item-detail-panel>




</template>

<script>
import { computed, onMounted, ref,h, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router'
import { useStore } from 'vuex';
import ItemDetailPanel from '../../components/miscellaneous/ItemListTable/ItemDetailPanel.vue'
import InvoiceStateTag from '../../components/miscellaneous/InvoiceStateTag.vue';
import StateTag from '../../components/miscellaneous/StateTag.vue';

import { formatPrice,formatDate,br,isFloat } from '../../components/helpers/helpers';
import Swal from 'sweetalert2';
import DatePicker from '../../components/miscellaneous/DatePicker.vue';

import Icon from '../../components/miscellaneous/Icon.vue';

import MiniPanel from '../../components/miscellaneous/MiniPanel.vue'
import { FACTURE_DETAIL_GET, FACTURE_DETAIL_LOAD, FACTURE_DETAIL_MODULE, FACTURE_DETAIL_REMOVE_FACTURATION, FACTURE_DETAIL_UPDATE_INVOICE_STATE } from '../../store/types/types';
import Payment from './Payment.vue';

    export default {
        name: "InvoiceDetail",
        components:{ItemDetailPanel,InvoiceStateTag, DatePicker, Icon,MiniPanel,Payment,StateTag},
        setup(){
            const route=useRoute();
            const router=useRouter();
            const store=useStore();
            const show=ref(false);
            const showloader=ref(false);
            const detailpanel=ref(null);
            let invoice_id=route.params.id;
            onMounted(()=>{
       
                store.dispatch(`${FACTURE_DETAIL_MODULE}${FACTURE_DETAIL_LOAD}`,invoice_id).then(()=>{
                    showloader.value=false;
                    show.value=true;
                }).finally(()=>{
                     showloader.value=false;
                });
            })
           
            const invoice=computed(()=>store.getters[`${FACTURE_DETAIL_MODULE}${FACTURE_DETAIL_GET}`]);
            const updateInvoiceState=(invoiceStateid)=>{
                showloader.value=true;
                store.dispatch(`${FACTURE_DETAIL_MODULE}${FACTURE_DETAIL_UPDATE_INVOICE_STATE}`,invoiceStateid).then(()=>{
                    showloader.value=false;
                }).finally(()=>{
                     showloader.value=false;
                })
            }
            const removeInvoice=()=>{
                
                showloader.value=true;
                store.dispatch(`${FACTURE_DETAIL_MODULE}${FACTURE_DETAIL_REMOVE_FACTURATION}`,invoice.value.order_invoice).then(()=>{
                    showloader.value=false;
                    router.back();
                }).finally(()=>{
                     showloader.value=false;
                })

            }

             return {
                invoice,
                 show,
                 showloader,
                 formatPrice,
                 formatDate,
                 br,
                 updateInvoiceState,
                 removeInvoice,
                 router,
                 detailpanel

                

             }
        }
    }
</script>

<style scoped>
.facture_action{
    color:#E8581B;
    cursor: pointer;
    }
h2{
    font-size: 16px;
    line-height: 20px;
    margin-bottom: 8px;
}
h3{
    font-size: 14px;
    line-height: 14px;
    margin-bottom: 8px;
}

hr{
    margin:25px -20px;
    background-color: #E0E0E0;

}
.subtitle{
    color:#C3C3C3;
    font-size:14px;
    font-weight: 400!important;
}

.lcdtgrey{
    color:var(--lcdtGrey);
}
.od_catname{
    position: relative;
    padding-left: 54px;
}
.od_catname::after{
    content:'';
    display: block;
    width: 13px;
    height: 13px;
    border-radius: 50%;
    background-color: var(--lcdtOrange);
    position: absolute;
    top:50%;
    left: 18px;
    transform: translateY(-50%);

}
.od_actions{
    display: flex;
    justify-content: space-between;
}

.noselect {
  -webkit-touch-callout: none; /* iOS Safari */
    -webkit-user-select: none; /* Safari */
     -khtml-user-select: none; /* Konqueror HTML */
       -moz-user-select: none; /* Old versions of Firefox */
        -ms-user-select: none; /* Internet Explorer/Edge */
            user-select: none; /* Non-prefixed version, currently
                                  supported by Chrome, Edge, Opera and Firefox */
}
.factions{
    line-height: 11px;
    cursor:pointer;
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

    .detail-comment{
        background: #EEE;
        padding: 2px 10px;
        border-radius: 11px;
        display: block;
        font-size: 11px;
        font-family: 'Mulish Regular'
    }

</style>