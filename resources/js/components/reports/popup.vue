<template>
    <div 
        class="popup m-2"
        :class="{ 'w-35': isTable }"
    >
        <span @click.prevent="close" class="close">&times;</span>
        <div class="popup-header">
            <h4 class="popup-header-title">Update Zone</h4>
        </div>
        <div class="popup-body almarai_regular_normal">
            <form>

                <div class="popup-body-item">
                    
                    <p class="label">Item:</p>

                    <div class="item">

                        <div class="d-flex gap-2" v-if="isTextarea">
                            
                            <div>
                                <label for="">Size</label>
                                <select @change="commitAction('fontsize', $event.target.value)">
                                    <option class="heading" selected>- size -</option>
                                    <option value="1">Very small</option>
                                    <option value="2">A bit small</option>
                                    <option value="3">Normal</option>
                                    <option value="4">Medium-large</option>
                                    <option value="5">Big</option>
                                    <option value="6">Very big</option>
                                    <option value="7">Maximum</option>
                                </select>
                            </div>

                            <div>
                                <label for="">Color</label>
                                <input 
                                    type="color" 
                                    @change="commitAction('forecolor', $event.target.value)"
                                    title="color"
                                >
                            </div>
                            
                            <div>
                                <label for="">Background</label>
                                <input 
                                    type="color" 
                                    @change="commitAction('backcolor', $event.target.value)"
                                    title="Background color"
                                >
                            </div>

                        </div>
                        
                        <div class="display-block" v-if="isTextarea">
                            <Icon
                                v-for="action in actions"
                                :key="action"
                                :name="action"
                                @click.prevent="action == 'blockquote' ? commitAction('formatblock', action) : commitAction(action)"
                                :title="action"
                            />
                        </div>
                        
                        <component 
                            v-if="isTextarea"
                            :is="'div'"
                            :class="[item.attributes?.class, { 'editable': isTextarea }]"
                            :contenteditable="isTextarea || isTable"
                            dom="input"
                            spellcheck="false"
                            autocomplete="false"
                            @keyup="updateTextValue($event.target)"
                        >
                        </component>

                        <component 
                            v-else
                            :is="item.item"
                            :class="[item.attributes?.class]"
                            :src="item.attributes?.src"
                            :name="item.attributes?.name"
                            :contenteditable="isTextarea || isTable"
                            :rows="table.rows"
                            :cols="table.cols"
                            :headers="table.headers"
                            :content="table.content"
                            width="50"
                            height="30"
                            @update="updateContent"
                        >
                        </component>

                        <div class="report-variables" v-if="isTextarea">
                            <a 
                                href="#" 
                                v-for="(variable, index) in reportVariables" 
                                :key="index"
                                @click.prevent="insertVariable(variable)"
                            >
                                {{ variable }}
                            </a>
                        </div>


                    </div>

                </div>

                <div class="popup-body-attributes">
                    <template v-if="isTable">
                        <div class="attribute">
                            <div>Rows:</div>
                            <div class="d-flex align-items-center gap-1">
                                <input type="number" v-model="table.rows">
                            </div>
                        </div>
                        <div class="attribute">
                            <div>Cols:</div>
                            <div class="d-flex align-items-center gap-1">
                                <input type="number" v-model="table.cols">
                            </div>
                        </div>
                        <div class="attribute">
                            <div>Headers:</div>
                            <div class="d-flex align-items-center gap-1">
                                <select name="font_family" v-model="table.headers">
                                    <option :value="true">Enable</option>
                                    <option :value="false">Disable</option>
                                </select>
                            </div>
                        </div>
                    </template>

                    <template v-if="isSvg">
                        <div class="attribute">
                            <div>Stroke:</div>
                            <div class="d-flex align-items-center gap-1">
                                <input 
                                    type="color" 
                                    name="stroke-width" 
                                    v-model="itemAttributes.svg.stroke"
                                    style="width: 5rem"
                                >
                            </div>
                        </div>
                        <div class="attribute">
                            <div>Stroke width:</div>
                            <div class="d-flex align-items-center gap-1">
                                <input type="text" name="stroke" v-model="itemAttributes.svg.strokeWidth">
                            </div>
                        </div>
                    </template>

                    <template v-if="!isSvg">

                        <div class="attribute">
                            <div>Font size:</div>
                            <div class="d-flex align-items-center gap-1">
                                <input type="text" v-model="itemAttributes.fontSize">
                                <span class="unit">px</span>
                            </div>
                        </div>
                        <div class="attribute">
                            <div>Font family:</div>
                            <div class="d-flex align-items-center gap-1">
                                <select name="font_family" v-model="itemAttributes.fontFamily">
                                    <option value="times">Times</option>
                                    <option value="mullish">Mullish</option>
                                    <option value="poppins">Poppins</option>
                                    <option value="Almarai ExtraBold">Almarai ExtraBold</option>
                                </select>
                            </div>
                        </div>
                        <div class="attribute" v-if="!isTextarea">
                            <div>Color:</div>
                            <div class="d-flex align-items-center gap-1">
                                <input type="color" name="color" v-model="itemAttributes.color" style="width: 5rem">
                            </div>
                        </div>
                        <div class="attribute">
                            <div>Text align:</div>
                            <div class="d-flex align-items-center gap-1">
                                <select name="text_align" v-model="itemAttributes.textAlign">
                                    <option value="left">Left</option>
                                    <option value="right">Right</option>
                                    <option value="center">Center</option>
                                </select>
                            </div>
                        </div>
                        <div class="attribute">
                            <div>Z-index:</div>
                            <div class="d-flex align-items-center gap-1">
                                <input type="text" name="zindex" v-model="itemAttributes.zIndex">
                            </div>
                        </div>
                        <div class="attribute">
                            <div>Border Width:</div>
                            <div class="d-flex align-items-center gap-1">
                                <input type="text" name="borderwidth" v-model="itemAttributes.borderWidth">
                                <span class="unit">px</span>
                            </div>
                        </div>
                        <div class="attribute">
                            <div>Border Color:</div>
                            <div class="d-flex align-items-center gap-1">
                                <input type="color" name="borderColor" v-model="itemAttributes.borderColor" style="width: 5rem">
                            </div>
                        </div>
                        <div class="attribute">
                            <div>Border Style:</div>
                            <div class="d-flex align-items-center gap-1">
                                <select name="text_align" v-model="itemAttributes.borderStyle">
                                    <option value="none">None</option>
                                    <option value="hidden">Hidden</option>
                                    <option value="dashed">Dashed</option>
                                    <option value="solid">Solid</option>
                                    <option value="double">Double</option>
                                    <option value="groove">Groove</option>
                                    <option value="Ridge">Ridge</option>
                                    <option value="inset">Inset</option>
                                    <option value="outset">Outset</option>
                                </select>
                            </div>
                        </div>

                    </template>


                </div>

                <base-button 
                    title="Update" 
                    class="float-end mt-2"
                    @click.prevent="submit"
                />
                <div class="clearfix"></div>    

            </form>

        </div>
    </div>
</template>

<script>

import { isEmpty } from 'lodash'
import reportTable from './report-table'
import { ref, computed, onMounted, reactive } from 'vue'
import useStyles from '../../composables/reports/useStyles'


export default {

    name: 'popup',

    components: {
        reportTable
    },

    props: {
        item: {
            required: true,
            type: [Object, Array]
        },
        domStyles: {
            required: true,
            type: [Object, Array]
        }
    },

    emits: ['close', 'update'],
    
    setup (props, { emit }) {

        const { itemAttributes, loadDefaultStyles, loadSvgAttributes } = useStyles()
        const textValue = ref('')
        const table = reactive({
            rows: 1,
            cols: 2,
            headers: true,
            content: {
                header: {},
                body: {}
            }
        })

        const hasVariables = ref(false)

        const reportVariables = ref([
            'société',
            'raisonsocial',
            'raisonsocial2',
            'téléphone-client',
            'prénom-contact',
            'nom-contact',
            'email-contact',
            'mobile-contact',
            'téléphone-contact',
            'société-adresse',
            'prénom-ou-batiment',
            'nom-adresse',
            'adresse1',
            'adresse2',
            'adresse3',
            'code-postal',
            'ville',
            'num-commande',
            'total-commande',
            'date-de-fin-prevue-commande',
            'date-de-commande'
        ])

        const insertVariable = (variable) => {
            console.log(textValue.value, " is the text value")
            // textValue.value += `[${variable}]`
            document.querySelector('.editable').innerHTML = document.querySelector('.editable').innerHTML + ` [${variable}]`
            hasVariables.value = true
        }

        const actions = [
            'cut',
            'copy',
            'undo', 
            'redo', 
            'bold', 
            'paste',
            'brush', 
            'indent',
            'italic', 
            'outdent',
            'underline', 
            'foramtting', 
            'justifyleft', 
            'blockquote',
            'justifyright',
            'justifycenter', 
            'insertorderedlist',
            'insertunorderedlist',
        ]

        const isTextarea = computed(() => props.item.name == 'textarea')
        const isTable = computed(() => props.item.name == 'table')
        const isSvg = computed(() => props.item.name == 'svg')

        const close = () => emit('close')

        const submit = () => {
            
            const textValue = document.querySelector('.editable')?.innerHTML

            emit('update', { 
                id: props.item.attributes.id, 
                textValue: isTextarea.value ? textValue : '',
                table,
                name: props.item.name,
            })

        }

        const updateTextValue = (target) => {
            // const value = target.innerHTML
            const value = document.querySelector('.editable')?.innerHTML
            console.log(value, " is value")
            // textValue.value = value
            if(isEmpty(value)) {
                textValue.value = ''
                hasVariables.value = false
            }
        }

        const updateContent = ({ row, col, value, type }) => {
            table.content[type] = {
                ...table.content[type],
                [`tr-${row}${col}`]: value,
            }
        }

        const loadDefaultValue = () => {
            if(isTextarea.value && props.item.content != '') {
                document.querySelector('.editable').innerHTML = props.item.content
                textValue.value = props.item.content 
            }
            if(isTable.value) {
                table.rows = props.item?.attributes?.rows,
                table.cols = props.item?.attributes?.cols,
                table.headers = props.item?.attributes?.headers || true
                table.content = _.cloneDeep(props.item?.content) || table.content
            }
        }

        const commitAction = (sCmd, sValue) => {
            document.execCommand(sCmd, false, sValue)
            document.querySelector('.editable').focus()
        }

        onMounted(() => {
            loadDefaultStyles(props.item, props.domStyles)
            loadDefaultValue()
            if(isSvg.value) loadSvgAttributes(props.item.attributes)
        })

        return {
            close,
            submit,
            table,
            isSvg,
            isTable,
            actions,
            textValue,
            isTextarea,
            commitAction,
            updateContent,
            insertVariable,
            itemAttributes,
            reportVariables,
            updateTextValue,
            hasVariables,
        }

    }

}
</script>

<style lang="scss" scoped>

.w-35 {
    width: 35rem !important;
}

.popup {
    
    background: #EEEEEE;
    box-shadow: 0 0.125rem 0.25rem rgb(0 0 0 / 8%) !important;
    z-index: 999999;
    width: 24rem;
    position: relative;
    line-height: 47px;
    padding: 1rem;

    .close {
        color: white;
        font-weight: bold;
        font-size: 1.2rem;
        position: absolute;
        top: -0.4rem;
        right: -0.4rem;
        padding: 0;
        margin: 0;
        background: #000;
        border: 3px solid #fff;
        border-radius: 50%;
        width: 1.7rem;
        height: 1.7rem;
        display: flex;
        justify-content: center;
        align-items: center;
        cursor: pointer;
        &:hover {
            opacity: .8;
        }
    }

    &-header {
        padding: 1rem;
        &-title {
            margin-bottom: 0.861rem;
            font-size: 1rem;
            line-height: 140%;
            color: #47454B;
            font-family: inherit;
            font-weight: bold;
            line-height: 140%;
            text-transform: capitalize;
            line-height: 22.4px;
        }
    }

    &-body {
        padding: 1rem;
        width: 100%;
        box-sizing: border-box;
        overflow-x: hidden;
        img {
            width: 90%;
            height: 100%;
        }
        &-item {
            display: grid;
            align-content: center;
            grid-template-columns: 17% 83%;
            gap: 1rem;
            border-bottom: 1px solid #ccc;
            margin: 0 0 1rem 0;
            padding-bottom: .5rem;

            font-size: 16px;
            line-height: 20px;
            color: #222;
            text-transform: capitalize;
            font-weight: 700;
            letter-spacing: 1px;

            .item {
                flex-grow: 1;
                label {
                    font-size: 9px;
                }
            }
            .editable {
                width: 95%;
                height: 6rem;
                background: white;
                overflow: auto;
                text-transform: none;
                p {
                    margin: 0;
                    font-size: 16px !important;
                    font-weight: normal !important;
                    font-style: normal !important;
                    color: #000;
                    text-transform: none;
                }
            }
        }
        &-attributes {
            .attribute {
                display: grid;
                justify-items: flex-start;
                align-content: center;
                grid-template-columns: 35% 65%;
                gap: 1rem;
                margin: .4rem 0;
                font-size: 16px;
                line-height: 20px;
                color: #222;
                text-transform: capitalize;
                font-weight: 700;
                letter-spacing: 1px;
                .unit {
                    font-size: 12px;
                } 
            }
        }
    }

    textarea {
        height: 4rem;
        width: 95%;
        resize: none;
    }

    table {
        width: 95% !important;
        height: auto !important;
        resize: none;
    }

    .attribute input {
        box-sizing: border-box;
        width: 30%;
    }

    select {
        width: 100%;
    }

    .report-variables {
        display: flex;
        align-items: center;
        gap: .2rem;
        flex-wrap: wrap;
        a {
            color: #47454B;
            font-size: 12px;
            text-transform: lowercase;
            &:hover {
                color: #000;
            }
        }
    }

}
</style>