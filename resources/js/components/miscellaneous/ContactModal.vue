<template>
    <Teleport to="body">
        <div class="search-layer d-flex align-items-center justify-content-center position-fixed" v-if="showModal">
            <transition name="list" appear>
                <div class="search-panel m-auto bg-white">
                    <div class="search-header d-flex align-items-center justify-content-center position-relative almarai-extrabold font-22">
                        <svg @click="closeModal" class="close-icon cursor-pointer" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M6.78812 5.2973C6.3976 4.90481 5.76444 4.90481 5.37392 5.2973C4.98339 5.6898 4.98339 6.32616 5.37392 6.71865L10.5883 11.9594L5.29289 17.2816C4.90237 17.6741 4.90237 18.3105 5.29289 18.703C5.68341 19.0955 6.31657 19.0955 6.7071 18.703L12.0025 13.3808L17.293 18.6979C17.6835 19.0904 18.3166 19.0904 18.7072 18.6979C19.0977 18.3054 19.0977 17.6691 18.7072 17.2766L13.4167 11.9594L18.6261 6.7237C19.0167 6.33121 19.0167 5.69485 18.6261 5.30235C18.2356 4.90986 17.6025 4.90986 17.2119 5.30235L12.0025 10.5381L6.78812 5.2973Z" fill="black"/>
                        </svg>
                    </div>
                    <div class="search-body rounded">
                        <div class="page-section">
                            <h3 class="m-0 mulish-extrabold font-22">CONTACT</h3>
                            <div class="d-flex mt-3">
                                <div class="col-9"></div>
                                <div class="col-3">
                                    <CheckBox v-model="contact.actif" :checked="true" :title="'ACTIF'"></CheckBox>
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
                                    <div class="d-flex justify-content-between">
                                        <div class="phone-country-code">
                                            <select-box 
                                                v-model="contact.phoneCountryCode1" 
                                                :options="phoneCodesSorted"
                                                :styles="{ width: '100px'}"
                                                :label="'&nbsp;'"
                                                :name="'phoneCountryCode'">
                                            </select-box>
                                        </div>
                                        <div class="form-group ms-2">
                                            <label class="text-uppercase">TELEPHONE FIXE</label>
                                            <input type="text" placeholder="telephone" v-model="contact.phoneNumber1" class="form-control custom-input">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex mt-3">
                                <div class="col-7">
                                    <select-box v-model="contact.address" 
                                        :options="[
                                            { value: 'M', display: 'M' },
                                            { value: 'Mme', display: 'Mme' },
                                            { value: 'Mlle', display: 'Mlle' },
                                        ]" 
                                        :name="'ADRESSE_BATIMENTS'"
                                        :label="'ADRESSE / BATIMENTS'"
                                        ></select-box>                                    
                                </div>                                
                                <div class="col-5 ps-4">
                                    <div class="d-flex justify-content-between">
                                        <div class="phone-country-code">
                                            <select-box 
                                                v-model="contact.phoneCountryCode2" 
                                                :options="phoneCodesSorted"
                                                :styles="{ width: '100px'}"
                                                :label="'&nbsp;'"
                                                :name="'phoneCountryCode'">
                                            </select-box>
                                        </div>
                                        <div class="form-group ms-2">
                                            <label class="text-uppercase">TELEPHONE MOBILE</label>
                                            <input type="text" placeholder="mobile" v-model="contact.phoneNumber2" class="form-control custom-input">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex mt-3">
                                <div class="col-7">
                                    <div class="form-group">
                                        <label class="mulish-medium font-16">EMAIL</label>
                                        <input type="text" v-model="contact.email" @change="validationUniqueEmail($event, 'contacts')" placeholder="email" class="form-control">
                                    </div>
                                </div>                               
                                <div class="col-5 ps-4">
                                    <div class="form-group">
                                        <label class="mulish-medium font-16">PROFIL LINKEDIN</label>
                                        <input type="text" v-model="contact.profilLinedin" placeholder="profillinedin" class="form-control">
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
                                            <CheckBox v-model="contact.acceptSMS" :checked="true" :title="'SMS Marketing'"></CheckBox>
                                        </div>
                                        <div class="col-4">
                                            <CheckBox v-model="contact.acceptmarketing" :checked="true" :title="'Email Marketing'"></CheckBox>
                                        </div>
                                        <div class="col-4">
                                            <CheckBox v-model="contact.acceptcourrier" :checked="true" :title="'Courrier Marketing'"></CheckBox>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="btns mt-4 d-flex justify-content-between">
                            <button class="custom-btn btn-cancel" @click="closeModal">Annuler</button>
                            <button class="custom-btn btn-ok" @click="addNewContact">AJOUTER CONTACT</button>
                        </div>
                    </div>
                </div>
            </transition>
        </div>
    </Teleport>
</template>
<script>

import { onMounted, ref } from 'vue';
import axios from 'axios';
import SelectBox from '../../components/miscellaneous/SelectBox';
import CheckBox from '../../components/miscellaneous/CheckBox';
import { phoneCountryCode as phoneCodes } from '../../static/PhoneCountryCodes';
import {     
  DISPLAY_LOADER,
  HIDE_LOADER,
  LOADER_MODULE,
  TOASTER_MODULE, 
  TOASTER_MESSAGE
  } from '../../store/types/types';
import { useStore } from 'vuex';
  
export default {
    name: 'ContactModal',
    props: {
        // modelValue: Object
    },
    emits: ['addedNewContact'],
    components:{
        SelectBox,
        CheckBox
    },
    setup(props, { emit }){
        const store = useStore();
        const contactTypes = ref([]);
        const contactQualites = ref([]);
        const uniqueEmail = ref(true);
        const customerAddresses = ref([]);
        const contact = ref(
            {
                customerId: '',
                type: '',
                actif: true,
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
            }
        );
        onMounted(()=>{
            axios.post('/get-list-info-for-customer').then((res)=>{
                contactTypes.value    = res.data.contactTypes;
                contactQualites.value    = res.data.contactQualites;
            }).catch((errors)=>{
                console.log(errors);
            }).finally(()=>{

            })            
        })
        const closeModal = ()=>{
            showModal.value = !showModal.value;
        }
        const showModal = ref(false);
        const openModal = (id, addresses)=>{
            contact.value.customerID = id;
            showModal.value = !showModal.value;
            addresses.forEach(element => {
                customerAddresses.value.push({
                    display: element.address1 + ' ' + element.postCode + ' ' + element.city,
                    value: element.id
                });
            });
        }  
        const validationUniqueEmail = (event, tableName)=>{
            axios.post('/check-email-exists', { table: tableName, email:  event.target.value })
            .then((res)=>{
                if( !res.data.success ){
                    uniqueEmail.value = false;
                    Object.values(res.data.errors).forEach(item => {
                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                            type: 'danger',
                            message: item[0],
                            ttl: 5,
                        });
                    });                    
                }else{
                    uniqueEmail.value = true;
                }
            }).catch((error)=>{
                console.log(error);
            })
        }        
        const addNewContact = ()=>{
            var error = false;
            if(contact.value.type == ''){
                error = true;
                store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                    type: 'danger',
                    message: 'Please select contact type',
                    ttl: 5,
                });  
            }else if(contact.value.firstName == ''){
                error = true;
                store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                    type: 'danger',
                    message: 'Please enter PRENOM',
                    ttl: 5,
                });                          
            }else if(contact.value.email == ''){
                error = true;
                store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                    type: 'danger',
                    message: 'Please enter email',
                    ttl: 5,
                });
            }else if(contact.value.name == ''){
                error = true;
                store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                    type: 'danger',
                    message: 'Please enter NOM',
                    ttl: 5,
                });                          
            }            
            // loading customer addresses
            if(!error){
                if(uniqueEmail.value){
                    store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'creating contact..']);
                    axios.post('/add-customer-contact', contact.value).then((res)=>{
                        emit('addedNewContact', {
                            id: res.data.id,
                            name: contact.value.firstName + " " + contact.value.name,
                            qualite: contactQualites.value.find( (item)=>{ item.value = contact.value.qualite } ).display,
                            comment: contact.value.comment,
                            email: contact.value.email,
                            mobile: contact.value.phoneCountryCode1 + ' ' + contact.value.phoneNumber1,
                        });
                        showModal.value = false;                
                    }).catch((error)=>{
                        console.log(error);
                    }).finally(()=>{
                        store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
                    })            
                }else{
                    store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                        type: 'danger',
                        message: 'Email has already been taken',
                        ttl: 5,
                    });                
                }
            }
        }

        const phoneCodesSorted = [...new Map(phoneCodes.map(item =>
            [item.value, item])).values()].sort((a, b)=>{
            return parseInt(a.value.replace(/\D/g, '')) - parseInt(b.value.replace(/\D/g, ''));
        }); 
                
        return {
            showModal,
            contact,
            contactTypes,
            contactQualites,
            phoneCodesSorted,
            validationUniqueEmail,
            closeModal,
            openModal,
            addNewContact,
        }
    }

}
</script>
<style lang="scss" scoped>
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
    opacity: 1;
    transform: scale(1);
}
.list-leave-to{
    opacity: 0;
    transform: scale(0.6);
}
.list-leave-active{
    transition: all 1s ease;
    position: absolute;
    width: 100%;
}
.list-move{
    transition:all 0.9s ease;
}
.address-map{
    min-width: 270px;
    min-height: 170px;
}
.search-layer{
    width: 100%;
    height: 100%;
    top: 0;
    z-index: 11;
    background: rgba(0, 0, 0, 0.3);
    .search-panel{
        width: 1020px;
        padding: 15px 80px;
        .search-header{
            .close-icon{
                position: absolute;
                top: 0;
                right: 0;
            }
        }
        .btns{
            .custom-btn{
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
        }
    }
}
</style>