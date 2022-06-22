
import {
    SAVE_ENTITE_LIST,
    GET_ENTITE_LIST,
    GET_ENTITE_LIST_MES,
    ENTITE_LIST_MODULE
}
from '../types/types'

const table = {
    
    columns_def: [
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
            class: "justify-content-center",
            header_class: "",
            sort: true,
            filter: true,
            prefix: "",
            suffix: "",
        },     
        {
            id: "raisonsociale",
            display_name: "Raison Sociale",
            type: "string",
            class: "justify-content-center",
            header_class: "",
            sort: true,
            filter: true,   
            having: false,
            prefix: "",
            suffix: "",
        },
        {
            id: "contact",
            display_name: "contact",
            type: "html",
            class: "justify-content-center",
            header_class: "",
            sort: true,
            filter: true,   
            having: true,
            prefix: "",
            suffix: "",
            allow_groupby: true,
        },
        {
            id: "address",
            display_name: "Addresse",
            type: "html",
            class: "justify-content-center",
            header_class: "",
            sort: true,
            filter: true,   
            having: true,
            prefix: "",
            suffix: "",
            allow_groupby: true,
        },
        {
            id: "comment",
            display_name: "Note",
            type: "string",
            class: "justify-content-center",
            header_class: "",
            sort: false,
            filter: true,   
            having: false,
            prefix: "",
            suffix: "",
        },
        {
            id: "created_at",
            display_name: "DATE Creation",
            type:"date",
            format:"DD/MM/YY",
            class: "justify-content-center",
            header_class: "",
            sort: true,
            filter: true,   
            having: true,
            prefix: "",
            suffix: "",
            allow_groupby: true,
        },
        {
            id: "origine",
            display_name: "Origine",
            type: "string",
            class: "justify-content-center",
            header_class: "",
            sort: false,
            filter: true,   
            having: false,
            prefix: "",
            suffix: "",
        },
        {
            id: "statut_name",
            display_name: "Statut",
            type: "component",
            class: "justify-content-center",
            header_class: "",
            sort: true,
            filter: true,   
            having: true,
            prefix: "",
            suffix: "",
            allow_groupby: true,
            filter_options: [
                { id: 'Cible', value: 'Cible' },
                { id: 'Contact', value: 'Contact' },
                { id: 'Suspect', value: 'Suspect' },
                { id: 'Prospect', value: 'Prospect' },
                { id: 'Client', value: 'Client' },
                { id: 'Fiche obsolète', value: 'Fiche obsolète' },
                { id: 'Fiche doublons', value: 'Fiche doublons' },
            ]
        },
        {
            id: "action_co",
            display_name: "Action co",
            type: "html",
            class: "justify-content-start",
            header_class: "",
            sort: false,
            filter: true,   
            having: true,
            prefix: "",
            suffix: "",
        },
        {
            id: "litige",
            display_name: "Litige",
            type: "component",
            class: "justify-content-start",
            header_class: "",
            sort: false,
            filter: false,   
            having: false,
            prefix: "",
            suffix: "",
        },
        {
            id: "total_orders",
            display_name: "NreCde",
            type: "price",
            class: "justify-content-start",
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
            display_name: "MONTANT",
            type: "price",
            class: "justify-content-start",
            header_class: "",
            sort: true,
            filter: true,   
            having: true,
            prefix: "",
            suffix: "",
            group_total: true,
            footer_total: true,
        },
    
    ],
    
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
      group_item: 'campagnes',
      group_items: 'campagnes',
      footer_item: 'ITEM',
      footer_items: 'ITEMS',
      no_batch_action: "Aucune action par lot n'est disponible.",
    },

    highlight_row: {
          where: [
          ], 
          backgroundColor: '#f7c5af',
          color: '#fd3b35'
    }
    
}


export const entite = {

    namespaced: true,

    state: {

        table_def: {

            column_filters: [],//required empty array
            store: {
              MODULE: ENTITE_LIST_MODULE,//required
              INIT: GET_ENTITE_LIST,//required
            },
            batch_actions: table.batch_actions,
            translations: table.translations,
            highlight_row: table.highlight_row,
            item_route_name: "",// the route to trigger when a line is click 
            max_per_page: 15,//required          
            identifier: "entite_list_all",//required
            filter: true,// required boolean
            rearrange_columns: true,// required boolean
            columns_def: table.columns_def,

        },

        entite_user_table_def: {

            column_filters: [],//required empty array
            store: {
              MODULE: ENTITE_LIST_MODULE,//required
              INIT: GET_ENTITE_LIST_MES,//required
            },
            batch_actions: table.batch_actions,
            translations: table.translations,
            highlight_row: table.highlight_row,
            item_route_name: "",// the route to trigger when a line is click 
            max_per_page: 15,//required          
            identifier: "entite_list_all_mes",//required
            filter: true,// required boolean
            rearrange_columns: true,// required boolean
            columns_def: table.columns_def,

        },

    },

    getters: {
        entiteList: state => state.table_def,
        entiteUserList: state => state.entite_user_table_def,
    },

    actions: {

        async [GET_ENTITE_LIST]({ commit }, params) {

            return axios.post(`/get-entite-list`, params).then((response) => {
                return Promise.resolve(response)
                      
            }).catch((error) => {
                return  Promise.resolve(error)
            })

        },

        async [GET_ENTITE_LIST_MES]({ commit }, params) {

            return axios.post(`/get-entite-list-user`, params).then((response) => {
                return Promise.resolve(response)
                      
            }).catch((error) => {
                return  Promise.resolve(error)
            })

        },

    }
}