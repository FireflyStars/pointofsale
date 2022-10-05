<template>
    <div class="switch-wrapper" :class="{disabled:disabled}">
        <label class="body_small" v-if="labelLeft" @click="toggle">{{labelLeft}}</label>
        <div class="switch" :class="{on:switchval}" @click="toggle">
            <span class="body_medium noselect">Oui</span>

            <span class="body_medium noselect">Non</span>
        </div>
        <label class="body_small" v-if="labelRight" @click="toggle">{{labelRight}}</label>
    </div>
</template>

<script>
    import {ref,watch} from 'vue';
    export default {
        name: "Switch",
        props:{
            modelValue: Boolean,
            labelLeft: String,
            disabled: Boolean,
            labelRight: String
        },
        setup(props,context){
            const switchval=ref(false);
            switchval.value=props.modelValue;

            watch(() => props.modelValue, (current_val, previous_val) => {
                switchval.value=current_val;

            });
            const toggle=()=>{
                switchval.value=!switchval.value;
                context.emit("update:modelValue",switchval.value);
            }


            return {
                switchval,
                toggle
            }
        }
    }
</script>

<style scoped>
    .switch-wrapper{
        display: flex;
        align-items: center;
    }
    .switch-wrapper label{
        cursor: pointer;
    }
    .switch{
        background-color: #E8581B;
        height: 28px;
        padding: 5px 3px 5px 8px;
        border-radius: 40px;
        display: flex;
        align-items: center;
        width: 65px;
        position: relative;
        cursor: pointer;
        transition: 0.3s ease-in-out background-color;
    }
    .switch span{
        color:#FFF;
    }
    .switch:before{
        content: " ";
        width: 18px;
        height: 18px;
        position: absolute;
        background: #FFF;
        top:5px;
        left:3px;
        border-radius: 50%;
        transition: 0.3s ease-in-out left;
    }
    .switch span{
        transition: 0.2s ease-in-out opacity;
    }
    .switch.on{
        background-color: #E8581B;
    }
    .switch.on:before{
        left: 44px;
    }
    .switch span:first-child{
        opacity: 0;
    }
    .switch span:last-child{
        opacity: 1;
    }
    .switch.on span:last-child{
        opacity: 0;
    }
    .switch.on span:first-child{
        opacity: 1;
    }
    .disabled .switch{
        opacity: 0.5;
        cursor: default;
        pointer-events: none;
    }
    .disabled label{
        opacity: 0.5;
        pointer-events: none;
        cursor: default;

    }
    .switch-wrapper label:first-child{
        margin-right: 8px;
    }
    .switch-wrapper label:last-child{
        margin-left: 8px;
    }

</style>