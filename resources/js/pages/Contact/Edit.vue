<template>
  <router-view>
    <transition enter-active-class="animate__animated animate__fadeIn">
      <div class="container-fluid h-100 bg-color" id="container">
        <main-header />
        <div class="row d-flex align-content-stretch align-items-stretch flex-row hmax main-view-wrap reports-page" style="z-index:100" >
            <side-bar />
            <div class="col main-view container">
                <h1 class="d-flex align-items-center m-0">
                  <span class="action-icon"></span>
                  <span class="ms-3 font-22 almarai_extrabold_normal_normal">EDITION CONTACT</span>
                </h1>
                <ul class="m-0 p-0 breadcrumb mt-3 mb-3" v-if="breadcrumbs.length">
                    <li class="breadcrumb-item almarai-extrabold font-18 cursor-pointer" 
                    v-for="(breadcrumb, index) in breadcrumbs" 
                    @click="goToStep(index)"
                    :key="index">{{ breadcrumb }}</li>
                </ul>    
                <transition name="list" appear v-if="step =='choose_customer'">
                    <div class="col-5 bg-white p-3 rounded">
                        <h2 class="almarai-extrabold font-22">Détail ENTITE <span @click="addNewCustomer" class="ms-3 almarai-bold font-16 cursor-pointer text-decoration-underline text-custom-success">Nouveau</span></h2>
                        <SearchCustomer name="search" @selected="selectedCustomer" :droppos="{top:'auto',right:'auto',bottom:'auto',left:'0',transformOrigin:'top right'}" label="Rechercher ENTITE" ></SearchCustomer>
                    </div>
                </transition>
                <transition name="list" appear  v-if="step == 'create_contact'">
                    <div class="cust-page-content client-detail m-auto">
                        <div class="col-5 p-3 bg-white rounded">
                            <div class="d-flex">
                                <div class="col-8">
                                <h2 class="almarai-extrabold font-22">{{ contact.customer.company }}</h2>
                                <p class="text-gray font-16 almarai-bold">{{ contact.customer.raisonsocial }}</p>
                                </div>
                                <div class="col-4">
                                    <p @click="chooseOtherCustomer" class="text-custom-success font-16 almarai-bold text-decoration-underline cursor-pointer">Autre client</p>
                                </div>
                            </div>
                            <div class="d-flex mt-3">
                                <div class="col-4">
                                <label for="" class="text-gray font-16 almarai-bold">GROUPE</label>
                                <p class="font-16 almarai-bold">{{ contact.customer.group }}</p>
                                </div>
                                <div class="col-4">
                                <label for="" class="text-gray font-16 almarai-bold">CONTACT</label>
                                <p class="font-16 almarai-bold">{{ contact.customer.contact }}</p>
                                </div>
                                <div class="col-4">
                                <label for="" class="text-gray font-16 almarai-bold">TELEPHONE</label>
                                <p class="font-16 almarai-bold">{{ contact.customer.telephone }}</p>
                                </div>
                            </div>
                            <div class="d-flex mt-3">
                                <div class="col-4">
                                <label for="" class="text-gray font-16 almarai-bold">TVA</label>
                                <p class="font-16 almarai-bold">{{ contact.customer.tax }}</p>
                                </div>
                                <div class="col-4">
                                <label for="" class="text-gray font-16 almarai-bold">NAF</label>
                                <p class="font-16 almarai-bold">{{ contact.customer.naf }}</p>
                                </div>
                                <div class="col-4">
                                <label for="" class="text-gray font-16 almarai-bold">SIRET</label>
                                <p class="font-16 almarai-bold">{{ contact.customer.siret }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="page-section bg-white rounded mt-3">
                            <h3 class="m-0 mulish-extrabold font-22">CONTACT</h3>
                            <div class="d-flex mt-3">
                                <div class="col-9"></div>
                                <div class="col-3">
                                    <CheckBox v-model="contact.active" :checked="contact.active" :title="'ACTIF'"></CheckBox>
                                </div>
                            </div>                            
                            <div class="d-flex mt-3">
                                <div class="col-4">
                                    <select-box v-model="contact.type" :options="contactTypes" :name="'contactType'" :label="'TYPE CONTACT *'"></select-box>
                                </div>
                                <div class="col-8 d-flex ps-3">
                                    <div class="col-2 form-group">
                                        <select-box v-model="contact.gender" 
                                            :options="[
                                                { value: 'M', display: 'M' },
                                                { value: 'Mme', display: 'Mme' },
                                                { value: 'Mlle', display: 'Mlle' },
                                            ]" 
                                            :name="'customerGender'"
                                            :label="'&nbsp;'"
                                            ></select-box>
                                    </div>
                                    <div class="col-5 ps-2 form-group">
                                        <label for="nom-client" class="mulish-medium font-16">PRENOM *</label>
                                        <input type="text" class="form-control" v-model="contact.firstName" placeholder="First Name">
                                    </div>
                                    <div class="col-5 ps-2 form-group">
                                        <label class="mulish-medium font-16">NOM *</label>
                                        <input type="text" v-model="contact.name" placeholder="Name" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex mt-3">
                                <div class="col-7">
                                    <select-box v-model="contact.qualite" 
                                        :options="contactQualites" 
                                        :name="'QUANTITE'"
                                        :label="'QUANTITE'"
                                        ></select-box>                                    
                                </div>
                                <div class="col-5 ps-4">
                                    <div class="d-flex">
                                        <div class="phone-country-code">
                                            <select-box 
                                                v-model="contact.phoneCountryCode1" 
                                                :options="phoneCodesSorted"
                                                :styles="{ width: '100px'}"
                                                :label="'&nbsp;'"
                                                :name="'phoneCountryCode'">
                                            </select-box>
                                        </div>
                                        <div class="form-group w-100 ms-2">
                                            <label class="text-uppercase">TELEPHONE FIXE</label>
                                            <input type="text" placeholder="Telephone" v-model="contact.phoneNumber1" class="form-control custom-input">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex mt-3">
                                <div class="col-7">
                                    <select-box v-model="contact.address" 
                                        :options="customerAddresses" 
                                        :name="'ADRESSE_BATIMENTS'"
                                        :label="'ADRESSE / BATIMENTS'"
                                        ></select-box>                                    
                                </div>                                
                                <div class="col-5 ps-4">
                                    <div class="d-flex">
                                        <div class="phone-country-code">
                                            <select-box 
                                                v-model="contact.phoneCountryCode2" 
                                                :options="phoneCodesSorted"
                                                :styles="{ width: '100px'}"
                                                :label="'&nbsp;'"
                                                :name="'phoneCountryCode'">
                                            </select-box>
                                        </div>
                                        <div class="form-group w-100 ms-2">
                                            <label class="text-uppercase">TELEPHONE MOBILE</label>
                                            <input type="text" placeholder="Mobile" v-model="contact.phoneNumber2" class="form-control custom-input">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex mt-3">
                                <div class="col-7">
                                    <div class="form-group">
                                        <label class="mulish-medium font-16">EMAIL*</label>
                                        <input type="text" v-model="contact.email" @change="validationUniqueEmail($event, 'contacts')" placeholder="Email" class="form-control">
                                    </div>
                                </div>                               
                                <div class="col-5 ps-4">
                                    <div class="form-group">
                                        <label class="mulish-medium font-16">PROFIL LINKEDIN</label>
                                        <input type="text" v-model="contact.profilLinedin" placeholder="Profillinedin" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex mt-3">       
                                <div class="col-9">
                                    <div class="d-flex">
                                        <div class="col-7">
                                            <div class="form-group">
                                                <label>COMMENTAIRES</label>
                                                <input type="text" v-model="contact.note" placeholder="comment" class="form-control">
                                            </div>
                                        </div>                                
                                        <div class="col-5 ps-3 d-flex">
                                            <div class="form-group">
                                                <label>NUM-GX</label>
                                                <input type="text" v-model="contact.numGx" class="form-control">
                                            </div>
                                        </div>                                
                                    </div>
                                    <div class="d-flex mt-3">
                                        <div class="col-4">
                                            <CheckBox v-model="contact.acceptSMS" :checked="contact.acceptSMS" :title="'SMS Marketing'"></CheckBox>
                                        </div>
                                        <div class="col-4">
                                            <CheckBox v-model="contact.acceptmarketing" :checked="contact.acceptmarketing" :title="'Email Marketing'"></CheckBox>
                                        </div>
                                        <div class="col-4">
                                            <CheckBox v-model="contact.acceptcourrier" :checked="contact.acceptcourrier" :title="'Courrier Marketing'"></CheckBox>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="btns d-flex justify-content-end mt-3 mb-3">
                            <button class="custom-btn btn-cancel me-3" @click="goToStep(0)">Annuler</button>
                            <button class="custom-btn btn-ok text-uppercase" @click="submit">mettre à jour CONTACT</button>
                        </div>
                    </div>                    
                </transition>
            </div>
        </div>
      </div>
    </transition>
  </router-view>
</template>
<script>
import { ref, onMounted, watchEffect } from 'vue';
import SelectBox from '../../components/miscellaneous/SelectBox';
import SearchCustomer from '../../components/miscellaneous/SearchCustomer';
import { phoneCountryCode as phoneCodes } from '../../static/PhoneCountryCodes';

import {     
  DISPLAY_LOADER,
  HIDE_LOADER,
  LOADER_MODULE,
  TOASTER_MESSAGE,
  TOASTER_MODULE, 
  } from '../../store/types/types';
  
import axios from 'axios';
import { useStore } from 'vuex';
import { useRouter, useRoute } from 'vue-router';

export default {
    components:{
        SelectBox,
        SearchCustomer,
    },
    setup() {
        const store = useStore();
        const router = useRouter();
        const route = useRoute();
        const breadcrumbs = ref(['Choix ENTITE']);
        const step = ref('create_contact');
        const contactTypes = ref([]);
        const contactQualites = ref([]);
        const customerAddresses = ref([]);    
        const contact = ref({
            type: '',
            active: true,
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
        });
        onMounted(()=>{
            axios.post('/contact/edit/'+route.params.id).then((res)=>{
                var contactTmp = res.data;
                if(contactTmp.active){
                    contactTmp.active = true;
                }else{
                    contactTmp.active = false;
                }
                if(contactTmp.acceptSMS){
                    contactTmp.acceptSMS = true;
                }else{
                    contactTmp.acceptSMS = false;
                }
                if(contactTmp.acceptmarketing){
                    contactTmp.acceptmarketing = true;
                }else{
                    contactTmp.acceptmarketing = false;
                }
                if(contactTmp.acceptcourrier){
                    contactTmp.acceptcourrier = true;
                }else{
                    contactTmp.acceptcourrier = false;
                }
                // let telephone = contactTmp.phoneCountryCode1.split("|");
                // if(telephone.length >1){
                //     contactTmp.phoneCountryCode1 = telephone[0];
                // }else{
                //     contactTmp.phoneNumber1 = telephone[1];
                // }
                // let mobile = contactTmp.phoneCountryCode2.split("|");
                // if(mobile.length >1){
                //     contactTmp.phoneCountryCode2 = mobile[0];
                // }else{
                //     contactTmp.phoneNumber2 = mobile[1];
                // }
                contact.value           = contactTmp;
                contactTypes.value      = res.data.contactTypes;
                contactQualites.value   = res.data.contactQualites;
                customerAddresses.value = res.data.customerAddresses;
            }).catch((errors)=>{
                console.log(errors);
            }).finally(()=>{

            })
        })    

        watchEffect(()=>{
            if(step.value == 'choose_customer'){
                breadcrumbs.value = ['Choix ENTITE'];
            }else{
                breadcrumbs.value = ['Choix ENTITE', 'Créer Contact'];
            }
        })            
        const cancel = ()=>{

        }
        const addNewCustomer = ()=>{
            router.push({
                name: "CreateCustomer"
            })
        }        
        const goToStep = (index)=>{
            if(index == 0){
                step.value = 'choose_customer';
            }else{
                step.value = 'create_contact';
            }
        }        
        const submit = ()=>{
            var error = false;
            if(contact.value.type == ''){
                error = true;
                store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                    type: 'danger',
                    message: 'Veuillez sélectionner le type de contact',
                    ttl: 5,
                });  
            }else if(contact.value.firstName == ''){
                error = true;
                store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                    type: 'danger',
                    message: 'Veuillez entrer PRENOM',
                    ttl: 5,
                });                          
            }else if(contact.value.email == ''){
                error = true;
                store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                    type: 'danger',
                    message: 'Veuillez saisir un e-mail',
                    ttl: 5,
                });
            }else if(contact.value.name == ''){
                error = true;
                store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                    type: 'danger',
                    message: 'Veuillez entrer NOM',
                    ttl: 5,
                });                          
            }            
            // loading customer addresses
            if(!error){
                store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'mise à jour d`un contact...']);
                axios.post('/contact/update/'+route.params.id, contact.value).then((res)=>{
                    if(res.data.success){
                        router.push({
                            name: 'contact-details',
                            params: { id: route.params.id }
                        })
                    }else{
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
                }).finally(()=>{
                    store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
                })            
            }
        }
        const selectedCustomer = (data)=>{
            contact.value.customer = data;
            store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'Chargement de l`adresse du client.']);
            axios.post('/get-customer-addresses', { customer_id: data.id }).then((res)=>{
                res.data.forEach(element => {
                    customerAddresses.value.push({
                        display: element.address1 + ', ' + element.postcode + ', ' + element.city,
                        value: element.id
                    });
                });
                // move on to "addess choose step"
                step.value = 'create_contact';
            }).catch((error)=>{
                console.log(error);
            }).finally(()=>{
                store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
            })            
        }        
        const validationUniqueEmail = (event, tableName)=>{
            axios.post('/check-email-exists', { table: tableName, email:  event.target.value })
            .then((res)=>{
                if( !res.data.success ){
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
            [item.value, item])).values()].sort((a, b)=>{
            return parseInt(a.value.replace(/\D/g, '')) - parseInt(b.value.replace(/\D/g, ''));
        });         
        return {
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
            cancel,
            submit
        }
  },
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