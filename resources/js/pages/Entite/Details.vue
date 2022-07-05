<template>

<item-detail-panel :showloader="showloader">

    <div class="panel-heading-section d-flex align-items-center" style="gap: 8rem;">

        <div class="d-flex gap-4 align-items-center">

            <h1 class="heading">
                <Icon name="entite" />
                <span style="vertical-align: bottom;">N°{{ details.id || '--/--' }}</span>
            </h1>

            <div class="text-editer cursor-pointer" @click.prevent="$router.push({
                    path: `/customer/edit/${details.id}`
                })"
            >
                Editer
            </div>
        
        </div>
    

        <div class="d-flex align-items-center justify-content-center gap-2">

            <div 
                class="tag text-uppercase"
                :style="{ background: actifBackground }" 
            >
                Actif        
            </div>

            <div 
                class="tag text-uppercase"
                :style="{ background: litigeBackground }"
            >
                No litige        
            </div>
        
        </div>


    </div>

    <div class="responsable-section d-flex justify-content-between align-items-center">
    
        <div class="d-flex flex-column gap-1">
            <span class="title-label">Date créaction :</span>
            <span class="detail">{{ moment(details.created_at).format('Y/MM/DD') }}</span>
        </div>

        <div class="d-flex flex-column gap-1">
            <span class="title-label">Type action</span>
            <span class="detail">{{ details.status.name }}</span>
        </div>

        <div class="d-flex flex-column gap-1">
            <span class="title-label">Origine</span>
            <span class="detail">{{ details.origin?.name || '--/--' }}</span>
        </div>

    </div>

    <hr />

    <div class="raison-social-entite-section">

        <div class="d-flex align-items-center justify-content-between">

            <h2 class="heading-label">{{ details?.raisonsociale }}</h2>
            <h2 class="heading-label">{{ details?.company }}</h2>
            <h2 class="heading-label">{{ details?.raisonsociale2 }}</h2>
        
        </div>
    

        <div class="row mt-4">
            <div class="col">
                <span class="title-label">Adresse du chantier</span>
                <span class="detail" v-html="formattedAddress"></span>
            </div>
            <div class="col">
                <span class="title-label">Adresse facturation</span>
                <span class="detail">{{ details?.address?.address_type?.name  || 'PAS D ADRESSE CLIENTE' }}</span>
            </div>
        </div>

        <div class="row" style="margin-top: 1.875rem;">
            <div class="col">
                <span class="title-label">Contact</span>
                <span class="detail" v-html="contact"></span>
            </div>
            <div class="col">
                <span class="title-label">Mode de paiement</span>
                <span class="detail">{{ details?.paiement?.name || '--/--'  }}</span>
            </div>
        </div>

    </div>

    <hr />


    <div class="history-section position-relative" v-if="show">

        <a href="#" class="link">Voir tout</a>

        <h4 class="heading-label-fade">Historique Action commerciale</h4>

        <div class="title-rows text-center">
            <div class="title-label">Date</div>
            <div class="title-label">Num</div>
            <div class="title-label">Action</div>
            <div class="title-label">Status</div>
            <div class="title-label">Responsable</div>
            <div class="title-label">Commentaire</div>
        </div>

        <div 
            v-for="history in details.event_history"
            :key="history.id"
            class="detail-rows" 
            style="margin-top: 1rem"
        >

            <div class="d-flex align-items-center gap-3">
                <div class="radio-button" style="width: 16px;"></div>
                <div>{{ history.created_at ? moment(history.created_at).format('DD/MM/Y HH:mm') : '' }}</div>
            </div>

            <div>
                <router-link 
                    class="routerLink"
                    :to="{ 
                        name: 'action-commercial',
                        params: {
                            id: history.id
                        } 
                    }"

                >
                    {{ history.id }}
                </router-link>
            </div>

            <div>{{ history?.event?.name }}</div>

            <div 
                :title="history?.status?.name"
                class="tag"
                :style="{ 'background': history?.status?.color }"
            >
                {{ history?.status?.name }}
            </div>

            <div class="">{{ history.user?.name }}</div>
            <div class="">{{ history.comment }}</div>

        </div>

    </div>


    <div class="devis-section position-relative" v-if="show && details.orders.length">

        <a href="#" class="link">Voir tout</a>

        <h4 class="heading-label-fade">Devis / Commande</h4>

        <div class="title-rows">
            <div class="title-label">Date</div>
            <div class="title-label">Numero</div>
            <div class="title-label">Responsable</div>
            <div class="title-label">Type</div>
            <div class="title-label">Status</div>
            <div class="title-label total">Total</div>
        </div>

        <div 
            v-for="order in details.orders"
            :key="order.id"
            class="detail-rows" 
            style="margin-top: 1.5rem;"
        >

            <div class="d-flex align-items-center gap-3">
                <div class="radio-button" style="width: 16px;"></div>
                <div>{{ order?.created_at ? moment(order?.created_at).format('DD/MM/Y HH:mm') : '' }}</div>
            </div>


            <div>
                <router-link 
                    class="routerLink"
                    :to="{ 
                        name: 'DevisDetail',
                        params: {
                            id: order?.id
                        } 
                    }"

                >
                    {{ order?.id }}
                </router-link>
            </div>
            <div>{{ order?.user?.name || '' }}</div>
            <div>{{ order?.state?.order_type }}</div>

            <div 
                :title="order?.state?.name"
                class="tag" 
                :style="{ 
                    background: order?.state?.color, 
                    color: order?.state?.fontcolor 
                }"
            >
                {{ order?.state?.name }}
            </div>

            <div class="">{{ order?.total?.toFixed(2) }} &euro;</div>

        </div>

    </div>


    <div class="footer-section d-flex align-items-center gap-4" v-if="show">

        <div style="flex: 1">
            <base-button title="Editer" kind="green" />
        </div>

        <base-button title="effacer" kind="primary" class="text-uppercase" />
        <base-button title="Litige" kind="danger" class="text-uppercase" />
        

    </div>


</item-detail-panel>


</template>

<script setup>

import moment from 'moment'
import { computed, onMounted, ref, onBeforeMount } from 'vue'
import ItemDetailPanel from '../../components/miscellaneous/ItemListTable/ItemDetailPanel.vue'
import StatusTag from '../../components/ActionCo/StatusTag.vue'
import { useStore } from 'vuex'

import {
    TOASTER_MODULE,
    TOASTER_MESSAGE,
    ENTITE_LIST_MODULE,
    GET_ENTITE_DETAILS,
    RESET_DETAILS
}
from '../../store/types/types'

const props = defineProps({
    id: {
        required: true,
        type: [String, Number]
    }
})

const store = useStore()
const showloader = ref(false)
const show = ref(true)

const details = computed(() => store.getters[`${ENTITE_LIST_MODULE}details`])

const actifBackground = computed(() => {
    return details.value?.active == 1 ? 'rgba(66, 167, 30, 0.2)' : '#ff000045'
})

const litigeBackground = computed(() => {
    return details.value?.litige == 1 ? 'rgba(66, 167, 30, 0.2)' : '#ff000045'
})


const formattedAddress = computed(() => {
    
    if(typeof details.value?.address == 'undefined') return 'PAS D ADRESSE CLIENTE'
    if(!Object.entries(details.value?.address).length) return 'PAS D ADRESSE CLIENTE'
    
    const address = details.value?.address
    
    return `
    ${address?.firstname} ${address?.lastname}
    <br> ${address?.address1 + '<br>'}
    ${address?.address2 == null ? '' : address?.address2 + '<br>'}
    ${address?.postcode + '<br>'}
    ${address?.city}
    `
})

const contact = computed(() => {
    
    if(typeof details.value?.contact == 'undefined') return '--/--'
    if(!Object.entries(details.value?.contact).length) return '--/--'

    return `
        ${details.value?.contact?.name}<br>
        ${details.value?.contact?.email}<br>
        ${details.value?.contact?.mobile}
    `
})


const getEntiteDetails = async () => {

    try {
        showloader.value = true
        show.value = false
        await store.dispatch(`${ENTITE_LIST_MODULE}${GET_ENTITE_DETAILS}`, props.id)
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
    store.commit(`${ENTITE_LIST_MODULE}${RESET_DETAILS}`)
}

onMounted(() => {
    getEntiteDetails()
})

onBeforeMount(() => {
    resetDetails()
})


</script>

<style lang="scss" scoped>

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
    font-family: 'Almarai Bold';
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
    width: 104px;
    height: 23px;
    background: rgba(66, 167, 30, 0.2);
    border-radius: 70px;
    font-family: 'Almarai Bold';
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

.devis-section {
    margin-top: 10px;
}

.action-commercial-section, .history-section, .devis-section {
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
    grid-template-columns: repeat(6, 1fr);
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
        grid-template-columns: repeat(6, 1fr);
    }
}


</style>


