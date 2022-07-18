<template>
  <router-view>
    <transition enter-active-class="animate__animated animate__fadeIn">
      <div class="container-fluid h-100 bg-color" id="container">
        <main-header />
        <div class="row d-flex align-content-stretch align-items-stretch flex-row hmax main-view-wrap reports-page" style="z-index:100" >
            <side-bar />
            <div class="col main-view container">
                <h1 class="d-flex align-items-center m-0">
                  <span class="user-icon"></span>
                  <span class="ms-3 font-22 almarai_extrabold_normal_normal">CREATION PERSONNEL</span>
                </h1>
                <transition name="list" appear>
                    <div class="cust-page-content client-detail m-auto pt-5">
                        <div class="page-section mt-3">
                            <h3 class="m-0 mulish-extrabold font-22">PERSONNEL</h3>
                            <div class="d-flex mt-3">
                                <div class="col-8 d-flex">
                                    <div class="col-2 form-group">
                                        <select-box v-model="user.gender" 
                                            :options="[
                                                { value: 'MONSIEUR', display: 'MONSIEUR' },
                                                { value: 'Mme', display: 'Mme' },
                                                { value: 'Mlle', display: 'Mlle' },
                                            ]" 
                                            :name="'userGender'"
                                            :label="'Le genre'"
                                            ></select-box>
                                    </div>
                                    <div class="col-5 ps-2 form-group">
                                        <label for="nom-client" class="mulish-medium font-16">PRENOM *</label>
                                        <input type="text" class="form-control" v-model="user.firstName" placeholder="First Name">
                                    </div>
                                    <div class="col-5 ps-2 form-group">
                                        <label class="mulish-medium font-16">NOM *</label>
                                        <input type="text" v-model="user.name" placeholder="Name" class="form-control">
                                    </div>
                                </div>                                
                                <div class="col-4 px-2">
                                    <select-box v-model="user.roleId" 
                                        :options="userRole" 
                                        :name="'userRole'"
                                        :label="'PROFIL'"
                                        ></select-box>                                    
                                </div>
                            </div>
                            <div class="d-flex mt-3">
                                <div class="col-4 pe-2">
                                    <div class="form-group w-100">
                                        <label class="text-uppercase">Coordonnées personnelles</label>
                                        <input type="text" placeholder="" v-model="user.coordpersonnelles" class="form-control custom-input">
                                    </div>                                    
                                </div>                                
                                <div class="col-4 px-2">
                                    <div class="d-flex">
                                        <div class="phone-country-code">
                                            <select-box 
                                                v-model="user.portableCode" 
                                                :options="phoneCodesSorted"
                                                :styles="{ width: '100px'}"
                                                :label="'&nbsp;'"
                                                :name="'phoneCountryCode'">
                                            </select-box>
                                        </div>
                                        <div class="form-group w-100 ms-2">
                                            <label class="text-uppercase">PORTABLE</label>
                                            <input type="text" placeholder="" v-model="user.portable" class="form-control custom-input">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4 px-2">
                                    <select-box v-model="user.userId" :options="userType" :label="'TYPE CONTRAT'" :name="'userType'"></select-box>
                                </div>
                            </div>                                           
                            <div class="d-flex mt-3">
                                <div class="col-4 form-group pe-2">
                                    <label class="mulish-medium font-16 text-nowrap">Email *</label>
                                    <input type="email" placeholder="" v-model="user.email" class="form-control custom-input">
                                </div>
                                <div class="col-4"></div>
                                <div class="col-4 px-2">
                                    <select-box 
                                        v-model="user.statusId" 
                                        :options="userStatus"
                                        :label="'STATUT'"
                                        :name="'userStatus'">
                                    </select-box>                                    
                                </div>                                
                            </div>
                            <div class="d-flex mt-3">
                                <div class="col-4 form-group pe-2">
                                    <label class="mulish-medium font-16 text-nowrap">DATE ENTREE</label>
                                    <Datepicker v-model="user.dateentree" position="left" :hideInputIcon="true" inputClassName="form-control" :timePicker="false" autoApply :format="dateFormat"/>
                                </div>
                                <div class="col-4 form-group px-2">
                                    <label class="mulish-medium font-16 text-nowrap">DATE SORTIE</label>
                                    <Datepicker v-model="user.datesorti" position="left" :hideInputIcon="true" inputClassName="form-control" :timePicker="false" autoApply :format="dateFormat"/>
                                </div>                                
                            </div>
                            <div class="d-flex mt-3">
                                <div class="col-4 form-group">
                                    <label class="text-uppercase">Personne à contacter en cas d'urgence</label>
                                    <input type="text" placeholder="Telephone" v-model="user.contacturgence" class="form-control custom-input">
                                </div>
                            </div>
                            <div class="d-flex mt-3">
                                <div class="col-8">
                                    <div class="form-group">
                                        <label class="text-nowrap">NOTES / INFORMATIONS / COMMENTAIRES</label>
                                        <textarea rows="4" class="form-control" v-model="user.comment"></textarea>
                                    </div>
                                </div>
                            </div>                                           
                        </div>
                        <div class="btns d-flex justify-content-end mb-3">
                            <button class="custom-btn btn-cancel me-3" @click="cancel">Annuler</button>
                            <button class="custom-btn btn-ok text-uppercase" @click="submit">ENREGISTRER</button>
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
import Datepicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'
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
import { useRouter } from 'vue-router';
import { mask } from 'vue-the-mask';

export default {
    directives: {
        mask
    },
    components:{
        SelectBox,
        Datepicker
    },
    setup() {
        const store = useStore();
        const router = useRouter();
        const userRole = ref([]);
        const userStatus = ref([]);
        const userType = ref([]);
        const user = ref({
            id: '',
            gender: 'M',
            firstName: '',
            name: '',
            email: '',
            roleId: 0,
            typeId: 0,
            statusId: 0,
            coordpersonnelles: '',
            portableCode: '+33',
            portable: '',
            dateentree: '',
            datesorti: '',
            contacturgence: '',
            comment: '',
        });
        const dateFormat = (date) => {
            const day = date.getDate();
            const month = date.getMonth() + 1;
            const year = date.getFullYear();

            return `${month.toString().padStart(2, '0')}/${day.toString().padStart(2, '0')}/${year}`;
        }
        const cancel = ()=>{

        }
        const submit = ()=>{
            if( user.value.name == '' ){
                store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                    type: 'danger',
                    message: 'Veuillez saisir LIBELLE DE L ACTION',
                    ttl: 5,
                });    
            }else if( user.value.firstName == '' ){
                store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                    type: 'danger',
                    message: 'Veuillez saisir TYPE D ACTION A REALISER',
                    ttl: 5,
                });                    
            }else if( user.value.email == '' ){
                store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                    type: 'danger',
                    message: 'Veuillez saisir TYPE D ACTION',
                    ttl: 5,
                });                    
            }
            store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'Création d`un Personnel ...']);
            axios.post('/user/create', user.value).then((res)=>{
                router.push({ name: 'personnel-details', params: { id: res.data.id } });
            }).catch((errors)=>{
                console.log(errors);
            }).finally(()=>{
                store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
            })
        }
        onMounted(()=>{
            axios.post('/get-user-info').then((res)=>{
                user.value = res.data.user;
                userStatus.value = res.data.userStatus;
                userRole.value = res.data.userRole;
                userType.value = res.data.userType;
            }).catch((error)=>{
                console.log(error);
            })
        })
        const phoneCodesSorted = [...new Map(phoneCodes.map(item =>
            [item.value, item])).values()].sort((a, b)=>{
            return parseInt(a.value.replace(/\D/g, '')) - parseInt(b.value.replace(/\D/g, ''));
        });             
        return {
            user,
            phoneCodesSorted,
            userStatus,
            userRole,
            userType,
            dateFormat,
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
.full-nav{
    margin-top: 28px;
    height: 70px;
    border-top: 1px solid #C3C3C3;
    .full-nav-item{
      cursor: pointer;
      position: relative;
      .icon{
          margin-right: 30px;
      }
      &::after{
          content: "";
          position: absolute;
          bottom: 0;
          width: 100%;
          height: 1px;
          background: #C3C3C3;
      }
      &.active,
      &:hover{
          background: rgba(217, 237, 210, 0.2);
          transition: background .3s ease-in-out;
      }
      &.active::after,
      &:hover::after{
          height: 4px;
          background: #42A71E;
          transition: background .3s ease-in-out;
      }
  }
  .border-right{
      border-right: 1px solid #C3C3C3;
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
.address-map{
    width: 270px;
    height: 170px;
}
</style>