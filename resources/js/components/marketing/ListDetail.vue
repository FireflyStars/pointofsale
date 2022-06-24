<template>

<item-detail-panel :showloader="showloader">

    <h1 class="heading">
        <Icon name="emailing" />
        {{ campagne.id }}
    </h1>
    
    <div class="row campagne-detail-grid">

        <div class="col">
            <h4></h4>
            <h4 class="heading-campagne-title">{{ campagne.name }}</h4>
            <p class="text-fade">{{ campagne.user?.name }}</p>
        </div>
        
        <div class="col">
            <h4 class="text-fade">Detail</h4>
            <p class="text-normal">{{ campagne.email }}</p>
            <p 
                v-if="campagne.type?.toLowerCase() == 'commande produit'"
                class="text-normal" 
            >
                {{ campagne.nb }} Clients
            </p>
        </div>
        
        <div class="col">
            <h4 class="text-fade">Date</h4>
            <p class="text-normal">Lancement: {{ moment(campagne.datelancement).format('DD/MM/Y') }}</p>
            <p class="text-normal">Creaction: {{ moment(campagne.created_at).format('DD/MM/Y') }}</p>
        </div>

    </div>

    <div class="email-campagne" v-if="campagne.type?.toLowerCase() == 'email'">

        <h4 class="heading-fade">Résultats</h4>

        <div class="row email-campagne-content">

            <div class="col-6 email-campagne-col">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-4">
                        <div class="radio"></div>
                        <h4 class="heading-campagne-title m-0">Nombre</h4>
                    </div>
                    <p class="text-normal d-flex align-items-end text-end">{{ campagne.count }}</p>
                </div>
            </div>

            <div class="col-6 email-campagne-col">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-4">
                        <div class="radio"></div>
                        <h4 class="heading-campagne-title m-0">Click</h4>
                    </div>
                    <p class="text-normal d-flex align-items-end text-end">{{ campagne.clicks }}</p>
                </div>
            </div>

            <div class="col-6 email-campagne-col">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-4">
                        <div class="radio"></div>
                        <h4 class="heading-campagne-title m-0">Déinscription</h4>
                    </div>
                    <p class="text-normal d-flex align-items-end text-end">{{ campagne.unsubscribers }}</p>
                </div>
            </div>

            <div class="col-6 email-campagne-col">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-4">
                        <div class="radio"></div>
                        <h4 class="heading-campagne-title m-0">Ouverture</h4>
                    </div>
                    <p class="text-normal d-flex align-items-end text-end">{{ campagne.opens }}</p>
                </div>
            </div>

        </div>

    </div>

    <div class="product-campagne" v-else-if="campagne.type?.toLowerCase() == 'commande produit'">

        <div class="d-flex justify-content-between">
            
            <h4 class="heading-fade">Détail</h4>

            <div class="d-flex align-items-center gap-4">
                <h4 class="heading-campagne-title">{{ campagne.transport }}&euro;</h4>
                <h4 class="heading-campagne-title">{{ grandTotalHt }} &euro;</h4>
            </div>
        
        </div>

        <div class="product-campagne-content">

            <div class="d-flex justify-content-between align-items-center" style="justify-items: center">
                <div>
                    <p class="text-light text-center">Name</p>
                </div>
                
                <div>
                    <p class="text-light text-center">Description</p>
                </div>
                
                <div>
                    <p class="text-light">Total HT</p>
                </div>    
            </div>

            <div 
                v-for="product in campagne.details"
                :key="product.id"
                class="d-flex justify-content-between align-items-center"
                style="justify-items: center"
            >
            
                <div class="product-campagne-col">
                    <div class="d-flex align-items-center gap-4">
                        <div class="radio"></div>
                        <h4 class="heading-campagne-title m-0">{{ product.name }}</h4>
                    </div>
                </div>

                <div class="product-campagne-col text-center">
                    <p class="text-normal" v-html="product.description"></p>
                </div>

                <div class="product-campagne-col">
                    <p class="text-normal d-flex justify-flex-end">{{ totalHt(product) }} &euro;</p>
                </div>

            </div>



        </div>

    </div>

</item-detail-panel>

</template>


<script setup>

import moment from 'moment'
import { useStore } from 'vuex'
import { computed, onMounted } from 'vue'
import ItemDetailPanel from '../miscellaneous/ItemListTable/ItemDetailPanel.vue'
import {
        CIBLE_MODULE,
        GET_CAMPAGNE_DETAILS,
        LOADER_MODULE,
        DISPLAY_LOADER,
        TOASTER_MODULE,
        TOASTER_MESSAGE,
        HIDE_LOADER
    }
from '../../store/types/types'

const props = defineProps({
    id: {
        type: String,
        required: true
    }
})

const showLoader = ref(false)

const store = useStore()

const campagne = computed(() => store.getters[`${CIBLE_MODULE}campagne`])

const totalHt = (product) => {
    return (product.price * product.qty).toFixed(2)
}

const grandTotalHt = computed(() => {
    return campagne.value?.details
    ?.map(detail => totalHt(detail))
    ?.reduce((newQty, oldQty) => +newQty + +oldQty, 0)
    ?.toFixed(2)
})

const getCampagneDetails = async () => {
    try {
        showLoader.value = true
        await store.dispatch(`${CIBLE_MODULE}${GET_CAMPAGNE_DETAILS}`, props.id)
    }
    catch(e) {
        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
            type: 'danger',
            message: 'Something went wrong',
            ttl: 5,
        })
        throw e
    }
    finally {
        showLoader.value = false
    }
}

onMounted(() => {
    getCampagneDetails()
})


</script>

<style scoped lang="scss">
.heading {

    display: flex;
    align-items: center;
    gap: 1rem;
    font-family: 'Almarai Bold';
    font-style: normal;
    font-weight: 800;
    font-size: 22px;
    line-height: 110%;
    color: #000000;
    margin-top: 2.25rem;

    &-campagne-title {
        font-family: 'Almarai Bold';
        font-style: normal;
        font-weight: 700;
        font-size: 14px;
        line-height: 140%;
        display: flex;
        align-items: flex-end;
        color: #000000;
    }

    &-fade {
        font-family: 'Almarai Bold';
        font-style: normal;
        font-weight: 700;
        font-size: 14px;
        line-height: 140%;
        display: flex;
        align-items: flex-end;
        color: #868686;
    }

}

.campagne-detail-grid {
    margin-top: .93rem; 
}

.text-fade {
    font-family: 'Almarai Bold';
    font-style: normal;
    font-weight: 700;
    font-size: 14px;
    line-height: 140%;
    display: flex;
    align-items: flex-end;
    color: #C3C3C3;
}

.text-normal {
    font-family: 'Almarai Light';
    font-style: normal;
    font-weight: 300;
    font-size: 14px;
    line-height: 140%;
    color: #000000;
}

.text-light {
    font-family: 'Almarai Light';
    font-style: normal;
    font-weight: 300;
    font-size: 12px;
    line-height: 140%;
    color: #868686 !important;
}

.email-campagne, 
.product-campagne {

    margin-top: 2rem;
    background: #F8F8F8;
    box-shadow: inset 0px -1px 0px rgba(168, 168, 168, 0.25);
    min-height: 206px;
    padding: 1rem 1.31rem !important;

    &-content {
        margin-top: 1.87rem;
    }

    &-col {
        margin-top: 1.375rem;
    }

}

.radio {
    width: 13px;
    height: 13px;
    background: #EFBF00;
    border-radius: 50%;
}


</style>