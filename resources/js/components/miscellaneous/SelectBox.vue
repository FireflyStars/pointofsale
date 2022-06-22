<template>
    <label
        class="select-label body_medium"
        :class="{ disabled: disabled == true }"
        v-if="label"
        >
            {{ label }}
        </label
    >
    <div 
    class="select noselect"
    :class="classNameSet"
    @click.self="toggle"
    :style="{
        ...styles
    }"
    v-click-away="close"
    >
    <span
            class="disp"
            :class="{
                '_placeholder': current_display == '',
                'disabled': disabled == true,
            }"
            
            @click.self="toggle"
   >
            <template v-if="current_display == ''">{{ placeholder }}</template>
            <template v-else>{{ current_display }}</template>
        </span>
        <transition name="trans-select">
            <div class="select-options" v-if="open" :style="{
                ...selectStyles
            }">
                <slot>
                    <div
                        class="opts text-nowrap"
                        v-for="(item, index) in options"
                        :key="index"
                        @click="select(index)"
                    >
                        {{ item.display }}
                    </div>
                </slot>
            </div>
        </transition>
    </div>
    <div class="hint" v-if="hint">{{ hint }}</div>
</template>

<script>

import { useStore } from "vuex"
import { ref, watch, computed, nextTick, onMounted } from "vue"

import {
    GET_CURRENT_SELECT,
    SELECT_MODULE,
    SET_CURRENT_SELECT,
} from "../../store/types/types";

export default {

    name: "SelectBox",

    props: {
        hint: String,
        placeholder: String,
        name: {
            type: String,
            required: true,
        },
        options: Object,
        modelValue: String | Number,
        classnames: String,
        label: String,
        disabled: Boolean,
        valid: Boolean | null,
        styles: {
            required: false,
            type: Object,
            default: () => {}
        },
        selectStyles: {
            required: false,
            type: Object, 
            default: () => {}
        }
    },

    emits: ['update:modelValue'],

    setup(props, context) {

        const store = useStore()
        const open = ref(false)
        const current = ref("")
        const current_display = ref("")

        let sel = computed(
            () => store.getters[`${SELECT_MODULE}${GET_CURRENT_SELECT}`]
        )

        const close = () => open.value = false

        const selectclick = () => {
            nextTick(() => {
                store.commit(
                    `${SELECT_MODULE}${SET_CURRENT_SELECT}`,
                    sel.value === props.name ? "" : props.name
                );
            }).then(() => {
                sel = computed(
                    () => store.getters[`${SELECT_MODULE}${GET_CURRENT_SELECT}`]
                );
            });
        }

        const select = (index) => {
            current.value = index;
            context.emit("update:modelValue", props.options[index].value);

            store.commit(`${SELECT_MODULE}${SET_CURRENT_SELECT}`, "");
            toggle()
        }

        const setCurrentDisplay = () => {
            let currentoption = props.options.filter((option) => {
                if(typeof props.modelValue!="undefined"){
                    return option.value.toString() == props.modelValue.toString();
                }
            });
            currentoption = _.cloneDeep(currentoption);
            if (typeof currentoption[0] != "undefined") {
                current_display.value = currentoption[0].display;
            }
        }

        onMounted(() => {
            setCurrentDisplay()
        })

        
        watch(
            () => props.modelValue,
            (current_val) => {
                if (current_val == "") current_display.value = "";
                setCurrentDisplay()
            }
        )


        const classNameSet = computed(() => {
            let classes = ''
            classes += typeof props.classnames != "undefined" ? props.classnames : ""
            classes += open.value ? " active " : ""
            classes +=  current_display.value != "" ? " selected " : ""
            classes += props.disabled == true ? " disabled " : ""
            classes += props.valid === true
                    ? " valid "
                    : props.valid === false
                    ? " invalid "
                    : ""
            return classes        
        })

        const toggle = () => {
            open.value = !open.value
        }

        return {
            toggle,
            open,
            close,
            classNameSet,
            selectclick,
            select,
            current,
            current_display,
            sel,
        };
    },
};
</script>

<style scoped>
/* width */
::-webkit-scrollbar {
  width: 9px;
}
/* Track */
::-webkit-scrollbar-track {
  background: #E0E0E0; 
}
/* Handle */
::-webkit-scrollbar-thumb {
  background: #47454B; 
  border-radius: 6px;
}
.trans-select-enter-from {
    opacity: 0;
    transform: scale(0.6);
}
.trans-select-enter-to {
    opacity: 1;
    transform: scale(1);
}
.trans-select-enter-active {
    transition: all ease 0.2s;
}
.trans-select-leave-from {
    opacity: 1;
    transform: scale(1);
}
.trans-select-leave-to {
    opacity: 0;
    transform: scale(0.6);
}
.trans-select-leave-active {
    transition: all ease 0.2s;
}

.select {
    background: #ffffff;
    border: 0.5px solid #e0e0e0;
    box-sizing: border-box;
    border-radius: 5px;
    padding: 0 36px 0 16px;
    height: 40px;
    font-size: 14px;
    display: flex;
    cursor: pointer;
    align-items: center;
    position: relative;
}
.select.active {
    background: #eeeeee;
    border: 1px solid #eeeeee;
    outline: 2px #000000 solid;
}
.disp{
    white-space: nowrap;
    width: 100%;
    overflow: hidden;
    display: block;
    text-overflow: ellipsis;    
}
.select.active .disp {
    font-weight: 600;
}
.select-options {
    position: absolute;
    min-width: 100%;
    width: fit-content;
    left: 0;
    top: 44px;
    background: #fff;
    box-shadow: 1px 1px 3px rgba(0, 0, 0, .4);
    max-height: 168px;
    z-index: 1;
    overflow-y: scroll;
    transform-origin: top center;
}
.select::after,
.select::before {
    content: " ";
    height: 3px;
    display: block;
    width: 13px;
    background: #868686;
    border-radius: 10px;
    transform: rotate(40deg);
    right: 22px;
    position: absolute;
}
.select.active::after,
.select.active::before {
    background: #000000;
}
.select::after {
    transform: rotate(-40deg);
    right: 13px;
}
.opts {
    height: 50px;
    padding: 13px 0 13px 16px;
    font-size: 16px;
}
.opts:hover {
    background: #eeeeee;
}
.hint ~ .select {
    margin-bottom: 20px;
}
.hint {
    margin-bottom: 20px;
    font-size: 16px;
    color: #757575;
    font-weight: 300;
}

.select:not(.active) .placeholder {
    color: #868686;
}
.select.selected {
    background: #f8f8f8;
    font-size: 16px;
}
.select::active {
    background-color: #e0e0e0;
    border-color: #e0e0e0;
}
.disabled {
    pointer-events: none;
}
.select-label.disabled {
    color: #c3c3c3;
}
.select.valid {
    background-color: #f7fbf6;
}
.select.valid .disp {
    color: #42a71e;
}
.select.valid::before,
.select.valid::after {
    background-color: #42a71e;
}
.select.invalid {
    background-color: #ffefed;
}
.select.invalid .disp {
    color: #eb5757;
}
.select.invalid::before,
.select.invalid::after {
    background-color: #eb5757;
}
.select.disabled {
    background-color: #f8f8f8;
}
.select.disabled .disp {
    color: #c3c3c3;
}
.select.disabled::before,
.select.disabled::after {
    background-color: #c3c3c3;
}
</style>
