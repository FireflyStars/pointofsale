<template>
    <Modal 
        id="change-fond"
        size="lg"
        style="margin-top: 3rem;"
    >
        <div>
            
            <div class="d-flex align-items-center gap-5">
                
                <h4 class="d-flex align-items-center gap-3">
                    <Icon name="report" width="32" height="32" />
                    Change Fond
                </h4>
                
                <BaseButton 
                    title="Votre Image"
                    @click="promptImage(true)"
                />
            
            </div>

            <div class="mt-3"> 

                <div class="background-container">

                    <div class="right">

                        <div class="background-boxes mt-4">
                            <div 
                                v-for="reportTemplate in reportTemplates"
                                :key="reportTemplate"
                            >

                                <div 
                                    v-for="template in reportTemplate" 
                                    :key="template"
                                >

                                    <div 
                                        class="background-box"
                                        :class="{ 'background-box--active': isActivePageBackground(template) }"
                                        @click.prevent="generateElement('background', {
                                            filename: `/images/${template}`,
                                            classes: 'page-background',
                                            image: `/images/${template}`
                                        })"
                                    >
                                        <img 
                                            :src="`/images/${template}`" 
                                            alt="Report Template"
                                        >
                                    </div>
                                
                                </div>

                            </div>
                        </div>


                    </div>

                </div>

                <div class="d-flex align-items-center justify-content-around mt-5">
                    <BaseButton 
                        title="Valider"
                        kind="green"
                        @click="toggleModal('change-fond', false)"
                    />
                    <BaseButton 
                        title="Non"
                        @click="toggleModal('change-fond', false)"
                    />
                </div>

            </div>
        </div>
    </Modal>

</template>

<script setup>

import { useStore } from 'vuex'
import { computed, inject } from 'vue'
import useModal from '../../composables/useModal'
import { 
    BUILDER_MODULE,
} from '../../store/types/types'

        
const store = useStore()
const generateElement = inject('generateElement')
const promptImage = inject('promptImage')

const { toggleModal } = useModal()

const page = computed(() => store.getters[`${BUILDER_MODULE}/page`])

const reportTemplates = computed(() => {
    return [
        [
            'page0.jpg',
            'page1.jpg',
            'page2.jpg',
            'page3.jpg',
            'page4.jpg',
            'page5.jpg',
            'page6.jpg',
            'page7.jpg',
            'page8.jpg',
        ],
        [
            'page9.jpg',
            'page10.jpg',
            'page11.jpg',
            'page12.jpg',
            'page13.jpg',
            'page14.jpg',
            'page15.jpg',
        ],
        [
            'page16.jpg',
            'page17.jpg',
            'page18.jpg',
            'page19.jpg',
            'page20.jpg',
            'page21.jpg',
            'page22.jpg',
        ],
        [
            'page23.jpg',
            'page24.jpg',
            'page25.jpg',
            'page26.jpg',
            'page27.jpg',
            'page28.jpg',
            'page29.jpg',
        ],
        [
            'page30.jpg',
            'page31.jpg',
            'page32.jpg',
            'page33.jpg',
            'page34.jpg',
            'page35.jpg',
            'page36.jpg',
        ],
        [
            'page37.jpg',
            'page38.jpg',
            'page39.jpg',
            'page40.jpg',
            'page41.jpg',
            'page42.jpg',
            'page43.jpg',
        ],
        [
            'page44.jpg',
            'page45.jpg',
            'page46.jpg',
            'page47.jpg',
            'page48.jpg',
            'page49.jpg',
            'page50.jpg',
        ],
        [
            'page51.jpg',
            'page52.jpg',
            'page53.jpg',
            'page54.jpg',
            'page55.jpg',
            'page56.jpg',
            'page57.jpg',
        ],
        [
            'page58.jpg',
            'page59.jpg',
            'page60.jpg',
            'page61.jpg',
            'page62.jpg',
            'page63.jpg',
            'page64.jpg',
            'page65.jpg',
            'page66.jpg',
        ]

    ]
})

const isActivePageBackground = (template) => {
    return page.value?.background?.dataFile == `/images/${template}`
}

        
</script>

<style lang="scss" scoped>

.background-container {
    display: flex;
    justify-content: center;
}

.background-boxes {

    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: .5rem;
    text-align: center;
    font-family: 'Poppins', sans-serif;
    font-style: normal;
    font-weight: 600;
    font-size: 16px;
    line-height: 22px;
    color: #000000;

    .background-box {
        width: 12rem;
        height: 12rem;
        border-radius: 8px;
        padding: 4px;
        border: 1px solid #ccc;
        cursor: pointer;
        
        margin: 10px 2px;
        &--active {
            border: 3px solid orange;
        }
        &:hover {
            border: 3px solid orange;
        }
        img {
            width: 100%;
            height: 100%;
            transition: .4s all ease-in-out;
            &:hover {
                transform: scale(2);
            }
        }
    }
}

</style>