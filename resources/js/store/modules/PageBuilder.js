
import useHelpers from '../../composables/useHelpers'
import useReports from '../../composables/reports/useReports'

const { generateId } = useHelpers()
const { formatFormData, getFormattedPages, saveReportPages } = useReports()

import { 

    BUILDER_MODULE,
    SAVE_META,
    UPDATE_SVG,
    SET_LOADING,
    GENERATE_PDF, 
    ADD_PAGE,
    DELETE_PAGE,
    SAVE_PAGE_ELEMENTS, 
    GET_ORDER_DETAILS, 
    SAVE_PAGE_ORDER, 
    SAVE_TEMPLATES,
    ASSIGN_TEMPLATE,
    SET_ACTIVE_TEMPLATE,
    SET_ACTIVE_PAGE,
    LOAD_REPORT_PAGES,
    SAVE_REPORT_PAGES,
    RESET_PAGES,
    DELETE_ITEM,
    GENERATE_ELEMENT,
    UPDATE_ELEMENT_STYLES,
    UPDATE_ELEMENT_CONTENT,
    UPDATE_ELEMENT_TABLE,
    SAVE_REPORT_TEMPLATE,
    SAVE_REPORT_TEMPLATES,
    UPDATE_REPORT_TEMPLATE,
    GET_REPORT_TEMPLATE,
    GET_REPORT_TEMPLATES,
    GET_REPORTS,
    SAVE_REPORTS,
    SAVE_REPORT,
    GET_REPORT,
    SET_PAGE_BACKGROUND,

} from "../types/types"

import { unref } from 'vue'
import axios from "axios"

export const PageBuilder = {

    namespaced: true,

    state: {
        pages: [],
        order: {},
        templates: [],
        reports: [],
        report: {
            id: null,
            status: 'store'
        },
        reportTemplates: [],
        activeTemplate: -1,
        activePage: 0,
        loading: {
            id: '',
            value: ''
        },
        meta: {},

        
    },

    getters: {
        meta: state => state.meta,
        loading: state => state.loading,
        pages: state => state.pages,
        order: state => state.order,
        templates: state => state.templates,
        report: state => state.report,
        reports: state => state.reports,
        reportTemplates: state => state.reportTemplates,
        activeTemplate: state => state.activeTemplate,
        template: state => {
            return state.templates.length 
            ? state.templates.find(template => template.id == state.activeTemplate) 
            : {} 
        },
        activePage: state => state.activePage,
        page: state => state.pages.length ? state.pages[state.activePage] : {},
        activePageTemplate: (state, getters) => {
            if(getters?.page?.template_id != -1 && getters?.page?.template_id != undefined) {
                const template = state.templates.find(template => {
                    return template.id == getters.page.template_id
                })
                return template != undefined ? template : {}
            }
            return {}
        }
    },

    mutations: {
        [SET_LOADING](state, { id, value = true }) {
            state.loading.id = id
            state.loading.value = value
        },
        [SAVE_PAGE_ELEMENTS](state, elements) {
            state.page.elements = elements
        },
        [SAVE_PAGE_ORDER](state, order) {
            state.order = order
        },
        [SAVE_TEMPLATES](state, data) {
            state.templates = data
        },
        [SET_ACTIVE_TEMPLATE](state, template = '') {
            if(template != '' && template != undefined) state.activeTemplate = template
            else state.activeTemplate = state.templates[0].id
        },
        [SET_ACTIVE_PAGE](state, page) {
            state.activePage = page
        },
        [SAVE_REPORT_PAGES](state, pages = {}) {
            if(!_.isEmpty(pages)) return state.pages = pages
            state.pages = [{
                id: generateId(12),
                elements: [],
                background: null,
            }]
        },
        [RESET_PAGES](state) {
            state.pages = []
        },
        [ADD_PAGE](state) {
            const page = +state.activePage + 1
            state.pages.splice(page, 0, {
                id: generateId(12),
                elements: [],
                background: null
            })
            state.activePage = page
        },
        [ASSIGN_TEMPLATE](state, id) {
            if(!state.pages.length && !state.activePage) return
            state.pages[state.activePage].template_id = id
        },
        [DELETE_PAGE](state) {
            if(state.pages.length > 1) {
                const deletedPageIndex = state.activePage
                if(state.activePage != 0) state.activePage --
                state.pages.splice(deletedPageIndex, 1)
            }
        },
        [DELETE_ITEM](state, elementIndex) {
            state.pages[state.activePage].elements.splice(elementIndex, 1)
        },
        [GENERATE_ELEMENT](state, element) {
            state.pages[state.activePage].elements.push(element)
        },
        [SET_PAGE_BACKGROUND](state, background) {
           state.pages[state.activePage].background = background   
        },
        [UPDATE_ELEMENT_STYLES](state, { styles, index }) {
            state.pages[state.activePage].elements[index].attributes.style = styles
        },
        [UPDATE_ELEMENT_CONTENT](state, { content, index }) {
            state.pages[state.activePage].elements[index].content = content
        },
        [UPDATE_SVG](state, { index, stroke, strokeWidth }) {
            const element = state.pages[state.activePage].elements[index]
            element.attributes = { 
                ...element.attributes,
                stroke, 
                strokeWidth
            }
        },
        [UPDATE_ELEMENT_TABLE](state, { rows, cols, headers, index, content }) {
            const page = state.pages[state.activePage].elements[index]
            page.attributes.rows = rows
            page.attributes.cols = cols
            page.attributes.headers = headers
            page.content = {
                ...page.content, 
                ...content
            }
        },
        [SAVE_REPORT_TEMPLATES](state, data) {
            state.reportTemplates = data
        },
        [SAVE_REPORTS](state, reports) {
            state.reports = reports
        },
        [SAVE_REPORT](state, report) {
            state.report = report
        },
        [SAVE_META](state, meta) {
            state.meta = meta
        }
    },

    actions: {

        async [GENERATE_PDF]({ state, commit }, { pages, template, orderId }) {

            if(state.loading.id == 'submit' && state.loading.value == true) return

            commit(SET_LOADING, { id: 'submit' })

            const formData = formatFormData(pages)
            if(!_.isEmpty(orderId)) formData.append('order_id', orderId)

            try {
                const { data } = await axios.post('/save-page-elements', formData, {  
                    responseType: 'arraybuffer'
                })
                commit(SET_LOADING, { id: 'submit', value: false })
                return data
            }
            catch(e) {
                commit(SET_LOADING, { id: 'submit', value: false })
                throw e
            }

        },

        async [GET_ORDER_DETAILS]({ commit }, orderId) {
            commit(SET_LOADING, { id: 'fetching' })
            try {
                const { data } = await axios.get(`/get-page-order/${orderId}`)
                commit(SAVE_PAGE_ORDER, data)
                commit(SET_LOADING, { id: 'fetching', value: false })
            }
            catch(e) {
                commit(SET_LOADING, { id: 'fetching', value: false })
                throw e
            }
        },

        async [SAVE_REPORT_TEMPLATE]({ commit }, { pages, orderId, name }) {

            commit(SET_LOADING, { id: 'save-template' })

            const formData = formatFormData(pages)

            if(orderId != null && orderId != undefined) formData.append('order_id', orderId)

            formData.append('name', name)

            try {
                const { data } = await axios.post('/report-template', formData)
                commit(SET_LOADING, { id: 'save-template', value: false })
                return data
            }
            catch(e) {
                commit(SET_LOADING, { id: 'save-template', value: false })
                throw e
            }

        },

        async [UPDATE_REPORT_TEMPLATE]({ commit }, { pages, id, orderId, name }) {

            commit(SET_LOADING, { id: 'save-template' })

            const formData = formatFormData(pages)

            if(orderId != null && orderId != undefined) formData.append('order_id', orderId)

            formData.append('name', name)

            try {
                const { data } = await axios.post(`/report-template/${id}`, formData)
                commit(SET_LOADING, { id: 'save-template', value: false })
                return data
            }
            catch(e) {
                commit(SET_LOADING, { id: 'save-template', value: false })
                throw e
            }

        },

        async [GET_REPORT_TEMPLATE]({ commit }, { id, route }) {
            try {
                const { data } = await axios.get(`/report-template/${id}`)
                saveReportPages(data, route)
            }
            catch(e) {
                throw e
            }
        },

        async [GET_REPORT_TEMPLATES]({ commit }, params) {

            try {
                const { data } = await axios.get('/get-report-templates')
                commit(SAVE_REPORT_TEMPLATES, data)
            }
            catch(e) {
                throw e
            }

        },

        async [GET_REPORT]({ commit }, orderId) {
            try {
                const { data } = await axios.get(`/page-report/${orderId}`)
                saveReportPages(data)
                data.report_id != null ? commit(SAVE_REPORT, {
                    id: data.report_id, 
                    status: 'update' 
                }) : {}
            }
            catch(e) {
                throw e
            }
        },

        async [SAVE_REPORT]({ commit, state }, { pages, orderId, templateId }) {
            
            let path = '/page-report'
            if(state.report.status == 'update') path += `/${orderId}`
            try {
                
                commit(SET_LOADING, { id: 'save-report' })

                const formData = formatFormData(pages)
                formData.append('template_id', templateId)
                formData.append('order_id', orderId)
                formData.append('report_id', state.report.id)

                const { data } = await axios.post(path, formData)
                commit(SAVE_REPORT, { id: data.id, status: 'update' })
                commit(SET_LOADING, { id: 'save-report', value: false })

            }
            
            catch(e) {
                commit(SET_LOADING, { id: 'save-report', value: false })
                throw e
            }

        },


        async [GET_REPORTS]({ commit }, page = 1) {
            try {
                commit(SET_LOADING, { id: 'fetching' })
                commit(SAVE_REPORTS, [])
                const { data } = await axios.get('/page-reports', {
                    params: {
                        page
                    }
                })
                commit(SAVE_REPORTS, data.data)
                commit(SAVE_META, data.meta)
                commit(SET_LOADING, { id: 'fetching', value: false })
            }
            catch(e) {
                commit(SET_LOADING, { id: 'fetching', value: false })
                throw e
            }
        },


    }

}