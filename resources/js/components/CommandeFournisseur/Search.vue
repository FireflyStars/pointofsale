<template>
    <div class="dp noselect position-relative">

        <label v-if="!showSearch && label" class="select-label" :class="{ disabled:disabled == true }">{{ label }}</label>

        <div  v-if="!showSearch" class="position-relative">
            <input type="text" :placeholder="placeholder" v-model="search" @keyup.prevent="submit"/>
             <span v-if="showbutton" @click='clearSearch' class="position-absolute"><i class="icon-close"></i></span>
        </div>

        <transition name="trans-search">

            <div class="row search" v-if="showSearch">

                <label class="select-label" :class="{ disabled:disabled == true }" v-if="label">{{ label }}</label>
                
                <div class="input_search">
                    <div  v-if="showSearch" class="position-relative input_search">
                        <input type="text"  ref="inputsearch" :placeholder="placeholder" v-model="search" @keyup.prevent="submit"/>
                        <span v-if="showbutton" @click='clearSearch' class="position-absolute"><i class="icon-close"></i></span>
                    </div>
                    <section class="nodata p-2" v-if="Fournisseurs.length == 0">
                        <p>nous n'avons trouvé aucun client.</p>
                    </section>
                </div>

                <ul class="" v-if="Fournisseurs.length > 0" >
                    <li v-for ="fournisseur in Fournisseurs " :key="fournisseur">
                        <div class="container d-block" @click="selectFournisseur(fournisseur)">
                            <div class="d-flex">
                                <div class="col-6 body_medium text-capitalize text-nowrap">
                                    {{ fournisseur.name }}
                                </div>
                                <div class="col-6">
                                    <b class="email body_regular">{{ fournisseur.email ? fournisseur.email.toLowerCase() : ''}}</b>
                                </div>
                            </div>
                            <div class="d-flex mt-2">
                                <div class="col-4 pe-2 eclipe-item">{{ fournisseur.contactname }}</div>
                                <div class="col-4 pe-2 eclipe-item">{{ fournisseur.phone }}</div>
                                <div class="col-4 eclipe-item">&euro;{{ fournisseur.total }}</div>
                            </div>
                        </div>
                    </li>
                </ul>

                <div class="col">
                    <span v-if="CountFournisseur > 0" class="show-more body_medium" @click="loadMore()">
                        {{CountFournisseur}} more Fournisseurs 
                    </span>
                </div>

            </div>

        </transition>
    </div>

</template>

<script setup>

    import { ref, nextTick, computed, watchEffect } from 'vue'
    import {
        COMMANDE_FOURNISSEUR_LIST_MODULE,
        FOURNISSEUR_SEARCH_LOAD_LIST,
        TOASTER_MODULE,
        TOASTER_MESSAGE
    } from "../../store/types/types"
    import { useStore } from 'vuex'

    const emit = defineEmits(['selected'])

    const props = defineProps({
        droppos: Object,
        label: String,
        disabled: Boolean,
        hint: String,
        placeholder: {
            required: false,
            type: String,
            default:"Recherche..."
        },
    })

    const search =ref('')
    const store =useStore()
    const timeout =ref('')
    const showSearch=ref(false)
    const showbutton = ref(false)
    const show_loader= ref(false)
    const inputsearch=ref(null)

    function clearSearch() {
        search.value = null
        showSearch.value = false
        showbutton.value = false
        show_loader.value= false
    }

    const featureunavailable=((feature) => {
        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, { message: feature+' feature not yet implemented.', ttl:5, type: 'success'});
    })

    function submit(e) {
        clearTimeout(timeout.value);
        timeout.value = setTimeout(function() {
            show_loader.value = true;
            nextTick(() => {
                store.dispatch(`${COMMANDE_FOURNISSEUR_LIST_MODULE}${FOURNISSEUR_SEARCH_LOAD_LIST}`, { showmore: 1, search: e.target.value })
                .then((response)=>{
                    if(e.target.value) {
                        showSearch.value = true
                        showbutton.value = true
                    } else {
                        showSearch.value = false
                        show_loader.value= false
                    }
                })
                .catch((error)=>{})
            });
        }
        , 300)
    }

    function loadMore(){
        store.dispatch(`${COMMANDE_FOURNISSEUR_LIST_MODULE}${FOURNISSEUR_SEARCH_LOAD_LIST}`, { showmore:1, search:search.value })
        .finally(()=>{})
    }

    const selectFournisseur = (customer) => {
        emit("selected", customer)
        showSearch.value = false
    }

    const Fournisseurs = computed(()=>{
        return store.getters[`${COMMANDE_FOURNISSEUR_LIST_MODULE}searchFournisseurs`]
    })

    const CountFournisseur = computed(()=>{
        return store.getters[`${COMMANDE_FOURNISSEUR_LIST_MODULE}countFournisseurs`]
    })

    watchEffect(() => {
        if(inputsearch.value!=null)
            inputsearch.value.focus()
    }, {
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
        height: 550px;
        overflow: auto;
    }
    li{
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
        font-size: 14px;
        line-height: 14px;
    }
    .eclipe-item{
        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: hidden;        
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
    .nodata{
    display: flex;
    justify-content: center;
    }

</style>