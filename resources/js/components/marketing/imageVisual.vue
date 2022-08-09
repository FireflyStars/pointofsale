<template>

    <div 
        class="text-center bg-panel" 
        style="
        position: absolute; 
        left: 1.5rem; 
        min-width: 795px;
        min-height: 1124px;
        "
        :style="{ 
            'background-image': `url(${category.imageTemplateUrl})`,
            'background-repeat': 'no-repeat',
            'background-size': 'contain'
        }"
    >
        

        <template v-if="(!productWithDownloadOnly && !isPersonalizeAble) && !loading">

            <template v-for="(item, index) in fields" :key="index">

                    <span 
                        v-if="item != '' && item != null && typeof item != 'undefined' && item?.active == 1" 
                    >

                        <span 
                        v-if="index == 'Telephone_agence'"
                        :style="{
                            'color': `${item.color} !important`,
                            'font-size': `${item.size} !important`,
                            'font-family': `${item.font} !important`,
                            'top': `${item.y}px !important`,
                            'left': `${item.x}px !important`,
                            'position': 'absolute'
                        }">
                            {{ phone }}
                        </span>

                        <span 
                        v-else-if="index == 'Email_agence'"
                        :style="{
                            'color': `${item.color} !important`,
                            'font-size': `${item.size} !important`,
                            'font-family': `${item.font} !important`,
                            'top': `${item.y}px !important`,
                            'left': `${item.x}px !important`,
                            'position': 'absolute'
                        }">
                            {{ email }}
                        </span>

                        <span 
                        v-else
                        :style="{
                            'color': `${item.color} !important`,
                            'font-size': `${item.size} !important`,
                            'font-family': `${item.font} !important`,
                            'top': `${item.y}px !important`,
                            'left': `${item.x}px !important`,
                            'position': 'absolute'
                        }">
                            {{ item.value }}
                        </span>
                            
                        
                    </span>

            </template> 
        
        </template>

        
    </div>

</template>

<script setup>

</script>

<style lang="scss" scoped>

</style>