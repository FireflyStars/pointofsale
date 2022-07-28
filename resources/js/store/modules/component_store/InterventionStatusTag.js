import { 
  INTERVENTION_LOAD_ORDER_STATES, 
  INTERVENTION_SET_LOADED, 
  INTERVENTION_SET_STATES 
} from "../../types/types";


export const InterventionStatusTag = {
    namespaced:true,
    state: {
        loaded: false,
        interventionStates: []
    },
    mutations: {
      [INTERVENTION_SET_STATES]:(state,order_states)=>{
        state.interventionStates = order_states;
      },
      [INTERVENTION_SET_LOADED]:(state, load = true)=>{
        state.loaded = load;
      }
    },
    actions: {
      [INTERVENTION_LOAD_ORDER_STATES]: async({ commit,state,dispatch }, params) => {
        commit(INTERVENTION_SET_LOADED);
        return axios.post(`/get-pointage-types`, params).then((response)=>{
          console.log(response.data)
          commit(INTERVENTION_SET_STATES, response.data);
          return  Promise.resolve(response);
                
        }).catch((error)=>{
          return  Promise.resolve(error);
        });
    },
    },
    getters: {
        interventionStates: state => state.interventionStates,
        loaded: state => state.loaded,
    }
}