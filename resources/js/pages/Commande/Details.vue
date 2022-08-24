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
        <div class="col"><span class="subtitle almarai_700_normal">Contact</span><br><span v-if="order.contact!=null" v-html="`${typeof order.contact.firstname !='undefined'? order.contact.firstname:''} ${typeof order.contact.name !='undefined'?order.contact.name:''}${br(order.contact.mobile)}${br(order.contact.telephone)}`"></span><span v-else>Pas de contact</span></div>
        <div class="col"><span class="subtitle almarai_700_normal">Mode de paiement</span><br><span>{{order.mode_paiements.join(", ")}}</span></div>
    </div>
     <hr v-if="show"/>
     <template v-if="typeof order.state!='undefined'&&order.state.order_type=='DEVIS'">
     <mini-panel  v-for="orderzone,index in order.order_zones" :key="index"  >
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
      
     </mini-panel>
     </template>
         <transition
                        enter-active-class="animate__animated animate__fadeIn"
                        leave-active-class="animate__animated animate__fadeOut"
                >
     <template v-if="typeof order.state!='undefined'&&order.state.order_type=='COMMANDE'">
     
        <mini-panel >
            <div class="row">
                <div class="col-4 almarai_700_normal font-14 lcdtgrey d-flex align-items-center">Facturation</div>
                <div class="col-2 almarai_700_normal font-14 d-flex align-items-center">{{facturation_total_taux.toFixed(2)}}%</div>
                <div class="col-2"></div>
                <div class="col-2  almarai_700_normal font-14 d-flex align-items-center justify-content-end">{{formatPrice(order.total)}}</div>
                <div class="col-1"></div>
                <div class="col-1"></div>
            </div>
            <div class="row mb-3">
                <div class="col-4"></div>
                <div class="col-2 almarai-light lcdtgrey d-flex font-12 align-items-center">Taux</div>
                <div class="col-2 almarai-light lcdtgrey  d-flex font-12 align-items-center justify-content-center">Date</div>
                <div class="col-2 almarai-light lcdtgrey  d-flex font-12 align-items-center justify-content-end">Total</div>
                <div class="col-1"></div>
                <div class="col-1"></div>
            </div>
                <transition-group tag="div" class="list"  name="list" appear>
            <template v-for="facture,index in facturations" :key="index">
                        <div class="row mb-3" :class="{avoir:facture.invoice_type_name=='AVOIR',remise:facture.invoice_type_name=='REMISE',facturer:(facture.invoice_type_name=='FACTURE'&&facture.facturer==1||facture.invoice_type_name==null&&facture.facturer==1),avenant:facture.invoice_type_name=='AVENANT',reste_a_facturer:facture.facturer==0}" >
                            <div class="col-4 almarai_bold_normal font-14 invoiceline d-inline align-items-center" >{{facture.description}} <span @click=" router.push({name:'FactureDetail', params: { id:facture.invoice_id }});" class="btn-link font-12 cursor-pointer text-nowrap" v-if="facture.invoice_id>0&&facture.invoice_type_name!='REMISE'">(N°{{facture.ref}})</span></div>
                            <div class="col-2 almarai-light  d-flex font-14 align-items-center" :class="{dangerred:facture.sign==-1}">{{isFloat(facture.pourcentage)?facture.pourcentage.toFixed(2):facture.pourcentage}}%</div>
                            <div class="col-2 almarai-light   d-flex font-14 align-items-center justify-content-center">{{formatDate(facture.dateinvoice)}}</div>
                            <div class="col-2 almarai-light   d-flex font-14 align-items-center justify-content-end" :class="{dangerred:facture.sign==-1}">{{formatPrice(facture.sign!=null?facture.sign*facture.montant:facture.montant)}}</div>
                            <div class="col-1 d-flex align-items-center justify-content-center"> <transition
                        enter-active-class="animate__animated animate__fadeIn"
                        leave-active-class="animate__animated animate__fadeOut"
                ><icon v-if="(facture.invoice_type_name=='FACTURE'&&facture.facturer==0||facture.invoice_type_name==null&&facture.facturer==0)" name="plus-circle" width="16px" height="16px" class="cursor-pointer" @click="createInvoice(facture)"></icon></transition></div>
                            <div class="col-1 d-flex align-items-center justify-content-center"> <transition
                        enter-active-class="animate__animated animate__fadeIn"
                        leave-active-class="animate__animated animate__fadeOut"
                ><icon v-if="((facture.invoice_type_name=='AVOIR')&&facture.facturer==0||(facture.invoice_type_name=='FACTURE')&&facture.facturer==0||facture.invoice_type_name==null&&facture.facturer==0||facture.invoice_type_name=='REMISE')&&(facture.invoice_state_id==1||facture.invoice_state_id==null)" name="trash-x" width="20px" height="20px" class="cursor-pointer" @click="removeInvoice(facture)"></icon></transition></div>  
                        </div>
                        </template>
                            </transition-group>
  <transition
                        enter-active-class="animate__animated animate__fadeIn"
                        leave-active-class="animate__animated animate__fadeOut"
                >
                            <div v-if="showresteafacturer" class="d-flex justify-content-center align-items-center lcdtgrey font-12">
                                <div>Reste à facturer: {{formatPrice(reste_a_facturer)}}</div>
                            </div>
  </transition>
            <transition
                        enter-active-class="animate__animated animate__fadeIn"
                        leave-active-class="animate__animated animate__fadeOut"
                >
                        <div class="d-flex justify-content-evenly mt-4" v-if="showFactureBtn">
                            <span v-if="order.total>0&&facturation_total_taux<100" class="font-14 mulish_600_normal facture_action noselect" @click="new_echeance"><icon name="plus-circle" width="16px" height="16px" /> AJOUTER ÉCHEANCE</span>
                            <span v-if="reste_a_facturer>0" class="font-14 mulish_600_normal facture_action  noselect" @click="new_remise" ><icon name="plus-circle" width="16px" height="16px"/> AJOUTER REMISE</span>
                        <span class="font-14 mulish_600_normal facture_action  noselect" @click="new_avoir" v-if="total_facture>0" ><icon name="plus-circle" width="16px" height="16px"/> AJOUTER AVOIR</span>
                        </div>    
                </transition>
        </mini-panel>
     
     </template>
         </transition>
       
    <transition
        enter-active-class="animate__animated animate__fadeIn"
        leave-active-class="animate__animated animate__fadeOut"
    >
                
        <order-documents v-if="typeof order!='undefined'&& typeof order.state!='undefined'" :order_id="order.id"></order-documents>
    </transition>

    <pointage-list @trigger="TriggerNewPointage" :orderId="order_id" />


    <div class="od_actions mb-3" v-if="show">
        <button class="btn btn-outline-dark almarai_700_normal" @click="goto()">Editer</button>
        <button v-if="order.order_state_id!=4" class="btn btn-outline-success almarai_700_normal" @click.once="changeOrderState(4)">Gagne</button>
        <button v-if="order.order_state_id!=4 && order.order_state_id!=20" class="btn btn-outline-secondary almarai_700_normal" @click.once="changeOrderState(20)">Perdu</button>  
        <button v-if="order.order_state_id!=4 && order.order_state_id!=18" class="btn btn-outline-primary almarai_700_normal"  @click.once="changeOrderState(18)">Abandonne</button>  
        <button v-if="order.order_state_id!=4 && order.order_state_id!=3" class="btn btn-outline-warning almarai_700_normal"  @click.once="changeOrderState(3)">Attente client</button>   
    </div>


</item-detail-panel>

<simple-modal-popup v-model="modal.show" :title="modal.title" @modalconfirm="commitAction" @modalclose="closemodal">
    
    
    <template v-if="modal.type == 'facturation'">
        
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-6">Description</div><div class="col-6"><input type="text" v-model="facture.description" class="input-text"/></div>
            </div>
            <div class="row mb-3" v-if="order.total>0">
                <div class="col-6">Taux</div><div class="col-6"><input type="text" v-model="facture.taux" @keyup="checktaux" @blur="addper" class="input-text" ></div> 
            </div>
            <div class="row mb-3" v-if="facture.invoice_type_id!=2">
                <div class="col-6">Date</div><div class="col-6"><date-picker @changed="setEcheanceDate" :disabledToDate="disabledToDate" name="echeance" :droppos="{top:'40px',right:'auto',bottom:'auto',left:'0',transformOrigin:'top center'}"></date-picker></div>
            </div>
            <div class="row mb-3">
                <div class="col-6">Montant</div><div class="col-6"> <money3 v-model="facture.montant" v-bind="moneyconfig" @keyup="checkmontant"></money3>

                </div>
            </div>
        </div>
    
    </template>


    <template v-if="modal.type == 'pointage'">

        <div class="container">

            <div class="row mb-3">
                <div class="col-6">
                    <label>TYPE POINTAGE*</label>
                    <select-box
                        v-model="newPointage.type" 
                        placeholder="Intervention" 
                        :options="pointageTypes" 
                        name="pointageTypes" 
                    />    
                </div>

                <div class="col-6">

                    <label for="">DATE*</label>
                    <date-picker 
                        @changed="setPointageDate" 
                        :disabledToDate="disabledToDate" 
                        name="pointage" 
                        :droppos="{top:'40px',right:'auto',bottom:'auto',left:'0',transformOrigin:'top center'}">
                    </date-picker>

                </div>

            </div>

            <div class="row mb-3">

                <div class="col-6">
                    <label for="">PERSONNEL*</label>
                    <select-box
                        v-model="newPointage.personnel" 
                        placeholder="Personnel"
                        :options="personnelList" 
                        name="personnelList" 
                    />    
                </div> 


                <div class="col-6">

                    <label for="">NOMRE HEURE *</label>
                    <input type="text" v-model="newPointage.numberh" style="padding: 5px;">
                
                </div>
                

            </div>

            <div class="row mb-3">

                <label for="">NOTES / INFORMATIONS / COMMENTAIRES</label>
                <textarea name="comment" v-model="newPointage.comment" style="margin-left: 0.8rem; box-sizing: border-box; width: 89%;"></textarea>
            
            </div>
        
        </div>

    </template>
     
</simple-modal-popup>


</template>

<script>
import moment from 'moment'
import { computed, onMounted, ref, unref, h, watch, reactive } from 'vue';
import { useRoute, useRouter } from 'vue-router'
import { useStore } from 'vuex';
import ItemDetailPanel from '../../components/miscellaneous/ItemListTable/ItemDetailPanel.vue'
import OrderStateTag from '../../components/miscellaneous/OrderStateTag.vue';
import { 
    COMMANDE_DETAIL_LOAD_POINTAGE, 
    COMMANDE_DETAIL_MODULE, 
    DEVIS_DETAIL_CREATE_FACTURATION, 
    DEVIS_DETAIL_GET, 
    DEVIS_DETAIL_GET_FACTURATION, 
    DEVIS_DETAIL_LOAD, 
    DEVIS_DETAIL_LOAD_FACTURATION, 
    DEVIS_DETAIL_MODULE, 
    DEVIS_DETAIL_NEW_FACTURATION, 
    DEVIS_DETAIL_REMOVE_FACTURATION, 
    DEVIS_DETAIL_SET, 
    DEVIS_DETAIL_SET_FACTURATION, 
    DEVIS_DETAIL_UPDATE_ORDER_STATE, 
    ITEM_LIST_MODULE, 
    ITEM_LIST_UPDATE_ROW, 
    ORDERSTATETAG_GET_ORDER_STATES, 
    ORDERSTATETAG_MODULE, 
    TOASTER_CLEAR_TOASTS, 
    TOASTER_MESSAGE, 
    TOASTER_MODULE, 
    GET_POINTAGE_TYPES,
    GET_PERSONNEL_LIST,
    LOADER_MODULE,
    HIDE_LOADER,
    DISPLAY_LOADER,
    COMMANDE_CREATE_POINTAGE

} from '../../store/types/types';
import { formatPrice,formatDate,br,isFloat } from '../../components/helpers/helpers';
import Swal from 'sweetalert2';
import DatePicker from '../../components/miscellaneous/DatePicker.vue';
 import { Money3Component } from 'v-money3';
import { mask } from 'vue-the-mask';
import Icon from '../../components/miscellaneous/Icon.vue';
import OrderDocuments from '../../components/miscellaneous/OrderDocuments.vue'
import MiniPanel from '../../components/miscellaneous/MiniPanel.vue'
import pointageList from '../../components/Commande/pointage-list.vue'

    export default {

        name: "CommandeDetail",

        components: { ItemDetailPanel, OrderStateTag, DatePicker, money3: Money3Component, Icon, OrderDocuments, MiniPanel, pointageList },
        
        directives: { mask },

        setup(){

            const route=useRoute();
            const router=useRouter();
            const store=useStore();
            const show=ref(false);
            const showloader=ref(false);
            const disabledToDate=ref('');
            const facturation_total_taux=ref(0);
            const facturation_total=ref(0);
            const avoir_total=ref(0);
            const avoir_total_taux=ref(0);
            const showFactureBtn=ref(false);
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
                montant:0
            }) 

            const showresteafacturer=ref(false);            
            const orderObj=ref({});                 
            const reste_a_facturer=ref(0);
            const total_remise=ref(0);
            const total_facture=ref(0);
            const total_taux_facture=ref(0);
            const order_total=ref(0);

            const newPointage = reactive({
                type: 0,
                personnel: 0,
                date: null,
                numberh: null,
                comment: null,
            })

            const modal = reactive({
                show: false,
                title: '',
                type: 'facturation'
            })

            watch(()=>orderObj.value,(current_val,previous_val)=>{
                order_total.value=current_val.value.total;
                 reste_a_facturer.value=current_val.value.total-total_remise.value-total_facture.value;
            },{
                deep:true
            })

            watch(()=>total_remise.value,(current_val,previous_val)=>{
                reste_a_facturer.value=order_total.value-current_val-total_facture.value;
            });
            watch(()=>total_facture.value,(current_val,previous_val)=>{
                reste_a_facturer.value=order_total.value-total_remise.value-current_val;
               
            });



            onMounted(()=>{
                store.commit(`${DEVIS_DETAIL_MODULE}${DEVIS_DETAIL_SET_FACTURATION}`,[]);
                     
                show.value=false;
                showloader.value=true;
                store.commit(`${DEVIS_DETAIL_MODULE}${DEVIS_DETAIL_SET}`,{})
                store.dispatch(`${DEVIS_DETAIL_MODULE}${DEVIS_DETAIL_LOAD}`,order_id).then((response)=>{
                    show.value=true;
               
                    if(response.data.state.order_type=='COMMANDE'){
                        store.dispatch(`${DEVIS_DETAIL_MODULE}${DEVIS_DETAIL_LOAD_FACTURATION}`).then((response)=>{
                            showloader.value=false;
                            showresteafacturer.value=true;
                            showFactureBtn.value=true;
                        });
                    }else{
                        showloader.value=false;
                    }
              
                });

                getPointage(order_id)
                
            })
            const order=computed(()=>store.getters[`${DEVIS_DETAIL_MODULE}${DEVIS_DETAIL_GET}`]);
            orderObj.value=computed(()=>store.getters[`${DEVIS_DETAIL_MODULE}${DEVIS_DETAIL_GET}`]);
            const facturations=computed(()=>store.getters[`${DEVIS_DETAIL_MODULE}${DEVIS_DETAIL_GET_FACTURATION}`]);

            const order_states=computed(()=>store.getters[`${ORDERSTATETAG_MODULE}${ORDERSTATETAG_GET_ORDER_STATES}`]);

            const pointages = computed(() => store.getters[`${COMMANDE_DETAIL_MODULE}pointages`])

            const pointageTotal = computed(() => {
                return pointages.value.map(pointage => pointage.numberh)
                                    .reduce((newValue, oldValue) => +newValue + +oldValue, 0)
            })

            const pointageTypes = computed(() => {
                return store.getters[`${COMMANDE_DETAIL_MODULE}pointageTypes`]?.map(type => {
                    return {
                        value: type.id,
                        display: type.name
                    }
                })
            })

            const personnelList = computed(() => {
                return store.getters[`${COMMANDE_DETAIL_MODULE}personnelList`]?.map(person => {
                    return {
                        value: person.id,
                        display: person.name
                    }
                })
            })

            const getPointage = async (id) => {

                try {
                    await store.dispatch(`${COMMANDE_DETAIL_MODULE}${COMMANDE_DETAIL_LOAD_POINTAGE}`, { id, take: 3 })
                }
                catch(e) {
                    throw e
                }
                
            }

            const setPointageDate = (date) => {
                newPointage.date = date.date
            }

            const getPersonnelList = () => {
                try {
                    store.dispatch(`${COMMANDE_DETAIL_MODULE}${GET_PERSONNEL_LIST}`, order.value.affiliate_id)
                }
                catch(e) {
                    throw e
                }
            }

            const getPointageTypes = () => {

                try {
                    store.dispatch(`${COMMANDE_DETAIL_MODULE}${GET_POINTAGE_TYPES}`)
                }
                catch(e) {
                    throw e
                }

            }


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
                            if(order_state_id==4)//gagne
                            router.push({name:'commande-details',params:{id:order_id}});
                        })
                    }
                });      
            }
            watch(() =>facturations.value, (current_val, previous_val) => {

                //
                    total_facture.value=0;
                    total_taux_facture.value=0;
                    total_remise.value=0;
                    avoir_total.value=0;
                      avoir_total_taux.value=0;
                //    

                let percentage=0;
                let total=0
                for(const i in current_val){
                    if(current_val[i].invoice_type_name=='FACTURE'&&current_val[i].facturer==1){
                    total_facture.value+=current_val[i].montant;
                    total_taux_facture.value+=current_val[i].pourcentage;
                    }
                    if(current_val[i].invoice_type_name=='REMISE'){
                    total_remise.value+=current_val[i].montant;
                    }
                    if(current_val[i].invoice_type_name=='AVOIR'){
                    avoir_total.value+=current_val[i].montant;
                      avoir_total_taux.value+=current_val[i].pourcentage;
                    }
                    

                    if(current_val[i].sign==-1){
                        total-=current_val[i].montant;
                       // percentage-=current_val[i].pourcentage;
                    }else{
                        total+=current_val[i].montant;  
                        percentage+=current_val[i].pourcentage; 
                    }
                }
                facturation_total_taux.value=percentage;
                facturation_total.value=total;
              
            },{
                deep:true
            });
            
            const goto=()=>{
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

            const new_echeance = () => {
                modal.title = 'Nouvelle échéance';
                facture.value.description='';
                facture.value.taux=0;
                facture.value.montant=0;
                facture.value.date='';
                modal.show = true;
                facture.value.invoice_type_id=1;
       
                let taux=(100-facturation_total_taux.value);
                let montant=reste_a_facturer.value/(100-total_taux_facture.value)*taux;
                facture.value.taux=`${Math.abs(taux)}%`;
                facture.value.montant=Math.abs(montant);
                
      
            }
            const new_remise = ()=> {
                modal.title='Nouvelle remise';
                facture.value.description='';
                facture.value.taux=0;
                facture.value.montant=0;
                 facture.value.date='';
                modal.show = true;
                facture.value.invoice_type_id=2;
                if(order.value.total>0){
                    let taux=(100-facturation_total_taux.value);
                   // facture.value.taux=`${Math.abs(taux)}%`;
                   // facture.value.montant=Math.abs((order.value.total/100)*taux);
                }
          
            }

            const new_avoir=()=>{
                modal.title='Nouveau avoir';
                facture.value.description='';
                facture.value.taux=0;
                facture.value.montant=0;
                facture.value.date='';
                modal.show = true;
                facture.value.invoice_type_id=3;
                if(order.value.total>0){
                    let taux=(100-facturation_total_taux.value);
                   // facture.value.taux=`${Math.abs(taux)}%`;
                   // facture.value.montant=Math.abs((order.value.total/100)*taux);
                }
               
            }
            
            const newOrderInvoice = ()=> {

                let valide=true
                
                store.commit(`${TOASTER_MODULE}${TOASTER_CLEAR_TOASTS}`)

                if(facture.value.description.trim()=='') {
                    store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                                    type: 'danger',
                                    message: 'Veuillez ajouter une description.',
                                    ttl: 8,
                                });
                                valide=false;
                }
                if(facture.value.date.trim()==''&&facture.value.invoice_type_id!=2) {
                    store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                                    type: 'danger',
                                    message: 'Veuillez saisir une échéance.',
                                    ttl: 8,
                                });
                                valide=false;
                }
                if(facture.value.montant==''){
                    store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                                    type: 'danger',
                                    message: 'Veuillez saisir un montant.',
                                    ttl: 8,
                                });
                                valide=false;
                }
                if(facture.value.taux.trim()==''&&order.value.total>0){
                    store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                                    type: 'danger',
                                    message: 'Veuillez saisir un taux.',
                                    ttl: 8,
                                });
                            valide=false;
                }
                else if(isNaN(parseFloat(facture.value.taux))){
                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                                    type: 'danger',
                                    message: 'Taux invalide.',
                                    ttl: 8,
                                });
                            valide=false;
                }

                if(valide) {
                      if(facture.value.invoice_type_id==1){
                            modal.show = false;
                            showloader.value=true;
                            store.dispatch(`${DEVIS_DETAIL_MODULE}${DEVIS_DETAIL_NEW_FACTURATION}`,facture.value).then(response=>{
                                store.dispatch(`${DEVIS_DETAIL_MODULE}${DEVIS_DETAIL_LOAD_FACTURATION}`).then(response=>{
                                    showloader.value=false;
                                });
                            
                            });
                      }else if(facture.value.invoice_type_id==2){
                            modal.show = false;
                                showloader.value=true;
                                store.dispatch(`${DEVIS_DETAIL_MODULE}${DEVIS_DETAIL_NEW_FACTURATION}`,facture.value).then(response=>{
                                    store.dispatch(`${DEVIS_DETAIL_MODULE}${DEVIS_DETAIL_LOAD_FACTURATION}`).then(response=>{
                                        showloader.value=false;
                                    });
                                
                                });
                      }else if(facture.value.invoice_type_id==3){
                        Swal.fire({
                                title: 'Veuillez confirmer!',
                                text: `Voulez-vous rajouter un avoir? Cette action est irréversible.`,
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#42A71E',
                                cancelButtonColor: 'var(--lcdtOrange)',
                                cancelButtonText: 'Annuler',
                                confirmButtonText: `Oui, s'il vous plaît.`
                            }).then((result) => {
                                modal.show = false;
                                 if (result.isConfirmed) {
                                showloader.value=true;
                                store.dispatch(`${DEVIS_DETAIL_MODULE}${DEVIS_DETAIL_NEW_FACTURATION}`,facture.value).then(response=>{
                                    store.dispatch(`${DEVIS_DETAIL_MODULE}${DEVIS_DETAIL_LOAD_FACTURATION}`).then(response=>{
                                        showloader.value=false;
                                    });
                                
                                });
                                 }
                                
                            });
                      }
                }
            }   

            const checktaux=(event)=>{
   
                let allowedkey=['0','1','2','3','4','5','6','7','8','9','.','Enter','Backspace','Delete'].filter(key=>key==event.key);
                if(allowedkey.length>0){
                    if(facture.value.invoice_type_id==1){
                   
                        let taux=parseFloat(facture.value.taux);
               
                        if(taux>(100-facturation_total_taux.value))
                        taux=100-facturation_total_taux.value;
                     
                        let montant=reste_a_facturer.value/(100-total_taux_facture.value)*taux;

                        facture.value.taux=isNaN(taux)?'':`${Math.abs(taux)}`;
                        facture.value.montant=isNaN(taux)?'':Math.abs(montant);
                    }
                     if(facture.value.invoice_type_id==2){
                        let taux=parseFloat(facture.value.taux);
                 
                        if(taux>(100-total_taux_facture.value))
                        taux=100-total_taux_facture.value;
                       // if(taux==0)
                       // taux=parseFloat(facture.value.taux);
                              let montant=reste_a_facturer.value/(100-total_taux_facture.value)*taux;

                        facture.value.taux=isNaN(taux)?'':`${Math.abs(taux)}`;
                        facture.value.montant=isNaN(taux)?'':Math.abs(montant);
                    }

                    if(facture.value.invoice_type_id==3){
                        let taux=parseFloat(facture.value.taux);
                        if(taux>total_taux_facture.value-avoir_total_taux.value)
                        taux=total_taux_facture.value-avoir_total_taux.value;
                     
                           

                        facture.value.taux=isNaN(taux)?'':`${Math.abs(taux)}`;
                        facture.value.montant=isNaN(taux)?'':Math.abs((order.value.total/100)*taux);
                    }

                    
                }
            } 
          
            const checkmontant=()=>{
                if(facture.value.invoice_type_id==1||((facture.value.invoice_type_id==2||facture.value.invoice_type_id==3))&&order.value.total>0){
        
                    let montant=parseFloat(facture.value.montant);
                    let taux=0;
                    
                    if(facture.value.invoice_type_id==1){
                    if(montant>reste_a_facturer.value.toFixed(2)){
                          montant=reste_a_facturer.value.toFixed(2);  
                    }
                        reste_a_facturer.value/(100-total_taux_facture.value)*taux;
                        taux=(montant*(100-total_taux_facture.value))/reste_a_facturer.value;
                    }

                    if(facture.value.invoice_type_id==2){//Regle 1 : La somme des remises ne doit jamais est superieur a la somme A FACTURER 
                        if(montant>reste_a_facturer.value.toFixed(2)){
                            montant=reste_a_facturer.value.toFixed(2);  
                        }
                        taux=(montant/order.value.total)*100;
                    }
                
                    if(facture.value.invoice_type_id==3){
                    
                        if(montant.toFixed(2)>total_facture.value.toFixed(2)-avoir_total.value){//Regle2 : La somme des avoirs ne doit pas depasser la somme des FACTURES
                            montant=total_facture.value.toFixed(2)-avoir_total.value;  
                     
                        }
                       taux=(montant/order.value.total)*100;
                    }


      
          
                    /*if(montant==0&&(facture.value.invoice_type_id==2||facture.value.invoice_type_id==3)){
                        montant=facture.value.montant;
                    }*/

                    facture.value.montant=Math.abs(montant);
                   
                    facture.value.taux=`${Math.abs(taux).toFixed(2)}%`;
                }

            }
            const addper=()=>{
                  //if(facture.value.invoice_type_id==1){
                    facture.value.taux=`${Math.abs(parseFloat(facture.value.taux))}%`;
                  //}
            }
            const setEcheanceDate=(datepicker)=>{
                    facture.value.date=datepicker.date;
            }
            const closemodal=()=>{
                             store.commit(`${TOASTER_MODULE}${TOASTER_CLEAR_TOASTS}`);
            }
            const createInvoice=(facture)=>{
                   showloader.value=true;
                store.dispatch(`${DEVIS_DETAIL_MODULE}${DEVIS_DETAIL_CREATE_FACTURATION}`,facture).then(()=>{
                    showloader.value=false;
                }).finally(()=>{
                       showloader.value=false;
                })
            
            }
            const removeInvoice=(facture)=>{
                                    showloader.value=true;
                store.dispatch(`${DEVIS_DETAIL_MODULE}${DEVIS_DETAIL_REMOVE_FACTURATION}`,facture).then(()=>{

                     store.dispatch(`${DEVIS_DETAIL_MODULE}${DEVIS_DETAIL_LOAD_FACTURATION}`).then(response=>{
                                        showloader.value=false;
                                    });
                });
            }

            const TriggerNewPointage = () => {
                
                try {
                    
                    getPersonnelList()
                    getPointageTypes()

                    modal.title = 'AJOUTER POINTAGE'
                    modal.show = true
                    modal.type = 'pointage'

                }
                catch(e) {
                    throw e
                }
               
            }

            const commitAction = () => {

                if(modal.type == 'facturation') {
                    newOrderInvoice()
                }

                if(modal.type == 'pointage') {
                    CreateNewPointage()
                }

            }


            const CreateNewPointage = async () => {

                try {
                    
                    let valid = true

                    if(newPointage.type == 0) {
                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                            type: 'danger',
                            message: 'Veuillez ajouter une type pointage.',
                            ttl: 8,
                        });
                        valid = false
                    }

                    if(newPointage.personnel == 0) {
                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                            type: 'danger',
                            message: 'Veuillez ajouter une personnel.',
                            ttl: 8,
                        });
                        valid = false
                    }

                    if(newPointage.date == null) {
                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                            type: 'danger',
                            message: 'Veuillez ajouter une date.',
                            ttl: 8,
                        });
                        valid = false
                    }

                    if(newPointage.numberh == null) {
                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                            type: 'danger',
                            message: 'Veuillez ajouter une NOMRE.',
                            ttl: 8,
                        });
                        valid = false
                    }

                    if(valid == false) return


                    showloader.value = true
                    store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'Loading...'])
                    await store.dispatch(`${COMMANDE_DETAIL_MODULE}${COMMANDE_CREATE_POINTAGE}`, {
                        pointage: unref(newPointage),
                        affiliate_id: order.value.affiliate_id,
                        order_id: order.value.id,
                        user_id: order.value.responsable_id
                    })
                    modal.show = false

                    newPointage.type = 0
                    newPointage.date = null
                    newPointage.numberh = null
                    newPointage.personnel = 0
                    newPointage.comment = null

                }
                catch(e) {
                    store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                        type: 'danger',
                        message: 'Something went wrong',
                        ttl: 8,
                    })
                    throw e
                }
                finally {
                    store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`)
                    showloader.value = false
                }

            }


             return {
                 TriggerNewPointage,
                 personnelList,
                 pointageTypes,
                 getPointage,
                 getPersonnelList,
                 getPointageTypes,
                 commitAction,
                 newPointage,
                 setPointageDate,
                 pointages,
                 modal,
                 moment,
                 pointageTotal,
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
                 moneyconfig,
                 facture,
                 newOrderInvoice,
                 disabledToDate,
                 facturation_total_taux,
                 showresteafacturer,
                 reste_a_facturer,
                 checktaux,
                 checkmontant,
                 addper,
                 setEcheanceDate,
                 closemodal,
                facturations,
                isFloat,
                showFactureBtn,
                createInvoice,
                removeInvoice,
                total_facture

             }
        }
    }
</script>

<style scoped lang="scss">

.title-bold {
    font-family: 'Almarai Regular';
    font-style: normal;
    font-weight: 700;
    font-size: 14px;
    line-height: 140%;
    display: flex;
    align-items: flex-end;
    color: #000000;
}

.tag {
    text-transform: capitalize;
    background: #DDD;
    border-radius: 70px;
    text-align: center;
    font-size: 12px;
    height: 24px;
    position: relative;
    display: inline-block;
    vertical-align: middle;
    line-height: 24px;
    transition: all 0.5s ease-in;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    padding: 0 10px;
}

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
.invoiceline{
    padding-left: 48px;
    position: relative;
}

.invoiceline::before{
    content: "";
    width: 13px;
    height: 13px;
    background-color:white;
    border-radius: 50%;
    display: block;
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    transition: background-color ease-in-out 0.3s;
}
.reste_a_facturer .invoiceline::before{
    background-color: var(--lcdtOrange);
}
.facturer .invoiceline::before{
    background-color: #78DC70;
}

.remise .invoiceline::before,.avoir .invoiceline::before{
    background-color: rgba(255, 0, 0, 0.7);
}
.dangerred{
    color:rgba(255, 0, 0, 0.7);
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