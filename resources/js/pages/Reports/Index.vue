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

                    <div class="col main-view container px-5">

                        <page-title 
                            icon="report" 
                            name="Reports" 
                            class="almarai_extrabold_normal_normal"
                            width="32" 
                            height="32"
                        />


                        <item-list-table 
                            :table_def="reports" 
                        >

                            <template v-slot:pages="{ row }">
                                {{ row.pages === null || row.pages === '' ? '' : JSON.parse(row.pages).length }}
                            </template>

                            <template v-slot:id="{ row }">

                                <a href="#" class="link" @click.stop="navigatePage(row.id)">
                                    {{ row.pages != null && row.pages != '' ? 'Edit Report': 'Create Report' }}
                                </a>

                            </template> 

                        </item-list-table>

                    </div>

                </div>
            </div>
        </transition>
    </router-view>


</template>

<script setup>

import { useStore } from 'vuex'
import { useRouter } from 'vue-router'
import { onMounted, ref, nextTick, computed } from 'vue'

import { 
    REPORTS_BUILDER_MODULE, 
} from '../../store/types/types'

import ItemListTable from '../../components/miscellaneous/ItemListTable/ItemListTable.vue'

const store = useStore()
const router = useRouter()

const showcontainer = ref(false)

const reports = computed(() => store.getters[`${REPORTS_BUILDER_MODULE}reportListDefinition`])

const navigatePage = (id) => {
    
    document.getElementsByTagName( 'body' )[0].className=''

    router.push({
        name: 'report-page',
        params: {
            id
        }
    })
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

