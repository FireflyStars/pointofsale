import {
    CUSTOMER_SEARCH_LOAD_LIST,
    MASTER_SEARCH_LOAD_LIST,
    CUSTOMER_SET_SEARCH_LIST,
    CUSTOMER_GET_SEARCH_LIST,
    CUSTOMER_SET_SEARCH_COUNT,
    CUSTOMER_GET_SEARCH_COUNT,
    CUSTOMER_SET_LOADER,
} from "../types/types";
import axios from 'axios';

export const search= {
    namespaced:true,
  
    state:{
        loader:'',
        countcustomers:'',
        listsearchcustomers:[],
    },
    mutations: {
        [CUSTOMER_SET_LOADER]: (state, payload) => state.loader = payload,
        [CUSTOMER_SET_SEARCH_LIST]: (state,payload) =>state.listsearchcustomers=payload,
        [CUSTOMER_SET_SEARCH_COUNT]: (state,payload) =>state.countcustomers=payload,
    },
    actions: {
        [CUSTOMER_SEARCH_LOAD_LIST]:async ({commit,state}, payload )=>{
            return axios.post('/search-customer', payload)
            .then( (response)=>{
                if(response.data!=null){
                    commit(CUSTOMER_SET_SEARCH_LIST ,response.data.data);
                    commit(CUSTOMER_SET_SEARCH_COUNT , ( response.data.total - 5 ) );
                }
                return Promise.resolve(response);
            })
            .catch((error)=>{
                return Promise.reject(error);
            }).finally(()=>{

            });
        },
        [MASTER_SEARCH_LOAD_LIST]:async ({commit,state}, payload )=>{
            return axios.post('/search-master', payload)
            .then( (response)=>{
                if(response.data!=null){
                    commit(CUSTOMER_SET_SEARCH_LIST ,response.data.data);
                    commit(CUSTOMER_SET_SEARCH_COUNT , ( response.data.total - 5 ) );
                }
                return Promise.resolve(response);
            })
            .catch((error)=>{
                return Promise.reject(error);
            }).finally(()=>{

            });
        },
    },
    getters: {
        [CUSTOMER_GET_SEARCH_LIST]:state=>state.listsearchcustomers,
        [CUSTOMER_GET_SEARCH_COUNT]:state=>state.countcustomers,
    }
}