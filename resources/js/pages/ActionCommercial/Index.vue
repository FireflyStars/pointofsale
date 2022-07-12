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
                            icon="user-star" 
                            name="ACTION COMMERCIAL" 
                            class="almarai_extrabold_normal_normal"
                            width="44"
                            height="46"
                            style="height: 46px; width: 44px;"
                        />

                        <div class="row m-0 ml-5 mr-5">
                            <div class="col-12">
                                <a class="btn btn-primary" href="/outlook/sync">Sync Outlook</a>
                            </div>
                            <div class="col-12">

                                    
                                <tab-pane :tabs="tabs" current='tout' class="almarai_700_normal px-5">

                                    <template v-slot:tout>
                                        
                                        <item-list-table :table_def="actionCommercialList">

                                            <template v-slot:event_status_id="{ row }">

                                                <status-tag :id="row.event_status_id" />

                                            </template>    

                                        </item-list-table>
                                            
                                    </template>

                                    <template v-slot:mes_actions_co>
                                        
                                        <item-list-table :table_def="actionCommercialListUser">
                                            
                                            <template v-slot:event_status_id="{ row }">

                                                <status-tag :id="row.event_status_id" />

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

    import lodash from 'lodash'
    import { useStore } from 'vuex'
    import { ref, computed } from 'vue'
    import ItemListTable from '../../components/miscellaneous/ItemListTable/ItemListTable.vue'
    import StatusTag from '../../components/ActionCo/StatusTag.vue'
    
    import {
        ACTION_COMMERCIAL_MODULE
    }
    from '../../store/types/types'


    const store = useStore()

    const tabs = ref({
        tout: 'Tout',
        mes_actions_co: 'Mes Actions Co',
    })

    const actionCommercialList = computed(() => store.getters[`${ACTION_COMMERCIAL_MODULE}actionCommercialList`])
    const actionCommercialListUser = computed(() => store.getters[`${ACTION_COMMERCIAL_MODULE}actionCommercialListUser`])

</script>

<style lang="scss" scoped>
.event-status {
    color: #000; 
    text-transform: lowercase !important; 
    border-radius: 70px; 
    padding: 5px 16px 5px 8px;
    font-family: 'Almarai Bold';
    font-style: normal;
    font-weight: 700;
    font-size: 12px;
    line-height: 13px;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 104px;
    height: 23px;
}

</style>
