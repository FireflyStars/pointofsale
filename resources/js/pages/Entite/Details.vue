<template>

<item-detail-panel :showloader="showloader">
    {{ details }}
    <div class="panel-heading-section d-flex align-items-center gap-4" v-if="show">
    
        <h1 class="heading">
            <Icon name="user-star" />
            <span style="vertical-align: bottom;">N°{{ details.id }}</span>
        </h1>
        <div class="text-editer">
            Editer
        </div>
        <div class="tag" style="margin-left: 3rem">
            A envoyer        
        </div>

    </div>

    <div class="responsable-section d-flex justify-content-between align-items-center" v-if="show">
    
        <div class="d-flex flex-column gap-1">
            <span class="title-label">Responsable action :</span>
            <span class="detail text-capitalize">{{ details.action }}</span>
        </div>

        <div class="d-flex flex-column gap-1">
            <span class="title-label">Date de l action :</span>
            <span class="detail">{{ moment(details.action_date).format('Y/MM/DD') }}</span>
        </div>

        <div class="d-flex flex-column gap-1">
            <span class="title-label">Type action</span>
            <span class="detail">{{ details.event_type }}</span>
        </div>

        <div class="d-flex flex-column gap-1">
            <span class="title-label">Origine</span>
            <span class="detail">{{ details.origin }}</span>
        </div>

    </div>

    <hr />


    <div class="raison-social-entite-section" v-if="show">
    
        <h2 class="heading-label">Raison social Entite</h2>

        <div class="row mt-4">
            <div class="col">
                <span class="title-label">Adresse du chantier</span>
                <span class="detail" v-html="details.address"></span>
            </div>
            <div class="col">
                <span class="title-label">Adresse facturation</span>
                <span class="detail">12 Leonard Street 31000 Toulouse</span>
            </div>
        </div>

        <div class="row" style="margin-top: 1.875rem;">
            <div class="col">
                <span class="title-label">Contact</span>
                <span class="detail" v-html="details.contact"></span>
            </div>
            <div class="col">
                <span class="title-label">Mode de paiement</span>
                <span class="detail">50% au lancement</span>
            </div>
        </div>

    </div>

    <hr>

    <div class="action-commercial-section" v-if="show">

        <h4 class="heading-label-fade">Action commerciale</h4>

        <h4 class="heading-label" style="margin-left: 2.18rem; margin-top: 10px;">{{ details.action }}</h4>

        <p 
            class="detail-label-fade text-justify" 
            style="width: 80%; margin: 10px auto 0 auto;"
        >
            {{ details.description }}
        </p>

    </div>

    <div class="history-section" v-if="show">

        <h4 class="heading-label-fade">Historique</h4>

        <div class="title-rows text-center">
            <div class="title-label"></div>
            <div class="title-label">Contact</div>
            <div class="title-label">Status</div>
            <div class="title-label">Contact</div>
            <div class="title-label">Commentaire</div>
        </div>

        <div class="detail-rows" style="margin-top: 1rem">

            <div class="radio-button"></div>

            <div>Laurent</div>

            <div class="tag">Creation</div>

            <div class="">Laurent</div>
            <div class="">Laurent</div>

        </div>

    </div>

    <div class="devis-section" v-if="show">

        <h4 class="heading-label-fade">Devis / Commande</h4>

        <div class="title-rows">
            <div class="title-label"></div>
            <div class="title-label">Numero</div>
            <div class="title-label">Status</div>
            <div class="title-label">Type</div>
            <div class="title-label">Status</div>
            <div class="title-label total">Total</div>
        </div>

        <div class="detail-rows" style="margin-top: 1.5rem;">

            <div class="radio-button"></div>

            <div>8547</div>
            <div>Laurent</div>
            <div>Devis</div>

            <div class="tag">Creation</div>

            <div class="">2300, 00 &euro;</div>

        </div>

    </div>

    <div class="footer-section d-flex align-items-center gap-4">

        <div style="flex: 1">
            <base-button title="Editer" kind="green" />
        </div>

        <base-button title="affecter a" kind="primary" class="text-capitalize" />
        <base-button title="Litige" kind="danger" class="text-capitalize" />
        <base-button title="Annuler" />
        

    </div>


</item-detail-panel>


</template>

<script setup>

import moment from 'moment'
import { computed, onMounted, ref } from 'vue'
import ItemDetailPanel from '../../components/miscellaneous/ItemListTable/ItemDetailPanel.vue'
import StatusTag from '../../components/ActionCo/StatusTag.vue'
import { useStore } from 'vuex'

import {
    TOASTER_MODULE,
    TOASTER_MESSAGE,
    ENTITE_LIST_MODULE,
    GET_ENTITE_DETAILS,
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

onMounted(() => {
    getEntiteDetails()
})


</script>

<style lang="scss" scoped>

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


