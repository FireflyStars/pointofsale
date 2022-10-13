
import axios from 'axios'
import {
    SET_LOADING,
    RESET_DETAILS,
    PERMIS_LIST_MODULE,
    PERMIS_GET_LIST,
}
from '../types/types'

const columns_def = [
    {
        id: "id",
        display_name: "",
        type: "checkbox",
        class: "",
        header_class: "",
        sort: false,
        filter: false,
        css: {
          flex: 0.5
        },
    }, 
    {
        id: "id",
        display_name: "No",
        type: "string",
        class: "",
        header_class: "",
        sort: true,
        filter: true,   
        prefix: "",
        suffix: "",
    },
    {
        id: "user_name",
        display_name: "Personnel",
        type: "string",
        class: "",
        header_class: "",
        sort: true,
        filter: true,   
        having: true,
        prefix: "",
        suffix: "",
    },
    {
        id: "name",
        display_name: "Permis",
        type: "string",
        class: "",
        header_class: "",
        sort: true,
        filter: true,   
        having: true,
        prefix: "",
        suffix: "",
    },
    {
        id: "dateofdocument",
        display_name: "Date Document",
        type: "date",
        class: "",
        header_class: "",
        sort: true,
        filter: true, 
        having: true,  
        prefix: "",
        suffix: "",
        allow_groupby: true,
    },
    {
        id: "expires",
        display_name: "Date Expiration",
        type: "date",
        class: "",
        header_class: "",
        sort: true,
        filter: true, 
        having: true,  
        prefix: "",
        suffix: "",
    }

]

export const permis = {

    namespaced: true,

    state: {

        table_def: {

            column_filters: [],//required empty array
            store: {
              MODULE: PERMIS_LIST_MODULE,//required
              INIT: PERMIS_GET_LIST,//required
            },
            batch_actions: {
                delete: {
                    name: "Delete",
                    route: "",
                    type: 'button'
                },
                status: {
                    type: "component"
                }

            },
            translations: {
              group_item: 'Permis',
              group_items: 'Permis',
              footer_item: 'ITEM',
              footer_items: 'ITEMS',
              no_batch_action: "Aucune action par lot n'est disponible.",
            },
            highlight_row: {
                  where: [
                    // { col: 'id', value: 10 }, example
                  ], 
                  backgroundColor: '#f7c5af',
                  color: '#fd3b35'
                }
            ,
            item_route_name: "",// the route to trigger when a line is click 
            max_per_page: 10,//required          
            identifier: "permis_list_all",//required
            filter: true,// required boolean
            rearrange_columns: true,// required boolean
            columns_def

        },

        loading: {
            id: null,
            status: false,
        },

        details: {},

    },

    getters: {
        list: state => state.table_def,
        loading: state => state.loading,
    },

    mutations: {

        [RESET_DETAILS](state) {
            state.details = {}
        },

        [SET_LOADING](state, payload) {
            state.loading.id = payload.id
            state.loading.status = payload.status
        },

    },

    actions: {


        async [PERMIS_GET_LIST]({ commit }, params) {


            return axios.post(`/get-permis-list`, params).then((response) => {
                return Promise.resolve(response)
                      
            }).catch((error)=>{
                return  Promise.resolve(error)
            })


        }
       

    }

}