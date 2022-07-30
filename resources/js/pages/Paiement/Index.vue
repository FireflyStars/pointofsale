<template>
    
    <router-view v-slot="{ Component }">
        
        <div class="container-fluid h-100 bg-color">

            <main-header />

            <div 
                class="row d-flex align-content-stretch align-items-stretch flex-row hmax main-view-wrap" style="z-index:100"
            >

                <side-bar />

                <transition
                    enter-active-class="animate__animated animate_fadeIn"
                    leave-active-class="animate__animated animate_fadeOut"
                >

                    <div class="col main-view container">
                        
                        <page-title 
                            icon="paiement" 
                            name="Paiement" 
                            class="almarai_extrabold_normal_normal"
                            style="height: 45px; width: 45px;"
                        />

                        <div class="row m-0 ml-5 mr-5">
                            
                            <div class="col">

                                <tab-pane :tabs="tabs" current='tout' class="almarai_700_normal">

                                    <template v-slot:tout>
                                        
                                        <item-list-table :table_def="paiements">

                                            <template v-slot:paiement_type_id="{ row }">
                                                
                                                <status-tag :id="row.paiement_type_id" />
                                            
                                            </template>

                                        </item-list-table>
                                            
                                    </template>

                                    <template v-slot:mesPaiements>
                                        
                                        <item-list-table :table_def="paiementsMes">

                                            <template v-slot:paiement_type_id="{ row }">
                                                
                                                <status-tag :id="row.paiement_type_id" />
                                            
                                            </template>

                                        </item-list-table>
                                            
                                    </template>

                                    <template v-slot:validerPatComptable>
                                        
                                        <item-list-table :table_def="paiementsValider">

                                            <template v-slot:paiement_type_id="{ row }">
                                                
                                                <status-tag :id="row.paiement_type_id" />
                                            
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
    import statusTag from '../../components/Paiements/StatusTag'

    import {
        PAIEMENT_LIST_MODULE
    }
    from '../../store/types/types'

    const store = useStore()

    const tabs = ref({
        tout: 'Tout',
        mesPaiements: 'Mes Paiements',
        validerPatComptable: 'Valider par Comptable',
    })


    const paiements = computed(() => store.getters[`${PAIEMENT_LIST_MODULE}paiements`])
    const paiementsMes = computed(() => store.getters[`${PAIEMENT_LIST_MODULE}paiementsMes`])
    const paiementsValider = computed(() => store.getters[`${PAIEMENT_LIST_MODULE}paiementsValider`])


</script>

<style lang="scss" scoped>
.page-title {
    margin-left: 0 !important;
}
</style>
