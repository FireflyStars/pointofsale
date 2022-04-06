<template>

    <div class="d-flex justify-content-between align-items-center margin-bottom">

        <div>
            <h4 class="tile_h1">
                <Icon name="report" width="32" height="32" />
                {{ title }}
            </h4>
        </div>

        <div class="d-flex">
            <BaseButton 
                title="Sauvegarder" 
                kind="success" 
                class="me-12 heading-buttons justify-content-center"
                :class="{ 'not-allowed': fetching || saving }"
                :disabled="fetching || saving"
                :textClass="saving ? 'd-none' : ''"
                @click="save" 
            >
                <Icon name="spinner" v-show="saving" />
            </BaseButton>
            <BaseButton 
                title="pdf" 
                kind="danger" 
                class="text-uppercase heading-buttons heading-buttons-pdf justify-content-center"
                :class="{ 'not-allowed': fetching }" 
                :disabled="fetching || loading"
                :textClass="loading ? 'd-none' : ''"
                @click="submitPage"
            >
                <Icon name="spinner" v-show="loading" />
            </BaseButton>
        </div>

    </div>

    <div class="d-flex justify-content-between align-items-center">

        <div class="reports-dropdown">
            <!-- :class="{ 'not-allowed': fetching }"  -->
                <!-- :disabled="fetching || loading" -->
            <BaseButton 
                title="Change Fond"
                @click="toggleModal('templates-modal', true)"
            />
        </div>

        <div class="d-flex align-items-center">
            
            <div>

                <a 
                    class="orange text d-flex align-items-center gap-half" 
                    :class="[fetching ? 'not-allowed' : 'pointer']"
                    @click.prevent="addPage"
                    :disabled="fetching"
                >
                    <Icon name="plus-circle" />
                    Ajouter Page
                </a>

                <a 
                class="text d-flex align-items-center gap-half"
                :class="[pages.length <= 1 || fetching ? 'not-allowed': 'pointer']" 
                @click.prevent="deletePage"
                :disabled="pages.length <= 1 || fetching"
                >
                    <Icon name="bin" />
                    Supprimer Page
                </a>

            </div>
            
            <div class="reports-dropdown page-dropdown">

                <select-box
                    v-model="activePage" 
                    :placeholder="pageName" 
                    :options="formattedPages" 
                    name="page"
                    classnames="reports-dropdown-button"
                    :disabled="fetching"
                    :selectStyles="{ maxHeight: '10rem', overflow: 'auto' }"
                />

            </div>

        </div>

    </div>

    <Modal 
        id="templates-modal"
        classes="p-5" 
        size="md"
    >
        <div>
            <h4 class="d-flex align-items-center gap-3">
                <Icon name="report" width="32" height="32" />
                Change Fond
            </h4>
            <div class="mt-3"> 

                <div class="d-flex align-items-center justify-content-around">
                    <BaseButton 
                        title="Votre Image"
                    />

                    <div class="image-boxes">
                        <div>
                            <div class="image-box"></div>
                            <label>Standard</label>
                        </div>
                        <div>
                            <div class="image-box"></div>
                            <label>XXXXXXX</label>
                        </div>
                        <div>
                            <div class="image-box"></div>
                            <label>Vide</label>
                        </div>
                        <div>
                            <div class="image-box"></div>
                            <label>YYYYYY</label>
                        </div>
                    </div>

                </div>

                <div class="d-flex align-items-center justify-content-around mt-5">
                    <BaseButton 
                        title="Valider"
                        kind="green"
                    />
                    <BaseButton 
                        title="Non"
                    />
                </div>

            </div>
        </div>
    </Modal>

</template>

<script>

import { useStore } from 'vuex'
import useModal from '../../composables/useModal'
import { computed, watch, inject } from 'vue'

import { 
    BUILDER_MODULE,
    ASSIGN_TEMPLATE,
    ADD_PAGE,
    DELETE_PAGE,
    SET_ACTIVE_TEMPLATE,
    SET_ACTIVE_PAGE 
} from '../../store/types/types'

export default {

    name: 'header-section',
    
    props: {
        title: {
            required: false,
            type: String,
            default: 'Creation/Edition Report'
        }
    },

    emits: ['submitPage', 'save'],

    setup (_, { emit }) {
        
        const store = useStore()
        const { toggleModal } = useModal()

        const fetching = inject('fetching')
        
        const loading = computed(() => {
            const { id, value } = store.getters[`${BUILDER_MODULE}/loading`]
            return id == 'submit' && value
        })

        const saving = computed(() => {
            const { id, value } = store.getters[`${BUILDER_MODULE}/loading`]
            return ['save-template', 'save-report'].includes(id) && value
        })

        const activeTemplate = computed({
            set(value) {
                store.commit(`${BUILDER_MODULE}/${SET_ACTIVE_TEMPLATE}`, value)
            },
            get() {
                return store.getters[`${BUILDER_MODULE}/activeTemplate`]
            }
        })

        const activePage = computed({
            set(value) {
                store.commit(`${BUILDER_MODULE}/${SET_ACTIVE_PAGE}`, value)
            },
            get() {
                return store.getters[`${BUILDER_MODULE}/activePage`]
            }
        })

        const page = computed(() => store.getters[`${BUILDER_MODULE}/page`])
        const pages = computed(() => store.getters[`${BUILDER_MODULE}/pages`])

        const pageName = computed(() =>  {
            const pageValue = +activePage.value + 1
            return 'Page ' + pageValue
        })

        const formattedPages = computed(() => {
            return pages.value.map((_, index) => {
                const pageValue = +index + 1
                return {
                    value: index, 
                    display: 'Page ' + pageValue
                }
            })
        })

        const templates = computed(() => store.getters[`${BUILDER_MODULE}/templates`])
        const template = computed(() => store.getters[`${BUILDER_MODULE}/template`])

        const formattedTemplates = computed(() => {
            return templates.value.map(template => {
                return {
                    value: template.id, 
                    display: template.name
                }
            })
        })
        
    
        const assignTemplateToActivePage = (id) => {
            if(!fetching.value) store.commit(`${BUILDER_MODULE}/${ASSIGN_TEMPLATE}`, id)
            return Promise.resolve()
        }

        const addPage = () => {
            if(!fetching.value) store.commit(`${BUILDER_MODULE}/${ADD_PAGE}`)
        }

        const deletePage = () => {
            if(!fetching.value) store.commit(`${BUILDER_MODULE}/${DELETE_PAGE}`)
        }

        const submitPage = () => {
            if(!fetching.value) emit('submitPage')
        }

        const save = () => {
            if(!fetching.value) emit('save')
        }

        watch(activeTemplate, (value) => {
            if(value) assignTemplateToActivePage(value)
        })
        
        return {
            save,
            page,
            pages,
            saving,
            addPage,
            loading,
            fetching,
            pageName,
            template,
            templates,
            deletePage,
            submitPage,
            activePage,
            toggleModal,
            activeTemplate,
            formattedPages,
            formattedTemplates,
            assignTemplateToActivePage
        }
    }
}
</script>

<style lang="scss" scoped>

.reports-dropdown {
    position: relative;
    margin-bottom: .75rem;
    &-button {
        background: #C4C4C4;
        border: none;
        font-family: Roboto;
        font-style: normal;
        font-weight: normal;
        font-size: 18px;
        line-height: 21px;
        color: #000000;
        gap: 0.51rem;
        padding-left: 1.43rem;
        padding-right: 1.43rem;
        min-width: 9.56rem;
        min-height: 2.93rem;
        border-radius: 0;
    }
    .page-button {
        gap: 2.62rem;
    }
}

.page-dropdown {
    margin-left: 2.06rem;
}

.heading-buttons {
    font-family: sans-serif;
    font-style: normal;
    font-weight: bold;
    font-size: 12px;
    line-height: 23px;
    color: #fff !important;
    width: 7.05rem;
    border: none;
    height: 2.31rem;
}

.image-boxes {

    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: .5rem;
    text-align: center;
    font-family: 'Poppins';
    font-style: normal;
    font-weight: 600;
    font-size: 16px;
    line-height: 22px;
    color: #000000;

    .image-box {
        width: 5.93rem;
        height: 5.625rem;
        background: #525252;
        border-radius: 8px;
    }
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