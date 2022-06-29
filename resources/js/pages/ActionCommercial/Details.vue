<template>

<item-detail-panel :showloader="showloader">

    <div class="panel-heading-section d-flex align-items-center gap-4">
    
        <h1 class="heading">
            <Icon name="user-star" />
            <span style="vertical-align: bottom;">N°{{ details.id || '--/--' }}</span>
        </h1>

        <div class="text-editer">
            Editer
        </div>

        <div 
            v-show="show"
            class="tag" 
            style="margin-left: 3rem"
            :style="{ 'background': details?.event_status_color }"
        >
            {{ details.event_status }}        
        </div>

    </div>

    <div class="responsable-section d-flex justify-content-between align-items-center">
    
        <div class="d-flex flex-column gap-1">
            <span class="title-label">Responsable action :</span>
            <span class="detail text-capitalize">{{ details.client_name || '--/--' }}</span>
        </div>

        <div class="d-flex flex-column gap-1">
            <span class="title-label">Date de l action :</span>
            <span class="detail">{{ details.event_date ? moment(details.event_date).format('Y/MM/DD HH:mm') : '--/--' }}</span>
        </div>

        <div class="d-flex flex-column gap-1">
            <span class="title-label">Type action</span>
            <span class="detail">{{ details.event_type || '--/--' }}</span>
        </div>

        <div class="d-flex flex-column gap-1">
            <span class="title-label">Origine</span>
            <span class="detail">{{ details.event_origin || '--/--' }}</span>
        </div>

    </div>

    <hr />


    <div class="raison-social-entite-section">
    
        <h2 class="heading-label">Raison social Entite</h2>

        <div class="row mt-4">
            <div class="col">
                <span class="title-label">Adresse du chantier</span>
                <span class="detail" v-html="formattedAddress"></span>
            </div>
            <div class="col">
                <span class="title-label">Adresse facturation</span>
                <span class="detail">{{ details.address_type?.name  || '--/--'}}</span>
            </div>
        </div>

        <div class="row" style="margin-top: 1.875rem;">
            <div class="col">
                <span class="title-label">Contact</span>
                <span class="detail" v-html="contact"></span>
            </div>
            <div class="col">
                <span class="title-label">Mode de paiement</span>
                <span class="detail">{{ details.paiement?.name || '--/--' }}</span>
            </div>
        </div>

    </div>

    <hr>

    <div class="action-commercial-section">

        <h4 class="heading-label-fade">Action commerciale</h4>

        <h4 class="heading-label" style="margin-left: 2.18rem; margin-top: 10px;">{{ details.event_name || '--/--' }}</h4>

        <p 
            class="detail-label-fade text-justify" 
            style="width: 80%; margin: 10px auto 0 auto;"
        >
            {{ details.event_description || '--/--' }}
        </p>

    </div>

    <div class="history-section" v-if="show">

        <a href="#" class="link" @click.prevent="fetchEventHistory" v-if="!fetchedHistory">Voir tout</a>

        <h4 class="heading-label-fade">Historique</h4>

        <div class="title-rows text-center">
            <div class="title-label">Date</div>
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
                <div class="radio-button"></div>
                <div>{{ moment(history.created_at).format('DD/MM/Y HH:mm') }}</div>
            </div>

            <div 
                class="tag"
                :style="{ 'background': history?.status?.color }"
            >
                {{ history?.status?.name }}
            </div>

            <div class="">{{ history.user?.name }}</div>
            <div class="">{{ history.comment }}</div>

        </div>

    </div>

    <div class="devis-section" v-if="show">

        <h4 class="heading-label-fade">Devis / Commande</h4>

        <div class="title-rows">
            <div class="title-label">Date</div>
            <div class="title-label">Numero</div>
            <div class="title-label">Responsable</div>
            <div class="title-label">Type</div>
            <div class="title-label">Status</div>
            <div class="title-label total">Total</div>
        </div>

        <div class="detail-rows" style="margin-top: 1.5rem;">

            <div class="d-flex align-items-center gap-3">
                <div class="radio-button" style="width: 16px;"></div>
                <div>{{ moment(details?.order?.created_at).format('DD/MM/Y HH:mm') }}</div>
            </div>


            <div>
                <router-link 
                    class="devisLink"
                    :to="{ 
                        name: 'DevisDetail',
                        params: {
                            id: details?.order?.nbheure
                        } 
                    }"

                >
                    {{ details?.order?.nbheure }}
                </router-link>
            </div>
            <div>{{ details?.order?.user?.name || '' }}</div>
            <div>{{ details?.order?.state?.order_type }}</div>

            <div 
                class="tag" 
                :style="{ 
                    background: details?.order?.state?.color, 
                    color: details?.order?.state?.fontcolor 
                }"
            >
                {{ details?.order?.state?.name }}
            </div>

            <div class="">{{ details?.order?.total?.toFixed(2) }} &euro;</div>

        </div>

    </div>

    <div class="footer-section d-flex justify-content-between align-items-center gap-2">

        <base-button 
            :disabled="showloader"
            title="Editer" 
            kind="green" 
            @click.prevent="$router.push({ path: `/action-commercial/edit/${details.id}` })" 
        />

        <base-button 
            :disabled="showloader"
            title="Replanifier" 
            kind="light-green" 
            class="text-uppercase"
            @click.prevent="changeDate" 
        />

        <base-button 
            :disabled="showloader"
            title="affecter a" 
            kind="primary" 
            class="text-uppercase" 
            @click.prevent="changeEventUser"
        />

        <base-button 
            :disabled="showloader"
            title="Effacer" 
            kind="danger" 
            class="text-uppercase" 
            @click.prevent="changeEffacer"
        />

        <base-button 
            :disabled="showloader"
            title="Annuler" 
            @click.prevent="changeAnnuler"
        />


    </div>


</item-detail-panel>

<simple-modal-popup 
    v-model="modal.show" 
    :title="modal.title" 
    confirmButtonTitle="Change" 
    @modalconfirm="commitAction"
>
    
    <div class="container-fluid" v-if="modal.status == 'date'">
        
        <div class="row mb-3">
            <div class="col-6">Datedebut</div>
            <div class="col-6">
                <date-picker 
                    :disabledToDate="disabledToDate" 
                    name="datedebut" 
                    :droppos="{ top: '40px', right: 'auto', bottom: 'auto', left: '0', transformOrigin: 'top center'}" 
                    @changed="datedebut = $event.date"
                />
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-6">DateFin</div>
            <div class="col-6">
                <date-picker 
                    :disabledToDate="disabledDateFin" 
                    name="datefin" 
                    :droppos="{ top: '40px', right: 'auto', bottom: 'auto', left: '0', transformOrigin: 'top center'}"
                    @changed="datefin = $event.date"
                />
            </div>
        </div>

    </div>

    <div class="container-fluid" v-else-if="modal.status == 'user'">

        <select-box
            v-model="eventUser" 
            placeholder="Choose User" 
            :options="userList" 
            name="user_list" 
            label="User List"
            :disabled="!userList.length" 
        />

    </div>

    <div class="conatiner-fluid" v-else-if="modal.status == 'effacer'">

        <div class="container-fluid">
        
        </div>

    </div>

    <div class="conatiner-fluid" v-else-if="modal.status == 'annuler'">

        <div class="container-fluid">
        
        </div>

    </div>
     
</simple-modal-popup>


</template>

<script setup>

import Swal from 'sweetalert2'
import moment from 'moment'
import { computed, onMounted, ref, reactive } from 'vue'
import { useStore } from 'vuex'

import ItemDetailPanel from '../../components/miscellaneous/ItemListTable/ItemDetailPanel.vue'
import StatusTag from '../../components/ActionCo/StatusTag.vue'

import {
    TOASTER_MODULE,
    TOASTER_MESSAGE,
    ACTION_COMMERCIAL_MODULE,
    GET_ACTION_COMMERCIAL_DETAILS,
    CHANGE_EVENT_DATE,
    GET_EVENT_USER_LIST,
    CHANGE_EVENT_USER,
    GET_EVENT_HISTORY,
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

const modal = reactive({
    show: false,
    title: '',
    status: ''
})

const datedebut = ref(null)
const datefin = ref(null)
const eventUser = ref(null)

const disabledToDate = computed(() => new Date(new Date().getTime() - 24*60*60*1000).toJSON().slice(0,10))
const disabledDateFin = computed(() => datedebut.value ? moment(datedebut.value).format('Y-MM-DD') : disabledToDate.value)

const details = computed(() => store.getters[`${ACTION_COMMERCIAL_MODULE}details`])
const fetchedHistory = computed(() => store.getters[`${ACTION_COMMERCIAL_MODULE}fetchedHistory`])


const formattedAddress = computed(() => {
    
    if(typeof details.value?.address == 'undefined') return '--/--'
    if(!Object.entries(details.value?.address).length) return '--/--'
    
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

const userList = computed(() => {
    return store.getters[`${ACTION_COMMERCIAL_MODULE}userList`].map(user => {
        return {
            value: user.id,
            display: user.name
        }
    })
})

const fetchEventHistory = () => {
    
    try {
        showloader.value = true
        store.dispatch(`${ACTION_COMMERCIAL_MODULE}${GET_EVENT_HISTORY}`, { id: details.value.id })
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

const changeDate = async () => {

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
        modal.title = 'Change Event Date'
        modal.status = 'date' 
    }

}

const changeEventUser = async () => {

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
        modal.title = 'Change Event User'
        modal.status = 'user' 
        store.dispatch(`${ACTION_COMMERCIAL_MODULE}${GET_EVENT_USER_LIST}`, details.value.id)
    }

}

const changeEffacer = async () => {
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
        modal.title = 'Change Event Status'
        modal.status = 'effacer' 
    }
}

const changeAnnuler = async () => {
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
        modal.title = 'Change Event Status'
        modal.status = 'annuler' 
    }
}

const commitAction = async () => {

    if(modal.status == 'date') {

        try {

            showloader.value = true
            modal.show = false

            await store.dispatch(`${ACTION_COMMERCIAL_MODULE}${CHANGE_EVENT_DATE}`, {
                id: details.value.id,
                datedebut: datedebut.value,
                datefin: datefin.value
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


    else if(modal.status == 'user') {

        try {
            showloader.value = true
            modal.show = false
            store.dispatch(`${ACTION_COMMERCIAL_MODULE}${CHANGE_EVENT_USER}`, {
                id: details.value.id,
                userId: eventUser.value
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

    else if(modal.status == 'effacer') {}

    else if(modal.status == 'annuler') {}




}


const getActionCommercialDetails = async () => {
    console.log(props.id, " is the props id")
    try {
        showloader.value = true
        show.value = false
        await store.dispatch(`${ACTION_COMMERCIAL_MODULE}${GET_ACTION_COMMERCIAL_DETAILS}`, props.id)
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

onMounted(() => {
    getActionCommercialDetails()
})


</script>

<style lang="scss" scoped>


.devisLink {
    font-weight: bold; 
    color: #000; 
    text-decoration: none;
    &:hover {
        text-decoration: underline;
    }
}

.panel-heading-section {
    margin-top: 2.25rem;
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
    background: rgba(241, 210, 164, 0.7);
    border-radius: 70px;
    font-family: 'Almarai Bold';
    font-style: normal;
    font-weight: 700;
    font-size: 12px;
    line-height: 13px;
    color: #000000;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    padding:0 10px;
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
    position: relative;
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
    font-family: 'Almarai Bold';
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
        align-items: center;
    }
}


</style>


