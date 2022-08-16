<template>
    <div class="d-flex filter">
        <div class="width-fit-content">
            <button class="btn rounded-2 bg-white body_small_bold py-2 d-flex align-items-center" :class="{active:showfilter,hasfilters:hasActiveFilters}" @click="toggleShow">
                <!-- <i class="bi bi-calendar me-2"></i> -->
                <svg class="me-2" width="18" height="20" viewBox="0 0 18 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M16.2266 2.49589H1.77342C1.2837 2.49589 0.886708 2.86907 0.886708 3.32942V18.333C0.886708 18.7933 1.2837 19.1665 1.77342 19.1665H16.2266C16.7163 19.1665 17.1133 18.7933 17.1133 18.333V3.32942C17.1133 2.86907 16.7163 2.49589 16.2266 2.49589ZM1.77342 1.66235C0.793985 1.66235 0 2.40872 0 3.32942V18.333C0 19.2537 0.793986 20.0001 1.77342 20.0001H16.2266C17.206 20.0001 18 19.2537 18 18.333V3.32942C18 2.40872 17.206 1.66235 16.2266 1.66235H1.77342Z" fill="black"/>
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M13.7745 0.0066459C14.0193 0.010324 14.215 0.176235 14.2116 0.377218L14.1535 3.79104C14.1501 3.99202 13.9489 4.15197 13.7041 4.14829C13.4592 4.14462 13.2635 3.9787 13.267 3.77772L13.325 0.363898C13.3284 0.162915 13.5296 0.00296781 13.7745 0.0066459Z" fill="black"/>
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M4.04157 0.0221488C4.28639 0.0258269 4.4821 0.191129 4.4787 0.391362L4.42088 3.79244C4.41747 3.99267 4.21624 4.15201 3.97142 4.14833C3.72659 4.14465 3.53088 3.97935 3.53428 3.77912L3.5921 0.378042C3.59551 0.17781 3.79674 0.0184707 4.04157 0.0221488Z" fill="black"/>
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M13.7446 5.04121C13.9895 5.04121 14.188 4.85462 14.188 4.62444C14.188 4.39427 13.9895 4.20768 13.7446 4.20768C13.4998 4.20768 13.3013 4.39427 13.3013 4.62444C13.3013 4.85462 13.4998 5.04121 13.7446 5.04121ZM13.7446 5.87474C14.4792 5.87474 15.0747 5.31496 15.0747 4.62444C15.0747 3.93392 14.4792 3.37415 13.7446 3.37415C13.01 3.37415 12.4146 3.93392 12.4146 4.62444C12.4146 5.31496 13.01 5.87474 13.7446 5.87474Z" fill="black"/>
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M3.99754 5.05061C4.2424 5.05061 4.4409 4.86402 4.4409 4.63384C4.4409 4.40367 4.2424 4.21708 3.99754 4.21708C3.75268 4.21708 3.55419 4.40367 3.55419 4.63384C3.55419 4.86402 3.75268 5.05061 3.99754 5.05061ZM3.99754 5.88414C4.73212 5.88414 5.3276 5.32436 5.3276 4.63384C5.3276 3.94332 4.73212 3.38354 3.99754 3.38354C3.26297 3.38354 2.66748 3.94332 2.66748 4.63384C2.66748 5.32436 3.26297 5.88414 3.99754 5.88414Z" fill="black"/>
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M2.66461 8.82045C2.66323 8.59028 2.8606 8.40263 3.10546 8.40133L14.6258 8.34015C14.8707 8.33885 15.0703 8.52438 15.0717 8.75455C15.0731 8.98472 14.8757 9.17237 14.6308 9.17367L3.11047 9.23485C2.86561 9.23615 2.666 9.05062 2.66461 8.82045Z" fill="black"/>
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M7.68018 12.1254C7.68018 11.8952 7.87867 11.7086 8.12353 11.7086H9.87695C10.1218 11.7086 10.3203 11.8952 10.3203 12.1254C10.3203 12.3556 10.1218 12.5422 9.87695 12.5422H8.12353C7.87867 12.5422 7.68018 12.3556 7.68018 12.1254Z" fill="black"/>
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M12.4922 12.1076C12.4922 11.8774 12.6907 11.6908 12.9355 11.6908H14.689C14.9338 11.6908 15.1323 11.8774 15.1323 12.1076C15.1323 12.3377 14.9338 12.5243 14.689 12.5243H12.9355C12.6907 12.5243 12.4922 12.3377 12.4922 12.1076Z" fill="black"/>
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M12.4146 17.0855C12.4146 16.8553 12.613 16.6687 12.8579 16.6687H14.6113C14.8562 16.6687 15.0547 16.8553 15.0547 17.0855C15.0547 17.3156 14.8562 17.5022 14.6113 17.5022H12.8579C12.613 17.5022 12.4146 17.3156 12.4146 17.0855Z" fill="black"/>
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M7.68018 17.1032C7.68018 16.873 7.87867 16.6864 8.12353 16.6864H9.87695C10.1218 16.6864 10.3203 16.873 10.3203 17.1032C10.3203 17.3333 10.1218 17.5199 9.87695 17.5199H8.12353C7.87867 17.5199 7.68018 17.3333 7.68018 17.1032Z" fill="black"/>
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M2.66748 17.0855C2.66748 16.8553 2.86598 16.6687 3.11083 16.6687H4.86425C5.10911 16.6687 5.3076 16.8553 5.3076 17.0855C5.3076 17.3156 5.10911 17.5022 4.86425 17.5022H3.11083C2.86598 17.5022 2.66748 17.3156 2.66748 17.0855Z" fill="black"/>
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M2.6875 12.1076C2.6875 11.8774 2.886 11.6908 3.13085 11.6908H4.88427C5.12913 11.6908 5.32762 11.8774 5.32762 12.1076C5.32762 12.3377 5.12913 12.5243 4.88427 12.5243H3.13085C2.886 12.5243 2.6875 12.3377 2.6875 12.1076Z" fill="black"/>
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M12.413 14.6066C12.4122 14.3764 12.6101 14.1892 12.855 14.1885L14.6281 14.1834C14.8729 14.1827 15.072 14.3687 15.0728 14.5988C15.0736 14.829 14.8757 15.0162 14.6308 15.0169L12.8577 15.0221C12.6129 15.0228 12.4137 14.8368 12.413 14.6066Z" fill="black"/>
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M7.67709 14.632C7.67556 14.4018 7.87281 14.214 8.11766 14.2126L9.8711 14.2023C10.1159 14.2009 10.3157 14.3863 10.3172 14.6165C10.3187 14.8466 10.1215 15.0344 9.87663 15.0358L8.1232 15.0461C7.87835 15.0476 7.67862 14.8621 7.67709 14.632Z" fill="black"/>
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M2.66748 14.6143C2.66748 14.3841 2.86598 14.1975 3.11083 14.1975H4.9419C5.18676 14.1975 5.38526 14.3841 5.38526 14.6143C5.38526 14.8444 5.18676 15.031 4.9419 15.031H3.11083C2.86598 15.031 2.66748 14.8444 2.66748 14.6143Z" fill="black"/>
                </svg>
                {{customFilter ? 'Personnalisé' : selectedPeriod }}
            </button>
        </div>
        <div class="d-flex align-items-center custom-filter-text" v-if="customFilter">
            <p :class="{ 'ps-3': customFilterText != ''}">{{customFilterText}}</p>
        </div>
    </div>
    <transition name="trans-filter" >
    <div class="filters position-absolute p-3" v-if="showfilter">
        <div class="row">
            <div class="col-6">
                <h5>Période Principale</h5>
                <div class="row mt-4">
                    <switch-btn v-model="customFilter" label-left="Filtre" label-right="" ></switch-btn>
                </div>
                <div class="row mt-4"  v-if="!customFilter">
                    <div class="col-10">
                        <select-options v-model="selectedValue" :options="dateRange" name="select-date-range"  classnames="black-border"></select-options>
                    </div>
                </div>
                <div v-if="customFilter">
                    <div class="row mt-4">
                        <div class="col-3">
                            <date-picker v-model="startDate" @update:modelValue="newValue => startDate = newValue" name="start_date" :droppos="{top:'auto',right:'auto',bottom:'auto',left:'0',transformOrigin:'top right'}" label="Début" :disabled-from-date="startDisabledtodate"></date-picker>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-3">
                            <date-picker v-model="endDate" @update:modelValue="newValue => endDate = newValue" name="end_date" :droppos="{top:'auto',right:'auto',bottom:'auto',left:'0',transformOrigin:'top right'}" label="Fin" :disabled-from-date="endDisabledtodate"></date-picker>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <h5>Période Prècedente</h5>
                <div class="row mt-4">
                    <switch-btn v-model="compareCustomFilter" label-left="Filtre" label-right="" ></switch-btn>
                </div>
                <div class="row mt-4"  v-if="!compareCustomFilter">
                    <div class="col-10">
                        <select-options v-model="selectedCompareMode" :options="compareMode" name="compare_mode"  classnames="black-border"></select-options>
                    </div>
                </div>
                <div v-if="compareCustomFilter">
                    <div class="row mt-4">
                        <div class="col-3">
                            <date-picker v-model="compareStartDate" @update:modelValue="newValue => compareStartDate = newValue" name="pre_start_date" :droppos="{top:'auto',right:'0',bottom:'auto',left:'auto',transformOrigin:'top right'}" label="Début" :disabled-from-date="startDisabledtodate"></date-picker>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-3">
                            <date-picker v-model="compareEndDate" @update:modelValue="newValue => compareEndDate = newValue" name="pre_end_date" :droppos="{top:'auto',right:'0',bottom:'auto',left:'auto',transformOrigin:'top right'}" label="Fin" :disabled-from-date="endDisabledtodate"></date-picker>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row buttons my-4">
            <div class="col text-center"><button class="btn btn-link  body_regular" @click="cancel">ANNULER</button></div>
            <div class="col text-center"><button class="btn btn-dark body_medium" @click="applyFilter">VALIDER</button></div>
        </div>
    </div>
    </transition>
</template>

<script>
    import {ref,computed, watch} from 'vue';
    import CheckBox from '../miscellaneous/CheckBox';
    import {useStore} from 'vuex';
    import SelectOptions from '../miscellaneous/SelectOptions';
    import DatePicker from '../miscellaneous/DatePicker';
    import SwitchBtn from '../miscellaneous/SwitchBtn';
    // import vClickOutside from 'click-outside-vue3';
    export default {
        name: "StatisticsFilters",
        directives: {
            // clickOutside: vClickOutside.directive
        },           
        props:['filterVal'],
        emits: ['update:filterVal'],
        components:{CheckBox,SelectOptions,DatePicker,SwitchBtn},
         setup(props,context){
            let monthNames =["janv","févr","mars","avril",
                      "mai","juin","juil","août",
                      "sept", "oct","nov","déc"];
    
            const current = new Date();
            const currentDate = `${current.getFullYear()}-${current.getMonth()+1}-${current.getDate()}`;
            const compareDate = `${current.getFullYear()-1}-${current.getMonth()+1}-${current.getDate()}`;
            const showfilter=ref(false);
            const customFilter=ref(false);
            const selectedValue =ref('Last Month');
            const selectedPeriod =ref('Le mois dernier');
            const startDate=ref(currentDate);
            const endDate=ref(currentDate);
            
            const compareCustomFilter=ref(false);
            const selectedCompareMode =ref('month');
            
            const compareStartDate=ref(compareDate);
            const compareEndDate=ref(compareDate);

            const startDisabledtodate = ref(currentDate);
            const endDisabledtodate = ref(currentDate);
            const customFilterText = ref('');
            const store=useStore();
            const toggleShow=(()=>showfilter.value=!showfilter.value);

            const hasActiveFilters=computed(()=>{
                return false;
            });
            watch(()=> selectedValue.value, (cur_val, pre_val)=>{
                selectedPeriod.value = dateRange.value.find((item)=>{
                    return item.value == cur_val
                }).display;
            })
            const dateRange = ref([]);
                dateRange.value = [
                    { value: "Today", display: "Aujourd\'hui" },
                    { value: "Yesterday", display: "Hier" },
                    { value: "Last 7 days", display: "Les 7 derniers jours" },
                    { value: "Last 90 days", display: "90 derniers jours" },
                    { value: "Last Month", display: "Le mois dernier" },
                    { value: "Year to date", display: "Année à ce jour" },
                    { value: "4th Quarter", display: "4e trimestre ("+`${current.getFullYear()-1}`+")" },
                    { value: "3rd Quarter", display: "3e trimestre ("+`${current.getFullYear()-1}`+")" },
                    { value: "2nd Quarter", display: "2e trimestre ("+`${current.getFullYear()-1}`+")" },
                    { value: "1st Quarter", display: "1er trimestre ("+`${current.getFullYear()-1}`+")" }
                ];
            const compareMode = ref([]);
            compareMode.value = [
                { value: 'year', display: 'Année' },
                { value: 'month', display: 'Mois' }
            ];
            function applyFilter() {
                const data = {
                    customFilter: customFilter.value,
                    startDate: startDate.value,
                    endDate: endDate.value,
                    dateRangeType: selectedValue.value,
                    compareMode: selectedCompareMode.value,
                    compareCustomFilter: compareCustomFilter.value,
                    compareStartDate: compareStartDate.value,
                    compareEndDate: compareEndDate.value,

                };
                customFilterText.value=`Comparé à partir de ${new Date(startDate.value).getDate()} ${monthNames[new Date(startDate.value).getMonth()]}, ${new Date(startDate.value).getFullYear()} À ${new Date(endDate.value).getDate()} ${monthNames[new Date(endDate.value).getMonth()]}, ${new Date(endDate.value).getFullYear()}`;
                context.emit("update:filterVal", data);
                toggleShow();
            }

            function cancel() {
                toggleShow();
            }

            return {
                toggleShow,
                applyFilter,
                cancel,
                dateRange,
                showfilter,
                customFilter,
                selectedPeriod,
                hasActiveFilters,
                selectedValue,
                startDisabledtodate,
                startDate,
                endDate,
                endDisabledtodate,
                customFilterText,
                compareCustomFilter,
                compareStartDate,
                compareEndDate,
                compareMode,
                selectedCompareMode
            }
        },
        methods:{
            onClickOutside (event) {
                this.showfilter = false;
            }
        }
    }
</script>

<style scoped>
    .filter{
        position: absolute;
        top: -45px;
        right: 0;
    }
    button:active,
    button:focus{
        outline: none;
        box-shadow: none;
    }
    .filters .buttons button{
        width: 130px;
        height: 56px;
    }
    .filter.hasfilters:not(:hover){
        background:#42A71E ;
        color:#FFF;
        border-color: #42A71E;
    }
    .filter.hasfilters.active{
        background: #47454B;
        border-color: #47454B;
    }

    .filter:focus{
        box-shadow: none;
    }
    .filter.hasfilters:not(:hover) span,.filter.hasfilters:not(:hover) span:before,.filter.hasfilters:not(:hover) span:after{
        background: #FFF;
    }
    .filter:hover span, .filter:hover span:before, .filter:hover span:after,
    .filter.active span, .filter.active span:before, .filter.active span:after
    {
        background: #FFF;
    }
    .filters{
        background: #F8F8F8;
        box-shadow: 0px 4px 16px rgba(0, 0, 0, 0.12);
        width:444px;
        min-height:250px;
        z-index: 2;
        transform-origin: top center;
        padding:0 46px;
        right: 0;
    }
    .trans-filter-enter-from{
        opacity: 0;
        transform: scale(0.6);
    }
    .trans-filter-enter-to{
        opacity: 1;
        transform: scale(1);
    }
    .trans-filter-enter-active{
        transition: all ease 0.2s;
    }
    .trans-filter-leave-from{
        opacity: 1;
        transform: scale(1);
    }
    .trans-filter-leave-to{
        opacity: 0;
        transform: scale(0.6);
    }
    .trans-filter-leave-active{
        transition: all ease 0.2s;
    }

    .select{
        background: #FFFFFF;
        border: 1px solid #000000;;
        box-sizing: border-box;
        border-radius: 5px;
        padding: 0 36px 0 16px;
        height: 40px;
        font-size: 16px;
        display: flex;
        cursor: pointer;
        align-items: center;
        position: relative;
        margin-bottom: 30px;

    }
    .select.active{
        margin-right: -2px;
        margin-left: -2px;
        background: #EEEEEE;
        border: 2px solid #000000;
    }
    .select-options{
        position: absolute;
        width: 100%;
        left: 0;
        top: 46px;
        background: #FFF;
        box-shadow: inset 0px 0px 4px rgba(37, 40, 43, 0.12);
        max-height: 168px;
        z-index: 1;
        overflow-y: auto;
        transform-origin: top center;
    }
    .select:after,.select:before{
        content: " ";
        height: 3px;
        display: block;
        width: 13px;
        background: #868686;
        border-radius: 10px;
        transform: rotate(40deg);
        right:22px;
        position: absolute;
    }
    .select.active:after,.select.active:before{
    background: #000000;
    }
    .select:after{
        transform: rotate(-40deg);
        right: 13px;
    }
    .custom-filter-text{
        font-size: 14px;
    }
    .width-fit-content{
        width: fit-content !important;
    }
</style>