import { 
  POINTAGE_LOAD_ORDER_STATES, 
  POINTAGE_SET_LOADED, 
  POINTAGE_SET_STATES 
} from "../../types/types";


export const pointageStatusTag = {
    namespaced:true,
    state: {
        loaded: false,
        pointageStates: []
    },
    mutations: {
      [POINTAGE_SET_STATES]:(state,order_states)=>{
        state.pointageStates = order_states;
      },
      [POINTAGE_SET_LOADED]:(state, load = true)=>{
        state.loaded = load;
      }
    },
    actions: {

      [POINTAGE_LOAD_ORDER_STATES]: async({ commit, state, dispatch }, params) => {

        commit(POINTAGE_SET_LOADED)

        return axios.post(`/get-pointage-types`, params)
        .then((response) => {

          commit(POINTAGE_SET_STATES, response.data);
          return  Promise.resolve(response);
                
        }).catch((error)=>{
          return  Promise.resolve(error);
        })

    },
    },
    getters: {
        pointageStates: state => state.pointageStates,
        loaded: state => state.loaded,
    }
}