<template>

<item-detail-panel :showloader="showloader">

    <div class="panel-heading-section d-flex align-items-center" style="gap: 8rem;">

        <div class="d-flex gap-5 align-items-center">

            <h1 class="heading d-flex align-items-center gap-3">
                <Icon name="pdf" style="width: 45px; height: 45px" />
                <span style="vertical-align: bottom;">N°{{ details.id || '--/--' }}</span>
            </h1>

            <h1 class="heading">{{ details.name }}</h1>

        </div>


    </div>

    <div class="responsable-section d-flex justify-content-between align-items-center">
    
        <div class="d-flex flex-column gap-1">
            <span class="title-label">Unité :</span>
            <span class="detail">{{ details.unit?.code || '--/--' }}</span>
        </div>

        <div class="d-flex flex-column gap-1">
            <span class="title-label">Réference</span>
            <span class="detail">{{ details.reference || '--/--' }}</span>
        </div>

        <div class="d-flex flex-column gap-1">
            <span class="title-label">Type Toit</span>
            <span class="detail">{{ details.toit?.name || '--/--' }}</span>
        </div>

        <div class="d-flex flex-column gap-1">
            <span class="title-label">Metier</span>
            <span class="detail">{{ details.metier?.name || '--/--' }}</span>
        </div>

        <div class="d-flex flex-column gap-1">
            <span class="title-label">Type</span>
            <span class="detail">{{ details.type || '--/--' }}</span>
        </div>

    </div>

    <hr />

    <div class="raison-social-entite-section">

        <div>
            <span class="title-label">Description</span>
            <span class="detail ms-5" style="font-weight: bold !important; margin-top: 0.93rem">{{ details.description }}</span>
        </div>

    </div>

    <div class="raison-social-entite-section">

        <div>
            <span class="title-label">Texte Client ouvrage</span>
            <span class="detail ms-5" style="font-weight: bold !important; color: #E8581B; margin-top: 0.93rem;">
                <input 
                    v-model="details.textcustomer"
                    type="text" 
                    style="border: none; background: none; min-width: 300px; width: auto; color: #E8581B; font-weight: bold; outline: none" 
                >
            </span>
        </div>

    </div>

    <div class="history-section position-relative mb-2" v-if="show">

        <h4 class="heading-label-fade">Détail de l Ouvrage</h4>

        <template v-for="task in details.tasks" :key="task.id">
        
            <h4 class="title-label d-flex align-items-center gap-3" style="color: #000; margin-top: 1.06rem;">
                <span class="radio-button" style="background: #E8581B;"></span>
                <span>{{ task.name }}</span>
            </h4>

            <div class="ml-3" style="margin-top: 0.68rem;">
                <h4 class="heading-label-fade">Texte Client Tache</h4>
                <input 
                    class="detail" 
                    style="color: #E8581B; margin-top: .5rem; border: none; background: none; min-width: 300px; width: auto; font-weight: bold; outline: none"
                    v-model="task.affiliated_textcustomer"
                >
            </div>


            <div class="title-rows" style="margin-top: 2rem;">
                <div></div>
                <div class="title-label">Réference</div>
                <div class="title-label">Unité</div>
                <div class="title-label">Type</div>
                <div class="title-label">Quantité</div>
                <div class="title-label">Votre quantité</div>
            </div>

            <div class="detail-rows" v-for="detail in task.details" :key="detail.id" style="margin-top: 1rem;">

                <div class="d-flex align-items-center gap-3" style="color: #000; font-weight: 700;">

                    <span class="radio-button"></span>
                    <span>{{ task.name }}</span>

                </div>

                <div :title="detail.product?.name">{{ slice(detail.product?.name, 10) }}</div>
                <div>{{ task.unit?.code }}</div>
                <div>{{ detail.product?.type }}</div>
                <div>{{ detail.qty }}</div>
                <div>
                    <input 
                        type="text" 
                        style="color: #E8581B; margin-top: .5rem; border: none; background: none; width: 100px; font-weight: bold; outline: none; text-align: center; margin-top: -1rem;"
                        v-model="detail.ouvrage_detail_qty"
                    >
                </div>

            </div>

        </template>

    </div>


    <div class="footer-section d-flex align-items-center justify-content-between" v-if="show">

        <div style="flex: 1">
        
            <base-button 
                title="Valider" 
                kind="green"
                :disabled="showloader" 
                :class="{ 'cursor-not-allowed': showloader }"
                @click.prevent="valider"
            />

        </div>

        <base-button 
            title="Annuler" 
        />
        
    </div>


</item-detail-panel>


</template>

<script setup>

import Swal from 'sweetalert2'
import moment from 'moment'
import { computed, onMounted, ref, onBeforeMount, reactive } from 'vue'
import { useStore } from 'vuex'
import { useRouter } from 'vue-router'
import useHelpers from '../../composables/useHelpers'

import ItemDetailPanel from '../../components/miscellaneous/ItemListTable/ItemDetailPanel.vue'
import StatusTag from '../../components/ActionCo/StatusTag.vue'


import {
    TOASTER_MODULE,
    TOASTER_MESSAGE,
    GET_OUVRAGE_DETAILS,
    RESET_DETAILS,
    ITEM_LIST_MODULE,
    ITEM_LIST_REMOVE_ROW,
    ITEM_LIST_UPDATE_ROW,
    OUVRAGE_MODULE,
    VALIDER_OUVRAGE
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

const { slice } = useHelpers()

const showloader = ref(false)
const show = ref(true)
const comment = ref(null)
const status = ref(null)
const modal = reactive({
    show: false,
    title: 'Change Status'
})

const fetched = reactive({
    event_history: false,
    orders: false,
})

const details = computed(() => store.getters[`${OUVRAGE_MODULE}details`])
const loading = computed(() => store.getters[`${OUVRAGE_MODULE}loading`])

const ouvrageTextcustomer = computed(() => {
    const affiliateTextcustomer = details.value?.ouvrage_affiliate?.textcustomer
    return !!affiliateTextcustomer ? affiliateTextcustomer : details.value?.textcustomer
})


const getOuvrageDetails = async () => {

    try {
        showloader.value = true
        show.value = false
        await store.dispatch(`${OUVRAGE_MODULE}${GET_OUVRAGE_DETAILS}`, props.id)
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
    store.commit(`${OUVRAGE_MODULE}${RESET_DETAILS}`)
}


const changeStatus = async () => {

    const result = await Swal.fire({
        title: 'Veuillez confirmer!',
        text: `Voulez-vous changer le statut en ?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#42A71E',
        cancelButtonColor: 'var(--lcdtOrange)',
        cancelButtonText: 'Annuler',
        confirmButtonText: `Oui, s'il vous plaît.`
    })

    if (result.isConfirmed) {
        modal.show = true
        store.dispatch(`${CONTACT_LIST_MODULE}${GET_CONTACT_STATUSES}`)
    }

}

const confirmChangeStatus = async () => {
    
    try {

        if(!status.value) {
            store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                type: 'danger',
                message: 'Please enter a status',
                ttl: 5,
            })
            return
        }

        showloader.value = true
        modal.show = false
        await store.dispatch(`${CONTACT_LIST_MODULE}${CHANGE_CONTACT_STATUS}`, { 
            id: details.value.id, 
            statusId: status.value 
        })

        store.commit(`${ITEM_LIST_MODULE}${ITEM_LIST_UPDATE_ROW}`, {
            id: 'id', idValue: details.value?.id, 
            colName: 'product_affiliate_price', colValue: details.value?.wholesale_price
        })

        router.replace({
            name: 'contact'
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

const valider = async () => {

    try {

        showloader.value = true

        await store.dispatch(`${OUVRAGE_MODULE}${VALIDER_OUVRAGE}`, details.value)

        router.replace({
            name: 'ouvrage'
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

onMounted(() => {
    getOuvrageDetails()
})

onBeforeMount(() => {
    resetDetails()
})


</script>

<style lang="scss" scoped>

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

.action-commercial-section, .history-section, .raison-social-entite-section {
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

.responsable-section {
    .title-label {
        font-weight: 300 !important;
    }
}

.history-section {
    .title-rows {
       .title-label {
        font-weight: 300;
       }
    }
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
    grid-template-columns: repeat(3, 1fr);
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

.history-section {
    .title-rows, .detail-rows {
        grid-template-columns: repeat(6, 1fr) !important;
        justify-items: center;
    }
}


</style>


