import { FACTURE_DETAIL_ADD_PAYMENT, FACTURE_DETAIL_GET, FACTURE_DETAIL_GET_PAYMENTS, FACTURE_DETAIL_GET_PAYMENT_STATES, FACTURE_DETAIL_GET_PAYMENT_TYPES, FACTURE_DETAIL_LOAD, FACTURE_DETAIL_LOAD_PAYMENTS, FACTURE_DETAIL_REMOVE_PAYMENT, FACTURE_DETAIL_SET, FACTURE_DETAIL_SET_PAYMENT, FACTURE_DETAIL_SET_PAYMENTS, FACTURE_DETAIL_SET_PAYMENT_STATES, FACTURE_DETAIL_SET_PAYMENT_TYPES, FACTURE_DETAIL_UNSET_PAYMENT } from "../types/types";

 

 
export const facturedetail= {
    namespaced:true,
    state: {
        invoice:{},
        payment_module:{
          payments:[],
          payment_states:[],
          payment_types:[]
        }
        },
    mutations: {
        [FACTURE_DETAIL_SET]:(state,invoice)=>state.invoice=invoice,
        [FACTURE_DETAIL_SET_PAYMENTS]:(state,payments)=>state.payment_module.payments=payments,
        [FACTURE_DETAIL_SET_PAYMENT_STATES]:(state,payment_states)=>state.payment_module.payment_states=payment_states,
        [FACTURE_DETAIL_SET_PAYMENT_TYPES]:(state,payment_types)=>state.payment_module.payment_types=payment_types,
        [FACTURE_DETAIL_SET_PAYMENT]:(state,payment)=>state.payment_module.payments.push(payment),
        [FACTURE_DETAIL_UNSET_PAYMENT]:(state,payment_id)=>state.payment_module.payments=state.payment_module.payments.filter(obj=>obj.id!=payment_id),
       
    },
    actions: {
        [FACTURE_DETAIL_LOAD]:async({commit},invoice_id)=>{
            return axios.post(`/get-invoice-detail`,{invoice_id:invoice_id}).then((response)=>{
                commit(FACTURE_DETAIL_SET,response.data);
                return  Promise.resolve(response);
                      
              }).catch((error)=>{
                return  Promise.resolve(error);
              });
        },
        [FACTURE_DETAIL_LOAD_PAYMENTS]:async({commit},invoice_id)=>{
          return axios.post(`/get-invoice-payments`,{invoice_id:invoice_id}).then((response)=>{
              commit(FACTURE_DETAIL_SET_PAYMENTS,response.data.paiements);
              commit(FACTURE_DETAIL_SET_PAYMENT_STATES,response.data.paiement_states);
              commit(FACTURE_DETAIL_SET_PAYMENT_TYPES,response.data.paiement_types);
              return  Promise.resolve(response);
                    
            }).catch((error)=>{
              return  Promise.resolve(error);
            });
      },
      [FACTURE_DETAIL_ADD_PAYMENT]:async({commit},paiement)=>{
        return axios.post(`/add-invoice-payment`,{paiement}).then((response)=>{
            commit(FACTURE_DETAIL_SET_PAYMENT,response.data);
            return  Promise.resolve(response);
                  
          }).catch((error)=>{
            return  Promise.resolve(error);
          });
    },
    [FACTURE_DETAIL_REMOVE_PAYMENT]:async({commit},paiement_id)=>{
      return axios.post(`/remove-invoice-payment`,{paiement_id:paiement_id}).then((response)=>{
          commit(FACTURE_DETAIL_UNSET_PAYMENT,paiement_id);
          return  Promise.resolve(response);
                
        }).catch((error)=>{
          return  Promise.resolve(error);
        });
  },
    },
    getters: {
        [FACTURE_DETAIL_GET]: state => state.invoice,
        [FACTURE_DETAIL_GET_PAYMENTS]: state => state.payment_module.payments,
        [FACTURE_DETAIL_GET_PAYMENT_STATES]:state=>state.payment_module.payment_states,
        [FACTURE_DETAIL_GET_PAYMENT_TYPES]:state=>state.payment_module.payment_types,
    }
}