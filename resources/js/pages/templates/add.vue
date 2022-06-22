<template>

    <router-view>
        <transition
            enter-active-class="animate__animated animate__fadeIn"
        >
            <div class="container-fluid h-100 bg-color" v-if="showcontainer">
                <main-header />

                <div 
                    class="row d-flex align-content-stretch align-items-stretch flex-row hmax main-view-wrap reports-page" 
                    style="z-index:100" 
                >
                    
                    <side-bar />

                    <div class="col main-view">
                        <div>

                            <div class="d-flex gap-3 main-container">

                                <div class="left-page-container">

                                    <header-section 
                                        title="Creation/Edition Templates" 
                                        @submitPage="generatePagePdf"
                                        @save="saveTemplate" 
                                    />

                                    <page-builder-container 
                                        :showcontainer="showcontainer"
                                    />            


                                </div>


                                <div 
                                    class="right-page-container" 
                                    @mouseenter="toggleContainer(true)"
                                    @mouseleave="toggleContainer"
                                    :class="showRightContainer ? 'right-page-container-visible': ''"
                                >

                                    <adjouter-zone />
                                    <div class="d-none">
                                        <input type="file" id="file" accept="image/*">
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </transition>
    </router-view>


</template>

<script>

import Swal from 'sweetalert2'
import { useStore } from 'vuex'
import { onMounted, ref, nextTick, computed, watch, provide } from 'vue'

import { 
    BUILDER_MODULE, 
    SAVE_REPORT_PAGES,
    SAVE_REPORT_TEMPLATE,
} from '../../store/types/types'

import adjouterZone from '../../components/reports/adjouter-zone'
import headerSection from '../../components/reports/header-section'
import reportOrderResources from '../../components/reports/report-order-resources'
import reportTable from '../../components/reports/report-table'
import pageBuilderContainer from '../../components/reports/page-builder-container'

import useElementsGenerator from '../../composables/reports/useElementsGenerator'
import useReports from '../../composables/reports/useReports'


export default {

    components: {
        reportTable,
        adjouterZone,
        headerSection,
        reportOrderResources,
        pageBuilderContainer
    },

    setup() {

        const store = useStore()
        const showRightContainer = ref(false)
        
        const { 
            promptImage,
            generateElement,
            generatePrefetchedImage,
        } = useElementsGenerator()

        const { resetPages, generatePagePdf, resetOrder } = useReports()

        const activeItem = ref(null)
        const showcontainer = ref(false)

        const page = computed(() => store.getters[`${BUILDER_MODULE}/page`])
        const pages = computed(() => store.getters[`${BUILDER_MODULE}/pages`])

        const fetching = computed(() => { 
            const { id, value } = store.getters[`${BUILDER_MODULE}/loading`]
            return id == 'fetching' && value
        })
    
        const loadPages = () => {
            store.commit(`${BUILDER_MODULE}/${SAVE_REPORT_PAGES}`)
            return Promise.resolve()
        }

        const toggleContainer = (value = true) => {
            // if(window?.screen?.width >= 1500) return
            if(value == true) showRightContainer.value = true
            else showRightContainer.value = false
        }

        const saveTemplate = async () => {
            activeItem.value = null
            const { value: name } = await Swal.fire({
                title: 'Sauvegarde',
                input: 'text',
                inputLabel: 'Voulez vous sauvegarder ce rapport comme template',
                inputPlaceholder: 'Nom du template',
                showCancelButton: true,
                confirmButtonText: 'Sauvegarde',
            })

            if (name) {
                store.dispatch(`${[BUILDER_MODULE]}/${[SAVE_REPORT_TEMPLATE]}`, {
                    pages,
                    name
                })
            }

        }

        provide('fetching', fetching)
        provide('promptImage', promptImage)
        provide('generateElement', generateElement)
        provide('generatePrefetchedImage', generatePrefetchedImage)
        provide('showRightContainer', showRightContainer)

        watch(page, () => activeItem.value = null)

        onMounted(() => {
            resetPages()
            resetOrder()
            loadPages()
            nextTick(async () => {
                showcontainer.value = true
                if(window?.screen && window?.screen?.width >= 1500) {
                    showRightContainer.value = true
                }
            })
        })
      
        return { 
            fetching,
            saveTemplate,
            showcontainer,
            toggleContainer,
            generatePagePdf,
            showRightContainer
        }
    },
}

</script>

<style lang="scss" scoped>

$orange: orange;

.swal2-container {
    z-index: 999999999999999999 !important;
}

.title_h1 {
    font-family: Almarai;
    font-style: normal;
    font-weight: 800;
    font-size: 22px;
    line-height: 110%;
    color: #000000;
}

.main-container {
    position: relative;
    margin-bottom: 1rem;
}

.left-page-container {
    z-index: 6;
}

.right-page-container {
    top: 0;
    right: 0;
    width: 300px;
    z-index: 0;
    position: absolute;
    transition: width .2s, z-index .2s;

    @media only screen and (min-width: 1500px) {
        width: 530px;
    }

    &-visible {
        width: 530px;
        z-index: 7;
    }
}

.main-view {
    margin-top: 7rem;
}

.text {
    font-family: Poppins;
    font-style: normal;
    font-weight: 600;
    font-size: 14px;
    line-height: 22px;
    display: flex;
    align-items: center;
    margin-bottom: 3px;
    text-decoration: none;
    color: #000000;
    &-base {
        font-family: Poppins;
        font-style: normal;
        font-weight: 600;
        line-height: 22px;
        display: flex;
        align-items: center;
        margin-bottom: 3px;
        font-size: 16px;
    }
}

</style>

