<template>
<div>
     <mini-panel title="Paiement">
       <div class="row mb-3">
        <div class="col-6"></div>
        <div class="col-3"><h3 class="almarai_700_normal">Reste à payer</h3></div>
        <div class="col-2"><h3 class="almarai_700_normal">{{formatPrice(reste_a_payer())}}</h3></div>
        <div class="col-1"><h3 class="almarai_700_normal">{{reste_a_payer_pourcentage()}}%</h3></div>
      </div>
      <div class="row mb-2">
        <div class="col-2 font-12 lcdtgrey">N°paiement</div>
        <div class="col-2 font-12 lcdtgrey d-flex justify-content-center">Date</div>
        <div class="col-1 font-12 lcdtgrey d-flex justify-content-right">%</div>
        <div class="col-2 font-12 lcdtgrey d-flex justify-content-center">Statut</div>
        <div class="col-2 font-12 lcdtgrey d-flex justify-content-center">Moyen</div>
        <div class="col-2 font-12 lcdtgrey">Montant</div>
        <div class="col-1"></div>
      </div>
          <transition-group tag="div" class="list"  name="list" appear>
      <template v-for="payment,index in payments" :key="index">
        <div class="row mb-2">
        <div class="col-2 font-14 paymentid almarai_700_normal d-flex align-items-center" :class="{valide:payment.paiement_state_id==2}">{{payment.id}}</div>
        <div class="col-2 font-14d-flex align-items-center justify-content-center">{{formatDate(payment.datepaiement)}}</div>
        <div class="col-1 font-14 d-flex align-items-center justify-content-right">{{payment.pourcentage}}%</div>
        <div class="col-2  d-flex align-items-center"><state-tag width="100%" classes="almarai_700_normal" :id="payment.paiement_state_id" :states="payment_states"/></div>
        <div class="col-2 d-flex align-items-center"><state-tag width="100%" classes="almarai_700_normal" :id="payment.paiement_type_id" :states="payment_types"/></div>
        <div class="col-2 font-14 d-flex align-items-center">{{formatPrice(parseFloat(payment.montantpaiement))}}</div>
        <div class="col-1 d-flex align-items-center"><icon v-if="payment.paiement_state_id==1" name="trash-x" width="20px" height="20px" class="cursor-pointer" @click="removePaiement(payment)"></icon></div>
        </div>
      </template>
          </transition-group>
           <transition enter-active-class="animate__animated animate__fadeIn" leave-active-class="animate__animated animate__fadeOut">
       <div class="row mt-4" v-if="reste_a_payer()!=0">
          <div class="col d-flex justify-content-center"><span @click="showpaymentform" class="factions lcdtOrange font-14 d-flex align-items-center gap-1"><icon name="plus-circle"/><span class=" noselect">AJOUTER PAIEMENT</span></span></div>
        </div> 
           </transition>
    </mini-panel>  

      <simple-modal-popup v-model="showmodal_payment" title="Ajouter un paiement" @modalconfirm="newpaiement" @modalclose="closemodal" icon="paiement" iconStyles="width:32px;height:32px;">
    <div class="container-fluid">
         <div class="row mb-3">
                <div class="col-6">Type de paiement</div><div class="col-6"> <select-box
                                        v-model="paiement.type" 
                                        placeholder="Chosir un type de paiement" 
                                        :options="typeOptions" 
                                        name="paiementtype" /></div>
         </div>
          <div class="row mb-3">
                <div class="col-6">Date de paiement</div><div class="col-6">
         <date-picker @changed="setPaiementDate"  name="paiement_date" :droppos="{top:'40px',right:'auto',bottom:'auto',left:'0',transformOrigin:'top center'}"></date-picker>
            </div>
          </div>
            <div class="row mb-3">
                <div class="col-6">Montant</div><div class="col-6"><money3 v-model="paiement.montantpaiement" v-bind="moneyconfig" @keyup="checkmontant"></money3></div>
            </div>
            <div class="row mb-3">
                <div class="col-6">Taux</div><div class="col-6"><input type="text" v-model="paiement.pourcentage" class="input-text"/></div>
            </div>
             <div class="row mb-3">
                <div class="col-6">Reference</div><div class="col-6"><input type="text" v-model="paiement.reference" class="input-text"/></div>
            </div>
       

    </div>
     
</simple-modal-popup>
</div>
</template>

<script>
import MiniPanel from '../../components/miscellaneous/MiniPanel.vue'
import Icon from '../../components/miscellaneous/Icon.vue';
import { formatPrice,formatDate,br,isFloat } from '../../components/helpers/helpers';
import { computed, onMounted, ref, watch } from '@vue/runtime-core';
import { FACTURE_DETAIL_ADD_PAYMENT, FACTURE_DETAIL_GET, FACTURE_DETAIL_GET_PAYMENTS, FACTURE_DETAIL_GET_PAYMENT_STATES, FACTURE_DETAIL_GET_PAYMENT_TYPES, FACTURE_DETAIL_LOAD_PAYMENTS, FACTURE_DETAIL_MODULE, FACTURE_DETAIL_REMOVE_PAYMENT, FACTURE_DETAIL_SET_PAYMENTS, TOASTER_CLEAR_TOASTS, TOASTER_MESSAGE, TOASTER_MODULE } from '../../store/types/types';
import StateTag from '../../components/miscellaneous/StateTag.vue';
 import { Money3Component } from 'v-money3';
import { useStore } from 'vuex';
import DatePicker from '../../components/miscellaneous/DatePicker.vue';
export default {
        name: "Payment",
        props:{
            invoice_id:{
              type:Number,
              required:true
            }
        },
        components:{MiniPanel,Icon,StateTag,money3:Money3Component,DatePicker},
         emits: ['showloader','hideloader'],
        setup(props,context){

                     const moneyconfig=ref({
                            masked: false,
                            prefix: '',
                            suffix: '€',
                            thousands: ',',
                            decimal: '.',
                            precision: 2,
                            disableNegative: false,
                            disabled: false,
                            min: null,
                            max: null,
                            allowBlank: false,
                            minimumNumberOfCharacters: 0,
                            });
        
          const store=useStore();
          const showmodal_payment=ref(false);
          const paiement=ref({
              invoice_id:props.invoice_id,
              type:0,
              reference:'',
              pourcentage:0,
              montantpaiement:0,
              datepaiement:''
          });

          const typeOptions=ref([]);

          onMounted(()=>{
            store.commit( `${FACTURE_DETAIL_MODULE}${FACTURE_DETAIL_SET_PAYMENTS}`,[]);
            store.dispatch(`${FACTURE_DETAIL_MODULE}${FACTURE_DETAIL_LOAD_PAYMENTS}`,props.invoice_id);
          })
         

          const payments=computed(()=>store.getters[`${FACTURE_DETAIL_MODULE}${FACTURE_DETAIL_GET_PAYMENTS}`]);
           const invoice=computed(()=>store.getters[`${FACTURE_DETAIL_MODULE}${FACTURE_DETAIL_GET}`]);

          const payment_states=computed(()=>store.getters[`${FACTURE_DETAIL_MODULE}${FACTURE_DETAIL_GET_PAYMENT_STATES}`]);
          const payment_types=computed(()=>store.getters[`${FACTURE_DETAIL_MODULE}${FACTURE_DETAIL_GET_PAYMENT_TYPES}`]);

        
          watch(()=>payment_types.value,(current_val,previous_val)=>{
            typeOptions.value=[];

            for(const i in payment_types.value){
              typeOptions.value.push({
                value:payment_types.value[i].id,
                display:payment_types.value[i].name
              });
            }

          },{
            deep:true
          })

          const reste_a_payer=()=>{
            let total_paid=payments.value.reduce((acc, payment) => {
                //if(payment.paiement_state_id==2)
                  return acc + parseFloat(payment.montantpaiement);
                  // return acc;
                }, 0);
          
             return invoice.value.montant-total_paid;
          }

          const reste_a_payer_pourcentage=()=>{
            let total_paid=payments.value.reduce((acc, payment) => {
                //if(payment.paiement_state_id==2)
                  return acc + parseFloat(payment.montantpaiement);
                 //  return acc;
                }, 0);
            let v=((invoice.value.montant-total_paid)/invoice.value.montant)*100;
             return isFloat(v)?v.toFixed(2):v;
          }
       
          watch(()=>paiement.value.montantpaiement,(current_val,previous_val)=>{
              let total_paid=payments.value.reduce((acc, payment) => {
                //if(payment.paiement_state_id==2)
                  return acc + payment.montantpaiement;
                 //  return acc;
                }, 0);
          
             let reste_a_payer= invoice.value.montant-total_paid;

             if(parseFloat(current_val)>reste_a_payer)
             paiement.value.montantpaiement=reste_a_payer;

             paiement.value.pourcentage=((paiement.value.montantpaiement/invoice.value.montant)*100);
              paiement.value.pourcentage=isFloat( paiement.value.pourcentage)? paiement.value.pourcentage.toFixed(2): paiement.value.pourcentage;

          })
          const showpaymentform=()=>{
             paiement.value={
              invoice_id:props.invoice_id,
              type:0,
              reference:'',
              pourcentage:0,
              montantpaiement:0,
              datepaiement:''
          };
              showmodal_payment.value=true;
          }
          const checkmontant=()=>{

          }
          const newpaiement=()=>{
              

                 let valide=true;
            store.commit(`${TOASTER_MODULE}${TOASTER_CLEAR_TOASTS}`);
           if(paiement.value.type==0){
             store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                            type: 'danger',
                            message: 'Veuillez choisir un type de paiement.',
                            ttl: 8,
                        });
                        valide=false;
           }

               if(paiement.value.datepaiement.trim()==''){
             store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                            type: 'danger',
                            message: 'Veuillez saisir la date de paiement.',
                            ttl: 8,
                        });
                        valide=false;
           }
                   if(parseFloat(paiement.value.montantpaiement)<=0){
             store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                            type: 'danger',
                            message: 'Montant paiement invalide.',
                            ttl: 8,
                        });
                        valide=false;
           }
          

             if(parseFloat(paiement.value.pourcentage)<=0){
             store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                            type: 'danger',
                            message: 'Taux invalide.',
                            ttl: 8,
                        });
                        valide=false;
           }
          if(paiement.value.reference.trim()==''){
             store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                            type: 'danger',
                            message: 'Veuillez saisir une référence afin de pouvoir identifier ce paiement.',
                            ttl: 8,
                        });
                        valide=false;
           }
           if(valide){
           showmodal_payment.value=false;
              context.emit('showloader')
           store.dispatch(`${FACTURE_DETAIL_MODULE}${FACTURE_DETAIL_ADD_PAYMENT}`,paiement.value).then(()=> context.emit('hideloader')).finally(()=> context.emit('hideloader'));
           }
          }
          const closemodal=()=>{

          }
          const removePaiement=(paiement)=>{
         
              context.emit('showloader')
              store.dispatch(`${FACTURE_DETAIL_MODULE}${FACTURE_DETAIL_REMOVE_PAYMENT}`,paiement.id).then(()=> context.emit('hideloader')).finally(()=> context.emit('hideloader'));
          }
          const setPaiementDate=(obj)=>{
            paiement.value.datepaiement=obj.date;
  
          }
          return {
            formatPrice,
            formatDate,
            payments,
            payment_states,
            payment_types,
            reste_a_payer,
            reste_a_payer_pourcentage,
            showpaymentform,
            showmodal_payment,
            newpaiement,
            closemodal,
            paiement,
            typeOptions,
            moneyconfig,
            checkmontant,
            removePaiement,
            setPaiementDate
          }
        }
}
</script>

<style lang="scss" scoped>
.lcdtgrey{
    color:var(--lcdtGrey);
}
h3{
    font-size: 14px;
    line-height: 14px;
    margin-bottom: 8px;
}
.paymentid{
    position: relative;
    padding-left: 54px;
}
.paymentid::before{
    content: '';
    display: block;
    width: 13px;
    height: 13px;
    border-radius: 50%;
    background-color: #c4c4c4;
    position: absolute;
    top: 50%;
    left: 18px;
    transform: translateY(-50%);
}
.paymentid.valide::before{
    content: '';
    display: block;
    width: 13px;
    height: 13px;
    border-radius: 50%;
    background-color: #78DC70;
    position: absolute;
    top: 50%;
    left: 18px;
    transform: translateY(-50%);
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
</style>