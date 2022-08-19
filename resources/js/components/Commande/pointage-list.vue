<template>
    <transition
        enter-active-class="animate__animated animate__fadeIn"
        leave-active-class="animate__animated animate__fadeOut"
    >
        <mini-panel v-if="pointages?.length">

            <div class="position-relative">
            
                <a 
                    href="#" 
                    class="link d-flex align-items-center"
                    style="gap: 0.4rem;" 
                    @click.prevent="getPointages"
                    v-if="fetched === false"
                >
                    Voir tout
                    <Icon 
                        class="icon"
                        name="spinner"
                        v-show="loading?.status == true && loading?.id == 'pointages'"
                    />
                </a>

                <div class="row mb-3 mt-4">
                    <div class="col-4 almarai_700_normal font-14 lcdtgrey d-flex align-items-center">Pointage</div>
                    <div class="col-2 almarai_700_normal font-14 d-flex align-items-center"></div>
                    <div class="col-2"></div>
                    <div class="col-4 almarai_700_normal font-14 d-flex align-items-center justify-content-end">{{ pointageTotal }} H</div>
                </div>

                <div class="row mb-3">
                    <div class="col-4"></div>
                    <div class="col-2 almarai-light lcdtgrey d-flex font-12 align-items-center">Date</div>
                    <div class="col-2 almarai-light lcdtgrey d-flex font-12 align-items-center justify-content-center">Nombre H</div>
                    <div class="col-2 almarai-light lcdtgrey d-flex font-12 align-items-center justify-content-center">Type</div>
                    <div class="col-2 almarai-light lcdtgrey d-flex font-12 align-items-center justify-content-center">Commentaire</div>
                </div>

                <transition
                    enter-active-class="animate__animated animate__fadeIn"
                    leave-active-class="animate__animated animate__fadeOut"
                >

                    <div>

                        <div class="row" v-for="pointage in pointages" :key="pointage.id" style="margin-top: .5rem;">
                            
                            <div class="col-4 title-bold">{{ pointage.user?.name }}</div>
                            <div class="col-2 almarai-light lcdtgrey d-flex font-12 align-items-center">
                                {{ pointage.datepointage ? moment(pointage.datepointage).format('LL') : '' }}
                            </div>
                            <div class="col-2 almarai-light lcdtgrey d-flex font-12 align-items-center justify-content-center">
                                {{ pointage.numberh }}
                            </div>
                            <div 
                                class="col-2 almarai-light lcdtgrey d-flex font-12 align-items-center justify-content-center tag" 
                                :style="{ background: pointage.type?.color }"
                            >
                                {{ pointage.type?.name }}
                            </div>
                            <div class="col-2 almarai-light lcdtgrey d-flex font-12 align-items-center justify-content-center">
                                {{ pointage.comment }}
                            </div>
                        
                        </div>

                        <div class="d-flex justify-content-evenly mt-4" @click.prevent="TriggerNewPointage">
                            <span 
                                class="font-14 mulish_600_normal facture_action noselect" 
                            >
                                <icon name="plus-circle" width="16px" height="16px" /> 
                                AJOUTER POINTAGE
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
    name: 'pointage-list'
}
</script>

<script setup>

    import moment from 'moment'
    import { ref, reactive, computed } from 'vue'
    import { useStore } from 'vuex'
    import MiniPanel from '../miscellaneous/MiniPanel.vue'

    import {
        COMMANDE_DETAIL_MODULE,
        COMMANDE_DETAIL_LOAD_POINTAGE
    } from '../../store/types/types'

    const store = useStore()
    
    const props = defineProps({
        orderId: {
            required: true,
            type: [Number, String]
        }
    })

    const emit = defineEmits(['trigger'])

    const fetched = ref(false)

    const loading = reactive({
        status: false,
        id: 'pointages'
    })

    const pointages = computed(() => store.getters[`${COMMANDE_DETAIL_MODULE}pointages`])

    const pointageTotal = computed(() => {
        return pointages.value.map(pointage => pointage.numberh)
                            .reduce((newValue, oldValue) => +newValue + +oldValue, 0)
    })

    const getPointages = async () => {
         try {
            loading.status = true
            await store.dispatch(`${COMMANDE_DETAIL_MODULE}${COMMANDE_DETAIL_LOAD_POINTAGE}`, { id: props.orderId,  take: 'all' })
            fetched.value = true
        }
        catch(e) {
            throw e
        }
        finally {
            loading.status = false
        }
    }

    const TriggerNewPointage = () => {
        emit('trigger')
    }


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