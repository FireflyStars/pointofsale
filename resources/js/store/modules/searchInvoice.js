import {
    INVOICE_SEARCH_LOAD_LIST,
    MASTER_SEARCH_LOAD_LIST,
    INVOICE_SET_SEARCH_LIST,
    INVOICE_GET_SEARCH_LIST,
    INVOICE_SET_SEARCH_COUNT,
    INVOICE_GET_SEARCH_COUNT,
    INVOICE_SET_LOADER,
} from "../types/types";
import axios from 'axios';

export const searchInvoice = {

    namespaced: true,
  
    state: {
        loader: '',
        countinvoices: '',
        listsearchinvoices: [],
    },

    getters: {

        listsearchinvoices: state => state.listsearchinvoices,
        countinvoices: state => state.countinvoices,

    },

    mutations: {
        
        [INVOICE_SET_LOADER]: (state, payload) => state.loader = payload,
        [INVOICE_SET_SEARCH_LIST]: (state, payload) =>state.listsearchinvoices = payload,
        [INVOICE_SET_SEARCH_COUNT]: (state, payload) =>state.countinvoices = payload,

    },

    actions: {

        [INVOICE_SEARCH_LOAD_LIST]:async ({commit,state}, payload ) => {

            return axios.post('/search-invoice', payload)
            .then((response) => {
                if(response.data != null) {
                    commit(INVOICE_SET_SEARCH_LIST, response.data.data)
                    commit(INVOICE_SET_SEARCH_COUNT, (response.data.total - 5))
                }
                return Promise.resolve(response)
            })
            .catch((error)=>{
                return Promise.reject(error)
            }).finally(()=>{

            })

        },
        
        [MASTER_SEARCH_LOAD_LIST]:async ({commit,state}, payload ) => {

            return axios.post('/search-master', payload)
            .then( (response)=>{
                if(response.data!=null){
                    commit(INVOICE_SET_SEARCH_LIST, response.data.data);
                    commit(INVOICE_SET_SEARCH_COUNT, ( response.data.total - 5 ) )
                }
                return Promise.resolve(response);
            })
            .catch((error)=>{
                return Promise.reject(error);
            }).finally(()=>{

            })

        }

    }
    
}