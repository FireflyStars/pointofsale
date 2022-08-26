
<template>
    <router-view v-slot="{ Component }">
      
            <div class="container-fluid h-100 bg-color">
                <main-header />

                <div class="row d-flex align-content-stretch align-items-stretch flex-row hmax main-view-wrap" style="z-index:100" >
                    
                    <side-bar />
            <transition enter-active-class="animate__animated animate__fadeIn" >
                    <div class="col main-view container" v-if="showcontainer">
                            <page-title icon="star" name="DEVIS" class="almarai_extrabold_normal_normal" style="width: 45px; height: 45px; " />

            <div class="row m-0 ml-5 mr-5">
                        <div class="col">
                            <tab-pane :tabs="tabs" current='tout' class="almarai_700_normal">
                                <template v-slot:tout>
                                   <item-list-table :table_def="all_devis" >
                                        <template v-slot:order_state_id="{row}">
                                            
                                            <order-state-tag  :order_state_id="row.order_state_id" classes="almarai_700_normal"></order-state-tag>
                                        </template>
                                   </item-list-table>
                                </template>
                                <template v-slot:en_cours>
                                    2nd
                                </template>
                               <template v-slot:a_faire>
                                    3rd
                                </template>
                                 <template v-slot:gagne>
                                    4
                                </template>
                                      <template v-slot:perdu>
                                    5
                                </template>
                                      <template v-slot:en_retard>
                                    6
                                </template>
                                      <template v-slot:mes_devis>
                                    7
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

<script>

import MainHeader from '../../components/layout/MainHeader.vue';
import SideBar from '../../components/layout/SideBar.vue';
import ItemListTable from '../../components/miscellaneous/ItemListTable/ItemListTable.vue';
import OrderStateTag from '../../components/miscellaneous/OrderStateTag.vue';
import { ref, onMounted, nextTick, computed } from 'vue';
import { useStore } from 'vuex';
import { DEVIS_LIST_MODULE, GET_DEVIS_LIST_DEF } from '../../store/types/types';


export default {

    name: "Devis",

    components: {
      MainHeader,
      SideBar,
      ItemListTable,
      OrderStateTag
    },

    setup() {

        const tabs = ref({})
        const store = useStore()

        const all_devis = computed(()=>store.getters[`${DEVIS_LIST_MODULE}${GET_DEVIS_LIST_DEF}`]);
    
        tabs.value= {
            tout:'Tout',
            en_cours:'En cours',
            a_faire:'A faire',
            gagne:'Gagné',
            perdu:'Perdu',
            en_retard:'En retard',
            mes_devis:'Mes devis',
        };

        const showcontainer = ref(false)

        onMounted(() => {
            nextTick(() => {
                showcontainer.value=true
            })

        })

        return {
            showcontainer,
            tabs,
            all_devis
        }

  }

}
</script>

<style lang="scss" scoped>
.lcdt-logo {
    padding-left: 0
}

</style>
