<template>
    <label class="select-label body_medium" :class="{disabled:disabled==true}" v-if="label">{{label}}</label>
    <div class="select noselect" :class="cname" @click.self="selectclick"><span class="disp" :class="{placeholder:current_display=='',disabled: disabled==true}" @click.self="selectclick"><template v-if="current_display==''">{{placeholder}}</template><template v-else>{{current_display}}</template></span>
        <transition name="trans-select">
            <div class="select-options"  v-if="sel===name"  >
               <slot>
                   <div class="opts" v-for="(item,index) in options" :key="index" @click="select(index)">{{item.display}}</div>
               </slot>
            </div>
        </transition>
    </div>
    <div class="hint" v-if="hint">{{hint}}</div>
</template>

<script>
    import {ref,watch,computed,nextTick} from 'vue';
    import {useStore} from 'vuex';
    import {GET_CURRENT_SELECT, SELECT_MODULE, SET_CURRENT_SELECT} from "../../store/types/types";
    export default {
        name: "SelectOptions",
        props:{
            hint:String,
            placeholder:String,
            name:{
                type: String,
                required: true
            },
            options:Object,
            modelValue: String|Number,
            classnames: String,
            label:String,
            disabled:{
                type:Boolean,
                required:false,
                default:false
                },
            valid:Boolean|null,

        },
        setup(props,context){
            const store=useStore();

            const current=ref('');
            const  current_display=ref('');
            let sel=computed(()=>store.getters[`${SELECT_MODULE}${GET_CURRENT_SELECT}`]);

            const selectclick=()=>{

                nextTick(()=>{

                    store.commit(`${SELECT_MODULE}${SET_CURRENT_SELECT}`,sel.value===props.name?'':props.name);


                }).then(()=>{

                    sel=computed(()=>store.getters[`${SELECT_MODULE}${GET_CURRENT_SELECT}`]);
                });

            }

            const select=(index)=>{
                    current.value=index;
                    context.emit("update:modelValue",props.options[index].value);

                store.commit(`${SELECT_MODULE}${SET_CURRENT_SELECT}`,'');
            }


            let currentoption=props.options.filter(option=>{
                return option.value.toString()==props.modelValue.toString()
            });
            currentoption=_.cloneDeep(currentoption);

            if(typeof currentoption[0] !="undefined") {
                current_display.value = currentoption[0].display;
            }
            watch(() => props.modelValue, (current_val, previous_val) => {

                if(current_val=="")
                    current_display.value ="";
                let currentoption=props.options.filter(option=>{
                    return option.value.toString()==props.modelValue.toString()
                });

                currentoption=_.cloneDeep(currentoption);
                if(typeof currentoption[0] !="undefined") {
                    current_display.value = currentoption[0].display;
                }

            });
            const cname=ref('');
            const classNames=()=>`${(typeof props.classnames!='undefined'?props.classnames:'')} ${(sel.value===props.name ? 'active' : '')} ${(current_display.value!='' ? 'selected' : '')} ${(props.disabled==true?'disabled':'')} ${(props.valid===true?'valid':(props.valid===false?'invalid':''))}`;
            cname.value = classNames();
            watch(() =>sel.value, (current_val, previous_val) => {
                cname.value = classNames();
            });

            watch(() =>current_display.value, (current_val, previous_val) => {
                cname.value = classNames()
            });
            watch(() =>props.disabled, (current_val, previous_val) => {
                cname.value = classNames();
                store.commit(`${SELECT_MODULE}${SET_CURRENT_SELECT}`,'');
            });

            return{
                selectclick,

                select,
                current,
                current_display,
                cname,
                sel
            }
        }
    }
</script>

<style scoped>
    .trans-select-enter-from{
        opacity: 0;
        transform: scale(0.6);
    }
    .trans-select-enter-to{
        opacity: 1;
        transform: scale(1);
    }
    .trans-select-enter-active{
        transition: all ease 0.2s;
    }
    .trans-select-leave-from{
        opacity: 1;
        transform: scale(1);
    }
    .trans-select-leave-to{
        opacity: 0;
        transform: scale(0.6);
    }
    .trans-select-leave-active{
        transition: all ease 0.2s;
    }
    .black-border{
        border: 1px solid #000000 !important;
    }
    .select{
        background: #FFFFFF;
        border: 0.5px solid #E0E0E0;
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
    .select.active{

        background: #EEEEEE;
        border: 1px solid #EEEEEE;
        outline: 2px #000000 solid;
    }
    .select.active .disp{
        font-weight: 600;
    }
    .select-options{
        position: absolute;
        width: 100%;
        left: 0;
        top: 44px;
        background: #FFF;
        box-shadow: inset 0px 0px 4px rgba(37, 40, 43, 0.12);
        max-height: 168px;
        z-index: 1;
        overflow-y: auto;
        transform-origin: top center;
    }
    .select:after,.select:before{
        content: " ";
        height: 3px;
        display: block;
        width: 13px;
        background: #868686;
        border-radius: 10px;
        transform: rotate(40deg);
        right:22px;
        position: absolute;
    }
    .select.active:after,.select.active:before{
        background: #000000;
    }
    .select:after{
        transform: rotate(-40deg);
        right: 13px;
    }
    .opts{
        height: 56px;
        padding: 17px 0 17px 16px;
        font-size: 16px;
    }
    .opts:hover{
        background: #EEEEEE;
    }
    .hint ~ .select{
        margin-bottom: 20px;
    }
    .hint{
        margin-bottom: 20px;
        font-size: 16px;
        color: #757575;;
        font-weight: 300;
    }

    .select:not(.active) .placeholder{
        color:#868686;
    }
    .select.selected{
       background: #F8F8F8;
        font-size: 16px;
    }
    .select:active{
        background-color: #E0E0E0;
        border-color: #E0E0E0;
    }
    .disabled{
        pointer-events: none;
    }
    .select-label.disabled{
        color:#C3C3C3;
    }
    .select.valid{
        background-color: #F7FBF6;
    }
    .select.valid .disp{
        color: #42A71E;
    }
    .select.valid:before,.select.valid:after{
        background-color: #42A71E;
    }
    .select.invalid{
        background-color: #FFEFED;
    }
    .select.invalid .disp{
        color: #EB5757;
    }
    .select.invalid:before,.select.invalid:after{
        background-color: #EB5757;
    }
    .select.disabled{
        background-color: #F8F8F8;
    }
    .select.disabled .disp{
        color:#C3C3C3;
    }
    .select.disabled:before, .select.disabled:after{
        background-color: #C3C3C3;
    }
</style>