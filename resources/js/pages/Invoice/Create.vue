<template>

  <router-view>
    
    <transition enter-active-class="animate__animated animate__fadeIn">

      <div class="container-fluid h-100 bg-color" id="container">

        <main-header />
        
        <div class="row d-flex align-content-stretch align-items-stretch flex-row hmax main-view-wrap reports-page" style="z-index:100" >
            
            <side-bar />

            <div class="col main-view container">
                
                <h1 class="d-flex align-items-center m-0">
                  <icon name="facture" style="width: 45px; height: 45px;" />
                  <span class="ms-3 font-22 almarai_extrabold_normal_normal">CREATION INVOICE / AVOIR</span>
                </h1>

                <ul class="m-0 p-0 breadcrumb mt-3 mb-3" v-if="breadcrumbs.length">
                    <li class="breadcrumb-item almarai-extrabold font-18 cursor-pointer" 
                    v-for="(breadcrumb, index) in breadcrumbs" 
                    @click="goToStep(index)"
                    :key="index">{{ breadcrumb }}</li>
                </ul>   

                <transition name="list" appear v-if="step == 'choose_customer'">

                    <div class="col-6 bg-white p-3 rounded">
                        
                        <h2 
                            class="almarai-extrabold font-22"
                        >
                            Détail ENTITE 
                            <span 
                                @click="addNewCustomer" 
                                class="ms-3 almarai-bold font-16 cursor-pointer text-decoration-underline text-custom-success"
                            >
                                Nouveau
                            </span>
                        </h2>

                        <SearchCustomer 
                            name="search" 
                            @selected="selectedCustomer" 
                            :droppos="{ top: 'auto', right: 'auto', bottom: 'auto', left: '0', transformOrigin: 'top right' }" 
                            label="Rechercher ENTITE" 
                        ></SearchCustomer>
                    
                    </div>

                </transition>


                <transition name="list" appear  v-if="step == 'invoice_type'">

                    <div class="cust-page-content client-detail m-auto">

                        <div class="page-section bg-white rounded mt-3">

                            
                            <h3 class="m-0 mulish-extrabold font-22">TYPE</h3>

                            <div class="d-flex mt-3">

                                <div class="col-4">
                                    
                                    <select-box 
                                        v-model="invoice.type" 
                                        :options="invoiceTypes" 
                                        name="invoiceType" 
                                        label="FACTURE/AVOIR"
                                    ></select-box>
                                
                                </div>

                            </div>

                        </div>

                        <div class="btns d-flex justify-content-end mt-3 mb-3">
                            
                            <button 
                                class="custom-btn btn-cancel me-3" 
                                @click.prevent="goToStep(0)"
                            >
                                Annuler
                            </button>
                            <button 
                                class="custom-btn btn-ok text-uppercase" 
                                @click.prevent="step='choose_invoice_type'"
                            >
                                VALIDER
                            </button>

                        </div>

                    </div>

                </transition>


                <transition name="list" appear  v-if="step == 'choose_invoice_type'">

                    <div class="cust-page-content client-detail m-auto">

                        <div class="page-section bg-white rounded mt-3">
                            
                            <h3 class="m-0 mulish-extrabold font-22">
                                {{ invoice.type?.toUpperCase() }}
                            </h3>
                                
                            <div class="d-flex mt-3">

                                <div class="col-6">
                                    
                                    <SearchInvoice 
                                        name="search" 
                                        @selected="selectedInvoice" 
                                        :droppos="{ top: 'auto', right: 'auto', bottom: 'auto', left: '0', transformOrigin: 'top right' }" 
                                        label="Recherche commande"
                                        :customerId="contact.customer?.id" 
                                    ></SearchInvoice>
                                
                                </div>

                            </div>

                        </div>

                        <div class="btns d-flex justify-content-end mt-3 mb-3">
                            <button class="custom-btn btn-cancel me-3" @click="goToStep(0)">Annuler</button>
                            <button class="custom-btn btn-ok text-uppercase" @click="submit">VALIDER</button>
                        </div>

                    </div>

                </transition>


                <template v-if="step=='invoice_creation'">

                    <invoice-creation />

                </template>

                

            </div>
        </div>

      </div>

    </transition>

  </router-view>
</template>

<script>

import moment from 'moment'
import lodash from 'lodash'    
import { ref, reactive, onMounted, watch, watchEffect } from 'vue'
import SelectBox from '../../components/miscellaneous/SelectBox'
import SearchCustomer from '../../components/miscellaneous/SearchCustomer'
import SearchInvoice from '../../components/miscellaneous/SearchInvoice'
import { phoneCountryCode as phoneCodes } from '../../static/PhoneCountryCodes'
import InvoiceCreation from '../../components/Invoices/Creation.vue'

import {     
  DISPLAY_LOADER,
  HIDE_LOADER,
  LOADER_MODULE,
  TOASTER_MESSAGE,
  TOASTER_MODULE, 
  INVOICE_MODULE,
  CREATE_INVOICE,
  CREATE_NEW_INVOICE
} from '../../store/types/types';
  
import axios from 'axios';
import { useStore } from 'vuex';
import { useRouter } from 'vue-router';

export default {
    
    components:{
        SelectBox,
        SearchCustomer,
        SearchInvoice,
        InvoiceCreation
    },

    setup() {

        const store = useStore();
        const router = useRouter();
        const breadcrumbs = ref(['Choix client']);
        const step = ref('choose_customer');
        const contactTypes = ref([]);
        const contactQualites = ref([]);
        const uniqueEmail = ref({ status: true, msg: '' });
        const customerAddresses = ref([])

        const contact = ref({
            type: '',
            qualite: '',
            gender: 'M',
            firstName: '',
            address: '',
            profilLinedin: '',
            name: '',
            email: '',
            note: '',
            numGx: '',
            phoneCountryCode1: '+33',
            phoneNumber1: '',
            phoneCountryCode2: '+33',
            phoneNumber2: '',
            acceptSMS: true,
            acceptmarketing: true,
            acceptcourrier: true,
            customer: {
                id: 0,
                company: '',
                raisonsocial: '',
                group: '',
                contact: '',
                telephone: '',
                tax: '',
                naf: '',
                siret: '',
            },                
        })

        const invoiceTypes = ref([
            { value: 'avoir', display: 'Avoir' },
            { value: 'facture', display: 'Facture' }
        ])


        const invoice = reactive({
            type: 'avoir',
            id: null,
            orderId: null,
        })

        watchEffect(()=> {

            if(step.value == 'choose_customer'){
                breadcrumbs.value = ['Choix ENTITE'];
            }
            else if(step.value == 'invoice_type') {
                breadcrumbs.value = ['Choix ENTITE', 'Type'];
            }
            else {
                breadcrumbs.value = ['Choix ENTITE', 'Type', invoice.type?.toUpperCase()];
            }

            // if(step.value == 'invoice_creation') selectedInvoice()

        })            
        
        const cancel = ()=> {

        }

        const addNewCustomer = ()=>{
            router.push({
                name: "CreateCustomer"
            })
        }

        const goToStep = (index)=>{
            if(index == 0) {
                step.value = 'choose_customer';
            }else {
                step.value = 'create_contact';
            }
        } 

        const selectedCustomer = (data) => {
            
            contact.value.customer = data
            step.value = 'invoice_type'

        }
        
        const selectedInvoice = async (data) => {

            step.value = 'invoice_creation'    
            invoice.id = data.invoice_id
            invoice.orderId = data.order_id

            try {

                store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'créer un invoice..'])  
                
                await store.dispatch(`${INVOICE_MODULE}${CREATE_INVOICE}`, {
                    customerId: contact.value.customer.id,
                    invoiceId: data.invoice_id,
                    orderId: data.order_id,
                    type: invoice.type
                })

            }

            catch(e) {
                throw e
            }

            finally {
                store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`) 
            }

        }

        const createNewInvoice = async () => {

            step.value = 'invoice_creation'    

            try {

                store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'créer un invoice..'])  
                
                await store.dispatch(`${INVOICE_MODULE}${CREATE_NEW_INVOICE}`, {
                    customerId: contact.value.customer.id,
                    type: invoice.type
                })

            }

            catch(e) {
                throw e
            }

            finally {
                store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`) 
            }

        }

        const validationUniqueEmail = (event, tableName)=>{
            
            axios.post('/check-email-exists', { table: tableName, email:  event.target.value })
            .then((res)=>{
                if( !res.data.success ){
                    uniqueEmail.value.status = false;
                    Object.values(res.data.errors).forEach(item => {
                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                            type: 'danger',
                            message: item[0],
                            ttl: 5,
                        });
                    });                    
                }
            }).catch((error)=>{
                console.log(error);
            })

        }            
        
        const phoneCodesSorted = [...new Map(phoneCodes.map(item =>
            [item.value, item])).values()].sort((a, b) => {
            return parseInt(a.value.replace(/\D/g, '')) - parseInt(b.value.replace(/\D/g, ''));
        })

        watch(invoice, (newInvoice) => {
            console.log(newInvoice, " is value")
            if(newInvoice.type == 'facture') createNewInvoice()
            if(newInvoice.type == 'avoir') step.value = 'choose_invoice_type'
        })

        return {
            invoice,
            invoiceTypes,
            contact,
            step,
            breadcrumbs,
            contactTypes,
            contactQualites,
            customerAddresses,
            phoneCodesSorted,
            goToStep,
            validationUniqueEmail,
            addNewCustomer,
            selectedCustomer,
            selectedInvoice,
            cancel,
            submit
        }

    }
}
</script>
<style>
.dp__active_date{
    background: var(--lcdtOrange) !important;
}
.dp__today{
    border: solid 1px var(--lcdtOrange) !important;
}
</style>
<style lang="scss" scoped>
.main-view{
    padding: 0;
    h1{
        padding: 60px 10px 0 0;
    }
}
.cust-page-content{
  margin-top: 3.125rem;
  .page-section{
    padding: 1.875rem 5rem 1.875rem;
    background: #FFFFFF;
    box-shadow: 0px 0px 4px rgba(80, 80, 80, 0.2);
    border-radius: 4px;
    margin-bottom: 30px;
    input[type="text"]:focus,
    input[type="tel"]:focus,
    input[type="email"]:focus{
        outline: 2px #000000 solid;
        border-color: #000000;
        box-shadow: none;
    }
  }
}
.custom-btn{
    padding: 0 1rem;
    height: 40px;
    font-family: 'Almarai Bold';
    font-style: normal;
    font-weight: 700;
    font-size: 16px;
    line-height: 140%;
    border-radius: 4px;
    text-align: center;
    border: 1px solid #47454B;
    cursor: pointer;
}
.btn-cancel{
    color: rgba(0, 0, 0, 0.2);
}
.btn-ok{
    background: #A1FA9F;
    color: #3E9A4D;
}
.btn-danger{
    background: rgba(255, 0, 0, 0.1);
    color: #E8581B;
}
</style>