
import axios from 'axios'
import {
    ARTICLES_MODULE,
    GET_ARTICLES_LIST,
    GET_ARTICLES_DETAILS,
    SAVE_ARTICLES_DETAILS,
    ARTICLES_VALIDATE_PRODUCT,
    ARTICLES_UNSET_PRODUCT_DOCUMENT,
    ARTICLES_SET_PRODUCT_DOCUMENTS,
    ARTICLES_LOAD_PRODUCT_DOCUMENTS,
    ARTICLES_REMOVE_DOCUMENT,
    ARTICLES_GET_DOCUMENT_URL,
    ARTICLES_UPLOAD_DOCUMENT,
    GET_PRODUCT_DOCUMENT_TYPES,
    SAVE_PRODUCT_DOCUMENT_TYPES,
    SET_LOADING,
    RESET_DETAILS
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
            table: 'products'
        },
        {
            id: "description",
            display_name: "Description",
            type: "string",
            class: "justify-content-start",
            header_class: "",
            sort: true,
            filter: true,   
            having: false,
            prefix: "",
            suffix: "",
            allow_groupby: true,
            table: 'products'
        },
        {
            id: "reference",
            display_name: "Reference",
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
            table: 'products'
        },
        {
            id: "unite",
            display_name: "Unité",
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
            id: "supplier_reference",
            display_name: "Réference fournisseurs",
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
            id: "type",
            display_name: "Type",
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
                { id: 'MO', value: 'MO' },
                { id: 'PRODUIT', value: 'PRODUIT' },
                { id: 'INTERIM', value: 'INTERIM' },
            ]
        },
        {
            id: "product_price",
            display_name: "Prix Achat",
            type: "number",
            class: "justify-content-start",
            header_class: "",
            sort: true,
            filter: true,   
            having: true,
            prefix: "",
            suffix: "",
        },
        {
            id: "product_wholesale_price",
            display_name: "Mon Prix Achat",
            type: "number",
            class: "justify-content-start",
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
      group_item: 'articles',
      group_items: 'articles',
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


export const articles = {

    namespaced: true,

    state: {

        table_def: {

            column_filters: [],//required empty array
            store: {
              MODULE: ARTICLES_MODULE,//required
              INIT: GET_ARTICLES_LIST,//required
            },
            batch_actions: table.batch_actions,
            translations: table.translations,
            highlight_row: table.highlight_row,
            item_route_name: "articles-details",// the route to trigger when a line is click 
            max_per_page: 15,//required          
            identifier: "articles_list",//required
            filter: true,// required boolean
            rearrange_columns: true,// required boolean
            columns_def: table.columns_def,

        },

        details: {},

        documents: [],

        loading: {
            id: null,
            status: false,
        },

        documentTypes: []

    },

    getters: {
        
        list: state => state.table_def,
        details: state => state.details,
        documents: state => state.documents,
        loading: state => state.loading,
        documentTypes: state => state.documentTypes

    },

    mutations: {

        [SAVE_ARTICLES_DETAILS](state, data) {
            state.details = data
        },

        [RESET_DETAILS](state) {
            state.details = {}
        },

        [ARTICLES_SET_PRODUCT_DOCUMENTS](state, data) {
            state.documents = data
        },

        [ARTICLES_UNSET_PRODUCT_DOCUMENT](state, id) {
            state.documents = state.documents.filter(document => document.id != id)
        },

        [SET_LOADING](state, payload) {
            state.loading.id = payload.id
            state.loading.status = payload.status
        },

        [SAVE_PRODUCT_DOCUMENT_TYPES](state, types) {
            state.documentTypes = types
        }
   
    },

    actions: {

        async [GET_ARTICLES_LIST]({ commit }, params) {

            return axios.post(`/get-articles-list`, params).then((response) => {
                return Promise.resolve(response)
                      
            }).catch((error) => {
                return  Promise.resolve(error)
            })

        },


        async [GET_ARTICLES_DETAILS]({ commit }, id) {

            try {
                const { data } = await axios.get(`/get-articles-details/${id}`)
                commit(SAVE_ARTICLES_DETAILS, data)
            }

            catch(e) {
                throw e
            }

        },

        async [ARTICLES_VALIDATE_PRODUCT]({ commit }, product) {
            
            try {
                await axios.post(`/validate-articles-product/${product.id}`, {
                    wholesale_price: product.wholesale_price
                })
            }

            catch(e) {
                throw e
            }

        },

        [ARTICLES_LOAD_PRODUCT_DOCUMENTS]({ commit, state }, data) {

            const { id, take = 3 } = data

            commit(SET_LOADING, { status: true, id: 'certification' })

            return axios.post(`/load-product-documents/${id}`, { take })
            .then((response) => {

                commit(ARTICLES_SET_PRODUCT_DOCUMENTS, response.data)
                commit(SET_LOADING, { status: false, id: 'certification' })
        
                return Promise.resolve(response)
                        
            })
            .catch((error) => {
                commit(SET_LOADING, { status: false, id: 'certification' })
                return Promise.resolve(error)
            })
        },

        [ARTICLES_REMOVE_DOCUMENT]({ commit, state }, document_id) {

            return axios.post(`/remove-product-document/${document_id}`)
            .then((response) => {
                commit(`${ARTICLES_UNSET_PRODUCT_DOCUMENT}`, document_id)
                return Promise.resolve(response)
            })
            .catch((error)=>{
                return Promise.resolve(error)
            })

        },

        [ARTICLES_GET_DOCUMENT_URL]({ commit, state }, document_id) {

            return axios.post(`/get-product-document-url/${document_id}`)
            .then((response)=>{
                window.location = `${response.data.document_url}`
                return Promise.resolve(response)
                      
            }).catch((error)=>{
                return Promise.resolve(error)
            })

        },

        [ARTICLES_UPLOAD_DOCUMENT]({ commit, state }, formData) {

            return axios.post(`/upload-product-document`, formData, {
              headers: {
                'Content-Type': 'multipart/form-data'
              }
            })
            .then((response) => {
        
               return dispatch(`${ARTICLES_LOAD_PRODUCT_DOCUMENTS}`, state.details.id)
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

        async [GET_PRODUCT_DOCUMENT_TYPES]({ commit, state }) {

            try {
                if(state.documentTypes.length) return
                const { data } = await axios.get('/get-product-document-types')
                commit(SAVE_PRODUCT_DOCUMENT_TYPES, data)
            }

            catch(e) {
                throw e
            }

        }


    }
}