
import axios from 'axios'
import {
    SET_LOADING,
    POINTAGE_LIST_MODULE,
    GET_POINTAGE_LIST,
    GET_POINTAGE_LIST_MES,
    RESET_DETAILS,
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
        type: "number",
        class: "",
        header_class: "",
        sort: true,
        filter: true,   
        prefix: "",
        suffix: "",
    },
    {
        id: "commande",
        display_name: "Commande",
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
        id: "client",
        display_name: "Client",
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
        id: "personnel",
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
        id: "datepointage",
        display_name: "Date Pointage",
        type: "date",
        format: "DD/MM/YY",
        class: "",
        header_class: "",
        sort: true,
        filter: true, 
        having: true,  
        prefix: "",
        suffix: "",
        table: 'users',
        allow_groupby: true,
    },
    {
        id: "numberh",
        display_name: "Numbre Heure",
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
        id: "pointage_type_id",
        display_name: "Type",
        type: "component",
        class: "",
        header_class: "",
        sort: true,
        filter: true,   
        having: true,
        prefix: "",
        suffix: "",
        allow_groupby: true,
        filter_options: '/get-pointage-types-formatted'
    },
    {
        id: "comment",
        display_name: "Commentaire",
        type: "string",
        class: "",
        header_class: "",
        sort: true,
        filter: true,
        having: true,   
        prefix: "",
        suffix: "",
    },

]

export const pointage = {

    namespaced: true,

    state: {

        table_def: {

            column_filters: [],//required empty array
            store: {
              MODULE: POINTAGE_LIST_MODULE,//required
              INIT: GET_POINTAGE_LIST,//required
            },
            batch_actions: {
                delete: {
                    name: "Delete",
                    route: "DeleteDevis",
                    type: 'button'
                },
                status: {
                    type: "component"
                }

            },
            translations: {
              group_item: 'Pointage',
              group_items: 'Pointages',
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
            identifier: "Poimtage-list-all",//required
            filter: true,// required boolean
            rearrange_columns: true,// required boolean
            columns_def,

        },

        table_def_mes: {

            column_filters: [],//required empty array
            store: {
              MODULE: POINTAGE_LIST_MODULE,//required
              INIT: GET_POINTAGE_LIST_MES,//required
            },
            batch_actions: {
                delete: {
                    name: "Delete",
                    route: "DeleteDevis",
                    type: 'button'
                },
                status: {
                    type: "component"
                }

            },
            translations: {
              group_item: 'Poimtage',
              group_items: 'Poimtages',
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
            identifier: "pointage-list-all",//required
            filter: true,// required boolean
            rearrange_columns: true,// required boolean
            columns_def,

        },

        loading: {
            id: null,
            status: false,
        },

    },

    getters: {
        pointages: state => state.table_def,
        pointagesMes: state => state.table_def_mes,
        loading: state => state.loading,
    },

    mutations: {

        [RESET_DETAILS](state) {
            state.details = {}
        },

        [SET_LOADING](state, payload) {
            state.loading.id = payload.id
            state.loading.status = payload.status
        }

    },

    actions: {

        async [GET_POINTAGE_LIST]({ commit }, params) {


            return axios.post(`/get-pointages-list`, params).then((response) => {
                return Promise.resolve(response)
                      
            }).catch((error)=>{
                return  Promise.resolve(error)
            })


        },

        async [GET_POINTAGE_LIST_MES]({ commit }, params) {


            return axios.post(`/get-pointages-list-mes`, params).then((response) => {
                return Promise.resolve(response)
                      
            }).catch((error)=>{
                return  Promise.resolve(error)
            })


        }


    }

}