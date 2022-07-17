<template>

<item-detail-panel :showloader="showloader">

    <div class="panel-heading-section d-flex align-items-center" style="gap: 8rem;">

        <div class="d-flex gap-4 align-items-center">

            <h1 class="heading">
                <Icon name="user-multiple" style="width: 45px; height: 45px;" />
                <span style="vertical-align: bottom; margin-left: 2rem;">N°{{ details.id || '--/--' }}</span>
            </h1>

            <div class="text-editer cursor-pointer" @click.prevent="$router.push({
                    path: `/personnel/edit/${details.id}`
                })"
            >
                Editer
            </div>

        </div>
    

        <div class="d-flex align-items-center justify-content-between gap-2" v-if="show">

            <div class="tag" :style="{ background: details.status?.color }">
                {{ details.status?.name }}
            </div>
        
        </div>


    </div>

    <div class="responsable-section d-flex justify-content-between align-items-center">
    
        <div class="d-flex flex-column gap-1">
            <span class="title-label">Prenom</span>
            <span class="detail">{{ details?.prenom || '--/--' }}</span>
        </div>

        <div class="d-flex flex-column gap-1">
            <span class="title-label">Nom</span>
            <span class="detail">{{ details?.nom || '--/--' }}</span>
        </div>

        <div class="d-flex flex-column gap-1">
            <span class="title-label">Type</span>
            <span class="detail">{{ details?.type?.name || '--/--' }}</span>
        </div>

        <div class="d-flex flex-column gap-1">
            <span class="title-label">Profil</span>
            <span class="detail">{{ details?.profil?.display_name || '--/--' }}</span>
        </div>

        <div class="d-flex flex-column gap-1">
            <span class="title-label">Affilie</span>
            <span class="detail">{{ details.affiliate?.name || '--/--' }}</span>
        </div>

    </div>

    <hr />

    <UserDocuments :id="details.id" v-if="show" @showloader="showloader=$event.value" />

    <hr />

    <div class="footer-section d-flex align-items-center gap-4" v-if="show">

        <div style="flex: 1">
        
            <base-button 
                title="Editer" 
                kind="green" 
                @click.prevent="$router.push({ path: `/personnel/edit/${details.id}` })" 
            />

        </div>

        <base-button 
            title="archiver" 
            kind="danger"
            class="text-uppercase"
            @click.prevent="deleteUser"
        />
        
    </div>


</item-detail-panel>


</template>

<script setup>

import Swal from 'sweetalert2'
import moment from 'moment'
import { useStore } from 'vuex'
import { useRouter } from 'vue-router'
import { computed, onMounted, ref, onBeforeMount, reactive } from 'vue'
import ItemDetailPanel from '../../components/miscellaneous/ItemListTable/ItemDetailPanel.vue'
import StatusTag from '../../components/ActionCo/StatusTag.vue'
import UserDocuments from '../../components/Personnel/UserDocuments.vue'

import {
    TOASTER_MODULE,
    TOASTER_MESSAGE,
    PERSONNEL_LIST_MODULE,
    GET_PERSONNEL_DETAILS,
    RESET_DETAILS,
    ITEM_LIST_MODULE,
    ITEM_LIST_REMOVE_ROW,
    DELETE_PERSONNEL
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
    title: 'Change Status'
})

const fetched = reactive({
    
})

const details = computed(() => store.getters[`${PERSONNEL_LIST_MODULE}details`])
const loading = computed(() => store.getters[`${PERSONNEL_LIST_MODULE}loading`])


const getUserDetails = async () => {

    try {
        showloader.value = true
        show.value = false
        await store.dispatch(`${PERSONNEL_LIST_MODULE}${GET_PERSONNEL_DETAILS}`, props.id)
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
    store.commit(`${PERSONNEL_LIST_MODULE}${RESET_DETAILS}`)
}


const deleteUser = async () => {

    const result = await Swal.fire({
        title: 'Veuillez confirmer!',
        text: `Voulez vous archiver ce personnel?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#42A71E',
        cancelButtonColor: 'var(--lcdtOrange)',
        cancelButtonText: 'Annuler',
        confirmButtonText: `Oui, s'il vous plaît.`
    })

    if (result.isConfirmed) {
        try {
            showloader.value = true

            await store.dispatch(`${PERSONNEL_LIST_MODULE}${DELETE_PERSONNEL}`, details.value?.id)

            store.commit(`${ITEM_LIST_MODULE}${ITEM_LIST_REMOVE_ROW}`, { id: 'id', idValue: details.value?.id })

            router.replace({
                name: 'personnel'
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

}


onMounted(() => {
    getUserDetails()
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


