<template>
    
    <div class="box-bottom-right bg-white p-3 shadow-sm">
                                        
        <customer-info :order="order" />

        <contact-info :order="order" />

        <template v-if="fetching">
            <div 
                v-for="n in 2"
                :key="n"
                class="d-flex gap-2 align-items-center" 
                :style="n == 1 ? 'margin-top: 3rem' : 'margin-top: 1.75rem'"
            >
                <loader type="icon-title" />
                <loader />
                <loader type="icon" />
            </div>

            <div 
                v-for="n in 2"
                :key="n"
                style="margin-top: 1.75rem"
            >
                <div class="d-flex gap-2 align-items-center">
                    <loader type="icon-title" />
                    <loader />
                </div>
                <div class="d-flex gap-2 mt-2 align-items-center">
                    <loader type="image" :count="4" />
                </div>
            </div>

            <div class="d-flex gap-2 align-items-center" style="margin-top: 1.75rem">
                <loader type="icon-title" />
                <loader />
                <loader type="icon" />
            </div>

        </template>
        
        <template v-else>

            <template v-if="'id' in order && order?.orderZones?.length">
                
                <template  
                    v-for="zone in order.orderZones"
                    :key="zone.id">
                    
                    <div>

                        <zone-info :zone="zone" />
                        
                        <camping-section :zone="zone" />

                        <ged-detail-image-grid-section 
                            v-if="zone?.gedDetails" 
                            :gedDetails="zone.gedDetails" 
                        />

                        <zone-comments-section 
                            v-if="zone?.orderZoneComments?.length"
                            :orderZoneComments="zone.orderZoneComments"
                        />

                    </div>

                    <order-ouvrages :orderOuvrages="zone.orderOuvrages" />    

                </template>

            </template>

        </template>

    </div>

    <div class="d-none">
        <input type="file" id="file" accept="image/*">
    </div>

</template>

<script>

import { useStore } from 'vuex'
import { computed, inject } from 'vue'

import zoneInfo from './zone-info'
import customerInfo from './customer-info'
import contactInfo from './contact-info'
import orderOuvrages from './order-ouvrages.vue'
import campingSection from './camping-section.vue'
import signatureSection from './signature-section'
import gedDetailFiles from './ged-detail-files.vue'
import zoneCommentsSection from './zone-comments-section.vue'
import gedDetailImageGridSection from './ged-detail-image-grid-section.vue'
import { BUILDER_MODULE } from '../../store/types/types'

export default {

    name: 'report-order-resource',

    components: {
        zoneInfo,
        customerInfo,
        contactInfo,
        orderOuvrages,
        campingSection,
        gedDetailFiles,
        zoneCommentsSection,
        gedDetailImageGridSection,
        signatureSection,
    },


    setup () {

        const store = useStore()
        const fetching = inject('fetching')
        const order = computed(() => store.getters[`${BUILDER_MODULE}/order`])

        const getCategoryClassName = (category) => {
            const categories = {
                installation: 'installation',
                securite: 'security',
                prestation: 'prestation'
            }
            return category !='' ? `${categories[category.toLowerCase()]}-section` : ''
        }

        return {
            order,
            fetching,
            getCategoryClassName,
        }
    }
}
</script>

<style lang="scss" scoped>

.box-bottom-right {
    margin-top: .75rem;
    margin-bottom: 2rem;
}

.black-box {
    border-radius: 8px;
    width: 3.43rem !important;
    height: 3.43rem !important;
    img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
}

</style>