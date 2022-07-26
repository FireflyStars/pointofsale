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
                            icon="ouvrage" 
                            name="OUVRAGE" 
                            class="almarai_extrabold_normal_normal"
                            style="height: 45px; width: 45px;"
                        />

                        <div class="row m-0 ml-5 mr-5">
                            
                            <div class="col">

                                <tab-pane :tabs="tabs" current='tout' class="almarai_700_normal">

                                    <template v-slot:tout>
                                        
                                        <item-list-table :table_def="ouvrageList" />
                                            
                                    </template>

                                    <template v-slot:installation>
                                        
                                        <item-list-table :table_def="ouvrageListInstallation" />

                                    </template>

                                    <template v-slot:securite>
                                        
                                        <item-list-table :table_def="ouvrageListSecurite" />

                                    </template>

                                    <template v-slot:prestation>
                                        
                                        <item-list-table :table_def="ouvrageListPrestation" />

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
    import UnitTag from '../../components/Ouvrages/UnitTag'

    import { 
        OUVRAGE_MODULE 
    }
    from '../../store/types/types'


    const store = useStore()

    const tabs = ref({
        tout: 'Tout',
        installation: 'Installation',
        securite: 'Sécurité',
        prestation: 'Prestation'
    })

    const ouvrageList = computed(() => store.getters[`${OUVRAGE_MODULE}ouvrageList`])
    const ouvrageListInstallation = computed(() => store.getters[`${OUVRAGE_MODULE}ouvrageListInstallation`])
    const ouvrageListSecurite = computed(() => store.getters[`${OUVRAGE_MODULE}ouvrageListSecurite`])
    const ouvrageListPrestation = computed(() => store.getters[`${OUVRAGE_MODULE}ouvrageListPrestation`])


</script>

<style lang="scss" scoped>


</style>
