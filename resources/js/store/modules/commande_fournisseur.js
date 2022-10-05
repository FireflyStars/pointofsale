
import axios from 'axios'
import {
    SET_LOADING,
    COMMANDE_FOURNISSEUR_LIST_MODULE,
    COMMANDE_FOURNISSEUR_GET_LIST,
    COMMANDE_FOURNISSEUR_GET_LIST_MES,
    FOURNISSEUR_SEARCH_LOAD_LIST,
    FOURNISSEUR_SET_SEARCH_LIST,
    FOURNISSEUR_SET_SEARCH_COUNT,
    CREATE_NEW_COMMANDE_FOURNISSEUR,
    COMMANDE_FOURNISSEUR_LOAD_ORDER_DETAILS,
    COMMMANDE_FOURNISSEUR_SAVE_DETAILS,
    COMMMANDE_FOURNISSEUR_SAVE_SUPPLIER_ORDER,
    COMMANDE_FOURNISSEUR_GET_SUPPLIER_ORDER,
    FOURNISSEUR_SEARCH_PRODUCTS,
    FOURNISSEUR_SET_PRODUCTS,
    FOURNISSEUR_SET_PRODUCT_SEARCH_COUNT,
    GET_PRODUCT_UNITS,
    SET_PRODUCT_UNITS,
    FOURNISSEUR_COMMANDE_CREATE_SUPPLIER_ORDER,
    SAVE_SUPPLIER_ORDER
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
        display_name: "No Commande",
        type: "string",
        class: "",
        header_class: "",
        sort: true,
        filter: true,   
        prefix: "",
        suffix: "",
        table: "supplier_orders"
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
                    // { col: 'id', value: 7 },
                  ], 
                  backgroundColor: '#f7c5af',
                  color: '#fd3b35'
                }
            ,
            item_route_name: "commande-fournisseur-details",// the route to trigger when a line is click 
            max_per_page: 10,//required          
            identifier: "commande_fournisseur_list_all",//required
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
            item_route_name: "commande-fournisseur-details",// the route to trigger when a line is click 
            max_per_page: 10,//required          
            identifier: "commande_fournisseur_list_mes",//required
            filter: true,// required boolean
            rearrange_columns: true,// required boolean
            columns_def

        },

        searchFournisseurs: [],
        countFournisseurs: 0,
        details: [],
        supplierOrderDetails: [],
        products: [],
        productCount: 0,
        productUnits: [],

    },

    getters: {

        list: state => state.table_def,
        userList: state => state.table_def_mes,
        searchFournisseurs: state => state.searchFournisseurs,
        countFournisseurs: state => state.countFournisseurs,
        details: state => state.details,
        supplierOrderDetails: state => state.supplierOrderDetails,
        products: state => state.products,
        productCount: state => state.productCount,
        productUnits: state => state.productUnits,

    },

    mutations: {
        [FOURNISSEUR_SET_SEARCH_LIST]: (state,payload) => state.searchFournisseurs = payload,
        [FOURNISSEUR_SET_SEARCH_COUNT]: (state,payload) => state.countFournisseurs = payload,
        [COMMMANDE_FOURNISSEUR_SAVE_SUPPLIER_ORDER]: (state, payload) => state.details = payload, 
        [COMMMANDE_FOURNISSEUR_SAVE_DETAILS]: (state, payload) => state.supplierOrderDetails = payload,
        [FOURNISSEUR_SET_PRODUCTS]: (state, payload) => state.products = payload,
        [FOURNISSEUR_SET_PRODUCT_SEARCH_COUNT]: (state, payload) => state.productCount = payload,
        [SET_PRODUCT_UNITS]: (state, payload) => state.productUnits = payload,
        [SAVE_SUPPLIER_ORDER]: (state, payload) => {
            state.supplierOrderDetails.push(payload)
        }
    },

    actions: {

        async [FOURNISSEUR_COMMANDE_CREATE_SUPPLIER_ORDER]({ commit }, payload) {

            try {

                const { supplierOrder, orderId } = payload

                const { data } = await axios.post(`/fournisseur-commande-create-supplier-order/${orderId}`, {
                    ...supplierOrder
                })

                commit(SAVE_SUPPLIER_ORDER, data)

            }

            catch(e) {
                throw e
            }

        },

        async [GET_PRODUCT_UNITS]({ state, commit }) {
            try {
                if(state.productUnits.length) return
                const { data } = await axios.get('/get-product-units')
                commit(SET_PRODUCT_UNITS, data)    
            }
            catch(e) {
                throw e
            }
        },

        [FOURNISSEUR_SEARCH_LOAD_LIST]: async ({commit,state}, payload )=>{
            return axios.post('/search-fournisseur', payload)
            .then( (response)=>{
                if(response.data != null) {
                    commit(FOURNISSEUR_SET_SEARCH_LIST, response.data.data);
                    commit(FOURNISSEUR_SET_SEARCH_COUNT, ( response.data.total - 5 ) )
                }
                return Promise.resolve(response)
            })
            .catch((error)=>{
                return Promise.reject(error)
            }).finally(()=>{

            })
        },

        [FOURNISSEUR_SEARCH_PRODUCTS]: async ({ commit, state }, payload ) => {
            return axios.post('/search-products', payload)
            .then( (response)=>{
                if(response.data != null) {
                    commit(FOURNISSEUR_SET_PRODUCTS, response.data.data);
                    commit(FOURNISSEUR_SET_PRODUCT_SEARCH_COUNT, ( response.data.total - 5 ) )
                }
                return Promise.resolve(response)
            })
            .catch((error)=>{
                return Promise.reject(error)
            }).finally(()=>{

            })
        },

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

        async [CREATE_NEW_COMMANDE_FOURNISSEUR]({ commit }, fournisseur) {

            try {

                return axios.post(`/commande-fournisseur/create/${fournisseur}`)
                .then(({ data })=>{
                }).catch((errors)=>{
                    console.log(errors);
                })

            }

            catch(e) {
                throw e
            }

        },

        async [COMMANDE_FOURNISSEUR_GET_SUPPLIER_ORDER]({ commit }, id) {

            try {

                const { data } = await axios.get(`/get-commande-fournisseur-supplier-order/${id}`)
                commit(COMMMANDE_FOURNISSEUR_SAVE_SUPPLIER_ORDER, data)
            }

            catch(e) {
                throw e
            }

        },

        async [COMMANDE_FOURNISSEUR_LOAD_ORDER_DETAILS]({ commit }, payload) {

            try {
                const { id, take } = payload
                const { data } = await axios.get(`/get-commande-fournisseur-order-details/${id}`, {
                    params: {
                        take
                    }
                })
                console.log(data, " is supplier order data")
                commit(COMMMANDE_FOURNISSEUR_SAVE_DETAILS, data)
            }

            catch(e) {
                throw e
            }

        }

    }

}