<template>

    <router-view>
        
        <div class="container-fluid h-100 bg-color" v-if="showcontainer">

            <main-header />

            <div 
            class="row d-flex align-content-stretch align-items-stretch flex-row hmax main-view-wrap" 
            style="z-index:100" >
                
                <side-bar />

                <transition
                    enter-active-class="animate__animated animate_fadeIn"
                    leave-active-class="animate__animated animate_fadeOut"
                >

                    <div class="col main-view container">

                        <div class="d-flex align-items-center gap-3">
                        
                            <page-title 
                                icon="report" 
                                name="Templates" 
                                class="almarai_extrabold_normal_normal"
                                width="44" 
                                height="46"
                            />

                        </div>

                        <div class="row m-0 ml-5 mr-5">

                            <div class="col">

                                <tab-pane :tabs="tabs" current='tout' class="almarai_700_normal">
                                
                                    <template v-slot:tout>

                                        <item-list-table 
                                            :table_def="templatesList" 
                                        >
                                            
                                            <template v-slot:pages="{ row }">
                                                {{ row.pages.length }}
                                            </template>

                                            <template v-slot:id="{ row }">

                                                <div class="d-flex align-items-center gap-3">
                                                
                                                    <a href="#" class="link" @click.stop="navigatePage(row.id)">
                                                        <Icon name="edit" width="20" height="20" />
                                                    </a>
                                                    <a href="#" class="link" @click.stop="deleteTemplate(row.id)">
                                                        <Icon name="delete" width="20" height="20" />
                                                    </a>
                                                    
                                                </div>

                                                
                                            </template>

                                        </item-list-table>

                                    </template>

                                </tab-pane>

                            
                            </div>

                        </div>


                    </div>

                </transition>

            </div>

        </div>

    </router-view>


</template>

<script setup>

import Swal from 'sweetalert2'
import { useStore } from 'vuex'
import { useRouter } from 'vue-router'
import { onMounted, ref, nextTick, computed } from 'vue'
import ItemListTable from '../../components/miscellaneous/ItemListTable/ItemListTable.vue'

import { 
    BUILDER_MODULE_LIST, 
    BUILDER_DELETE_TEMPLATE,
    ITEM_LIST_MODULE,
    ITEM_LIST_REMOVE_ROW,
    TOASTER_MODULE,
    TOASTER_MESSAGE,
    LOADER_MODULE,
    DISPLAY_LOADER,
    HIDE_LOADER,
} from '../../store/types/types'

const store = useStore()
const router = useRouter()

const showcontainer = ref(false)

const templatesList = computed(() => store.getters[`${BUILDER_MODULE_LIST}templateListDefinition`])

const tabs = ref({
    tout: 'Tout',
})


const navigatePage = (id) => {
    
    router.push({
        name: 'templates-edit',
        params: {
            id
        }
    })

}

const deleteTemplate = async (id) => {

    try {

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

        if(result.isConfirmed) {

            store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [
                true,
                "Delete template en cours..",
            ])

            await store.dispatch(`${BUILDER_MODULE_LIST}${BUILDER_DELETE_TEMPLATE}`, id)
            store.commit(`${ITEM_LIST_MODULE}${ITEM_LIST_REMOVE_ROW}`, { id: 'id', idValue: id })

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

onMounted(() => {
    nextTick(() => {
        showcontainer.value = true
    })
})
      

</script>

<style lang="scss" scoped>

$orange: orange;

.table-container {
    margin-left: 7.125rem;
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

.left-page-container {
    width: 55%;
}

.right-page-container {
    width: 45%;
}

.main-view {
    margin-top: 6rem;
}

.template {
    &-header {
        top: 0;
        max-height: 4.75rem;
    }
    &-footer {
        bottom: 0;
    }
    &-header, 
    &-footer {
        width: 100%;
        height: auto;
        position: absolute;
        left: 0;
        img {
            width: 100%;
            height: 100%;
            padding: 1rem;
            object-fit: cover;
        }
    }
    &-body {
        margin-top: 5.75rem;
        img {
            object-fit: cover;
            width: 25rem;
            height: 25rem;
            height: auto;
            border: 3px solid $orange;
        }
        span {
            word-break: break-all !important;
        }
        .page-number {
            float: right;
            font-size: 12px;
            font-family: inherit;
            &::after, &::before {
                float: none;
                clear: both;
            }
        }
    }
}

.builder-container {
    position: relative;
    min-height: 58rem;
    height: auto;
    background: #fff;
    overflow: hidden;
    padding: 1rem 2rem;
    margin-bottom: 1rem;

    .draggable {
        z-index: 10;
        position: absolute;
        .close {
            position: absolute;
            top: -100%;
            left: 50%;
            width: 1.2rem;
            height: 1.2rem;
            background: #000;
            color: white;
            transform: translate(-50%, -100%);
            transform-origin: center;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            line-height: 1.08rem;
            cursor: pointer;
            &:hover {
                opacity: .8;
            }
        }
    }

    .transparent-button {
        background: transparent;
        border-radius: 0;
        border: 2px solid #000;
        text-transform: uppercase;
        font-weight: 900;
        font-family: 'Almarai ExtraBold';
    }

    .title-bar {
        background: #797272;
        color: rgb(243, 243, 243);
        padding: 5px 1rem;
        margin: .8rem 0;
        text-transform: uppercase;
        font-size: 1.2rem;
        font-weight: 900;
        font-family: 'Almarai ExtraBold';
        width: 92%;
        display: flex;
        align-items: center;
    }

    .textarea {
        min-width: 350px;
        min-height: 50px;
        border: 1px solid #ccc;
        z-index: 99999;
        word-wrap: normal;
        &::before,
        &::after {
            float: none;
            clear: both;
        }
    }

    .builder-image {
        width: 200;
        height: 250px;
        object-fit: cover;
        margin: .5rem 0;
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

