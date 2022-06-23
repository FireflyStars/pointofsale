
import {
    GET_ACTION_COMMERCIAL,
    GET_ACTION_COMMERCIAL_MES,
    ACTION_COMMERCIAL_MODULE,
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
            id: "client_name",
            display_name: "Nom Client",
            type: "string",
            class: "justify-content-center",
            header_class: "",
            sort: true,
            filter: true,   
            having: true,
            prefix: "",
            suffix: "",
        },
        {
            id: "contact",
            display_name: "contact",
            type: "html",
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
            id: "action",
            display_name: "Action",
            type: "string",
            class: "justify-content-start",
            header_class: "",
            sort: true,
            filter: true,   
            having: true,
            prefix: "",
            suffix: "",
        },
        {
            id: "action_date",
            display_name: "DATE Action",
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
            id: "event_type",
            display_name: "Type Action",
            type: "string",
            class: "justify-content-center",
            header_class: "",
            sort: false,
            filter: true,   
            having: true,
            prefix: "",
            suffix: "",
        },
        {
            id: "event_status_id",
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
            filter_options: '/action-commercial-statuses'
        },
        {
            id: "address",
            display_name: "Addresse",
            type: "html",
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
            id: "origin",
            display_name: "Origine",
            type: "string",
            class: "justify-content-center",
            header_class: "",
            sort: true,
            filter: true,   
            having: true,
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


export const actionCommercial = {

    namespaced: true,

    state: {

        table_def: {

            column_filters: [],//required empty array
            store: {
              MODULE: ACTION_COMMERCIAL_MODULE,//required
              INIT: GET_ACTION_COMMERCIAL,//required
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
              MODULE: ACTION_COMMERCIAL_MODULE,//required
              INIT: GET_ACTION_COMMERCIAL_MES,//required
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
        actionCommercialList: state => state.table_def,
        actionCommercialListUser: state => state.entite_user_table_def,
    },

    actions: {

        async [GET_ACTION_COMMERCIAL]({ commit }, params) {

            return axios.post(`/action-commercial-list`, params).then((response) => {
                return Promise.resolve(response)
                      
            }).catch((error) => {
                return  Promise.resolve(error)
            })

        },

        async [GET_ACTION_COMMERCIAL_MES]({ commit }, params) {

            return axios.post(`/action-commercial-list-mes`, params).then((response) => {
                return Promise.resolve(response)
                      
            }).catch((error) => {
                return  Promise.resolve(error)
            })

        },

    }
}