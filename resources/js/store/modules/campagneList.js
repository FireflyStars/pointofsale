
import {
    CAMPAGNE_LIST_MODULE,
    GET_CAMPAGNE_LIST,
    GET_USER_CAMPAGNE_LIST
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
            display_name: "No Commande",
            type: "string",
            class: "justify-content-start",
            header_class: "",
            sort: true,
            filter: true,
            prefix: "",
            suffix: "",
            table: "campagnes"
        },     
        {
            id: "name",
            display_name: "NOM CAMPAGNE",
            type: "string",
            class: "justify-content-start",
            header_class: "",
            sort: true,
            filter: true,   
            having: false,
            prefix: "",
            suffix: "",
            table: "campagnes"
        },
        {
            id: "username",
            display_name: "CREATEUR",
            type: "string",
            class: "justify-content-start",
            header_class: "",
            sort: true,
            filter: true,   
            having: true,
            prefix: "",
            suffix: "",
            allow_groupby: true,
        },
        {
            id: "datelancement",
            display_name: "DATE LANCEMENT",
            type:"date",
            format:"DD/MM/YY",
            class: "justify-content-start",
            header_class: "",
            sort: true,
            filter: true,   
            having: false,
            prefix: "",
            suffix: "",
            allow_groupby: true,
        },
        {
            id: "created_at",
            display_name: "DATE CREATION",
            type:"date",
            format:"DD/MM/YY",
            class: "justify-content-start",
            header_class: "",
            sort: true,
            filter: true,   
            having: false,
            prefix: "",
            suffix: "",
            table: "campagnes",
            allow_groupby: true,
        },
        {
            id: "type",
            display_name: "TYPE",
            type: "string",
            class: "justify-content-start",
            header_class: "",
            sort: true,
            filter: true,   
            having: false,
            prefix: "",
            suffix: "",
            allow_groupby: true,
            filter_options: [
                { id: 'EMAIL', value: 'EMAIL' },
                { id: 'SMS', value: 'SMS' },
                { id: 'COURRIER', value: 'COURRIER' },
                { id: 'COMMANDE PRODUIT', value: 'COMMANDE PRODUIT' },
            ]
        },
        {
            id: "status",
            display_name: "STATUS",
            type: "string",
            class: "justify-content-start",
            header_class: "",
            sort: true,
            filter: true,   
            having: false,
            prefix: "",
            suffix: "",
            allow_groupby: true,
            filter_options: [{
                id: 'NOUVEAU', value: 'NOUVEAU',
            }, {
                id: 'CAMPAGNE ENVOYEE', value: 'CAMPAGNE ENVOYEE'
            }]
        },
        {
            id: "nb",
            display_name: "NOMBRE",
            type: "number",
            class: "justify-content-start",
            header_class: "",
            sort: true,
            filter: false,   
            having: false,
            prefix: "",
            suffix: "",
        },
        {
            id: "montant",
            display_name: "MONTANT",
            type: "price",
            class: "justify-content-start",
            header_class: "",
            sort: true,
            filter: true,   
            having: false,
            prefix: "",
            suffix: "",
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



export const campagneList = {

    namespaced: true,

    state: {

        table_def: {

            column_filters: [],//required empty array
            store: {
              MODULE: CAMPAGNE_LIST_MODULE,//required
              INIT: GET_CAMPAGNE_LIST,//required
            },
            batch_actions: table.batch_actions,
            translations: table.translations,
            highlight_row: table.highlight_row,
            item_route_name: "marketing-campagne-details",// the route to trigger when a line is click 
            max_per_page: 15,//required          
            identifier: "campagne_list_all",//required
            filter: true,// required boolean
            rearrange_columns: true,// required boolean
            columns_def: table.columns_def,

        },

        campagnes_user_table_def: {

            column_filters: [],//required empty array
            store: {
              MODULE: CAMPAGNE_LIST_MODULE,//required
              INIT: GET_USER_CAMPAGNE_LIST,//required
            },
            batch_actions: table.batch_actions,
            translations: table.translations,
            highlight_row: table.highlight_row,
            item_route_name: "marketing-campagne-details",// the route to trigger when a line is click 
            max_per_page: 15,//required          
            identifier: "campagne_list_all_users",//required
            filter: true,// required boolean
            rearrange_columns: true,// required boolean
            columns_def: table.columns_def,

        },

    },

    getters: {
        campagnesList: state => state.table_def,
        campagnesUserList: state => state.campagnes_user_table_def,
    },

    actions: {

        async [GET_CAMPAGNE_LIST]({ commit }, params) {

            return axios.post(`/get-campagne-list`, params).then((response) => {
                return Promise.resolve(response)
                      
            }).catch((error) => {
                return  Promise.resolve(error)
            })

        },

        async [GET_USER_CAMPAGNE_LIST]({ commit }, params) {

            return axios.post(`/get-user-campagne-list`, params).then((response) => {
                return Promise.resolve(response)
                      
            }).catch((error) => {
                return  Promise.resolve(error)
            })

        },

    }
}