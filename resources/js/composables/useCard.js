
import {
    CIBLE_MODULE,
    GET_CARD_QUANTITY
} from '../store/types/types'
import { useStore } from 'vuex'

import { computed } from 'vue'

export default function useCard () {

    const store = useStore()

    const cardQty = computed(() => {
        return store.getters[`${CIBLE_MODULE}cardQty`]
    })

    const getCardQty = async () => {
        try {
            await store.dispatch(`${CIBLE_MODULE}${GET_CARD_QUANTITY}`)
        }
        catch(e) {
            throw e
        }
    }

    return {
        cardQty,
        getCardQty
    }

}