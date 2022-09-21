
import axios from 'axios'
import {
    CHANGE_EVENT_DATE,
    GET_ACTION_COMMERCIAL,
    GET_ACTION_COMMERCIAL_MES,
    ACTION_COMMERCIAL_MODULE,
    GET_ACTION_COMMERCIAL_DETAILS,
    SAVE_ACTION_COMMERCIAL_DETAILS,
    GET_EVENT_USER_LIST,
    SAVE_EVENT_USER_LIST,
    CHANGE_EVENT_USER,
    GET_EVENT_HISTORY,
    SAVE_EVENT_HISTORY,
    FORCE_SET_FETCH_HISTORY_FALSE,
    RESET_DETAILS,
    GET_EVENT_STATUSES,
    SAVE_EVENT_STATUSES,
    CHANGE_EVENT_STATUS
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
            display_name: "No Rendez-vous",
            type: "string",
            class: "justify-content-start",
            header_class: "",
            sort: true,
            filter: true,
            prefix: "",
            suffix: "",
            table: "events"
        },     
        {
            id: "client_name",
            display_name: "Nom Client",
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
            id: "event_type",
            display_name: "Type Action",
            type: "string",
            class: "justify-content-start",
            header_class: "",
            sort: false,
            filter: true,   
            having: true,
            prefix: "",
            suffix: "",
            allow_groupby: true,
            filter_options: [
                { id: 'Prospection', value: 'Prospection' },
                { id: 'Fidelisation', value: 'Fidelisation' },
                { id: 'Reactivation', value: 'Reactivation' },
                { id: 'Entrant', value: 'Entrant' }
            ]
        },
        {
            id: "event_status_id",
            display_name: "Statut",
            type: "component",
            class: "justify-content-start",
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
            class: "justify-content-start",
            header_class: "",
            sort: true,
            filter: true,   
            having: true,
            prefix: "",
            suffix: "",
            allow_groupby: true,
            filter_options: [
                { id: 'Web Google', value: 'Web Google' },
                { id: 'Emailling', value: 'Emailling' },
                { id: 'Téléphone', value: 'Téléphone' }
            ]
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
            item_route_name: "action-commercial-details",// the route to trigger when a line is click 
            max_per_page: 15,//required          
            identifier: "action_co_list_all",//required
            filter: true,// required boolean
            rearrange_columns: true,// required boolean
            columns_def: table.columns_def,

        },

        action_co_user_table_def: {

            column_filters: [],//required empty array
            store: {
              MODULE: ACTION_COMMERCIAL_MODULE,//required
              INIT: GET_ACTION_COMMERCIAL_MES,//required
            },
            batch_actions: table.batch_actions,
            translations: table.translations,
            highlight_row: table.highlight_row,
            item_route_name: "action-commercial-details",// the route to trigger when a line is click 
            max_per_page: 15,//required          
            identifier: "entite_list_all_mes",//required
            filter: true,// required boolean
            rearrange_columns: true,// required boolean
            columns_def: table.columns_def,

        },

        details: {},

        userList: [],

        fetchedHistory: false,

        eventStatuses: []

    },

    getters: {
        
        actionCommercialList: state => state.table_def,
        actionCommercialListUser: state => state.action_co_user_table_def,
        details: state => state.details,
        userList: state => state.userList,
        fetchedHistory: state => state.fetchedHistory,
        eventStatuses: state => state.eventStatuses,

    },

    mutations: {

        [SAVE_ACTION_COMMERCIAL_DETAILS](state, data) {
            state.details = data
        },
        
        [SAVE_EVENT_USER_LIST](state, data) {
            state.userList = data
        },

        [SAVE_EVENT_HISTORY](state, data) {
            state.details.event_history = data
            state.fetchedHistory = true
        },

        [FORCE_SET_FETCH_HISTORY_FALSE](state) {
            state.fetchedHistory = false
        },

        [CHANGE_EVENT_USER](state, user) {
            state.details.client_name = user.name
        },

        [RESET_DETAILS](state) {
            state.details = {}
            state.fetchedHistory = false
        },

        [SAVE_EVENT_STATUSES](state, data) {
            state.eventStatuses = data
        }

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

        async [GET_ACTION_COMMERCIAL_DETAILS]({ commit }, id) {

            try {
                const { data } = await axios.get(`/action-commercial-details/${id}`)
                commit(SAVE_ACTION_COMMERCIAL_DETAILS, data)
            }

            catch(e) {
                throw e
            }

        },

        async [CHANGE_EVENT_DATE]({ dispatch, commit }, data) {
            
            try {
                await axios.post(`/change-action-commercial-event-date/${data.id}`, {
                    ...data
                })
                await dispatch(GET_EVENT_HISTORY, { id: data.id, limit: 5 })
                commit(FORCE_SET_FETCH_HISTORY_FALSE)
            }   

            catch(e) {
                throw e
            }

        },

        async [GET_EVENT_USER_LIST]({ commit, state }, id) {
            if(state.userList.length) return 
            try {
                const { data } = await axios.get(`/get-action-commercial-event-users/${id}`)
                commit(SAVE_EVENT_USER_LIST, data)
            }

            catch(e) {
                throw e
            }

        },

        async [CHANGE_EVENT_USER]({ commit }, payload) {
            
            try {
                const { data } = await axios.post(`/change-action-commercial-event-user/${payload.id}`, {
                    ...payload
                })
                commit(CHANGE_EVENT_USER, data)
            }   

            catch(e) {
                throw e
            }

        },

        async [GET_EVENT_HISTORY]({ commit }, { id, limit = 0 }) {
            
            try {
                const { data } = await axios.get(`/get-event-history/${id}`, {
                    params: {
                        limit
                    }
                })
                commit(SAVE_EVENT_HISTORY, data)
            }

            catch(e) {
                throw e
            }

        },

        async [GET_EVENT_STATUSES]({ commit, state }) {

            if(state.eventStatuses.length) return 

            try {
                const { data } = await axios.get('/get-event-statuses-all')
                commit(SAVE_EVENT_STATUSES, data)
            }

            catch(e) {
                throw e
            }

        },

        async [CHANGE_EVENT_STATUS](_, data) {

            const { id, statusId, annuler = false } = data

            await axios.post(`/change-event-status/${id}`, {
                id,
                statusId,
                annuler
            })

        }


    }
}