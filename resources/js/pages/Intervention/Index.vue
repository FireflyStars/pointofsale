<template>
    
    <router-view v-slot="{ Component }">
        
        <div class="container-fluid h-100 bg-color">

            <main-header />

            <div 
                class="row d-flex align-content-stretch align-items-stretch flex-row hmax main-view-wrap" 
                style="z-index:100"
            >

                <side-bar />

                <transition
                    enter-active-class="animate__animated animate_fadeIn"
                    leave-active-class="animate__animated animate_fadeOut"
                >

                    <div class="col main-view container">
                        
                        <page-title 
                            icon="intervention" 
                            name="Intervention" 
                            class="almarai_extrabold_normal_normal"
                            style="height: 45px; width: 45px;"
                        />

                        <div class="row m-0 ml-5 mr-5">
                            
                            <div class="col">

                                <tab-pane :tabs="tabs" current='tout' class="almarai_700_normal">

                                    <template v-slot:tout>
                                        
                                        <item-list-table :table_def="interventions">
                                        
                                            <template v-slot:intervention_statut_id="{ row }">
                                                <status-tag :id="row.intervention_statut_id" />
                                            </template>    

                                        </item-list-table>
                                            
                                    </template>

                                    <template v-slot:mes_interventions>
                                        
                                        <item-list-table :table_def="interventionsMes">

                                            <template v-slot:intervention_statut_id="{ row }">
                                                <status-tag :id="row.intervention_statut_id" />
                                            </template>           

                                        </item-list-table>
                                            
                                    </template>
                                    
                                </tab-pane>

                            </div>
                            
                        </div>

                        <component :is="Component" />

                    </div>

                </transition>    

            </div>

        </div>

    </router-view>


</template>

<script setup>

    import { useStore } from 'vuex'
    import { ref, computed } from 'vue'
    import ItemListTable from '../../components/miscellaneous/ItemListTable/ItemListTable.vue'
    import StatusTag from '../../components/Intervention/StatusTag'

    import {
        INTERVENTION_LIST_MODULE,
    }
    from '../../store/types/types'

    const tabs = ref({
        tout: 'Tout',
        mes_interventions: 'Mes interventions'
    })

    const store = useStore()

    const interventions = computed(() => store.getters[`${INTERVENTION_LIST_MODULE}interventions`])
    const interventionsMes = computed(() => store.getters[`${INTERVENTION_LIST_MODULE}interventionsMes`])


</script>

<style lang="scss" scoped>
.page-title {
    margin-left: 0 !important;
}

</style>
