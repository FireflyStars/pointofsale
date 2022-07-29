<template>
    <div :style="{
        width: $attrs.width ? `${$attrs.width} !important` : 'auto',
        padding: $attrs.padding || 0,
        margin: $attrs.margin || 0,
        ...styles
    }"
    class="box-container"
    :class="[{ 'dark': dark }, ...classes.split(',')]"
    >

        <label for="" class="label" v-if="title!=''">{{ title }}</label>

        <div class="select-box">
            <div class="selected" @click="toggleActiveItem($attrs.id || 'multiSelect')">
                <div class="item" 
                v-for="item in controlledSelectedValues" 
                :key="item.id"
                :style="{
                    background: $attrs.tagBackground || '#fff',
                    color: $attrs.tagColor || '#000'
                }"
                >
                    <span class="text">{{ slice(item.value) }}</span>
                    <span 
                    class="close" 
                    title="Remove" 
                    @click.stop="updateSelectOptions({ value: false, id: item.id })">
                        &times;
                    </span>
                </div>
                <div 
                v-if="tagCountLimitExceeded"
                class="item"
                :style="{ 
                    color: $attrs.tagBackground || '#000',
                    background: 'transparent' 
                }">
                    <span class="d-inline-block">+ {{ remainingTagCount }}</span>
                </div>
            </div>

            <Dropdown 
            class="options-container"
            height="auto"
            :id="$attrs.id || 'multiSelect'"
            :dark="dark"
            :classes="$attrs.dropdownClasses"
            :styles="$attrs.dropdownStyles"
            :transformOrigin="$attrs.transformOrigin"
            >
            
                <div class="search-box" v-if="search">
                    <input type="search" name="" id="" placeholder="Search..." v-model="searchValue">
                </div>

                <div 
                class="option"
                v-for="item in filteredOptions"
                :key="item.id"
                >
                    <CheckBox 
                        class="checkbox-custom-class"
                        :id="item.id"
                        :checked="item.check"
                        :name="item.value"
                        :title="item.value"
                        @change="updateSelectOptions"
                    />
                </div>
            </Dropdown>

        </div>

    </div>
</template>

<script>
import { ref, computed, onMounted } from "vue"
import useToggler from '../../composables/useToggler'
import useHelpers from '../../composables/useHelpers'

export default {
    
    name: "MultiSelect",
    inheritAttrs: false,

    props: {
        title: String,
        search: {
            type: Boolean,
            default: false,
        },
        options: {
            required: true,
            type: [Object, Array]
        },
        dark: Boolean,
        classes: String,
        styles: {
            type: Object,
            default: () => {}
        },
        numtag: {
            type: Number,
            required: false,
            default: 0
        },
        selectedOptions: {
            type: [Array],
            required: false,
            default: []
        }
    },

    emits: ['update:options'],

    
    setup(props, { emit }) {

        const { open, toggleActiveItem } = useToggler()
        const { slice } = useHelpers()

        const searchValue = ref('')
        const selectedValues = ref([])

        const controlledSelectedValues = computed(() => {
            return props.numtag && props.numtag > 0 ? [...selectedValues.value].slice(0, props.numtag) : 0
        })

        const remainingTagCount = computed(() => {
            const limitedValues = controlledSelectedValues.value
            const remaining = ['array', 'object'].includes(typeof limitedValues) ? limitedValues.length : limitedValues
            return selectedValues.value.length - remaining
        })

        const tagCountLimitExceeded = computed(() => {
            return selectedValues.value.length > props.numtag && props.numtag > 0
        })

        const filteredOptions = computed(() => {
            let options = []
            if(props.search && searchValue.value != '') {
                options = props.options.filter(option => {
                    return option.value.toLowerCase().includes(searchValue.value.toLowerCase())
                })
            }
            else options = [ ...props.options ]
            return options
        })

        const updateSelectOptions = ({ value, id }) => {
            const optionIndex = props.options.findIndex(option => option.id == id)
            const filteredOptions = [ ...props.options ]
            filteredOptions[optionIndex].check = value
            emit('update:options', filteredOptions)
            udpateSelectedValues({ filteredOptions, value, id, optionIndex })
        }

        const udpateSelectedValues = ({ filteredOptions, value, id, optionIndex }) => {
            if(value) {
                selectedValues.value.push(filteredOptions[optionIndex])
            }
            else {
                optionIndex = selectedValues.value.findIndex(option => option.id == id)
                selectedValues.value.splice(optionIndex, 1)
            }
        }

        onMounted(() => {
        
            if(props.selectedOptions?.length) {
                props.selectedOptions.forEach(option => {
                    updateSelectOptions({ id: option, value: true })
                })
            }
        })

        return {
            open, 
            slice,
            searchValue,
            selectedValues,
            filteredOptions,
            toggleActiveItem,
            remainingTagCount,
            updateSelectOptions,
            tagCountLimitExceeded,
            controlledSelectedValues,
        }


    }

}

</script>


<style lang="scss" scoped>

.box-container {
    position: relative;
   
}

.select-box {

    position: relative;
    display: flex;
    flex-direction: column;
    justify-items: center;
    width: 100%;
    box-sizing: border-box;

    .options-container {
        padding: 2.215rem 1.75rem 2.25rem 1.75rem;
        .option {
            font-family: inherit;
            font-style: normal;
            font-weight: bold;
            font-size: 14px;
            line-height: 140%;
            display: flex;
            align-items: center;
            color: #000000;
            &:not(:last-of-type) {
                margin-bottom: 1rem;
            }
        }
    }

    .selected {
        background: #EEEEEE;
        border-radius: 4px;
        color: #000000;
        position: relative;
        order: 0;
        padding: .4rem;
        display: flex;
        justify-items: center;
        flex-wrap: nowrap;
        gap: .3rem;
        max-height: 40px;
        height: 40px;
        overflow: hidden;
        font-family: inherit;
        .item {
            background: #fff;
            padding: 2px 4px;
            font-size: 13px;
            display: flex;
            align-items: center;
            justify-items: flex-start;
            border-radius: 5px;
            .close {
               margin: 0 4px;
               font-size: 16px;
               margin-top: 1px;
               &:hover {
                    opacity: .8;
                    transform: scale(108%);
               }             
            }
        }
        
    }

    .search-box {
        padding-bottom: 3rem;
        margin-left: -1.75rem;
        input {
            width: 100%;
            padding: 6px 12px;
            // font-family: Almarai;
            font-family: inherit;
            font-size: 16px;
            position: absolute;
            border-radius: 8px 8px 0 0;
            z-index: 100;
            border: 8px solid #EEEEEE;
            opacity: 0;
            pointer-events: none;
            transition: all 0.2s;
            &:focus {
                outline: none;
            }
        }
    }

}


.label {
    color: #868686;
    // font-family: Almarai;
    font-family: inherit;
    font-style: normal;
    font-weight: bold;
    font-size: 14px;
    line-height: 140%;
    text-transform: uppercase;
    margin-top: 8px;
}

.selected {
    padding: 12px;
    cursor: pointer;
}

.select-box .options-container.active .search-box input {
    opacity: 1;
    pointer-events: auto;
}

.dark {
    .selected {
        background: #494c50;
        &::-webkit-scrollbar {
            background: #525861;
        }

        &::-webkit-scrollbar-thumb {
            background: #494c50;
        }
        .item {
            background: #525861;
            color: #F0F0F0;
        }
    }
    .options-container {
        background: #494c50;
        color: #F0F0F0;
        &::-webkit-scrollbar {
            background: #525861;
        }

        &::-webkit-scrollbar-thumb {
            background: #494c50;
        }
    }
    .search-box {
        input {
            border: 8px solid #494c50;
            background: #525861;
            color: #F0F0F0;
        }
    }
}

</style>

