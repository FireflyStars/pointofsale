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
            <div class="col-3 almarai-light lcdtgrey d-flex font-12 align-items-center justify-content-center">Date</div>
            <div class="col-2 almarai-light lcdtgrey d-flex font-12 align-items-center justify-content-center">Type</div>
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
                    {{ document.created_at ? moment(document.created_at).format('LL') : '' }}
                </div>

                <div 
                    class="col-2 almarai-light d-flex font-14 align-items-center justify-content-center">
                    {{ document.type?.name }}
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
                    <span @click="downloadDoc(document.id)"> 
                        <icon name="download" width="16px" height="16px" class="cursor-pointer" />
                    </span>
                </div>

                <div 
                    v-if="document.type?.id != 1 && document.affiliate_id != 0" 
                    class="col-1 d-flex align-items-center justify-content-center"
                >
                    <span @click="removeDoc(document)">
                        <icon  name="trash-x" width="20px" height="20px" class="cursor-pointer" />
                    </span>
                </div>

            </div>

        </transition-group>

        <div class="d-flex justify-content-evenly mt-4">

            <span class="font-14 mulish_600_normal document_action noselect" @click.prevent="addDocument">
                <icon name="plus-circle" width="16px" height="16px" /> 
                AJOUTER DOCUMENT
            </span>

        </div>  
        
        <input type="file" id="fileuploadEl" class="d-none" ref="fileUploadEl" accept="application/pdf" />

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
                    <div class="form-group">
                        <label for="nom" class="text-gray font-16 almarai-bold">Nom</label>
                        <input type="text" id="nom" class="form-control" v-model="document.name">
                    </div>
                </div>
                
                <div class="col d-flex align-items-center gap-2">

                    <div class="form-group">
                        <label for="dateExpiry" class="text-gray font-16 almarai-bold">Type</label>
                        <select-box
                            v-model="document.type" 
                            :options="typeOptions" 
                            name="type" 
                        />
                    </div>

                </div>

            </div>

            <div class="row mb-3 mt-4">

                <div class="col">
                    <div class="form-group">
                        <a href="#" class="link-upload text-gray font-16 almarai-bold" @click.prevent="addfile">Téléchargement le document en pdf</a>
                    </div>
                </div>


            </div>

        </div>
        
    </simple-modal-popup>  

</template>

<script setup>

import moment from 'moment'
import MiniPanel from '../miscellaneous/MiniPanel.vue'
import { ref, reactive, computed, onMounted } from 'vue'
import { useStore } from 'vuex'
import Swal from 'sweetalert2'

import { 
    TOASTER_MODULE,
    TOASTER_MESSAGE,
    DISPLAY_LOADER, 
    HIDE_LOADER, 
    LOADER_MODULE,
    ARTICLES_MODULE,
    ARTICLES_UNSET_PRODUCT_DOCUMENT,
    ARTICLES_SET_PRODUCT_DOCUMENTS,
    ARTICLES_LOAD_PRODUCT_DOCUMENTS,
    ARTICLES_REMOVE_DOCUMENT,
    ARTICLES_GET_DOCUMENT_URL,
    ARTICLES_UPLOAD_DOCUMENT,
    GET_PRODUCT_DOCUMENT_TYPES,
} 
from '../../store/types/types'

import { useRouter } from 'vue-router'
import useReports from '../../composables/reports/useReports'
import SelectBox from '../miscellaneous/SelectBox.vue'

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
            
const documents = computed(() => store.getters[`${ARTICLES_MODULE}documents`])
const loading = computed(() => store.getters[`${ARTICLES_MODULE}loading`])
const disabledToDate = computed(() => new Date(new Date().getTime() - 24*60*60*1000).toJSON().slice(0,10))
const fileUploadEl = ref(null)

const typeOptions = computed(() => {
    return store.getters[`${ARTICLES_MODULE}documentTypes`]?.map(type => {
        return {
            value: type.id,
            display: type.name
        }
    })
})

const document = reactive({
    name: null,
    type: 1
})

const fetched = reactive({
    certification: false
})

const modal = reactive({
    show: false,
    title: 'Ajout Document'
})


const addDocument = async () => {

    modal.show = true
    modal.title = 'Ajout Document'

}

const removeDoc = async (document) => {

    const result = await Swal.fire({
        title: 'Veuillez confirmer!',
        text: `Voulez vous archiver ce permis?`,
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
            "Suppression permis en cours..",
        ])
    
        store.dispatch(`${ARTICLES_MODULE}${ARTICLES_REMOVE_DOCUMENT}`,document.id)
        .then(_ => {
            store.commit(`${ARTICLES_MODULE}${ARTICLES_UNSET_PRODUCT_DOCUMENT}`, document.id)
            store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`)
        })

    }
    
}

const downloadDoc = async (document_id) => {

    store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [
        true,
        "Téléchargement document en cours..",
    ])

    store.dispatch(`${ARTICLES_MODULE}${ARTICLES_GET_DOCUMENT_URL}`, document_id)
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

    if(input.files[0]?.name == null || typeof input.files[0]?.name == 'undefined' || input.files[0]?.name == '') {
        
        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
            type: 'danger',
            message: 'Please upload document',
            ttl: 5,
        })
        return
    }

    if(input.files[0]?.type != 'application/pdf') {
        
        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
            type: 'danger',
            message: 'Only pdf files are allowed',
            ttl: 5,
        })
        return
    }

    if(document.name == null || typeof document.name == 'undefined' || document.name == '') {
        
        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
            type: 'danger',
            message: 'Nom is empty',
            ttl: 5,
        })
        return
    }

    if(document.type == 0) {
        
        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
            type: 'danger',
            message: 'Please select type',
            ttl: 5,
        })
        return
    }



    data.append('name', document.name)
    data.append('type', document.type)
    data.append('productId', props.id)


    store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [
        true,
        "Chargement document en cours..",
    ])

    store.dispatch(`${ARTICLES_MODULE}${ARTICLES_UPLOAD_DOCUMENT}`, data)
    .then(_ => {

        store.dispatch(`${ARTICLES_MODULE}${ARTICLES_LOAD_PRODUCT_DOCUMENTS}`, { id: props.id, take: 3 })
        .then(_ => {

            store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`)

            modal.show = false
            document.name = null
            document.type = 1

        })
        .catch(_ => {
            store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`)
        })
    })
    .catch(_ => {
        store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`)
    })
    
}

const appendResults = async (type) => {

    if(loading.value.status) return
    
    try {
        emit('showloader', true)
        await store.dispatch(`${ARTICLES_MODULE}${ARTICLES_LOAD_PRODUCT_DOCUMENTS}`, { id: props.id, take: null })
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
    store.commit(`${ARTICLES_MODULE}${ARTICLES_SET_PRODUCT_DOCUMENTS}`, [])
    store.dispatch(`${ARTICLES_MODULE}${ARTICLES_LOAD_PRODUCT_DOCUMENTS}`, { id: props.id, take: 3 })
    store.dispatch(`${ARTICLES_MODULE}${GET_PRODUCT_DOCUMENT_TYPES}`)
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

.link-upload {
    top: .7rem;
    right: 1rem;
    font-style: normal;
    font-size: 14px;
    line-height: 140%;
    display: flex;
    align-items: flex-end;
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