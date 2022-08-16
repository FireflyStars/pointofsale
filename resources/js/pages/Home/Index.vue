<template>
    <div class="container-fluid h-100 bg-color">
        <main-header />
        <div class="row d-flex align-content-stretch align-items-stretch flex-row hmax main-view-wrap" style="z-index:100">
            <side-bar />
            <transition>
                <div class="col main-view container">
                    <div class="row m-0 ml-5 mr-5">
                        <div class="col p-0 d-flex mt-5">
                            <div class="home-news-pannel bg-white rounded-3 p-3" v-html="content">
                                
                            </div>
                            <div class="stats-pannel">
                                <div class="breif-total p-3 bg-white rounded-3">
                                    <h4 class="font-24 mulish_normal_600">En bref cette semaine semaine</h4>
                                    <div class="mt-3 action-commercial px-3">
                                        <h4 class="font-16 mulish_normal_600">Mes actions commerciales</h4>
                                        <div class="mt-3 bg-dark-gray rounded-3 p-3">
                                            <div class="d-flex justify-content-between mt-2" v-for="(event, index) in events" :key="index">
                                                <div class="col-4">{{ event.status }}</div>
                                                <div class="col-4 text-end">{{ event.countOfEvent }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-3 marketing px-3">
                                        <h4 class="font-16 mulish_normal_600">Marketing</h4>
                                        <div class="mt-3 bg-dark-gray rounded-3 p-3">
                                            <div class="d-flex justify-content-between mt-2" v-for="(camp, index) in campagne" :key="index">
                                                <div class="col-4">{{ camp.name }}</div>
                                                <div class="col-4 text-end">{{ camp.date }}</div>
                                            </div>
                                        </div>
                                    </div>                                    
                                </div>
                                <div class="month-sales p-3 bg-white rounded-3 mt-3">
                                    <div class="d-flex justify-content-between">
                                        <h4 class="font-24 mulish_normal_600">Vente par mois</h4>
                                        <div class="me-3">
                                            <TotalPercent :amount="totalOrder" :pastAmount="pastTotalOrder"></TotalPercent>
                                            <TotalPercent :amount="totalHour" :symbol="'Hr'" :pastAmount="pastTotalHour"></TotalPercent>
                                        </div>
                                    </div>
                                    <div class="d-flex mt-3 px-3">
                                        <div class="col-5">
                                            <h4 class="font-16 mulish_600_normal">Devis</h4>
                                            <div class="d-flex flex-wrap">
                                                <div class="avg-sale-block py-3 px-2 mt-2 me-2 d-flex flex-wrap">
                                                    <p class="w-100 text-center font-12">Devis A faire</p>
                                                    <p class="w-100 text-center font-20 mulish_600_normal align-self-end">{{ devisAFaire }}</p>
                                                </div>
                                                <div class="avg-sale-block py-3 px-2 mt-2 d-flex flex-wrap">
                                                    <p class="w-100 text-center font-12">Devis <span class="text-nowrap">Attente client</span></p>
                                                    <p class="w-100 text-center font-20 mulish_600_normal align-self-end">{{ devisAttenteClient }}</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-7">
                                            <h4 class="font-16 mulish_600_normal">Finance</h4>
                                            <div class="d-flex flex-wrap">
                                                <div class="avg-sale-block py-3 px-2 mt-2 me-2 d-flex flex-wrap">
                                                    <p class="w-100 text-center font-12">Paiement</p>
                                                    <p class="w-100 text-center font-20 mulish_600_normal align-self-end" v-if="paiement != '--'">{{ paiement }} %</p>
                                                    <p class="w-100 text-center font-20 mulish_600_normal align-self-end" v-else>--</p>                                                    
                                                </div>
                                                <div class="avg-sale-block py-3 px-2 me-2 mt-2 d-flex flex-wrap">
                                                    <p class="w-100 text-center font-12">Commande Facturé</p>
                                                    <p class="w-100 text-center font-20 mulish_600_normal align-self-end" v-if="facture != '--'">{{ facture }} %</p>
                                                    <p class="w-100 text-center font-20 mulish_600_normal align-self-end" v-else>--</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </transition>    
        </div>
    </div>    
</template>
<script>
import { ref, onMounted } from 'vue';
import TotalPercent from '../../components/miscellaneous/TotalPercent';
import {
    LOADER_MODULE,
    DISPLAY_LOADER,
    HIDE_LOADER,
}
from '../../store/types/types'
import axios from 'axios';
import { useStore } from 'vuex';
export default {
    components:{
        TotalPercent
    },
    setup(){
        const store = useStore();
        const content = ref('');
        const devisAFaire = ref(0);
        const devisAttenteClient = ref(0);
        const paiement = ref(0);
        const facture = ref(0);
        const totalOrder = ref(0);
        const pastTotalOrder = ref(0);
        const totalHour = ref(0);
        const pastTotalHour = ref(0);
        const events = ref([]);
        const campagne = ref([]);

        onMounted(()=>{
            store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'Loading home news...']);
            axios.post('/homenews').then((res)=>{
                content.value = res.data.content;
                devisAFaire.value = res.data.devisAFaire;
                devisAttenteClient.value = res.data.devisAttenteClient;
                paiement.value = res.data.paiement;
                facture.value = res.data.facture;
                totalOrder.value = res.data.totalOrder;
                pastTotalOrder.value = res.data.pastTotalOrder;
                totalHour.value = res.data.totalHour;
                pastTotalHour.value = res.data.pastTotalHour;
                events.value = res.data.events;
                campagne.value = res.data.campagne;
            }).catch((error)=>{
                console.log(error);
            }).finally(()=>{
                store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
            })
        })
        return {
            content,
            devisAFaire,
            devisAttenteClient,
            paiement,
            facture,
            totalOrder,
            pastTotalOrder,
            totalHour,
            pastTotalHour,
            events,
            campagne,
        }
    }
}
</script>
<style>
.main-view{
    margin: 64px 60px 0;
}
</style>
<style lang="scss" scoped>
.home-news-pannel{
    width: calc(100% - 620px);
}
.bg-dark-gray{
    background-color: #E0E0E0 !important;
}
.stats-pannel{
    margin-left: 60px;
    width: 560px;
    .avg-sale-block{
        width: 85px;
        height: 100px;
        background: #E0E0E0;
        border-radius: 10px;
    }    
}
</style>