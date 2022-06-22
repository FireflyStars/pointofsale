<template>

    <router-view>
        <transition
            enter-active-class="animate__animated animate__fadeIn"
        >
            <div class="container-fluid bg-color" v-if="showcontainer">
                <main-header />

                <div 
                class="row d-flex align-content-stretch align-items-stretch flex-row main-view-wrap reports-page" 
                style="z-index:100" >
                    
                    <side-bar />

                    <div class="col main-view container">
                        <div>

                            <div class="d-flex gap-3 main-container">

                                <div class="left-page-container">

                                    <header-section 
                                        title="Creation/Edition Template" 
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

import { useRoute } from 'vue-router'
import { useStore } from 'vuex'
import { onMounted, ref, nextTick, computed, provide } from 'vue'

import { 
    BUILDER_MODULE, 
    UPDATE_REPORT_TEMPLATE,
    GET_REPORT_TEMPLATE
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

    props: {
        id: {
            required: true,
            type: [Number, String]
        }
    },

    setup(props) {

        const route = useRoute()
        const store = useStore()
        const showRightContainer = ref(false)

        const { 
            promptImage,
            generateElement,
            generatePrefetchedImage,
        } = useElementsGenerator()

        const { resetPages, generatePagePdf, resetOrder } = useReports()

        const showcontainer = ref(false)

        const pages = computed(() => store.getters[`${BUILDER_MODULE}/pages`])


        const fetching = computed(() => { 
            const { id, value } = store.getters[`${BUILDER_MODULE}/loading`]
            return id == 'fetching' && value
        })
    
        const saveTemplate = async () => {
            store.dispatch(`${[BUILDER_MODULE]}/${[UPDATE_REPORT_TEMPLATE]}`, {
                pages,
                id: props.id
            })
        }

        const getPageTemplate = () => {
            store.dispatch(`${[BUILDER_MODULE]}/${[GET_REPORT_TEMPLATE]}`, { id: props.id, route: route.name})
            return Promise.resolve()
        }

        const toggleContainer = (value = true) => {
            // if(window?.screen?.width >= 1500) return
            if(value == true) showRightContainer.value = true
            else showRightContainer.value = false
        }

        provide('fetching', fetching)
        provide('promptImage', promptImage)
        provide('generateElement', generateElement)
        provide('showRightContainer', showRightContainer)
        provide('generatePrefetchedImage', generatePrefetchedImage)

        onMounted(() => {
            resetPages()
            resetOrder()
            nextTick(async () => {

                document.getElementsByTagName( 'body' )[0].className=''
                console.log(document.getElementsByTagName('body')[0].className)

                await getPageTemplate()
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
            showRightContainer,
        }
    },
}

</script>

<style lang="scss" scoped>

$orange: orange;

body {
    overflow-y: visible !important;
}

.hide-overflowY {
    overflow-y: visible !important;
}

.swal2-container {
    z-index: 999999999999 !important;
}

.active-item {
    cursor: move;
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
    margin-bottom: 2rem;
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
    margin-bottom: 2rem;
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

