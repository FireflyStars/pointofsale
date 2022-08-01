<template>
    
    <div class="container-fluid h-100 bg-color">

        <main-header />

        <div 
            class="row d-flex align-content-stretch align-items-stretch flex-row hmax main-view-wrap" style="z-index:100"
        >

            <side-bar />

            <div class="col main-view container" style="overflow-x: hidden">

                <div class="d-flex justify-content-between align-items-center">
                    
                    <page-title 
                        icon="emailing" 
                        name="PLATEFORME MARKETING" 
                        class="almarai_extrabold_normal_normal"
                    />

                    <base-button
                        prepend
                        class="btn btn-newrdv body_medium"
                        kind="warning"
                        title="RETOUR VERS PRODUIT"
                        classes="border-0"
                        style="border-radius: 10px; font-size: 12px !important; margin-right: 5rem;"
                        @click.prevent="$router.push({
                            name: 'emailing'
                        })"
                    >
                        <icon name="clipboard" />
                    </base-button>

                </div>
                
                <transition
                    enter-active-class="animate__animated animate_fadeIn"
                    leave-active-class="animate__animated animate_fadeOut"
                >


                    <form action="" class="space-y-6">

                        <div class="container">
                            <div class="row">

                                <div class="col-lg-12">

                                    <div class="d-flex justify-content-between align-items-center mb-4 mt-2">

                                        <h3 
                                            class="margin-align m-0"
                                        >
                                            PLATEFORME MARKETING
                                        </h3>

                                        <div class="d-flex align-items-center gap-2">

                                            <base-button
                                                prepend
                                                class="btn btn-newrdv body_medium"
                                                kind="warning"
                                                title="Liste campagne"
                                                classes="list-button"
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
                                                @click.prevent=""
                                            >
                                                <icon name="clipboard" />
                                            </base-button>
                                        
                                        </div>


                                    </div>

                                    <div class="bg-panel py-5 ps-5 pe-2">

                                        <div class="row">
                                            
                                            <div class="col-8 left-panel">
                                                
                                                <div 
                                                    class="panel-heading d-flex justify-content-between align-items-center"
                                                >
                                                    <h4 class="panel-title text-uppercase">Votre PANIER</h4>
                                                </div>
        
                                                <div class="content">
                                                    
                                                    <div 
                                                        v-for="product in products" 
                                                        :key="product.id"
                                                        class="item-box"
                                                    >
                                                        
                                                        <div 
                                                            class="delete-tag" 
                                                            @click.prevent="deleteCard(product)"
                                                        >
                                                            <Icon 
                                                                name="trash-solid" 
                                                                color="white" 
                                                                width="18" 
                                                                height="18" 
                                                            />
                                                        </div>

                                                        <div class="row">

                                                            <div class="col-4">

                                                                <div class="item-box--left">
                                                                    
                                                                    <div class="thumbnail">
                                                                        <img 
                                                                            src="/images/item.png" 
                                                                            alt="Item"
                                                                            style="width: 100px; height: 100px; object-fit: cover;"
                                                                        >
                                                                    </div>

                                                                    <h4 
                                                                        class="text-uppercase item-title text-center almarai-bold"
                                                                    >
                                                                        {{ product.name }}
                                                                    </h4>
                                                                    
                                                                    <base-button
                                                                        v-if="!perso(product)"
                                                                        class="btn btn-newrdv body_medium text-uppercase border-0"
                                                                        kind="warning"
                                                                        title="VOIR"
                                                                        style="font-size: 12px !important; border-radius: 0; padding-left: 3rem; padding-right: 3rem;"
                                                                        @click.prevent="updateCard(product)"
                                                                        :disabled="loading"
                                                                        :class="{ 'cursor-not-allowed': loading }"
                                                                    />

                                                                </div>

                                                            </div>

                                                            <div class="col-8 py-4">

                                                                <div class="panel-description">
                                                                    <p class="p-title text-uppercase text-start">
                                                                        Descriptif
                                                                    </p>
                                                                    <p class="fs-6" >
                                                                        <span v-html="product.description"></span> <br>
                                                                        Conditionnement: 1ex. <br>
                                                                        Poids: {{ productPoids(product)?.toFixed(2) }} kg
                                                                    </p>
                                                                </div>

                                                                <hr>

                                                                <div class="footer">

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
                                                                                style="width: 10%"
                                                                                v-model="product.qty"
                                                                            />
                    
                                                                            <span class="help text-lowercase">
                                                                                (Minimum {{ product.campagne_category?.qtymini }} ex)
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
                                                                                <strong>{{ productPrice(product) }}</strong> &euro; <i>HT</i>
                                                                            </div>
                    
                                                                            <span class="help">
                                                                                Soit {{ productPriceWithTax(product) }} &euro; TTC
                                                                            </span>
                            
                                                                        </div>
                    
                                                                    </div>    
                    
                                                                </div>
                                                            </div>
                                                        
                                                        </div>

                                                    </div>
                                                 
                                                </div>

                                            </div>
                                            
                                            <div class="col-4 item-box--right px-3">

                                                <div class="items-list">
                                                    <p class="p-title text-uppercase">
                                                        Votre commande
                                                    </p>

                                                    <ul>
                                                        <li class="row" v-for="(item, index) in items" :key="index">
                                                            <span class="col-4 fw-bold">{{ item.name }}</span>
                                                            <span class="hr col-5"></span>
                                                            <span 
                                                                class="col-3"
                                                                :class="[{ 
                                                                    'fw-bold': ['total ht', 'total ttc'].includes(item.name.toLowerCase()) 
                                                                },{
                                                                    'color': item.name.toLowerCase() == 'total ht'
                                                                }, {
                                                                    'text-black': item.name.toLowerCase() == 'total ttc'
                                                                }]"
                                                            >
                                                                {{ item.price }} &euro;
                                                            </span>
                                                        </li>
                                                    </ul>

                                                </div>

                                                <div class="form" style="margin: 4rem 0;">

                                                    <p class="p-title text-uppercase">Adresse De livraison</p>

                                                    <div class="row">
                                                        
                                                        <div class="col-lg-12 group_input">
                                                                
                                                            <label class="fix_width">Adresse:</label>
                                                            <input
                                                                type="text"
                                                                placeholder="1 Rue Jean-Baptiste Colbert"
                                                                name="adresse"
                                                                :value="address"
                                                                disabled
                                                            />
            
                                                        </div>

                                                        <div class="col-lg-12 group_input">
                                                            <label class="fix_width"></label>
                                                            <input
                                                                type="text"
                                                                id="inputPassword"
                                                                name="adresse2"
                                                                :value="postcode"
                                                                disabled
                                                            />
                                                        </div>

                                                        <div class="col-lg-12 group_input">
                                                                
                                                            <label class="fix_width">Contact:</label>
                                                            <input
                                                                type="text"
                                                                placeholder="1 Rue Jean-Baptiste Colbert"
                                                                name="adresse"
                                                                :value="affiliateContact"
                                                                disabled
                                                            />
            
                                                        </div>


                                                        <div class="col-lg-12 group_input">
                                                                
                                                            <label class="fix_width">Coord:</label>
                                                            <input
                                                                type="text"
                                                                placeholder="1 Rue Jean-Baptiste Colbert"
                                                                name="adresse"
                                                                :value="affiliate?.telephone"
                                                                disabled
                                                            />
        
                                                        </div>

                                                        <div class="col-lg-12 group_input">
                                                            <label class="fix_width"></label>
                                                            <input
                                                                type="text"
                                                                id="inputPassword"
                                                                name="adresse2"
                                                                :value="affiliate?.email"
                                                                disabled
                                                            />
                                                        </div>

                                                    </div>


                                                </div>


                                                <div class="d-flex align-items-center justify-content-end my-4" style="margin-top: 13rem;">
                                                    
                                                    <base-button
                                                        prepend
                                                        class="btn btn-newrdv body_medium text-uppercase"
                                                        kind="warning"
                                                        title="commander"
                                                        style="border-radius: 10px; font-size: 12px !important;"
                                                        :disabled="loading || !products.length"
                                                        @click.prevent="validerCard"
                                                        :classes="commandreClasses"
                                                    >
                                                        <Icon name="clipboard" width="18" height="18" />
                                                    </base-button>

                                                </div>


                                            </div>

                                        </div><!-- inner row-->



                                    </div> <!-- bg-panel -->


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

    import { ref, computed, onMounted } from 'vue'
    import { useStore } from 'vuex'
    import {
        CIBLE_MODULE,
        GET_CARD_PRODUCTS,
        UPDATE_CARD,
        DELETE_CARD,
        LOADER_MODULE,
        DISPLAY_LOADER,
        TOASTER_MODULE,
        TOASTER_MESSAGE,
        HIDE_LOADER,
        VALIDER_CARD
    }
    from '../../store/types/types'

    import useCard from '../../composables/useCard'

    const store = useStore()
    const { getCardQty, cardQty } = useCard()

    const Coef = ref(1)
    const loading = ref(false)
    const productQty = ref(0)

    const products = computed(() => store.getters[`${CIBLE_MODULE}products`]?.card_details || [])
    const affiliate = computed(() => store.getters[`${CIBLE_MODULE}products`]?.affiliate || {})

    const perso = (product) => {
        return product.value?.campagne_category?.typeofproduct?.toLowerCase() == 'product perso' 
        && product.value?.campagne_category?.type == 'Produit'
    }

    const address = computed(() => {
        const address = affiliate.value?.address || ''
        const address2 = affiliate.value?.address2 || ''
        return `${address} ${address2}`
    })

    const postcode = computed(() => {
        const code = affiliate.value?.postcod || ''
        const city = affiliate.value?.city || ''
        return `${code} ${city}`
    })

    const commandreClasses = computed(() => {
        let classes = 'border-0 '
        classes += loading.value || !products.value.length ? 'cursor-not-allowed' : ''
        return classes
    })

    const affiliateContact = computed(() => {
        if(_.isEmpty(affiliate.value)) return ''
        return affiliate.value?.firstnamedirector + ' ' + affiliate.value?.namedirector
    })

    // const cardQuantity = computed(() => {
    //     // console.log(products.value)
    //     // return products.value
    //     // .map(product => product.qty)
    //     // .reduce((oldQty, newQty) => oldQty + newQty, 0)
    //     return store.getters[`${CIBLE_MODULE}cardQty`]
    // })

    const cardPrice = computed(() => {
        let total = 0
        products.value
            .forEach(product => total += +productPrice(product))
        return total.toFixed(2)
    })

    const livraisonPrice = computed(() => {
        let total = 0
        products.value.forEach(product => {
            let weight = +productPoids(product) - 10
            weight = +Math.ceil(weight / 10)
            let transportPrice = 17 + +(weight * 7)
            total += +transportPrice * Coef.value
        })
        return total.toFixed(2)
    })

    const totalHt = computed(() => (+cardPrice.value + +livraisonPrice.value).toFixed(2))

    const totalTtc = computed(() => {
        return (+totalHt.value + +(+totalHt.value * +totalTax.value)).toFixed(2)
    })

    const totalTax = computed(() => {
        return products.value
        .map(product => product.tax?.taux)
        ?.reduce((oldTax, newTax) => +oldTax + +newTax, 0)
    })

    const items = computed(() => {
        return [{
            name: 'Produits',
            price: cardPrice.value
        }, {
            name: 'Livraison',
            price: livraisonPrice.value
        }, {
            name: 'Total ht',
            price: totalHt.value
        }, {
            name: 'Total ttc',
            price: totalTtc.value
        }]
    })


    const productPoids = (product) => {
        return (product.qty * product.campagne_category?.poids) || 0
    }

    const productPrice = (product) => {
        const price = product.price * product.qty
        return price?.toFixed(2) || 0
    }

    const productPriceWithTax = (product) => { 
        const price = +productPrice(product)
        const tax = product?.tax?.taux * price
        const percentage = +price + +tax
        return percentage?.toFixed(2) || 0
    }

    const updateCard = async (product) => {

        try {

            if(loading.value) return

            if(product.qty > product?.campagne_category?.qtymini) {
                store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                    type: 'danger',
                    message: 'PAS LA BONNE QUANTITE',
                    ttl: 5,
                })
                return
            }

            if(product.qty <= 0) {
                store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                    type: 'danger',
                    message: 'Please enter a quantity first',
                    ttl: 5,
                })
                return
            }

            loading.value = true
            store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'Loading...'])
            await store.dispatch(`${CIBLE_MODULE}${UPDATE_CARD}`, product)

        }

        catch(e) {
            throw e
        }

        finally {
            loading.value = false
            store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`)
        }

    }

    const deleteCard = async (product) => {
        if(loading.value) return
        try {
            loading.value = true
            store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'Loading...'])
            await store.dispatch(`${CIBLE_MODULE}${DELETE_CARD}`, product.id)
        }
        catch(e) {
            throw e
        }
        finally {
            loading.value = false
            store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`)
        }
    }


    const getProducts = async () => {
        try {
            loading.value = true
            store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'Loading...'])
            await store.dispatch(`${CIBLE_MODULE}${GET_CARD_PRODUCTS}`)
        }
        catch(e) {
            throw e
        }
        finally {
            loading.value = false
            store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`)
        }
    }

    const validerCard = async () => {
        try {
            loading.value = true
            store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'Loading...'])
            await store.dispatch(`${CIBLE_MODULE}${VALIDER_CARD}`)
            store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                type: 'success',
                message: 'Campagne order has been created',
                ttl: 5,
            })
        }
        catch(e) {
            throw e
        }
        finally {
            loading.value = false
            store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`)
        }
    }

    onMounted(() => {
        getProducts()
        getCardQty()
    })

</script>

<style lang="scss" scoped>

.list-button {
    border: 0;
    border-radius: 10px; 
    font-size: 12px !important; 
    margin-right: 1rem; 
    background: #FF0000;
}

.item-box {

    border: 1px solid #E8581B;
    position: relative;
    margin-top: 1rem;

    &:first-of-type {
        margin-top: 4.875rem;
    }

    .delete-tag {
        background: #E8581B;
        padding: .5rem;
        color: white;
        position: absolute;
        top: 0;
        right: 0;
        clip-path: polygon(0 0, 100% 100%, 100% 0);
        width: 60px;
        height: 60px;
        display: flex; 
        justify-content: flex-end;
        cursor: pointer;
        &:hover {
            opacity: .8;
        }
    }

    &--left {
        
        display: flex;
        flex-direction: column;
        align-items: center;
        height: 100%;
        gap: 4;
        
        .thumbnail {
            height: 65% !important;
            width: 100%;
            background: #EBEBEB;
            display: flex;
            justify-content: center;
            align-items: center;
            align-self: flex-start;
            margin-bottom: 1rem;
            clip-path: polygon(0 0, 100% 0, 100% 87%, 50% 100%, 0 90%, 0% 50%);
        }

    }

    &--right {
        margin-top: 4.875rem;
        ul {
            li {
                list-style-type: none;
                line-height: 2;
                text-transform: uppercase;
                font-family: 'Almarai Regular';
                font-size: 13px;

                .hr {
                    border-bottom: 1px dotted #ccc; 
                    max-width: 150px;
                }

            }
        }
    }


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

.content {
    padding-left: 2rem;
    padding-right: 1rem;

    .item-title {
        font-family: 'Almarai Bold';
        font-size: 18px;
    }

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
    border: 1px solid #000;
    height: 90%;
    padding: 1rem;
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
        width: 220px;
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

    label {
        font-weight: bold;
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
