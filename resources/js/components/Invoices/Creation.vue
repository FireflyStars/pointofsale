<template>

    <div class="bg-panel p-5 rounded">

        <div class="panel-heading-section d-flex align-items-center" style="gap: 8rem;">
    
            <div class="d-flex gap-4 align-items-center">
    
                <h1 class="heading">
                    <Icon name="pdf" />
                    <span style="vertical-align: bottom;">N°{{ invoice?.reference || '--/--' }}</span>
                </h1>
    
            </div>
        
    
            <div class="d-flex align-items-center justify-content-between gap-2" v-if="show">
    
                <div class="status-tag">CREATION</div>
            
            </div>
    
    
        </div>
    
        <div class="responsable-section d-flex justify-content-between align-items-center">
        
            <div class="d-flex flex-column gap-1">
                <span class="title-label">Commande:</span>
                <span class="detail">
                    N {{ invoice?.order?.id || '--/--' }}
                </span>
            </div>
    
            <div class="d-flex flex-column gap-1">
                <span class="title-label">Date Commande</span>
                <span class="detail">
                    {{ 
                        invoice?.datecommande != null && invoice?.commande != undefined 
                        ? moment(invoice.datecommande).format('DD/MM/YYYY')
                        : '--/--' 
                    }}
                </span>
            </div>
    
            <div class="d-flex flex-column gap-1">
                <span class="title-label">Date Facture</span>
                <span class="detail">
                    {{ 
                        invoice?.created_at != null && invoice?.created_at != undefined 
                        ? moment(invoice.created_at).format('DD/MM/YYYY')
                        : '--/--' 
                    }}
                </span>
            </div>
    
        </div>
    
        <hr />

        <div class="responsable-section">

            <h4 class="heading-label-fade">Usine Logo</h4>

            <div class="content">

                <div class="d-flex flex-column gap-1">
                    <span class="title-label">Adresse du chantier:</span>
                    <span class="detail" v-html="formattedAddress"></span>
                </div>
        
                <div class="d-flex flex-column gap-1">
                    <span class="title-label">Adresse facturation</span>
                    <span class="detail">
                        {{ invoice?.customer_address?.address_type?.name  || 'PAS D ADRESSE CLIENTE' }}
                    </span>
                </div>

                <div class="d-flex flex-column gap-1">
                    <span class="title-label">Contact</span>
                    <span class="detail" v-html="contact"></span>
                </div>
    
                <div class="d-flex flex-column gap-1">
                    <span class="title-label">Mode de paiement</span>
                    <span class="detail">
                        {{ invoice?.customer?.paiement?.name || '--/--' }}
                    </span>
                </div>

            </div>

        
        </div>


        <hr />

        <div class="history-section position-relative" v-if="show">
    
            <div class="title-rows text-center">
                <div class="title-label">Description</div>
                <div class="title-label">Montant</div>
                <div class="title-label">Taxe</div>
                <div class="title-label">Commentaire</div>
                <div class="title-label"></div>
            </div>
    
            <div 
                v-for="detail in invoice?.details"
                :key="detail.id"
                class="detail-rows" 
                style="margin-top: 1rem"
            >
    
                <div class="d-flex align-items-center gap-3">
                    <div class="radio-button" style="width: 15px;"></div>
                    <div>{{ detail.description }}</div>
                </div>
    
                <div>{{ detail?.montant }}</div>
    
                <div>{{ detail?.tax?.name }}</div>
    
                <div>{{ detail?.comment }}</div>
    
                <div @click.prevent="deleteLigne(detail)" title="Delete Ligne">
                    <Icon name="delete" style="cursor: pointer" />
                </div>
    
            </div>

            <span class="font-14 mulish_600_normal ligne_action noselect" @click="openLigneModal">
                <icon name="plus-circle" width="16px" height="16px" /> 
                AJOUTER LIGNE
            </span>

    
        </div>
    
    
        <div class="footer-section d-flex justify-content-between align-items-center" v-if="show">
    
            <base-button 
                title="valider" 
                kind="green"
                class="text-uppercase"
                @click.prevent="$router.push({
                    name: 'FacturePage',
                    params: {
                        id: invoice.value?.id
                    }
                })"
            />
            
            <base-button 
                title="annuler"
                kind="danger" 
                class="text-uppercase" 
            />
    
        </div>

    </div>
    

    <simple-modal-popup 
        v-model="modal.show" 
        :title="modal.title" 
        confirmButtonTitle="Validé" 
        @modalconfirm="createLigne"
    >
        
        <div class="container-fluid">

            <div class="row mb-3">
                
                <div class="col">
                    <label>Description</label>
                    <div>
                        <input type="text" v-model="ligne.description" class="input-text"/>
                    </div>
                </div>
                <div class="col">
                    <label>Montant</label>
                    <div>
                        <money3 v-model="ligne.montant" v-bind="moneyconfig"></money3>
                    </div>
                </div>
                <div class="col">
                    <label>TAXE</label>
                    <div>
                        <select-box
                            v-model="ligne.tax" 
                            placeholder="Tax details" 
                            :options="taxList" 
                            name="tax" 
                        />    
                    </div>
                </div>

            </div>
            
            <div class="row mb-3">
    
                <div class="col-12">NOTES / INFORMATIONS / COMMENTAIRES</div>
                
                <div class="col-12 d-flex align-items-center gap-2">
    
                    <textarea v-model="ligne.comment"></textarea>
    
                </div>
    
            </div>
    
        </div>
         
    </simple-modal-popup>
    
    
    
</template>
    
    <script setup>
    
    import Swal from 'sweetalert2'
    import moment from 'moment'
    import { computed, onMounted, unref, ref, reactive } from 'vue'
    import { useStore } from 'vuex'
    import { useRouter } from 'vue-router'
    import { Money3Component as money3 } from 'v-money3';

    
    import {
        LOADER_MODULE,
        DISPLAY_LOADER,
        HIDE_LOADER,
        TOASTER_MODULE,
        TOASTER_MESSAGE,
        INVOICE_MODULE,
        GET_TAX_LIST,
        CREATE_LIGNE,
        DELETE_LIGNE
    }
    from '../../store/types/types'

    
    const store = useStore()
    const router = useRouter()
    const showloader = ref(false)
    const show = ref(true)
    const comment = ref(null)
    const modal = reactive({
        show: false,
        title: ''
    })
    
    const fetched = reactive({
        event_history: false,
        orders: false,
        event_invoices: false,
    })

    const moneyconfig = ref({
        masked: false,
        prefix: '',
        suffix: '€',
        thousands: ',',
        decimal: '.',
        precision: 2,
        disableNegative: false,
        disabled: false,
        min: null,
        max: null,
        allowBlank: false,
        minimumNumberOfCharacters: 0,
    })

    const ligne = reactive({
        description: null,
        montant: 0,
        tax: 0,
        comment: null
    })
    
    const invoice = computed(() => store.getters[`${INVOICE_MODULE}invoice`].invoice)
    const taxList = computed(() => { 
        return store.getters[`${INVOICE_MODULE}taxList`]
                .map(tax => {
                    return {
                        value: tax.id,
                        display: tax.name
                    }
                }) 
    })

    const formattedAddress = computed(() => {
    
        if(typeof invoice.value?.customer_address == 'undefined' || invoice.value?.customer_address == null) return 'PAS D ADRESSE CLIENTE'
        if(!Object.entries(invoice.value?.customer_address)?.length) return 'PAS D ADRESSE CLIENTE'
        
        const { firstname = '', lastname = '', address1 = '', address2 = '', postcode = '', city = '' } = invoice.value?.customer_address

        const name = firstname + ' ' + lastname
        
        return `
        ${name ? name + '<br>' : ''}
        ${address1 ? address1 + '<br>' : ''}
        ${address2 == null ? '' : address2 + '<br>'}
        ${postcode ? postcode + '<br>' : ''}
        ${city || ''}
        `
    })

    const contact = computed(() => {
    
        if(typeof invoice.value?.customer_contact == 'undefined' || invoice.value?.customer_contact == null) return '--/--'
        if(!Object.entries(invoice.value?.customer_contact)?.length) return '--/--'

        const { firstname = '', email = '', mobile = '' } = invoice.value?.customer_contact

        return `
            ${firstname ? firstname + '<br>' : ''}
            ${email ? email + '<br>' : ''}
            ${mobile || ''}
        `
    })
    
    const openLigneModal = () => {

        modal.show = true
        modal.title = 'AJOUTER LIGNE'

        store.dispatch(`${INVOICE_MODULE}${GET_TAX_LIST}`)
    
    }
    
    const createLigne = async () => {
        
        try {

            if(ligne.tax == 0) {
                store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                    type: 'danger',
                    message: 'Tax is invalid',
                    ttl: 5,
                })
                return
            }

            if(ligne.montant == 0 || ligne.montant == '') {
                store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                    type: 'danger',
                    message: 'Montant is invalid',
                    ttl: 5,
                })
                return
            }
            
            store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'créer un ligne..']) 

            await store.dispatch(`${INVOICE_MODULE}${CREATE_LIGNE}`, { ligne: unref(ligne), id: invoice?.value?.id })
            
            modal.show = false

            resetLigne()
    
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
            store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`)
        }
    
    }

    const deleteLigne = async (detail) => {
        
        try {
            
            const result = await Swal.fire({
                title: 'Veuillez confirmer!',
                text: `Voulez vous archiver ce ligne?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#42A71E',
                cancelButtonColor: 'var(--lcdtOrange)',
                cancelButtonText: 'Annuler',
                confirmButtonText: `Oui, s'il vous plaît.`
            })

            if(result.isConfirmed) {

                store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'Delete un ligne..']) 
                await store.dispatch(`${INVOICE_MODULE}${DELETE_LIGNE}`, { id: detail?.id, invoiceId: invoice?.value?.id })

            }

            
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
            store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`)
        }

    }

    const resetLigne = () => {
        ligne.description = null
        ligne.montant = 0
        ligne.tax = 0
        ligne.comment = null
    }
    
    
    const appendResults = async (type) => {
    
        if(loading.value.status) return
        
        try {
            showloader.value = true
            await store.dispatch(`${INVOICE_MODULE}${GET_ENTITE_RESULTS}`, { type, id: details.value.id })
            fetched[type] = true
        }
    
        catch(e) {
            throw e
        }
    
        finally {
            showloader.value = false
        }
    
    
    }
  
    
    
    </script>
    
    <style lang="scss" scoped>
    
    .ligne_action {
        color:#E8581B;
        cursor: pointer;
        display: flex;
        justify-content: center;
        margin-top: 2rem;
        align-items: center;
        gap: 0.5rem;
    }
    .status-tag {
        font-family: 'Almarai Regular';
        font-style: normal;
        font-weight: 700;
        font-size: 12px;
        line-height: 13px;
        color: #42A71E;
        background: rgba(66, 167, 30, 0.2);
        border-radius: 70px;
        width: 104px;
        height: 23px;
        display: flex;
        align-items: center;
        justify-content: center;
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
        display: flex;
        align-items: center;
        gap: 2;
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
        .content {
            display: grid;
            justify-content: space-between;
            align-items: center;
            grid-template-columns: 50% 50%;
        }
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

    .title-label {
        font-weight: 400 !important;
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
    
    
    