import {DEVIS_DETAIL_LOAD, DEVIS_DETAIL_GET, DEVIS_DETAIL_SET, DEVIS_DETAIL_UPDATE_ORDER_STATE, DEVIS_DETAIL_SET_ORDER_STATE} from '../types/types'

export const devisdetail= {
    namespaced:true,
    state: {
        order:{}
    },
    mutations: {
        [DEVIS_DETAIL_SET]:(state,order)=>state.order=order,
        [DEVIS_DETAIL_SET_ORDER_STATE]:(state,order_state_id)=>state.order.order_state_id=order_state_id,
    },
    actions: {
        [DEVIS_DETAIL_LOAD]:async({commit},order_id)=>{
            return axios.post(`/get-order-detail`,{order_id:order_id}).then((response)=>{
                commit(DEVIS_DETAIL_SET,response.data);
                return  Promise.resolve(response);
                      
              }).catch((error)=>{
                return  Promise.resolve(error);
              });
        },

        [DEVIS_DETAIL_UPDATE_ORDER_STATE]:async({commit,state},order_state_id)=>{
            return axios.post(`/set-order-state`,{order_id:state.order.id,order_state_id:order_state_id}).then((response)=>{
                commit(DEVIS_DETAIL_SET_ORDER_STATE,order_state_id);
                return  Promise.resolve(response);
                      
              }).catch((error)=>{
                return  Promise.resolve(error);
              });
        }
 
    },
    getters: {
        [DEVIS_DETAIL_GET]: state => state.order,
    }
}