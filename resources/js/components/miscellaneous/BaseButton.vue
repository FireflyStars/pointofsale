<template>
    <button 
        :class="classList"
        :title="$attrs.htmlTitle"
        :style="btnStyle"
    >
        <div 
            class="text" 
            :class="[{ 'order-1': prepend }, ...textClass.split(',')]"
            :style="textStyle"
        >
            {{ title }}
        </div>

        <div class="icon" :style="iconStyle">
            <slot></slot>
        </div>
    
    </button>
</template>

<script>

    import { computed } from 'vue'
    
    export default {

        name: "BaseButton",

        props: {
            title: {
                required: true,
                type: String
            },
            textClass: {
                type: [String, Object],
                default: ''
            },
            kind: {
                type: String,
                default: 'default' 
            },
            prepend: {
                type: Boolean,
                required: false,
                default: false
            },
            textStyle: {
                type: Object,
                default: () => {}
            },
            btnStyle: {
                type: String,
                default: ''
            },
            iconStyle: {
                type: Object,
                default: () => {}
            },
            classes: {
                type: [String, Object],
                default: ''
            },
            size: {
                required: false,
                type: String,
                default: 'md',
                validator(value) {
                    return ['sm', 'md', 'lg'].includes(value)
                },
                
            }
        },
        
        setup(props) {

            const classList = computed(() => {
                let classes = []
                if(typeof props.classes == 'object') {
                    classes = Object.keys(props.classes)
                }
                else classes = props.classes.split(',')
                return [props.kind, 'button-' + props.size, ...classes]
            })

            return {
                classList
            }

        }

    }

</script>



<style lang="scss" scoped>
    button {
        display: flex;
        align-items:center;
        gap: 4px;
        position: relative;
        background: #ECECEC;
        border: 1px solid #47454B;
        box-sizing: border-box;
        border-radius: 4px;
        text-align: left;
        font-style: normal;
        font-weight: bold;
        transition: all .1s;
        padding: 7px 18px;
        font-size: 16px;
        color:#47454B;
        line-height: 140%;
        font-family: 'Open Sans', sans-serif;
        
        &:hover {
            opacity: 0.9;
        }
        &-sm {
            padding: 5px 12px;
            font-size: 12px;
            line-height: 100%;
            font-weight: 600;
        }
        &-md {
            padding: 7px 18px;
            font-size: 16px;
            line-height: 140%;
        }
        &-lg {
            padding: 10px 24px;
            font-size: 18px;
            line-height: 160%;
        }
    }
    button.danger {
        background: #FF0000;
        color: white;
    }
    button.green {
        background: #A1FA9F;
        color: #3E9A4D;
    }
    button.light-green, button.lightgreen {
        background: rgba(161, 250, 159, 0.47);
        color: rgba(0, 0, 0, 0.2);
    }
    button.primary {
        background: #1FB9FC;
        color: #fff;
    }
    button.success {
        background: #3E9A4D;
        color: white;
    }
    button.light-warning, button.lightWarning {
        background: rgba(232, 88, 27, 0.7);;
        color: white;
    }
    button.warning {
        background: #E8581B;
        color: white;
    }
    .order-1 {
        order: 1 !important;
    }
    
</style>