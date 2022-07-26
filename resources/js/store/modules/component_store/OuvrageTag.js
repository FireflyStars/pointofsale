import { 
  OUVRAGE_STATE_MODULE,
  OUVRAGE_GET_LOADED, 
  OUVRAGE_GET_TAG_STATES, 
  OUVRAGE_LOAD_TAG_STATES, 
  OUVRAGE_SET_LOADED, 
  OUVRAGE_SET_TAG_STATES 
} from "../../types/types"


export const ouvrageTag = {
    namespaced:true,
    state: {
        loaded: false,
        states:[]
    },
    getters: {
        states: state => state.states,
        loaded: state => state.loaded,
    },
    mutations: {
      [OUVRAGE_SET_TAG_STATES]: (state, tag_states) => {
        state.states = tag_states
      },
      [OUVRAGE_SET_LOADED]: (state) => {
        state.loaded = true
      }
    },
    actions: {
      [OUVRAGE_LOAD_TAG_STATES]: async({ commit,state,dispatch }, payload) => {
          const { route, params = {} } = payload
          commit(OUVRAGE_SET_LOADED)
          return axios.post(route, params).then((response)=>{
            commit(OUVRAGE_SET_TAG_STATES,response.data)
            return  Promise.resolve(response)
                  
          }).catch((error)=>{
            return  Promise.resolve(error)
          })
      },
    },
    
}