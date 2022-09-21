
import axios from 'axios'
import {
    SET_LOADING,
    PERSONNEL_LIST_MODULE,
    GET_PERSONNEL_LIST,
    RESET_DETAILS,
    GET_PERSONNEL_DETAILS,
    SAVE_PERSONNEL_DETAILS,
    PERSONNEL_SET_USER_DOCUMENTS,
    PERSONNEL_LOAD_USER_DOCUMENTS,
    PERSONNEL_UNSET_ORDER_DOCUMENT,
    PERSONNEL_REMOVE_DOCUMENT,
    PERSONNEL_GET_DOCUMENT_URL,
    PERSONNEL_UPLOAD_DOCUMENT,
    DELETE_PERSONNEL,
    PERSONNEL_SAVE_PERMIS_LIST,
    PERSONNEL_GET_PERMIS_LIST
}
from '../types/types'

export const personnel = {

    namespaced: true,

    state: {

        table_def: {

            column_filters: [],//required empty array
            store: {
              MODULE: PERSONNEL_LIST_MODULE,//required
              INIT: GET_PERSONNEL_LIST,//required
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
              group_item: 'user',
              group_items: 'user',
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
            item_route_name: "personnel-details",// the route to trigger when a line is click 
            max_per_page: 10,//required          
            identifier: "personnel_list_all",//required
            filter: true,// required boolean
            rearrange_columns: true,// required boolean
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
                    display_name: "No Personnel",
                    type: "string",
                    class: "",
                    header_class: "",
                    sort: true,
                    filter: true,   
                    prefix: "",
                    suffix: "",
                    table: "users"
                },
                {
                    id: "type_name",
                    display_name: "Type Contact",
                    type: "component",
                    class: "",
                    header_class: "",
                    sort: true,
                    filter: true,   
                    having: true,
                    prefix: "",
                    suffix: "",
                    allow_groupby: true,
                    filter_options: [{
                        id: "CDD", value: "CDD"
                    }, {
                        id: "CDI", value: "CDI"
                    }],
                    table: "user_type"
                },
                {
                    id: "prenom",
                    display_name: "Prénom",
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
                    id: "nom",
                    display_name: "Nom",
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
                    id: "role_display_name",
                    display_name: "Profil",
                    type: "string",
                    class: "",
                    header_class: "",
                    sort: true,
                    filter: true, 
                    having: true,  
                    prefix: "",
                    suffix: "",
                    allow_groupby: true,
                },
                {
                    id: "status_name",
                    display_name: "STATUS",
                    type: "component",
                    class: "",
                    header_class: "",
                    sort: true,
                    filter: true,   
                    having: true,
                    prefix: "",
                    suffix: "",
                    allow_groupby: true,
                    filter_options: [{
                        id: "ACTIF", value: "ACTIF"
                    }, {
                        id: "NON ACTIF", value: "NON ACTIF"
                    }]
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
                    id: "email",
                    display_name: "Email",
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

        },

        loading: {
            id: null,
            status: false,
        },

        details: {},

        userDocuments: [],
        userPermisList: []

    },

    getters: {
        userList: state => state.table_def,
        loading: state => state.loading,
        details: state => state.details,
        userDocuments: state => state.userDocuments,
        userPermisList: state => state.userPermisList,
    },

    mutations: {

        [RESET_DETAILS](state) {
            state.details = {}
            state.userDocuments = []
        },

        [SAVE_PERSONNEL_DETAILS](state, data) {
            state.details = data
        },

        [PERSONNEL_SET_USER_DOCUMENTS](state, documents) {
            state.userDocuments = documents
        },

        [PERSONNEL_UNSET_ORDER_DOCUMENT](state, id) {
            state.userDocuments = state.userDocuments.filter(obj => obj.id != id)
        },

        [SET_LOADING](state, payload) {
            state.loading.id = payload.id
            state.loading.status = payload.status
        },

        [PERSONNEL_SAVE_PERMIS_LIST](state, data) {
            state.userPermisList = data
        }

    },

    actions: {

        async [PERSONNEL_GET_PERMIS_LIST]({ state, commit }) {

            if(state.userPermisList.lengh) return 

            try {   
                const { data } = await axios.get('/get-user-permis-list')
                commit(PERSONNEL_SAVE_PERMIS_LIST, data)
            }

            catch(e) {
                throw e
            }

        },

        async [GET_PERSONNEL_LIST]({ commit }, params) {


            return axios.post(`/get-user-list`, params).then((response) => {
                return Promise.resolve(response)
                      
            }).catch((error)=>{
                return  Promise.resolve(error)
            })


        },

        async [GET_PERSONNEL_DETAILS]({ commit }, id) {

            try {
                const { data } = await axios.get(`/get-user-details/${id}`)
                commit(SAVE_PERSONNEL_DETAILS, data)
            }

            catch(e) {
                throw e
            }

        },

        [PERSONNEL_LOAD_USER_DOCUMENTS]({ commit, state }, data) {

            const { id, take = 3 } = data

            commit(SET_LOADING, { status: true, id: 'certification' })

            return axios.post(`/load-user-documents/${id}`, { take })
            .then((response) => {

                commit(PERSONNEL_SET_USER_DOCUMENTS, response.data)
                commit(SET_LOADING, { status: false, id: 'certification' })
        
                return Promise.resolve(response)
                        
            })
            .catch((error) => {
                commit(SET_LOADING, { status: false, id: 'certification' })
                return Promise.resolve(error)
            })
        },

        [PERSONNEL_REMOVE_DOCUMENT]({ commit, state }, document_id) {

            return axios.post(`/remove-user-document/${document_id}`)
            .then((response) => {
                commit(`${PERSONNEL_UNSET_ORDER_DOCUMENT}`, document_id)
                return Promise.resolve(response)
            })
            .catch((error)=>{
                return Promise.resolve(error)
            })

        },

        [PERSONNEL_GET_DOCUMENT_URL]({ commit, state }, document_id) {

            return axios.post(`/get-user-document-url/${document_id}`)
            .then((response)=>{
                window.location = `${response.data.document_url}`
                return Promise.resolve(response)
                      
            }).catch((error)=>{
                return Promise.resolve(error)
            })

        },

        [PERSONNEL_UPLOAD_DOCUMENT]({ commit, state }, formData) {

            return axios.post(`/upload-user-document`, formData, {
              headers: {
                'Content-Type': 'multipart/form-data'
              }
            })
            .then((response) => {
        
               return dispatch(`${PERSONNEL_LOAD_USER_DOCUMENTS}`, state.details.id)
                .then(resp2 => {
                  return Promise.resolve(response)
                })
                .finally(resp2 => {
                  return Promise.resolve(response)
                })
        
            })
            .catch((error)=>{
                return Promise.resolve(error)
            })
        },

        async [DELETE_PERSONNEL]({ commit }, id) {

            try {
                await axios.post(`/delete-user/${id}`)
            }
            catch(e) {
                throw e
            }

        }


    }

}