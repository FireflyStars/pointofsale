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
                        <span class="ms-3 font-22 almarai_extrabold_normal_normal">CREATION COMMANDE FOURNISSEUR</span>
                    </h1>
    
                    <transition name="list" appear v-if="step == 'choose_fournisseur'">
    
                        <div class="col-6 bg-white p-3 rounded mt-3">
                            
                            <h2 
                                class="almarai-extrabold font-22"
                            >
                                    Détail Fournisseur 
                                    <span 
                                        @click="addNewFournisseur" 
                                        class="ms-3 almarai-bold font-16 cursor-pointer text-decoration-underline text-custom-success"
                                    >
                                        Nouveau
                                    </span>
                            </h2>
    
                            <SearchFournisseur 
                                name="search" 
                                @selected="selectedFournisseur" 
                                :droppos="{ top: 'auto', right: 'auto', bottom: 'auto', left: '0', transformOrigin: 'top right' }" 
                                label="Rechercher Fournisseur" 
                            ></SearchFournisseur>
                        
                            <div class="btns d-flex justify-content-end my-3">
                                <button class="custom-btn btn-ok text-uppercase" @click="addNewFournisseur">VALIDER</button>
                            </div>

                        </div>
    
                    </transition>

    
                </div>

            </div>
    
            </div>
    
        </transition>
  
    </router-view>
  </template>
  
  <script setup>
  
    import moment from 'moment'
    import lodash from 'lodash'    
    import { ref, reactive, onMounted, watch, watchEffect } from 'vue'
    import SelectBox from '../../components/miscellaneous/SelectBox'
    import SearchFournisseur from '../../components/CommandeFournisseur/Search'
    import { phoneCountryCode as phoneCodes } from '../../static/PhoneCountryCodes'
    
    import {     
        COMMANDE_FOURNISSEUR_LIST_MODULE,
        CREATE_NEW_COMMANDE_FOURNISSEUR,
        DISPLAY_LOADER,
        HIDE_LOADER,
        LOADER_MODULE,
        TOASTER_MESSAGE,
        TOASTER_MODULE, 
    } from '../../store/types/types'
        
    import axios from 'axios'
    import { useStore } from 'vuex'
    import { useRouter } from 'vue-router'
    
    const store = useStore()
    const router = useRouter()
    const newFournisseur = ref(0)

    const step = ref('choose_fournisseur')

    const addNewFournisseur = async () => {

        try {

            if(newFournisseur.value == 0) {
                store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                    type: 'danger',
                    message: 'Please enter a fournisseur first',
                    ttl: 5,
                })
                return
            }

            store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'Création d`un FOURNISSEUR...'])
            await store.dispatch(`${COMMANDE_FOURNISSEUR_LIST_MODULE}${CREATE_NEW_COMMANDE_FOURNISSEUR}`, newFournisseur.value)
            store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`)
            
            router.push({
                name: 'commande-fournisseur'
            })

        }

        catch(e) {
            throw e
        }

    }

    const selectedFournisseur = (fournisseur) => {
        newFournisseur.value = fournisseur.id
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