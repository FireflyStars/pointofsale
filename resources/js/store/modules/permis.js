
import axios from 'axios'
import {
    SET_LOADING,
    PERMIS_LIST_MODULE,
    PERMIS_GET_LIST,
    PERMIS_GET_DETAILS
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
        id: "contact",
        display_name: "Contact",
        type: "html",
        class: "",
        header_class: "",
        sort: true,
        filter: true,   
        having: true,
        prefix: "",
        suffix: "",
    },
    {
        id: "email",
        display_name: "Email",
        type: "string",
        class: "",
        header_class: "",
        sort: true,
        filter: true,   
        having: false,
        prefix: "",
        suffix: "",
    },
    {
        id: "comment",
        display_name: "Note",
        type: "string",
        class: "",
        header_class: "",
        sort: true,
        filter: true, 
        having: false,  
        prefix: "",
        suffix: "",
    },
    
    {
        id: "created_at",
        display_name: "Date Creaction",
        type: "date",
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
        id: "supplier_type",
        display_name: "TYPE",
        type: "string",
        class: "",
        header_class: "",
        sort: true,
        filter: true,   
        having: true,
        prefix: "",
        suffix: "",
        allow_groupby: true,
        filter_options: [{
            id: 'PRODUIT MECANIQUE', value: 'PRODUIT MECANIQUE'
        }, {
            id: 'ORDINATEUR', value: 'ORDINATEUR'
        }, {
            id: 'INTERIM', value: 'INTERIM'
        }]
    },

    {
        id: "supplier_status_id",
        display_name: "STATUT",
        type: "component",
        class: "",
        header_class: "",
        sort: true,
        filter: true,   
        having: true,
        prefix: "",
        suffix: "",
        allow_groupby: true,
        filter_options: '/get-fournisseur-supplier-status-formatted'
    },
    {
        id: "actif",
        display_name: "Actif",
        type: "component",
        class: "",
        header_class: "",
        sort: false,
        filter: false,   
        having: true,
        prefix: "",
        suffix: "",
        allow_groupby: true,
        filter_options: ''
    },
    {
        id: "count_orders",
        display_name: "NreCde",
        type: "number",
        class: "",
        header_class: "",
        sort: true,
        filter: true,   
        having: true,
        prefix: "",
        suffix: "",
        group_total: true,
        footer_total: true,
    },

    {
        id: "montant",
        display_name: "MONTANT CDE",
        type: "price",
        class: "",
        header_class: "",
        sort: true,
        filter: true,   
        having: true,
        prefix: "",
        suffix: "",
        group_total: true,
        footer_total: true,
    },

]

export const fournisseur = {

    namespaced: true,

    state: {

        table_def: {

            column_filters: [],//required empty array
            store: {
              MODULE: FOURNISSEUR_LIST_MODULE,//required
              INIT: FOURNISSEUR_GET_LIST,//required
            },
            batch_actions: {
                delete: {
                    name: "Delete",
                    route: "DeleteFournisseur",
                    type: 'button'
                },
                status: {
                    type: "component"
                }

            },
            translations: {
              group_item: 'Fournisseur',
              group_items: 'Fournisseur',
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
            item_route_name: "fournisseur-details",// the route to trigger when a line is click 
            max_per_page: 10,//required          
            identifier: "fournisseur_list_all",//required
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


        async [FOURNISSEUR_GET_LIST]({ commit }, params) {


            return axios.post(`/get-permis-list`, params).then((response) => {
                return Promise.resolve(response)
                      
            }).catch((error)=>{
                return  Promise.resolve(error)
            })


        }
       

    }

}