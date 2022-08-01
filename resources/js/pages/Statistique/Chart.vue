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
                                    <div class="origin-dot bg-danger rounded-circle"></div>
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
                        <h3 class="font-20 mulish_600_normal m-0">Vente par Client</h3>
                        <TotalPercent class="ms-5" :amount="'4.7K'" :arrow="1" :symbol="'€'" :percent="38"></TotalPercent>
                    </div>
                    <div class="d-flex mt-2">
                        <div class="col-5">
                            <h4 class="font-16 mulish_600_normal">Par type de client</h4>
                            <div class="d-flex flex-wrap">
                                <div class="avg-sale-block py-3 px-2 mt-2 me-2 d-flex flex-wrap">
                                    <p class="w-100 text-center font-12">SARL</p>
                                    <p class="w-100 text-center font-20 mulish_600_normal align-self-end">3,3 K€</p>
                                </div>
                                <div class="avg-sale-block py-3 px-2 mt-2 d-flex flex-wrap">
                                    <p class="w-100 text-center font-12">SAS</p>
                                    <p class="w-100 text-center font-20 mulish_600_normal align-self-end">5.3 K€</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-7">
                            <h4 class="font-16 mulish_600_normal">Finance</h4>
                            <div class="d-flex flex-wrap">
                                <div class="avg-sale-block py-3 px-2 mt-2 me-2 d-flex flex-wrap">
                                    <p class="w-100 text-center font-12">Paiement</p>
                                    <p class="w-100 text-center font-20 mulish_600_normal align-self-end">3,3 K€</p>
                                </div>
                                <div class="avg-sale-block py-3 px-2 me-2 mt-2 d-flex flex-wrap">
                                    <p class="w-100 text-center font-12">Commande Facturé</p>
                                    <p class="w-100 text-center font-20 mulish_600_normal align-self-end">5.3 K€</p>
                                </div>
                                <div class="avg-sale-block py-3 px-2 mt-2 d-flex flex-wrap">
                                    <p class="w-100 text-center font-12">Rentabilité</p>
                                    <p class="w-100 text-center font-20 mulish_600_normal align-self-end">5.3 K€</p>
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
                        <TotalPercent class="ms-5" :amount="'150 471'" :arrow="1" :symbol="'€'" :percent="38"></TotalPercent>
                        <TotalPercent class="ms-5" :amount="'4501'" :arrow="1" :symbol="'h'" :percent="25"></TotalPercent>
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
                            <div class="d-flex">
                                <div class="col-3">Laurent</div>
                                <TotalPercent class="col-4" :size="14" :amount="'38k'" :arrow="1" :symbol="''" :percent="184"></TotalPercent>
                                <TotalPercent class="ms-3" :size="14" :amount="'15k'" :arrow="1" :symbol="'h'" :percent="184"></TotalPercent>
                            </div>                                                            
                            <div class="d-flex">
                                <div class="col-3">Franck</div>
                                <TotalPercent class="col-4" :size="14" :amount="'38k'" :arrow="1" :symbol="''" :percent="184"></TotalPercent>
                                <TotalPercent class="ms-3" :size="14" :amount="'15k'" :arrow="1" :symbol="'h'" :percent="184"></TotalPercent>                                                                
                            </div>                                                            
                            <div class="d-flex">
                                <div class="col-3">André</div>
                                <TotalPercent class="col-4" :size="14" :amount="'38k'" :arrow="1" :symbol="''" :percent="184"></TotalPercent>
                                <TotalPercent class="ms-3" :size="14" :amount="'15k'" :arrow="1" :symbol="'h'" :percent="184"></TotalPercent>                                                                
                            </div>                                                            
                        </div>
                        <div class="col-6">
                            <div class="d-flex justify-content-between">
                                <h3 class="font-20 mulish_600_normal m-0">Résultats</h3>
                                <h4 class="mb-0 ms-auto font-14 text-custom-success text-decoration-underline cursor-pointer"><em>Voir rapport</em></h4>
                            </div>
                            <h4 class="font-16 mulish_600_normal mt-3">Par commande</h4>
                            <div class="d-flex">
                                <div class="col-4">Commande XXX</div>
                                <TotalPercent class="col-4" :size="14" :amount="'38k'" :arrow="1" :symbol="''" :percent="184"></TotalPercent>
                                <TotalPercent class="ms-3" :size="14" :amount="'15k'" :arrow="1" :symbol="'h'" :percent="184"></TotalPercent>                                                                
                            </div>                                                            
                            <div class="d-flex">
                                <div class="col-4">Commande lerou</div>
                                <TotalPercent class="col-4" :size="14" :amount="'38k'" :arrow="1" :symbol="''" :percent="184"></TotalPercent>
                                <TotalPercent class="ms-3" :size="14" :amount="'15k'" :arrow="1" :symbol="'h'" :percent="184"></TotalPercent>                                                                
                            </div>                                                            
                            <div class="d-flex">
                                <div class="col-4">installation truc</div>
                                <TotalPercent class="col-4" :size="14" :amount="'38k'" :arrow="1" :symbol="''" :percent="184"></TotalPercent>
                                <TotalPercent class="ms-3" :size="14" :amount="'15k'" :arrow="1" :symbol="'h'" :percent="184"></TotalPercent>                                                                
                            </div>                                                           
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-5 ps-3">
                <div class="bg-gray rounded-3 p-3">
                    <div class="d-flex align-items-center">
                        <h3 class="font-20 mulish_600_normal m-0">Actions Commerciales</h3>
                        <TotalPercent class="ms-5" :amount="'239'" :arrow="1" :symbol="''" :percent="38"></TotalPercent>
                        <h4 class="mb-0 ms-auto font-14 text-custom-success text-decoration-underline cursor-pointer"><em>Voir rapport</em></h4>
                    </div>
                    <div class="d-flex">
                        <div class="col-6">
                            <h4 class="font-16 mulish_600_normal mt-3">Par Origine</h4>
                            <div class="d-flex">
                                <div class="col-4">Web</div>
                                <div class="col-8">
                                    <TotalPercent :size="14" :amount="'103'" :arrow="1" :symbol="''" :percent="184"></TotalPercent>
                                </div>
                            </div>
                            <div class="d-flex">
                                <div class="col-4">Emailling</div>
                                <div class="col-8">
                                    <TotalPercent :size="14" :amount="'36'" :arrow="1" :symbol="''" :percent="184"></TotalPercent>
                                </div>
                            </div>
                            <div class="d-flex">
                                <div class="col-4">Téléphone</div>
                                <div class="col-8">
                                    <TotalPercent :size="14" :amount="'33'" :arrow="1" :symbol="''" :percent="184"></TotalPercent>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <h4 class="font-16 mulish_600_normal mt-3">Par Type</h4>
                            <div class="d-flex">
                                <div class="col-4">Prospection</div>
                                <div class="col-8">
                                    <TotalPercent :size="14" :amount="'38'" :arrow="1" :symbol="''" :percent="184"></TotalPercent>
                                </div>
                            </div>                                                            
                        </div>
                    </div>
                </div>
                <div class="bg-gray rounded-3 p-3 mt-3">
                    <div class="d-flex align-items-center">
                        <h3 class="font-20 mulish_600_normal m-0">Devis</h3>
                        <TotalPercent class="ms-5" :amount="'1026'" :arrow="1" :symbol="''" :percent="38"></TotalPercent>
                        <h4 class="mb-0 ms-auto font-14 text-custom-success text-decoration-underline cursor-pointer"><em>Voir rapport</em></h4>
                    </div>
                    <div class="d-flex mt-3">
                        <div class="col-6">
                            <h4 class="font-16 mulish_600_normal mt-3">Par Statut</h4>
                            <div class="d-flex">
                                <div class="col-4">Delivery</div>
                                <TotalPercent :size="14" class="ms-5" :amount="'38k'" :arrow="1" :symbol="''" :percent="184"></TotalPercent>
                            </div>                                                            
                            <div class="d-flex">
                                <div class="col-4">Marylebone</div>
                                <TotalPercent :size="14" class="ms-5" :amount="'38k'" :arrow="1" :symbol="''" :percent="184"></TotalPercent>
                            </div>                                                            
                            <div class="d-flex">
                                <div class="col-4">Notting Hill</div>
                                <TotalPercent :size="14" class="ms-5" :amount="'38k'" :arrow="1" :symbol="''" :percent="184"></TotalPercent>
                            </div>                                                            
                            <div class="d-flex">
                                <div class="col-4">Chelsea</div>
                                <TotalPercent :size="14" class="ms-5" :amount="'38k'" :arrow="1" :symbol="''" :percent="184"></TotalPercent>
                            </div>                                                            
                            <div class="d-flex">
                                <div class="col-4">South Ken</div>
                                <TotalPercent :size="14" class="ms-5" :amount="'38k'" :arrow="1" :symbol="''" :percent="184"></TotalPercent>
                            </div>                                                            
                        </div>
                        <div class="col-6">
                            <h4 class="font-16 mulish_600_normal mt-3">Par Créateur / Responsable</h4>
                            <div class="d-flex">
                                <div class="col-4">Laurent</div>
                                <TotalPercent :size="14" class="ms-5" :amount="'38k'" :arrow="1" :symbol="''" :percent="184"></TotalPercent>
                            </div>                                                            
                            <div class="d-flex">
                                <div class="col-4">Franck</div>
                                <TotalPercent :size="14" class="ms-5" :amount="'38k'" :arrow="1" :symbol="''" :percent="184"></TotalPercent>
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
        let totalRoot = null;
        let totalChart = null;
        let xAxis = null;
        let yAxis = null;
        let series1 = null;
        let series2 = null;
        let series3 = null;
        let series4 = null;        
        let series5 = null;
        let series6 = null;
        let series7 = null;
        let series8 = null;        
        let series9 = null;        
        let series10 = null;        
        const salesByOriginTotal = ref(0);
        const salesByOriginTotalToCompare = ref(0);        
        const salesByClientTotal = ref(0);
        const salesByClientTotalToCompare = ref(0);        
        const today = new Date();
        const filterVal = ref({
            customFilter: 0,
            startDate: `${today.getFullYear()}-${today.getMonth()+1}-${today.getDate()}`,
            endDate:`${today.getFullYear()}-${today.getMonth()+1}-${today.getDate()}`,
            dateRangeType:'Today',
            compareMode: 'year',
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
        }
        watch(() => filterVal.value, (current_val, previous_val) => {
            store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'Loading data...']);
            axios.post('/statistique', current_val)
                .then((res) => {
                    originChartData.value = res.data.salesByOrigin;
                    clientChartData.value = res.data.salesByClient;                    
                    destroyChart();
                }).finally(()=>{
                    store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
            });
        });        
        let originChartRoot = null;
        let originChart = null;
        let originSeries= null;
        const originChartData = ref([
            { origin: "Fichier initial", amount: 0 },
            { origin: "Client affiliate", amount: 0 } 
        ]);
        const initOriginChart = ()=>{
            // Define data for sales by origin
            // Create root element
            if(originChartRoot == null){
                originChartRoot = am5.Root.new("saleByOrigin");
            }
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
                percent = `[#0f0][fontStyle: italic]${(salesByOriginTotal.value* 100 / salesByOriginTotalToCompare.value).toFixed(0)}%[/][/]`;
            }
            if(salesByOriginTotal.value < salesByOriginTotalToCompare.value && salesByOriginTotalToCompare.value != 0){
                percent = `[#f00][fontStyle: italic]${(salesByOriginTotal.value* 100 / salesByOriginTotalToCompare.value).toFixed(0)}%[/][/]`;
            }
            if(salesByOriginTotal.value > salesByOriginTotalToCompare.value && salesByOriginTotalToCompare.value == 0){
                percent = "--";
            }
            let originLabel = originSeries.children.push(am5.Label.new(originChartRoot, {
                text: "${valueSum.formatNumber('#,###.')}\n   "+ percent,
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
            { client: "Client xx", amount: 28630 },
            { client: "Client xx", amount: 10000 },
            { client: "Client xx", amount: 20000 },
            { client: "Client xx", amount: 15000 },
            { client: "Client xx", amount: 24000 },
            { client: "Autres Clients", amount: 26000 },
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
                percent = `[#0f0][fontStyle: italic]${(salesByClientTotal.value* 100 / salesByClientTotalToCompare.value).toFixed(0)}%[/][/]`;
            }
            if(salesByClientTotal.value < salesByClientTotalToCompare.value && salesByClientTotalToCompare.value != 0){
                percent = `[#f00][fontStyle: italic]${(salesByClientTotal.value* 100 / salesByClientTotalToCompare.value).toFixed(0)}%[/][/]`;
            }
            if(salesByClientTotal.value > salesByClientTotalToCompare.value && salesByClientTotalToCompare.value == 0){
                percent = "--";
            }            
            let clientLabel = clientSeries.children.push(am5.Label.new(clientChartRoot, {
                text: "${valueSum.formatNumber('#,###.')}\n   " + percent,
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

            addSeries(1);
            // Add scrollbar
            totalChart.set("scrollbarX", am5.Scrollbar.new(totalRoot, {
                orientation: "horizontal"
            }));

            totalChart.appear(1000, 100);
        }

        let date,value;
        const addSeries = (seriesIndex)=>{
            // Add series
            if(seriesIndex == 1){
                series1 = totalChart.series.push(am5xy.LineSeries.new(totalRoot, {
                    name: name,
                    xAxis: xAxis,
                    yAxis: yAxis,
                    valueYField: "value",
                    valueXField: "date",
                    tooltip: am5.Tooltip.new(totalRoot, {
                        labelText: "{valueY}"
                    })
                }));
                // Generate random data
                date = new Date();
                date.setHours(0, 0, 0, 0);
                value = 100;
    
                // Set data
                let data = generateDatas(20);
                series1.data.setAll(data);
                // Make stuff animate on load
                series1.appear(1000);
            }else if(seriesIndex == 2){
                series2 = totalChart.series.push(am5xy.LineSeries.new(totalRoot, {
                    name: name,
                    xAxis: xAxis,
                    yAxis: yAxis,
                    valueYField: "value",
                    valueXField: "date",
                    tooltip: am5.Tooltip.new(totalRoot, {
                        labelText: "{valueY}"
                    })
                }));
                // Generate random data
                date = new Date();
                date.setHours(0, 0, 0, 0);
                value = 100;
    
                // Set data
                let data = generateDatas(20);
                series2.data.setAll(data);
                // Make stuff animate on load
                series2.appear(1000);                
            }else if(seriesIndex == 3){
                series3 = totalChart.series.push(am5xy.LineSeries.new(totalRoot, {
                    name: name,
                    xAxis: xAxis,
                    yAxis: yAxis,
                    valueYField: "value",
                    valueXField: "date",
                    tooltip: am5.Tooltip.new(totalRoot, {
                        labelText: "{valueY}"
                    })
                }));
                // Generate random data
                date = new Date();
                date.setHours(0, 0, 0, 0);
                value = 100;
    
                // Set data
                let data = generateDatas(20);
                series3.data.setAll(data);
                // Make stuff animate on load
                series3.appear(1000);                
            }else if(seriesIndex == 4){
                series4 = totalChart.series.push(am5xy.LineSeries.new(totalRoot, {
                    name: name,
                    xAxis: xAxis,
                    yAxis: yAxis,
                    valueYField: "value",
                    valueXField: "date",
                    tooltip: am5.Tooltip.new(totalRoot, {
                        labelText: "{valueY}"
                    })
                }));
                // Generate random data
                date = new Date();
                date.setHours(0, 0, 0, 0);
                value = 100;
    
                // Set data
                let data = generateDatas(20);
                series4.data.setAll(data);
                // Make stuff animate on load
                series4.appear(1000);                
            }else if(seriesIndex == 5){
                series5 = totalChart.series.push(am5xy.LineSeries.new(totalRoot, {
                    name: name,
                    xAxis: xAxis,
                    yAxis: yAxis,
                    valueYField: "value",
                    valueXField: "date",
                    tooltip: am5.Tooltip.new(totalRoot, {
                        labelText: "{valueY}"
                    })
                }));
                // Generate random data
                date = new Date();
                date.setHours(0, 0, 0, 0);
                value = 100;
    
                // Set data
                let data = generateDatas(20);
                series5.data.setAll(data);
                // Make stuff animate on load
                series5.appear(1000);                
            }else if(seriesIndex == 6){
                series6 = totalChart.series.push(am5xy.LineSeries.new(totalRoot, {
                    name: name,
                    xAxis: xAxis,
                    yAxis: yAxis,
                    valueYField: "value",
                    valueXField: "date",
                    tooltip: am5.Tooltip.new(totalRoot, {
                        labelText: "{valueY}"
                    })
                }));
                // Generate random data
                date = new Date();
                date.setHours(0, 0, 0, 0);
                value = 100;
    
                // Set data
                let data = generateDatas(20);
                series6.data.setAll(data);
                // Make stuff animate on load
                series6.appear(1000);                
            }else if(seriesIndex == 7){
                series7 = totalChart.series.push(am5xy.LineSeries.new(totalRoot, {
                    name: name,
                    xAxis: xAxis,
                    yAxis: yAxis,
                    valueYField: "value",
                    valueXField: "date",
                    tooltip: am5.Tooltip.new(totalRoot, {
                        labelText: "{valueY}"
                    })
                }));
                // Generate random data
                date = new Date();
                date.setHours(0, 0, 0, 0);
                value = 100;
    
                // Set data
                let data = generateDatas(20);
                series7.data.setAll(data);
                // Make stuff animate on load
                series7.appear(1000);                
            }else if(seriesIndex == 8){
                series8 = totalChart.series.push(am5xy.LineSeries.new(totalRoot, {
                    name: name,
                    xAxis: xAxis,
                    yAxis: yAxis,
                    valueYField: "value",
                    valueXField: "date",
                    tooltip: am5.Tooltip.new(totalRoot, {
                        labelText: "{valueY}"
                    })
                }));
                // Generate random data
                date = new Date();
                date.setHours(0, 0, 0, 0);
                value = 100;
    
                // Set data
                let data = generateDatas(20);
                series8.data.setAll(data);
                // Make stuff animate on load
                series8.appear(1000);                
            }else if(seriesIndex == 9){
                series9 = totalChart.series.push(am5xy.LineSeries.new(totalRoot, {
                    name: name,
                    xAxis: xAxis,
                    yAxis: yAxis,
                    valueYField: "value",
                    valueXField: "date",
                    tooltip: am5.Tooltip.new(totalRoot, {
                        labelText: "{valueY}"
                    })
                }));
                // Generate random data
                date = new Date();
                date.setHours(0, 0, 0, 0);
                value = 100;
    
                // Set data
                let data = generateDatas(20);
                series9.data.setAll(data);
                // Make stuff animate on load
                series9.appear(1000);                
            }else{
                series10 = totalChart.series.push(am5xy.LineSeries.new(totalRoot, {
                    name: name,
                    xAxis: xAxis,
                    yAxis: yAxis,
                    valueYField: "value",
                    valueXField: "date",
                    tooltip: am5.Tooltip.new(totalRoot, {
                        labelText: "{valueY}"
                    })
                }));
                // Generate random data
                date = new Date();
                date.setHours(0, 0, 0, 0);
                value = 100;
    
                // Set data
                let data = generateDatas(20);
                series10.data.setAll(data);
                // Make stuff animate on load
                series10.appear(1000);                
            }
        }
        const removeSeries = (series)=>{
            totalChart.series.removeIndex(
                totalChart.series.indexOf(series)
            );
        }
        const generateData = ()=> {
            value = Math.round((Math.random() * 10 - 5) + value);
            am5.time.add(date, "day", 1);
            return {
                date: date.getTime(),
                value: value
            };
        }
        const generateDatas = (count)=> {
            let data = [];
            for (var i = 0; i < count; ++i) {
                data.push(generateData());
            }
            return data;
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
            clientChartData
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
    width: 75px;
    height: 95px;
    background: #E0E0E0;
    border-radius: 10px;
}
.total-chart{
    min-height: 400px;
}
</style>