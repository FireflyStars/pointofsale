import { INVOICESTATETAG_GET_LOADED, INVOICESTATETAG_GET_INVOICE_STATES, INVOICESTATETAG_LOAD_INVOICE_STATES, INVOICESTATETAG_SET_LOADED, INVOICESTATETAG_SET_INVOICE_STATES } from "../../types/types";


export const invoicestatetag= {
    namespaced:true,
    state: {
        loaded:false,
        invoice_states:[]
    },
    mutations: {
      [INVOICESTATETAG_SET_INVOICE_STATES]:(state,invoice_states)=>{
        state.invoice_states=invoice_states;
      },
      [INVOICESTATETAG_SET_LOADED]:(state)=>{
        state.loaded=true;
      }
    },
    actions: {
      [INVOICESTATETAG_LOAD_INVOICE_STATES]:async({commit,state,dispatch},params)=>{
        commit(INVOICESTATETAG_SET_LOADED);
        return axios.post(`/get-invoice-states`,params).then((response)=>{
          commit(INVOICESTATETAG_SET_INVOICE_STATES,response.data);
          return  Promise.resolve(response);
                
        }).catch((error)=>{
          return  Promise.resolve(error);
        });
    },
    },
    getters: {
        [INVOICESTATETAG_GET_INVOICE_STATES]: state => state.invoice_states,
        [INVOICESTATETAG_GET_LOADED]: state => state.loaded,
    }
}