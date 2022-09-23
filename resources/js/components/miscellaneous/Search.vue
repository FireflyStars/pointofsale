<template>

    <div class="search">

        <input 
            class="mulish_400_normal" 
            type="search" 
            placeholder="Rechercher..."
            v-model="search"
            @keyup.enter="submit"
            @focus="show=true"
            @keyup.esc="close"
        >

        <Icon 
            class="icon"
            name="spinner"
            v-show="loading"
        />
        
    </div>

    <search-results 
        :show="show"
        @close="show=false"
    />

</template>

<script>
import { useStore } from 'vuex'
import { ref, computed } from 'vue'
import {
    SET_SEARCH,
    SEARCH_MODULE,
    GET_SEARCH_RESULTS
}
 from '../../store/types/types'
import searchResults from './search-results/index'

export default {
    name: "Search",
}
</script>

<script setup>

    import lodash from 'lodash'
    const store = useStore()
    const show = ref(false)

    const search = computed({
        set(value) {
            if(value == '') show.value = false
            store.commit(`${SEARCH_MODULE}/${SET_SEARCH}`, value)
        },
        get() {
            return store.getters[`${SEARCH_MODULE}/search`]
        }
    })

    const loading = computed(() => {
        const { id, value } = store.getters[`${SEARCH_MODULE}/loading`]
        return id == 'search' && value
    })

    const close = () => {
        show.value = false
        document.querySelector('input[type="search"]').blur()
    }
    
    const submit = () => {
        if(loading.value) return
        show.value = true
        store.dispatch(`${[SEARCH_MODULE]}/${[GET_SEARCH_RESULTS]}`)
    }

</script>

<style scoped lang="scss">

.search {

    float: left;
    margin-right: 19px;
    width: 217px;
    position: relative;

    input[type="search"] {
        box-sizing: border-box;
        border:1px solid #FFF;
        border-radius: 10px;
        background: transparent;
        height: 29px;
        font-size: 12px;
        width: 100%;
        color:#FFF;
        padding-left:12px;
        letter-spacing: 0.2px;
        background: url("../../images/search-icon.svg") no-repeat right 14px center;
        &:focus,
        &:active {
            outline: 0;
        }
    }

    .icon {
        position: absolute;
        top: 6px;
        right: 35px;
        color: white;
    }

}

</style>