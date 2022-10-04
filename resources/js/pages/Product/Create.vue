<template>

    <router-view>

        <transition enter-active-class="animate__animated animate__fadeIn">

            <div class="container-fluid h-100 bg-color" id="container">

                <main-header />
                
                <div class="row d-flex align-content-stretch align-items-stretch flex-row hmax main-view-wrap reports-page" style="z-index:100" >
                    
                    <side-bar />

                    <div class="col main-view container">

                        <transition name="list" appear>

                            <div class="cust-page-content client-detail m-auto pt-5">
                                
                                <div class="page-section mt-3">

                                    <div class="d-flex my-2">
                                        <h3 class="m-0 mulish-extrabold font-22">Product</h3>
                                    </div>
            
                                    <p class="text-normal mb-2">
                                        Mise en place d'un complexe de sur toiture de type ONDUCLAIR RENOV FC avec isolation comprenant nettoyage du support, mise en place isolation polystyrène 50mm et plaque polystyrène opaque compris fixation 
                                    </p>

                                    <div class="row mb-2">

                                        <div class="col-6">
                                            <label class="mulish-medium font-16">Unit</label>
                                            <select-box 
                                                v-model="product.unit" 
                                                :options="units"
                                                :styles="{ width: '100px' }"
                                                label=""
                                                name="unit"
                                            >
                                            </select-box>
                                        </div>

                                        <div class="col-6">
                                            <label class="mulish-medium font-16">Product</label>
                                            <select-box 
                                                v-model="product.type" 
                                                :options="types"
                                                :styles="{ width: '100px' }"
                                                label=""
                                                name="unit"
                                            >
                                            </select-box>
                                        </div>
                                    
                                    </div>

                                    <div class="row mb-2">
    
                                        <div class="col-6">
                                            <label class="mulish-medium font-16 form-label">Nom produit</label>
                                            <input type="text" v-model="product.name" placeholder="Name" class="form-control">                                    
                                        </div>
    
                                        <div class="col-6">
    
                                            <label class="mulish-medium font-16 form-label">Prix d’achat</label>
                                            <input type="text" v-model="product.price" placeholder="Name" class="form-control"> 
                                            
                                        </div>

                                    </div>
                                    
                                    <div class="row mb-2">
    
                                        <div class="col-12">

                                            <div class="form-group">
                                                <label class="text-nowrap">Description</label>
                                                <textarea rows="4" class="form-control" v-model="product.description"></textarea>
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

<script setup>

import { ref, onMounted, reactive, computed } from 'vue';
import SelectBox from '../../components/miscellaneous/SelectBox';
import CheckBox from '../../components/miscellaneous/CheckBox';

import {     
    DISPLAY_LOADER,
    HIDE_LOADER,
    LOADER_MODULE,
    TOASTER_MESSAGE,
    TOASTER_MODULE, 
    GET_PRODUCT_UNITS,
    COMMANDE_FOURNISSEUR_LIST_MODULE,
    PRODUCT_MODULE,
    CREATE_NEW_PRODUCT,
} from '../../store/types/types';

import axios from 'axios';
import { useStore } from 'vuex';
import { useRouter } from 'vue-router';
  
const store = useStore()
const router = useRouter()

const product = reactive({
    unit: 0,
    type: 0,
    name: '',
    price: 0,
    description: null
})

const units = computed(() => store.getters[`${COMMANDE_FOURNISSEUR_LIST_MODULE}productUnits`])

const types = computed(() => {
    return [
        { value: 'produit', display: 'PRODUIT' },
        { value: 'mo', display: 'MO' }
    ]
})

const getProductUnits = () => {
    try {
        store.dispatch(`${COMMANDE_FOURNISSEUR_LIST_MODULE}${GET_PRODUCT_UNITS}`)
    }
    catch(e) {
        throw e
    }
}


const submit = async ()=> {
    
    let error = false

    if(product.name == '') {
        error = true
        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
            type: 'danger',
            message: 'You have to enter a nom',
            ttl: 5,
        })                 
    }

    if(!error) {

        try {
            store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'Création d`un Product...'])
            await store.dispatch(`${PRODUCT_MODULE}${CREATE_NEW_PRODUCT}`, product)
            store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                type: 'success',
                message: 'Product saved',
                ttl: 5,
            })
            store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`)
            resetProduct()
        }
        catch(e) {
            throw e
        }

    }
}

const resetProduct = () => {
    product.unit = 0
    product.type = 0
    product.name = ''
    product.price = 0
    product.description = null
}

onMounted(()=> {
    getProductUnits()
})


</script>

<style lang="scss" scoped>
    .text-normal {
        font-family: 'Almarai';
        font-style: normal;
        font-weight: 300;
        font-size: 14px;
        line-height: 140%;
        /* or 20px */


        color: #000000;

    }

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