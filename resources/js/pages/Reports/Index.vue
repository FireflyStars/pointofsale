<template>

    <router-view>
        <transition
            enter-active-class="animate__animated animate__fadeIn"
        >
            <div class="container-fluid h-100 bg-color" v-if="showcontainer">
                <main-header />

                <div 
                class="row d-flex align-content-stretch align-items-stretch flex-row hmax main-view-wrap reports-page" 
                style="z-index:100" >
                    
                    <side-bar />

                    <div class="col main-view container">

                        <page-title 
                            icon="rapport" 
                            name="Rapports" 
                            class="almarai_extrabold_normal_normal"
                            width="45" 
                            height="45"
                        />

                        <div class="row m-0 ml-5 mr-5">
                            
                            <div class="col-12 p-0 position-relative">

                                <item-list-table 
                                    :table_def="reports" 
                                >

                                    <template v-slot:id="{ row }">

                                        <div class="d-flex align-items-center gap-3">

                                            <a href="#" class="link" @click.stop="navigatePage({ id: row.id, orderId: row.order_id })">
                                                <Icon name="edit" width="20" height="20" />
                                            </a>

                                            <a href="#" class="link" @click.stop="deleteReport(row.id)">
                                                <Icon name="delete" width="20" height="20" />
                                            </a>
                                        </div>


                                    </template> 

                                </item-list-table>
                            
                            </div>

                        </div>


                    </div>

                </div>
            </div>
        </transition>
    </router-view>


</template>

<script setup>

import Swal from 'sweetalert2'
import { useStore } from 'vuex'
import { useRouter } from 'vue-router'
import { onMounted, ref, nextTick, computed } from 'vue'

import { 
    REPORTS_BUILDER_MODULE, 
    LOADER_MODULE,
    DISPLAY_LOADER,
    BUILDER_DELETE_REPORT,
    ITEM_LIST_MODULE,
    ITEM_LIST_REMOVE_ROW,
    TOASTER_MODULE,
    TOASTER_MESSAGE,
    HIDE_LOADER
} from '../../store/types/types'

import ItemListTable from '../../components/miscellaneous/ItemListTable/ItemListTable.vue'

const store = useStore()
const router = useRouter()

const showcontainer = ref(false)

const reports = computed(() => store.getters[`${REPORTS_BUILDER_MODULE}reportListDefinition`])

const navigatePage = ({ id, orderId }) => {
    
    router.push({
        name: 'edit-report-page',
        params: {
            id,
            orderId
        }
    })
}

const deleteReport = async (id) => {

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
                "Delete Rapport en cours..",
            ])
            await store.dispatch(`${REPORTS_BUILDER_MODULE}${BUILDER_DELETE_REPORT}`, id)
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

<style scoped>
.main-view {
    margin-top: 6rem;
}

.table-container {
    margin-left: 7.125rem;
}

</style>

