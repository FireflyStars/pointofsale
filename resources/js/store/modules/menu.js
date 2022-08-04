
import { 
    MENU_ITEMS_MODULE,
    GET_MENU_ITEMS,
    SAVE_MENU_ITEMS
} from '../types/types'

export const menu = {
    
    namespaced: true,

    state: {
        items: []
    },

    getters: {
        items: state => state.items
    },

    mutations: {

        SAVE_MENU_ITEMS(state, data) {
            state.items = data
        }

    },

    actions: {

        async [GET_MENU_ITEMS]({ state, commit }) {

            try {
                if(state.items.length) return
                const { data } = await axios.get('/get-menu-items')
                commit(SAVE_MENU_ITEMS, data)
            }

            catch(e) {
                throw e
            }

        }

    }

}