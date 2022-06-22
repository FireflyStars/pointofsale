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

                    <div class="col main-view container px-5">
                        
                        <page-title 
                            icon="entite" 
                            name="ENTITE" 
                            class="almarai_extrabold_normal_normal"
                        />

                            
                        <tab-pane :tabs="tabs" current='tout' class="almarai_700_normal">

                            <template v-slot:tout>
                                
                                <item-list-table :table_def="entiteList">

                                    <template v-slot:statut_name="{ row }">

                                        <span 
                                            class="text-uppercase" 
                                            :style="{ 
                                                'color': row.statut_color, 
                                            }"
                                            >
                                            {{ row.statut_name }}
                                        </span>

                                    </template>    

                                    <template v-slot:litige="{ row }">
                                        
                                        <span v-if="row.litige"><Icon name="check" /></span>
                                        <span v-else><Icon name="times" /></span>

                                    </template>

                                </item-list-table>
                                    
                            </template>

                            <template v-slot:mes_entities>
                                
                                <item-list-table :table_def="entiteUserList" />

                            </template>
                            
                        </tab-pane>

                        <component :is="Component" />

                    </div>

                </transition>    

            </div>

        </div>

    </router-view>


</template>

<script setup>

    import { useStore } from 'vuex'
    import { ref, computed, onMounted } from 'vue'
    import ItemListTable from '../../components/miscellaneous/ItemListTable/ItemListTable.vue';

    
    import {
        ENTITE_LIST_MODULE
    }
    from '../../store/types/types'


    const store = useStore()

    const tabs = ref({
        tout: 'Tout',
        mes_entities: 'Mes Entités',
    })

    const entiteList = computed(() => store.getters[`${ENTITE_LIST_MODULE}entiteList`])
    const entiteUserList = computed(() => store.getters[`${ENTITE_LIST_MODULE}entiteUserList`])

</script>

<style lang="scss" scoped>


</style>
