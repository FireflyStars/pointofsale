<template>
    <statisticsFilters :filterVal="filterVal" @update:filterVal="newValue => filterVal = newValue"></statisticsFilters>
    <div class="p-3 bg-black">
        <div class="d-flex">
            <div class="col-4 p-2 d-flex">
                <div class="rounded-3 bg-white w-100 p-2">
                    <h3 class="font-20 mulish_600_normal">Vente par origine</h3>
                    <div class="d-flex">
                        <div class="col-7">
                            <div class="d-flex" v-for="(origin, index) in originChartData" :key="index">
                                <div class="col-1 d-flex align-items-center">
                                    <div class="origin-dot bg-danger rounded-circle" :style="{'background': origin.color }"></div>
                                </div>
                                <div class="col-6">{{ origin.origin }}</div>
                                <div class="col-5">{{ origin.amount }} €</div>
                            </div>
                        </div>
                        <div class="col-5" id="saleByOrigin" style="height: 150px">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-4 p-2 d-flex">
                <div class="rounded-3 bg-white w-100 p-2">
                    <h3 class="font-20 mulish_600_normal">Vente par Client</h3>
                    <div class="d-flex">
                        <div class="col-7">
                            <div class="d-flex" v-for="(client, index) in clientChartData" :key="index">
                                <div class="col-1 d-flex align-items-center">
                                    <div class="origin-dot rounded-circle" v-if="index==0" :style="{'background-color': '#EB5757'}"></div>
                                    <div class="origin-dot rounded-circle" v-else-if="index==1" :style="{'background-color': '#5200FF'}"></div>
                                    <div class="origin-dot rounded-circle" v-else-if="index==2" :style="{'background-color': '#8ADFDF'}"></div>
                                    <div class="origin-dot rounded-circle" v-else-if="index==3" :style="{'background-color': '#80A2EC'}"></div>
                                    <div class="origin-dot rounded-circle" v-else-if="index==4" :style="{'background-color': '#EEE516'}"></div>
                                    <div class="origin-dot rounded-circle" v-else :style="{'background-color': '#1F78B4'}"></div>
                                </div>
                                <div class="col-6">{{ client.client }}</div>
                                <div class="col-5">{{ client.amount }} €</div>
                            </div>
                        </div>
                        <div class="col-5" id="saleByClient" style="height: 150px">
                        </div>                                                    
                    </div>
                </div>
            </div>
            <div class="col-4 p-2 d-flex">
                <div class="rounded-3 bg-white w-100 p-2">
                    <div class="d-flex">
                        <h3 class="font-20 mulish_600_normal m-0">Moyenne Vente</h3>
                        <TotalPercent class="ms-5" :amount="avgSale" :pastAmount="avgSaleToCompare"></TotalPercent>
                    </div>
                    <div class="d-flex mt-2">
                        <div class="col-5">
                            <h4 class="font-16 mulish_600_normal">Par type de client</h4>
                            <div class="d-flex flex-wrap">
                                <div class="avg-sale-block py-3 px-2 mt-2 me-2 d-flex flex-wrap" v-for="(cate, index) in salesByCustCat" :key="index">
                                    <p class="w-100 text-center font-12">{{cate.name}}</p>
                                    <p class="w-100 text-center font-16 mulish_600_normal align-self-end">{{ cate.amount }}€</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-7">
                            <h4 class="font-16 mulish_600_normal">Finance</h4>
                            <div class="d-flex flex-wrap">
                                <div class="avg-sale-block py-3 px-2 mt-2 me-2 d-flex flex-wrap">
                                    <p class="w-100 text-center font-12">Paiement</p>
                                    <p class="w-100 text-center font-16 mulish_600_normal align-self-end">{{ paiement }}%</p>
                                </div>
                                <div class="avg-sale-block py-3 px-2 me-2 mt-2 d-flex flex-wrap">
                                    <p class="w-100 text-center font-12">Commande Facturé</p>
                                    <p class="w-100 text-center font-16 mulish_600_normal align-self-end">{{ facture }}%</p>
                                </div>
                                <div class="avg-sale-block py-3 px-2 mt-2 d-flex flex-wrap">
                                    <p class="w-100 text-center font-12">Rentabilité</p>
                                    <p class="w-100 text-center font-16 mulish_600_normal align-self-end">5.3 K€</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="p-3 bg-white">
        <div class="d-flex">
            <div class="col-7">
                <div class="bg-gray p-3 rounded-3">
                    <div class="d-flex align-items-center p-2">
                        <h3 class="font-20 mulish_600_normal m-0">Total Vente</h3>
                        <TotalPercent class="ms-5" :amount="salesByOriginTotal" :pastAmount="salesByOriginTotalToCompare"></TotalPercent>
                        <TotalPercent class="ms-5" :amount="salesByClientTotal" :symbol="'h'" :pastAmount="salesByClientTotalToCompare"></TotalPercent>
                        <h4 class="mb-0 ms-auto font-14 text-custom-success text-decoration-underline cursor-pointer"><em>Voir rapport</em></h4>
                    </div>
                    <div class="legends bg-white p-2">
                        <div class="d-flex">
                            <div class="col-2"></div>
                            <div class="col-2 d-flex justify-content-center">Vente €</div>
                            <div class="col-2 d-flex justify-content-center">Vente H</div>
                            <div class="col-2 d-flex justify-content-center">Facture €</div>
                            <div class="col-2 d-flex justify-content-center">Paiement €</div>
                            <div class="col-2 d-flex justify-content-center">Marge %</div>
                        </div>
                        <div class="d-flex">
                            <div class="col-2 d-flex justify-content-center">Période Principale</div>
                            <div class="col-2 d-flex justify-content-center"><CheckBox v-model="legend1" :checked="true"></CheckBox></div>
                            <div class="col-2 d-flex justify-content-center"><CheckBox v-model="legend2" :checked="false"></CheckBox></div>
                            <div class="col-2 d-flex justify-content-center"><CheckBox v-model="legend3" :checked="false"></CheckBox></div>
                            <div class="col-2 d-flex justify-content-center"><CheckBox v-model="legend4" :checked="false"></CheckBox></div>
                            <div class="col-2 d-flex justify-content-center"><CheckBox v-model="legend5" :checked="false"></CheckBox></div>
                        </div>
                        <div class="d-flex">
                            <div class="col-2 d-flex justify-content-center">Période Prècedente</div>
                            <div class="col-2 d-flex justify-content-center"><CheckBox v-model="legend6" :checked="false"></CheckBox></div>
                            <div class="col-2 d-flex justify-content-center"><CheckBox v-model="legend7" :checked="false"></CheckBox></div>
                            <div class="col-2 d-flex justify-content-center"><CheckBox v-model="legend8" :checked="false"></CheckBox></div>
                            <div class="col-2 d-flex justify-content-center"><CheckBox v-model="legend9" :checked="false"></CheckBox></div>
                            <div class="col-2 d-flex justify-content-center"><CheckBox v-model="legend10" :checked="false"></CheckBox></div>
                        </div>
                    </div>
                    <div class="total-chart bg-white" id="totalChart">

                    </div>
                </div>
                <div class="bg-gray p-3 rounded-3 mt-3">
                    <div class="d-flex">
                        <div class="col-6">
                            <h3 class="font-20 mulish_600_normal">Commande</h3>
                            <h4 class="font-16 mulish_600_normal mt-3">Par Responsable</h4>
                            <div class="d-flex align-items-center" v-for="(saleByUser, index) in salesByUser" :key="index">
                                <div class="col-3">{{ saleByUser.name }}</div>
                                <TotalPercent class="col-4" :fontSize="14" :amount="saleByUser.amount" :pastAmount="saleByUser.pastAmount"></TotalPercent>
                                <TotalPercent class="ms-3" :fontSize="14" :amount="saleByUser.hour" :symbol="'h'" :pastAmount="saleByUser.pastHour"></TotalPercent>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex justify-content-between">
                                <h3 class="font-20 mulish_600_normal m-0">Résultats</h3>
                                <h4 class="mb-0 ms-auto font-14 text-custom-success text-decoration-underline cursor-pointer"><em>Voir rapport</em></h4>
                            </div>
                            <h4 class="font-16 mulish_600_normal mt-3">Par commande</h4>
                            <div class="d-flex" v-for="(saleByCommande, index) in salesByCommande" :key="index">
                                <div class="col-3">{{ saleByCommande.id }}</div>
                                <div class="col-3">{{ saleByCommande.name }}</div>
                                <div class="col-3">{{ saleByCommande.amount }}€</div>
                                <div class="col-3">{{ saleByCommande.hour }}h</div>
                            </div>                                                            
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-5 ps-3">
                <div class="bg-gray rounded-3 p-3">
                    <div class="d-flex align-items-center">
                        <h3 class="font-20 mulish_600_normal m-0">Actions Commerciales</h3>
                        <TotalPercent class="ms-5" :amount="totalEventCount" :symbol="''" :pastAmount="totalEventCountPast"></TotalPercent>
                        <h4 class="mb-0 ms-auto font-14 text-custom-success text-decoration-underline cursor-pointer"><em>Voir rapport</em></h4>
                    </div>
                    <div class="d-flex">
                        <div class="col-6">
                            <h4 class="font-16 mulish_600_normal mt-3">Par Action</h4>
                            <div class="d-flex" v-for="(event, index) in eventsByStatus" :key="index">
                                <div class="col-4">{{ event.status }}</div>
                                <div class="col-8 d-flex align-items-center">
                                    <TotalPercent :fontSize="14" :amount="event.countOfEvent" :symbol="''" :pastAmount="event.pastCountOfEvent"></TotalPercent>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <h4 class="font-16 mulish_600_normal mt-3">Par Type</h4>
                            <div class="d-flex" v-for="(event, index) in eventsByType" :key="index">
                                <div class="col-4">{{event.type}}</div>
                                <div class="col-8 d-flex align-items-center">
                                    <TotalPercent :fontSize="14" :amount="event.countOfEvent" :symbol="''" :pastAmount="event.pastCountOfEvent"></TotalPercent>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray rounded-3 p-3 mt-3">
                    <div class="d-flex align-items-center">
                        <h3 class="font-20 mulish_600_normal m-0">Devis</h3>
                        <TotalPercent class="ms-5" :amount="totalDevisCount" :symbol="''" :pastAmount="totalDevisCountPast"></TotalPercent>
                        <h4 class="mb-0 ms-auto font-14 text-custom-success text-decoration-underline cursor-pointer"><em>Voir rapport</em></h4>
                    </div>
                    <div class="d-flex mt-3">
                        <div class="col-6">
                            <h4 class="font-16 mulish_600_normal mt-3">Par Statut</h4>
                            <div class="d-flex align-items-center" v-for="(devis, index) in devisByStatus" :key="index">
                                <div class="col-4">{{ devis.status }}</div>
                                <TotalPercent :fontSize="14" class="ms-5" :amount="devis.countOfDevis" :symbol="''" :pastAmount="devis.pastCountOfDevis"></TotalPercent>
                            </div>
                        </div>
                        <div class="col-6">
                            <h4 class="font-16 mulish_600_normal mt-3">Par Créateur / Responsable</h4>
                            <div class="d-flex align-items-center" v-for="(devis, index) in devisByUser" :key="index">
                                <div class="col-4">{{ devis.name }}</div>
                                <TotalPercent :fontSize="14" class="ms-5" :amount="devis.countOfDevis" :symbol="''" :pastAmount="devis.pastCountOfDevis"></TotalPercent>
                            </div>
                        </div>
                    </div>                                                    
                </div>                                                
            </div>
        </div>
    </div>
</template>
<script>
import { useStore } from 'vuex'
import { ref, watch, onMounted, onUnmounted } from 'vue'
import TotalPercent from '../../components/miscellaneous/TotalPercent';
import StatisticsFilters from "../../components/miscellaneous/StatisticsFilters";
import {
    LOADER_MODULE,
    DISPLAY_LOADER,
    HIDE_LOADER,
}
from '../../store/types/types'
import * as am5 from "@amcharts/amcharts5";
import * as am5percent from "@amcharts/amcharts5/percent";
import * as am5xy from "@amcharts/amcharts5/xy";
import am5themes_Animated from "@amcharts/amcharts5/themes/Animated";
import axios from 'axios';

export default {
    components:{
        TotalPercent,
        StatisticsFilters,
    },
    setup(){
        const store = useStore();
        const legend1 = ref(true);
        const legend2 = ref(false);
        const legend3 = ref(false);
        const legend4 = ref(false);
        const legend5 = ref(false);
        const legend6 = ref(false);
        const legend7 = ref(false);
        const legend8 = ref(false);
        const legend9 = ref(false);
        const legend10 = ref(false);
        const salesByOriginTotal = ref(0);
        const salesByOriginTotalToCompare = ref(0);        
        const salesByClientTotal = ref(0);
        const salesByClientTotalToCompare = ref(0);    

        const salesByCustCat = ref(0);
        const avgSale = ref(0);
        const avgSaleToCompare = ref(0);
        const facture = ref(0);
        const paiement = ref(0);
        const salesByCommande = ref([]);
        const salesByUser = ref([]);

        const devisByStatus = ref([]);
        const devisByUser = ref([]);
        const totalDevisCount = ref(0);
        const totalDevisCountPast = ref(0);

        const eventsByStatus = ref([]);
        const eventsByType = ref([]);
        const totalEventCount = ref(0);
        const totalEventCountPast = ref(0);

        const today = new Date();
        const filterVal = ref({
            customFilter: 0,
            startDate: `${today.getFullYear()}-${today.getMonth()+1}-${today.getDate()}`,
            endDate:`${today.getFullYear()}-${today.getMonth()+1}-${today.getDate()}`,
            dateRangeType:'Last Month',
            compareMode: 'month',
            compareCustomFilter: false,
            compareStartDate: `${today.getFullYear()-1}-${today.getMonth()+1}-${today.getDate()}`,
            compareEndDate: `${today.getFullYear()-1}-${today.getMonth()+1}-${today.getDate()}`,
        });        
        onMounted(()=>{
            store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'Loading data...']);
            axios.post('/statistique', filterVal.value)
                .then((res) => {
                    originChartData.value = res.data.salesByOrigin;
                    salesByOriginTotal.value = res.data.salesByOriginTotal;
                    salesByOriginTotalToCompare.value = res.data.salesByOriginTotalToCompare;

                    clientChartData.value = res.data.salesByClient;
                    salesByClientTotal.value = res.data.salesByClientTotal;
                    salesByClientTotalToCompare.value = res.data.salesByClientTotalToCompare;

                    series1Data.value = res.data.series1Data;
                    series2Data.value = res.data.series2Data;
                    series3Data.value = res.data.series3Data;
                    series4Data.value = res.data.series4Data;
                    series5Data.value = res.data.series5Data;
                    series6Data.value = res.data.series6Data;
                    series7Data.value = res.data.series7Data;
                    series8Data.value = res.data.series8Data;
                    series9Data.value = res.data.series9Data;
                    series10Data.value = res.data.series10Data;

                    salesByCustCat.value = res.data.salesByCustCat;
                    avgSale.value = res.data.avgSale;
                    avgSaleToCompare.value = res.data.avgSaleToCompare;
                    facture.value = res.data.facture;
                    paiement.value = res.data.paiement;
                    salesByCommande.value = res.data.salesByCommande;
                    salesByUser.value = res.data.salesByUser;

                    devisByStatus.value = res.data.devisByStatus;
                    devisByUser.value = res.data.devisByUser;
                    totalDevisCount.value = res.data.totalDevisCount;
                    totalDevisCountPast.value = res.data.totalDevisCountPast;

                    eventsByStatus.value = res.data.eventsByStatus;
                    eventsByType.value = res.data.eventsByType;
                    totalEventCount.value = res.data.totalEventCount;
                    totalEventCountPast.value = res.data.totalEventCountPast;
                    initOriginChart();
                    initClientChart();
                    initTotalChart();
                }).finally(()=>{
                    store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
            });
        })
        onUnmounted(()=>{
            destroyChart();
        })
        const destroyChart = ()=>{
            originChartRoot.dispose();
            clientChartRoot.dispose();
            totalRoot.dispose();
        }
        watch(() => filterVal.value, (current_val, previous_val) => {
            destroyChart();
            store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'Loading data...']);
            axios.post('/statistique', current_val).then((res) => {
                originChartData.value = res.data.salesByOrigin;
                salesByOriginTotal.value = res.data.salesByOriginTotal;
                salesByOriginTotalToCompare.value = res.data.salesByOriginTotalToCompare;

                clientChartData.value = res.data.salesByClient;
                salesByClientTotal.value = res.data.salesByClientTotal;
                salesByClientTotalToCompare.value = res.data.salesByClientTotalToCompare;

                series1Data.value = res.data.series1Data;
                series2Data.value = res.data.series2Data;
                series3Data.value = res.data.series3Data;
                series4Data.value = res.data.series4Data;
                series5Data.value = res.data.series5Data;
                series6Data.value = res.data.series6Data;
                series7Data.value = res.data.series7Data;
                series8Data.value = res.data.series8Data;
                series9Data.value = res.data.series9Data;
                series10Data.value = res.data.series10Data;

                salesByCustCat.value = res.data.salesByCustCat;
                avgSale.value = res.data.avgSale;
                avgSaleToCompare.value = res.data.avgSaleToCompare;
                facture.value = res.data.facture;
                paiement.value = res.data.paiement;
                salesByCommande.value = res.data.salesByCommande;
                salesByUser.value = res.data.salesByUser;

                devisByStatus.value = res.data.devisByStatus;
                devisByUser.value = res.data.devisByUser;
                totalDevisCount.value = res.data.totalDevisCount;
                totalDevisCountPast.value = res.data.totalDevisCountPast;

                eventsByStatus.value = res.data.eventsByStatus;
                eventsByType.value = res.data.eventsByType;
                totalEventCount.value = res.data.totalEventCount;
                totalEventCountPast.value = res.data.totalEventCountPast;
                initOriginChart();
                initClientChart();
                initTotalChart();                    
            }).finally(()=>{
                store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
            });
        });        
        let originChartRoot = null;
        let originChart = null;
        let originSeries= null;
        const originChartData = ref([
        ]);
        const initOriginChart = ()=>{
            // Define data for sales by origin
            // Create root element
            originChartRoot = am5.Root.new("saleByOrigin");
            // Set themes
            originChartRoot.setThemes([
                am5themes_Animated.new(originChartRoot)
            ]);
            // Create chart
            originChart = originChartRoot.container.children.push(am5percent.PieChart.new(originChartRoot, {
                radius: am5.percent(70),
                innerRadius: 70,
                layout: originChartRoot.verticalLayout
            }));
            // hide logo
            originChart._root._logo._childrenDisplay.visible  = false;
            // originChart._root._logo._display.visible  = false;
            // originChart._root._logo._display.scale  = 0;
            // Create series
            originSeries = originChart.series.push(am5percent.PieSeries.new(originChartRoot, {
                name: "Series",
                valueField: "amount",
                categoryField: "origin",
            }));
            originSeries.get("colors").set("colors", [
                am5.color(0xEB5757),
                am5.color(0x5200FF),
            ]);                
            originSeries.labels.template.set("forceHidden", true);
            originSeries.ticks.template.set("visible", false);
            // Add label
            let percent = '';
            if(salesByOriginTotal.value == salesByOriginTotalToCompare.value){
                percent = `[#0f0][fontStyle: italic]0%[/][/]`;
            }
            if(salesByOriginTotal.value > salesByOriginTotalToCompare.value && salesByOriginTotalToCompare.value != 0){
                percent = `[#0f0][fontStyle: italic]${((salesByOriginTotal.value / salesByOriginTotalToCompare.value -1)* 100).toFixed(0)}%[/][/]`;
            }
            if(salesByOriginTotal.value < salesByOriginTotalToCompare.value && salesByOriginTotalToCompare.value != 0){
                percent = `[#f00][fontStyle: italic]${((1 - salesByOriginTotal.value / salesByOriginTotalToCompare.value)* 100).toFixed(0)}%[/][/]`;
            }
            if(salesByOriginTotal.value > salesByOriginTotalToCompare.value && salesByOriginTotalToCompare.value == 0){
                percent = "--";
            }
            let originLabel = originSeries.children.push(am5.Label.new(originChartRoot, {
                text: "${valueSum.formatNumber('#,###.')}\n"+ percent,
                textAlign: 'center',
                centerX: am5.percent(50),
                centerY: am5.percent(50),
                fontSize: 20,
                populateText: true
            }));
            // Set data
            originSeries.data.setAll(originChartData.value);
            originSeries.onPrivate("valueSum", function(){
                originLabel.text.markDirtyText();
            })
            originSeries.appear(1000, 100);
        }

        let clientChartRoot = null;
        let clientChart = null;
        let clientSeries = null;
        // Define data for sales by customer
        const clientChartData = ref([
        ]);
        const initClientChart = ()=>{
            // Create root element
            clientChartRoot = am5.Root.new("saleByClient");
            // Set themes
            clientChartRoot.setThemes([
                am5themes_Animated.new(clientChartRoot)
            ]);
            // Create chart
            clientChart = clientChartRoot.container.children.push(am5percent.PieChart.new(clientChartRoot, {
                radius: am5.percent(70),
                innerRadius: 70,
                layout: clientChartRoot.verticalLayout
            }));
            // hide logo
            clientChart._root._logo._childrenDisplay.visible  = false;
            // clientChart._root._logo._display.visible  = false;
            // clientChart._root._logo._display.scale  = 0;
            // Create series
            clientSeries = clientChart.series.push(am5percent.PieSeries.new(clientChartRoot, {
                name: "Series",
                valueField: "amount",
                categoryField: "client",
            }));
            clientSeries.get("colors").set("colors", [
                am5.color(0xEB5757),
                am5.color(0x5200FF),
                am5.color(0x8ADFDF),
                am5.color(0x80A2EC),
                am5.color(0xEEE516),
                am5.color(0x1F78B4)
            ]);            
            clientSeries.labels.template.set("forceHidden", true);
            clientSeries.ticks.template.set("visible", false);
            // Add label
            let percent = '';
            if(salesByClientTotal.value == salesByClientTotalToCompare.value){
                percent = `[#0f0][fontStyle: italic]0%[/][/]`;
            }
            if(salesByClientTotal.value > salesByClientTotalToCompare.value && salesByClientTotalToCompare.value != 0){
                percent = `[#0f0][fontStyle: italic]${((salesByClientTotal.value / salesByClientTotalToCompare.value-1)* 100).toFixed(0)}%[/][/]`;
            }
            if(salesByClientTotal.value < salesByClientTotalToCompare.value && salesByClientTotalToCompare.value != 0){
                percent = `[#f00][fontStyle: italic]${((1 - salesByClientTotal.value / salesByClientTotalToCompare.value)* 100).toFixed(0)}%[/][/]`;
            }
            if(salesByClientTotal.value > salesByClientTotalToCompare.value && salesByClientTotalToCompare.value == 0){
                percent = "--";
            }            
            let clientLabel = clientSeries.children.push(am5.Label.new(clientChartRoot, {
                text: "${valueSum.formatNumber('#,###.')}\n" + percent,
                textAlign: 'center',
                centerX: am5.percent(50),
                centerY: am5.percent(50),
                fontSize: 20,
                populateText: true
            }));
            // Set data
            clientSeries.data.setAll(clientChartData.value);
            clientSeries.onPrivate("valueSum", function(){
                clientLabel.text.markDirtyText();
            })
            clientSeries.appear(1000, 100);
        }
        let totalRoot = null;
        let totalChart = null;
        let xAxis = null;
        let yAxis = null;
        let series1 = null;
        const series1Data = ref([]);
        let series2 = null;
        const series2Data = ref([]);
        let series3 = null;
        const series3Data = ref([]);
        let series4 = null;        
        const series4Data = ref([]);
        let series5 = null;
        const series5Data = ref([]);
        let series6 = null;
        const series6Data = ref([]);
        let series7 = null;
        const series7Data = ref([]);
        let series8 = null;        
        const series8Data = ref([]);
        let series9 = null;        
        const series9Data = ref([]);
        let series10 = null;        
        const series10Data = ref([]);        
        const initTotalChart = ()=>{
            totalRoot = am5.Root.new("totalChart");
            totalRoot.setThemes([
                am5themes_Animated.new(totalRoot)
            ]);
            // Create chart
            totalChart = totalRoot.container.children.push(am5xy.XYChart.new(totalRoot, {
                panX: true,
                panY: true,
                wheelX: "panX",
                wheelY: "zoomX",
                pinchZoomX: true
            }));
            // hide logo
            totalChart._root._logo._childrenDisplay.visible  = false;
            // totalChart._root._logo._display.visible  = false;
            // totalChart._root._logo._display.scale  = 0;
            // Add cursor
            let cursor = totalChart.set("cursor", am5xy.XYCursor.new(totalRoot, {
                behavior: "none"
            }));
            cursor.lineY.set("visible", false);

            
            // Create axes
            xAxis = totalChart.xAxes.push(am5xy.DateAxis.new(totalRoot, {
                maxDeviation: 0.2,
                baseInterval: {
                    timeUnit: "day",
                    count: 1
                },
                renderer: am5xy.AxisRendererX.new(totalRoot, {}),
                tooltip: am5.Tooltip.new(totalRoot, {})
            }));
            yAxis = totalChart.yAxes.push(am5xy.ValueAxis.new(totalRoot, {
                renderer: am5xy.AxisRendererY.new(totalRoot, {})
            }));
            // Add scrollbar
            totalChart.set("scrollbarX", am5.Scrollbar.new(totalRoot, {
                orientation: "horizontal"
            }));

            addSeries(1);
            totalChart.appear(1000, 100);
        }

        const addSeries = (seriesIndex)=>{
            // Add series
            if(seriesIndex == 1){
                series1 = totalChart.series.push(am5xy.LineSeries.new(totalRoot, {
                    name: name,
                    xAxis: xAxis,
                    yAxis: yAxis,
                    valueYField: "amount",
                    valueXField: "date",
                    tooltip: am5.Tooltip.new(totalRoot, {
                        labelText: "{valueY}"
                    })
                }));
                series1.data.processor = am5.DataProcessor.new(totalRoot, {
                    dateFields: ["date"], dateFormat: "yyyy-MM-dd"
                });                
                series1.data.setAll(series1Data.value);
                // Make stuff animate on load
                series1.appear(1000);
            }else if(seriesIndex == 2){
                series2 = totalChart.series.push(am5xy.LineSeries.new(totalRoot, {
                    name: name,
                    xAxis: xAxis,
                    yAxis: yAxis,
                    valueYField: "amount",
                    valueXField: "date",
                    tooltip: am5.Tooltip.new(totalRoot, {
                        labelText: "{valueY}"
                    })
                }));
                // Generate random data
                series2.data.processor = am5.DataProcessor.new(totalRoot, {
                    dateFields: ["date"], dateFormat: "yyyy-MM-dd"
                });                                
                series2.data.setAll(series2Data.value);
                // Make stuff animate on load
                series2.appear(1000);                
            }else if(seriesIndex == 3){
                series3 = totalChart.series.push(am5xy.LineSeries.new(totalRoot, {
                    name: name,
                    xAxis: xAxis,
                    yAxis: yAxis,
                    valueYField: "amount",
                    valueXField: "date",
                    tooltip: am5.Tooltip.new(totalRoot, {
                        labelText: "{valueY}"
                    })
                }));
                // Generate random data
                series3.data.processor = am5.DataProcessor.new(totalRoot, {
                    dateFields: ["date"], dateFormat: "yyyy-MM-dd"
                });                    
                series3.data.setAll(series3Data.value);
                // Make stuff animate on load
                series3.appear(1000);                
            }else if(seriesIndex == 4){
                series4 = totalChart.series.push(am5xy.LineSeries.new(totalRoot, {
                    name: name,
                    xAxis: xAxis,
                    yAxis: yAxis,
                    valueYField: "amount",
                    valueXField: "date",
                    tooltip: am5.Tooltip.new(totalRoot, {
                        labelText: "{valueY}"
                    })
                }));
                // Generate random data
                series4.data.processor = am5.DataProcessor.new(totalRoot, {
                    dateFields: ["date"], dateFormat: "yyyy-MM-dd"
                });    
                series4.data.setAll(series4Data.value);
                // Make stuff animate on load
                series4.appear(1000);                
            }else if(seriesIndex == 5){
                series5 = totalChart.series.push(am5xy.LineSeries.new(totalRoot, {
                    name: name,
                    xAxis: xAxis,
                    yAxis: yAxis,
                    valueYField: "amount",
                    valueXField: "date",
                    tooltip: am5.Tooltip.new(totalRoot, {
                        labelText: "{valueY}"
                    })
                }));
                series5.data.processor = am5.DataProcessor.new(totalRoot, {
                    dateFields: ["date"], dateFormat: "yyyy-MM-dd"
                });    
                series5.data.setAll(series5Data.value);
                // Make stuff animate on load
                series5.appear(1000);                
            }else if(seriesIndex == 6){
                series6 = totalChart.series.push(am5xy.LineSeries.new(totalRoot, {
                    name: name,
                    xAxis: xAxis,
                    yAxis: yAxis,
                    valueYField: "amount",
                    valueXField: "date",
                    tooltip: am5.Tooltip.new(totalRoot, {
                        labelText: "{valueY}"
                    })
                }));
                series6.data.processor = am5.DataProcessor.new(totalRoot, {
                    dateFields: ["date"], dateFormat: "yyyy-MM-dd"
                });    
                series6.data.setAll(series6Data.value);
                // Make stuff animate on load
                series6.appear(1000);                
            }else if(seriesIndex == 7){
                series7 = totalChart.series.push(am5xy.LineSeries.new(totalRoot, {
                    name: name,
                    xAxis: xAxis,
                    yAxis: yAxis,
                    valueYField: "amount",
                    valueXField: "date",
                    tooltip: am5.Tooltip.new(totalRoot, {
                        labelText: "{valueY}"
                    })
                }));
                series7.data.processor = am5.DataProcessor.new(totalRoot, {
                    dateFields: ["date"], dateFormat: "yyyy-MM-dd"
                });    
                series7.data.setAll(series7Data.value);
                // Make stuff animate on load
                series7.appear(1000);                
            }else if(seriesIndex == 8){
                series8 = totalChart.series.push(am5xy.LineSeries.new(totalRoot, {
                    name: name,
                    xAxis: xAxis,
                    yAxis: yAxis,
                    valueYField: "amount",
                    valueXField: "date",
                    tooltip: am5.Tooltip.new(totalRoot, {
                        labelText: "{valueY}"
                    })
                }));
                series8.data.processor = am5.DataProcessor.new(totalRoot, {
                    dateFields: ["date"], dateFormat: "yyyy-MM-dd"
                });    
                series8.data.setAll(series8Data.value);
                // Make stuff animate on load
                series8.appear(1000);                
            }else if(seriesIndex == 9){
                series9 = totalChart.series.push(am5xy.LineSeries.new(totalRoot, {
                    name: name,
                    xAxis: xAxis,
                    yAxis: yAxis,
                    valueYField: "amount",
                    valueXField: "date",
                    tooltip: am5.Tooltip.new(totalRoot, {
                        labelText: "{valueY}"
                    })
                }));
                series9.data.processor = am5.DataProcessor.new(totalRoot, {
                    dateFields: ["date"], dateFormat: "yyyy-MM-dd"
                });    
                series9.data.setAll(series9Data.value);
                // Make stuff animate on load
                series9.appear(1000);                
            }else{
                series10 = totalChart.series.push(am5xy.LineSeries.new(totalRoot, {
                    name: name,
                    xAxis: xAxis,
                    yAxis: yAxis,
                    valueYField: "amount",
                    valueXField: "date",
                    tooltip: am5.Tooltip.new(totalRoot, {
                        labelText: "{valueY}"
                    })
                }));
                series10.data.processor = am5.DataProcessor.new(totalRoot, {
                    dateFields: ["date"], dateFormat: "yyyy-MM-dd"
                });    
                series10.data.setAll(series10Data.value);
                // Make stuff animate on load
                series10.appear(1000);                
            }
        }
        const removeSeries = (series)=>{
            totalChart.series.removeIndex(
                totalChart.series.indexOf(series)
            );
        }
        watch( ()=> legend1.value, (cur_val, pre_val)=>{
            if(cur_val){
                addSeries(1);
            }else{
                removeSeries(series1);
            }
        });
        watch( ()=> legend2.value, (cur_val, pre_val)=>{
            if(cur_val){
                addSeries(2);
            }else{
                removeSeries(series2);
            }
        });
        watch( ()=> legend3.value, (cur_val, pre_val)=>{
            if(cur_val){
                addSeries(3);
            }else{
                removeSeries(series3);
            }
        });
        watch( ()=> legend4.value, (cur_val, pre_val)=>{
            if(cur_val){
                addSeries(4);
            }else{
                removeSeries(series4);
            }
        });
        watch( ()=> legend5.value, (cur_val, pre_val)=>{
            if(cur_val){
                addSeries(5);
            }else{
                removeSeries(series5);
            }
        });
        watch( ()=> legend6.value, (cur_val, pre_val)=>{
            if(cur_val){
                addSeries(6);
            }else{
                removeSeries(series6);
            }
        });
        watch( ()=> legend7.value, (cur_val, pre_val)=>{
            if(cur_val){
                addSeries(7);
            }else{
                removeSeries(series7);
            }
        });
        watch( ()=> legend8.value, (cur_val, pre_val)=>{
            if(cur_val){
                addSeries(8);
            }else{
                removeSeries(series8);
            }
        });
        watch( ()=> legend9.value, (cur_val, pre_val)=>{
            if(cur_val){
                addSeries(9);
            }else{
                removeSeries(series9);
            }
        });
        watch( ()=> legend10.value, (cur_val, pre_val)=>{
            if(cur_val){
                addSeries(10);
            }else{
                removeSeries(series10);
            }
        });              
        return {
            legend1,
            legend2,
            legend3,
            legend4,
            legend5,
            legend6,
            legend7,
            legend8,
            legend9,
            legend10,
            filterVal,
            originChartData,
            clientChartData,
            series1Data,
            series2Data,
            series3Data,
            series4Data,
            series5Data,
            series6Data,
            series7Data,
            series8Data,
            series9Data,
            series10Data,
            salesByOriginTotal,
            salesByOriginTotalToCompare,
            salesByClientTotal,
            salesByClientTotalToCompare,
            salesByCustCat,
            avgSale,
            avgSaleToCompare,
            facture,
            paiement,
            salesByCommande,
            salesByUser,
            devisByStatus,
            devisByUser,
            totalDevisCount,
            totalDevisCountPast,
            eventsByStatus,
            eventsByType,
            totalEventCount,
            totalEventCountPast,
        }
    }
}
</script>

<style lang="scss" scoped>
.origin-dot{
    width: 14px;
    height: 14px;
}
.avg-sale-block{
    min-width: 75px;
    max-width: 95px;
    height: 95px;
    background: #E0E0E0;
    border-radius: 10px;
}
.total-chart{
    min-height: 400px;
}
</style>