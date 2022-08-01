import { 
  PAIEMENT_LOAD_STATES, 
  PAIEMENT_SET_LOADED, 
  PAIEMENT_SET_STATES 
} from "../../types/types";


export const paiementStatusTag = {
    namespaced:true,
    state: {
        loaded: false,
        paiementStates: []
    },
    mutations: {
      [PAIEMENT_SET_STATES]:(state, states)=>{
        state.paiementStates = states;
      },
      [PAIEMENT_SET_LOADED]:(state)=>{
        state.loaded = true
      }
    },
    actions: {
      [PAIEMENT_LOAD_STATES]: async({ commit,state,dispatch }, params) => {
        commit(PAIEMENT_SET_LOADED)
        return axios.post(`/get-paiements-types`, params).then((response)=>{
          commit(PAIEMENT_SET_STATES, response.data);
          return  Promise.resolve(response);
                
        }).catch((error)=>{
          return  Promise.resolve(error);
        });
    },
    },
    getters: {
        states: state => state.paiementStates,
        loaded: state => state.loaded,
    }
}