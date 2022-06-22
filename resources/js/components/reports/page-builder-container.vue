<template>
    <div 
        id="builder-container"
        class="shadow-sm builder-container"
        :style="{ 
            backgroundImage: `url('${reportBackground?.attributes?.src}')`,
            backgroundRepeat: 'no-repeat',
            backgroundSize: 'cover',
            backgroundPosition: 'center' 
        }" 
    >

        <div 
            v-if="!fetching"
            class="template-body"
        >

            <template 
                v-for="(element, index) in page.elements"
                :key="index"
            >
                <component 
                    v-if="element.name != 'svg'"
                    :is="element.item" 
                    v-bind="element.attributes"
                    @click.stop="activateItem($event)"
                    @dblclick="openUpdatePopup(element, $event.target)"
                    :disabled="element.name == 'table'"
                    :content="element.content"
                    contenteditable="false"
                    :class="{ 
                        'active-item': `#${element.attributes.id}` == activeItem 
                    }"
                >

                    <span 
                        v-if="['textarea', 'table'].includes(element.name)" 
                        v-html="element.content"
                    >
                    </span>

                </component>

                <div 
                    v-else
                    v-bind="element.attributes"
                    @click.stop="activateItem($event)"
                    @dblclick="openUpdatePopup(element, $event.target)"
                    :class="{ 
                        'active-item': `#${element.attributes.id}` == activeItem 
                    }"
                >
                    <Icon 
                        :name="element.attributes.name"
                        :width="element.attributes.width"
                        :height="element.attributes.height"
                        :strokeWidth="element.attributes.strokeWidth"
                        :stroke="element.attributes.stroke"
                    />
                </div>

            </template>

            <span 
                class="close"
                style="display: none"
                @click="deleteItem" 
            >
                &times;
            </span>

            <popup 
                v-if="openPopup"
                :item="activeElement"
                :domStyles="getStylesOfElement(activeDomElement)" 
                @close="openPopup = false"
                @update="updateElementFromPopup"
            />

        </div>

        <report-footer 
            v-if="isReportPage && activePage != 0 && activePage != pages.length - 1" 
        />

    </div>

    <Moveable
        v-if="pages.length"
        className="moveable"
        v-bind:target="[activeItem]"
        v-bind:draggable="true"
        v-bind:scalable="true"
        v-bind:rotatable="true"
        :keepRatio="false"
        @drag="onDrag"
        @scale="onScale"
        @rotate="onRotate"
    />

</template>

<script>
import { useStore } from 'vuex'
import { useRoute } from 'vue-router'
import { ref, unref, computed, inject, watch, nextTick } from 'vue'

import { 
    UPDATE_SVG,
    DELETE_ITEM,
    BUILDER_MODULE, 
    UPDATE_ELEMENT_CONTENT,
    UPDATE_ELEMENT_STYLES,
    UPDATE_ELEMENT_TABLE,
} from '../../store/types/types'

import Moveable from "vue3-moveable"
import popup from '../../components/reports/popup'
import reportTable from '../../components/reports/report-table'
import reportFooter from '../../components/reports/report-footer'

import useStyles from '../../composables/reports/useStyles'
import useHelpers from '../../composables/useHelpers'
import useElementsGenerator from '../../composables/reports/useElementsGenerator'

export default {
    
    name: 'page-builder-container',

    components: {
        popup,
        Moveable,
        reportTable,
        reportFooter
    },

    props: {
        showcontainer: {
            required: true,
            type: Boolean
        }
    },

    setup () {
        
        const store = useStore()
        const route = useRoute()
        
        const activeItem = ref(null)
        const activeElement = ref({})
        const activeDomElement = ref(null)
        const openPopup = ref(false)

        const fetching = inject('fetching')

        const { generateTags } = useElementsGenerator()
        const { getDomElementParent } = useHelpers()
        const { itemAttributes, getStylesOfElement, getComputedStyle } = useStyles()


        const isReportPage = computed(() => route.name == 'report-page')
        
        const activePage = computed(() => store.getters[`${BUILDER_MODULE}/activePage`])
        const page = computed(() => store.getters[`${BUILDER_MODULE}/page`])
        const pages = computed(() => store.getters[`${BUILDER_MODULE}/pages`])
        const order = computed(() => store.getters[`${BUILDER_MODULE}/order`])

        const reportBackground = computed(() => page.value?.background || {})

        const onDrag = ({ top, left, target }) => {
            updateCloseButtonStyles(target)
            updateElementStyles(target, { left, top }, getStylesOfElement(target))
        }

        const onScale = ({ target, drag }) => {
            updateCloseButtonStyles(target)
            updateElementStyles(target, { transform: drag.transform }, getStylesOfElement(target))
        }        

        const onRotate = ({ target, drag }) => {
            updateCloseButtonStyles(target)
            updateElementStyles(target, { transform: drag.transform }, getStylesOfElement(target))
        }

        const activateItem = (e) => {
            let elem = e.target
            elem = getDomElementParent(elem, 'draggable')
            updateCloseButtonStyles(elem)
            const id = elem.getAttribute('id')
            activeItem.value = `#${id}`
            document.querySelector('.moveable').style.display = "inline-block"
            elem.blur()
        }

        const updateCloseButtonStyles = (elem) => {
            const close = document.querySelector('.close')
            const { width, height } = elem.getBoundingClientRect()
            const left = elem.style.left || 0
            const top = elem.style.top || 0
            const closeLeft = parseInt(left) + width/2
            close.style.left = `${closeLeft}px`
            close.style.top = top != 0 && top != '' ? `${parseInt(top) - 30}px` : 0
            close.style.display = 'flex'
        }

        const hideDrag = () => {
            document.querySelector('.moveable').style.display = "none"
            document.querySelector('.close').style.display = "none"
        }

        const openUpdatePopup = (element, domElement) => {
            activeElement.value = element
            activeDomElement.value = getDomElementParent(domElement, 'draggable')
            openPopup.value = true
            hideDrag()
        }

        const deleteItem = () => {
            const id = activeItem.value.slice(1)
            const elementIndex = page.value.elements.findIndex(page => {
                return page.attributes.id == id
            })
            if(elementIndex != -1) deleteAndRemove(elementIndex)
        }

        const deleteAndRemove = (elementIndex) => {
            hideDrag()
            store.commit(`${BUILDER_MODULE}/${DELETE_ITEM}`, elementIndex)
            activeItem.value = null
        }

        const updateElementFromPopup = ({ id, textValue, table, name }) => {
            const index = pages.value[activePage.value].elements.findIndex(item => item.attributes.id == id)
            const domElem = document.querySelector(`#${id}`)
            if(textValue != undefined && name != 'table' && name == 'textarea') {

                if(['templates-add', 'templates-edit'].includes(route.name)) {
                    updateElementValue({ index, value: unref(textValue) })
                    openPopup.value = false
                    return 
                }

                if(generateTags(textValue)) {
                    deleteAndRemove(index)
                    openPopup.value = false
                    return
                }
                else updateElementValue({ index, value: unref(textValue) })
            }
            if(!_.isEmpty(table) && name == 'table') {
                updateElementTable({ 
                    index, 
                    rows: table.rows, 
                    cols: table.cols, 
                    headers: table.headers,
                    content: table.content 
                })
            }
            if(name == 'svg') {
                updateSvg({ index, stroke: itemAttributes.svg.stroke, strokeWidth: itemAttributes.svg.strokeWidth })
            }
            updateElementStyles(domElem, unref(itemAttributes), getStylesOfElement(domElem), name)
            openPopup.value = false
        }

        const updateSvg = ({ index, stroke, strokeWidth }) => {
            store.commit(`${BUILDER_MODULE}/${UPDATE_SVG}`, { index, stroke, strokeWidth })
        }

        const updateElementStyles = (target, styles, elementOldStyles, item = '') => {
            const { id } = target
            const itemIndex = pages.value[activePage.value].elements.findIndex(item => item.attributes.id == id)
            const itemName = pages.value[activePage.value].elements.find(item => item.attributes.id == id).name
            item = item == '' ? itemName : item
            const computedStyles = getComputedStyle(styles, elementOldStyles, item)
            nextTick(() => {
                store.commit(`${BUILDER_MODULE}/${UPDATE_ELEMENT_STYLES}`, { 
                    styles: computedStyles, 
                    index: itemIndex 
                })
            })
        }

        const updateElementValue = ({ item = 'textarea', index, value }) => {
            const domElements = ['input', 'textarea', 'select']
            if(domElements.includes(item)) {
                store.commit(`${BUILDER_MODULE}/${UPDATE_ELEMENT_CONTENT}`, {
                    content: value,
                    index    
                })
            }
        }

        const updateElementTable = ({ index, rows, cols, headers, content }) => {
            store.commit(`${BUILDER_MODULE}/${UPDATE_ELEMENT_TABLE}`, {
                rows,
                cols,
                headers,
                content,
                index    
            })
        }

        watch(page, () => {
            activeItem.value = null
            nextTick(() => {
                if(document) {
                    const close = document.querySelector('.close')
                    close ? close.style.display = 'none' : ''
                }
            })
        })

        return {
            page,
            pages,
            order,
            onDrag,
            onScale,
            hideDrag,
            onRotate,
            fetching,
            openPopup,
            activePage,
            activeItem,
            deleteItem,
            isReportPage,
            activateItem,
            activeElement,
            openUpdatePopup,
            activeDomElement,
            reportBackground,
            getStylesOfElement,
            updateElementFromPopup
        }

    }
}
</script>

<style lang="scss" scoped>

$orange: orange;

.active-item {
    cursor: move;
}

.template {
    &-body {
        img {
            object-fit: cover;
            width: 20rem;
            height: 20rem;
            height: auto;
            border: 3px solid $orange;
        }
        span {
            word-break: break-all !important;
        }
        .page-number {
            float: right;
            font-size: 12px;
            font-family: inherit;
            &::after, &::before {
                float: none;
                clear: both;
            }
        }
    }
}

.builder-container {

    position: relative;
    height: 1122px;
    width: 793px;
    background: #fff;
    overflow: hidden;
    margin-bottom: 1rem;

    .draggable {
        z-index: 10;
        position: absolute;
        left: 1rem;
        top: 1rem;
    }

    .close {
        position: absolute;
        top: 0;
        left: 0;
        width: 1.2rem;
        height: 1.2rem;
        background: #000;
        color: white;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        line-height: 1.08rem;
        cursor: pointer;
        &:hover {
            opacity: .8;
        }
    }

    .transparent-button {
        background: transparent;
        border-radius: 0;
        border: 2px solid #000;
        text-transform: uppercase;
        font-weight: 900;
        font-family: 'Almarai ExtraBold';
    }

    .title-bar {
        background: #797272;
        color: rgb(243, 243, 243);
        padding: 5px 1rem;
        margin: .8rem 0;
        text-transform: uppercase;
        font-size: 1.2rem;
        font-weight: 900;
        font-family: 'Almarai ExtraBold';
        width: 92%;
        display: flex;
        align-items: center;
    }

    .textarea {
        width: auto;
        height: auto;
        border: 1px solid #ccc;
        z-index: 13;
        word-wrap: normal;
        .heading {
            font-style: normal;
            font-family: inherit;
            font-size: 20px;
            line-height: 29px;
            color: #212529;
            font-weight: 500;
        }
    }

    .builder-image {
        width: 200;
        height: 250px;
        object-fit: cover;
        margin: .5rem 0;
    }

}

</style>