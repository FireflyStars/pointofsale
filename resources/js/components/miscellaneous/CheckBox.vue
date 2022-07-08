<template>
    <div 
    @click="togglechkbox"
     class="chkbox_wrap">
        <span 
        class="chkbox" 
        :class="classSet"
        :style="styles"
        >
        </span>
        <label class="noselect body_regular" v-if="title!=null" style="margin-left:0.5rem;">{{ title }}</label>
    </div>
</template>

<script>
import { computed,ref, watch } from 'vue'
export default {
    
    name: "CheckBox",

    props: {
        id: [String, Number],
        modelValue: { 
            type: Boolean, 
            default: false 
        },
        checked: {
            type:Boolean,
            default:false
            },
        name: String,
        title: String,
        classes: {
            required: false,
            type: String,
            default: ''
        }
    },

    emits: ['change', 'update:modelValue'],

    setup(props, { emit, attrs }) { 
        const check = ref(false);
        check.value = props.checked;
        const togglechkbox = () => {
            check.value=!check.value;
            emit('change', { value: check.value, id: props.id, name: props.name })
            emit('update:modelValue', check.value);

        }

        const styles = computed(() => {
            return {
                background: attrs.background && check.value ? `${attrs.background} !important`: '',
                color: attrs.color && props.color ? `${attrs.color} !important`: '',
                fontFamily: attrs.fontFamily ? `${attrs.fontFamily} !important`: '',
                border: attrs.border ? `${attrs.border} !important`: '',
            }
        })
        watch(() => props.checked, (current_val, previous_val) => {
                check.value=current_val;
            });
        const classSet = computed(() => [{ 'checked': check.value }, ...props.classes.split(',')])
        
        return {
            styles,
            classSet,
            togglechkbox
        }
    }
}

</script>

<style scoped>
    .checkbox-items {
        cursor: pointer;
    }
    span.chkbox{
        width: 20px;
        height: 20px;
        display: inline-block;
        position: relative;
        border: #868686 2px solid;
    }
    span.chkbox.checked {
        background: #47454B;
        border:none;
    }
    span.chkbox:after{
      opacity: 0;
        position: absolute;
        content: " ";
        left: 6px;
        top: 2px;
        width: 8px;
        height: 13px;
        border: solid white;
        border-width: 0 3px 3px 0;
        -webkit-transform: rotate(45deg);
        -ms-transform: rotate(45deg);
        transform: rotate(45deg);
    }
    span.chkbox.checked:after{
        opacity: 1;
        transition: opacity 0.3s ease-out;
        -webkit-transition: opacity 0.3s ease-out;
        -moz-transition:  opacity 0.3s ease-out;
    }
    .filters span.chkbox,thead span.chkbox,header span.chkbox{
        border:#868686 2px solid;
    }
    .filters span.chkbox.checked,header span.chkbox.checked{
        background: #47454B;
        border:none;
    }
    .chkbox_wrap {
        display: flex;
        align-items: center;
        margin: .3rem 0;
    }
    .filters span.chkbox{
        margin: 14px 22px 14px 22px;
    }
    .filters .chkbox_wrap{
    display: flex;
    }
    .filters .chkbox_wrap label{
        margin: 14px 22px 14px 0;
        font-weight: lighter;
    }
</style>