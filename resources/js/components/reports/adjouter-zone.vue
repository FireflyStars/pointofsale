<template>
    
    <div class="box-top-right d-flex flex-wrap align-items-center shadow-sm">

        <div class="row m-0">

            <template v-if="fetching && false">
                <div class="col-5">
                    <div 
                        v-for="n in 2"
                        :key="n"
                        class="d-flex align-items-center gap-2"
                    >
                        <loader type="icon-title" />
                        <loader />
                    </div>
                </div>

                <div class="col-7">
                    <div class="d-flex align-items-center gap-6">
                        <loader type="icon-title" :count="3" />
                        <loader />
                    </div>
                    <div class="d-flex justify-content-center align-items-center gap-2">
                        <loader type="button" :count="2" />
                    </div>
                </div>

            </template>

            <template v-else>

                <div 
                    :class="showRightContainer ? 'col-5' : 'col-3'" 
                >
                    <div 
                    class="pointer" 
                    @click="generateElement('textarea', { 
                        class: 'title-bar',
                        content: `VUE D'ENSEMBLE DUE BATIMENT`
                    })">
                        <img src="/images/tag.svg" />
                        <span v-show="showRightContainer">
                            <p 
                                class="d-inline orange text-base" 
                                style="margin-left: .7rem;"
                            >
                                Ajouter Titre
                            </p>
                        </span>
                    </div>
                    <div 
                    @click="generateElement('textarea', {
                        content: 'Votre TEXTE'
                    })" 
                    class="pointer" style="margin-top: 1rem">
                        <img src="/images/notepad.svg" />
                        <span v-show="showRightContainer">
                            <p 
                                class="d-inline orange text-base" 
                                style="margin-left: .7rem"
                                
                            >
                                Ajouter Zone text
                            </p>
                        </span>
                    </div>
                </div>
                
                <div 
                    :class="showRightContainer ? 'col-7' : 'col-9'" 
                >
                    
                    <div class="d-flex flex-wrap align-items-center gap-6">

                        <Icon
                            v-for="icon in libraryIcons"
                            :key="icon" 
                            class="library-item"
                            :name="icon"
                            :height="icon == 'arrow-right' ? 30 : 20"
                            color="black"
                            @click="generateElement('icon', { id: 'Icon', name: icon })"
                        />
                        <a 
                            href="#" 
                            class="orange text text-base self-end"
                            @click.prevent="generateElement('table')"
                        >
                            <Icon name="table" />
                        </a>
                    </div>

                    <div v-show="showRightContainer">
                        <div class="d-flex align-items-center">
                            
                            <BaseButton 
                                title="Ajouter Image"
                                class="image-button"
                                @click.prevent="promptImage"
                            />

                            <BaseButton 
                                title="Bank Image"
                                class="image-button"
                            />            

                        </div>
                    </div>


                </div>

            </template>

        </div>
    </div>

</template>

<script>

import { inject } from 'vue'

export default {
    
    name: 'adjouter-zone',

    setup () {
        
        const fetching = inject('fetching')
        const promptImage = inject('promptImage')
        const generateElement = inject('generateElement')
        const showRightContainer = inject('showRightContainer') || true

        const libraryIcons = ['arrow-right', 'egg', 'comment-box']

        return {
            fetching,
            promptImage,
            libraryIcons,
            generateElement,
            showRightContainer
        }
    }

}

</script>

<style lang="scss" scoped>
.box-top-right {
    background: #fff;
    padding: 1rem;
    overflow: hidden;
    .image-button {
        background: rgba(168, 168, 168, 0.25);
        border-radius: 10px;
        border: 1px solid rgba(168, 168, 168, 0.25) !important;
        padding: 0;
        width: 8.75rem;
        height: 2.03rem;
        display: flex;
        place-content: center;
        margin: 1.6rem .3rem;
    }    
}
.text {
    font-family: 'Poppins', sans-serif;
    font-style: normal;
    font-weight: 600;
    font-size: 14px;
    line-height: 22px;
    display: flex;
    align-items: center;
    margin-bottom: 3px;
    text-decoration: none;
    color: #000000;
    &-base {
        font-family: 'Poppins', sans-serif;
        font-style: normal;
        font-weight: 600;
        line-height: 22px;
        display: flex;
        align-items: center;
        margin-bottom: 3px;
        font-size: 16px;
    }
}
</style>