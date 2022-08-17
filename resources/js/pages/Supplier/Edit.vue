<template>
  <router-view>
    <transition enter-active-class="animate__animated animate__fadeIn">
      <div class="container-fluid h-100 bg-color" id="container">
        <main-header />
        <div class="row d-flex align-content-stretch align-items-stretch flex-row hmax main-view-wrap reports-page" style="z-index:100" >
            <side-bar />
            <div class="col main-view container">
                <h1 class="d-flex align-items-center m-0">
                  <span class="supplier-icon"></span>
                  <span class="ms-3 font-22 almarai_extrabold_normal_normal">EDITION FOURNISSEUR</span>
                </h1>
                <transition name="list" appear>
                    <div class="cust-page-content client-detail m-auto pt-5">
                        <div class="page-section mt-3">
                            <div class="d-flex">
                                <div class="col-6">
                                    <h3 class="m-0 mulish-extrabold font-22">FOURNISSEUR</h3>
                                </div>
                                <div class="col-6">
                                    <CheckBox v-model="supplier.active" :checked="supplier.active" :title="'ACTIF'"></CheckBox>
                                </div>
                            </div>
                            <div class="d-flex mt-3">
                                <div class="col-3">
                                    <p class="m-0 mulish-light font-14 text-gray">N {{ supplier.id }}</p>
                                </div>
                                <div class="col-9 d-flex px-2">
                                    <div class="col-4">
                                    </div>
                                    <div class="col-4"></div>
                                    <div class="col-4">
                                        <p class="m-0 mulish-light font-14 text-gray text-nowrap">Date Création : {{ supplier.created }}</p>
                                        <p class="m-0 mulish-light font-14 text-gray text-nowrap">Date Modification : {{ supplier.updated }}</p>
                                    </div>
                                </div>
                            </div>                            
                            <div class="d-flex mt-3">
                                <div class="col-4 px-2">
                                    <label class="mulish-medium font-16">NOM *</label>
                                    <input type="text" v-model="supplier.nom" placeholder="Name" class="form-control">
                                </div>
                                <div class="col-4 px-2">
                                    <label class="mulish-medium font-16">CONTACT</label>
                                    <input type="text" v-model="supplier.contact" placeholder="Name" class="form-control">                                    
                                </div>
                                <div class="col-4 px-2 d-flex">
                                    <div class="phone-country-code">
                                        <select-box 
                                            v-model="supplier.phoneCode" 
                                            :options="phoneCodesSorted"
                                            :styles="{ width: '100px'}"
                                            :label="'&nbsp;'"
                                            :name="'phoneCountryCode'">
                                        </select-box>
                                    </div>
                                    <div class="form-group w-100 ms-2">
                                        <label class="text-uppercase">TELEPHONE FIXE</label>
                                        <input type="text" v-model="supplier.phoneNumber" class="form-control custom-input">
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex mt-3">
                                <div class="col-4 px-2">
                                    <select-box v-model="supplier.type" 
                                        :options="supplierType" 
                                        :name="'TYPE'"
                                        :label="'TYPE'"
                                        ></select-box>
                                </div>                                
                                <div class="col-4 px-2">
                                    <select-box v-model="supplier.status" 
                                        :options="supplierStatus" 
                                        :name="'STATUT'"
                                        :label="'STATUT'"
                                        ></select-box>
                                </div>   
                                <div class="col-4 px-2">
                                    <label class="mulish-medium font-16 text-nowrap">Email</label>
                                    <input type="email" placeholder="email" v-model="supplier.email" class="form-control custom-input">
                                </div>                             
                            </div>
                            <div class="d-flex mt-3">
                                <div class="col-7 d-flex px-2">
                                    <div class="d-flex col-8">
                                        <div class="form-group col-9">
                                            <label>SIRET *</label>
                                            <input type="text" v-model="supplier.siret" class="form-control" v-mask="'#########'">
                                        </div>
                                        <div class="form-group col-3 px-2">
                                            <label>&nbsp;</label>
                                            <button class="btn btn-primary" @click="checkSiret">VERIFIER</button>
                                        </div>
                                    </div>
                                    <div class="col-1"></div>
                                    <div class="col-3">
                                        <div class="form-group">
                                            <label class="text-nowrap">NAF *</label>
                                            <input type="text" v-model="supplier.naf" class="form-control" placeholder="">
                                        </div>                                 
                                    </div>
                                </div>
                                <div class="col-5 px-2">
                                    <label class="text-nowrap">NOM NAF</label>
                                    <input type="text" v-model="supplier.nomNaf" class="form-control" readonly>                                    
                                </div>
                            </div>                                           
                            <div class="d-flex mt-3 px-2">
                                <div class="col-8">
                                    <div class="form-group">
                                        <label class="text-nowrap">NOTES / INFORMATIONS / COMMENTAIRES</label>
                                        <textarea rows="4" class="form-control" v-model="supplier.comment"></textarea>
                                    </div>
                                </div>
                            </div>                                           
                        </div>
                        <div class="btns d-flex justify-content-end mb-3">
                            <button class="custom-btn btn-cancel me-3" @click="cancel">Annuler</button>
                            <button class="custom-btn btn-ok text-uppercase" @click="submit">VALIDER</button>
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
import { ref, onMounted, watch } from 'vue';
import SelectBox from '../../components/miscellaneous/SelectBox';
import CheckBox from '../../components/miscellaneous/CheckBox';
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
import { mask } from 'vue-the-mask';

export default {
    directives: {
        mask
    },
    components:{
        SelectBox,
        CheckBox
    },
    setup() {
        const store     = useStore();
        const router    = useRouter();
        const route    = useRoute();
        const supplierStatus= ref([]);
        const supplierType  = ref([]);
        const customerNafs  = ref([]);
        const supplier = ref({
            nom: '',
            contact: '',
            email: '',
            typeId: 0,
            statusId: 0,
            phoneCode: '+33',
            phoneNumber: '',
            siret: '',
            naf: '',
            nomNaf: '',
            comment: '',
            active: true,
            siretValidation: true,
        });
        const cancel = ()=>{

        }
        const submit = ()=>{
            if( !supplier.value.siretValidation){
                store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                    type: 'danger',
                    message: 'Vous devez vérifier le siret s\'il est valide ou non.',
                    ttl: 5,
                });                     
                return;
            }
            let error = false;
            if( supplier.value.nom == '' ){
                error = true;
                store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                    type: 'danger',
                    message: 'You have to enter a nom',
                    ttl: 5,
                });    
            }else if( supplier.value.siret == '' ){
                error = true;
                store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                    type: 'danger',
                    message: 'Veuillez entrer SIRET',
                    ttl: 5,
                });                       
            }else if( supplier.value.naf == '' ){
                error = true;
                store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                    type: 'danger',
                    message: 'Veuillez entrer NAF',
                    ttl: 5,
                });                       
            }
            if(!error){
                store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'Création d`un FOURNISSEUR...']);
                axios.post('/supplier/update/'+ route.params.id, supplier.value).then((res)=>{
                    if(res.data.success){
                        router.push({ name: 'EditSupplier', params: { id: route.params.id } });
                    }else{
                        Object.values(res.data.errors).forEach(item => {
                            store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                                type: 'danger',
                                message: item[0],
                                ttl: 5,
                            });
                        })
                    }
                }).catch((errors)=>{
                    console.log(errors);
                }).finally(()=>{
                    store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
                })
            }
        }
        onMounted(()=>{
            axios.post('/get-supplier/' + route.params.id).then((res)=>{
                supplier.value = res.data.supplier;
                let phone = formatPhone(supplier.value.phoneNumber);
                supplier.value.phoneCode = phone[0];
                supplier.value.phoneNumber = phone[1];
                if(supplier.value.active){
                    supplier.value.active = true;
                }else{
                    supplier.value.active = false;
                }
                supplier.value.siretValidation = true;
                supplierStatus.value = res.data.status;
                supplierType.value = res.data.type;
                customerNafs.value = res.data.nafs;
            }).catch((error)=>{
                console.log(error);
            })
        })
        const formatPhone = (phoneNumber)=>{
            if(phoneNumber == null){
                return ['+33', ''];
            }
            if(phoneNumber.split('|').length == 1){
                return ['+33', phoneNumber];
            }else{
                return phoneNumber.split('|');
            }
        }        
        const phoneCodesSorted = [...new Map(phoneCodes.map(item =>
            [item.value, item])).values()].sort((a, b)=>{
            return parseInt(a.value.replace(/\D/g, '')) - parseInt(b.value.replace(/\D/g, ''));
        }); 
        const checkSiret = ()=>{
            if(supplier.value.siret.length == 9){
                store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'checking siret ...']);
                axios.post('/check-siret', { 'siret' : supplier.value.siret }).then((res)=>{
                    if(res.data.success){
                        supplier.value.siretValidation = true;
                        supplier.value.naf = res.data.data.activitePrincipaleUniteLegale.replace('.', '');
                        supplier.value.nom    = res.data.data.denominationUniteLegale;
                    }else{
                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                            type: 'danger',
                            message: res.data.error,
                            ttl: 5,
                        });
                    }
                }).catch((error)=>{
                    console.log(error);
                }).finally(()=>{
                    store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
                })
            }else{
                store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                    type: 'danger',
                    message: 'Siret must be 9 digits',
                    ttl: 5,
                }); 
            }
        }       
        watch(() => supplier.value.naf, (curVal, preVal)=>{
            var selectedNaf = customerNafs.value.filter((item)=>{
                return item.code == curVal;
            })[0];

            if(selectedNaf != undefined){
                supplier.value.nomNaf = selectedNaf.name;
            }else{
                supplier.value.nomNaf = '';
            }
        })                     
        return {
            supplier,
            phoneCodesSorted,
            supplierStatus,
            supplierType,
            cancel,
            submit,
            checkSiret
        }
  },
}
</script>
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
</style>