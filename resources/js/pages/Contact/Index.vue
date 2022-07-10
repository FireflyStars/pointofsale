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
                            icon="action-commercial" 
                            name="CONTACT" 
                            class="almarai_extrabold_normal_normal"
                            style="height: 46px; width: 44px;"
                        />

                        <div class="row m-0 ml-5 mr-5">
                            
                            <div class="col">

                                <tab-pane :tabs="tabs" current='tout' class="almarai_700_normal">

                                    <template v-slot:tout>
                                        
                                        <item-list-table :table_def="contactList">

                                            <template v-slot:contact_qualite="{ row }">

                                                <span 
                                                    v-if="!!row.contact_qualite"
                                                    class="tag" 
                                                    :style="{ 
                                                        'background': row.contact_qualite_color, 
                                                    }"
                                                    >
                                                    {{ row.contact_qualite }}
                                                </span>

                                            </template>    

                                            <template v-slot:contact_type="{ row }">
                                                
                                                <span v-if="row.contact_type">
                                                    {{ row.contact_type }}
                                                </span>

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
    import ItemListTable from '../../components/miscellaneous/ItemListTable/ItemListTable.vue';

    import {
        CONTACT_LIST_MODULE
    }
    from '../../store/types/types'


    const store = useStore()

    const tabs = ref({
        tout: 'Tout',
    })

    const contactList = computed(() => store.getters[`${CONTACT_LIST_MODULE}contactList`])
    // const contactUserList = computed(() => store.getters[`${CONTACT_LIST_MODULE}contactUserList`])

</script>

<style lang="scss" scoped>
.tag {
    text-transform: capitalize;
    background: #DDD;
    border-radius: 70px;
    text-align: center;
    font-size: 12px;
    height: 24px;
    position: relative;
    display: inline-block;
    vertical-align: middle;
    line-height: 24px;
    transition: all 0.5s ease-in;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    padding: 0 10px;
    width: 120px;
}

</style>
