<template>

    <mini-panel title="Permis / Certification" style="position: relative;">

        <a 
            href="#" 
            class="link d-flex align-items-center" 
            @click.prevent="appendResults('certification')"
            style="gap: .4rem"
            v-if="!!fetched.certification === false"
        >
            Voir tout
            <Icon 
                class="icon"
                name="spinner"
                v-show="loading?.status == true && loading?.id == 'certification'"
            />
        </a>
    
        <div class="row mb-3">
            <div class="col-4"></div>
            <div class="col-3 almarai-light lcdtgrey d-flex font-12 align-items-center justify-content-center">Validité</div>
            <div class="col-2 almarai-light lcdtgrey d-flex font-12 align-items-center justify-content-center">Date</div>
            <div class="col-1"></div>
            <div class="col-1"></div>
            <div class="col-1"></div>
        </div>
            
        <transition-group tag="div" class="list"  name="list" appear>
            
            <div class="row mb-3" v-for="document, index in documents" :key="index">
                
                <div 
                    class="col-4 documentline almarai_bold_normal font-14  d-flex align-items-center" 
                    :class="{ report: document.template_id > 0 }"
                >
                    {{ document.name }}
                </div>
                <div 
                    class="col-3 almarai-light d-flex font-14 align-items-center justify-content-center">
                    {{ document.date_expired }}
                </div>
                <div 
                    class="col-2 almarai-light d-flex font-14 align-items-center justify-content-center">
                    {{ document.date_document }}
                </div>
                <div 
                    class="col-1 d-flex align-items-center justify-content-center">
                    <span 
                        v-if="document.template_id>0" 
                        @click="editReport(document)"
                    >
                        <icon name="file-outline" width="16px" height="16px" class="cursor-pointer" />
                    </span>
                </div>
                <div class="col-1 d-flex align-items-center justify-content-center">
                    <span @click="document.template_id > 0 ? generatePdfById(document.id) : downloadDoc(document.id)"> 
                        <icon name="download" width="16px" height="16px" class="cursor-pointer" />
                    </span>
                </div>
                <div class="col-1 d-flex align-items-center justify-content-center" >
                    <span @click="document.template_id > 0 ? removeReport(document) : removeDoc(document)">
                        <icon  name="trash-x" width="20px" height="20px" class="cursor-pointer" />
                    </span>
                </div>

            </div>

        </transition-group>

        <div class="d-flex justify-content-evenly mt-4">

            <!-- <span class="font-14 mulish_600_normal document_action noselect" @click="createReport" >
                <icon name="plus-circle" width="16px" height="16px" /> AJOUTER RAPPORT
            </span> -->
            <span class="font-14 mulish_600_normal document_action noselect" @click.prevent="addDocument">
                <icon name="plus-circle" width="16px" height="16px" /> AJOUTER DOCUMENT
            </span>

        </div>  
        
        <input type="file" id="fileuploadEl" class="d-none" ref="fileUploadEl" accept="image/*" />

  </mini-panel>

  <simple-modal-popup 
    v-model="modal.show" 
    :title="modal.title" 
    confirmButtonTitle="Validé" 
    @modalconfirm="fileElUpdated"
    >
        
        <div class="container-fluid">
            
            <div class="row mb-3">

                <div class="col">
                    <input type="text" placeholder="Nom" v-model="document.name">
                </div>
                
                <div class="col d-flex align-items-center gap-2">

                    <date-picker 
                        :disabledToDate="disabledToDate" 
                        name="dateExpiry" 
                        :droppos="{ top: '40px', right: 'auto', bottom: 'auto', left: '0', transformOrigin: 'top center'}" 
                        @changed="document.dateExpired = $event.date"
                    />

                </div>

            </div>

            <div class="row mb-3">

                <div class="col">
                    <base-button title="Upload File" @click.prevent="addfile" />         
                </div>

                <div class="col">
                    <date-picker 
                        :disabledToDate="disabledToDate" 
                        name="dateDocument" 
                        :droppos="{ top: '40px', right: 'auto', bottom: 'auto', left: '0', transformOrigin: 'top center' }" 
                        @changed="document.dateDocument = $event.date"
                    />
                </div>

            </div>

        </div>
        
    </simple-modal-popup>  

</template>

<script setup>

import MiniPanel from '../miscellaneous/MiniPanel.vue'
import { ref, reactive, computed, onMounted } from 'vue'
import { useStore } from 'vuex'
import Swal from 'sweetalert2'

import { 
    PERSONNEL_UPLOAD_DOCUMENT, 
    DISPLAY_LOADER, 
    HIDE_LOADER, 
    LOADER_MODULE,
    PERSONNEL_LIST_MODULE,
    PERSONNEL_LOAD_USER_DOCUMENTS,
    PERSONNEL_SET_USER_DOCUMENTS,
    PERSONNEL_UNSET_ORDER_DOCUMENT,
    PERSONNEL_REMOVE_DOCUMENT,
} 
from '../../store/types/types'

import { formatDate } from '../../components/helpers/helpers'
import { useRouter } from 'vue-router'
import useReports from '../../composables/reports/useReports'

const props = defineProps({
    id: {
        type: Number,
        required: true
    }
})

const emit = defineEmits(['showloader'])
            
const { generatePdfById, deleteReport } = useReports()
const store = useStore()
const router = useRouter()
            
const documents = computed(() => store.getters[`${PERSONNEL_LIST_MODULE}userDocuments`])
const loading = computed(() => store.getters[`${PERSONNEL_LIST_MODULE}loading`])
const disabledToDate = computed(() => new Date(new Date().getTime() - 24*60*60*1000).toJSON().slice(0,10))
const fileUploadEl = ref(null)

const document = reactive({
    name: null,
    dateExpired: null,
    dateDocument: null,
})

const fetched = reactive({
    certification: false
})

const modal = reactive({
    show: false,
    title: 'Ajout permis / Certification'
})

const editReport = (document) => {
    router.push({ name: 'edit-report-page', params: { id: document.id, orderId: document.order_id } })
}

const addDocument = async () => {

    const result = await Swal.fire({
        title: 'Veuillez confirmer!',
        text: `Confirm adding a document?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#42A71E',
        cancelButtonColor: 'var(--lcdtOrange)',
        cancelButtonText: 'Annuler',
        confirmButtonText: `Oui, s'il vous plaît.`
    })

    if(result.isConfirmed) {

       modal.show = true
       modal.title = 'Ajout permis / Certification'

    }

}

const removeReport = async (document) => {

    const result = await Swal.fire({
        title: 'Veuillez confirmer!',
        text: `Are you sure you want to delete?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#42A71E',
        cancelButtonColor: 'var(--lcdtOrange)',
        cancelButtonText: 'Annuler',
        confirmButtonText: `Oui, s'il vous plaît.`
    })

    if (result.isConfirmed) {

        store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [
            true,
            "Suppression rapport en cours..",
        ])
        await deleteReport(document.id)
        store.commit(`${PERSONNEL_LIST_MODULE}${PERSONNEL_UNSET_ORDER_DOCUMENT}`, document.id)
        store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`)

    }

    
}

const removeDoc = async (document) => {

    const result = await Swal.fire({
        title: 'Veuillez confirmer!',
        text: `Are you sure you want to delete?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#42A71E',
        cancelButtonColor: 'var(--lcdtOrange)',
        cancelButtonText: 'Annuler',
        confirmButtonText: `Oui, s'il vous plaît.`
    })

    if(result.isConfirmed) {

        store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [
            true,
            "Suppression document en cours..",
        ])
    
        store.dispatch(`${PERSONNEL_LIST_MODULE}${PERSONNEL_REMOVE_DOCUMENT}`,document.id)
        .then(response => {
            store.commit(`${PERSONNEL_LIST_MODULE}${PERSONNEL_UNSET_ORDER_DOCUMENT}`, document.id)
            store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`)
        })

    }
    
}
const downloadDoc = async (document_id) => {

    store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [
        true,
        "Téléchargement document en cours..",
    ])

    store.dispatch(`${PERSONNEL_LIST_MODULE}${PERSONNEL_GET_DOCUMENT_URL}`, document_id)
    .then(_ => {
        store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`)
    })

}

const addfile = () => {
    fileUploadEl.value.click()
}

const fileElUpdated = () => {

    var input = fileUploadEl.value

    var data = new FormData()
    
    for (const file of input.files) {
        data.append('files', file, file.name)
    }

    data.append('name', document.name)
    data.append('dateExpired', document.dateExpired)
    data.append('dateDocument', document.dateDocument)

    store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [
        true,
        "Chargement document en cours..",
    ])

    store.dispatch(`${PERSONNEL_LIST_MODULE}${PERSONNEL_UPLOAD_DOCUMENT}`, data)
    .then(resp => {
        store.dispatch(`${PERSONNEL_LIST_MODULE}${PERSONNEL_LOAD_USER_DOCUMENTS}`, { id: props.id, take: 3 })
        .then(resp2 => {

            store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`)

            modal.show = false
            document.name = null
            document.dateExpired = null
            document.dateDocument = null

        })
    })
    
}

const createReport = () => {
    document.getElementsByTagName('body')[0].className=''
    router.push({ name: 'create-report-page', params: { id: props.id } })
}

const appendResults = async (type) => {

    if(loading.value.status) return
    
    try {
        emit('showloader', true)
        await store.dispatch(`${PERSONNEL_LIST_MODULE}${PERSONNEL_LOAD_USER_DOCUMENTS}`, { id: props.id, take: null })
        fetched[type] = true
    }

    catch(e) {
        throw e
    }

    finally {
        emit('showloader', false)
    }


}

onMounted(() => {
    store.commit(`${PERSONNEL_LIST_MODULE}${PERSONNEL_SET_USER_DOCUMENTS}`, [])
    store.dispatch(`${PERSONNEL_LIST_MODULE}${PERSONNEL_LOAD_USER_DOCUMENTS}`, { id: props.id, take: 3 })
})            
           
</script>

<style lang="scss" scoped>

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

.documentline {
    padding-left: 48px;
    position: relative;
    word-break: break-word;
}
.documentline::before{
    content: "";
    width: 13px;
    height: 13px;
    background-color: var(--lcdtOrange) ;
    border-radius: 50%;
    display: block;
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    transition: background-color ease-in-out 0.3s;
}
.documentline.report::before{
    background-color: #C4C4C4;
}
.document_action{
    color:var(--lcdtOrange);
    cursor: pointer;
}

.list-enter-from{
        opacity: 0;
        transform: scale(0.6);
    }
    .list-enter-to{
        opacity: 1;
        transform: scale(1);
    }
    .list-enter-active{
        transition: all 1s ease;
    }

    .list-leave-from{
        transform-origin: right center;
        opacity: 1;
        transform: scale(1);
   
    }
    .list-leave-to{
        transform-origin: right center;
        opacity: 0;
        transform: scale(0.6);
    }
    .list-leave-active{
               transition: all 1s ease;
         transform-origin: right center;
        position:absolute;     
        width: 100%;
    }
    .list-move{
        transition:all 0.3s ease;
    }
</style>