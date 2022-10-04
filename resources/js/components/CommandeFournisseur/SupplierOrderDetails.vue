<template>
    <transition
        enter-active-class="animate__animated animate__fadeIn"
        leave-active-class="animate__animated animate__fadeOut"
    >
        <mini-panel v-if="details?.length" class="mt-3">

            <div class="position-relative">
            
                <a 
                    href="#" 
                    class="link d-flex align-items-center"
                    style="gap: 0.4rem;" 
                    @click.prevent="getDetails"
                    v-if="fetched === false"
                >
                    Voir tout
                    <Icon 
                        class="icon"
                        name="spinner"
                        v-show="loading?.status == true && loading?.id == 'supplierOrderDetails'"
                    />
                </a>

                <div class="row mb-3 mt-4">
                    <div class="col-4 almarai_700_normal font-14 lcdtgrey d-flex align-items-center">Detail Commande</div>
                    <div class="col-2 almarai_700_normal font-14 d-flex align-items-center"></div>
                    <div class="col-2"></div>
                </div>

                <div class="row mb-3">
                    <div class="col-4 almarai-light lcdtgrey d-flex font-12 align-items-center">Description</div>
                    <div class="col-1 almarai-light lcdtgrey d-flex font-12 align-items-center justify-content-center">Prix unitaitaire</div>
                    <div class="col-1 almarai-light lcdtgrey d-flex font-12 align-items-center justify-content-center">Quantité</div>
                    <div class="col-2 almarai-light lcdtgrey d-flex font-12 align-items-center justify-content-center">Unité</div>
                    <div class="col-2 almarai-light lcdtgrey d-flex font-12 align-items-center justify-content-center">Type</div>
                    <div class="col-2 almarai-light lcdtgrey d-flex font-12 align-items-center justify-content-center">Total</div>
                </div>

                <transition
                    enter-active-class="animate__animated animate__fadeIn"
                    leave-active-class="animate__animated animate__fadeOut"
                >

                    <div>

                        <div 
                            class="row" 
                            v-for="detail in details" 
                            :key="detail.id" style="margin-top: .5rem;"
                        >
                            
                            <div class="col-4 title-bold">{{ slice(detail.product?.name, 25) }}</div>
                            <div class="col-1 almarai-light lcdtgrey d-flex font-12 align-items-center justify-content-center">
                                &euro;{{ detail.unitprice }}
                            </div>
                            <div class="col-1 almarai-light lcdtgrey d-flex font-12 align-items-center justify-content-center">
                                {{ detail.qty }}
                            </div>
                            <div class="col-2 almarai-light lcdtgrey d-flex font-12 align-items-center justify-content-center">
                                {{ detail.product?.type }}
                            </div>
                            <div 
                                class="col-2 almarai-light lcdtgrey d-flex font-12 align-items-center justify-content-center" 
                            >
                                <span class="tag">{{ detail.unit?.name }}</span>
                            </div>
                            <div class="col-2 almarai-light lcdtgrey d-flex font-12 align-items-center justify-content-center">
                                &euro;{{ detail.qty * detail.unitprice }}
                            </div>
                        
                        </div>

                        <div class="d-flex justify-content-evenly mt-4" @click.prevent="TriggerNewDetail">
                            <span 
                                class="font-14 mulish_600_normal facture_action noselect" 
                            >
                                <icon name="plus-circle" width="16px" height="16px" /> 
                                AJOUTER LIGNE
                            </span>
                        </div>  

                    </div>


                </transition>

            </div>


        </mini-panel>
                
    </transition>
</template>

<script>
export default {
    name: 'detail-list'
}
</script>

<script setup>

    import moment from 'moment'
    import { useStore } from 'vuex'
    import { ref, reactive, computed, onMounted } from 'vue'
    import MiniPanel from '../miscellaneous/MiniPanel.vue'
    import useHelpers from '../../composables/useHelpers'

    import {
        COMMANDE_FOURNISSEUR_LIST_MODULE,
        COMMANDE_FOURNISSEUR_LOAD_ORDER_DETAILS,
    } from '../../store/types/types'

    const store = useStore()
    
    const props = defineProps({
        supplierOrderId: {
            required: true,
            type: [Number, String]
        }
    })

    const emit = defineEmits(['trigger'])

    const { slice } = useHelpers()
    const fetched = ref(false)

    const loading = reactive({
        status: false,
        id: 'supplierOrderDetails'
    })

    const details = computed(() => store.getters[`${COMMANDE_FOURNISSEUR_LIST_MODULE}supplierOrderDetails`])

    const getDetails = async () => {
         try {
            loading.status = true
            await store.dispatch(`${COMMANDE_FOURNISSEUR_LIST_MODULE}${COMMANDE_FOURNISSEUR_LOAD_ORDER_DETAILS}`, { id: props.supplierOrderId,  take: 'all' })
            fetched.value = true
        }
        catch(e) {
            throw e
        }
        finally {
            loading.status = false
        }
    }

    const TriggerNewDetail = () => {
        emit('trigger')
    }

    onMounted(() => getDetails())


</script>

<style lang="scss" scoped>

.link {
    position: absolute;
    top: -1.3rem;
    right: 0;
    font-family: 'Almarai Regular';
    font-style: normal;
    font-weight: 400 !important;
    font-size: 14px;
    line-height: 140%;
    display: flex;
    align-items: flex-end;
    color: #3E9A4D;
}

.lcdtgrey {
    color:var(--lcdtGrey);
}
.title-bold {
    font-family: 'Almarai Regular';
    font-style: normal;
    font-weight: 700;
    font-size: 14px;
    line-height: 140%;
    display: flex;
    align-items: flex-end;
    color: #000000;
}

.tag {
    text-transform: capitalize;
    background: #DDD;
    border-radius: 70px;
    text-align: center;
    font-size: 12px;
    height: 24px;
    position: relative;
    display: inline-block;
    vertical-align: middle;
    line-height: 24px;
    transition: all 0.5s ease-in;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    padding: 0 10px;
}

.list-enter-from {
    opacity: 0;
    transform: scale(0.6);
}
.list-enter-to {
    opacity: 1;
    transform: scale(1);
}
.list-enter-active {
    transition: all 1s ease;
}

.list-leave-from {
    transform-origin: right center;
    opacity: 1;
    transform: scale(1);

}
.list-leave-to {
    transform-origin: right center;
    opacity: 0;
    transform: scale(0.6);
}
.list-leave-active {
            transition: all 1s ease;
        transform-origin: right center;
    position:absolute;     
    width: 100%;
}
.list-move {
    transition:all 0.3s ease;
}

h2 {
    font-size: 16px;
    line-height: 20px;
    margin-bottom: 8px;
}
h3 {
    font-size: 14px;
    line-height: 14px;
    margin-bottom: 8px;
}

hr {
    margin:25px -20px;
    background-color: #E0E0E0;

}
.subtitle {
    color:#C3C3C3;
    font-size:14px;
    font-weight: 400!important;
}

.facture_action {
    color:#E8581B;
    cursor: pointer;
}

</style>