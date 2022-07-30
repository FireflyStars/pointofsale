<template>
    
    <div class="container-fluid h-100 bg-color">

        <main-header />

        <div 
            class="row d-flex align-content-stretch align-items-stretch flex-row hmax main-view-wrap" 
            style="z-index:100"
        >

            <side-bar />

            <div class="col main-view container">
                
                <page-title 
                    icon="emailing" 
                    name="PLATEFORME MARKETING" 
                    class="almarai_extrabold_normal_normal"
                    mainStyle="margin-left: 15px !important;"
                />

                <transition
                    enter-active-class="animate__animated animate_fadeIn"
                    leave-active-class="animate__animated animate_fadeOut"
                >


                    <form action="" class="space-y-6">

                        <div class="container">
                            <div class="row" style="height: calc(222.75mm);">

                                <div class="col-lg-12">

                                    <div class="d-flex justify-content-between align-items-center mb-4 mt-2">

                                        <h3 
                                            class="margin-align m-0"
                                        >
                                            PLATEFORME MARKETING > {{ category.name }}
                                        </h3>

                                        <div class="d-flex align-items-center gap-2">

                                            <base-button
                                                prepend
                                                class="btn btn-newrdv body_medium"
                                                kind="warning"
                                                title="Liste campagne"
                                                classes="border-0"
                                                style="border-radius: 10px; font-size: 12px !important; margin-right: 1rem; background: #FF0000"
                                                @click.prevent="$router.push({
                                                    name: 'marketing-list'
                                                })"
                                            >
                                                <icon name="clipboard" />
                                            </base-button>

                                            <base-button
                                                prepend
                                                class="btn btn-newrdv body_medium"
                                                kind="warning"
                                                :title="'Panier :' + cardQty"
                                                classes="border-0"
                                                style="border-radius: 10px; font-size: 12px !important"
                                                @click.prevent="$router.push({
                                                    name: 'marketing-card'
                                                })"
                                            >
                                                <icon name="clipboard" />
                                            </base-button>
                                        
                                        </div>


                                    </div>


                                    <div class="row">
                                        
                                        <div class="col-6 left-panel py-5 ps-5 pe-2 bg-panel">
                                            
                                            <div 
                                                class="panel-heading d-flex justify-content-between align-items-center"
                                            >
                                                <h4 class="panel-title">{{ category.name }}</h4>
                                                <base-button
                                                    class="btn btn-newrdv body_medium"
                                                    kind="warning"
                                                    title="Retour vers produit"
                                                    classes="border-0"
                                                    style="border-radius: 10px; font-size: 12px !important"
                                                    @click.prevent="$router.push({
                                                        name: 'emailing'
                                                    })"
                                                />
                                            </div>
    
                                            <div class="content">
    
                                                <div class="panel-description">
                                                    <p class="p-title text-uppercase text-start">
                                                        Descriptif
                                                    </p>
                                                    <p class="fs-6" v-html="category.text"></p>
                                                </div>
        
                                                <hr v-if="!isPersonalizeAble && !loading" />
        
        
                                                <div class="row" v-if="!isPersonalizeAble && !loading">
        
                                                    <div class="col">
                                                        <p class="p-title text-uppercase text-start">
                                                            votre personnalisation
                                                        </p>
                                                    </div>
        
                                                    <div class="col-lg-12 group_input">
        
                                                        <label 
                                                            class="fix_width" 
                                                            for="expediteur"
                                                        >
                                                            RAISON SO.:
                                                        </label>
        
                                                        <input
                                                            type="text"
                                                            placeholder="La Compagnie des Toits"
                                                            name="expediteur"
                                                            :value="affiliate.raisonsociale"
                                                            disabled
                                                        />
        
                                                    </div>
        
                                                    <div class="col-lg-12 group_input">

                                                        <template v-if="fields?.Telephone_agence?.active == 1">
                                                            
                                                            <label class="fix_width">coord:</label>
                                                            <input
                                                                type="text"
                                                                placeholder="1 Rue Jean-Baptiste Colbert"
                                                                name="adresse"
                                                                v-model="phone"
                                                            />
                                                        
                                                        </template>
                                                        
                                                        <template v-if="fields?.Email_agence?.active == 1">
        
                                                            <label class="fix_width_tiret">-</label>
                                                            <input
                                                                type="text"
                                                                id="inputPassword"
                                                                name="adresse2"
                                                                v-model="email"
                                                            />

                                                        </template>
        
                                                    </div>
        
                                                    <div class="col-lg-12 group_input" v-if="fields?.Adresse_agence?.active == 1">
                                                        
                                                        <label class="fix_width">adresse:</label>
                                                        <input
                                                            type="text"
                                                            placeholder="1 Rue Jean-Baptiste Colbert"
                                                            name="adresse"
                                                            :value="affiliate.address"
                                                            disabled
                                                        />
        
                                                        <label class="fix_width_tiret">-</label>
                                                        <input
                                                            type="text"
                                                            id="inputPassword"
                                                            name="adresse2"
                                                            :value="affiliate.address2"
                                                            disabled
                                                        />
        
                                                    </div>
        
                                                    <div class="col-lg-12 group_input" v-if="fields?.CP_agence?.active == 1 && fields?.Ville_agence?.active == 1">
                                                        
                                                        <label class="fix_width">cp & ville:</label>
                                                        <input
                                                            type="text"
                                                            placeholder="1 Rue Jean-Baptiste Colbert"
                                                            name="adresse"
                                                            :value="affiliate.postcod + ' ' + affiliate.city"
                                                            disabled
                                                        />
                                                    </div>
        
                                                    <div class="col-lg-12 group_input">
                                                        
                                                        <label class="fix_width">statut:</label>
                                                        <input
                                                            type="text"
                                                            placeholder="1 Rue Jean-Baptiste Colbert"
                                                            name="adresse"
                                                            :value="affiliate.statutagence"
                                                            disabled
                                                        />
        
                                                        <label class="fix_width_tiret">-</label>
                                                        <input
                                                            type="text"
                                                            id="inputPassword"
                                                            name="adresse2"
                                                            disabled
                                                        />
        
                                                    </div>
        
                                                    <div class="col-lg-12 group_input">
                                                        
                                                        <label class="fix_width">sired & tva:</label>
                                                        <input
                                                            type="text"
                                                            placeholder="1 Rue Jean-Baptiste Colbert"
                                                            name="adresse"
                                                            :value="affiliate.siret"
                                                            disabled
                                                        />
        
                                                        <label class="fix_width_tiret">-</label>
                                                        <input
                                                            type="text"
                                                            id="inputPassword"
                                                            name="adresse2"
                                                            :value="affiliate.tva"
                                                            disabled
                                                        />
        
                                                    </div>
        
                                                    <div class="col-lg-12 group_input">
                                                        
                                                        <label class="fix_width">ape & rcs:</label>
                                                        <input
                                                            type="text"
                                                            placeholder="1 Rue Jean-Baptiste Colbert"
                                                            name="adresse"
                                                            :value="affiliate.ape"
                                                            disabled
                                                        />
        
                                                        <label class="fix_width_tiret">-</label>
                                                        <input
                                                            type="text"
                                                            id="inputPassword"
                                                            name="adresse2"
                                                            :value="affiliate.secteuragence"
                                                            disabled
                                                        />
        
                                                    </div>
        
                                                </div>
        
                                                <hr v-if="!productWithDownloadOnly && !loading" />
    
                                                <div class="footer" v-if="!productWithDownloadOnly && !loading">

                                                    <p class="p-title text-uppercase text-start">
                                                        votre commande
                                                    </p>
    
                                                    <div class="row">
    
                                                        <div class="col-lg-12 group_input">
        
                                                            <label 
                                                                class="fix_width" 
                                                                for="expediteur"
                                                            >
                                                                quantite:
                                                            </label>
            
                                                            <input
                                                                type="text"
                                                                placeholder="La Compagnie des Toits"
                                                                name="expediteur"
                                                                v-model="qtyOfProduct"
                                                                max="250"
                                                                min="1"
                                                            />
    
                                                            <span class="help text-lowercase">
                                                                (Minimum {{ category.qtymini }})
                                                            </span>
            
                                                        </div>
    
                                                        <div class="col-lg-12 group_input d-flex align-items-center justify-content-start">
        
                                                            <label 
                                                                class="fix_width" 
                                                                for="expediteur"
                                                            >
                                                                tarif:
                                                            </label>
            
                                                            <div class="price-tag">
                                                                <strong>{{ categoryPrice  }}</strong> &euro; <i>HT</i>
                                                            </div>
    
                                                            <span class="help">
                                                                Soit {{ categoryPriceWithTax }} &euro; TTC
                                                            </span>
            
                                                        </div>
    
                                                    </div>    
    
                                                </div>
    
                                            </div>

                                        </div>
                                        
                                        <div 
                                            class="col-6 apercu d-table position-relative" 
                                            style="margin: 0; padding: 0;"
                                        >

                                            <div 
                                                class="text-center bg-panel" 
                                                style="
                                                position: absolute; 
                                                left: 1.5rem; 
                                                transform: scale(.75);
                                                transform-origin: top left;
                                                min-width: 500px;
                                                min-height: 500px;
                                                "
                                            >
                                                
                                                <img 
                                                    :src="category.imageTemplateUrl" 
                                                    alt="Lcdt Logo" 
                                                    style="width: 100%; height: 100%"
                                                >


                                                <template v-if="!productWithDownloadOnly && !isPersonalizeAble && !loading">

                                                    <template v-for="(item, index) in fields" :key="index">

                                                            <span 
                                                                v-if="item != '' && item != null && typeof item != 'undefined' && item?.active == 1" 
                                                            >

                                                                <span 
                                                                :style="{
                                                                    color: item.color,
                                                                    fontSize: item.size,
                                                                    fontFamily: item.font,
                                                                    top: item.y + 'px',
                                                                    left: item.x + 'px',
                                                                    position: 'absolute'
                                                                }">
                                                                    {{ item.value }}
                                                                </span>
                                                                    
                                                                
                                                            </span>

                                                        </template> 
                                                
                                                </template>

                                                
                                            </div>



                                        </div>

                                    </div><!-- inner row-->

                                    <div class="row">
                                        <div class="col-6">
                                            
                                            <div class="d-flex align-items-center justify-content-end gap-2 my-4">

                                                <base-button
                                                    v-if="canGeneratePdf"
                                                    class="btn btn-newrdv body_medium"
                                                    kind="warning"
                                                    title="Télécharger fichier"
                                                    classes="border-0"
                                                    style="border-radius: 10px; font-size: 12px !important; background: #000;"
                                                    @click.prevent="generatePDF"
                                                />

                                                <base-button
                                                    v-if="canAddToCart"
                                                    prepend
                                                    class="btn btn-newrdv body_medium"
                                                    kind="warning"
                                                    title="Ajouter au panier"
                                                    classes="border-0"
                                                    style="border-radius: 10px; font-size: 12px !important;"
                                                    @click.prevent="storeProduct"
                                                >
                                                    <Icon name="clipboard" width="18" height="18" />
                                                </base-button>

                                            </div>

                                        </div>
                                    </div>            


                                </div> <!-- col-lg-12 -->
                                

                            </div><!-- row -->

                        </div><!-- container -->

                    </form>

                </transition>


            </div>

        </div>

    </div>

</template>

<script setup>
    
    import { ref, onMounted, computed, watch } from 'vue'
    import { useStore } from 'vuex'
    import {
        CIBLE_MODULE,
        GET_CAMPAGNE_CATEGORY,
        GET_CAMPAGNE_FIELDS,
        GENERATE_PRODUCT_PDF,
        STORE_PRODUCT,
        LOADER_MODULE,
        DISPLAY_LOADER,
        TOASTER_MODULE,
        TOASTER_MESSAGE,
        HIDE_LOADER
    }
    from '../../store/types/types'
    import useCard from '../../composables/useCard'


    const { getCardQty, cardQty } = useCard()

    const props = defineProps({
        categoryId: {
            type: [Number, String],
            required: true
        }
    })

    const store = useStore()
    const loading = ref(false)

    const phone = ref('')
    const email = ref('')

    const category = computed(() => store.getters[`${CIBLE_MODULE}campagneCategory`]?.campagne || {})
    const affiliate = computed(() => store.getters[`${CIBLE_MODULE}campagneCategory`]?.affiliate || {})
    const fields = computed(() => store.getters[`${CIBLE_MODULE}fields`])

    const productWithDownloadOnly = computed(() => {
        return category.value.typeofproduct?.toLowerCase() == 'download' 
        && category.value.type?.toLowerCase() == 'produit' 
    })
    
    const canGeneratePdf = computed(() => {
        return (category.value.typeofproduct?.toLowerCase() == 'download'
        || (category.value.typeofproduct?.toLowerCase() == 'product perso'))
        && category.value.type == 'Produit' 
    })

    const canAddToCart = computed(() => {
        return (
                category.value.typeofproduct?.toLowerCase() == 'product' 
                || 
                (category.value.typeofproduct?.toLowerCase() == 'product perso')
            )
        && 
        category.value.type == 'Produit'

    })

    const isPersonalizeAble = computed(() => {
        return category.value.typeofproduct?.toLowerCase() == 'product' 
            && category.value.type?.toLowerCase() == 'produit'
    })

    const cardQuantity = computed(() => category.value?.card_detail?.qty || 0)
    const qtyOfProduct = ref(1)     

    const categoryPrice = computed(() => {
        const price = category.value.price * (qtyOfProduct.value || 1)
        return price?.toFixed(2) || 0
    })

    const categoryPriceWithTax = computed(() => { 
        const tax = 0.20 * categoryPrice.value //making it to fix 20%
        const percentage = +categoryPrice.value + +tax
        return percentage?.toFixed(2) || 0
    })
    
    const getCategory = async (id) => {
        try {
            loading.value = true
            await store.dispatch(`${[CIBLE_MODULE]}${[GET_CAMPAGNE_CATEGORY]}`, id)
        }
        catch(e) {
            throw e
        }
        finally {
            loading.value = false
        }
    }

    const getFieldsData = (id) => {
        return store.dispatch(`${[CIBLE_MODULE]}${[GET_CAMPAGNE_FIELDS]}`, id)
    }

    const storeProduct = async () => {
        try {

            if(qtyOfProduct.value > category.value.qtymini) {
                store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                    type: 'danger',
                    message: 'PAS LA BONNE QUANTITE',
                    ttl: 5,
                })
                return
            }
            if(qtyOfProduct.value <= 0) {
                store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                    type: 'danger',
                    message: 'Please enter a quantity first',
                    ttl: 5,
                })
                return
            }
            store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'Loading...'])
            await store.dispatch(`${[CIBLE_MODULE]}${[STORE_PRODUCT]}`, { 
                category: category.value, 
                qty: qtyOfProduct.value,
                email: email.value,
                phone: phone.value
            })
            
            await getCardQty()

            store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                type: 'success',
                message: 'Produit ajouté au panier',
                ttl: 5,
            })

        }

        catch(e) {
            store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                type: 'danger',
                message: 'Something went wrong',
                ttl: 5,
            })
        }

        finally {
            store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`)
        }

    }

    const downloadProductFile = async () => {
        try {
            if(category.value?.fileDepliantFull?.fullpath) {
                window.location = category.value?.fileDepliantFull?.fullpath
            }
        }
        catch(e) {
            store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                type: 'danger',
                message: e || 'Something went wrong',
                ttl: 5,
            })
            throw e
        }
    }

    const generatePDF = async () => {


        if(productWithDownloadOnly.value) {

            downloadProductFile()

            return
        }

        
        try {
            store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'Veuillez patienter. Génération du PDF en cours...'])
            await store.dispatch(`${CIBLE_MODULE}${GENERATE_PRODUCT_PDF}`, category.value.id)
        }
        
        catch(e) {
            store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                type: 'danger',
                message: e || 'Something went wrong',
                ttl: 5,
            })
            throw e
        }

        finally {
            store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`)
        }

    }

    watch(fields, (value) => {
        if(value) {
            email.value = value.Email_agence.value
            phone.value = value.Telephone_agence.value
        }
    })

    onMounted(async () => {
        await getCategory(props.categoryId)
        getFieldsData(props.categoryId)
        getCardQty()
    })

</script>

<style lang="scss" scoped>

.main-view {
    margin: 64px 20px 0 20px !important;
}

.apercu {
    height: auto;
    overflow: overlay;
    width: auto;
}

.panel-title {
    color: #E8581B;
    font-family: 'Almarai Bold';
    font-style: normal;
    font-weight: 800;
    font-size: 22px;
    line-height: 110%;
    display: flex;
    align-items: center;
}

.panel-description {
    margin-top: 3.875rem;
}

.content {
    padding-left: 1rem;
    padding-right: 1rem;
}

.margin-align {
    font-size: 17px;
    font-weight: bold;
}
.margin-ajustement {
    margin-top: 39px;
}
.ajustement {
    display: flex;
}

.left-panel {
    border-right: 1px solid #000;
}

.p-subtitle {
    text-align: center;
    font-size: 11px;
    font-weight: 700;
    color: orangered;
}
.p-title {
    text-align: center;
    font-size: 17px;
    font-weight: 700;
    color: #e4703f;
    margin-bottom: .7rem;
}
.type {
    font-size: x-small;
    font-weight: bold;
}
.type-click {
    border: orangered 1px solid;
}
.container {
    padding-bottom: 100px;
}
img.card-img-top {
    height: 100%;
}

.preview-box {
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.group_input {

    justify-content: space-between;
    text-transform: uppercase;
    margin: 7px 0;
    align-items: center;

    input {
        width: 200px;
        background-color: #d3d3d3;
        border: none;
        height: 27px;
        font-size: small;
    }

    span {
        margin-left: 15px;
        font-size: 11px;
    }

    .price-tag {
        color: #E8581B;
        border: 1px solid #E8581B;
        padding: 0.01rem 0.3rem;
    }

}

.italic {
    font-style: italic;
}

label.fix_width {
    width: 100px;
    font-size: 11px;
    font-weight: 700;
}

label.fix_width_tiret {
    width: 45px;
    text-align: center;
    font-size: 11px;
    font-weight: 700;
}
.color {
    color: orangered;
}
.margin {
    margin-bottom: 40px;
    margin-top: 35px;
}
.card-title {
    text-align: center;
    font-size: unset;
    font-family: revert;
}
.card-text {
    font-size: x-small;
    margin-top: 18%;
    text-align: center;
}
.card {
    width: 12rem;
}
.body {
    width: 100%;
    height: 100%;
}

hr {
    margin: 40px 0px;
    border: none !important;
    border-color: #000 !important;
}
.apercu h6 {
    font-size: 12px;
    font-weight: 700;
    margin-bottom: 15px;
    text-align: center;
}
.last label {
    margin-right: 10px;
    margin-left: 15px;
}
.type {
    font-size: x-small;
    font-weight: bold;
}
.link {
    cursor: pointer;
    text-decoration: none;
    color:orange;
}
.link:hover {
    color: orangered;
}

</style>
