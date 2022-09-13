
import useHelpers from '../useHelpers'
import store from '../../store/store'

import { 
    BUILDER_MODULE, 
    SAVE_PAGE,
    DELETE_ITEM,
    GENERATE_ELEMENT,
    UPDATE_ELEMENT_STYLES,
    UPDATE_ELEMENT_CONTENT,
    UPDATE_ELEMENT_TABLE,
    UPDATE_REPORT_TEMPLATE,
    GET_REPORT_TEMPLATE,
    SET_PAGE_BACKGROUND
} from '../../store/types/types'

const { generateId } = useHelpers()

export default function useElementsGenerator() {

    const tags =  [
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
        'date-de-commande',
    ]

    const generateTags = (textValue) => {

        for(const tag of tags) {
            textValue = textValue.replace(`[${tag}]`, getCustomerValue(tag))
        }

        return textValue

        var result = textValue.match(/\[(.*?)\]/g)

        if(result?.length) {
            result = result.map(function(val) {
                return val.replace(/\[/g,'').replace(/\]/g, '')
            })
            if(result.length) {
                result.forEach((tag) => generateCustomerInfo(tag, action))
                return result.length
            }
            return false
        }
        return false
    }

    const generatePreRenderedTags = (textValue, action = 'generateTextarea') => {
        var result = textValue.match(/\[(.*?)\]/g)
        if(result?.length) {
            result = result.map(function(val) {
                console.log(val, " is the tag value")
                return val.replace(/\[/g,'').replace(/\]/g, '')
            })
            if(result.length) {
                const tags = []
                result.forEach((tag) => {
                    tags.push(getCustomerInfo(tag, action))
                })
                return tags
            }
            return []
        }
        return []
    }
    
    const generateTextarea = (attributes) => {
        return {
            item: 'div',
            attributes: {
                class: `draggable textarea ${attributes.class || ''}`,
                style: attributes.style || '',
                dataName: 'textarea',
                id: generateId(12),
            },
            content: 'content' in attributes ? attributes.content : '',
            name: 'textarea'
        }
    }

    const generateButton = ({ id, kind }) => {
        return { 
            item: id,
            attributes: {
                kind,
                title: 'Button',
                class: 'draggable', 
                id: generateId(12),
                style: '',
                dataName: 'button'
            },
            name: 'button'
        }
    }
    
    const generateIcon = ({ id, name, strokeWidth = null, width = null, height = null, stroke = '#000' }) => {
        return {
            item: id,
            attributes: {
                name,
                class: 'draggable',
                id: generateId(12),
                style: '',
                dataName: 'svg',
                strokeWidth: strokeWidth || 0.4,
                stroke,
                width: width || 50,
                height: height || 50,
            },
            name: 'svg'
        }
    }

    const generateImage = ({ filename, image, prefetched = false }) => {
        return {
            item: 'img',
            attributes: {
                src: filename,
                alt:  image.name || 'Uploaded Image',
                class: 'draggable',
                style: '',
                dataName: 'img',
                id: generateId(12),
            },
            name: 'img',
            dataFile: image,
            prefetched
        }
    }

    const generateTable = (attributes) => {
        return {
            item: 'reportTable',
            attributes: {
                class: `draggable ${attributes.class || ''}`,
                style: '',
                dataName: 'table',
                id: generateId(12),
                rows: 1,
                cols: 3,
                headers: true
            },
            content: {
                header: {},
                body: {}
            },
            name: 'table'
        }
    }

    const generateBackground = ({ classes, image = {}, filename, prefetched = true }) => {
        return {
            item: 'backgroundImage',
            attributes: {
                class: `draggable ${classes || ''}`,
                style: '',
                dataName: 'backgroundImage',
                id: generateId(12),
                src: filename,
                alt: image?.name || 'Report page background',
            },
            prefetched,
            dataFile: image,
            name: 'backgroundImage'
        }
    }

    const generateElement = (name, attrs = {}) => {
        const elementMapping = {
            textarea: generateTextarea,
            icon: generateIcon,
            button: generateButton,
            table: generateTable,
            background: generateBackground
        }
        const element = elementMapping[name](attrs)
        if(name == 'background') {
            store.commit(`${BUILDER_MODULE}/${SET_PAGE_BACKGROUND}`, element)
        }
        else {
            store.commit(`${BUILDER_MODULE}/${GENERATE_ELEMENT}`, element)
        }
    }

    const generatePrefetchedImage = (detail) => {
        store.commit(`${BUILDER_MODULE}/${GENERATE_ELEMENT}`, generateImage({ 
            filename: detail.urls.original,
            image: `${detail.storage_path}/${detail.file}.${detail.type}`,
            prefetched: true
        }))
    }

    const promptImage = async (background = false) => {

        let image = {}
        let filename = {}

        let file = document.querySelector('#file')
        file.click()

        file.onchange = (e) => {
            image = e.target.files[0]
            filename = URL.createObjectURL(image)

            if(background == true) {
                store.commit(`${BUILDER_MODULE}/${SET_PAGE_BACKGROUND}`, 
                    generateImage({ filename, image, prefetched: false })
                )
            }
            else {
                store.commit(`${BUILDER_MODULE}/${GENERATE_ELEMENT}`, 
                    generateImage({ filename, image })
                )
            }


            file.value = ''
        }

    }

    const generateCustomerInfo = (tag) => {
        generateElement('textarea', {
            content: getCustomerInfo(tag)
        })
    }

    const getCustomerValue = (tag) => {

        const order = store.getters[`${BUILDER_MODULE}/order`]
        tag = tag?.toString()?.trim()

        switch (tag) {
            case 'société': return `${order?.customer?.company || ''}`
            case 'raisonsocial': return `${order?.customer?.raisonsociale || ''}`
            case 'raisonsocial2': return `${order?.customer?.raisonsociale2 || ''}`
            case 'téléphone-client': return `${order?.customer?.telephone || ''}`
            case 'prénom-contact': return `${order?.contact?.firstname || ''}`
            case 'nom-contact': return `${order?.contact?.name || ''}`
            case 'email-contact': return `${order?.contact?.email || ''}`
            case 'mobile-contact': return `${order?.contact?.mobile || ''}`
            case 'téléphone-contact': return `${order?.contact?.telephone || ''}`
            case 'société-adresse': return `${order?.customer?.address?.company || ''}`
            case 'prénom-ou-batiment': return `${order?.customer?.address?.firstname || ''}`
            case 'nom-adresse': return `${order?.customer?.address?.name || ''}`
            case 'adresse1': return `${order?.customer?.address?.address1 || ''}`
            case 'adresse2': return `${order?.customer?.address?.address2 || ''}`
            case 'adresse3': return `${order?.customer?.address?.address3 || ''}`
            case 'code-postal': return `${order?.customer?.address?.postcode || ''}`
            case 'ville': return `${order?.customer?.address?.city || ''}`
            case 'num-commande': return `${order?.id || ''}`
            case 'total-commande': return `${order?.total || ''}`
            case 'date-de-fin-prevue-commande': return `${order?.datefinprevu || ''}`
            case 'date-de-commande': return `${order?.datecommande || ''}`
        }

    }

    const getCustomerInfo = (tag) => {

        const order = store.getters[`${BUILDER_MODULE}/order`]
        tag = tag?.toString()?.trim()

        switch (tag) {
            case 'customer-name': return `<div>
                    <div class='heading'>Client</div>
                    <div>${order?.customer?.name || ''}</div>
                    <div>${order?.customer?.firstname || ''}</div>
                    <div>${order?.customer?.compnay || ''}</div>
                    <div>${order?.customer?.signupdate || ''}</div>
                </div>`
            break
            case 'customer-contact': return `
                    <h4 class='title'>Contact</h4>
                    <div>${order?.contact?.name || ''}</div>
                    <div>${order?.contact?.email || ''}</div>
                    <div>${order?.contact?.mobile || ''}</div>
                    <div>${order?.contact?.type || ''}</div>
                    <div>${order?.contact?.comment || ''}</div>
                `
            break
            case 'customer-address': return `<div>
                    <div class='heading'>Client Address</div>
                    <div>${order?.customer?.address?.address1 || ''}</div>
                    <div>${order?.customer?.address?.address2 || ''}</div>
                    <div>${order?.customer?.address?.city || ''}</div>
                    <div>${order?.customer?.address?.phone || ''}</div>
                    <div>${order?.customer?.address?.phone_mobile || ''}</div>
                    <div>${order?.customer?.address?.vat_number || ''}</div>
                </div>    
                `
            break
            case 'société': return `<div>
                    <div>${order?.customer?.company || ''}</div>
                </div>    
                `
            break
            case 'raisonsocial': return `<div>
                    <div>${order?.customer?.raisonsociale || ''}</div>
                </div>    
                `
            break
            case 'raisonsocial2': return `<div>
                    <div>${order?.customer?.raisonsociale2 || ''}</div>
                </div>    
                `
            break
            case 'téléphone-client': return `<div>
                    <div>${order?.customer?.telephone || ''}</div>
                </div>    
                `
            break
            case 'prénom-contact': return `<div>
                    <div>${order?.contact?.firstname || ''}</div>
                </div>    
                `
            break
            case 'nom-contact': return `<div>
                    <div>${order?.contact?.name || ''}</div>
                </div>    
                `
            break
            case 'email-contact': return `<div>
                    <div>${order?.contact?.email || ''}</div>
                </div>    
                `
            break
            case 'mobile-contact': return `<div>
                    <div>${order?.contact?.mobile || ''}</div>
                </div>    
                `
            break
            case 'téléphone-contact': return `<div>
                    <div>${order?.contact?.telephone || ''}</div>
                </div>    
                `
            break
            case 'société-adresse': return `<div>
                    <div>${order?.customer?.address?.company || ''}</div>
                </div>    
                `
            break
            case 'prénom-ou-batiment': return `<div>
                    <div>${order?.customer?.address?.firstname || ''}</div>
                </div>    
                `
            break
            case 'nom-adresse': return `<div>
                    <div>${order?.customer?.address?.name || ''}</div>
                </div>    
                `
            break
            case 'adresse1': return `<div>
                    <div>${order?.customer?.address?.address1 || ''}</div>
                </div>    
                `
            break
            case 'adresse2': return `<div>
                    <div>${order?.customer?.address?.address2 || ''}</div>
                </div>    
                `
            break
            case 'adresse3': return `<div>
                    <div>${order?.customer?.address?.address3 || ''}</div>
                </div>    
                `
            break
            case 'code-postal': return `<div>
                    <div>${order?.customer?.address?.postcode || ''}</div>
                </div>    
                `
            break
            case 'ville': return `<div>
                    <div>${order?.customer?.address?.city || ''}</div>
                </div>    
                `
            break
            case 'num-commande': return `<div>
                    <div>${order?.id || ''}</div>
                </div>    
                `
            break
            case 'total-commande': return `<div>
                    <div>${order?.total || ''}</div>
                </div>    
                `
            break
            case 'heure-commande': return `<div>
                    <div>${order?.nbheure || ''}</div>
                </div>    
                `
            break
            case 'date-de-fin-prevue-commande': return `<div>
                    <div>${order?.datefinprevu || ''}</div>
                </div>    
                `
            break
            case 'date-de-commande': return `<div>
                    <div>${order?.datecommande || ''}</div>
                </div>    
                `
            break
        }

    }

    return {
        promptImage,
        generateTags,
        generateIcon,
        generateTable,
        generateImage,
        generateButton,
        generateElement,
        generateTextarea,
        generateBackground,
        generateCustomerInfo,
        generatePrefetchedImage,
        generatePreRenderedTags,
    }

}