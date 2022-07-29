
import {
    OUVRAGE_MODULE,
    GET_OUVRAGE_LIST,
    GET_OUVRAGE_LIST_USER,
    RESET_DETAILS,
    SET_LOADING,
    GET_OUVRAGE_DETAILS,
    SAVE_OUVRAGE_DETAILS,
    GET_OUVRAGE_RESULTS,
    UPDATE_OUVRAGE_RESULTS,
    VALIDER_OUVRAGE,
    GET_OUVRAGE_LIST_PRESTATION,
    GET_OUVRAGE_LIST_INSTALLATION,
    GET_OUVRAGE_LIST_SECURITE
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
            class: "justify-content-start",
            header_class: "",
            sort: true,
            filter: true,
            prefix: "",
            suffix: "",
        },     
        {
            id: "name",
            display_name: "Nom",
            type: "string",
            class: "justify-content-start",
            header_class: "",
            sort: true,
            filter: true,   
            having: false,
            prefix: "",
            suffix: "",
            table: "ouvrages"
        },
        {
            id: "textchargeaffaire",
            display_name: "Description",
            type: "string",
            class: "justify-content-start",
            header_class: "",
            sort: true,
            filter: true,   
            having: true,
            prefix: "",
            suffix: "",
            allow_groupby: true,
            table: "ouvrages",
        },
        {
            id: "codelcdt",
            display_name: "Réference",
            type: "string",
            class: "justify-content-start",
            header_class: "",
            sort: true,
            filter: true,   
            having: false,
            prefix: "",
            suffix: "",
        },
        {
            id: "created_at",
            display_name: "Date Création",
            type: "date",
            format: "DD/MM/YY",
            class: "justify-content-start",
            header_class: "",
            sort: true,
            filter: true,   
            having: false,
            prefix: "",
            suffix: "",
           
        },
        {
            id: "unit_name",
            display_name: "Unité",
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
                { id: 'L', value: 'L' },
                { id: 'HR', value: 'HR' },
                { id: 'UNIT', value: 'UNIT' },
                { id: 'KG', value: 'KG' },
                { id: 'M', value: 'M' },
                { id: 'M2', value: 'M2' },
                { id: 'ML', value: 'ML' },
                { id: 'ENS', value: 'ENS' },
                { id: 'F', value: 'F' },
            ]
        },
        {
            id: "type",
            display_name: "Type",
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
                { id: 'PRESTATION', value: 'PRESTATION' },
                { id: 'INSTALLATION', value: 'INSTALLATION' },
                { id: 'SECURITE', value: 'SECURITE' }
            ]
        },
        {
            id: "ouvrage_toit_name",
            display_name: "Type toit",
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
                { id: 'Bac Acier', value: 'Bac Acier' },
                { id: 'Etanchéité Bitumineuse', value: 'Etanchéité Bitumineuse' },
                { id: 'Plaques Fibres Ciment', value: 'Plaques Fibres Ciment' },
                { id: 'Tous les toits', value: 'Tous les toits' },
            ]
        },
        {
            id: "ouvrage_metier_name",
            display_name: "Metier",
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
                { id: 'Maintenance', value: 'Maintenance' },
                { id: 'Interventions', value: 'Interventions' },
                { id: 'Etudes', value: 'Etudes' },
                { id: 'Organisation Chantier', value: 'Organisation Chantier' },
                { id: 'Transverse', value: 'Transverse' },
                { id: 'Réparation', value: 'Réparation' }
            ]
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
      group_item: 'Ouvrages',
      group_items: 'Ouvrages',
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

const def = {
    column_filters: [],//required empty array
    batch_actions: table.batch_actions,
    translations: table.translations,
    highlight_row: table.highlight_row,
    item_route_name: "ouvrage-details",// the route to trigger when a line is click 
    max_per_page: 15,//required          
    filter: true,// required boolean
    rearrange_columns: true,// required boolean
    columns_def: table.columns_def,
}


export const ouvrage = {

    namespaced: true,

    state: {

        

        table_def: {

            store: {
              MODULE: OUVRAGE_MODULE,//required
              INIT: GET_OUVRAGE_LIST,//required
            },
            identifier: "ouvrage_list_all",//required
            ...def,

        },

        table_def_installation: {

            store: {
              MODULE: OUVRAGE_MODULE,//required
              INIT: GET_OUVRAGE_LIST_INSTALLATION,//required
            },
            identifier: "ouvrage_list_installation",//required
            ...def,

        },

        table_def_securite: {

            store: {
              MODULE: OUVRAGE_MODULE,//required
              INIT: GET_OUVRAGE_LIST_SECURITE,//required
            },
            identifier: "ouvrage_list_securite",//required
            ...def,

        },

        table_def_prestation: {

            store: {
              MODULE: OUVRAGE_MODULE,//required
              INIT: GET_OUVRAGE_LIST_PRESTATION,//required
            },
            identifier: "ouvrage_list_prestation",//required
            ...def,

        },

        details: {},

        loading: {
            id: 0,
            status: false
        },


    },

    getters: {
        ouvrageList: state => state.table_def,
        ouvrageListInstallation: state => state.table_def_installation,
        ouvrageListSecurite: state => state.table_def_securite,
        ouvrageListPrestation: state => state.table_def_prestation,
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
        [SAVE_OUVRAGE_DETAILS](state, data) {
            state.details = data
        },
        [UPDATE_OUVRAGE_RESULTS](state, { data, type }) {
            state.details[type] = data
        },
    },

    actions: {

        async [GET_OUVRAGE_LIST]({ commit }, params) {

            return axios.post(`/get-ouvrage-list`, params).then((response) => {
                return Promise.resolve(response)
                      
            }).catch((error) => {
                return  Promise.resolve(error)
            })

        },

        async [GET_OUVRAGE_LIST_INSTALLATION]({ commit }, params) {

            return axios.post(`/get-ouvrage-list-installation`, params).then((response) => {
                return Promise.resolve(response)
                      
            }).catch((error) => {
                return  Promise.resolve(error)
            })

        },

        async [GET_OUVRAGE_LIST_PRESTATION]({ commit }, params) {

            return axios.post(`/get-ouvrage-list-prestation`, params).then((response) => {
                return Promise.resolve(response)
                      
            }).catch((error) => {
                return  Promise.resolve(error)
            })

        },

        async [GET_OUVRAGE_LIST_SECURITE]({ commit }, params) {

            return axios.post(`/get-ouvrage-list-securite`, params).then((response) => {
                return Promise.resolve(response)
                      
            }).catch((error) => {
                return  Promise.resolve(error)
            })

        },


        async [GET_OUVRAGE_DETAILS]({ commit }, id) {

            const { data } = await axios.get(`/get-ouvrage-details/${id}`)
            
            commit(SAVE_OUVRAGE_DETAILS, data)

        },

        async [GET_OUVRAGE_RESULTS]({ commit }, { type, id }) {

            try {

                commit(SET_LOADING, { status: true, id: type })

                const { data } = await axios.get(`/get-ouvrage-results/${id}`, {
                    params: {
                        type
                    }
                })

                commit(UPDATE_OUVRAGE_RESULTS, { data, type }) 

            }

            catch(e) {
                throw e
            }

            finally {
                commit(SET_LOADING, { status: false, id: type })
            }

        },

        async [VALIDER_OUVRAGE]({ commit }, details) {

            try {

                await axios.post(`/valider-ouvrage/${details.id}`, {
                    ...details
                })

            }

            catch(e) {
                throw e
            }

        }


    }
}