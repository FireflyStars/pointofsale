
import axios from 'axios'
import {
    SET_LOADING,
    COMMANDE_FOURNISSEUR_LIST_MODULE,
    COMMANDE_FOURNISSEUR_GET_LIST,
    COMMANDE_FOURNISSEUR_GET_LIST_MES,
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
        id: "name",
        display_name: "NOM",
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
        id: "contact",
        display_name: "CONTACT",
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
        id: "user_id",
        display_name: "Responsable",
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
        id: "refenece",
        display_name: "Reference",
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
        id: "dateinvoice",
        display_name: "DATE FACTURE",
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
        id: "supplier_order_state_id",
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
        filter_options: '/get-commande-fournisseur-supplier-status-formatted'
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

export const commande_fournisseur = {

    namespaced: true,

    state: {

        table_def: {

            column_filters: [],//required empty array
            store: {
              MODULE: COMMANDE_FOURNISSEUR_LIST_MODULE,//required
              INIT: COMMANDE_FOURNISSEUR_GET_LIST,//required
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
            item_route_name: "",// the route to trigger when a line is click 
            max_per_page: 10,//required          
            identifier: "fournisseur_list_all",//required
            filter: true,// required boolean
            rearrange_columns: true,// required boolean
            columns_def

        },

        table_def_mes: {

            column_filters: [],//required empty array
            store: {
              MODULE: COMMANDE_FOURNISSEUR_LIST_MODULE,//required
              INIT: COMMANDE_FOURNISSEUR_GET_LIST_MES,//required
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
            identifier: "fournisseur_list_mes",//required
            filter: true,// required boolean
            rearrange_columns: true,// required boolean
            columns_def

        },

    },

    getters: {
        list: state => state.table_def,
        userList: state => state.table_def_mes,
    },

    mutations: {
    },

    actions: {


        async [COMMANDE_FOURNISSEUR_GET_LIST]({ commit }, params) {


            return axios.post(`/get-commande-fournisseur-list`, params).then((response) => {
                return Promise.resolve(response)
                      
            }).catch((error)=>{
                return  Promise.resolve(error)
            })


        },

        async [COMMANDE_FOURNISSEUR_GET_LIST_MES]({ commit }, params) {


            return axios.post(`/get-commande-fournisseur-list-mes`, params).then((response) => {
                return Promise.resolve(response)
                      
            }).catch((error)=>{
                return  Promise.resolve(error)
            })


        },

    }

}