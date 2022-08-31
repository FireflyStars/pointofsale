<template>
    <div class="dp noselect position-relative">
        
        <label 
            v-if="!showSearch && label" 
            class="select-label" 
            :class="{ disabled:disabled == true }"
        >
            {{ label }}
        </label>
        
        <div  v-if="!showSearch" class="position-relative">
            <input type="text"  placeholder="Type name..." v-model="search" @keyup.prevent="submit"/> 
            <span v-if="showbutton" @click='clearSearch' class="position-absolute">
                <i class="icon-close"></i>
            </span>
        </div>

        <transition name="trans-search">

            <div class="row search" v-if="showSearch">
                
                <label class="select-label" :class="{ disabled: disabled == true }" v-if="label">
                    {{ label }}
                </label>
                
                <div class="input_search">
                    
                    <div v-if="showSearch" class="position-relative input_search">
                        <input type="text"  ref="inputsearch" placeholder="Type name..." v-model="search" @keyup.prevent="submit"/>
                        <span v-if="showbutton" @click='clearSearch' class="position-absolute"><i class="icon-close"></i></span>
                    </div>
                    <section class="nodata p-2" v-if="invoices.length == 0">
                        <p>nous n'avons trouvé aucun client.</p>
                    </section>

                </div>

                <ul class="list-group list-group-flush" v-if="invoices.length > 0">

                    <li v-for="invoice in invoices" :key="invoice">
                        
                        <div class="container">
                            
                            <div class="row" @click="selectInvoice(invoice)">
                                
                                <div class="col" style="padding:0">

                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="body_medium text-capitalize">{{ invoice?.reference }}</span>
                                        <span class="body_medium text-capitalize">{{ invoice?.montant }} &euro;</span>
                                        <span class="body_medium text-capitalize">
                                            {{ invoice?.dateecheance ? moment(invoice.dateecheance).format('DD/MM/YYYY') : '' }}
                                        </span>
                                    </div>

                                    <div>
                                        <div v-if="invoice.raisonsociale !=''">
                                            <div class="d-flex align-items-center gap-4">
                                                <b class ="body_regular">{{ invoice?.raisonsociale }}</b>
                                                <b class ="body_regular">{{ invoice.email?.toLowerCase() }}</b>
                                            </div>
                                        </div>
                                        <div v-else>
                                            <div class="phone body_small">--</div>
                                        </div>
                                    </div>
                                </div>   
                                

                            </div>

                        </div>

                    </li>

                </ul>

                <div class="col">
                    
                    <span 
                        v-if="countInvoices > 0" 
                        class="show-more body_medium" 
                        @click="loadMore()"
                    >
                        {{ countInvoices }} more invoices
                    </span>

                </div>
            
            </div>
        </transition>
    </div>
 
</template>

<script setup>

    import moment from 'moment'
    import lodash from 'lodash'
    import { ref, nextTick, computed, watchEffect } from 'vue'
    import {
        INVOICELIST_MODULE,
        INVOICE_SEARCH_LOAD_LIST,
        TOASTER_MODULE,
        TOASTER_MESSAGE
    } from "../../store/types/types"
    import { useStore } from 'vuex'
    
    const emit = defineEmits(["selected"])
    
    const props = defineProps({
        droppos: Object,
        label: String,
        disabled: Boolean,
        hint: String,
        customerId: Number
    })

    const search =ref('');
    const store =useStore();
    const timeout =ref('');
    const showSearch=ref(false);
    const showbutton = ref(false);
    const show_loader= ref(false);
    const inputsearch=ref(null);
     
    function clearSearch() {
        search.value = null;
        showSearch.value = false;
        showbutton.value = false;
        show_loader.value= false;
    }
    
    const featureunavailable = ((feature) => {
        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, { message: feature + ' feature not yet implemented.', ttl: 5, type: 'success' })
    })

    function submit() { 
        
        show_loader.value = true;

        clearTimeout(timeout.value)

        timeout.value = setTimeout(function() {
        
            store.dispatch(`${INVOICELIST_MODULE}${INVOICE_SEARCH_LOAD_LIST}`, { 
                showmore: 1, 
                search: search.value,
                customer_id: props.customerId 
            })
            .then((response) => {
                
                if(search.value) {
                    showSearch.value = true;
                    showbutton.value = true;
                } else {
                    showSearch.value = false;
                    show_loader.value= false;
                }

            })
            .catch((error)=>{})

        }, 300)


    }
 
    function loadMore() {
        store.dispatch(`${INVOICELIST_MODULE}${INVOICE_SEARCH_LOAD_LIST}`,{ showmore:1, search:search.value})
        .finally(()=>{});
    }

    const selectInvoice = (invoice) => {
        emit("selected", invoice)
        showSearch.value = false
    }

    const invoices = computed(() => {
        return store.getters[`${INVOICELIST_MODULE}listsearchinvoices`];
    })
    
    const countInvoices = computed(() => {
        return store.getters[`${INVOICELIST_MODULE}countinvoices`];
    })
    
    watchEffect(() => {
        if(inputsearch.value!=null)
            inputsearch.value.focus();
    },{
        flush: 'post'
    })

</script>

<style scoped>
  .trans-search-enter-from{
        opacity: 0;
        transform: scale(0.6);
    }
    .trans-search-enter-to{
        opacity: 1;
        transform: scale(1);
    }
    .trans-search-enter-active{
        transition: all ease 0.2s;
    }
    .trans-search-leave-from{
        opacity: 1;
        transform: scale(1);
    }
    .trans-search-leave-to{
        opacity: 0;
        transform: scale(0.6);
    }
    .trans-search-leave-active{
        transition: all ease 0.2s;
    }

  .select-label{
    font-family: Gotham Rounded;
    font-style: normal;
    font-weight: normal;
    font-size: 16px;
    line-height: 24px;
    margin-bottom: 6px;
  }
  .search{
    position: absolute;
    background: #fff;
    width: 100%;
    height: auto;
    box-shadow: 0px 4px 16px rgba(0, 0, 0, 0.12);
    border-radius: 5px;
    padding: 20px 32px 40px 32px !important;
    margin: 0;
      top:-10px;
  }
  .input_search{
      padding:0 !important;
  }
   .body_medium{
      color:#000000;
    }
    .body_regular{
      color: #868686;
    }
    .show-more{
        padding-top: 34px;
        justify-content: center;
        align-items: center;
        display: flex;
        color: #868686;
        line-height: 22px;
        cursor: pointer;
    }
    input{
        border: 1px solid transparent !important;
        box-sizing: border-box;
        border-radius: 5px;
        background: #F8F8F8 url(/images/search_gray.svg?9dab47b…) no-repeat center left 11px;
        height: 40px;
        line-height: 40px;
        padding-left: 45px;
        vertical-align: middle;
        font-size: 16px;
        padding-right: 30px;
        width: 100%;
        height: 40px;
        padding-right: 50px;
        margin-bottom: 12px;
        font-family: 'Mulish Regular';
        font-weight: normal;
    }
    input:focus-visible{
       outline:2px #000000 solid;
    }
    ul{ 
        border-radius: 5px;
        list-style-type:none;
        padding:0px;
        height: fit-content;
    }
    li{
        height: 79px;
        margin-bottom: 6px;
        position: relative;
    }
    .email{
        width : 100%;
        overflow:hidden;
        display:inline-block;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .container{
        background: #F8F8F8;
        margin: 0;
        height: 100%;
        padding-top: 15px;
        padding-bottom: 13px;
        justify-content: center;
        display: flex;
        align-items: center;
        padding-left: 21px;
        padding-right: 11px;
    }
    .container .row{
        width: 100%;
        margin: 0;
        padding: 0;
        justify-content: center;
        display: flex;
        align-items: flex-start;
        cursor: pointer;
        line-height: 22px;
    }

    input::placeholder {
    position: static;
    width: 316px;
    height: 20px;
    left: 34px;
    top: 4px;
    font-family: 'Mulish Regular';
    font-style: normal;
    font-weight: normal;
    font-size: 14px;
    line-height: 140%;
    flex: none;
    order: 1;
    align-self: flex-end;
    flex-grow: 0;
    margin: 0px 10px;
    }
    .position-absolute{
        height: 20px;
        width: 20px;
        right: 20px;
        top: 10px;
    }
    title{
        padding-bottom: 58px;
        padding-top: 18px;
        padding-left: 15px;
        width: 271px;
    }
    .tag.b2c{
    color: #9E44F2;
    background: rgba(234, 214, 247, 0.7);
    border-radius: 70px;
    height: 22px;
    width: 77px !important;
    }
    .tag.b2b {
    color: #4E58E7;
    background: rgba(212, 221, 247, 0.7);
    border-radius: 70px;
    height: 22px;
    width: 77px !important;
    }
    .nodata{
    display: flex;
    justify-content: center;
    }
  
</style>