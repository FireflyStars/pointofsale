<template>
  <router-view>
    <transition enter-active-class="animate__animated animate__fadeIn">
      <div class="container-fluid h-100 bg-color" id="container">
        <main-header />
        <div class="row d-flex align-content-stretch align-items-stretch flex-row hmax main-view-wrap reports-page" style="z-index:100" >
            <side-bar />
            <div class="col main-view container">
                <h1 class="d-flex align-items-center m-0">
                  <span class="customer-icon"></span>
                  <span class="ms-3 font-22 almarai_extrabold_normal_normal">CREATION ACTION COMMERCIAL</span>
                </h1>
                <ul class="m-0 p-0 breadcrumb mt-3 mb-3" v-if="breadcrumbs.length">
                    <li class="breadcrumb-item almarai-extrabold font-18 cursor-pointer" 
                    v-for="(breadcrumb, index) in breadcrumbs" 
                    @click="goToStep(index)"
                    :key="index">{{ breadcrumb }}</li>
                </ul>    
                <transition  name="list" appear v-if="step =='choose_customer'">
                    <div class="col-5 bg-white p-3 rounded">
                        <h2 class="almarai-extrabold font-22">Détail Client <span @click="addNewCustomer" class="ms-3 almarai-bold font-16 cursor-pointer text-decoration-underline text-custom-success">Nouveau</span></h2>
                        <SearchCustomer name="search" @selected="selectedCustomer" :droppos="{top:'auto',right:'auto',bottom:'auto',left:'0',transformOrigin:'top right'}" label="Rechercher client" hint="disabled till 2021-09-10" ></SearchCustomer>
                    </div>
                </transition>            
                <transition name="list" appear v-if="step =='choose_action'">
                    <div class="cust-page-content client-detail m-auto pt-5">
                        <div class="page-section">
                            <h3 class="m-0 mulish-extrabold font-22">ACTION COMMERCIAL</h3>
                            <div class="d-flex mt-3">
                                <div class="col-3">
                                    <p class="m-0 mulish-light font-14 text-gray">N {{ action.id }}</p>
                                </div>
                                <div class="col-9 d-flex px-2">
                                    <div class="col-4">
                                    </div>
                                    <div class="col-4"></div>
                                    <div class="col-4">
                                        <p class="m-0 mulish-light font-14 text-gray text-nowrap">Date Création :</p>
                                        <p class="m-0 mulish-light font-14 text-gray text-nowrap">Date Modification :</p>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex mt-3">
                                <div class="col-7 d-flex">
                                    <div class="col-5">
                                        <div class="form-group">
                                            <label class="mulish-medium font-16 text-nowrap">LIBELLE DE L ACTION *</label>
                                            <input type="text" v-model="action.libelle" placeholder="LIBELLE DE L ACTION" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-2"></div>
                                    <div class="col-5">
                                        <select-box v-model="action.status" 
                                            :options="[
                                                {
                                                    display: 'status1',
                                                    value: 'status1',
                                                },
                                                {
                                                    display: 'status2',
                                                    value: 'status1',
                                                },
                                                {
                                                    display: 'status3',
                                                    value: 'status1',
                                                },
                                            ]" 
                                            :name="'actionStatus'"
                                            :label="'STATUS DE L ACTION'"
                                            ></select-box>
                                    </div>
                                </div>
                            </div>    
                            <div class="d-flex mt-3">
                                <div class="col-5">
                                    <select-box v-model="action.realiser" :options="[{ display: 'a', value: 'a' }]" :label="'TYPE D ACTION A REALISER'" :name="'actionRealiser'"></select-box>
                                </div>
                                <div class="col-2"></div>
                                <div class="col-5 d-flex">
                                    <div class="col-5">
                                        <div class="form-group">
                                            <label class="mulish-medium font-16 text-nowrap">DATE</label>
                                            <input type="text" v-model="action.date" placeholder="DATE" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-2"></div>
                                    <div class="col-5">
                                        <div class="form-group">
                                            <label class="mulish-medium font-16 text-nowrap">HEURE</label>
                                            <input type="text" v-model="action.hour" placeholder="HEURE" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>                                           
                            <div class="d-flex mt-3">
                                <div class="col-5">
                                    <select-box v-model="action.affecte" :options="[{ display: 'a', value: 'a' }]" :label="'AFFECTE  A'" :name="'actionAffecte'"></select-box>
                                </div>
                                <div class="col-2"></div>
                                <div class="col-5">
                                    <div class="col-9 form-group">
                                        <label class="text-nowrap">NOTES / INFORMATIONS / COMMENTAIRES</label>
                                        <textarea rows="4" class="form-control" v-model="action.note"></textarea>
                                    </div>
                                </div>
                            </div>                                           
                        </div>
                        <div class="page-section mt-3">
                            <h3 class="m-0 mulish-extrabold font-22">ENTITE / ADRESSE / CONTACT</h3>
                            <div class="d-flex mt-3">
                                <div class="col-4 pe-3">
                                    <div class="form-group">
                                        <label class="mulish-medium font-16 text-nowrap">NOM ENTITE</label>
                                        <input type="text" v-model="action.nomEntitie" placeholder="NOM ENTITE" class="form-control">
                                    </div>
                                </div>
                                <div class="col-4 pe-3">
                                    <div class="form-group">
                                        <label class="mulish-medium font-16 text-nowrap">PRENOM / BATIMENT</label>
                                        <input type="text" v-model="action.prenom" placeholder="PRENOM / BATIMENT" class="form-control">
                                    </div>
                                </div>
                                <div class="col-4 pe-3">
                                    <div class="form-group">
                                        <label class="mulish-medium font-16 text-nowrap">PRENOM / BATIMENT</label>
                                        <input type="text" v-model="action.batiment" placeholder="BATIMENT" class="form-control">
                                    </div>                                    
                                </div>
                            </div>
                            <div class="d-flex mt-3">
                                <div class="col-4 pe-3 ">
                                    <div class="form-group">
                                        <label class="mulish-medium font-16 text-nowrap">NOM CLIENT COMPLEMENTAIRE</label>
                                        <input type="text" v-model="action.nomClient" placeholder="NOM CLIENT COMPLEMENTAIRE" class="form-control">
                                    </div>
                                </div>
                                <div class="col-4 pe-3">
                                    <div class="form-group">
                                        <label class="mulish-medium font-16 text-nowrap">ADRESSE</label>
                                        <input type="text" v-model="action.address" placeholder="ADRESSE" class="form-control">
                                    </div>
                                </div>
                                <div class="col-4 pe-3">
                                    <div class="form-group">
                                        <label class="mulish-medium font-16 text-nowrap">NOM</label>
                                        <input type="text" v-model="action.nom" placeholder="NOM" class="form-control">
                                    </div>                                    
                                </div>
                            </div>
                        </div>
                        <div class="btns d-flex justify-content-end mb-3">
                            <button class="custom-btn btn-cancel me-3" @click="cancel">Annuler</button>
                            <button class="custom-btn btn-ok text-uppercase" @click="nextStep">Suivant</button>
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
import {     
  DISPLAY_LOADER,
  HIDE_LOADER,
  LOADER_MODULE, 
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
        SearchCustomer
    },
    setup() {
        const store = useStore();
        const router = useRouter();
        const breadcrumbs = ref(['Choix client']);
        const step = ref('choose_customer');
        watchEffect(()=>{
            if(step.value == 'choose_customer'){
                breadcrumbs.value = ['Choix client'];
            }else if(step.value == 'choose_address'){
                breadcrumbs.value = ['Choix client', 'Choix adresse chantier'];
            }else if(step.value == 'choose_contact'){
                breadcrumbs.value = ['Choix client', 'Choix adresse chantier', 'Choix contact'];
            }else if(step.value == 'choose_action'){
                breadcrumbs.value = ['Choix client', 'Choix adresse chantier', 'Choix contact', 'Action'];
            }else{

            }
        })        
        const action = ref({
            libelle: '',
            status: '',
            realiser: '',
            date: '',
            hour: '',
            affecte: '',
            note: '',
            nomEntitie: '',
            prenom: '',
            batiment: '',
            nomClient: '',
            address: '',
            nom: '',
        });

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
            }else if(index == 1){
                step.value = 'choose_address';
            }else if(index == 2){
                step.value = 'choose_contact';
            }else if(index == 3){
                step.value = 'choose_action';
            }
        }        
        const submit = ()=>{
            store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'Création d`un nouveau client ...']);
            axios.post('/add-action', action.value).then((res)=>{

            }).catch((errors)=>{
                console.log(errors);
            }).finally(()=>{
                store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
            })
        }
        const selectedCustomer = (data)=>{
            // move on to "addess choose step"
            step.value = 'choose_address';
            // set customer value to devis form
            // form.value.customer = data;

            // loading customer addresses
            store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'Chargement des adresses des clients..']);
            axios.post('/get-customer-addresses', { customer_id: data.id }).then((res)=>{
                customerAddresses.value = res.data;
            }).catch((error)=>{
                console.log(error);
            }).finally(()=>{
                store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
            })
        }        
        onMounted(()=>{

        })
        return {
            action,
            step,
            breadcrumbs,
            goToStep,
            addNewCustomer,
            selectedCustomer,
            cancel,
            submit
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