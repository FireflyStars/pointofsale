<template>

<item-detail-panel :showloader="showloader">

    <div class="panel-heading-section d-flex align-items-center" style="gap: 8rem;">

        <div class="d-flex gap-4 align-items-center">

            <h1 class="heading d-flex gap-2 align-items-center">
                <Icon name="paiement" style="width: 45px; height: 45px" />
                <span style="vertical-align: bottom;">N°{{ details.id || '--/--' }}</span>
            </h1>

            <h1 class="heading">{{ Math.ceil(details.montant) || '--/--' }}</h1>
        
        </div>


    </div>

    <div class="responsable-section d-flex justify-content-between align-items-center">
    
        <div class="d-flex flex-column gap-1">
            <span class="title-label">No Facture :</span>
            <span class="detail">
                {{ details.invoice_id }}
            </span>
        </div>

        <div class="d-flex flex-column gap-1">
            <span class="title-label">Percentage</span>
            <span class="detail">{{ Math.ceil(details.percentage) + ' %' || '--/--' }}</span>
        </div>

        <div class="d-flex flex-column gap-1">
            <span class="title-label">Type</span>
            <span class="detail">{{ details?.type?.name || '--/--' }}</span>
        </div>

        <div class="d-flex flex-column gap-1">
            <span class="title-label">Statut</span>
            <span class="detail">{{ details?.state?.name || '--/--' }}</span>
        </div>

    </div>

    <div class="responsable-section d-flex justify-content-between align-items-center">

        <div class="d-flex flex-column gap-1">
            <span class="title-label">Client</span>
            <span class="detail">{{ details?.customer?.name || '--/--' }}</span>
        </div>

        <div class="d-flex flex-column gap-1">
            <span class="title-label">Date paiement</span>
            <span class="detail">{{ details.datepaiement ? moment(details.datepaiement).format('LL') : '--/--' }}</span>
        </div>

        <div class="d-flex flex-column gap-1">
            <span class="title-label">Reference</span>
            <span class="detail">{{ details.reference || '--/--' }}</span>
        </div>

        <div></div>

    </div>


    <hr />


    <div class="history-section position-relative" v-if="show && details?.history?.length">

        <a 
            href="#" 
            class="link d-flex align-items-center" 
            @click.prevent="appendResults('history')"
            style="gap: .4rem"
            v-if="!!fetched.history === false"
        >
            Voir tout
            <Icon 
                class="icon"
                name="spinner"
                v-show="loading?.status == true && loading?.id == 'history'"
            />
        </a>


        <div class="title-rows text-center mt-3">
            <div class="title-label"></div>
            <div class="title-label">Responsable</div>
            <div class="title-label">Date</div>
            <div class="title-label">Statut</div>
        </div>

        <div 
            v-for="history in details?.history"
            :key="history.id"
            class="detail-rows" 
            style="margin-top: 1rem"
        >

            <div class="d-flex align-items-center gap-3">
                <div class="radio-button" style="width: 13px;"></div>
                <div>N°{{ history.id }}</div>
            </div>

            <div class="">{{ history.user?.name }}</div>
            <div class="">{{ history.created_at ? moment(history.created_at).format('LL') : '--' }}</div>

            <div 
                :title="history?.state?.name"
                class="tag"
                :style="{ 'background': history?.state?.color, color: history.state?.fontcolor }"
            >
                {{ history?.state?.name }}
            </div>

            

        </div>

    </div>



    <div class="footer-section d-flex align-items-center gap-4" v-if="show">

        <div style="flex: 1">
        
            <base-button 
                title="Valider Le Paiement" 
                kind="green" 
                @click.prevent="validerPaiement" 
            />

        </div>

        <base-button 
            title="Fermer" 
            class="text-uppercase"
            @click.prevent="$router.push({ name: 'paiement' })"
        />
        
    </div>


</item-detail-panel>




</template>

<script setup>

import moment from 'moment'
import { computed, onMounted, ref, onBeforeMount, reactive } from 'vue'
import ItemDetailPanel from '../../components/miscellaneous/ItemListTable/ItemDetailPanel.vue'
import { useStore } from 'vuex'
import { useRouter } from 'vue-router'

import {
    TOASTER_MODULE,
    TOASTER_MESSAGE,
    PAIEMENT_LIST_MODULE,
    GET_PAIEMENT_DETAILS,
    RESET_DETAILS,
    VALIDER_PAIEMENT,
    GET_PAIEMENT_RESULTS
}
from '../../store/types/types'

const props = defineProps({
    id: {
        required: true,
        type: [String, Number]
    }
})

const store = useStore()
const router = useRouter()
const showloader = ref(false)
const show = ref(true)
const comment = ref(null)
const status = ref(null)
const modal = reactive({
    show: false,
    title: 'Change Status'
})

const fetched = reactive({
    history: false,
})

const details = computed(() => store.getters[`${PAIEMENT_LIST_MODULE}details`])
const loading = computed(() => store.getters[`${PAIEMENT_LIST_MODULE}loading`])


const getPaiementDetails = async () => {

    try {
        showloader.value = true
        show.value = false
        await store.dispatch(`${PAIEMENT_LIST_MODULE}${GET_PAIEMENT_DETAILS}`, props.id)
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
        show.value = true
        showloader.value = false
    }

}

const resetDetails = () => {
    store.commit(`${PAIEMENT_LIST_MODULE}${RESET_DETAILS}`)
}

const validerPaiement = async () => {
    
    try {

        showloader.value = true
        await store.dispatch(`${PAIEMENT_LIST_MODULE}${VALIDER_PAIEMENT}`, details.value.id)

        router.replace({
            name: 'paiement'
        })

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
        showloader.value = false
    }

}


const appendResults = async (type) => {

    if(loading.value.status) return
    
    try {
        showloader.value = true
        await store.dispatch(`${PAIEMENT_LIST_MODULE}${GET_PAIEMENT_RESULTS}`, { type, id: details.value.id })
        fetched[type] = true
    }

    catch(e) {
        throw e
    }

    finally {
        showloader.value = false
    }


}


onMounted(() => {
    getPaiementDetails()
})

onBeforeMount(() => {
    resetDetails()
})


</script>

<style lang="scss" scoped>

.responsable-section {
    .title-label {
        font-weight: 300 !important;
    }
}

.status-tag {
    font-family: 'Almarai Regular';
    font-style: normal;
    font-weight: 700;
    font-size: 16px;
    line-height: 110%;
    color: #000000;
    &:last-of-type {
        margin-left: 4rem;
    }
    span {
        margin-right: 10px;
    }
}

textarea {
    border: 1px solid #ccc;
    width: 100%;
    height: 70px;
}    

.routerLink {
    font-weight: bold; 
    color: #000; 
    text-decoration: none;
    &:hover {
        text-decoration: underline;
    }
}

 .link {
    position: absolute;
    top: .7rem;
    right: 1rem;
    font-family: 'Almarai Regular';
    font-style: normal;
    font-weight: 400 !important;
    font-size: 14px;
    line-height: 140%;
    display: flex;
    align-items: flex-end;
    color: #3E9A4D;
}

.panel-heading-section {
    margin-top: 2.35rem;
}

.heading {
    font-family: 'Almarai Regular';
    font-style: normal;
    font-weight: 800 !important;
    font-size: 22px;
    line-height: 110%;
    color: #000000;
    margin-bottom: 0;
}
.text-editer {
    font-family: 'Almarai Light';
    font-style: normal;
    font-weight: 400;
    font-size: 14px;
    line-height: 140%;
    display: flex;
    align-items: flex-end;
    color: #3E9A4D;
}
.tag {
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: center;
    padding: 5px 16px 5px 8px;
    gap: 8px;
    position: relative;
    min-width: 104px;
    width: auto;
    height: 23px;
    background: rgba(66, 167, 30, 0.2);
    border-radius: 70px;
    font-family: 'Almarai Regular';
    font-style: normal;
    font-weight: 700;
    font-size: 12px;
    line-height: 13px;
    color: #000000;
}

.responsable-section {
    margin-top: 1.93rem;
    margin-bottom: 2.81rem;
}

.raison-social-entite-section {
    margin-top: 0.81rem;
    margin-bottom: 1.3rem;
}

.action-commercial-section {
    margin-top: 10px;
}

.history-section {
    margin-top: 10px;
}

.devis-section, .invoice-section {
    margin-top: 10px;
}

.action-commercial-section, .history-section, .devis-section, .invoice-section {
    background: #F8F8F8;
    box-shadow: inset 0px -1px 0px rgba(168, 168, 168, 0.25);
    padding: 1.18rem 1.31rem 2.375rem 1.31rem;
}

.footer-section {
    margin-top: 1.56rem;
    margin-bottom: 2rem;
    margin-left: 1.45rem;
    margin-right: 1.45rem;
}

.radio-button {
    background: #C4C4C4;
    width: 13px;
    height: 13px;
    border-radius: 50%;
}

.title-label, 
.heading-label, 
.heading-label-fade, 
.detail-label-fade {
    font-family: 'Almarai Regular';
    font-style: normal;
    font-weight: 700 !important;
    font-size: 14px;
    line-height: 140%;
    display: flex;
    align-items: flex-end;
    color: #C3C3C3;
}

.heading-label {
    color: #000000;
}

.heading-label-fade {
    color: #868686;
}

.detail-label-fade {
    color: #979797;
    font-family: 'Almarai Regular';
    font-weight: 400 !important;
}

.detail {
    font-family: 'Almarai Light';
    font-style: normal;
    font-weight: 300 !important;
    font-size: 14px;
    line-height: 140%;
    color: #000000;
}

.title-rows, .detail-rows {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    align-items: center;
    justify-items: center;
}

.detail-rows {
    font-family: 'Almarai Light';
    font-style: normal;
    font-weight: 300 !important;
    font-size: 14px;
    line-height: 140%;
    color: #000000;
}

.devis-section {
    .title-rows, .detail-rows {
        grid-template-columns: repeat(6, 1fr) !important;
    }
}


</style>


