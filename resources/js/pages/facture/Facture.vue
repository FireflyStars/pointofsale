
<template>
    <router-view v-slot="{ Component }">
      
            <div class="container-fluid h-100 bg-color">
                <main-header />

                <div class="row d-flex align-content-stretch align-items-stretch flex-row hmax main-view-wrap" style="z-index:100" >
                    
                    <side-bar />
            <transition enter-active-class="animate__animated animate__fadeIn" >
                    <div class="col main-view container" v-if="showcontainer">
                            <page-title icon="edit" name="FACTURE" class="almarai_extrabold_normal_normal"/>

            <div class="row m-0 ml-5 mr-5">
                        <div class="col">
                            <tab-pane :tabs="tabs" current='tout' class="almarai_700_normal">
                                <template v-slot:tout>
                                   <item-list-table :table_def="all_facture" >
                                        <template v-slot:invoice_state_id="{row}">
                                            
                                            <invoice-state-tag  :invoice_state_id="row.invoice_state_id" classes="almarai_700_normal"></invoice-state-tag>
                                        </template>
                                   </item-list-table>
                                </template>
                                <template v-slot:en_creation>
                                    2nd
                                </template>
                               <template v-slot:en_paiement>
                                    3rd
                                </template>
                                 <template v-slot:avoir_remise>
                                    4
                                </template>
                                      <template v-slot:annuler>
                                    5
                                </template>
                                <template v-slot:mes_factures>
                                    6
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
import InvoiceStateTag from '../../components/miscellaneous/InvoiceStateTag.vue';
import { ref, onMounted, nextTick, computed } from 'vue';
import { useStore } from 'vuex';
import { FACTURE_LIST_MODULE, GET_FACTURE_LIST_DEF } from '../../store/types/types';


export default {

    name: "Facture",

    components: {
      MainHeader,
      SideBar,
      ItemListTable,
      InvoiceStateTag
    },

    setup() {

        const tabs=ref({});
        const store=useStore();

        const all_facture=computed(()=>store.getters[`${FACTURE_LIST_MODULE}${GET_FACTURE_LIST_DEF}`]);
  
        tabs.value= {
            tout:'Tout',
            en_creation:'En création',
            en_paiement:'En paiement',
            avoir_remise:'Avoir/Remise',
            annuler:'Annuler',
            mes_factures:'Mes factures',
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
            all_facture
        }

  }

}
</script>

<style lang="scss" scoped>
.lcdt-logo {
    padding-left: 0
}

</style>
