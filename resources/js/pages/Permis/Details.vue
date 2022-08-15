<template>

<item-detail-panel :showloader="showloader">

    <div class="panel-heading-section d-flex align-items-center" style="gap: 8rem;">

        <div class="d-flex gap-4 align-items-center mt-2">

            <h1 class="heading d-flex align-items-center gap-2">
                <Icon name="fournisseur-plain" />
                <span style="vertical-align: bottom;">N°{{ details.id || '--/--' }}</span>
            </h1>

            <div class="text-editer cursor-pointer" @click.prevent="$router.push({
                    path: `/fournisseur/edit/${details.id}`
                })"
            >
                Editer
            </div>
        
        </div>

        <div 
            v-if="show"
            class="tag" 
            :style="{ background: details.status?.color }"
        >
            {{ details.status?.name }}
        </div>
    

        <div class="d-flex align-items-center justify-content-between gap-2" v-if="show">

            <div 
                class="status-tag"
            >
                <span>Actif:</span> 
                <Icon name="check" v-if="details.active == 1" width="22.2" height="16.2" />        
                <Icon name="times-circle" v-else width="22" height="22" />        
            </div>

        </div>


    </div>

    <div class="responsable-section d-flex justify-content-between">
    
        <div class="d-flex flex-column gap-1">
            <span class="title-label">XXXXXXXXX</span>
            <span class="detail"></span>
        </div>

        <div class="d-flex flex-column gap-1">
            <span class="title-label">Contact</span>
            <span class="detail" v-html="details.contact"></span>
        </div>

        <div class="d-flex flex-column gap-1">
            <span class="title-label">Date créaction :</span>
            <span class="detail">
                {{ 
                    details.created_at != null 
                    ? moment(details.created_at).format('Y/MM/DD H:m')
                    : '--/--' 
                }}
            </span>
        </div>

        <div class="d-flex flex-column gap-1">
            <span class="title-label">Type</span>
            <span class="detail">{{ details.type?.name || '--/--' }}</span>
        </div>

    </div>

    <hr />

    <div class="history-section position-relative" v-if="show && details.orders.length">

        <a 
            href="#" 
            class="link d-flex align-items-center" 
            @click.prevent="appendResults('orders')"
            style="gap: .4rem"
            v-if="!!fetched.orders === false"
        >
            Voir tout
            <Icon 
                class="icon"
                name="spinner"
                v-show="loading?.status == true && loading?.id == 'orders'"
            />
        </a>

        <h4 class="heading-label-fade">Historique Devis / Commande</h4>

        <div class="title-rows text-center">
            <div class="title-label">Date</div>
            <div class="title-label">Numero</div>
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
                <div class="radio-button" style="width: 15px;"></div>
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

    <div class="footer-section d-flex align-items-center gap-4" v-if="show">

        <div style="flex: 1">
        
            <base-button 
                title="Editer" 
                kind="green" 
                @click.prevent="$router.push({ path: `/fournisseur/edit/${details.id}` })" 
            />

        </div>

        <base-button 
            title="deactiver" 
            kind="danger"
            class="text-uppercase"
        />
        
        <base-button 
            title="CREER COMMANDE"
            kind="success" 
            class="text-uppercase" 
        />

    </div>


</item-detail-panel>

<simple-modal-popup 
    v-model="modal.show" 
    :title="modal.title" 
    confirmButtonTitle="Litige" 
    @modalconfirm="confirmChangeLitige"
>
    
    <div class="container-fluid">
        
        <div class="row mb-3">

            <div class="col-4">COMMENTAIRE</div>
            
            <div class="col-8 d-flex align-items-center gap-2">

                <textarea v-model="comment"></textarea>

            </div>

        </div>

    </div>
     
</simple-modal-popup>



</template>

<script setup>

import Swal from 'sweetalert2'
import moment from 'moment'
import { computed, onMounted, ref, onBeforeMount, reactive } from 'vue'
import ItemDetailPanel from '../../components/miscellaneous/ItemListTable/ItemDetailPanel.vue'
import StatusTag from '../../components/ActionCo/StatusTag.vue'
import { useStore } from 'vuex'
import { useRouter } from 'vue-router'

import {
    TOASTER_MODULE,
    TOASTER_MESSAGE,
    ENTITE_LIST_MODULE,
    GET_ENTITE_DETAILS,
    RESET_DETAILS,
    CHANGE_LITIGE,
    ITEM_LIST_MODULE,
    ITEM_LIST_REMOVE_ROW,
    CHANGE_ACTIF,
    FOURNISSEUR_LIST_MODULE,
    FOURNISSEUR_GET_DETAILS,
    FOURNISSEUR_APPEND_RESULTS
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
const modal = reactive({
    show: false,
    title: 'Mettre cet entité en Litige'
})

const fetched = reactive({
    event_history: false,
    orders: false,
    event_invoices: false,
})

const details = computed(() => store.getters[`${FOURNISSEUR_LIST_MODULE}details`])
const loading = computed(() => store.getters[`${FOURNISSEUR_LIST_MODULE}loading`])

const actifBackground = computed(() => {
    return details.value?.active == 1 ? 'rgba(66, 167, 30, 0.2)' : '#ff000045'
})

const litigeBackground = computed(() => {
    return details.value?.litige == 1 ? 'rgba(66, 167, 30, 0.2)' : '#ff000045'
})

const getFournisseurDetails = async () => {

    try {
        showloader.value = true
        show.value = false
        await store.dispatch(`${FOURNISSEUR_LIST_MODULE}${FOURNISSEUR_GET_DETAILS}`, props.id)
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
    store.commit(`${FOURNISSEUR_LIST_MODULE}${RESET_DETAILS}`)
}

const changeLitige = async () => {

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
    }

}

const confirmChangeLitige = async () => {
    
    try {

        showloader.value = true
        modal.show = false
        await store.dispatch(`${ENTITE_LIST_MODULE}${CHANGE_LITIGE}`, { 
            id: details.value.id, 
            comment: comment.value 
        })

        router.replace({ name: 'entite' })

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
        await store.dispatch(`${FOURNISSEUR_LIST_MODULE}${FOURNISSEUR_APPEND_RESULTS}`, { type, id: details.value.id })
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
    getFournisseurDetails()
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

.responsable-section {
    .title-label {
        font-weight: 400 !important;
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
    grid-template-columns: repeat(5, 1fr);
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


