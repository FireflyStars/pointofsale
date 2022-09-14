import {
    CREATE_INVOICE,
    CREATE_NEW_INVOICE,
    SAVE_INVOICE,
    GET_TAX_LIST,
    SAVE_TAX_LIST,
    CREATE_LIGNE,
    UPDATE_LIGNE,
    DELETE_LIGNE
    
} from "../types/types";
import axios from 'axios';

export const invoice = {

    namespaced: true,
  
    state: {
        invoice: {},
        taxList: []
    },

    getters: {

        invoice: state => state.invoice,
        taxList: state => state.taxList

    },

    mutations: {

        [SAVE_INVOICE](state, data) {
            state.invoice = data
        },
        [SAVE_TAX_LIST](state, data) {
            state.taxList = data
        },

        [UPDATE_LIGNE](state, data) {
            state.invoice.invoice.details.push(data)
        },

        [DELETE_LIGNE](state, data) {
            state.invoice.invoice.details = data
        }

    },

    actions: {

        async [DELETE_LIGNE]({ commit }, { id, invoiceId }) {

            try {

                const { data } = await axios.delete(`/delete-ligne/${id}`, {
                    invoice_id: invoiceId
                })

                commit(DELETE_LIGNE, data)

            }

            catch(e) {
                throw e
            }

        },

        async [CREATE_LIGNE]({ commit }, { ligne, id }) {

            try {

                const { data } = await axios.post(`/create-ligne/${id}`, ligne)

                commit(UPDATE_LIGNE, data)

            }

            catch(e) {
                throw e
            }

        },

        async [GET_TAX_LIST]({ commit, state }) {
            
            if(state.taxList.length) return

            try {

                const { data } = await axios.get('/get-tax-list')

                commit(SAVE_TAX_LIST, data)

            }

            catch(e) {
                throw e
            }

        },

        async [CREATE_INVOICE]({ commit,state }, payload) {

            const { customerId, invoiceId, orderId, type } = payload

            try {

                const { data } = await axios.post('/create-invoice', {
                    customer_id: customerId,
                    invoice_id: invoiceId,
                    order_id: orderId,
                    invoice_type: type
                })

                commit(SAVE_INVOICE, data)

            }

            catch(e) {
                throw e
            }


        },

        async [CREATE_NEW_INVOICE]({ commit,state }, payload) {

            const { customerId, invoiceId, orderId, type } = payload

            try {

                const { data } = await axios.post('/create-new-invoice', {
                    customer_id: customerId,
                    invoice_type: type
                })

                commit(SAVE_INVOICE, data)

            }

            catch(e) {
                throw e
            }


        },

    }
    
}