

import {
    CREATE_NEW_PRODUCT,
    GET_PRODUCT_TYPES,
    SAVE_PRODUCT_TYPES
}
from '../types/types'

export const product = {

    namespaced: true,

    state: {
        product: {},
        types: []
    },

    getters: {
        product: state => state.product,
        types: state => state.types
    },

    mutations: {
        [SAVE_PRODUCT_TYPES](state, payload) {
            state.types = payload
        }
    },

    actions: {

        async [CREATE_NEW_PRODUCT]({ commit }, product) {

            try {
                const { data } = axios.post('/create-new-product', {
                    ...product
                })
            }

            catch(e) {
                throw e
            }

        },

        async [GET_PRODUCT_TYPES]({ commit }, product) {

            try {
                const { data } = axios.get('/get-product-types')
                commit(SAVE_PRODUCT_TYPES, data)
            }

            catch(e) {
                throw e
            }

        }

    }

}