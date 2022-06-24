<template>
<item-detail-panel :showloader="showloader">
    <div class="row" v-if="show">
        <div class="col-4">
            <page-title icon="pdf" :name="`N° ${order_id}`" class="almarai_extrabold_normal_normal"/>
        </div>
        <div class="col-8" style="padding-top:33px">
            <order-state-tag :order_state_id="order.order_state_id" classes="almarai_700_normal" width="auto"></order-state-tag>
        </div>
    </div>

    <div class="row"  v-if="show" style="margin:40px 0 0 8px;">
        <div class="col-4" v-if="order.order_state_id==4" ><h2 class="almarai_700_normal">Date Commande<br/>{{formatDate(order.datecommande)}}</h2></div>
         <div :class="{'col-8':order.order_state_id!=4,'col-4':order.order_state_id==4}"><h2 class="almarai_700_normal">Date Promise<br/>{{formatDate(order.datefinprevu)}}</h2></div>
        <div class="col-2 font-14 almarai_700_normal">{{order.nbheure}}H</div>
        <div class="col-2 font-16 almarai_700_normal">{{formatPrice(order.total)}}</div>
    </div>
    <hr v-if="show"/>
    <div class="row" v-if="show"><div class="col"><h3 class="almarai_700_normal" v-if="typeof order.customer!='undefined'">{{order.customer.company}}</h3></div></div>
    <div class="row" v-if="show">
        <div class="col"><span class="subtitle almarai_700_normal">Adresse du chantier</span><br><span v-html="order.formatted_chantier_address"></span></div>
        <div class="col"><span class="subtitle almarai_700_normal">Adresse facturation</span><br><span v-html="order.formatted_facturation_address"></span></div>
    </div>
    <div class="row" v-if="show">
        <div class="col"><span class="subtitle almarai_700_normal">Contact</span><br><span v-if="typeof order.contact!='undefined'" v-html="`${typeof order.contact.firstname !='undefined'? order.contact.firstname:''} ${typeof order.contact.name !='undefined'?order.contact.name:''}${br(order.contact.mobile)}${br(order.contact.telephone)}`"></span></div>
        <div class="col"><span class="subtitle almarai_700_normal">Mode de paiement</span><br><span>--/--</span></div>
    </div>
     <hr v-if="show"/>
     <template v-if="order.order_state_id!=4">
     <div  v-for="orderzone,index in order.order_zones" :key="index" class="od_orderzone" >
         <div class="row mb-3">
             <div class="col-8 almarai_700_normal font-14 lcdtgrey d-flex  align-items-center">{{orderzone.name}}</div>
             <div class="col-2 almarai_700_normal font-14 d-flex justify-content-end align-items-center">{{sumZoneH(orderzone)}}H</div>
             <div class="col-2 almarai_700_normal font-14 d-flex justify-content-end align-items-center">{{formatPrice(sumZoneTotal(orderzone))}}</div>
        </div>
        <div class="row mb-2">
             <div class="col-4"></div>
             <div class="col-8">
                <div class="row flex-grow-1 justify-content-between">
                    <div class="col-2 almarai-light font-12 d-flex lcdtgrey justify-content-end align-items-center"></div>
                    <div class="col-4 almarai-light font-12 d-flex lcdtgrey justify-content-center align-items-center">Ouvrages</div>
                    <div class="col-3 almarai-light font-12 d-flex lcdtgrey justify-content-end align-items-center">Main-d'œuvre</div>
                    <div class="col-3 almarai-light font-12 d-flex lcdtgrey justify-content-end align-items-center">Total</div>
                </div>
             </div>
        </div>
         <div class="row" v-for="groupedOrderOuvrage,index2 in orderzone.groupedOrderOuvrage" :key="index2">
             <div class="col-4 od_catname font-14 d-flex align-items-center almarai_700_normal">{{index2}}</div>
             <div class="col-8 font-14 flex-column">
                 <div class="row flex-grow-1 justify-content-between" v-for="orderOuvrage,index3 in groupedOrderOuvrage" :key="index3">
                     <div class="col-2 d-flex align-items-center justify-content-end almarai-light">{{orderOuvrage.qty}}</div>
                     <div class="col-4 d-flex align-items-center almarai-light">{{orderOuvrage.name}}</div>
                    <div class="col-3 almarai-light font-14 d-flex  justify-content-end align-items-center">{{orderOuvrage.nbheure}}H</div>
                    <div class="col-3 almarai-light font-14 d-flex  justify-content-end align-items-center">{{formatPrice(orderOuvrage.total)}}</div>
                 </div>

             </div>
           
        </div>
      
     </div>
     </template>
     <template v-if="order.order_state_id==4">
        <div class="od_invoices">
            <div class="row">
                <div class="col-4 almarai_700_normal font-14 lcdtgrey d-flex align-items-center">Facturation</div>
                <div class="col-2 almarai_700_normal font-14 d-flex align-items-center">{{facturation_total_taux}}%</div>
                <div class="col-2"></div>
                <div class="col-2  almarai_700_normal font-14 d-flex align-items-center justify-content-end">{{formatPrice(facturation_total)}}</div>
                <div class="col-1"></div>
                <div class="col-1"></div>
            </div>
            <div class="row">
                <div class="col-4"></div>
                <div class="col-2 almarai-light lcdtgrey d-flex font-12 align-items-center">Taux</div>
                <div class="col-2 almarai-light lcdtgrey  d-flex font-12 align-items-center justify-content-center">Date</div>
                <div class="col-2 almarai-light lcdtgrey  d-flex font-12 align-items-center justify-content-end">Total</div>
                <div class="col-1"></div>
                <div class="col-1"></div>
            </div>

            <div class="d-flex justify-content-evenly mt-4">
                <span v-if="order.total>0&&facturation_total_taux<100" class="font-14 mulish_600_normal facture_action noselect" @click="new_echeance"><icon name="plus-circle" /> AJOUTER ÉCHEANCE</span>
                <span class="font-14 mulish_600_normal facture_action  noselect" @click="new_remise" ><icon name="plus-circle" /> AJOUTER REMISE</span>
               <span class="font-14 mulish_600_normal facture_action  noselect" @click="new_avoir" ><icon name="plus-circle" /> AJOUTER AVOIR</span>
            </div>    
        </div>
     </template>
     <div class="od_actions mb-3" v-if="show">
        <button class="btn btn-outline-dark almarai_700_normal" @click="goto()">Editer</button>
        <button v-if="order.order_state_id!=4" class="btn btn-outline-success almarai_700_normal" @click="changeOrderState(4)">Gagne</button>
        <button v-if="order.order_state_id!=4" class="btn btn-outline-secondary almarai_700_normal"  @click="changeOrderState(20)">Perdu</button>  
        <button v-if="order.order_state_id!=4" class="btn btn-outline-primary almarai_700_normal"  @click="changeOrderState(18)">Abandonne</button>  
        <button v-if="order.order_state_id!=4" class="btn btn-outline-warning almarai_700_normal"  @click="changeOrderState(3)">Attente client</button>   
     </div>


</item-detail-panel>

<simple-modal-popup v-model="showmodal_facturation" :title="modal_facturation_title" @modalconfirm="newOrderInvoice">
    <div class="container-fluid">
<div class="row mb-3">
    <div class="col-6">Description</div><div class="col-6"><input type="text" v-model="facture.description" class="input-text"/></div>
</div>
<div class="row mb-3">
    <div class="col-6">Taux</div><div class="col-6"><input type="text" v-model="facture.taux" @keyup="checktaux" @blur="addper" class="input-text" ></div> 
</div>
<div class="row mb-3">
    <div class="col-6">Date</div><div class="col-6"><date-picker :disabledToDate="disabledToDate" name="echeance" :droppos="{top:'40px',right:'auto',bottom:'auto',left:'0',transformOrigin:'top center'}"></date-picker></div>
</div>
<div class="row mb-3">
    <div class="col-6">Montant</div><div class="col-6"> <money3 v-model="facture.montant" v-bind="moneyconfig" @keyup="checkmontant"></money3>
    <pre>
    {{facture}}</pre>
     </div>
</div>
    </div>
     
</simple-modal-popup>
</template>

<script>
import { computed, onMounted, ref,h, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router'
import { useStore } from 'vuex';
import ItemDetailPanel from '../../components/miscellaneous/ItemListTable/ItemDetailPanel.vue'
import OrderStateTag from '../../components/miscellaneous/OrderStateTag.vue';
import { DEVIS_DETAIL_GET, DEVIS_DETAIL_LOAD, DEVIS_DETAIL_MODULE, DEVIS_DETAIL_SET, DEVIS_DETAIL_UPDATE_ORDER_STATE, ITEM_LIST_MODULE, ITEM_LIST_UPDATE_ROW, ORDERSTATETAG_GET_ORDER_STATES, ORDERSTATETAG_MODULE } from '../../store/types/types';
import { formatPrice,formatDate,br } from '../../components/helpers/helpers';
import Swal from 'sweetalert2';
import DatePicker from '../../components/miscellaneous/DatePicker.vue';
 import { Money3Component } from 'v-money3';
import { mask } from 'vue-the-mask';


    export default {
        name: "DevisDetail",
        components:{ItemDetailPanel,OrderStateTag, DatePicker,money3:Money3Component},
        directives:{mask},
        setup(){
            const route=useRoute();
            const router=useRouter();
            const store=useStore();
            const show=ref(false);
            const showloader=ref(false);
            const disabledToDate=ref('');
            const facturation_total_taux=ref(50);
            const facturation_total=ref(890.40);
            disabledToDate.value= new Date(new Date().getTime() - 24*60*60*1000).toJSON().slice(0,10);
            let order_id=route.params.id;
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
        
                        const facture=ref({
                            invoice_type_id:1,
                            description:'',
                            taux:'',
                            date:'',
                            montant:''
                        })         

            onMounted(()=>{
                     document.getElementsByTagName( 'body' )[0].className='hide-overflowY';
                show.value=false;
                showloader.value=true;
                store.commit(`${DEVIS_DETAIL_MODULE}${DEVIS_DETAIL_SET}`,{})
                store.dispatch(`${DEVIS_DETAIL_MODULE}${DEVIS_DETAIL_LOAD}`,order_id).then((response)=>{
                    show.value=true;
                    showloader.value=false;
                });
            })
            const order=computed(()=>store.getters[`${DEVIS_DETAIL_MODULE}${DEVIS_DETAIL_GET}`]);

             const order_states=computed(()=>store.getters[`${ORDERSTATETAG_MODULE}${ORDERSTATETAG_GET_ORDER_STATES}`]);

            const changeOrderState = (order_state_id)=>{
                const order_state=order_states.value.filter(obj=>obj.id==order_state_id);
                Swal.fire({
                    title: 'Veuillez confirmer!',
                    text: `Voulez-vous changer le statut en ${typeof order_state[0]!="undefined"?order_state[0].name:''}?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#42A71E',
                    cancelButtonColor: 'var(--lcdtOrange)',
                    cancelButtonText: 'Annuler',
                    confirmButtonText: `Oui, s'il vous plaît.`
                }).then((result) => {
                     
                    if (result.isConfirmed) {
                        showloader.value=true;
                        store.dispatch(`${DEVIS_DETAIL_MODULE}${DEVIS_DETAIL_UPDATE_ORDER_STATE}`,order_state_id).then(response=>{
                            showloader.value=false;
                            store.commit(`${ITEM_LIST_MODULE}${ITEM_LIST_UPDATE_ROW}`,{id:'id',idValue:order_id,colName:'order_state_id',colValue:order_state_id});
                        })
                    }
                });      
            }

            const goto=()=>{
                document.getElementsByTagName( 'body' )[0].className='';
                router.push({ name: 'EditDevis', params: { id: order_id } })
            }
            const sumZoneH=(orderzone)=>{
                let sum=0;
                for(const i in orderzone.groupedOrderOuvrage){
                    sum+=orderzone.groupedOrderOuvrage[i].reduce((a, b) =>  a + b.nbheure, 0);
                }
                return sum;
            }
            const sumZoneTotal=(orderzone)=>{
                  let sum=0;
                for(const i in orderzone.groupedOrderOuvrage){
                    sum+=orderzone.groupedOrderOuvrage[i].reduce((a, b) =>  a + b.total, 0);
                }
                return sum;
            }
            const showmodal_facturation=ref(false);
            const modal_facturation_title=ref('');

           /* watch(()=>facture.value.montant,(current_val, previous_val)=>{
                                if(facture.value.invoice_type_id==1){
                                        let montant=parseFloat(current_val);
                                        console.log(montant);
                                }   
    
            },{deep:true});
    */
            const new_echeance=()=>{
                modal_facturation_title.value='Nouvelle échéance';
                showmodal_facturation.value=true;
                facture.value.invoice_type_id=1;
       
                let taux=(100-facturation_total_taux.value);
                facture.value.taux=`${taux}%`;
                facture.value.montant=(order.value.total/100)*taux;
                facture.value.description='Lancement reparation';
                console.log(facture);
                        }
            const new_remise=()=>{
                modal_facturation_title.value='Nouvelle remise';
                showmodal_facturation.value=true;
                facture.value.invoice_type_id=2;
                facture.value.montant='12';
                facture.value.taux='50';
                facture.value.description='Lancement reparation';
                        }
            const new_avoir=()=>{
                modal_facturation_title.value='Nouveau avoir';
                showmodal_facturation.value=true;
                facture.value.invoice_type_id=3;
                facture.value.montant='12';
                facture.value.taux='50%';
                facture.value.description='Lancement reparation';
                        }
            const newOrderInvoice=()=>{
                showmodal_facturation.value=false;
                console.log('confirmed');
            }   
            const checktaux=(event)=>{
              
                let allowedkey=['0','1','2','3','4','5','6','7','8','9','.','Enter','Backspace','Delete'].filter(key=>key==event.key);
                if(allowedkey.length>0){
                    if(facture.value.invoice_type_id==1){
                        let taux=parseFloat(facture.value.taux);
                     
                        if(taux>(100-facturation_total_taux.value))
                        taux=100-facturation_total_taux.value;
                           

                        facture.value.taux=isNaN(taux)?'':`${taux}`;
                        facture.value.montant=isNaN(taux)?'':(order.value.total/100)*taux;
                    }
                     if(facture.value.invoice_type_id==2){
                        let taux=parseFloat(facture.value.taux);
                     
                        if(taux>(100-facturation_total_taux.value))
                        taux=100-facturation_total_taux.value;
                           

                        facture.value.taux=isNaN(taux)?'':`${taux}`;
                        facture.value.montant=isNaN(taux)?'':(order.value.total/100)*taux;
                    }
                }
            } 
            const checkmontant=()=>{
                if(facture.value.invoice_type_id==1){
                  
                    let montant=facture.value.montant;
                    if(montant>(order.value.total-facturation_total.value)){
                          montant=order.value.total-facturation_total.value;  
                            
                    }
                    facture.value.montant=montant;
                    let taux=(montant/order.value.total)*100;
                    facture.value.taux=`${taux.toFixed(2)}%`;
                }

            }
            const addper=()=>{
                  if(facture.value.invoice_type_id==1){
                    facture.value.taux=`${facture.value.taux}%`;
                  }
            }
             return {
                 show,
                 showloader,
                 order_id,
                 order,
                 formatPrice,
                 formatDate,
                 br,
                 router,
                 changeOrderState,
                 goto,
                 sumZoneH,
                 sumZoneTotal,
                 new_echeance,
                 new_remise,
                 new_avoir,
                 showmodal_facturation,
                 moneyconfig,
                 facture,
                 newOrderInvoice,
                 modal_facturation_title,
                 disabledToDate,
                 facturation_total_taux,
                 facturation_total,
                 checktaux,
                 checkmontant,
                 addper
    

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
.od_orderzone,.od_invoices{
    background: #F8F8F8;
    display: block;
    margin-bottom:22px;
    padding:10px 20px;
    box-shadow: inset 0px -1px 0px rgba(168, 168, 168, 0.25);
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
.od_actions button{

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
</style>