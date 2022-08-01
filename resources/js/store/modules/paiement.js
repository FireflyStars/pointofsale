
import axios from 'axios'
import {
    SET_LOADING,
    PAIEMENT_LIST_MODULE,
    GET_PAIEMENT_LIST,
    GET_PAIEMENT_LIST_MES,
    GET_PAIEMENT_LIST_VALIDER,
    RESET_DETAILS,
    GET_PAIEMENT_DETAILS,
    SAVE_PAIEMENT_DETAILS,
    VALIDER_PAIEMENT,
    GET_PAIEMENT_RESULTS,
    SAVE_PAIEMENT_RESULTS
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
        display_name: "No Paiement",
        type: "number",
        class: "",
        header_class: "",
        sort: true,
        filter: true,   
        prefix: "",
        suffix: "",
    },
    {
        id: "invoice_id",
        display_name: "No Facture",
        type: "string",
        class: "",
        header_class: "",
        sort: true,
        filter: true,   
        prefix: "",
        suffix: "",
    },
    {
        id: "",
        display_name: "No Commande",
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
        id: "customer_name",
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
        id: "datepaiement",
        display_name: "Date Paiement",
        type: "date",
        format: "DD/MM/YY",
        class: "",
        header_class: "",
        sort: true,
        filter: true, 
        prefix: "",
        suffix: "",
        table: 'users',
        allow_groupby: true,
    },
    {
        id: "responsable",
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
        id: "paiement_state_id",
        display_name: "Statut",
        type: "component",
        class: "",
        header_class: "",
        sort: true,
        filter: true,   
        having: true,
        prefix: "",
        suffix: "",
        allow_groupby: true,
        filter_options: '/get-paiements-types-formatted'
    },
    {
        id: "montantpaiement",
        display_name: "Montant",
        type: "number",
        class: "",
        header_class: "",
        sort: true,
        filter: true,
        prefix: "",
        suffix: "",
    },

]

export const paiement = {

    namespaced: true,

    state: {

        table_def: {

            column_filters: [],//required empty array
            store: {
              MODULE: PAIEMENT_LIST_MODULE,//required
              INIT: GET_PAIEMENT_LIST,//required
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
              group_item: 'Paiement',
              group_items: 'Paiements',
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
            item_route_name: "paiement-details",// the route to trigger when a line is click 
            max_per_page: 10,//required          
            identifier: "PAIEMENT-list-all",//required
            filter: true,// required boolean
            rearrange_columns: true,// required boolean
            columns_def,

        },

        table_def_mes: {

            column_filters: [],//required empty array
            store: {
              MODULE: PAIEMENT_LIST_MODULE,//required
              INIT: GET_PAIEMENT_LIST_MES,//required
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
              group_item: 'PAIEMENT',
              group_items: 'PAIEMENTs',
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
            item_route_name: "paiement-details",// the route to trigger when a line is click 
            max_per_page: 10,//required          
            identifier: "PAIEMENT-list-mes",//required
            filter: true,// required boolean
            rearrange_columns: true,// required boolean
            columns_def,

        },

        table_def_validerParComptable: {

            column_filters: [],//required empty array
            store: {
              MODULE: PAIEMENT_LIST_MODULE,//required
              INIT: GET_PAIEMENT_LIST_VALIDER,//required
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
              group_item: 'PAIEMENT',
              group_items: 'PAIEMENTs',
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
            item_route_name: "paiement-details",// the route to trigger when a line is click 
            max_per_page: 10,//required          
            identifier: "PAIEMENT-list-valider",//required
            filter: true,// required boolean
            rearrange_columns: true,// required boolean
            columns_def,

        },

        loading: {
            id: null,
            status: false,
        },

        details: {}

    },

    getters: {
        paiements: state => state.table_def,
        paiementsMes: state => state.table_def_mes,
        paiementsValider: state => state.table_def_validerParComptable,
        loading: state => state.loading,
        details: state => state.details,
    },

    mutations: {

        [RESET_DETAILS](state) {
            state.details = {}
        },

        [SET_LOADING](state, payload) {
            state.loading.id = payload.id
            state.loading.status = payload.status
        },

        [SAVE_PAIEMENT_DETAILS](state, data) {
            state.details = data
        },

        [SAVE_PAIEMENT_RESULTS](state, data) {
            state.details.history = data
        }

    },

    actions: {

        async [GET_PAIEMENT_LIST]({ commit }, params) {


            return axios.post(`/get-paiements-list`, params).then((response) => {
                return Promise.resolve(response)
                      
            }).catch((error)=>{
                return  Promise.resolve(error)
            })


        },

        async [GET_PAIEMENT_LIST_MES]({ commit }, params) {


            return axios.post(`/get-paiements-list-mes`, params).then((response) => {
                return Promise.resolve(response)
                      
            }).catch((error)=>{
                return  Promise.resolve(error)
            })


        },

        async [GET_PAIEMENT_LIST_VALIDER]({ commit }, params) {


            return axios.post(`/get-paiements-list-validar`, params).then((response) => {
                return Promise.resolve(response)
                      
            }).catch((error)=>{
                return  Promise.resolve(error)
            })


        },

        async [GET_PAIEMENT_DETAILS]({ commit }, id) {

            try {
                const { data } = await axios.get(`/get-paiement-details/${id}`)
                commit(SAVE_PAIEMENT_DETAILS, data)
            }
            
            catch(e) {
                throw e
            }

        },

        async [VALIDER_PAIEMENT]({ }, id) {

            try {
                await axios.post(`/valider-paiement/${id}`)
            }

            catch(e) {
                throw e
            }

        },

        async [GET_PAIEMENT_RESULTS]({ commit }, data) {
            
            const { type, id } = data
            
            try {
                commit(SET_LOADING, { id: 'history', status: true })
                const { data } = await axios.get(`/get-paiement-history/${id}`)
                console.log(data)
                commit(SAVE_PAIEMENT_RESULTS, data)
            }

            catch(e) {
                throw e
            }

            finally {
                commit(SET_LOADING, { id: 'history', status: false })
            }

        }


    }

}