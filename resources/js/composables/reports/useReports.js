
import { unref, computed } from 'vue'
import { isEmpty } from 'lodash'
import useHelpers from '../useHelpers'
import store from '../../store/store'
import useElementsGenerator from './useElementsGenerator'

import {
    BUILDER_MODULE,
    SAVE_REPORT,
    SAVE_REPORT_PAGES,
    RESET_PAGES,
    GENERATE_PDF,
    SAVE_PAGE_ORDER,
    LOADER_MODULE,
    DISPLAY_LOADER,
    TOASTER_MODULE,
    TOASTER_MESSAGE,
    HIDE_LOADER,
}
from '../../store/types/types'

export default function useReports() {

    const { generateId } = useHelpers()
    const { generateElement, generatePreRenderedTags } = useElementsGenerator()

    const pages = computed(() => store.getters[`${BUILDER_MODULE}/pages`])


    const formatFormData = (pages) => {

        let formData = new FormData()
        pages = unref(pages)

        pages.forEach((page) => {
            
            const { elements, background } = page

            let fileElements = elements
            .filter((element) => element.item == 'img')
            .map(element => {
                return {
                    id: element.attributes.id, 
                    file: element.dataFile,
                }
            })

            fileElements.forEach(element => {
                formData.append(`Img#${element.id}`, element.file)
            })

            if(!isEmpty(page.background)) {
                formData.append(`BackgroundImage#${page.background.attributes.id}`, page.background.dataFile)
            }

        })

        formData.append('pages', JSON.stringify({ ...pages }))
        return formData
    
    }

    const getFormattedPages = (data, route = 'report-page') => {

        const pages = data.pages
        const formattedPages = []

        if(pages.length) {

            pages.forEach((page, index) => {

                let formattedPage = {
                    id: page.id,
                    elements: [],
                    background: {}
                }

                if(!isEmpty(page.background)) {
                    const id = `backgroundImage_${index}`
                    formattedPage.background = {
                        ...page.background,
                        attributes: {
                            ...page.background.attributes,
                            src: findFile(data.page_files, id)?.public_path
                        },
                        dataFile: findFile(data.page_files, id)?.file,
                        prefetched: true,
                    }
                }

                page.elements.forEach(element => {
                    if(element.name == 'img') {
                        
                        formattedPage.elements.push({
                            ...element,
                            attributes: {
                                ...element.attributes,
                                src: findFile(data.page_files, element.attributes.id)?.public_path,
                            },
                            dataFile: findFile(data.page_files, element.attributes.id)?.file,
                            prefetched: true,
                        })
                    }
                    else if(element.name == 'textarea') {
                        if(route != 'report-page') {
                            formattedPage.elements.push({ ...element })
                        }
                        else {
                            const tags = generatePreRenderedTags(element.content)
                            if(tags.length) {
                                tags.forEach((tag, i) => {
                                    formattedPage.elements.push({ 
                                        ...element,
                                        attributes: {
                                            ...element.attributes,
                                            id: generateId(),
                                            style: element.attributes.style += ` margin: ${+i*8}px;`
                                        },
                                        content: tag
                                    })
                                })
                            }
                            else formattedPage.elements.push({ ...element })
                        }
                    }
                    else {
                        formattedPage.elements.push({ ...element })
                    }
                })

                formattedPages.push(formattedPage)

            })

        }

        return formattedPages

    }
    

    const findFile = (files, id) => {
        return files.find(file => file.id == id)
    }

    const saveReportPages = (data, route = 'report-page') => {
        if(!_.isEmpty(data)) {
            const pages = typeof pages == 'string' ? JSON.parse(data.pages) : data.pages
            if(!pages.length) {
                resetPages()
                return
            }
            const formattedPages = getFormattedPages(data, route)
            store.commit(`${BUILDER_MODULE}/${SAVE_REPORT_PAGES}`, formattedPages)
        }
    }

    const resetPages = () => {
        store.commit(`${BUILDER_MODULE}/${SAVE_REPORT}`, {
            id: null, 
            status: 'store' 
        })
        store.commit(`${BUILDER_MODULE}/${RESET_PAGES}`, [])
    }

    const resetOrder = () => {
        store.commit(`${BUILDER_MODULE}/${SAVE_PAGE_ORDER}`, [])
    }

    const generatePagePdf = async (orderId = null) => {

        try {
            store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'Generating PDF...'])

            const data = await store.dispatch(`${[BUILDER_MODULE]}/${[GENERATE_PDF]}`, { 
                pages: pages.value,
                orderId 
            })

            if(data) generatePDF(data)

        }

        catch(e) {
            throw e
        }

        finally {
            store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`)
        }

    }

    const generatePDF = (data) => {
        let blob = new Blob([data], { type: 'application/pdf' })
        let link = document.createElement('a')
        link.href = window.URL.createObjectURL(blob)
        link.download = 'Report.pdf'
        link.click()
    }

    return {
        resetOrder,
        resetPages,
        generatePDF,
        formatFormData,
        generatePagePdf,
        saveReportPages,
        getFormattedPages,
    }

}
