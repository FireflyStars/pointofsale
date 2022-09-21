
import {
    CONTACT_LIST_MODULE,
    GET_CONTACT_LIST,
    GET_CONTACT_LIST_USER,
    RESET_DETAILS,
    SET_LOADING,
    GET_CONTACT_DETAILS,
    SAVE_CONTACT_DETAILS,
    GET_CONTACT_RESULTS,
    UPDATE_CONTACT_RESULTS,
    GET_CONTACT_STATUSES,
    SAVE_CONTACT_STATUSES,
    CHANGE_CONTACT_STATUS,
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
            display_name: "No Contact",
            type: "string",
            class: "justify-content-start",
            header_class: "",
            sort: true,
            filter: true,
            prefix: "",
            suffix: "",
            table: "contacts"
        },     
        {
            id: "firstname",
            display_name: "Prénom",
            type: "string",
            class: "justify-content-start",
            header_class: "",
            sort: true,
            filter: true,   
            having: false,
            prefix: "",
            suffix: "",
            table: "contacts"
        },
        {
            id: "name",
            display_name: "Nom",
            type: "string",
            class: "justify-content-start",
            header_class: "",
            sort: true,
            filter: true,   
            having: true,
            prefix: "",
            suffix: "",
            allow_groupby: true,
            table: "contacts",
        },
        {
            id: "contact_qualite",
            display_name: "Qualite",
            type: "component",
            class: "justify-content-start",
            header_class: "",
            sort: true,
            filter: true,   
            having: true,
            prefix: "",
            suffix: "",
            allow_groupby: true,
            filter_options: [
                { id: 'Directeur', value: 'Directeur' },
                { id: 'Comptable', value: 'Comptable' },
                { id: 'Acheteur', value: 'Acheteur' }
            ]
        },
        {
            id: "contact_type",
            display_name: "Type",
            type: "component",
            class: "justify-content-start",
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
                { id: 'Fiche obsolète', value: 'Fiche obsolète' },
                { id: 'Fiche doublons', value: 'Fiche doublons' },
            ]
        },
        {
            id: "customer_name",
            display_name: "Entité",
            type: "string",
            class: "justify-content-start",
            header_class: "",
            sort: true,
            filter: true,   
            having: true,
            prefix: "",
            suffix: "",
            allow_groupby: true,
            table: "customers"
        },
        {
            id: "email",
            display_name: "Email",
            type: "string",
            class: "justify-content-start",
            header_class: "",
            sort: true,
            filter: true,   
            having: false,
            prefix: "",
            suffix: "",
            table: "contacts"
        }

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


export const contactList = {

    namespaced: true,

    state: {

        table_def: {

            column_filters: [],//required empty array
            store: {
              MODULE: CONTACT_LIST_MODULE,//required
              INIT: GET_CONTACT_LIST,//required
            },
            batch_actions: table.batch_actions,
            translations: table.translations,
            highlight_row: table.highlight_row,
            item_route_name: "contact-details",// the route to trigger when a line is click 
            max_per_page: 15,//required          
            identifier: "contact_list_all",//required
            filter: true,// required boolean
            rearrange_columns: true,// required boolean
            columns_def: table.columns_def,

        },

        campagnes_user_table_def: {

            column_filters: [],//required empty array
            store: {
              MODULE: CONTACT_LIST_MODULE,//required
              INIT: GET_CONTACT_LIST_USER,//required
            },
            batch_actions: table.batch_actions,
            translations: table.translations,
            highlight_row: table.highlight_row,
            item_route_name: "contact-details",// the route to trigger when a line is click 
            max_per_page: 15,//required          
            identifier: "contact_list_all_users",//required
            filter: true,// required boolean
            rearrange_columns: true,// required boolean
            columns_def: table.columns_def,

        },

        details: {},

        loading: {
            id: 0,
            status: false
        },

        statuses: []

    },

    getters: {
        contactList: state => state.table_def,
        contactUserList: state => state.campagnes_user_table_def,
        loading: state => state.loading,
        details: state => state.details,
        statuses: state => state.statuses,
    },

    mutations: {
        [RESET_DETAILS](state) {
            state.details = {}
        },
        [SET_LOADING](state, payload) {
            state.loading.id = payload.id
            state.loading.status = payload.status
        },
        [SAVE_CONTACT_DETAILS](state, data) {
            state.details = data
        },
        [UPDATE_CONTACT_RESULTS](state, { data, type }) {
            state.details[type] = data
        },
        [SAVE_CONTACT_STATUSES](state, data) {
            state.statuses = data
        }
    },

    actions: {

        async [GET_CONTACT_LIST]({ commit }, params) {

            return axios.post(`/get-contact-list`, params).then((response) => {
                return Promise.resolve(response)
                      
            }).catch((error) => {
                return  Promise.resolve(error)
            })

        },

        async [GET_CONTACT_LIST_USER]({ commit }, params) {

            return axios.post(`/get-user-contact-list`, params).then((response) => {
                return Promise.resolve(response)
                      
            }).catch((error) => {
                return  Promise.resolve(error)
            })

        },

        async [GET_CONTACT_DETAILS]({ commit }, id) {

            const { data } = await axios.get(`/get-contact-details/${id}`)
            
            commit(SAVE_CONTACT_DETAILS, data)

        },

        async [GET_CONTACT_RESULTS]({ commit }, { type, id }) {

            try {

                commit(SET_LOADING, { status: true, id: type })

                const { data } = await axios.get(`/get-contact-results/${id}`, {
                    params: {
                        type
                    }
                })

                commit(UPDATE_CONTACT_RESULTS, { data, type }) 

            }

            catch(e) {
                throw e
            }

            finally {
                commit(SET_LOADING, { status: false, id: type })
            }

        },


        async [GET_CONTACT_STATUSES]({ commit, state }) {

            if(state.statuses.length > 0) return 

            try {
                const { data } = await axios.get('/get-contact-statuses-all')
                commit(SAVE_CONTACT_STATUSES, data)
            }

            catch(e) {
                throw e
            }

        },

        async [CHANGE_CONTACT_STATUS](_, data) {

            const { id, statusId } = data

            await axios.post(`/change-contact-status/${id}`, {
                statusId,
            })

        }



    }
}