import { 
  ORDERSTATETAG_GET_LOADED, 
  ORDERSTATETAG_GET_ORDER_STATES, 
  ORDERSTATETAG_LOAD_ORDER_STATES, 
  ORDERSTATETAG_SET_LOADED, 
  ORDERSTATETAG_SET_ORDER_STATES 
} from "../../types/types";


export const commandeFournisseurStatusTag = {
    namespaced:true,
    state: {
        loaded: false,
        order_states:[]
    },
    mutations: {
      [ORDERSTATETAG_SET_ORDER_STATES]:(state,order_states)=>{
        state.order_states=order_states;
      },
      [ORDERSTATETAG_SET_LOADED]:(state)=>{
        state.loaded=true;
      }
    },
    actions: {
      [ORDERSTATETAG_LOAD_ORDER_STATES]: async({ commit,state,dispatch }, params) => {
        commit(ORDERSTATETAG_SET_LOADED);
        return axios.post(`/get-commande-fournisseur-statuses`,params).then((response)=>{
          commit(ORDERSTATETAG_SET_ORDER_STATES,response.data);
          return  Promise.resolve(response);
                
        }).catch((error)=>{
          return  Promise.resolve(error);
        });
    },
    },
    getters: {
        orderStates: state => state.order_states,
        loaded: state => state.loaded,
    }
}